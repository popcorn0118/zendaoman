<?php
/**
 * Base: wpdmpro
 * Developer: shahjada
 * Team: W3 Eden
 * Date: 22/5/20 08:20
 */
if(!defined("ABSPATH")) die();
?>
<div class="panel panel-default">
    <div class="panel-heading">
        <a href="#" class="btn btn-success pull-right btn-xs add-new-tag"><i class="fa fa-plus-circle"></i> <?php _e( "Add New Tag", "download-manager" ); ?></a>
        <?php echo  esc_attr__( 'Custom Template Tags', "download-manager" ); ?>
    </div>
    <table class="table table-striped" id="tagstable">
        <thead>
        <tr>
            <th><?php _e( "Tag", "download-manager" ) ?></th>
            <th><?php _e( "Value", "download-manager" ) ?></th>
            <th><?php _e( "Action", "download-manager" ) ?></th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td colspan="3">
                <?php wpdmpro_required(); ?>
            </td>
        </tr>
        </tbody>
    </table>
</div>

<script type="text/template" id="newtagmodal-tpl">
    <form method="post" id="newtagform">
        <input type="hidden" name="action" value="wpdm_save_custom_tag">
        <input type="hidden" name="__ctxnonce" value="<?php echo wp_create_nonce(NONCE_KEY); ?>">
        <div class="form-group">
            <input type="text" id="tag_name" name="ctag[name]" class="form-control input-lg" placeholder="<?php echo esc_attr__( "Tag Name", "download-manager" ) ?>" />
        </div>
        <div class="form-group">
            <textarea id="tag_value" placeholder="<?php echo esc_attr__( "Tag Value", "download-manager" ) ?>" class="form-control" style="height: 100px" name="ctag[value]"></textarea>
            <em class="note"><?php echo __( "No php code, only text, html, css and js", "download-manager" ); ?></em>
        </div>
        <div class="text-right" style="margin-top: 10px">
            <button disabled="disabled" type="submit" id="newtagformsubmit" style="width: 180px" class="btn btn-success btn-lg"><?php echo __( "Save Tag", "download-manager" ) ?></button>
        </div>
    </form>
</script>
