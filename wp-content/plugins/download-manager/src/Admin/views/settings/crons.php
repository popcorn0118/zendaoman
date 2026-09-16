<?php
/**
 * Date: 9/28/16
 * Time: 9:26 PM
 */
if ( ! defined( 'ABSPATH' ) ) {
	die( '!' );
}
?>
<div class="panel panel-default">
    <div class="panel-heading"><?php _e('Cron Handler URL', 'download-manager'); ?></div>
    <div class="panel-body">
        <div class="input-group">
            <input type="text" id="wpdmcrl" readonly value="<?php echo home_url('/?wpdm_cron=1'); ?>" class="form-control" />
            <div class="input-group-btn"><button onclick="WPDM.copy('wpdmcrl')" type="button" class="btn btn-secondary ttip" title="Copy"><i class="fa fa-copy"></i></button></div>
        </div>
        <br/>
        <em class="note">
			<?php _e('Configure the cron url from your server control panel at 15 mins interval', 'download-manager'); ?>.
            <a target="_blank" href="https://www.wpdownloadmanager.com/mastering-cron-jobs-a-comprehensive-guide-to-creating-cron-jobs-with-cpanel-and-plesk-parallel/">
				<?php _e('How to configure cron job', 'download-manager'); ?>.
            </a>
        </em>
    </div>
</div>
<div class="panel panel-default">
    <div class="panel-heading"><?php echo __( "Cron Jobs", "download-manager" ); ?></div>
    <table class="table table-striped">
        <thead>
        <tr>
            <th><?php _e( 'ID', 'download-manager' ); ?></th>
            <th><?php _e( 'Task Type', 'download-manager' ); ?></th>
            <th><?php _e( 'Payload', 'download-manager' ); ?></th>
            <th><?php _e( 'Execute At', 'download-manager' ); ?></th>
            <th><?php _e( 'Repeat', 'download-manager' ); ?></th>
            <th><?php _e( 'Ex.Count', 'download-manager' ); ?></th>
            <th><?php _e( 'Interval', 'download-manager' ); ?></th>
            <th><?php _e( 'Created At', 'download-manager' ); ?></th>
            <th><?php _e( 'Action', 'download-manager' ); ?></th>
        </tr>
        </thead>

		<?php
		$cronjobs = \WPDM\__\CronJob::getAll();
		if ( count( $cronjobs ) > 0 ) {
			foreach ( $cronjobs as $cronjob ) {
				?>
                <tr id="cr<?php echo $cronjob->ID ?>">
                    <td><?php echo $cronjob->ID ?></td>
                    <td><?php echo $cronjob->type::getTitle() ?></td>
                    <td>
                        <button onclick="WPDM.bootAlert('Payload', jQuery('#payload<?php echo $cronjob->ID ?>').html(), 500)"
                                type="button"
                                class="btn btn-info btn-xs"><?php _e( 'View', 'download-manager' ); ?></button>
                        <template id="payload<?php echo $cronjob->ID ?>">
                            <div style="margin: 20px"><?php print_r( json_decode( $cronjob->data ) ); ?></div>
                        </template>
                    </td>
                    <td><?php echo date( get_option( 'date_format' ), $cronjob->execute_at ) ?></td>
                    <td><?php echo $cronjob->repeat_execution ?></td>
                    <td><?php echo $cronjob->execution_count ?></td>
                    <td><?php echo $cronjob->interval ?></td>
                    <td><?php echo date( get_option( 'date_format' ), $cronjob->created_at ) ?></td>
                    <td>
                        <button onclick="deleteCron(<?php echo $cronjob->ID ?>)" type="button"
                                class="btn btn-danger btn-xs"><?php _e( 'Delete', 'download-manager' ); ?></button>
                    </td>
                </tr>
				<?php
			}
		} else {
            ?>
            <tr>
                <td colspan="9" style="text-align: center;padding: 40px">
                    <img src="<?php echo WPDM_ASSET_URL; ?>/images/no-job.png" style="width: 128px;padding: 20px;" /><br/>
                    <p class="lead"><?php _e('No job in the queue!', 'download-manager'); ?></p>
                </td>
            </tr>
            <?php
		}
		?>
    </table>
</div>
<script>
    function deleteCron(id) {
        if (confirm('<?php _e( 'Are you sure?', 'download-manager' ) ?>')) {
            jQuery.post(ajaxurl, {
                action: 'wpdm_delete_cron',
                cronid: id,
                wpdmdcx: '<?php echo wp_create_nonce( WPDM_PRI_NONCE ) ?>'
            }, function (res) {
                if (res.success)
                    jQuery('#cr' + id).remove();
            });
        }
    }
</script>
