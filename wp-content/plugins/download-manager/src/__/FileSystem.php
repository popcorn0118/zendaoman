<?php

/**
 * Manages file operations<br/>
 * Version: 6|32
 * *** DO NOT CHANGE ***
 */

namespace WPDM\__;

use WPDM\AssetManager\AssetManager;

class FileSystem {
	function __construct() {

	}

	public static function mime_type( $filename ) {
		$filetype = wp_check_filetype( $filename );

		return $filetype['type'];
	}

	public static function uploadFile( $FILE ) {

	}

	/**
	 * @usage Download Given File
	 *
	 * @param $filepath
	 * @param $filename
	 * @param int $speed
	 * @param int $resume_support
	 * @param array $extras
	 */
	public static function downloadFile( $filepath, $filename, $speed = 1024, $resume_support = 1, $extras = array() ) {


		if ( headers_sent( $_filename, $_linenum ) ) {
			Messages::error( "Headers already sent in $_filename on line $_linenum", 1 );
		}

		if ( substr_count( $filepath, "../" ) > 0 ) {
			Messages::error( "Please, no funny business, however, good try though!", 1 );
		}

		if ( __::is_url( $filepath ) ) {
			do_action( "wpdm_download_url_redirect", $filepath, $extras );
			header( "location: " . $filepath );
			die();
		}
		$type = self::fileExt( $filepath );
		if ( WPDM()->fileSystem->isBlocked( $filepath ) ) {
			Messages::error( "Invalid File Type (*.{$type})!", 1 );
		}

		$content_type = function_exists( 'mime_content_type' ) ? mime_content_type( $filepath ) : self::mime_type( $filepath );

		$speed = $speed * 1024; // to bits

		$buffer = 1024 * 8; // to bits

		$bandwidth = 0;

		if ( function_exists( 'ini_set' ) ) {
			@ini_set( 'display_errors', 0 );
		}

		@session_write_close();

		if ( function_exists( 'apache_setenv' ) ) {
			@apache_setenv( 'no-gzip', 1 );
		}

		if ( function_exists( 'ini_set' ) ) {
			@ini_set( 'zlib.output_compression', 'Off' );
		}


		@set_time_limit( 0 );
		@session_cache_limiter( 'none' );

		if ( get_option( '__wpdm_support_output_buffer', 1 ) == 1 ) {
			$pcl = ob_get_level();
			do {
				@ob_end_clean();
				if ( ob_get_level() == $pcl ) {
					break;
				}
				$pcl = ob_get_level();
			} while ( ob_get_level() > 0 );
		}

		if ( ! file_exists( $filepath ) ) {
			Messages::fullPage( '404', esc_attr__( 'File not found!', WPDM_TEXT_DOMAIN ) );
		}

		$file_size = filesize( $filepath );

		$org_size = $file_size;
		$end_byte = $file_size - 1;

		$start_range = 0;
		$end_range   = $end_byte;

		nocache_headers();
        if(!(int)get_option('__wpdm_allow_index')) {
	        header( "X-Robots-Tag: noindex, nofollow", true );
	        header( "Robots: none" );
        }
		header( 'Content-Description: File Transfer' );

		header( "Content-type: $content_type" );

		$filename = apply_filters( "wpdm_download_filename", $filename, $filepath, $extras );

		$filename = rawurlencode( $filename );

		$parallel_download = (int) get_option( '__wpdm_parallel_download', 1 );
		if ( $parallel_download === 0 ) {
			TempStorage::set( "download." . __::get_client_ip(), 1, 15 );
		}

		if ( ! isset( $extras['play'] ) ) {
			if ( get_option( '__wpdm_open_in_browser', 0 ) || wpdm_query_var( 'open' ) == 1 ) {
				header( "Content-disposition: inline;filename=\"{$filename}\"" );
			} else {
				header( "Content-disposition: attachment;filename=\"{$filename}\"" );
			}

			header( "Content-Transfer-Encoding: binary" );
		}

		if ( (int) get_option( '__wpdm_download_resume', 1 ) === 2 ) {
			header( "Content-Length: " . $file_size );
			header( "Content-disposition: attachment;filename=\"{$filename}\"" );
			readfile( $filepath );

			return;
		}

		$file = @fopen( $filepath, "rb" );

		$proto = isset( $_SERVER['SERVER_PROTOCOL'] ) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.1';

		//check if http_range is sent by browser (or download manager)
		if ( isset( $_SERVER['HTTP_RANGE'] ) && $file_size > 0 ) {

			list( $rangeUnit, $http_range ) = explode( "=", $_SERVER['HTTP_RANGE'], 2 );

			if ( substr_count( $http_range, ',' ) > 0 ) {
				header( $proto.' 416 Requested Range Not Satisfiable' );
				header( "Content-Range: bytes $start_range-$end_byte/$org_size" );
				exit;
			}
			if ( $rangeUnit == 'bytes' ) {
				if ( $http_range === '-' ) {
					$start_range = $file_size - substr( $http_range, 1 );
				} else {
					$http_range  = explode( '-', $http_range );
					$start_range = $http_range[0];
					$end_range   = wpdm_valueof( $http_range, 1, [ 'validate' => 'int' ] ) > 0 ? wpdm_valueof( $http_range, 1, [ 'validate' => 'int' ] ) : $end_byte;
					//file_put_contents(ABSPATH.'/server.txt', print_r($http_range, 1));
				}
			} else {
				header( $proto.' 416 Requested Range Not Satisfiable' );
				exit;
			}
			$end_range = $end_range > $end_byte ? $end_byte : $end_range;

			header( "Accept-Ranges: bytes" );
			//header( "Accept-Ranges: 0-$end_byte" );
			header( "{$proto} 206 Partial Content" );

			$content_length = $end_range - $start_range + 1;
			header( "Content-Length: $content_length" );
			header( "Content-Range: bytes $start_range-$end_range/$org_size" );

			fseek( $file, $start_range );

		} else {
			header( "Content-Length: " . $file_size );
		}
		$packet = 1;

		if ( $file ) {
			$speed_chunk = 0;
			while ( ! ( connection_aborted() || connection_status() == 1 ) && $file_size > 0 ) {

				if ( $file_size > $buffer ) {
					echo fread( $file, $buffer );
				} else {
					echo fread( $file, $file_size );
				}
				if ( function_exists( 'ob_get_level' ) && ob_get_level() > 0 ) {
					@ob_flush();
				}
				@flush();
				//Remaining file size to transfer
				$file_size -= $buffer;
				//
				$bandwidth += $buffer;

				//Calculating download speed
				$speed_chunk += $buffer;
				//Condition for download speed control
				if ( $speed > 0 && ( $speed_chunk >= $speed ) ) {
					sleep( 1 );
					$speed_chunk = 0;
					$packet ++;
				}


			}
			$package['downloaded_file_size'] = $file_size;
			//add_action('wpdm_download_completed', $package);
			@fclose( $file );
		}
	}


	static function chunkDownload( $file ) {
		$fp     = @fopen( $file, 'rb' );
		$size   = filesize( $file );
		$length = $size;
		$start  = 0;
		$end    = $size - 1;
		header( "Accept-Ranges: bytes" );
		if ( isset( $_SERVER['HTTP_RANGE'] ) ) {
			$c_start = $start;
			$c_end   = $end;
			list( , $range ) = explode( '=', $_SERVER['HTTP_RANGE'], 2 );
			if ( strpos( $range, ',' ) !== false ) {
				header( 'HTTP/1.1 416 Requested range is not valid' );
				header( "Content-Range: bytes $start-$end/$size" );
				exit;
			}

			if ( $range == '-' ) {
				$c_start = $size - substr( $range, 1 );
			} else {
				$range   = explode( '-', $range );
				$c_start = $range[0];
				$c_end   = ( isset( $range[1] ) && is_numeric( $range[1] ) ) ? $range[1] : $size;
			}
			$c_end = ( $c_end > $end ) ? $end : $c_end;

			if ( $c_start > $c_end || $c_start > $size - 1 || $c_end >= $size ) {
				header( 'HTTP/1.1 416 Requested range is not valid' );
				header( "Content-Range: bytes $start-$end/$size" );
				exit;
			}
			$start  = $c_start;
			$end    = $c_end;
			$length = $end - $start + 1;
			fseek( $fp, $start );
			header( 'HTTP/1.1 206 Partial Content' );
		}
		header( "Content-Range: bytes $start-$end/$size" );
		header( "Content-Length: " . $length );
		$buffer = 1024 * 8;
		while ( ! feof( $fp ) && ( $p = ftell( $fp ) ) <= $end ) {
			if ( $p + $buffer > $end ) {
				$buffer = $end - $p + 1;
			}
			set_time_limit( 0 );
			echo fread( $fp, $buffer );
			flush();
		}
		fclose( $fp );
		die();
	}

	/**
	 * @usage Download any content as a file
	 *
	 * @param $filename
	 * @param $content
	 */
	public function downloadData( $filename, $content ) {
		@ob_end_clean();
		nocache_headers();
		$filetype = wp_check_filetype( $filename );
		if(!(int)get_option('__wpdm_allow_index')) {
			header( "X-Robots-Tag: noindex, nofollow", true );
			header( "Robots: none" );
		}
		header( "Content-Description: File Transfer" );
		header( "Content-Type: {$filetype['type']}" );
		header( "Content-disposition: attachment;filename=\"$filename\"" );
		header( "Content-Transfer-Encoding: Binary" );
		header( "Content-Length: " . strlen( $content ) );
		echo $content;
	}

	/**
	 * @usage Sends download headers only
	 *
	 * @param $filename
	 * @param int $size
	 */
	public function downloadHeaders( $filename, $size = null ) {
		@ob_end_clean();
		$filetype = wp_check_filetype( $filename );
		header( "Content-Description: File Transfer" );
		header( "Content-Type: {$filetype['type']}" );
		header( "Content-disposition: attachment;filename=\"$filename\"" );
		header( "Content-Transfer-Encoding: Binary" );
		if ( $size ) {
			header( "Content-Length: " . $size );
		}
	}


	/**
	 * @usage Download any content as a file
	 *
	 * @param $filename
	 * @param $content
	 */
	public static function mkDir( $path, $mode = 0777, $recur = false ) {
		$success = true;
		if ( ! file_exists( $path ) ) {
			$success = @mkdir( $path, $mode, $recur );
		}

		return $success;
	}

	/**
	 * @usage Create ZIP from given file list
	 *
	 * @param $files
	 * @param $zipname
	 *
	 * @return bool|string
	 */
	public static function zipFiles( $files, $zipname ) {

		if ( ! class_exists( 'ZipArchive' ) ) {
			Messages::fullPage( 'Error!', "<div class='card bg-danger text-white p-4'>" . __( "<b>Zlib</b> is not active! Failed to initiate <b>ZipArchive</b>", "download-manager" ) . "</div>", 'error' );
		}


		$zipped = ( basename( $zipname ) === $zipname ) ? WPDM_CACHE_DIR . sanitize_file_name( $zipname ) : $zipname;

		if ( substr_count( $zipname, '.zip' ) <= 0 ) {
			$zipped .= '.zip';
		}

		if ( file_exists( $zipped ) ) {
			unlink( $zipped );
		}

		if ( count( $files ) < 1 ) {
			return false;
		}

		$zip = new \ZipArchive();
		if ( $zip->open( $zipped, \ZIPARCHIVE::CREATE ) !== true ) {
			return false;
		}
		foreach ( $files as $file ) {
			$file     = trim( $file );
			$filename = wp_basename( $file );
			$file     = WPDM()->fileSystem->absPath( $file );
			if ( $file ) {
				$zip->addFile( $file, $filename );
			}
		}
		$zip->close();

		return $zipped;
	}

	/**
	 * @usage Create ZIP from given dir path
	 *
	 * @param $files
	 * @param $zipname
	 *
	 * @return bool|string
	 */
	public static function zipDir( $dir, $zipname = '' ) {

		if ( $zipname === '' ) {
			$zipname = basename( $dir );
		}

		$zipped = WPDM_CACHE_DIR . sanitize_file_name( $zipname ) . '.zip';

		$base_folder = sanitize_file_name( $zipname );

		$rootPath = realpath( $dir );

		$zip = new \ZipArchive();
		$zip->open( $zipped, \ZipArchive::CREATE | \ZipArchive::OVERWRITE );


		$files = new \RecursiveIteratorIterator(
			new \RecursiveDirectoryIterator( $rootPath, \RecursiveDirectoryIterator::SKIP_DOTS ),
			\RecursiveIteratorIterator::LEAVES_ONLY
		);
		foreach ( $files as $name => $file ) {
			if ( ! $file->isDir() && ! strstr( $file->getRealPath(), ".git" ) && ! strstr( $file->getRealPath(), ".DS" ) ) {
				$filePath     = $file->getRealPath();
				$relativePath = substr( $filePath, strlen( $rootPath ) + 1 );
				$zip->addFile( $filePath, $base_folder . "/" . $relativePath );
			}
		}

		$zip->close();

		return $zipped;
	}

	/**
	 * UnZip a zip file
	 *
	 * @param $zip_file
	 * @param string $dir
	 *
	 * @return bool
	 */
	public static function unZip( $zip_file, $dir = '' ) {
		$zip = new \ZipArchive();
		$res = $zip->open( $zip_file );
		if ( $dir === '' ) {
			$dir = str_replace( ".zip", "", $zip_file );
		}
		if ( ! file_exists( $dir ) ) {
			mkdir( $dir, 0755, true );
		}
		if ( $res === true ) {
			$zip->extractTo( $dir );
			$zip->close();

			return true;
		} else {
			return false;
		}
	}

	/**
	 * @param $dir
	 * @param bool|true $recur
	 *
	 * @return array
	 */
	public static function scanDir( $dir, $recur = true, $abspath = true, $filter = null, $md5_index = false ) {
		$dir = trailingslashit( realpath( $dir ) );
		if ( $dir === '/' || $dir === '' ) {
			return array();
		}
		$tmpfiles = file_exists( $dir ) ? array_diff( scandir( $dir ), array( ".", "..", ".DS_Store", ".htaccess" ) ) : array();
		$files    = array();
		foreach ( $tmpfiles as $file ) {
			if ( is_dir( $dir . $file ) && $recur == true ) {
				$files = array_merge( $files, self::scanDir( $dir . $file, true, $abspath, $filter, $md5_index ) );
			} else {
				if ( ! $filter || substr_count( $file, $filter ) > 0 ) {
					$path = $abspath ? realpath( $dir . $file ) : $file;
					$path = str_replace( "\\", "/", $path );
					if ( $md5_index ) {
						$files[ md5( $path ) ] = $path;
					} else {
						$files[] = $path;
					}
				}
			}
		}

		return $files;
	}


	/**
	 * Get formatted file size
	 *
	 * @param $dir
	 *
	 * @return string
	 */
	function fileSize( $filepath ) {
		if ( __::is_url( $filepath ) ) {
			return '0 KB';
		}
		$filepath = WPDM()->fileSystem->locateFile( $filepath );
		if ( ! $filepath ) {
			return '0 KB';
		}

		return __::formatBytes( filesize( $filepath ) );
	}

	/**
	 * Calculate directory size
	 *
	 * @param $dir
	 *
	 * @return string
	 */
	function dirSize( $dir ) {
		$bytestotal = 0;
		$path       = realpath( $dir );
		if ( $path !== false ) {
			foreach ( new \RecursiveIteratorIterator( new \RecursiveDirectoryIterator( $path, \FilesystemIterator::SKIP_DOTS ) ) as $object ) {
				try {
					$bytestotal += $object->getSize();
				} catch ( \Exception $e ) {

				}
			}
		}
		$bytestotal = $bytestotal / 1024;
		$bytestotal = $bytestotal / 1024;

		return number_format( $bytestotal, 2 );
	}

	/**
	 * @param $dir
	 * @param bool|true $recur
	 *
	 * @return array
	 */
	public static function listFiles( $dir, $recur = true, $abspath = true ) {
		$dir = realpath( $dir ) . "/";
		if ( $dir == '/' || $dir == '' ) {
			return array();
		}
		$tmpfiles = file_exists( $dir ) ? array_diff( scandir( $dir ), array( ".", ".." ) ) : array();
		$files    = array();
		foreach ( $tmpfiles as $file ) {
			if ( is_dir( $dir . $file ) && $recur == true ) {
				$files = array_merge( $files, self::scanDir( $dir . $file, true ) );
			} else if ( ! is_dir( $dir . $file ) ) {
				$files[] = $abspath ? $dir . $file : $file;
			}
		}

		return $files;
	}

	/**
	 * @param $dir
	 * @param bool|true $recur
	 *
	 * @return array
	 */
	public static function subDirs( $dir, $abspath = true ) {
		$dir = realpath( $dir ) . "/";
		$dir = str_replace( "\\", "/", $dir );
		if ( $dir == '/' || $dir == '' ) {
			return array();
		}
		$tmpfiles = file_exists( $dir ) ? array_diff( scandir( $dir ), array( ".", ".." ) ) : array();
		$subdirs  = array();
		foreach ( $tmpfiles as $file ) {
			if ( is_dir( $dir . $file ) ) {
				$subdirs[] = $abspath ? $dir . $file : $file;
			}

		}

		return $subdirs;
	}


	/**
	 * @param $dir
	 * @param bool|true $recur
	 *
	 * @return array|bool
	 */
	public static function deleteFiles( $dir, $recur = true, $filter = '*' ) {
		$dir = realpath( $dir ) . "/";
		if ( $dir == '/' || $dir == '' ) {
			return array();
		}
		$tmpfiles = file_exists( $dir ) ? array_diff( scandir( $dir ), array( ".", ".." ) ) : array();
		$files    = array();
		foreach ( $tmpfiles as $file ) {
			if ( is_dir( $dir . $file ) && $recur == true ) {
				$files = array_merge( $files, self::scanDir( $dir . $file, true ) );
			} else {
				if ( is_array( $filter ) ) {
					$ext        = isset( $filter['ext'] ) ? $filter['ext'] : '*';
					$expiretime = isset( $filter['filetime'] ) ? $filter['filetime'] : null;
					$delete     = true;
					$filetime   = filectime( $dir . $file );
					if ( ! $filetime || ! $expiretime || $filetime < $expiretime ) {
						if ( $ext === '*' || substr_count( $file, $ext ) > 0 ) {
							@unlink( $dir . $file );
						}
					}
				} else {
					if ( $filter === '*' || substr_count( $file, $filter ) > 0 ) {
						@unlink( $dir . $file );
					}
				}
			}
		}

		return true;
	}

	/**
	 * @param $src
	 * @param $dst
	 */
	public static function copyDir( $src, $dst ) {
		$src = realpath( $src );
		$dir = opendir( $src );

		$dst = realpath( $dst ) . '/' . basename( $src );
		@mkdir( $dst );

		while ( false !== ( $file = readdir( $dir ) ) ) {
			if ( ( $file != '.' ) && ( $file != '..' ) ) {
				if ( is_dir( $src . '/' . $file ) ) {
					self::copyDir( $src . '/' . $file, $dst . '/' . $file );
				} else {
					copy( $src . '/' . $file, $dst . '/' . $file );
				}
			}
		}
		closedir( $dir );
	}

	/**
	 * Generates image thumbnail
	 *
	 * @param $path
	 * @param $width
	 * @param $height
	 * @param |null $crop
	 * @param bool $usecache
	 *
	 * @return string|string[]
	 */
	public static function imageThumbnail( $path, $width, $height, $crop = WPDM_USE_GLOBAL, $usecache = true ) {
		$original_path = $path;

		if ( ! function_exists( 'get_home_path' ) ) {
			require_once ABSPATH . 'wp-admin/includes/file.php';
		}
		$abspath = get_home_path();

		$abspath  = str_replace( "\\", "/", ABSPATH );
		$cachedir = str_replace( "\\", "/", WPDM_CACHE_DIR );
		$path     = str_replace( "\\", "/", $path );
		if ( is_ssl() ) {
			$path = str_replace( "http://", "https://", $path );
		} else {
			$path = str_replace( "https://", "http://", $path );
		}
		$path = str_replace( site_url( '/' ), $abspath, $path );

		$crop = $crop === WPDM_USE_GLOBAL ? get_option( '__wpdm_crop_thumbs', false ) : $crop;

		if ( strpos( $path, '.wp.com' ) ) {
			$path = explode( "?", $path );
			$path = $path[0] . "?resize={$width},{$height}";

			return $path;
		}

		if ( strpos( $path, '://' ) ) {
			return $path;
		}
		if ( ! file_exists( $path ) && wpdm_is_url( $original_path ) ) {
			return $original_path;
		}
		if ( ! file_exists( $path ) ) {
			return WPDM_BASE_URL . 'assets/images/404.jpg';
		}


        $thumbname = md5( $path.$width.$height );
		$name_p    = explode( ".", $path );
		$ext       = "." . end( $name_p );
		$filename  = basename( $path );
		//$thumbpath = $cachedir . str_replace( $ext, "-{$width}x{$height}" . $ext, $filename );
		$thumbpath = $cachedir . $thumbname.$ext;
        //wpdmdd($thumbpath);

		if ( file_exists( $thumbpath ) && $usecache ) {
			$thumbpath = str_replace( $cachedir, WPDM_CACHE_URL, $thumbpath );
			return $thumbpath;
		}

        try {
	        $image = wp_get_image_editor( $path );
	        $fullurl = str_replace( $cachedir, WPDM_CACHE_URL, $path );
	        if ( ! is_wp_error( $image ) ) {
		        //if ( is_wp_error( $image->resize( $width, $height, true ) ) ) return $fullurl;
		        try {
			        $image->resize( $width, $height, $crop );
			        $image->save( $thumbpath );
		        } catch ( \Exception $e ) {
			        return "https://fakeimg.pl/600x400?text=x&font=museo";
		        }

	        } else {
		        return str_replace( ABSPATH, home_url( '/' ), $path );
	        }

	        $thumb_size = $image->get_size();
	        if ( $thumb_size['width'] < $width || $thumb_size['height'] < $height ) {
		        if ( $height == 0 ) {
			        $height = $thumb_size['height'];
		        }
		        $_image_back = imagecreatetruecolor( $width, $height );
		        $color       = imagecolorallocatealpha( $_image_back, 255, 255, 255, 127 );
		        imagefill( $_image_back, 0, 0, $color );
		        if ( strstr( $thumbpath, ".png" ) ) {
			        $_image_top = imagecreatefrompng( $thumbpath );
		        }
		        if ( strstr( $thumbpath, ".gif" ) ) {
			        $_image_top = imagecreatefromgif( $thumbpath );
		        }
		        if ( strstr( $thumbpath, ".jpg" ) || strstr( $thumbpath, ".jpeg" ) ) {
			        $_image_top = imagecreatefromjpeg( $thumbpath );
		        }
		        if ( ! isset( $_image_top ) || ! $_image_top ) {
			        return $thumbpath;
		        }
		        $imgw = imagesx( $_image_top );
		        $imgh = imagesy( $_image_top );
		        $posx = (int) ( ( $width - $imgw ) / 2 );
		        $posy = (int) ( ( $height - $imgh ) / 2 );
		        imagecopy( $_image_back, $_image_top, $posx, $posy, 0, 0, $imgw, $imgh );
		        imagepng( $_image_back, $thumbpath );
		        imagedestroy( $_image_back );
	        }

	        $thumbpath = str_replace( "\\", "/", $thumbpath );
	        $thumbpath = str_replace( $cachedir, WPDM_CACHE_URL, $thumbpath );
        } catch ( \Exception $e ) {
            wpdmdd($e->getMessage());
        }

		return $thumbpath;
	}

	/**
	 * @param $pdf
	 * @param $id
	 *
	 * @return string
	 * @usage Generates thumbnail from PDF file. [ From v4.1.3 ]
	 */
	public static function pdfThumbnail( $pdf, $id ) {
		$pdfurl = '';
		if ( strpos( $pdf, "://" ) ) {
			$pdfurl = $pdf;
			$pdf    = str_replace( home_url( '/' ), ABSPATH, $pdf );
		}
		if ( $pdf == $pdfurl ) {
			return '';
		}
		if ( file_exists( $pdf ) ) {
			$source = $pdf;
		} else {
			$source = UPLOAD_DIR . $pdf;
		}
		if ( ! file_exists( WPDM_CACHE_DIR . "pdfthumbs/" ) ) {
			@mkdir( WPDM_CACHE_DIR . "pdfthumbs/", 0755 );
			@chmod( WPDM_CACHE_DIR . "pdfthumbs/", 0755 );
		}
		$dest   = WPDM_CACHE_DIR . "pdfthumbs/{$id}.png";
		$durl   = WPDM_CACHE_URL . "pdfthumbs/{$id}.png";
		$ext    = explode( ".", $source );
		$ext    = end( $ext );
		$colors = WPDM()->setting->ui_colors;
		$color  = is_array( $colors['secondary'] ) && isset( $colors['secondary'] ) ? str_replace( "#", "", $colors['secondary'] ) : '6c757d';
		if ( $ext != 'pdf' ) {
			return '';
		}
		if ( file_exists( $dest ) ) {
			return $durl;
		}
		if ( ! file_exists( $source ) ) {
			$source = utf8_encode( $source );
		}
		$source = $source . '[0]';
		if ( ! class_exists( '\Imagick' ) ) {
			return "https://via.placeholder.com/600x900/{$color}/FFFFFF?text=[+Imagick+Missing+]";
		} // "Error: Imagick is not installed properly";
		try {
			$image = new \imagick( $source );
			$image->setResolution( 1200, 0 );
			$image->setImageFormat( "png" );
			$image->writeImage( $dest );
		} catch ( \Exception $e ) {
			return "https://via.placeholder.com/600x900/{$color}/FFFFFF?text=" . urlencode( $e->getMessage() );
			//return '';
		}

		return $durl;
	}

	function filePreview( $file, $width = 150, $height = 150, $pid = 0) {
        if(!__::is_url($file)) {
	        $ext = self::fileExt( $file );
	        if ( $ext === 'pdf' ) {
		        return self::pdfThumbnail( $file, md5( $file ) );
	        }
	        if ( in_array( $ext, [ 'png', 'jpg', 'jpeg' ] ) ) {
		        return self::imageThumbnail( $file, $width, $height, true );
	        }

	        return self::fileTypeIcon( $ext );
        } else {
	        $ext = self::fileExt( $file );
            $preview = self::fileTypeIcon( $ext );
            $preview = apply_filters( 'wpdm_remote_file_preview', $preview, $file, $width, $height, $pid );
            return $preview;
        }
    }

	/**
	 * @usgae Block http access to a dir
	 *
	 * @param $dir
	 */
	public static function blockHTTPAccess( $dir, $fileType = '*' ) {
		$cont = "RewriteEngine On\r\n<Files {$fileType}>\r\nDeny from all\r\n</Files>\r\n";
		@file_put_contents( $dir . '/.htaccess', $cont );
		file_put_contents($dir . '/index.php', '<?php // ?>');
	}

	/**
	 * @usage Google Doc Preview Embed
	 *
	 * @param $url
	 *
	 * @return string
	 */
	public static function docViewer( $url, $ID, $ext = '' ) {

		$doc_preview_html = "";
		if ( $ext == 'pdf' ) {
			$doc_preview_html = '<iframe src="https://docs.google.com/viewer?url=' . urlencode( $url ) . '&embedded=true" width="100%" height="600" style="border: none;"></iframe>';
		} else {
			$doc_preview_html = '<iframe src="https://view.officeapps.live.com/op/view.aspx?src=' . urlencode( $url ) . '&embedded=true" width="100%" height="600" style="border: none;"></iframe>';
		}
		$doc_preview_html = apply_filters( 'wpdm_doc_viewer', $doc_preview_html, $ID, $url, $ext );

		return $doc_preview_html;
	}

	/**
	 * Retrieve absolute path of the given file ( $file ) assiciated with the given package id ( $pid )
	 *
	 * @param $file
	 * @param $pid
	 *
	 * @return string
	 */
	public static function fullPath( $file, $pid ) {
		$post            = get_post( $pid );
		$user            = get_user_by( 'id', $post->post_author );
		$user_upload_dir = UPLOAD_DIR . $user->user_login . '/';
		if ( file_exists( UPLOAD_DIR . $file ) ) {
			$fullpath = UPLOAD_DIR . $file;
		} else if ( file_exists( $user_upload_dir . $file ) ) {
			$fullpath = $user_upload_dir . $file;
		} else if ( file_exists( $file ) ) {
			$fullpath = $file;
		} else {
			$fullpath = '';
		}

		return $fullpath;
	}

	/**
	 * Get the file extension
	 *
	 * @param $file
	 *
	 * @return string
	 */
	public static function fileExt( $file ) {
		$ext = pathinfo( $file, PATHINFO_EXTENSION );
		$ext = strtolower( $ext );
		if ( $ext === '' ) {
			$ext = "|=|";
		}

		return $ext;
	}

    public static function mimeTypes( $ext = false ) {
	    $mimeTypes = [
		    // Images
		    'jpg' => ['image/jpeg', 'image/pjpeg'],
		    'jpeg' => ['image/jpeg', 'image/pjpeg'],
		    'png' => ['image/png'],
		    'gif' => ['image/gif'],
		    'bmp' => ['image/bmp', 'image/x-windows-bmp'],
		    'svg' => ['image/svg+xml'],
		    'webp' => ['image/webp'],
		    'ico' => ['image/x-icon', 'image/vnd.microsoft.icon'],

		    // Documents
		    'pdf' => ['application/pdf'],
		    'doc' => ['application/msword'],
		    'docx' => ['application/vnd.openxmlformats-officedocument.wordprocessingml.document'],
		    'xls' => ['application/vnd.ms-excel'],
		    'xlsx' => ['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'],
		    'ppt' => ['application/vnd.ms-powerpoint'],
		    'pptx' => ['application/vnd.openxmlformats-officedocument.presentationml.presentation'],
		    'odt' => ['application/vnd.oasis.opendocument.text'],
		    'ods' => ['application/vnd.oasis.opendocument.spreadsheet'],
		    'txt' => ['text/plain'],
		    'rtf' => ['application/rtf', 'text/rtf'],

		    // Archives
		    'zip' => ['application/zip', 'application/x-zip-compressed'],
		    'rar' => ['application/x-rar-compressed', 'application/vnd.rar'],
		    'tar' => ['application/x-tar'],
		    'gz' => ['application/gzip'],
		    '7z' => ['application/x-7z-compressed'],

		    // Audio
		    'mp3' => ['audio/mpeg', 'audio/mp3'],
		    'wav' => ['audio/wav', 'audio/x-wav'],
		    'ogg' => ['audio/ogg', 'application/ogg'],
		    'flac' => ['audio/flac'],
		    'm4a' => ['audio/mp4', 'audio/x-m4a'],

		    // Video
		    'mp4' => ['video/mp4'],
		    'avi' => ['video/x-msvideo'],
		    'mov' => ['video/quicktime'],
		    'wmv' => ['video/x-ms-wmv'],
		    'mkv' => ['video/x-matroska'],
		    'webm' => ['video/webm'],

		    // Web
		    'html' => ['text/html'],
		    'htm' => ['text/html'],
		    'css' => ['text/css'],
		    'js' => ['application/javascript', 'text/javascript'],
		    'json' => ['application/json'],
		    'xml' => ['application/xml', 'text/xml'],

		    // Programming
		    'php' => ['text/x-php', 'application/x-httpd-php', 'text/php'],
		    'py' => ['text/x-python', 'application/x-python'],
		    'java' => ['text/x-java-source', 'text/java'],
		    'c' => ['text/x-c'],
		    'cpp' => ['text/x-c++', 'text/x-c++src'],
		    'cs' => ['text/x-csharp'],
		    'rb' => ['text/x-ruby'],
		    'go' => ['text/x-go'],
		    'pl' => ['text/x-perl'],
		    'sh' => ['text/x-shellscript', 'application/x-sh'],

		    // Fonts
		    'ttf' => ['application/x-font-ttf', 'font/ttf'],
		    'otf' => ['application/x-font-opentype', 'font/otf'],
		    'woff' => ['application/font-woff', 'font/woff'],
		    'woff2' => ['application/font-woff2', 'font/woff2'],
		    'eot' => ['application/vnd.ms-fontobject'],

		    // Other
		    'csv' => ['text/csv'],
		    'sql' => ['application/sql', 'text/x-sql'],
		    'apk' => ['application/vnd.android.package-archive'],
		    'exe' => ['application/x-msdownload', 'application/x-executable'],
		    'dll' => ['application/x-msdownload'],
		    'bin' => ['application/octet-stream'],
		    'iso' => ['application/x-iso9660-image'],
            'dmg' => ['application/x-apple-diskimage'],
            'torrent' => ['application/x-bittorrent'],
            'srt' => ['application/x-subrip'],
	    ];
	    $mimeTypes = apply_filters( 'wpdm_mime_types', $mimeTypes );
        $foundMimeTypes = $ext === false ? $mimeTypes : wpdm_valueof($mimeTypes, $ext);
        return is_array($foundMimeTypes) ? $foundMimeTypes : ['application/octet-stream'];
    }

	public static function getMimeType($file) {
		if(!class_exists( '\finfo' )) {
            if(function_exists('mime_content_type'))
                return mime_content_type( $file );
            error_log("WPDM: Enable file info extension to use mime type detection. See: https://www.php.net/manual/en/book.finfo.php");
			return false;
		}
		$finfo = new \finfo(FILEINFO_MIME_TYPE);
		if($finfo) {
			$mimeType = $finfo->file( $file );
			return $mimeType;
		} else {
			return false;
		}
	}

	/**
	 * Validates if a file's actual type matches its extension
	 *
	 * This function examines the file content to determine its actual type
	 * and compares it with the expected type based on the file extension.
	 * It helps prevent security issues like XSS by ensuring users cannot
	 * upload unauthorized file types by simply changing the extension.
	 *
	 * @param string $filePath Path to the file to be validated
	 * @return array Returns an array with the following keys:
	 *               - 'valid' (bool): Whether the file type matches the extension
	 *               - 'message' (string): Detailed explanation of the result
	 *               - 'actual_type' (string): The detected MIME type
	 *               - 'expected_type' (string): The expected MIME type based on extension
	 *               - 'extension' (string): The file extension
	 */
	public static function validateFileType($filePath) {
		// Initialize the result array
		$result = [
			'valid' => false,
			'message' => '',
			'actual_type' => '',
			'expected_type' => '',
			'extension' => ''
		];

		// Check if file exists
		if (!file_exists($filePath)) {
			$result['message'] = 'Error: File does not exist';
			return $result;
		}

		// Check if file is readable
		if (!is_readable($filePath)) {
			$result['message'] = 'Error: File is not readable';
			return $result;
		}

		// Get file extension
		$extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
		$result['extension'] = $extension;

		// If no extension, we can't validate
		if (empty($extension)) {
			$result['message'] = 'Error: File has no extension';
			return $result;
		}

		// Get actual MIME type using finfo
		$actualType = self::getMimeType($filePath);
		$result['actual_type'] = $actualType;

		// Get expected MIME type based on extension
		$expectedTypes = self::mimeTypes($extension);

		$result['expected_type'] = implode(' or ', $expectedTypes);

		// Check if actual type matches any of the expected types
		$isValid = in_array($actualType, $expectedTypes);

		// Set result values
		$result['valid'] = $isValid;

		// Set appropriate message if not already set
		if (empty($result['message'])) {
			if ($isValid) {
				$result['message'] = 'File type matches the extension';
			} else {
				$result['message'] = 'File type does not match the extension';
			}
		}

		return $result;
	}

    public static function validateMimeType($file, $ext) {
        $actualMimeType = self::getMimeType( $file );
        $extMimeType = self::mimeTypes( $ext );
	    return in_array($actualMimeType, $extMimeType);
    }

	public static function validateUploadMimeType($tmp_file, $file_name) {
		if (function_exists('extension_loaded') && !extension_loaded('fileinfo')) {
			error_log("WPDM: Enable file info extension to use mime type detection. See: https://www.php.net/manual/en/book.finfo.php");
			return true;
		}
		$ext = self::fileExt( $file_name );
		$actualMimeType = self::getMimeType( $tmp_file );
		$extMimeType = self::mimeTypes( $ext );
		$isValidMimeType = in_array($actualMimeType, $extMimeType);
		if(!$isValidMimeType)
			error_log("Actual Mime Type: {$actualMimeType}. File Extension: {$ext}. Expected Mime Type: ".implode(" or ", $extMimeType).".");
		$isValidMimeType = apply_filters("wpdm_is_valid_mime_type", $isValidMimeType, $actualMimeType, $extMimeType);
		return $isValidMimeType;
	}

	public static function mediaURL( $pid, $fileID, $fileName = '' ) {
		if ( $fileName == '' ) {
			$files    = WPDM()->package->getFiles( $pid );
			$fileName = wpdm_basename( $files[ $fileID ] );
		}

		return WPDM()->package->expirableDownloadLink( $pid, 5, 1800 ) . "&ind={$fileID}&file={$fileName}";
	}

	/**
	 * Find or generate file type icon and returns the url
	 *
	 * @param $filename_or_ext
	 * @param string $color
	 * @param bool $return
	 *
	 * @return string File Type Icon URL
	 */
	static function fileTypeIcon( $filename_or_ext, $color = '#269def|#26bdef', $return = true ) {
		$ext = $filename_or_ext;
		if ( substr_count( $ext, '.' ) ) {
			$ext = self::fileExt( $ext );
		}
		$upload_dir         = wp_upload_dir();
		$_upload_dir        = $upload_dir['basedir'];
		$_upload_url        = $upload_dir['baseurl'];
		$file_type_icon_url = '';
		if ( file_exists( $_upload_dir . "/wpdm-file-type-icons/" . $ext . ".svg" ) ) {
			$file_type_icon_url = $_upload_url . "/wpdm-file-type-icons/" . $ext . ".svg";
		} else if ( file_exists( WPDM_BASE_DIR . "assets/file-type-icons/" . $ext . ".svg" ) ) {
			$file_type_icon_url = WPDM_BASE_URL . "assets/file-type-icons/" . $ext . ".svg";
		}
		if ( $file_type_icon_url === '' ) {
			ob_start();
			$id         = uniqid();
			$ext        = strtoupper( $ext );
			$color = explode("|", $color);
			$ext        = substr( $ext, 0, 3 );
			?>

            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 40 40">
                <defs>
                    <linearGradient id="gradient" x1="0" y1="0" x2="0" y2="1">
                        <stop stop-color="#269def" offset="0"/>
                        <stop stop-color="#26bdef" offset="1"/>
                    </linearGradient>
                </defs>
                <g>
                    <rect fill="url(#gradient)" x="0" y="0" width="40" height="40" rx="3" ry="3"/>
                    <text x="5" y="19" font-family="Arial, Helvetica, sans-serif" font-size="13px" letter-spacing="1" fill="#FFFFFF">
                        <tspan><?php echo $ext; ?></tspan>
                        <tspan x="6" y="28">_</tspan>
                    </text>
                </g>
            </svg>

			<?php
			$file_type_icon_url = ob_get_clean();
			$file_type_icon_url = "data:image/svg+xml;base64," . base64_encode( $file_type_icon_url );
		}
		$file_type_icon_url = apply_filters( "wpdm_file_type_icon", $file_type_icon_url, $filename_or_ext );
		if ( $return ) {
			return $file_type_icon_url;
		}
		echo $file_type_icon_url;
	}

	/**
	 * Generates a quick download url for the given file
	 *
	 * @param $file
	 * @param int $expire
	 *
	 * @return string|void
	 */
	public static function instantDownloadURL( $file, $expire = 3600 ) {
		$id = uniqid();
		TempStorage::set( "__wpdm_instant_download_{$id}", $file, $expire );

		return home_url( "/?wpdmidl={$id}" );
	}

	/**
	 * Get allowed file type for upload
	 *
	 * @param bool $ARRAY
	 *
	 * @return array|false|int[]|mixed|string|string[]|void
	 */
	function getAllowedFileTypes( $ARRAY = true ) {
		$allowed_file_types = get_option( "__wpdm_allowed_file_types", '' );
		if ( $allowed_file_types === '' || ! $allowed_file_types ) {
			$wp_allowed_file_types = get_allowed_mime_types();
			$wp_allowed_file_exts  = array_keys( $wp_allowed_file_types );
			$wp_allowed_file_exts  = implode( ",", $wp_allowed_file_exts );
			$wp_allowed_file_exts  = str_replace( "|", ",", $wp_allowed_file_exts );
			$allowed_file_types    = $wp_allowed_file_exts;
		}
		$wp_allowed_file_types_array = explode( ",", $allowed_file_types );
		$wp_allowed_file_types_array = array_map( "trim", $wp_allowed_file_types_array );

		return $ARRAY ? $wp_allowed_file_types_array : $allowed_file_types;
	}

	/**
	 * Check for blocked file types
	 *
	 * @param $filename
	 * @param string $abspath
	 *
	 * @return bool
	 */
	function isBlocked( $filename, $abspath = '' ) {
		if ( $filename === '' ) {
			return true;
		}

		$types = $this->getAllowedFileTypes();

		if ( in_array( '*', $types ) ) {
			return false;
		}
		$ext = null;
		if ( $abspath && file_exists( $abspath ) ) {
			$mimes = wp_get_mime_types();
			foreach ( $types as $type ) {
				if ( ! isset( $mimes[ $type ] ) ) {
					$mimes[ $type ] = 'application/' . $type;
				}
			}
			$fileinfo = wp_check_filetype_and_ext( $abspath, $filename, $mimes );
			$ext      = wpdm_valueof( $fileinfo, 'ext' );
		}

		if ( ! $ext ) {
			$ext = self::fileExt( $filename );
		}

		$ext = strtolower( $ext );

		return ! in_array( $ext, $types );
	}

	/**
	 * Convert absolute path to relative path
	 *
	 * @param $abs_path
	 *
	 * @return array|string|string[]
	 */
	function relPath( $abs_path ) {
		$abs_path = str_replace( "\\", "/", $abs_path );
        $up_root = str_replace( "\\", "/", UPLOAD_DIR );
		$rel_path = str_replace( $up_root, "", $abs_path );
        $abs_root = str_replace( "\\", "/", ABSPATH );
		$rel_path = str_replace( $abs_root, "", $rel_path );
		return $rel_path;
	}

	/**
	 * <p>Resolve absolute file path from the given relative path, check file in all possible wpdm upload dirs</p>
	 * <p>Returns absolute path if file is found, returns false if file is not found</p>
	 *
	 * @param $rel_path
	 * @param null $pid
	 *
	 * @return bool|string
	 */
	function absPath( $rel_path, $pid = null ) {
		$abs_path = false;

        if(!$rel_path) return false;

		$upload_dir      = wp_upload_dir();
		$upload_base_url = $upload_dir['baseurl'];
		$upload_dir      = $upload_dir['basedir'];

		//Covert media library url to file path
		if ( __::is_url( $rel_path ) ) {
			$abs_path = str_replace( $upload_base_url, $upload_dir, $rel_path );
			if ( __::is_url( $abs_path ) ) {
				return $rel_path;
			}
		}

		if ( substr_count( $rel_path, './' ) ) {
			return false;
		}

		$fixed_abs_path = false;
		if ( substr_count( $rel_path, 'wp-content' ) > 0 && substr_count( $rel_path, WP_CONTENT_DIR ) === 0 ) {
			$rel_rel_path   = explode( "wp-content", $rel_path );
			$rel_rel_path   = end( $rel_rel_path );
			$fixed_abs_path = WP_CONTENT_DIR . $rel_rel_path;
		}

		$file_browser_root  = get_option( '_wpdm_file_browser_root', '' );
		$network_upload_dir = explode( "sites", UPLOAD_DIR );
		$network_upload_dir = $network_upload_dir[0];
		$network_upload_dir = $network_upload_dir . "download-manager-files/";

		if ( file_exists( $rel_path ) ) {
			$abs_path = $rel_path;
		} else if ( file_exists( UPLOAD_DIR . $rel_path ) ) {
			$abs_path = UPLOAD_DIR . $rel_path;
		} else if ( file_exists( $network_upload_dir . $rel_path ) ) {
			$abs_path = $network_upload_dir . $rel_path;
		} else if ( file_exists( ABSPATH . $rel_path ) ) {
			$abs_path = ABSPATH . $rel_path;
		} else if ( file_exists( trailingslashit( $file_browser_root ) . $rel_path ) ) {
			$abs_path = trailingslashit( $file_browser_root ) . $rel_path;
		} else if ( $fixed_abs_path && file_exists( $fixed_abs_path ) ) {
			$abs_path = $fixed_abs_path;
		} else if ( $pid ) {
			$user_upload_dir = null;
			$package         = get_post( $pid );
			if ( is_object( $package ) ) {
				$author = get_user_by( 'id', $package->post_author );
				if ( $author ) {
					$user_upload_dir = UPLOAD_DIR . $author->user_login . '/';
				}
			}
			if ( $user_upload_dir && file_exists( $user_upload_dir . $rel_path ) ) {
				$abs_path = $user_upload_dir . $rel_path;
			}
		}

		$abs_path = str_replace( '\\', '/', $abs_path );
		if ( ! $abs_path ) {
			return null;
		}
		$real_path = realpath( $abs_path );

		return $real_path;
	}

	/**
	 * Count pages in a PDF file
	 *
	 * @param $path
	 *
	 * @return int|string
	 */
	function countPDFPages( $path ) {
		if ( self::fileExt( $path ) !== 'pdf' ) {
			return 1;
		}
		$fp  = @fopen( preg_replace( "/\[(.*?)\]/i", "", $path ), "r" );
		$max = 0;
		if ( ! $fp ) {
			return "Could not open file: $path";
		} else {
			while ( ! @feof( $fp ) ) {
				$line = @fgets( $fp, 255 );
				if ( preg_match( '/\/Count [0-9]+/', $line, $matches ) ) {
					preg_match( '/[0-9]+/', $matches[0], $matches2 );
					if ( $max < $matches2[0] ) {
						$max = trim( $matches2[0] );
						break;
					}
				}
			}
			@fclose( $fp );
		}

		return $max;
	}

	function filePermissions( $path, $parsed = true ) {
		$perm  = substr( sprintf( '%o', fileperms( $path ) ), - 3 );
		$perms = [ 'user' => $perm[0], 'group' => $perm[1], 'world' => $perm[2] ];
		if ( ! $parsed ) {
			return $perm;
		}

		return $perms;
	}

	function locateFile( $file ) {
		return $this->absPath( $file );
	}

	function allowedPath( $absPath ) {
		if ( ! $absPath ) {
			return false;
		}
		$absPath          = str_replace( "\\", "/", $absPath );
		$upload_dir       = str_replace( "\\", "/", UPLOAD_DIR );
		$upload_dir_real  = realpath($upload_dir);
		$asset_root       = str_replace( "\\", "/", AssetManager::root() );
		$allowedPathCheck = apply_filters( "wpdm_allowed_path_check", true );
		if ( substr_count( $absPath, $upload_dir ) || substr_count( $absPath, $upload_dir_real ) || substr_count( $absPath, $asset_root ) || ! $allowedPathCheck ) {
			return true;
		}

		return false;
	}

	function fileHash( $filePath ) {
		return hash_file('sha256', $filePath);
	}
}

