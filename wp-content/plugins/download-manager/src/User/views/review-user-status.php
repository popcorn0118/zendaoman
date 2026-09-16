<?php

use WPDM\__\__;

if(!defined('ABSPATH')) die('Dream more!');

$iid = uniqid();
?>
<style>
    .d-block{
        display: block;
    }
    .media .avatar{
        width: 148px;
        height: auto;
    }
</style>

<div id="rst-<?= $iid ?>">
    <div class="panel panel-default">
        <div class="panel-body">
            <div class="media">
                <div class="pull-left">
					<?= get_avatar($user->ID, 256); ?>
                </div>
                <div class="media-body" style="padding-left: 10px">

                    <div class="form-group">
                        <strong class="d-block"><?php _e('Name', WPDM_TEXT_DOMAIN); ?></strong>
						<?= $user->display_name; ?>
                    </div>
                    <div class="form-group">
                        <strong class="d-block"><?php _e('Email', WPDM_TEXT_DOMAIN); ?></strong>
						<?= $user->user_email; ?>
                    </div>
                    <div class="form-group">
                        <strong class="d-block"><?php _e('Signup Date', WPDM_TEXT_DOMAIN); ?></strong>
						<?= wp_date(get_option('date_format')." ".get_option('time_format'), strtotime($user->user_registered)); ?>
                    </div>

                </div>
            </div>
        </div>
    </div>
    <div class="form-group">
        <div class="panel panel-default">
            <div class="panel-body">
                <label><?php _e('If decline or suspend a user, you may add the reason:', WPDM_TEXT_DOMAIN); ?></label>
                <textarea id="reason" class="form-control" placeholder="<?= __('Reason to decline (optional)', WPDM_TEXT_DOMAIN) ?>"></textarea>
            </div>
        </div>
    </div>

    <div class="row">
        <?php if(in_array($status, ['', 'approved'])) { ?>
            <div class="col-md-12"><button type="button" class="btn btn-danger btn-block wpdmusersup-<?= $user->ID ?>" data-action="suspend"><i class="fa fa-ban"></i> <?php _e('Suspend', WPDM_TEXT_DOMAIN); ?></button></div>
        <?php } else { ?>
	        <?php if($status !== 'suspended') { ?>
            <div class="col-md-6"><button type="button" class="btn btn-danger btn-block wpdmusersup-<?= $user->ID ?>" data-action="decline"><i class="fa fa-times-circle"></i> <?php _e('Decline', WPDM_TEXT_DOMAIN); ?></button></div>
            <?php } ?>
            <div class="col-md-<?= $status === 'suspended' ? 12 : 6 ?>"><button type="button" class="btn btn-success btn-block wpdmusersup-<?= $user->ID ?>" data-action="approve"><i class="fa fa-check-double"></i> <?php _e('Approve', WPDM_TEXT_DOMAIN); ?></button></div>
        <?php } ?>
    </div>
</div>


<script>
    jQuery(function ($) {
       $('.wpdmusersup-<?= $user->ID ?>').on('click', function (e) {
           e.preventDefault();
           WPDM.blockUI('#rst-<?= $iid ?>');
           $.post(ajaxurl, {action: 'wpdmdz_update_user_status', user: <?= $user->ID ?>, do: $(this).data('action'), __uscnonce: '<?= wp_create_nonce(WPDM_PRI_NONCE); ?>', reason: $('#reason').val()}, function (res){
               var __mdid = '<?= esc_js(__::query_var('__mdid', 'txt')); ?>';
               var $dlg = $('#rst-<?= $iid ?>').closest('.wpdm-dialog-wrapper');
               if ($dlg.length) { $dlg.find('.wpdm-dialog__close').trigger('click'); }
               else if (__mdid) { $('#' + __mdid).modal('hide'); }
               WPDM.notify(res.msg, 'info', "top-center", 4000);
               if(res.status === 'approved')
                   $('#usts-<?= $user->ID ?>').html('<span class="text-success"><i class="fa-solid fa-check-double"></i> <?php _e('Approved', WPDM_TEXT_DOMAIN); ?></span>');
               else if(res.status === 'declined')
                   $('#usts-<?= $user->ID ?>').html('<span class="text-danger"><i class="fa-solid fa-times-circle"></i> <?php _e('Declined', WPDM_TEXT_DOMAIN); ?></span>');
               else if(res.status === 'suspended')
                   $('#usts-<?= $user->ID ?>').html('<span class="text-danger"><i class="fa-solid fa-ban"></i> <?php _e('Suspended', WPDM_TEXT_DOMAIN); ?></span>');
           });
       });
    });
</script>
