<?php
if (!defined('ABSPATH')) die();
/**
 * User: shahnuralam
 * Date: 1/16/18
 * Time: 12:33 AM
 */

error_reporting(0);
//global $post;
$ID = wpdm_query_var('__wpdmlo');

$post_type = get_post_type($ID);
$post_status = get_post_status($ID);
if($post_type !== 'wpdmpro' && $post_status !== 'publish'){
    \WPDM\__\Messages::fullPage("Error: Invalid request", \WPDM\__\UI::card("Error: Invalid request", ["Your request could not be processed."]));
    die();
}

//$post = get_post(wpdm_query_var('__wpdmlo'));
//setup_postdata($post);
//$pack = new \WPDM\Package();
//$pack->Prepare(get_the_ID());
$form_lock = (int)get_post_meta($ID, '__wpdm_form_lock', true);
$terms_lock = (int)get_post_meta($ID, '__wpdm_terms_lock', true);
$base_price = (float)get_post_meta($ID, '__wpdm_base_price', true);
$color_scheme = get_option('__wpdm_color_scheme', 'light');

?><!DOCTYPE html>
<html style="background: transparent">
<head>
    <title>Download <?php get_the_title($ID); ?></title>


    <?php if($form_lock === 1  || $base_price > 0) wp_head(); else { ?>
        <script type="text/javascript">
            const wpdm_url = <?php echo json_encode(WPDM()->wpdm_urls);?>;
        </script>
        <link rel="stylesheet" href="<?php echo WPDM_ASSET_URL; ?>css/front.min.css" />
        <?php if ($color_scheme !== 'light') { ?>
        <link rel="stylesheet" href="<?php echo WPDM_ASSET_URL; ?>css/front-dark.min.css" />
        <?php } ?>
        <link rel="stylesheet" href="<?= WPDM_FONTAWESOME_URL ?>" />
        <script src="<?php echo includes_url(); ?>/js/jquery/jquery.js"></script>
        <script src="<?php echo includes_url(); ?>/js/jquery/jquery.form.min.js"></script>
        <script src="<?php echo WPDM_ASSET_URL; ?>js/wpdm.js"></script>
        <script src="<?php echo WPDM_ASSET_URL; ?>js/front.min.js"></script>
        <?php
        $_font = get_option('__wpdm_google_font', 'Sen');
        $font = explode(":", $_font);
        $font = $font[0];
        $font = $font ? $font . ',' : '';
        if($_font) {
        ?>
        <link href="https://fonts.googleapis.com/css2?family=<?php echo str_replace("regular", 400, $_font); ?>" rel="stylesheet">
        <style>
            .w3eden .fetfont,
            .w3eden .btn,
            .w3eden .btn.wpdm-front h3.title,
            .w3eden .wpdm-social-lock-box .IN-widget a span:last-child,
            .w3eden .card-header,
            .w3eden .card-footer,
            .w3eden .badge,
            .w3eden .label,
            .w3eden .table,
            .w3eden .card-body,
            .w3eden .wpdm-frontend-tabs a,
            .w3eden .alert:before,
            .w3eden .discount-msg,
            .w3eden .panel.dashboard-panel h3,
            .w3eden #wdmds .list-group-item,
            .w3eden #package-description .wp-switch-editor,
            .w3eden .w3eden.author-dashbboard .nav.nav-tabs li a,
            .w3eden .wpdm_cart thead th,
            .w3eden #csp .list-group-item,
            .w3eden .modal-title {
                font-family: <?php echo $font; ?> -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol";
            }
            .w3eden .btn
            {
                font-weight: 800 !important;
            }
            .w3eden .btn {
                letter-spacing: 1px;
                text-transform: uppercase;
            }
            .w3eden #csp .list-group-item {
                text-transform: unset;
            }


        </style>
    <?php
    }
    $wpdmss = maybe_unserialize(get_option('__wpdm_disable_scripts', array()));
    $uicolors = maybe_unserialize(get_option('__wpdm_ui_colors', array()));
    $primary = isset($uicolors['primary']) ? $uicolors['primary'] : '#4a8eff';
    $secondary = isset($uicolors['secondary']) ? $uicolors['secondary'] : '#4a8eff';
    $success = isset($uicolors['success']) ? $uicolors['success'] : '#18ce0f';
    $info = isset($uicolors['info']) ? $uicolors['info'] : '#2CA8FF';
    $warning = isset($uicolors['warning']) ? $uicolors['warning'] : '#f29e0f';
    $danger = isset($uicolors['danger']) ? $uicolors['danger'] : '#ff5062';
    $font = get_option('__wpdm_google_font', 'Sen');
    $font = explode(":", $font);
    $font = $font[0];
    $font = $font ? "\"{$font}\"," : '';
    if (is_singular('wpdmpro'))
	    $ui_button = get_option('__wpdm_ui_download_button');
    else
	    $ui_button = get_option('__wpdm_ui_download_button_sc');
    $class = ".btn." . (isset($ui_button['color']) ? $ui_button['color'] : 'btn-primary') . (isset($ui_button['size']) && $ui_button['size'] != '' ? "." . $ui_button['size'] : '');

    ?>
        <style>

            :root {
                --color-primary: <?php echo $primary; ?>;
                --color-primary-rgb: <?php echo wpdm_hex2rgb($primary); ?>;
                --color-primary-hover: <?php echo isset($uicolors['primary'])?$uicolors['primary_hover']:'#4a8eff'; ?>;
                --color-primary-active: <?php echo isset($uicolors['primary'])?$uicolors['primary_active']:'#4a8eff'; ?>;
                --clr-sec: <?php echo $secondary; ?>;
                --clr-sec-rgb: <?php echo wpdm_hex2rgb($secondary); ?>;
                --clr-sec-hover: <?php echo isset($uicolors['secondary'])?$uicolors['secondary_hover']:'#4a8eff'; ?>;
                --clr-sec-active: <?php echo isset($uicolors['secondary'])?$uicolors['secondary_active']:'#4a8eff'; ?>;
                --color-success: <?php echo $success; ?>;
                --color-success-rgb: <?php echo wpdm_hex2rgb($success); ?>;
                --color-success-hover: <?php echo isset($uicolors['success_hover'])?$uicolors['success_hover']:'#4a8eff'; ?>;
                --color-success-active: <?php echo isset($uicolors['success_active'])?$uicolors['success_active']:'#4a8eff'; ?>;
                --color-info: <?php echo $info; ?>;
                --color-info-rgb: <?php echo wpdm_hex2rgb($info); ?>;
                --color-info-hover: <?php echo isset($uicolors['info_hover'])?$uicolors['info_hover']:'#2CA8FF'; ?>;
                --color-info-active: <?php echo isset($uicolors['info_active'])?$uicolors['info_active']:'#2CA8FF'; ?>;
                --color-warning: <?php echo $warning; ?>;
                --color-warning-rgb: <?php echo wpdm_hex2rgb($warning); ?>;
                --color-warning-hover: <?php echo isset($uicolors['warning_hover'])?$uicolors['warning_hover']:'orange'; ?>;
                --color-warning-active: <?php echo isset($uicolors['warning_active'])?$uicolors['warning_active']:'orange'; ?>;
                --color-danger: <?php echo $danger; ?>;
                --color-danger-rgb: <?php echo wpdm_hex2rgb($danger); ?>;
                --color-danger-hover: <?php echo isset($uicolors['danger_hover'])?$uicolors['danger_hover']:'#ff5062'; ?>;
                --color-danger-active: <?php echo isset($uicolors['danger_active'])?$uicolors['danger_active']:'#ff5062'; ?>;
                --color-green: <?php echo isset($uicolors['green'])?$uicolors['green']:'#30b570'; ?>;
                --color-blue: <?php echo isset($uicolors['blue'])?$uicolors['blue']:'#0073ff'; ?>;
                --color-purple: <?php echo isset($uicolors['purple'])?$uicolors['purple']:'#8557D3'; ?>;
                --color-red: <?php echo isset($uicolors['red'])?$uicolors['red']:'#ff5062'; ?>;
                --color-muted: rgba(69, 89, 122, 0.6);
                --wpdm-font: <?php echo $font; ?> -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol";
            }

            .wpdm-download-link<?php echo $class; ?> {
                border-radius: <?php echo (isset($ui_button['borderradius'])?$ui_button['borderradius']:4); ?>px;
            }


        </style>
	    <?php
    }
    ?>
    <style>
        /* ============================================
           Lock Panel - Premium Modal UI
           ============================================ */

        :root {
            --lp-bg: #ffffff;
            --lp-shadow:
                0 0 0 1px rgba(0, 0, 0, 0.04),
                0 4px 8px rgba(0, 0, 0, 0.04),
                0 12px 24px rgba(0, 0, 0, 0.06),
                0 24px 48px rgba(0, 0, 0, 0.08),
                0 48px 96px rgba(0, 0, 0, 0.12);
            --lp-radius: 24px;
            --lp-icon-size: 88px;
            --lp-spring: cubic-bezier(0.34, 1.56, 0.64, 1);
            --lp-smooth: cubic-bezier(0.4, 0, 0.2, 1);
            --lp-exit: cubic-bezier(0.4, 0, 1, 1);
            --lp-accent: var(--color-primary, #6366f1);
            --lp-accent-rgb: var(--color-primary-rgb, 99, 102, 241);
            --lp-accent2: var(--color-purple, #8557D3);
        }

        html, body {
            overflow: visible;
            height: 100%;
            width: 100%;
            padding: 0;
            margin: 0;
            font-weight: 400;
            font-size: 10pt;
            font-family: var(--wpdm-font);
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        /* Lock Panel Root */
        .wpdm-lp {
            position: fixed;
            inset: 0;
            z-index: 99999;
            display: flex;
            align-items: center;
            justify-content: center;
            pointer-events: none;
            visibility: hidden;
        }

        .wpdm-lp--visible {
            pointer-events: auto;
            visibility: visible;
        }

        /* Backdrop - deeper, richer */
        .wpdm-lp__backdrop {
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(15, 23, 42, 0.35) 0%, rgba(30, 20, 60, 0.4) 100%);
            backdrop-filter: blur(6px) saturate(1.2);
            -webkit-backdrop-filter: blur(6px) saturate(1.2);
            opacity: 0;
            transition: opacity 0.5s var(--lp-smooth);
        }

        .wpdm-lp--visible .wpdm-lp__backdrop {
            opacity: 1;
        }

        /* Dialog Container */
        .wpdm-lp__dialog {
            position: relative;
            z-index: 1;
            transform: scale(0.92) translateY(40px);
            opacity: 0;
            transition:
                transform 0.6s var(--lp-spring),
                opacity 0.4s var(--lp-smooth);
        }

        .wpdm-lp--visible .wpdm-lp__dialog {
            transform: scale(1) translateY(0);
            opacity: 1;
        }

        /* Exit animation */
        .wpdm-lp--closing .wpdm-lp__backdrop {
            opacity: 0;
            transition: opacity 0.3s var(--lp-exit);
        }

        .wpdm-lp--closing .wpdm-lp__dialog {
            transform: scale(0.95) translateY(20px);
            opacity: 0;
            transition:
                transform 0.25s var(--lp-exit),
                opacity 0.2s var(--lp-exit);
        }

        /* Panel */
        .wpdm-lp__panel {
            background: var(--lp-bg);
            border: 1px solid rgba(0, 0, 0, 0.06);
            border-radius: var(--lp-radius);
            box-shadow: var(--lp-shadow);
            max-width: 100%;
            padding-top: calc(var(--lp-icon-size) / 2 + 12px);
            overflow: visible;
            position: relative;
        }

        /* Accent gradient wash across top */
        .wpdm-lp__panel::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 140px;
            background: linear-gradient(175deg,
                rgba(var(--lp-accent-rgb), 0.06) 0%,
                rgba(var(--lp-accent-rgb), 0.02) 40%,
                transparent 100%);
            pointer-events: none;
            border-radius: var(--lp-radius) var(--lp-radius) 0 0;
        }

        /* Top accent stripe */
        .wpdm-lp__panel::after {
            content: '';
            position: absolute;
            top: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 60px;
            height: 3px;
            background: linear-gradient(90deg, var(--lp-accent), var(--lp-accent2));
            border-radius: 0 0 3px 3px;
            opacity: 0;
            animation: lpStripeFadeIn 0.4s var(--lp-smooth) 0.5s forwards;
        }

        @keyframes lpStripeFadeIn {
            to { opacity: 0.7; }
        }

        /* Header */
        .wpdm-lp__header {
            padding: 0 24px;
            position: relative;
        }

        .wpdm-lp__header h4 {
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: #1e293b;
            font-size: 11px;
            display: inline-block;
            font-family: var(--wpdm-font);
            margin: 0 0 10px;
            padding: 5px 14px;
            background: linear-gradient(135deg, rgba(var(--lp-accent-rgb), 0.08), rgba(var(--lp-accent-rgb), 0.04));
            border-radius: 20px;
            border: 1px solid rgba(var(--lp-accent-rgb), 0.1);
            opacity: 0;
            transform: translateY(10px);
            animation: lpContentFadeIn 0.5s var(--lp-spring) 0.2s forwards;
        }

        /* Package title */
        .wpdm-lp__header .wpdm-lp__title {
            display: block;
            font-size: 13pt;
            font-weight: 600;
            color: #334155;
            letter-spacing: 0.2px;
            line-height: 1.4;
            margin-bottom: 0;
            opacity: 0;
            transform: translateY(10px);
            animation: lpContentFadeIn 0.5s var(--lp-spring) 0.3s forwards;
        }

        /* Separator line */
        .wpdm-lp__sep {
            display: block;
            width: 40px;
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(var(--lp-accent-rgb), 0.3), transparent);
            margin: 16px auto;
            opacity: 0;
            animation: lpContentFadeIn 0.5s var(--lp-smooth) 0.35s forwards;
        }

        @keyframes lpContentFadeIn {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Avatar - Floating with animated ring */
        .wpdm-lp__avatar {
            width: var(--lp-icon-size);
            height: var(--lp-icon-size);
            padding: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            top: 0;
            border-radius: 50%;
            margin-top: calc(var(--lp-icon-size) / -2);
            left: calc(50% - var(--lp-icon-size) / 2);
            position: absolute;
            z-index: 999999;
            background: linear-gradient(145deg, #ffffff 0%, #f1f5f9 100%);
            box-shadow:
                0 4px 12px rgba(var(--lp-accent-rgb), 0.15),
                0 8px 32px rgba(0, 0, 0, 0.12),
                inset 0 1px 0 rgba(255, 255, 255, 0.9);
            opacity: 0;
            transform: scale(0.5) translateY(20px);
            animation: lpIconPop 0.6s var(--lp-spring) 0.1s forwards;
        }

        @keyframes lpIconPop {
            to {
                opacity: 1;
                transform: scale(1) translateY(0);
            }
        }

        /* Animated gradient ring */
        .wpdm-lp__avatar::before {
            content: '';
            position: absolute;
            inset: -4px;
            border-radius: 50%;
            background: conic-gradient(
                from 0deg,
                var(--lp-accent),
                var(--lp-accent2),
                var(--color-info, #2CA8FF),
                var(--lp-accent)
            );
            opacity: 0.2;
            z-index: -1;
            animation: lpRingSpin 8s linear infinite;
        }

        @keyframes lpRingSpin {
            to { transform: rotate(360deg); }
        }

        /* Outer glow pulse */
        .wpdm-lp__avatar::after {
            content: '';
            position: absolute;
            inset: -8px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(var(--lp-accent-rgb), 0.12) 0%, transparent 70%);
            z-index: -2;
            animation: lpGlowPulse 3s ease-in-out infinite;
        }

        @keyframes lpGlowPulse {
            0%, 100% { opacity: 0.5; transform: scale(1); }
            50% { opacity: 1; transform: scale(1.08); }
        }

        .wpdm-lp__avatar img,
        .wpdm-lp__avatar .wp-post-image {
            border-radius: 50%;
            width: 100% !important;
            height: 100% !important;
            object-fit: cover;
        }

        /* Close Button */
        .wpdm-lp__close {
            position: absolute;
            z-index: 999999;
            top: 14px;
            right: 14px;
            width: 32px;
            height: 32px;
            padding: 0;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(0, 0, 0, 0.04);
            border: 1px solid rgba(0, 0, 0, 0.06);
            border-radius: 10px;
            opacity: 1;
            cursor: pointer;
            transition: all 0.2s var(--lp-smooth);
        }

        .wpdm-lp__close svg {
            width: 16px;
            height: 16px;
            color: #94a3b8;
            transition: color 0.2s var(--lp-smooth);
        }

        .wpdm-lp__close:hover {
            background: #fef2f2;
            border-color: #fecaca;
        }

        .wpdm-lp__close:hover svg {
            color: #ef4444;
        }

        .wpdm-lp__close:active {
            transform: scale(0.92);
        }

        /* Panel Body */
        .wpdm-lp__body {
            max-height: calc(100vh - 260px);
            overflow-y: auto;
            padding: 0 24px 24px;
            opacity: 0;
            transform: translateY(15px);
            animation: lpBodyFadeIn 0.5s var(--lp-spring) 0.4s forwards;
        }

        @keyframes lpBodyFadeIn {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Custom Scrollbar */
        .wpdm-lp__body::-webkit-scrollbar {
            width: 5px;
        }

        .wpdm-lp__body::-webkit-scrollbar-track {
            background: transparent;
        }

        .wpdm-lp__body::-webkit-scrollbar-thumb {
            background: rgba(var(--lp-accent-rgb), 0.15);
            border-radius: 10px;
        }

        .wpdm-lp__body::-webkit-scrollbar-thumb:hover {
            background: rgba(var(--lp-accent-rgb), 0.3);
        }

        /* ---- Inner Content Styling ---- */

        /* Cards */
        .w3eden .card {
            margin-bottom: 0;
            border-radius: 14px;
            border: 1px solid rgba(0, 0, 0, 0.06);
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
            transition: all 0.3s var(--lp-smooth);
            overflow: hidden;
        }

        .w3eden .card:hover {
            box-shadow:
                0 4px 12px rgba(0, 0, 0, 0.06),
                0 1px 3px rgba(0, 0, 0, 0.04);
            border-color: rgba(var(--lp-accent-rgb), 0.12);
        }

        .w3eden .card:last-child {
            margin-bottom: 10px !important;
        }

        .w3eden .card-default {
            margin-top: 10px !important;
        }

        .w3eden .card-header {
            background: linear-gradient(135deg, rgba(var(--lp-accent-rgb), 0.04) 0%, rgba(var(--lp-accent-rgb), 0.01) 100%);
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            font-weight: 600;
        }

        .card-body {
            line-height: 1.7;
            letter-spacing: 0.2px;
            font-size: 10.5pt;
            color: #334155;
        }

         

        .btn-viewcart,
        #cart_submit {
            line-height: 30px !important;
            width: 100%;
        }

        /* Social Lock Buttons */
        .wpdm-social-lock.btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            width: 100%;
            border-radius: 12px !important;
            padding: 13px 16px;
            margin-bottom: 8px;
            font-weight: 600 !important;
            letter-spacing: 0.5px;
            border: none !important;
            position: relative;
            overflow: hidden;
        }

        .wpdm-social-lock.btn::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(180deg, rgba(255,255,255,0.1) 0%, transparent 50%);
            pointer-events: none;
        }

        .wpdm-social-lock.btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.2) !important;
        }

        /* Price Display */
        .w3eden h3.wpdmpp-product-price {
            text-align: center;
            margin-bottom: 24px !important;
            font-size: 28pt;
            font-weight: 800;
            color: #1e293b;
            letter-spacing: -0.5px;
        }

        /* Spin Animation */
        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        .spin {
            animation: spin 1.5s linear infinite;
            display: inline-block;
        }

        /* Images */
        img {
            max-width: 100%;
        }

        .wp-post-image {
            width: 100%;
            height: auto;
            border-radius: 50%;
        }

        form * {
            max-width: 100% !important;
        }

        /* Terms checkbox area */
        .w3eden .wpdm-checkbox,
        .w3eden .custom-control {
            padding: 12px 14px;
            background: #f8fafc;
            border: 1px solid rgba(0, 0, 0, 0.06);
            border-radius: 12px;
            margin-bottom: 12px;
        }

        /* Reduced Motion Support */
        @media (prefers-reduced-motion: reduce) {
            .wpdm-lp__dialog,
            .wpdm-lp__backdrop,
            .wpdm-lp__avatar,
            .wpdm-lp__avatar::before,
            .wpdm-lp__avatar::after,
            .wpdm-lp__panel::after,
            .wpdm-lp__header h4,
            .wpdm-lp__header .wpdm-lp__title,
            .wpdm-lp__sep,
            .wpdm-lp__body {
                animation: none !important;
                transition-duration: 0.01ms !important;
                opacity: 1 !important;
                transform: none !important;
            }
        }

        /* ---- Dark Mode ---- */

        /* Manual dark mode */
        .w3eden.dark-mode .wpdm-lp__panel {
            background: var(--dm-bg-secondary, #1e293b);
            border-color: rgba(255, 255, 255, 0.06);
        }
        .w3eden.dark-mode .wpdm-lp__panel::before {
            background: linear-gradient(175deg, rgba(var(--lp-accent-rgb), 0.1) 0%, transparent 100%);
        }
        .w3eden.dark-mode .wpdm-lp__panel::after {
            opacity: 0.5;
        }
        .w3eden.dark-mode .wpdm-lp__header h4 {
            color: var(--dm-text, #f1f5f9);
            background: rgba(var(--lp-accent-rgb), 0.15);
            border-color: rgba(var(--lp-accent-rgb), 0.2);
        }
        .w3eden.dark-mode .wpdm-lp__header .wpdm-lp__title {
            color: var(--dm-text-secondary, #cbd5e1);
        }
        .w3eden.dark-mode .wpdm-lp__sep {
            background: linear-gradient(90deg, transparent, rgba(var(--lp-accent-rgb), 0.4), transparent);
        }
        .w3eden.dark-mode .wpdm-lp__avatar {
            background: linear-gradient(145deg, var(--dm-bg-tertiary, #334155) 0%, var(--dm-bg-secondary, #1e293b) 100%);
            box-shadow:
                0 4px 12px rgba(var(--lp-accent-rgb), 0.2),
                0 8px 32px rgba(0, 0, 0, 0.4),
                inset 0 1px 0 rgba(255, 255, 255, 0.06);
        }
        .w3eden.dark-mode .wpdm-lp__close {
            background: rgba(255, 255, 255, 0.06);
            border-color: rgba(255, 255, 255, 0.08);
        }
        .w3eden.dark-mode .wpdm-lp__close svg {
            color: var(--dm-text-muted, #94a3b8);
        }
        .w3eden.dark-mode .wpdm-lp__close:hover {
            background: rgba(239, 68, 68, 0.15);
            border-color: rgba(239, 68, 68, 0.2);
        }
        .w3eden.dark-mode .wpdm-lp__close:hover svg {
            color: #f87171;
        }
        .w3eden.dark-mode .form-control {
            background: var(--dm-bg-tertiary, #334155);
            border-color: rgba(255, 255, 255, 0.08);
            color: var(--dm-text, #f1f5f9);
        }
        .w3eden.dark-mode .form-control:focus {
            background: rgba(var(--lp-accent-rgb), 0.08);
            border-color: var(--lp-accent);
        }
        .w3eden.dark-mode .card {
            border-color: rgba(255, 255, 255, 0.06);
            background: rgba(255, 255, 255, 0.03);
        }
        .w3eden.dark-mode .card:hover {
            border-color: rgba(var(--lp-accent-rgb), 0.15);
        }
        .w3eden.dark-mode .card-header {
            background: rgba(var(--lp-accent-rgb), 0.08);
            border-bottom-color: rgba(255, 255, 255, 0.05);
        }
        .w3eden.dark-mode h3.wpdmpp-product-price {
            color: var(--dm-text, #f1f5f9);
        }
        .w3eden.dark-mode .wpdm-checkbox,
        .w3eden.dark-mode .custom-control {
            background: var(--dm-bg-tertiary, #334155);
            border-color: rgba(255, 255, 255, 0.06);
        }

        /* System preference dark mode */
        @media (prefers-color-scheme: dark) {
            .w3eden:not(.light-mode) .wpdm-lp__panel {
                background: var(--dm-bg-secondary, #1e293b);
                border-color: rgba(255, 255, 255, 0.06);
            }
            .w3eden:not(.light-mode) .wpdm-lp__panel::before {
                background: linear-gradient(175deg, rgba(var(--lp-accent-rgb), 0.1) 0%, transparent 100%);
            }
            .w3eden:not(.light-mode) .wpdm-lp__panel::after {
                opacity: 0.5;
            }
            .w3eden:not(.light-mode) .wpdm-lp__header h4 {
                color: var(--dm-text, #f1f5f9);
                background: rgba(var(--lp-accent-rgb), 0.15);
                border-color: rgba(var(--lp-accent-rgb), 0.2);
            }
            .w3eden:not(.light-mode) .wpdm-lp__header .wpdm-lp__title {
                color: var(--dm-text-secondary, #cbd5e1);
            }
            .w3eden:not(.light-mode) .wpdm-lp__sep {
                background: linear-gradient(90deg, transparent, rgba(var(--lp-accent-rgb), 0.4), transparent);
            }
            .w3eden:not(.light-mode) .wpdm-lp__avatar {
                background: linear-gradient(145deg, var(--dm-bg-tertiary, #334155) 0%, var(--dm-bg-secondary, #1e293b) 100%);
                box-shadow:
                    0 4px 12px rgba(var(--lp-accent-rgb), 0.2),
                    0 8px 32px rgba(0, 0, 0, 0.4),
                    inset 0 1px 0 rgba(255, 255, 255, 0.06);
            }
            .w3eden:not(.light-mode) .wpdm-lp__close {
                background: rgba(255, 255, 255, 0.06);
                border-color: rgba(255, 255, 255, 0.08);
            }
            .w3eden:not(.light-mode) .wpdm-lp__close svg {
                color: var(--dm-text-muted, #94a3b8);
            }
            .w3eden:not(.light-mode) .wpdm-lp__close:hover {
                background: rgba(239, 68, 68, 0.15);
                border-color: rgba(239, 68, 68, 0.2);
            }
            .w3eden:not(.light-mode) .wpdm-lp__close:hover svg {
                color: #f87171;
            }
            .w3eden:not(.light-mode) .form-control {
                background: var(--dm-bg-tertiary, #334155);
                border-color: rgba(255, 255, 255, 0.08);
                color: var(--dm-text, #f1f5f9);
            }
            .w3eden:not(.light-mode) .form-control:focus {
                background: rgba(var(--lp-accent-rgb), 0.08);
                border-color: var(--lp-accent);
            }
            .w3eden:not(.light-mode) .card {
                border-color: rgba(255, 255, 255, 0.06);
                background: rgba(255, 255, 255, 0.03);
            }
            .w3eden:not(.light-mode) .card:hover {
                border-color: rgba(var(--lp-accent-rgb), 0.15);
            }
            .w3eden:not(.light-mode) .card-header {
                background: rgba(var(--lp-accent-rgb), 0.08);
                border-bottom-color: rgba(255, 255, 255, 0.05);
            }
            .w3eden:not(.light-mode) h3.wpdmpp-product-price {
                color: var(--dm-text, #f1f5f9);
            }
            .w3eden:not(.light-mode) .wpdm-checkbox,
            .w3eden:not(.light-mode) .custom-control {
                background: var(--dm-bg-tertiary, #334155);
                border-color: rgba(255, 255, 255, 0.06);
            }
        }

    </style>




    <?php do_action("wpdm_modal_iframe_head"); ?>
</head>
<?php
// Build body classes based on color scheme setting
$body_classes = 'w3eden';
if ($color_scheme === 'light') {
    $body_classes .= ' light-mode';
} elseif ($color_scheme === 'dark') {
    $body_classes .= ' dark-mode';
}
// 'system' = no extra class, follows OS preference via @media (prefers-color-scheme: dark)
?>
<body class="<?php echo esc_attr($body_classes); ?>" style="background: transparent">

<div class="wpdm-lp" id="wpdm-locks">
    <div class="wpdm-lp__backdrop"></div>
    <div class="wpdm-lp__dialog" style="width: <?php echo $terms_lock === 1?395:365; ?>px;max-width: calc(100% - 20px);">
        <div class="wpdm-lp__panel">
            <div class="wpdm-lp__avatar">
                <?php if(has_post_thumbnail($ID)) echo get_the_post_thumbnail($ID, 'thumbnail'); else echo WPDM()->package::icon($ID, true, 'p-2'); ?>
            </div>
            <button type="button" class="wpdm-lp__close" aria-label="Close">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" xmlns="http://www.w3.org/2000/svg"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
            <div class="wpdm-lp__header text-center" style="margin-top: 16px">
                <h4><?php echo ($base_price > 0)? __('Buy','download-manager'): __('Download','download-manager'); ?></h4>
                <span class="wpdm-lp__title"><?php echo get_the_title($ID); ?></span>
                <span class="wpdm-lp__sep"></span>
            </div>
            <div class="wpdm-lp__body" id="wpdm-lock-options">
                <?php
                $extras = isset($_REQUEST['__wpdmfl']) ? ['ind' => wpdm_query_var('__wpdmfl', 'txt')] : [];
                echo WPDM()->package->downloadLink(wpdm_query_var('__wpdmlo', 'int'), 1, $extras);
                ?>
            </div>
        </div>
    </div>
</div>

<script>
    jQuery(function ($) {

        $('a').each(function () {
            if($(this).attr('href') !== '#')
                $(this).attr('target', '_blank');
        });

        var $locks = $('#wpdm-locks');

        function closeLockPanel() {
            $locks.addClass('wpdm-lp--closing');
            $locks.removeClass('wpdm-lp--visible');
            setTimeout(function () {
                var parentWindow = document.createElement("a");
                parentWindow.href = document.referrer.toString();
                if(parentWindow.hostname === window.location.hostname)
                    window.parent.hideLockFrame();
                else
                    window.parent.postMessage({'task': 'hideiframe'}, "*");
            }, 300);
        }

        $locks.on('click', '.wpdm-lp__close', closeLockPanel);
        $locks.on('click', '.wpdm-lp__backdrop', closeLockPanel);

        showModal();
    });

    function showModal() {
        jQuery('#wpdm-locks').addClass('wpdm-lp--visible');
    }

</script>
<div style="display: none">
    <?php  if($form_lock === 1 || $base_price > 0) wp_footer(); ?>
    <?php do_action("wpdm_modal_iframe_footer"); ?>
</div>
</body>
</html>
