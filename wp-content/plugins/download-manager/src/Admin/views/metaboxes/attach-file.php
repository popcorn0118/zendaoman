<?php

$files = maybe_unserialize(get_post_meta($post->ID, '__wpdm_files', true));

if (!is_array($files)) $files = array();

include __DIR__.'/attach-file/upload-file.php';
if(!defined("WPDM_DISABLE_REMOTE_URL_ATTACHMENT") || WPDM_DISABLE_REMOTE_URL_ATTACHMENT === false)
	include __DIR__.'/attach-file/remote-url.php';
if(!defined("WPDM_DISABLE_MEDIA_ATTACHMENT") || WPDM_DISABLE_MEDIA_ATTACHMENT === false)
	include __DIR__.'/attach-file/media-library-file.php';
if(!defined("WPDM_DISABLE_SERVER_FILE_ATTACHMENT") || WPDM_DISABLE_SERVER_FILE_ATTACHMENT === false)
	include __DIR__.'/attach-file/server-file.php';

do_action("wpdm_attach_file_metabox");

if(!defined('WPDM_CLOUD_STORAGE')){
	?>
	<div class="w3eden">
		<br/>
		<div class="panel panel-default" style="margin-bottom: 0">
			<div class="panel-body">
				<small><?php echo esc_attr__('To store files in cloud storage or to link files from cloud storage ( ex: DropBox, Google Drive, Amazon S3), please check our cloud storage add-ons', 'download-manager')  ?></small>
			</div>
			<div class="panel-footer"><a target="_blank" class="btn wpdm-linkedin btn-nlock" href="https://www.wpdownloadmanager.com/downloads/cloud-storage/"><?php echo esc_attr__('Cloud Storage Add-ons', 'download-manager')  ?></a></div>
		</div>
	</div>
	<?php
}
