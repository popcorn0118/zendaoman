<?php
/**
 * Base: wpdmpro
 * Developer: shahjada
 * Team: W3 Eden
 * Date: 22/5/20 08:21
 */


if(!defined("ABSPATH")) die();

if(!isset($_GET['_type']) || $_GET['_type'] !== 'email'){ ?>

        <blockquote  class="alert alert-info" style="margin-bottom: 10px">
            <?php echo __( "Pre-designed templates can't be deleted or edited from this section. But you can clone any of them and edit as your own. If you seriously want to edit any pre-designed template you have to edit those directly edting php files at /download-manager/templates/ dir" , "download-manager" ); ?>
        </blockquote>

    <?php } ?>
<div class="panel panel-default">
<table cellspacing="0" class="table table-hover">
    <thead>
    <tr>
        <th style="min-width: 400px"><?php echo __( "Template Name" , "download-manager" ); ?></th>
	    <?php if(wpdm_query_var('_type') === 'email'){ ?>
            <th style="width: 150px"><?php echo __( "Mail Receiver" , "download-manager" ); ?></th>
	    <?php } ?>
        <th style="width: 250px;"><?php echo __( "Template ID" , "download-manager" ); ?></th>
	    <?php if(!isset($_GET['_type']) || $_GET['_type'] != 'email'){ ?>
            <th style="width: 150px"><?php echo __( "Status" , "download-manager" ); ?></th>
	    <?php } else { ?>
            <th style="width: 150px"><?php echo __( "Email Status" , "download-manager" ); ?></th>
	    <?php } ?>
        <th style="width: 160px;text-align: right"><?php echo __( "Actions" , "download-manager" ); ?></th>
    </tr>
    </thead>


    <tbody>

    <?php
    $ttype = isset($_GET['_type'])?wpdm_query_var('_type'):'link';
    if($ttype != 'email'){
        //$ctpls = WPDM\Admin\Menu\Templates::dropdown(array('data_type' => 'ARRAY', 'type' => $ttype));
        $ctpls = WPDM()->packageTemplate->getTemplates($ttype);

        $tplstatus = maybe_unserialize(get_option("_fm_{$ttype}_template_status"));
        $ctemplates = [];
        foreach($ctpls as $ctpl => $template){
            $name = "";
	        $path = "";

            if(!is_array($template)){
                $tmpdata = file_get_contents($template);
                $path = $template;
                $regx = "/WPDM.*Template[\s]*:([^\-\->]+)/";
                if (preg_match($regx, $tmpdata, $matches)) {
                    $name = $matches[1];
                } else continue;
            } else {
                $name = $template['name'];
                $ctemplates[] = $ctpl;
            }

            $tplid = str_replace(".php","",$ctpl);
            $status = isset($tplstatus[$tplid])?(int)$tplstatus[$tplid]:1;
            ?>

            <tr valign="top" class="author-self status-inherit" id="template-<?php echo $ttype; ?>-<?php echo $ctpl; ?>">
                <td class="column-icon media-icon" style="text-align: left;">
                    <a href="#" class="pull-right ttip" title="<?= __('Show file path', WPDM_TEXT_DOMAIN) ?>" onclick="jQuery('#lnk-<?= $tplid ?>').slideToggle();return false;"><i class="fa fa-globe"></i></a>
                    <nobr><?php echo $name; ?></nobr><br/>
                    <code style="display: none;" id="lnk-<?= $tplid ?>"><?php echo str_replace(ABSPATH, '', $path); ?></code>
                </td>
                <td>
                    <input class="form-control input-sm input-tplid" type="text" readonly="readonly" onclick="this.select()" value="<?php echo $tplid; ?>" />
                </td>
                <td>
                    <div class="btn-group" data-toggle="buttons">
                        <label class="btn btn-<?php echo $status === 1?'success active':'secondary'; ?> btn-sm btn-status <?php echo str_replace(".php","",$ctpl); ?>" data-value="1" data-id="<?php echo str_replace(".php","",$ctpl); ?>"><input type="radio" <?php checked($status,1); ?> name="<?php echo $ctpl; ?>-status" value="1"/><i class="fa fa-check"></i></label>
                        <label class="btn btn-<?php echo $status === 0?'danger active':'secondary'; ?> btn-sm btn-status <?php echo str_replace(".php","",$ctpl); ?>" data-value="0" data-id="<?php echo str_replace(".php","",$ctpl); ?>"><input type="radio" name="<?php echo $ctpl; ?>-status" <?php checked($status,0); ?> value="0"/><i class="fa fa-times"></i></label>
                    </div>
                </td>
                <td style="text-align: right">
                    <a href="#" data-href="admin-ajax.php?action=template_preview&_type=<?php echo $ttype; ?>&template=<?php echo $ctpl; ?>&_tplnonce=<?php echo  wp_create_nonce(WPDM_PUB_NONCE) ?>" rel="<?php echo $ctpl; ?>" class="template_preview btn btn-sm btn-success"><i class="fa fa-desktop"></i> Preview</a>

                </td>


            </tr>
            <?php
        }} else {
	    $status_all = WPDM()->email->getStatus();
	    $templates = \WPDM\__\Email::templates();
        foreach($templates as $ctpl => $template){
	        $status = isset($status_all[$ctpl]) ? (int) $status_all[$ctpl] : 1;
            ?>
            <tr valign="top" class="author-self status-inherit" id="post-8">
                <td class="column-icon media-icon" style="text-align: left;">
                    <?php echo esc_attr($template['label']); ?>

                </td>

                    <td style="width: 150px"><?php echo ucfirst($template['for']); ?></td>

                <td>
                    <?php echo $ctpl; ?>
                </td>
                <td>
                    <div class="btn-group" data-toggle="buttons">
                        <label class="btn btn-<?php echo $status === 1?'success active':'light'; ?> btn-sm btn-status <?php echo $ctpl; ?>" data-value="1" data-id="<?php echo $ctpl; ?>"><input type="radio" <?php checked($status,1); ?> name="<?php echo $ctpl; ?>-status" value="1"/><i class="fa fa-check"></i></label>
                        <label class="btn btn-<?php echo $status === 0?'danger active':'light'; ?> btn-sm btn-status <?php echo $ctpl; ?>" data-value="0" data-id="<?php echo $ctpl; ?>"><input type="radio" name="<?php echo $ctpl; ?>-status" <?php checked($status,0); ?> value="0"/><i class="fa fa-times"></i></label>
                    </div>
                </td>
                <td style="text-align: right">

                    <a href="edit.php?post_type=wpdmpro&page=templates&_type=email&task=EditEmailTemplate&id=<?php echo $ctpl; ?>" class="btn btn-sm btn-primary"><i class="fas fa-pencil-alt"></i> <?php echo __( "Edit" , "download-manager" ); ?></a>

                </td>


            </tr>
            <?php
        }}
    ?>
    </tbody>
</table>
</div>
