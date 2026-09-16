<?php
if (!defined('ABSPATH')) die();
/**
 * User: shahnuralam
 * Date: 1/26/18
 * Time: 12:33 AM
 */


?><!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>
<head>
    <meta http-equiv="Content-Type"
          content="<?php bloginfo( 'html_type' ); ?>; charset=<?php bloginfo( 'charset' ); ?>"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?php echo WPDM_ASSET_URL.'css/front.min.css'; ?>" />
    <?php $color_scheme = get_option('__wpdm_color_scheme', 'system'); if ($color_scheme !== 'light') { ?>
    <link rel="stylesheet" href="<?php echo WPDM_ASSET_URL.'css/front-dark.min.css'; ?>" />
    <?php } ?>
    <?php
    $_font = get_option('__wpdm_google_font', 'Sen');
    $font = explode(":", $_font);
    $font = $font[0];
    $font = $font ? $font . ',' : '';
    if($_font) {
    ?>
    <link href="https://fonts.googleapis.com/css2?family=<?php echo str_replace("regular", 400, $_font); ?>" rel="stylesheet">
    <?php } ?>
    <?php WPDM()->apply::uiColors(); ?>
    <style>
        body{
            font-family: var(--wpdm-font);
            background: #ffffff url("<?php echo get_the_post_thumbnail_url(); ?>") no-repeat;
            background-size: cover;
        }
        .outer {
            display: table;
            position: absolute;
            top: 0;
            left: 0;
            height: 100%;
            width: 100%;
        }

        .middle {
            display: table-cell;
            vertical-align: middle;
            text-align: center;
        }


        .w3eden .panel .panel-heading{
            font-size: 10px;
        }
        .w3eden p{
            margin: 15px 0 !important;
        }

        .w3eden .inner {
            letter-spacing: 0.3px;
        }
        .w3eden .inner .card-header strong,
        .w3eden .inner .card-header {
            font-weight: 600;
            font-size: 16px !important;
        }
        .w3eden .inner .card {
            width: 500px;
            margin: 0 auto;
        }

    </style>

</head>
<body class="w3eden">
<div class="outer">
    <div class="middle">
        <div class="inner">
	        <?php
	        echo do_shortcode($msg);
	        ?>
        </div>
    </div>
</div>

</body>


</html>
