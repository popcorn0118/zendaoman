<?php
if(!defined("ABSPATH")) die("Shit happens!");
$afiles = maybe_unserialize(get_post_meta(get_the_ID(), "__wpdm_files", true));
if(!is_array($afiles)) $afiles = [];
$afiles = array_values($afiles);
$afile = wpdm_valueof($afiles, 0);
?>
<div class="w3eden">

    <input type="hidden" name="file[files][]" value="<?php  echo esc_attr($afile); ?>" id="wpdmfile" />

    <div class="cfile" id="cfl">
		<?php
        $icon_info = ['icon' => 'fa-file', 'color' => '#28a745', 'gradient' => 'linear-gradient(135deg, #28a745 0%, #20c997 100%)'];
		$filesize = "<em style='color: darkred'>( ".__("attached file is missing/deleted",'download-manager')." )</em>";
        $afile = rtrim($afile);

		$url = false;
		if($afile !=''){
			if(substr_count($afile, "://") > 0){
				$fparts = parse_url($afile);
				$url = true;
				$hurl = strlen($fparts['host']) > 20 ? substr($fparts['host'], 0, 20)."..." : $fparts['host'];
				$filesize = "<span class='w3eden'><span class='text-primary ellipsis ttip' title='".esc_attr($afile)."'><i class='fa fa-link'></i>".esc_html($hurl)."</span></span>";
			}
			else {
				$filesize = wpdm_file_size($afile);
			}

			if(strpos($afile, "#")) {
				$afile = explode("#", $afile);
				$afile = $afile[1];
			}

            // Determine file icon based on extension
            $file_ext = strtolower(pathinfo($afile, PATHINFO_EXTENSION));
            $icon_map = [
                // Documents
                'pdf' => ['icon' => 'fa-file-pdf', 'color' => '#e53e3e', 'gradient' => 'linear-gradient(135deg, #e53e3e 0%, #c53030 100%)'],
                'doc' => ['icon' => 'fa-file-word', 'color' => '#2b6cb0', 'gradient' => 'linear-gradient(135deg, #3182ce 0%, #2b6cb0 100%)'],
                'docx' => ['icon' => 'fa-file-word', 'color' => '#2b6cb0', 'gradient' => 'linear-gradient(135deg, #3182ce 0%, #2b6cb0 100%)'],
                'xls' => ['icon' => 'fa-file-excel', 'color' => '#276749', 'gradient' => 'linear-gradient(135deg, #38a169 0%, #276749 100%)'],
                'xlsx' => ['icon' => 'fa-file-excel', 'color' => '#276749', 'gradient' => 'linear-gradient(135deg, #38a169 0%, #276749 100%)'],
                'ppt' => ['icon' => 'fa-file-powerpoint', 'color' => '#c05621', 'gradient' => 'linear-gradient(135deg, #dd6b20 0%, #c05621 100%)'],
                'pptx' => ['icon' => 'fa-file-powerpoint', 'color' => '#c05621', 'gradient' => 'linear-gradient(135deg, #dd6b20 0%, #c05621 100%)'],
                'txt' => ['icon' => 'fa-file-alt', 'color' => '#718096', 'gradient' => 'linear-gradient(135deg, #a0aec0 0%, #718096 100%)'],
                // Archives
                'zip' => ['icon' => 'fa-file-archive', 'color' => '#d69e2e', 'gradient' => 'linear-gradient(135deg, #ecc94b 0%, #d69e2e 100%)'],
                'rar' => ['icon' => 'fa-file-archive', 'color' => '#d69e2e', 'gradient' => 'linear-gradient(135deg, #ecc94b 0%, #d69e2e 100%)'],
                '7z' => ['icon' => 'fa-file-archive', 'color' => '#d69e2e', 'gradient' => 'linear-gradient(135deg, #ecc94b 0%, #d69e2e 100%)'],
                'tar' => ['icon' => 'fa-file-archive', 'color' => '#d69e2e', 'gradient' => 'linear-gradient(135deg, #ecc94b 0%, #d69e2e 100%)'],
                'gz' => ['icon' => 'fa-file-archive', 'color' => '#d69e2e', 'gradient' => 'linear-gradient(135deg, #ecc94b 0%, #d69e2e 100%)'],
                // Images
                'jpg' => ['icon' => 'fa-file-image', 'color' => '#805ad5', 'gradient' => 'linear-gradient(135deg, #9f7aea 0%, #805ad5 100%)'],
                'jpeg' => ['icon' => 'fa-file-image', 'color' => '#805ad5', 'gradient' => 'linear-gradient(135deg, #9f7aea 0%, #805ad5 100%)'],
                'png' => ['icon' => 'fa-file-image', 'color' => '#805ad5', 'gradient' => 'linear-gradient(135deg, #9f7aea 0%, #805ad5 100%)'],
                'gif' => ['icon' => 'fa-file-image', 'color' => '#805ad5', 'gradient' => 'linear-gradient(135deg, #9f7aea 0%, #805ad5 100%)'],
                'svg' => ['icon' => 'fa-file-image', 'color' => '#805ad5', 'gradient' => 'linear-gradient(135deg, #9f7aea 0%, #805ad5 100%)'],
                'webp' => ['icon' => 'fa-file-image', 'color' => '#805ad5', 'gradient' => 'linear-gradient(135deg, #9f7aea 0%, #805ad5 100%)'],
                // Audio
                'mp3' => ['icon' => 'fa-file-audio', 'color' => '#00b5d8', 'gradient' => 'linear-gradient(135deg, #0bc5ea 0%, #00b5d8 100%)'],
                'wav' => ['icon' => 'fa-file-audio', 'color' => '#00b5d8', 'gradient' => 'linear-gradient(135deg, #0bc5ea 0%, #00b5d8 100%)'],
                'ogg' => ['icon' => 'fa-file-audio', 'color' => '#00b5d8', 'gradient' => 'linear-gradient(135deg, #0bc5ea 0%, #00b5d8 100%)'],
                'flac' => ['icon' => 'fa-file-audio', 'color' => '#00b5d8', 'gradient' => 'linear-gradient(135deg, #0bc5ea 0%, #00b5d8 100%)'],
                // Video
                'mp4' => ['icon' => 'fa-file-video', 'color' => '#e53e3e', 'gradient' => 'linear-gradient(135deg, #fc8181 0%, #e53e3e 100%)'],
                'avi' => ['icon' => 'fa-file-video', 'color' => '#e53e3e', 'gradient' => 'linear-gradient(135deg, #fc8181 0%, #e53e3e 100%)'],
                'mov' => ['icon' => 'fa-file-video', 'color' => '#e53e3e', 'gradient' => 'linear-gradient(135deg, #fc8181 0%, #e53e3e 100%)'],
                'mkv' => ['icon' => 'fa-file-video', 'color' => '#e53e3e', 'gradient' => 'linear-gradient(135deg, #fc8181 0%, #e53e3e 100%)'],
                'webm' => ['icon' => 'fa-file-video', 'color' => '#e53e3e', 'gradient' => 'linear-gradient(135deg, #fc8181 0%, #e53e3e 100%)'],
                // Code
                'html' => ['icon' => 'fa-file-code', 'color' => '#ed8936', 'gradient' => 'linear-gradient(135deg, #f6ad55 0%, #ed8936 100%)'],
                'css' => ['icon' => 'fa-file-code', 'color' => '#3182ce', 'gradient' => 'linear-gradient(135deg, #63b3ed 0%, #3182ce 100%)'],
                'js' => ['icon' => 'fa-file-code', 'color' => '#ecc94b', 'gradient' => 'linear-gradient(135deg, #faf089 0%, #ecc94b 100%)'],
                'php' => ['icon' => 'fa-file-code', 'color' => '#667eea', 'gradient' => 'linear-gradient(135deg, #7f9cf5 0%, #667eea 100%)'],
                'json' => ['icon' => 'fa-file-code', 'color' => '#48bb78', 'gradient' => 'linear-gradient(135deg, #68d391 0%, #48bb78 100%)'],
                'xml' => ['icon' => 'fa-file-code', 'color' => '#ed8936', 'gradient' => 'linear-gradient(135deg, #f6ad55 0%, #ed8936 100%)'],
            ];

            $icon_info = $icon_map[$file_ext] ?? ['icon' => 'fa-file', 'color' => '#28a745', 'gradient' => 'linear-gradient(135deg, #28a745 0%, #20c997 100%)'];

            // Use link icon for URLs
            if ($url) {
                $icon_info = ['icon' => 'fa-link', 'color' => '#3182ce', 'gradient' => 'linear-gradient(135deg, #4299e1 0%, #3182ce 100%)'];
            }
            ?>
            <div style="display: flex; align-items: center; gap: 12px; padding: 8px 12px; background: linear-gradient(135deg, #f8fffe 0%, #f0faf7 100%); border: 1px solid #d4edda; border-radius: 8px; transition: all 0.2s ease;">
                <div style="width: 42px; height: 42px; background: <?php echo $icon_info['gradient']; ?>; border-radius: 8px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; box-shadow: 0 2px 8px <?php echo $icon_info['color']; ?>40;">
                    <i class="fas <?php echo $icon_info['icon']; ?>" style="color: #fff; font-size: 18px;"></i>
                </div>
                <div class="media-body" style="flex: 1; min-width: 0;">
                    <strong style="display: block; font-size: 13px; color: #2d3748; line-height: 1.4; word-break: break-word; margin-bottom: 2px;"><?php echo esc_html(basename(urldecode($afile))); ?></strong>
                    <span style="font-size: 12px; color: #718096;"><?php echo $filesize; ?></span>
                </div>
                <a href="#" id="dcf" title="Delete Current File" style="width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; background: #fff; border: 1px solid #e2e8f0; border-radius: 6px; color: #a0aec0; transition: all 0.2s ease; flex-shrink: 0;" onmouseover="this.style.background='#fed7d7'; this.style.borderColor='#fc8181'; this.style.color='#c53030';" onmouseout="this.style.background='#fff'; this.style.borderColor='#e2e8f0'; this.style.color='#a0aec0';">
                    <i class="fa fa-trash" style="font-size: 14px;"></i>
                </a>
            </div>

		<?php } else { ?>
            <div style="display: flex; padding: 10px; gap: 10px; background: linear-gradient(135deg, #fefefe 0%, #f7fafc 100%); border: 2px dashed #e2e8f0; border-radius: 8px; text-align: left;">
                <div style="width: 48px; height: 48px; background: linear-gradient(135deg, #edf2f7 0%, #e2e8f0 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                    <i class="fas fa-cloud-upload-alt" style="font-size: 20px; color: #a0aec0;"></i>
                </div>
                <div>
                    <div style="font-size: 14px; margin-top: 4px; color: #718096; font-weight: 500;"><?php echo __('No file attached yet!', 'download-manager'); ?></div>
                    <span style="font-size: 11px; color: #a0aec0; margin-top: 4px;"><?php echo __('Use any option below', 'download-manager'); ?></span>
                </div>
            </div>
        <?php } ?>
        <div style="clear: both;"></div>
    </div>


    <div id="upload">
        <div id="plupload-upload-ui" class="hide-if-no-js">
            <div id="drag-drop-area">
                <div class="drag-drop-inside" style="margin-top: 40px">
                    <p class="drag-drop-info" style="letter-spacing: 1px;font-size: 10pt"><?php _e('Drop file here','download-manager'); ?><p>
                    <p>&mdash; <?php _ex('or', 'Uploader: Drop file here - or - Select File','download-manager'); ?> &mdash;</p>
                    <p class="drag-drop-buttons">
                        <button id="plupload-browse-button" type="button" class="btn btn-sm btn-success wpdm-whatsapp"><i class="fa fa-file"></i> <?php esc_attr_e('Select File','download-manager'); ?></button><br/>
                        <small style="margin-top: 15px;display: block">[ <?php _e('Max', 'download-manager'); ?>: <?php echo get_option('__wpdm_chunk_upload',0) == 1?'No Limit':(int)(wp_max_upload_size()/1048576).' MB'; ?> ]</small>
                    </p>
                </div>
            </div>
        </div>

		<?php

		$plupload_init = array(
			'runtimes'            => 'html5,silverlight,flash,html4',
			'browse_button'       => 'plupload-browse-button',
			'container'           => 'plupload-upload-ui',
			'drop_element'        => 'drag-drop-area',
			'file_data_name'      => 'package_file',
			'multiple_queues'     => true,
			'url'                 => admin_url('admin-ajax.php'),
			'flash_swf_url'       => includes_url('js/plupload/plupload.flash.swf'),
			'silverlight_xap_url' => includes_url('js/plupload/plupload.silverlight.xap'),
			'filters'             => array(array('title' => __('Allowed Files'), 'extensions' => WPDM()->fileSystem->getAllowedFileTypes(false) )),
			'multipart'           => true,
			'urlstream_upload'    => true,
			// additional post data to send to our ajax hook
			'multipart_params'    => array(
				'_ajax_nonce' => wp_create_nonce(NONCE_KEY),
				'type'          => 'package_attachment',
				'package_id'          => get_the_ID(),
				'action'      => 'wpdm_admin_upload_file',            // the ajax action name
			),
		);

		if(get_option('__wpdm_chunk_upload',0) == 1){
			$plupload_init['chunk_size'] = (int)get_option('__wpdm_chunk_size', 1024).'kb';
			$plupload_init['max_retries'] = 3;
		} else
			$plupload_init['max_file_size'] = wp_max_upload_size().'b';

		// we should probably not apply this filter, plugins may expect wp's media uploader...
		$plupload_init = apply_filters('plupload_init', $plupload_init); ?>

        <script type="text/javascript">

            jQuery(document).ready(function($){

                // create the uploader and pass the config from above
                var uploader = new plupload.Uploader(<?php echo json_encode($plupload_init); ?>);

                // checks if browser supports drag and drop upload, makes some css adjustments if necessary
                uploader.bind('Init', function(up){
                    var uploaddiv = jQuery('#plupload-upload-ui');

                    if(up.features.dragdrop){
                        uploaddiv.addClass('drag-drop');
                        jQuery('#drag-drop-area')
                            .bind('dragover.wp-uploader', function(){ uploaddiv.addClass('drag-over'); })
                            .bind('dragleave.wp-uploader, drop.wp-uploader', function(){ uploaddiv.removeClass('drag-over'); });

                    }else{
                        uploaddiv.removeClass('drag-drop');
                        jQuery('#drag-drop-area').unbind('.wp-uploader');
                    }
                });

                uploader.init();

                // a file was added in the queue
                uploader.bind('FilesAdded', function(up, files){
                    //var hundredmb = 100 * 1024 * 1024, max = parseInt(up.settings.max_file_size, 10);



                    plupload.each(files, function(file){
                        jQuery('#filelist').append(
                            '<div class="file" id="' + file.id + '"><b>' +

                            file.name.replace(/</ig, "&lt;") + '</b> (<span>' + plupload.formatSize(0) + '</span>/' + plupload.formatSize(file.size) + ') ' +
                            '<div class="progress progress-success progress-striped active"><div class="bar fileprogress"></div></div></div>');
                    });

                    up.refresh();
                    up.start();
                });

                uploader.bind('UploadProgress', function(up, file) {

                    jQuery('#' + file.id + " .fileprogress").width(file.percent + "%");
                    jQuery('#' + file.id + " span").html(plupload.formatSize(parseInt(file.size * file.percent / 100)));
                });


                // a file was uploaded
                uploader.bind('FileUploaded', function(up, file, response) {

                    jQuery('#' + file.id ).remove();
                    var d = new Date();
                    var ID = d.getTime();
                    response = response.response;
                    if(response == -3)
                        jQuery('#cfl').html('<span class="text-danger"><i class="fa fa-exclamation-triangle"></i> &nbsp; <?php _e('Invalid File Type!','download-manager');?></span>');
                    else if(response == -2)
                        jQuery('#cfl').html('<span class="text-danger"><i class="fa fa-exclamation-triangle"></i> &nbsp; <?php _e('Unauthorized Access!','download-manager');?></span>');
                    else {
                        var data = response.split("|||");
                        jQuery('#wpdmfile').val(data[1]);
                        jQuery('#cfl').html('<div class="media"><a href="#" class="pull-right ttip" id="dcf" title="<?php _e('Delete Current File', 'download-manager');?>" style="font-size: 24px"><i class="fa fa-trash color-red"></i></a><div class="media-body"><strong>' + data[1] + '</strong><br/>'+data[2]+' </div></div>').slideDown();
                    }
                });
            });

        </script>
        <div id="filelist"></div>

        <div class="clear"></div>
    </div>

    <script type="text/x-template" id="atftpl">
        <div style="display: flex; align-items: center; gap: 12px; padding: 8px 12px; background: linear-gradient(135deg, #f8fffe 0%, #f0faf7 100%); border: 1px solid #d4edda; border-radius: 8px; transition: all 0.2s ease;">
            <div style="width: 42px; height: 42px; background: <?php echo $icon_info['gradient']; ?>; border-radius: 8px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; box-shadow: 0 2px 8px <?php echo $icon_info['color']; ?>40;">
                <i class="fas <?php echo $icon_info['icon']; ?>" style="color: #fff; font-size: 18px;"></i>
            </div>
            <div class="media-body" style="flex: 1; min-width: 0;">
                <strong style="display: block; font-size: 13px; color: #2d3748; line-height: 1.4; word-break: break-word; margin-bottom: 2px;">{{filetitle}}</strong>
                <span style="font-size: 12px; color: #718096;"><i class="fa fa-check-double"></i> Attached</span>
            </div>
            <a href="#" id="dcf" title="Delete Current File" style="width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; background: #fff; border: 1px solid #e2e8f0; border-radius: 6px; color: #a0aec0; transition: all 0.2s ease; flex-shrink: 0;" onmouseover="this.style.background='#fed7d7'; this.style.borderColor='#fc8181'; this.style.color='#c53030';" onmouseout="this.style.background='#fff'; this.style.borderColor='#e2e8f0'; this.style.color='#a0aec0';">
                <i class="fa fa-trash" style="font-size: 14px;"></i>
            </a>
        </div>
    </script>


    <script>
        function wpdm_html_compile(html, dataset){
            return html.replace(/{{(.*?)}}/g,
                function (...match) {
                    return dataset[match[1]];
                });
        }
        function wpdm_attach_file(file)
        {
            jQuery('#wpdmfile').val(file.filepath);
            let atftpl = jQuery('#atftpl').html();
            atftpl = wpdm_html_compile(atftpl, file);
            jQuery('#cfl').html(atftpl);
            //jQuery('#cfl').html('<div class="media"><a href="#" class="pull-right ttip" id="dcf" title="<?php _e('Delete Current File', 'download-manager');?>" style="font-size: 24px"><i class="fa fa-trash color-red"></i></a><div class="media-body"><strong>' + file.filetitle + '</strong><br/>&mdash;</div></div>').slideDown();


        }

        jQuery(function($){


            $('body').on('click', '#dcf', function(){
                if(!confirm('<?php _e('Are you sure?','download-manager'); ?>')) return false;
                $('#wpdmfile').val('');
                $('#cfl').html('<?php _e('<div class="w3eden"><div class="text-danger"><i class="fa fa-check-circle"></i> Removed!</div></div>','download-manager'); ?>');
            });




        });

    </script>


</div>
