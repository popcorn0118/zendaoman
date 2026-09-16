<?php
/**
 * Author: shahnuralam
 * Date: 2019-01-01
 * Time: 14:39
 */
if (!defined('ABSPATH')) die();

$ui_button = get_option('__wpdm_ui_download_button');
$ui_button_sc = get_option('__wpdm_ui_download_button_sc');
$gappsk = get_option('_wpdm_google_app_secret', 'AIzaSyCgvNB-55xoUiz1zKIJgFPQbqyn4lCCB_E');
$gappsk = $gappsk ?: 'AIzaSyCgvNB-55xoUiz1zKIJgFPQbqyn4lCCB_E';
?>


<div class="panel panel-default wpdm-font-panel">
    <div class="panel-heading">
        <div class="wpdm-font-header">
            <div class="wpdm-font-header-left">
                <?= __('Google Fonts', WPDM_TEXT_DOMAIN); ?>
            </div>
            <label class="wpdm-font-toggle">
                <input type="hidden" name="__wpdm_enable_gf" value="0">
                <input type="checkbox" id="__wpdm_enable_gf_checkbox" <?php checked(1, get_option('__wpdm_enable_gf', 0)) ?> name="__wpdm_enable_gf" value="1">
                <span class="wpdm-font-toggle-slider"></span>
            </label>
        </div>
    </div>
    <div class="panel-body">
        <div class="wpdm-font-selector">
            <div class="wpdm-font-selector-row">
                <div class="wpdm-font-field">
                    <label class="wpdm-font-label"><?= __('Font Family', WPDM_TEXT_DOMAIN); ?></label>
                    <div class="wpdm-font-select-wrapper">
                        <select name="__wpdm_google_font" id="__wpdm_google_font" data-selected="<?php echo esc_attr(get_option('__wpdm_google_font', 'Sen')); ?>"></select>
                        <div class="wpdm-font-loading" id="wpdm-font-loading">
                            <div class="wpdm-font-loading-spinner"></div>
                            <span><?= __('Loading fonts...', WPDM_TEXT_DOMAIN); ?></span>
                        </div>
                    </div>
                </div>
                <div class="wpdm-font-actions">
                    <button type="button" id="pig" class="wpdm-font-btn wpdm-font-btn--secondary" title="<?= esc_attr__('View on Google Fonts', WPDM_TEXT_DOMAIN); ?>">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg>
                        <?= __('Google Fonts', WPDM_TEXT_DOMAIN); ?>
                    </button>
                </div>
            </div>
        </div>

        <div class="wpdm-font-preview-container">
            <div class="wpdm-font-preview-header">
                <span class="wpdm-font-preview-label"><?= __('Preview', WPDM_TEXT_DOMAIN); ?></span>
                <div class="wpdm-font-preview-controls">
                    <button type="button" class="wpdm-font-size-btn active" data-size="16">Aa</button>
                    <button type="button" class="wpdm-font-size-btn" data-size="24">Aa</button>
                    <button type="button" class="wpdm-font-size-btn" data-size="36">Aa</button>
                </div>
            </div>
            <?php
$saved_font = get_option('__wpdm_google_font', 'Sen');
$font_family = preg_replace('/:.*$/', '', $saved_font); // Remove :wght@... part
$font_family = str_replace('+', ' ', $font_family); // Replace + with space
?>
            <div class="wpdm-font-preview" id="fontpreview" style="font-family: '<?php echo esc_attr($font_family); ?>'">
                <div class="wpdm-font-preview-main" contenteditable="true" data-placeholder="<?= esc_attr__('Type to preview...', WPDM_TEXT_DOMAIN); ?>">The quick brown fox jumps over the lazy dog</div>
                <div class="wpdm-font-preview-samples">
                    <div class="wpdm-font-sample wpdm-font-sample--heading">Heading Text Sample</div>
                    <div class="wpdm-font-sample wpdm-font-sample--body">Body text looks like this. Perfect for paragraphs and content.</div>
                    <div class="wpdm-font-sample wpdm-font-sample--small">Small caption or footnote text</div>
                </div>
                <div class="wpdm-font-preview-chars">
                    <span>ABC</span><span>abc</span><span>123</span><span>!@#</span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="panel panel-default">
    <div class="panel-heading"><?php _e("Colors",'download-manager'); ?></div>


        <?php $uicolors = maybe_unserialize(get_option('__wpdm_ui_colors', array())); ?>

        <table class="table">
            <thead>
            <tr>
                <th>Color Name</th>
                <th width="50px">Default</th>
                <th width="50px">Hover</th>
                <th width="50px">Active</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td><?php echo __("Primary:", "download-manager") ?></td>
                <td><input type="text" data-css-var="--color-primary" class="color-control" name="__wpdm_ui_colors[primary]" value="<?php echo isset($uicolors['primary'])?$uicolors['primary']:'#4a8eff'; ?>" /></td>
                <td><input type="text" data-css-var="--color-primary-hover" class="color-control" name="__wpdm_ui_colors[primary_hover]" value="<?php echo isset($uicolors['primary_hover'])?$uicolors['primary_hover']:'#4a8eff'; ?>" /></td>
                <td><input type="text" data-css-var="--color-primary-active" class="color-control" name="__wpdm_ui_colors[primary_active]" value="<?php echo isset($uicolors['primary_active'])?$uicolors['primary_active']:'#4a8eff'; ?>" /></td>
            </tr>
            <tr>
                <td><?php echo __("Secondary:", "download-manager") ?></td>
                <td><input type="text" data-css-var="--clr-sec" class="color-control" name="__wpdm_ui_colors[secondary]" value="<?php echo isset($uicolors['secondary'])?$uicolors['secondary']:'#6c757d'; ?>" /></td>
                <td><input type="text" data-css-var="--clr-sec-hover" class="color-control" name="__wpdm_ui_colors[secondary_hover]" value="<?php echo isset($uicolors['secondary_hover'])?$uicolors['secondary_hover']:'#6c757d'; ?>" /></td>
                <td><input type="text" data-css-var="--clr-sec-active" class="color-control" name="__wpdm_ui_colors[secondary_active]" value="<?php echo isset($uicolors['secondary_active'])?$uicolors['secondary_active']:'#6c757d'; ?>" /></td>
            </tr>
            <tr>
                <td><?php echo __("Info:", "download-manager") ?></td>
                <td><input type="text" data-css-var="--color-info" class="color-control" name="__wpdm_ui_colors[info]" value="<?php echo isset($uicolors['info'])?$uicolors['info']:'#2CA8FF'; ?>" /></td>
                <td><input type="text" data-css-var="--color-info-hover" class="color-control" name="__wpdm_ui_colors[info_hover]" value="<?php echo isset($uicolors['info_hover'])?$uicolors['info_hover']:'#2CA8FF'; ?>" /></td>
                <td><input type="text" data-css-var="--color-info-active" class="color-control" name="__wpdm_ui_colors[info_active]" value="<?php echo isset($uicolors['info_active'])?$uicolors['info_active']:'#2CA8FF'; ?>" /></td>
            </tr>
            <tr>
                <td><?php echo __("Success:", "download-manager") ?></td>
                <td><input type="text" data-css-var="--color-success" class="color-control" name="__wpdm_ui_colors[success]" value="<?php echo isset($uicolors['success'])?$uicolors['success']:'#18ce0f'; ?>" /></td>
                <td><input type="text" data-css-var="--color-success-hover" class="color-control" name="__wpdm_ui_colors[success_hover]" value="<?php echo isset($uicolors['success_hover'])?$uicolors['success_hover']:'#18ce0f'; ?>" /></td>
                <td><input type="text" data-css-var="--color-success-active" class="color-control" name="__wpdm_ui_colors[success_active]" value="<?php echo isset($uicolors['success_active'])?$uicolors['success_active']:'#18ce0f'; ?>" /></td>
            </tr>
            <tr>
                <td><?php echo __("Warning:", "download-manager") ?></td>
                <td><input type="text" data-css-var="--color-warning" class="color-control" name="__wpdm_ui_colors[warning]" value="<?php echo isset($uicolors['warning'])?$uicolors['warning']:'#FFB236'; ?>" /></td>
                <td><input type="text" data-css-var="--color-warning-hover" class="color-control" name="__wpdm_ui_colors[warning_hover]" value="<?php echo isset($uicolors['warning_hover'])?$uicolors['warning_hover']:'#FFB236'; ?>" /></td>
                <td><input type="text" data-css-var="--color-warning-active" class="color-control" name="__wpdm_ui_colors[warning_active]" value="<?php echo isset($uicolors['warning_active'])?$uicolors['warning_active']:'#FFB236'; ?>" /></td>
            </tr>
            <tr>
                <td><?php echo __("Danger:", "download-manager") ?></td>
                <td><input type="text" data-css-var="--color-danger" class="color-control" name="__wpdm_ui_colors[danger]" value="<?php echo isset($uicolors['danger'])?$uicolors['danger']:'#ff5062'; ?>" /></td>
                <td><input type="text" data-css-var="--color-danger-hover" class="color-control" name="__wpdm_ui_colors[danger_hover]" value="<?php echo isset($uicolors['danger_hover'])?$uicolors['danger_hover']:'#ff5062'; ?>" /></td>
                <td><input type="text" data-css-var="--color-danger-active" class="color-control" name="__wpdm_ui_colors[danger_active]" value="<?php echo isset($uicolors['danger_active'])?$uicolors['danger_active']:'#ff5062'; ?>" /></td>
            </tr>
            </tbody>
        </table>


</div>

<div class="panel panel-default">
    <div class="panel-heading"><?php _e("Color Scheme",'download-manager'); ?></div>
    <div class="panel-body">
        <?php $color_scheme = get_option('__wpdm_color_scheme', 'system'); ?>
        <div class="wpdm-color-scheme-options">
            <label class="wpdm-scheme-option <?php echo $color_scheme === 'system' ? 'active' : ''; ?>">
                <input type="radio" name="__wpdm_color_scheme" value="system" <?php checked('system', $color_scheme); ?> />
                <span class="wpdm-scheme-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect><line x1="8" y1="21" x2="16" y2="21"></line><line x1="12" y1="17" x2="12" y2="21"></line></svg>
                </span>
                <span class="wpdm-scheme-text">
                    <span class="wpdm-scheme-label"><?php _e('System', 'download-manager'); ?></span>
                    <span class="wpdm-scheme-desc"><?php _e('Follow OS preference', 'download-manager'); ?></span>
                </span>
            </label>
            <label class="wpdm-scheme-option <?php echo $color_scheme === 'light' ? 'active' : ''; ?>">
                <input type="radio" name="__wpdm_color_scheme" value="light" <?php checked('light', $color_scheme); ?> />
                <span class="wpdm-scheme-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="5"></circle><line x1="12" y1="1" x2="12" y2="3"></line><line x1="12" y1="21" x2="12" y2="23"></line><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line><line x1="1" y1="12" x2="3" y2="12"></line><line x1="21" y1="12" x2="23" y2="12"></line><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line></svg>
                </span>
                <span class="wpdm-scheme-text">
                    <span class="wpdm-scheme-label"><?php _e('Light', 'download-manager'); ?></span>
                    <span class="wpdm-scheme-desc"><?php _e('Always use light mode', 'download-manager'); ?></span>
                </span>
            </label>
            <label class="wpdm-scheme-option <?php echo $color_scheme === 'dark' ? 'active' : ''; ?>">
                <input type="radio" name="__wpdm_color_scheme" value="dark" <?php checked('dark', $color_scheme); ?> />
                <span class="wpdm-scheme-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path></svg>
                </span>
                <span class="wpdm-scheme-text">
                    <span class="wpdm-scheme-label"><?php _e('Dark', 'download-manager'); ?></span>
                    <span class="wpdm-scheme-desc"><?php _e('Always use dark mode', 'download-manager'); ?></span>
                </span>
            </label>
        </div>
    </div>
</div>

<div class="panel panel-default">
    <div class="panel-heading"><?php _e("Download Button",'download-manager'); ?></div>

        <table class="table table-bordered">
            <tr>
                <th colspan="2"><?php _e("Details Page", "download-manager");  ?></th>
            </tr>
            <tr>
                <td valign="middle" title="Select Button Color">
                    <small>
                        <label><input class="__wpdm_ui_download_button_color" <?php checked('btn-link', (isset($ui_button['color'])?$ui_button['color']:'')); ?> type="radio" value="btn-link" name="__wpdm_ui_download_button[color]"> None</label>
                        <label><input class="__wpdm_ui_download_button_color" <?php checked('btn-primary', (isset($ui_button['color'])?$ui_button['color']:'')); ?> type="radio" value="btn-primary" name="__wpdm_ui_download_button[color]"> Primary</label>
                        <label><input class="__wpdm_ui_download_button_color" <?php checked('btn-secondary', (isset($ui_button['color'])?$ui_button['color']:'')); ?> type="radio" value="btn-secondary" name="__wpdm_ui_download_button[color]"> Secondary</label>
                        <label><input class="__wpdm_ui_download_button_color" <?php checked('btn-info', (isset($ui_button['color'])?$ui_button['color']:'')); ?> type="radio" value="btn-info" name="__wpdm_ui_download_button[color]"> Info</label>
                        <label><input class="__wpdm_ui_download_button_color" <?php checked('btn-success', (isset($ui_button['color'])?$ui_button['color']:'')); ?> type="radio" value="btn-success" name="__wpdm_ui_download_button[color]"> Success</label>
                        <label><input class="__wpdm_ui_download_button_color" <?php checked('btn-danger', (isset($ui_button['color'])?$ui_button['color']:'')); ?> type="radio" value="btn-danger" name="__wpdm_ui_download_button[color]"> Danger</label>
                    </small>
                </td>
                <td rowspan="2" valign="middle" align="center" style="width: 200px">
                    <button id="__wpdm_ui_download_button" type="button" style="border-radius: <?php echo (isset($ui_button['borderradius'])?$ui_button['borderradius']:4);?>px" class="<?php echo wpdm_download_button_style(true); ?>"><?php _e("Download", "download-manager");  ?></button>
                </td>
            </tr>
            <tr>
                <td valign="middle">
                    <small>
                        <label><input class="__wpdm_ui_download_button_size" <?php checked('btn-xs', (isset($ui_button['size'])?$ui_button['size']:'')); ?> type="radio" value="btn-xs" name="__wpdm_ui_download_button[size]"> Extra Small</label>
                        <label><input class="__wpdm_ui_download_button_size" <?php checked('btn-sm', (isset($ui_button['size'])?$ui_button['size']:'')); ?> type="radio" value="btn-sm" name="__wpdm_ui_download_button[size]"> Small</label>
                        <label><input class="__wpdm_ui_download_button_size" <?php checked('', (isset($ui_button['size'])?$ui_button['size']:'')); ?> type="radio" value="" name="__wpdm_ui_download_button[size]"> Regular</label>
                        <label><input class="__wpdm_ui_download_button_size" <?php checked('btn-lg', (isset($ui_button['size'])?$ui_button['size']:'')); ?> type="radio" value="btn-lg" name="__wpdm_ui_download_button[size]"> Large</label>
                        <div style="width: 160px;float: right;" class="input-group input-group-sm"><div class="input-group-addon"><?php echo __( "Border Radius", "download-manager" ) ?>:</div><input min="0" max="999" id="__wpdm_ui_download_button_br"  name="__wpdm_ui_download_button[borderradius]" data-target="#__wpdm_ui_download_button" class="form-control" type="number" value="<?php echo (isset($ui_button['borderradius'])?$ui_button['borderradius']:4);?>" /></div>
                    </small>
                </td>
            </tr>
            <tr>
                <th colspan="2"><?php _e("Shortcode Page", "download-manager");  ?></th>
            </tr>
            <tr>
                <td valign="middle" title="Select Button Color">
                    <small>
                        <label><input class="__wpdm_ui_download_button_sc_color" <?php checked('btn-link', (isset($ui_button_sc['color'])?$ui_button_sc['color']:'')); ?> type="radio" value="btn-link" name="__wpdm_ui_download_button_sc[color]"> None</label>
                        <label><input class="__wpdm_ui_download_button_sc_color" <?php checked('btn-primary', (isset($ui_button_sc['color'])?$ui_button_sc['color']:'')); ?> type="radio" value="btn-primary" name="__wpdm_ui_download_button_sc[color]"> Primary</label>
                        <label><input class="__wpdm_ui_download_button_sc_color" <?php checked('btn-secondary', (isset($ui_button_sc['color'])?$ui_button_sc['color']:'')); ?> type="radio" value="btn-secondary" name="__wpdm_ui_download_button_sc[color]"> Secondary</label>
                        <label><input class="__wpdm_ui_download_button_sc_color" <?php checked('btn-info', (isset($ui_button_sc['color'])?$ui_button_sc['color']:'')); ?> type="radio" value="btn-info" name="__wpdm_ui_download_button_sc[color]"> Info</label>
                        <label><input class="__wpdm_ui_download_button_sc_color" <?php checked('btn-success', (isset($ui_button_sc['color'])?$ui_button_sc['color']:'')); ?> type="radio" value="btn-success" name="__wpdm_ui_download_button_sc[color]"> Success</label>
                        <label><input class="__wpdm_ui_download_button_sc_color" <?php checked('btn-danger', (isset($ui_button_sc['color'])?$ui_button_sc['color']:'')); ?> type="radio" value="btn-danger" name="__wpdm_ui_download_button_sc[color]"> Danger</label>
                    </small>
                </td>
                <td rowspan="2" valign="middle" align="center" style="width: 200px">
                    <button id="__wpdm_ui_download_button_sc" type="button" class="<?php echo wpdm_download_button_style(); ?>"  style="border-radius: <?php echo (isset($ui_button_sc['borderradius'])?$ui_button_sc['borderradius']:4);?>px"><?php _e("Download", "download-manager");  ?></button>
                </td>
            </tr>
            <tr>
                <td valign="middle">
                    <small>
                        <label><input class="__wpdm_ui_download_button_sc_size" <?php checked('btn-xs', (isset($ui_button_sc['size'])?$ui_button_sc['size']:'')); ?> type="radio" value="btn-xs" name="__wpdm_ui_download_button_sc[size]"> Extra Small</label>
                        <label><input class="__wpdm_ui_download_button_sc_size" <?php checked('btn-sm', (isset($ui_button_sc['size'])?$ui_button_sc['size']:'')); ?> type="radio" value="btn-sm" name="__wpdm_ui_download_button_sc[size]"> Small</label>
                        <label><input class="__wpdm_ui_download_button_sc_size" <?php checked('', (isset($ui_button_sc['size'])?$ui_button_sc['size']:'')); ?> type="radio" value="" name="__wpdm_ui_download_button_sc[size]"> Regular</label>
                        <label><input class="__wpdm_ui_download_button_sc_size" <?php checked('btn-lg', (isset($ui_button_sc['size'])?$ui_button_sc['size']:'')); ?> type="radio" value="btn-lg" name="__wpdm_ui_download_button_sc[size]"> Large</label>
                        <div style="width: 160px;float: right;" class="input-group input-group-sm"><div class="input-group-addon"><?php echo __( "Border Radius", "download-manager" ) ?>:</div><input min="0" max="999" data-target="#__wpdm_ui_download_button_sc" id="__wpdm_ui_download_button_sc_br" class="form-control" type="number"  name="__wpdm_ui_download_button_sc[borderradius]" value="<?php echo (isset($ui_button_sc['borderradius'])?$ui_button_sc['borderradius']:4);?>" /></div>
                    </small>
                </td>
            </tr>

        </table>

</div>
<div class="panel panel-default">
    <div class="panel-heading"><?php _e("Front-end UI",'download-manager'); ?></div>
    <div class="panel-body">

        <input type="hidden" name="__wpdm_disable_frontend_css" value="0">
        <label><input type="checkbox" name="__wpdm_disable_frontend_css" value="1" <?php checked(1, get_option('__wpdm_disable_frontend_css', 0)); ?> /> <?php echo __( "Disable front-end stylesheet", "download-manager" ) ?></label>
        <p class="text-muted" style="margin: 5px 0 0 24px; font-size: 12px;"><?php _e("For advanced users who want to use custom CSS. This will disable all WPDM front-end styles.", "download-manager"); ?></p>
    </div>
</div>
<div class="panel panel-default">
    <div class="panel-heading"><?php _e("Image and Previews",'download-manager'); ?></div>
    <div class="panel-body"><input type="hidden" name="__wpdm_crop_thumbs" value="0">
      <label><input type="checkbox" name="__wpdm_crop_thumbs" value="1" <?php checked(1, get_option('__wpdm_crop_thumbs', 0)); ?> /> <?php echo __( "Crop thumbnails", "download-manager" ) ?></label>
    </div>
</div>

<div class="panel panel-default">
    <div class="panel-heading"><?php _e("Admin UI",'download-manager'); ?></div>
    <div class="panel-body"><input type="hidden" name="__wpdm_left_aligned" value="0">
        <label><input type="checkbox" id="lasp" name="__wpdm_left_aligned" value="1" <?php checked(1, get_option('__wpdm_left_aligned', 0)); ?> /> <?php echo __( "Left aligned settings page", "download-manager" ) ?></label>

    </div>
</div>

<style>
    /* Google Fonts Panel */
    .wpdm-font-panel {
        overflow: visible;
    }
    .wpdm-font-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .wpdm-font-header-left {
        display: flex;
        align-items: center;
        gap: 10px;
        font-weight: 600;
    }
    .wpdm-font-icon {
        color: var(--admin-color, #4a8eff);
    }

    /* Toggle Switch */
    .wpdm-font-toggle {
        position: relative;
        display: inline-block;
        width: 44px;
        height: 24px;
        margin: 0 !important;
    }
    .wpdm-font-toggle input {
        opacity: 0;
        width: 0;
        height: 0;
    }
    .wpdm-font-toggle-slider {
        position: absolute;
        cursor: pointer;
        inset: 0;
        background: #cbd5e1;
        border-radius: 24px;
        transition: all 200ms ease;
    }
    .wpdm-font-toggle-slider::before {
        content: '';
        position: absolute;
        height: 18px;
        width: 18px;
        left: 3px;
        bottom: 3px;
        background: #fff;
        border-radius: 50%;
        transition: all 200ms ease;
        box-shadow: 0 1px 3px rgba(0,0,0,0.2);
    }
    .wpdm-font-toggle input:checked + .wpdm-font-toggle-slider {
        background: var(--admin-color, #4a8eff);
    }
    .wpdm-font-toggle input:checked + .wpdm-font-toggle-slider::before {
        transform: translateX(20px);
    }

    /* Font Selector */
    .wpdm-font-selector {
        margin-bottom: 20px;
    }
    .wpdm-font-selector-row {
        display: flex;
        gap: 16px;
        align-items: flex-end;
    }
    .wpdm-font-field {
        flex: 1;
    }
    .wpdm-font-label {
        display: block;
        font-size: 12px;
        font-weight: 600;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 8px !important;
    }
    .wpdm-font-select-wrapper {
        position: relative;
    }
    #__wpdm_google_font {
        width: 100% !important;
        display: none;
    }
    .wpdm-font-loading.hidden + .select2-container,
    .wpdm-font-loading.hidden ~ #__wpdm_google_font {
        display: block;
    }
    .wpdm-font-select-wrapper .select2-container {
        width: 100% !important;
    }

    /* Loading State */
    .wpdm-font-loading {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 12px 16px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        color: #64748b;
        font-size: 13px;
    }
    .wpdm-font-loading-spinner {
        width: 16px;
        height: 16px;
        border: 2px solid #e2e8f0;
        border-top-color: var(--admin-color, #4a8eff);
        border-radius: 50%;
        animation: wpdm-font-spin 0.8s linear infinite;
    }
    @keyframes wpdm-font-spin {
        to { transform: rotate(360deg); }
    }
    .wpdm-font-loading.hidden {
        display: none;
    }

    /* Font Actions */
    .wpdm-font-actions {
        display: flex;
        gap: 8px;
    }
    .wpdm-font-btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 10px 16px;
        font-size: 13px;
        font-weight: 500;
        border-radius: 6px;
        border: 1px solid transparent;
        cursor: pointer;
        transition: all 150ms ease;
        white-space: nowrap;
    }
    .wpdm-font-btn--secondary {
        background: #f1f5f9;
        border-color: #e2e8f0;
        color: #475569;
    }
    .wpdm-font-btn--secondary:hover {
        background: #e2e8f0;
        border-color: #cbd5e1;
        color: #1e293b;
    }

    /* Preview Container */
    .wpdm-font-preview-container {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        overflow: hidden;
    }
    .wpdm-font-preview-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 12px 16px;
        background: #fff;
        border-bottom: 1px solid #e2e8f0;
    }
    .wpdm-font-preview-label {
        font-size: 12px;
        font-weight: 600;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .wpdm-font-preview-controls {
        display: flex;
        gap: 4px;
    }
    .wpdm-font-size-btn {
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #f1f5f9;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        color: #64748b;
        font-weight: 600;
        cursor: pointer;
        transition: all 150ms ease;
    }
    .wpdm-font-size-btn:nth-child(1) { font-size: 11px; }
    .wpdm-font-size-btn:nth-child(2) { font-size: 13px; }
    .wpdm-font-size-btn:nth-child(3) { font-size: 15px; }
    .wpdm-font-size-btn:hover {
        background: #e2e8f0;
        color: #1e293b;
    }
    .wpdm-font-size-btn.active {
        background: var(--admin-color, #4a8eff);
        border-color: var(--admin-color, #4a8eff);
        color: #fff;
    }

    /* Preview Area */
    .wpdm-font-preview {
        padding: 24px;
    }
    .wpdm-font-preview-main {
        font-size: 24px;
        line-height: 1.4;
        color: #1e293b;
        padding: 16px;
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        margin-bottom: 20px;
        min-height: 60px;
        outline: none;
        transition: border-color 150ms ease, box-shadow 150ms ease;
    }
    .wpdm-font-preview-main:focus {
        border-color: var(--admin-color, #4a8eff);
        box-shadow: 0 0 0 3px rgba(74, 142, 255, 0.1);
    }
    .wpdm-font-preview-main:empty::before {
        content: attr(data-placeholder);
        color: #94a3b8;
    }

    /* Font Samples */
    .wpdm-font-preview-samples {
        display: flex;
        flex-direction: column;
        gap: 12px;
        margin-bottom: 20px;
    }
    .wpdm-font-sample {
        color: #475569;
    }
    .wpdm-font-sample--heading {
        font-size: 20px;
        font-weight: 600;
        color: #1e293b;
    }
    .wpdm-font-sample--body {
        font-size: 15px;
        line-height: 1.6;
    }
    .wpdm-font-sample--small {
        font-size: 12px;
        color: #64748b;
    }

    /* Character Preview */
    .wpdm-font-preview-chars {
        display: flex;
        gap: 16px;
        padding: 16px;
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
    }
    .wpdm-font-preview-chars span {
        font-size: 18px;
        color: #64748b;
        padding: 8px 16px;
        background: #f8fafc;
        border-radius: 6px;
    }

    /* Color Scheme Options - Media Card Style */
    .wpdm-color-scheme-options {
        display: flex;
        gap: 12px;
    }
    .wpdm-scheme-option {
        display: flex !important;
        align-items: center;
        gap: 16px;
        padding: 16px 20px;
        background: #f8fafc;
        border: 2px solid #e2e8f0;
        border-radius: 10px;
        cursor: pointer;
        transition: all 150ms ease;
        width: 100%;
    }
    .wpdm-scheme-option:hover {
        border-color: #cbd5e1;
        background: #f1f5f9;
    }
    .wpdm-scheme-option.active {
        border-color: var(--admin-color, #4a8eff);
        background: rgba(74, 142, 255, 0.05);
    }
    .wpdm-scheme-option input[type="radio"] {
        display: none;
    }
    .wpdm-scheme-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 44px;
        height: 44px;
        background: #fff;
        border-radius: 10px;
        color: #64748b;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        transition: all 150ms ease;
        flex-shrink: 0;
    }
    .wpdm-scheme-option.active .wpdm-scheme-icon {
        background: var(--admin-color, #4a8eff);
        color: #fff;
        box-shadow: 0 4px 12px rgba(74, 142, 255, 0.3);
    }
    .wpdm-scheme-text {
        display: flex;
        flex-direction: column;
        gap: 2px;
    }
    .wpdm-scheme-label {
        font-weight: 600;
        font-size: 14px;
        color: #1e293b;
        line-height: 1.3;
    }
    .wpdm-scheme-desc {
        font-size: 12px;
        color: #64748b;
        font-weight: 400;
        line-height: 1.3;
    }
    .wpdm-scheme-option.active .wpdm-scheme-label {
        color: var(--admin-color, #4a8eff);
    }

    .color-control{
        padding: 0 !important;
        height: 24px;
        text-align: center;
    }
    .wp-picker-container .wp-color-result.button{
        width: 30px;
        float: right;
        padding: 2px !important;
        height: 30px;
        margin: 0 !important;
        border: 0 !important;
        border-radius: 500px;
    }
    .wp-picker-holder{
        position: absolute;
        z-index: 999999;
        margin-top: 26px;
    }
    .color-group{
        float: right;
        margin-top: -2px !important;
        margin-right: -12px !important;
    }
    .wp-picker-container{
        float: left;
    }
    .wp-color-result{
        border-bottom: 0 !important;
    }
    .wp-picker-input-wrap label{
        margin: 0 !important;
    }
    .wp-picker-container input[type="text"].wp-color-picker{
        margin: 0 !important;
        box-shadow: none;
        border-radius: 0;
        height: 25px;
    }
    .wp-picker-input-wrap .button{
        border-bottom: 0;
        border-radius: 0;
    }
    .wp-color-result-text{
        display: none;
    }
    .wp-picker-input-wrap{
        position: absolute;
        margin-left: 28px;
        width: 150px;
    }
    #__wpdm_google_font{
        width: 400px !important;
    }
    #__wpdm_ui_download_button_sc.btn-primary,
    #__wpdm_ui_download_button.btn-primary{
        background: var(--color-primary) !important;
    }
    #__wpdm_ui_download_button_sc.btn-link,
    #__wpdm_ui_download_button.btn-link {
        color: var(--color-primary) !important;
    }
</style>
<link rel="stylesheet" id="gfont" href="https://fonts.googleapis.com/css2?family=<?php echo get_option('__wpdm_google_font', 'Sen'); ?>" />

<script>
    jQuery(function($){

        // Font change handler
        $('#__wpdm_google_font').on('change', function () {
            let font = $(this).val();
            if (!font) {
                $('#fontpreview').css('font-family', 'inherit');
                return;
            }
            let _font = font.split(':')[0];
            let fontFamily = _font.replace(/\+/ig, ' ');
            let link = 'https://fonts.googleapis.com/css2?family=' + _font;
            $('#gfont').attr('href', link);
            $('#fontpreview').css('font-family', fontFamily);
        });

        // Font size buttons
        $('.wpdm-font-size-btn').on('click', function() {
            let size = $(this).data('size');
            $('.wpdm-font-size-btn').removeClass('active');
            $(this).addClass('active');
            $('.wpdm-font-preview-main').css('font-size', size + 'px');
        });

        // Color Scheme Selection
        $('.wpdm-scheme-option').on('click', function() {
            $('.wpdm-scheme-option').removeClass('active');
            $(this).addClass('active');
        });

        $('body').on('click', '#lasp', function () {
            if($(this).is(':checked'))
                $('.wrap.w3eden').removeClass('wpdms-body-centered');
            else
                $('.wrap.w3eden').addClass('wpdms-body-centered');

        });

        $('body').on('click', '#pig', function () {
            let font = $('#__wpdm_google_font').val();
            if(!font) {
                alert('<?= esc_attr__('No font has been selected'); ?>');
                return false;
            }
            let _font = font.split(':');
            _font = _font[0];
            WPDM.popupWindow('https://fonts.google.com/specimen/'+_font, "Font Preview", 1000, 800);
        });

        $('.color-control').wpColorPicker({
            change: function (event, ui) {
                let root = document.documentElement;
                root.style.setProperty($(this).data('css-var'), ui.color.toString());
            }
        });

        $('.__wpdm_ui_download_button_color, .__wpdm_ui_download_button_size').on('change', function () {
            $('#__wpdm_ui_download_button').attr('class', 'btn '+ $('.__wpdm_ui_download_button_color:checked').val() + ' ' + $('.__wpdm_ui_download_button_size:checked').val());
        });

        $('.__wpdm_ui_download_button_sc_color, .__wpdm_ui_download_button_sc_size').on('change', function () {
            $('#__wpdm_ui_download_button_sc').attr('class', 'btn '+ $('.__wpdm_ui_download_button_sc_color:checked').val() + ' ' + $('.__wpdm_ui_download_button_sc_size:checked').val());
        });

        $('#__wpdm_ui_download_button_br, #__wpdm_ui_download_button_sc_br').on('change', function () {
            $($(this).data('target')).css('border-radius', $(this).val()+'px');
        });

        function generateGoogleFontUrl(fontJson) {
            const family = fontJson.family.replace(/ /g, '+'); // Replace spaces with '+'
            let url = `${family}`;
            if(fontJson.axes) {
                const weightAxis = fontJson.axes.find(axis => axis.tag === 'wght');
                let wghtRange = '';
                if (weightAxis) {
                    wghtRange = `${weightAxis.start}..${weightAxis.end}`;
                    url += `:wght@${wghtRange}`;
                }
            }
            return url;
        }

        $.getJSON("https://www.googleapis.com/webfonts/v1/webfonts?capability=VF&key=<?php echo $gappsk; ?>", function(fonts){
            const $select = $('#__wpdm_google_font');
            const selectedValue = $select.data('selected');

            $select.append($("<option></option>").attr("value", '').text('<?= esc_attr__("-- Select a font --", WPDM_TEXT_DOMAIN); ?>'));

            for (let i = 0; i < fonts.items.length; i++) {
                let value = generateGoogleFontUrl(fonts.items[i]);
                let $option = $("<option></option>").attr("value", value).text(fonts.items[i].family);
                if (value === selectedValue) {
                    $option.attr("selected", "selected");
                }
                $select.append($option);
            }

            // Hide loading, show select
            $('#wpdm-font-loading').addClass('hidden');
            $select.select2({
                minimumResultsForSearch: 6,
                placeholder: '<?= esc_attr__("Search fonts...", WPDM_TEXT_DOMAIN); ?>'
            });
        }).fail(function() {
            // Handle error
            $('#wpdm-font-loading').html('<span style="color: #ef4444;"><?= esc_attr__("Failed to load fonts", WPDM_TEXT_DOMAIN); ?></span>');
        });

    });
</script>
