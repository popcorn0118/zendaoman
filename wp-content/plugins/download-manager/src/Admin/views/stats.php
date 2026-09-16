<?php
$type = wpdm_query_var('type', array('validate' => 'alpha', 'default' => 'history'));
$base_page_uri = "edit.php?post_type=wpdmpro&page=wpdm-stats";
?>
<link rel="stylesheet" href="<?php echo WPDM_BASE_URL; ?>assets/css/wpdm-elegant-history.css?v=<?php echo WPDM_VERSION; ?>">
<div class="wrap w3eden">

    <?php

    $actions = [
            ['link' => "#", "class" => "success disabled", "name" => '<i class="sinc far fa-arrow-alt-circle-down"></i> ' . __("Export Full History", "download-manager")]
    ];

    $menus = [
            ['link' => "edit.php?post_type=wpdmpro&page=wpdm-stats&type=overview", "name" => __("Overview", "download-manager"), "active" => ($type === 'overview')],
            ['link' => "edit.php?post_type=wpdmpro&page=wpdm-stats", "name" => __("Download History", "download-manager"), "active" => ($type === 'history')],
            ['link' => "edit.php?post_type=wpdmpro&page=wpdm-stats&type=insight", "name" => __("Insights", "download-manager"), "active" => ($type === 'insight')],
    ];

    WPDM()->admin->pageHeader(esc_attr__( 'History and Stats', WPDM_TEXT_DOMAIN ), 'chart-pie color-purple', $menus, $actions);

    ?>



        <!--<div class="panel-heading">
            <a class="btn btn-primary btn-sm pull-right" href="<?/*= $base_page_uri; */?>&task=export&__xnonce=<?/*=wp_create_nonce(NONCE_KEY); */?>" style="font-weight: 400">
                <i class="sinc far fa-arrow-alt-circle-down"></i> <?php /*_e("Export History", 'download-manager'); */?>
            </a>
            <b><i class="fas fa-chart-line color-purple"></i> &nbsp; <?php /*echo __("Download Statistics", "download-manager"); */?></b>

        </div>-->


        <div class="wpdm-admin-page-content">
            <?php
            if(file_exists(wpdm_admin_tpl_path("stats/{$type}.php"))) include wpdm_admin_tpl_path("stats/{$type}.php");
            else if (isset($stat_types[$type])) call_user_func($stat_types[$type]['callback']);
            else {
                ?>
                <div style="display: flex;width: 100%;">
                    <div style="display: inline-block; margin: 20px auto">
                        <div class="panel panel-default">
                            <div class="panel-body">
                                <strong><?php echo __( 'Extended Download Stats', 'download-manager' ) ?></strong><br/>
                                <?php echo __( 'Available with the pro version only!', 'download-manager' ) ?>
                            </div>
                            <div class="panel-footer"><a href="https://try.wpdownloadmanager.com/wp-admin/edit.php?post_type=wpdmpro&page=wpdm-stats&y=2025&m=9" target="_blank" class="btn btn-primary btn-sm">Preview</a></div>
                        </div>
                    </div>
                </div>
                <?php
            }
            ?>
        </div>


    <style>
        .notice{ display: none; }
    </style>
