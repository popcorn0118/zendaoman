<?php if(get_post_meta($post->ID,'__wpdm_legacy_id',true)){ ?>
<input type="hidden" name="file[legacy_id]" value="<?php echo get_post_meta($post->ID,'__wpdm_legacy_id',true); ?>" />
<?php } ?>
<div class="w3eden" id="all-package-settings">
<div class="wpdm-tabs">
    <div class="wpdm-tabs__nav">
        <button type="button" class="wpdm-tabs__btn active" data-tab="package-settings">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z"/><circle cx="12" cy="12" r="3"/></svg>
            <?php _e('Package Settings', 'download-manager'); ?>
        </button>
        <button type="button" class="wpdm-tabs__btn" data-tab="lock-options">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
            <?php _e('Lock Options', 'download-manager'); ?>
        </button>
        <button type="button" class="wpdm-tabs__btn" data-tab="package-icons">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
            <?php _e('Icons', 'download-manager'); ?>
        </button>
        <?php
        $etabs = apply_filters('wpdm_package_settings_tabs', array());
        foreach($etabs as $id => $tab){
            $icon = isset($tab['icon']) ? $tab['icon'] : '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4"/><path d="M12 8h.01"/></svg>';
            echo '<button type="button" class="wpdm-tabs__btn" data-tab="' . esc_attr($id) . '">' . $icon . esc_html($tab['name']) . '</button>';
        } ?>
    </div>
    <div class="wpdm-tabs__content">
        <div class="wpdm-tabs__panel active" data-panel="package-settings">
    <div class="wpdm-settings-panel">
        <!-- Basic Info Section -->
        <div class="panel panel-default">
            <div class="panel-heading">
                <svg class="wpdm-settings-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
                <span><?php _e('Basic Information', 'download-manager'); ?></span>
            </div>
            <div class="panel-body"><div class="row">
                <div class="col-md-3" id="version_row">
                    <label class="wpdm-settings-label"><?php _e('Version', 'download-manager'); ?></label>
                    <input type="text" class="form-control" value="<?php echo esc_attr(get_post_meta($post->ID,'__wpdm_version',true)); ?>" name="file[version]" placeholder="1.0.0" />
                </div>
                <div class="col-md-5" id="link_label_row">
                    <label class="wpdm-settings-label"><?php _e('Link Label', 'download-manager'); ?></label>
                    <input type="text" class="form-control" value="<?php echo esc_attr(get_post_meta($post->ID,'__wpdm_link_label',true)); ?>" name="file[link_label]" placeholder="Download Now" />
                </div>
                <div class="col-md-4" id="package_size_row">
                    <label class="wpdm-settings-label"><?php _e('Package Size', 'download-manager'); ?></label>
                    <input type="text" class="form-control" name="file[package_size]" value="<?php echo esc_attr(get_post_meta($post->ID,'__wpdm_package_size',true)); ?>" placeholder="10 MB" />
                    <span class="wpdm-settings-hint"><?php _e('Size of included file', 'download-manager'); ?></span>
                </div>
            </div></div>
        </div>

        <!-- Limits & Quotas Section -->
        <div class="panel panel-default">
            <div class="panel-heading">
                <svg class="wpdm-settings-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                <span><?php _e('Limits & Quotas', 'download-manager'); ?></span>
            </div>
            <div class="panel-body"><div class="row">
                <div class="col-md-6" id="stock_row">
                    <label class="wpdm-settings-label"><?php _e('Stock Limit', 'download-manager'); ?></label>
                    <input type="number" class="form-control" name="file[quota]" value="<?php echo esc_attr(get_post_meta($post->ID,'__wpdm_quota',true)); ?>" min="0" placeholder="∞" />
                    <span class="wpdm-settings-hint"><?php _e('Leave empty for unlimited', 'download-manager'); ?></span>
                </div>

                <div class="col-md-6" id="download_limit_row">

                    <div style="display: flex; padding: 10px; gap: 10px; background: linear-gradient(135deg, #fefefe 0%, #f7fafc 100%); border: 2px dashed #e2e8f0; border-radius: 8px; text-align: left;">
                        <div style="width: 48px; height: 48px; background: linear-gradient(135deg, #edf2f7 0%, #e2e8f0 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-cloud-download-alt" style="font-size: 20px; color: #a0aec0;"></i>
                        </div>
                        <div>
                            <div style="font-size: 14px; margin-top: 4px; color: #718096; font-weight: 500;">Per user download limit</div>
                            <span style="font-size: 11px; color: #a0aec0; margin-top: 4px;">Available with pro only</span>
                        </div>
                    </div>
                </div>
            </div></div>
        </div>

        <!-- Statistics Section -->
        <div class="panel panel-default">
            <div class="panel-heading">
                <svg class="wpdm-settings-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 20V10"/><path d="M12 20V4"/><path d="M6 20v-6"/></svg>
                <span><?php _e('Statistics', 'download-manager'); ?></span>
            </div>
            <div class="panel-body"><div class="row">
                <div class="col-md-6" id="view_count_row">
                    <label class="wpdm-settings-label"><?php _e('View Count', 'download-manager'); ?></label>
                    <input type="number" class="form-control" name="file[view_count]" value="<?php echo esc_attr(get_post_meta($post->ID,'__wpdm_view_count',true)); ?>" min="0" />
                </div>
                <div class="col-md-6" id="download_count_row">
                    <label class="wpdm-settings-label"><?php _e('Download Count', 'download-manager'); ?></label>
                    <input type="number" class="form-control" name="file[download_count]" value="<?php echo esc_attr(get_post_meta($post->ID,'__wpdm_download_count',true)); ?>" min="0" />
                </div>
            </div></div>
        </div>

        <!-- Access Control Section -->
        <div class="panel panel-default">
            <div class="panel-heading">
                <svg class="wpdm-settings-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                <span><?php _e('Access Control', 'download-manager'); ?></span>
            </div>
            <div class="panel-body">
                <div class="row">
                <div class="col-md-12" id="access_row">
                    <label class="wpdm-settings-label"><?php _e('Allow Access', 'download-manager'); ?></label>
                    <div>
                        <?php
                        global $wp_roles;
                        $roles = array_reverse($wp_roles->role_names);
                        $currentAccess = get_post_meta($post->ID, '__wpdm_access', true);
                        $currentAccess = maybe_unserialize($currentAccess);
                        ?>
                        <select name="file[access][]" data-placeholder="<?php _e('Select who can download...', 'download-manager'); ?>" class="chzn-select role wpdm-select" multiple="multiple" id="access">
                            <?php
                            $selz = '';
                            if(is_array($currentAccess)) $selz = (in_array('guest',$currentAccess))?'selected=selected':'';
                            if(!isset($_GET['post']) && !$currentAccess) $selz = 'selected=selected';
                            ?>
                            <option value="guest" <?php echo $selz ?>><?php _e('All Visitors', 'download-manager'); ?></option>
                            <?php foreach($roles as $role => $name): ?>
                                <?php $sel = (is_array($currentAccess) && in_array($role,$currentAccess)) ? 'selected=selected' : ''; ?>
                                <option value="<?php echo $role; ?>" <?php echo $sel ?>><?php echo $name; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>
                <?php do_action('wpdm_access_control_settings', $post->ID); ?>
            </div>
        </div>



        <!-- Templates Section -->
        <div class="panel panel-default">
            <div class="panel-heading">
                <svg class="wpdm-settings-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><line x1="3" y1="9" x2="21" y2="9"/><line x1="9" y1="21" x2="9" y2="9"/></svg>
                <span><?php _e('Templates', 'download-manager'); ?></span>
            </div>
            <div class="panel-body"><div class="row">
                <div class="col-md-6" id="page_template_row">
                    <label class="wpdm-settings-label"><?php _e('Page Template', 'download-manager'); ?></label>
                    <?php echo WPDM()->packageTemplate->dropdown(array('type'=>'page','name' => 'file[page_template]', 'id'=>'pge_tpl', 'selected' => get_post_meta($post->ID,'__wpdm_page_template',true), 'class' => 'form-control'), true); ?>
                </div>
            </div></div>
        </div>

        <?php if(isset($_GET['post']) && $_GET['post'] != ''): ?>
        <!-- Master Key Section -->
        <div class="panel panel-default">
            <div class="panel-heading">
                <svg class="wpdm-settings-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 2l-2 2m-7.61 7.61a5.5 5.5 0 1 1-7.778 7.778 5.5 5.5 0 0 1 7.777-7.777zm0 0L15.5 7.5m0 0l3 3L22 7l-3-3m-3.5 3.5L19 4"/></svg>
                <span><?php _e('Master Key', 'download-manager'); ?></span>
            </div>
            <div class="panel-body"><div class="row">
                <div class="col-md-12">
                    <label class="wpdm-settings-label"><?php _e('Direct Download Key', 'download-manager'); ?></label>
                    <div class="wpdm-master-key-field">
                        <input type="text" class="form-control" style="font-family: monospace; flex: 1;" readonly="readonly" value="<?php echo get_post_meta($post->ID, '__wpdm_masterkey', true); ?>" />
                        <button type="button" class="btn btn-light wpdm-btn-copy" onclick="navigator.clipboard.writeText(this.previousElementSibling.value)" title="<?php _e('Copy to clipboard', 'download-manager'); ?>">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
                        </button>
                    </div>
                    <label class="wpdm-checkbox-label">
                        <input type="checkbox" value="1" name="reset_key" />
                        <span><?php _e('Regenerate master key', 'download-manager'); ?></span>
                    </label>
                    <span class="wpdm-settings-hint"><?php _e('This key can be used for direct download without authentication', 'download-manager'); ?></span>
                </div>
            </div></div>
        </div>
        <?php endif; ?>

        <?php do_action("wpdm_package_settings_panel", $post->ID); ?>
    </div>
</div>

<div class="wpdm-tabs__panel" data-panel="lock-options">
    <?php include wpdm_admin_tpl_path("metaboxes/lock-options.php"); ?>
</div>

<div class="wpdm-tabs__panel" data-panel="package-icons">
    <?php include wpdm_admin_tpl_path("metaboxes/icons.php"); ?>
</div>

<?php foreach($etabs as $id => $tab){
    echo "<div class='wpdm-tabs__panel' data-panel='" . esc_attr($id) . "'>";
    call_user_func($tab['callback']);
    echo "</div>";
} ?>
    </div>
</div>
<?php if(!file_exists(dirname(WPDM_BASE_DIR).'/wpdm-premium-packages/wpdm-premium-packages.php')){  ?>
    <div class="w3eden" id="wpdm-activate-shop"><br/>
        <div  class="alert alert-warning" id="wpdm-activate-shop-info" style="background-image: none !important;border-radius:0 !important;margin:0;background:#d7b75d33;border:0;text-align:center;">

            Planning to sell your digital products? <a style="font-weight: 900" href="#" id="wpdm-activate-shop-link">Activate Digital Store Option</a>

        </div>

        <script>
            jQuery(function(){
                jQuery('#wpdm-activate-shop-link').on('click', function(){
                    jQuery(this).html('Activating...')
                    jQuery.post(ajaxurl,{action:'wpdm-activate-shop', wpdmappnonce: '<?php echo wp_create_nonce(WPDM_PRI_NONCE) ?>'}, function(res){
                        jQuery('#wpdm-activate-shop-info').html(res);
                    });
                    return false;
                });
            });
        </script>

    </div>
<?php } ?>

</div>








<!-- all js ------>

<script type="text/javascript">

    jQuery(function($) {

        // WPDM Tabs - Custom Tab System
        (function() {
            var $tabs = $('#all-package-settings .wpdm-tabs');
            if (!$tabs.length) return;

            var $buttons = $tabs.find('.wpdm-tabs__btn');
            var $panels = $tabs.find('.wpdm-tabs__panel');

            $buttons.on('click', function(e) {
                e.preventDefault();
                var $btn = $(this);
                var tabId = $btn.data('tab');

                // Update button states
                $buttons.removeClass('active');
                $btn.addClass('active');

                // Update panel states
                $panels.removeClass('active');
                $panels.filter('[data-panel="' + tabId + '"]').addClass('active');

                // Store in localStorage for persistence
                try {
                    localStorage.setItem('wpdm_active_tab', tabId);
                } catch(e) {}
            });

            // Restore last active tab
            try {
                var savedTab = localStorage.getItem('wpdm_active_tab');
                if (savedTab && $buttons.filter('[data-tab="' + savedTab + '"]').length) {
                    $buttons.filter('[data-tab="' + savedTab + '"]').trigger('click');
                }
            } catch(e) {}
        })();

        // Radio button toggle - fallback for browsers without :has() support
        function updateRadioStates() {
            $('.wpdm-radio-option').removeClass('active');
            $('.wpdm-radio-option input[type="radio"]:checked').closest('.wpdm-radio-option').addClass('active');
        }
        updateRadioStates();
        $(document).on('change', '.wpdm-radio-option input[type="radio"]', updateRadioStates);

        // Uploading files
        var file_frame;



        jQuery('body').on('click','#img', function( event ){

            event.preventDefault();

            // If the media frame already exists, reopen it.
            if ( file_frame ) {
                file_frame.open();
                return;
            }

            // Create the media frame.
            file_frame = wp.media.frames.file_frame = wp.media({
                title: jQuery( this ).data( 'uploader_title' ),
                button: {
                    text: jQuery( this ).data( 'uploader_button_text' ),
                },
                multiple: false  // Set to true to allow multiple files to be selected
            });

            // When an image is selected, run a callback.
            file_frame.on( 'select', function() {
                // We set multiple to false so only get one image from the uploader
                attachment = file_frame.state().get('selection').first().toJSON();
                jQuery('#fpvw').val(attachment.url);
                jQuery('#rmvp').remove();
                jQuery('#img').html("<img src='"+attachment.url+"' style='max-width:100%'/><input type='hidden' name='file[preview]' value='"+attachment.url+"' >");
                jQuery('#img').after('<a href="#"  id="rmvp"> <img style="width:16px;height:16px" align="left" src="<?php echo plugins_url('/download-manager/images/delete.svg'); ?>" /> Remove Preview Image</a>');
                file_frame.close();
                // Do something with attachment.id and/or attachment.url here
            });

            // Finally, open the modal
            file_frame.open();
        });


        /*jQuery('body').on('click', ".cb-enable",function(){
            var parent = jQuery(this).parents('.switch');
            jQuery('.cb-disable',parent).removeClass('selected');
            jQuery(this).addClass('selected');
            jQuery('.checkbox',parent).attr('checked', true);
        });
        jQuery('body').on('click', ".cb-disable",function(){
            var parent = jQuery(this).parents('.switch');
            jQuery('.cb-enable',parent).removeClass('selected');
            jQuery(this).addClass('selected');
            jQuery('.checkbox',parent).attr('checked', false);
        });*/

        var n = 0;

        /*jQuery('body').on('click', '.wpdm-label',function(){
            if(jQuery(this).hasClass('wpdm-checked')) jQuery(this).addClass('wpdm-unchecked').removeClass('wpdm-checked');
            else jQuery(this).addClass('wpdm-checked').removeClass('wpdm-unchecked');

        });*/



        jQuery("#wpdm-settings select").select2({no_results_text: "", width: "100%", minimumResultsForSearch: 6});

        jQuery('.handlediv').click(function(){
            jQuery(this).parent().find('.inside').slideToggle();
        });




        jQuery('.nopro').click(function(){
            if(this.checked) jQuery('.wpdmlock').removeAttr('checked');
        });

        jQuery('.wpdmlock').click(function(){
            if(this.checked) {
                jQuery('#'+jQuery(this).attr('rel')).slideDown();
                jQuery('.nopro').removeAttr('checked');
            } else {
                jQuery('#'+jQuery(this).attr('rel')).slideUp();
            }
        });

        jQuery('.w3eden .info.fa-solid').tooltip({html:true, placement: 'top'});

        /*$('body').on('click', '#all-package-settings .nav-tabs a', function (e) {
            e.preventDefault();
            $('#all-package-settings .nav-tabs li').removeClass('active');
            $(this).parent('li').addClass('active');
            $('#all-package-settings .tab-pane').removeClass('active in');
            $($(this).attr('href')).addClass('active in');
        });*/



    });


    function generatepass(id){
        wpdm_pass_target = '#'+id;
        var tpl = document.getElementById('generatepass-tpl');
        if (typeof WPDM !== 'undefined' && WPDM.dialog && tpl) {
            WPDM.dialog.show({
                title: '<?php echo esc_js(__("Generate Password", "download-manager")); ?>',
                content: tpl.innerHTML,
                size: 'md',
                icon: false,
                backdrop: 'static'
            });
        } else {
            jQuery('#generatepass').modal('show');
        }
    }

    function wpdm_view_package(){

    }




    <?php /* if(is_array($file)&&get_post_meta($file['id'],'__wpdm_lock',true)!='') { ?>
    jQuery('#<?php echo get_post_meta($file['id'],'__wpdm_lock',true); ?>').show();
    <?php } */ ?>
</script>

<style>
/* ========================================
   WPDM Settings Panel - Synced with admin-styles.css
   Uses CSS variables from base.css
   ======================================== */

.wpdm-tabs {
    display: flex;
    gap: 0;
    background: #fff;
    border-radius: 8px;
    overflow: hidden;
}

/* Tab Navigation */
.wpdm-tabs__nav {
    flex: 0 0 200px;
    background: linear-gradient(180deg, #f8f9fb 0%, #f3f4f7 100%);
    min-width: 220px;
    border-right: 1px solid #e1e5eb;
    padding: 8px;
    display: flex;
    flex-direction: column;
    gap: 2px;
}

.wpdm-tabs__btn {
    display: flex;
    align-items: center;
    gap: 10px;
    width: 100%;
    padding: 11px 14px;
    border: none;
    background: transparent;
    color: #5a6270;
    font-size: 13px;
    font-weight: 500;
    text-align: left;
    cursor: pointer;
    border-radius: 6px;
    transition: all 0.15s ease;
    position: relative;
    white-space: nowrap;
}

.wpdm-tabs__btn svg {
    width: 18px;
    height: 18px;
    flex-shrink: 0;
    opacity: 0.7;
}

.wpdm-tabs__btn:hover {
    background: rgba(255, 255, 255, 0.8);
    color: #1e2533;
}

.wpdm-tabs__btn:hover svg {
    opacity: 1;
}

.wpdm-tabs__btn.active {
    background: #fff;
    color: var(--admin-color, #3b74d5);
    font-weight: 600;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08), 0 1px 2px rgba(0, 0, 0, 0.06);
}

.wpdm-tabs__btn.active svg {
    opacity: 1;
    color: var(--admin-color, #3b74d5);
}

.wpdm-tabs__btn.active::before {
    content: '';
    position: absolute;
    left: 0;
    top: 50%;
    transform: translateY(-50%);
    width: 3px;
    height: 20px;
    background: var(--admin-color, #3b74d5);
    border-radius: 0 2px 2px 0;
}

/* Tab Content */
.wpdm-tabs__content {
    flex: 1;
    padding: 20px;
    min-height: 400px;
    background: #fff;
}

.wpdm-tabs__panel {
    display: none;
}

.wpdm-tabs__panel.active {
    display: block;
    animation: wpdmTabFadeIn 0.2s ease;
}

@keyframes wpdmTabFadeIn {
    from { opacity: 0; transform: translateY(4px); }
    to { opacity: 1; transform: translateY(0); }
}

/* Panel Enhancements */
.wpdm-settings-panel .panel {
    margin-bottom: 12px;
}

.wpdm-settings-panel .panel:last-child {
    margin-bottom: 0;
}

.wpdm-settings-panel .panel-heading {
    display: flex;
    align-items: center;
    gap: 10px;
}

.wpdm-settings-icon {
    width: 16px;
    height: 16px;
    color: var(--admin-color, #3b74d5);
    flex-shrink: 0;
    opacity: 0.8;
}

/* Labels */
.wpdm-settings-label {
    font-size: 10pt;
    font-weight: 600;
    color: #333;
    display: inline-block;
}

/* Hint Text */
.wpdm-settings-hint {
    font-size: 11px;
    color: #888;
    line-height: 1.4;
}

/* Form Control Enhancements */
.w3eden .form-control {
    padding: 7px 10px;
    font-size: 13px;
    line-height: 1.5;
    color: #333;
    background: #ffffff;
    border: 1px solid var(--border-color, #dde6ee);
    border-radius: 3px;
    transition: border-color 200ms ease, box-shadow 200ms ease;
    outline: none;
    height: auto;
}

.w3eden .form-control:hover {
    border-color: #b0c4d8;
}

.w3eden .form-control:focus {
    border-color: var(--admin-color, #3b74d5);
    box-shadow: 0 0 0 2px rgba(59, 116, 213, 0.15);
}

.wpdm-settings-panel .form-control::placeholder {
    color: #aaa;
}

/* Radio Group - Pill style */
.wpdm-radio-group {
    display: flex;
    flex-wrap: wrap;
    gap: 5px;
}

.wpdm-radio-option {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 6px 12px;
    background: var(--bg-lighter, #fbfdff);
    border: 1px solid var(--border-color, #dde6ee);
    border-radius: 3px;
    cursor: pointer;
    transition: all 200ms ease;
    margin: 0 !important;
    position: relative;
}

.wpdm-radio-option:hover {
    background: var(--bg-light, #f7fafd);
    border-color: #b0c4d8;
}

.wpdm-radio-option input[type="radio"] {
    position: absolute;
    opacity: 0;
    pointer-events: none;
    width: 0;
    height: 0;
}

.wpdm-radio-btn {
    width: 16px;
    height: 16px;
    border: 2px solid #c0c8d0;
    border-radius: 50%;
    position: relative;
    transition: all 200ms ease;
    flex-shrink: 0;
    background: #fff;
    left: 6px;
    position: absolute;
    margin-top: 1px;
}

.wpdm-radio-btn::after {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%) scale(0);
    width: 8px;
    height: 8px;
    background: var(--admin-color, #3b74d5);
    border-radius: 50%;
    transition: transform 200ms ease;
}

.wpdm-radio-text {
    font-size: 12px;
    color: #555;
    transition: color 200ms ease;
}

/* Checked state */
.wpdm-radio-option.active .wpdm-radio-btn,
.wpdm-radio-option:has(input[type="radio"]:checked) .wpdm-radio-btn {
    border-color: var(--admin-color, #3b74d5);
}

.wpdm-radio-option.active .wpdm-radio-btn::after,
.wpdm-radio-option:has(input[type="radio"]:checked) .wpdm-radio-btn::after {
    transform: translate(-50%, -50%) scale(1);
}

.wpdm-radio-option.active .wpdm-radio-text,
.wpdm-radio-option:has(input[type="radio"]:checked) .wpdm-radio-text {
    color: var(--admin-color, #3b74d5);
    font-weight: 600;
}

.wpdm-radio-option.active,
.wpdm-radio-option:has(input[type="radio"]:checked) {
    background: rgba(59, 116, 213, 0.08);
    border-color: var(--admin-color, #3b74d5);
}

/* Checkbox Styling - Custom style separate from toggle */
.wpdm-checkbox-label {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    cursor: pointer;
    font-size: 12px;
    color: #555;
    margin: 0 !important;
}

.wpdm-checkbox-label .wpdm-checkbox {
    -webkit-appearance: checkbox !important;
    appearance: checkbox !important;
    width: 14px !important;
    height: 14px !important;
    margin: 0 !important;
    cursor: pointer;
}

.wpdm-checkbox-label .wpdm-checkbox:after,
.wpdm-checkbox-label .wpdm-checkbox:before {
    display: none !important;
}

/* Master Key Field */
.wpdm-master-key-field {
    display: flex;
    gap: 6px;
    align-items: stretch;
}

.wpdm-btn-copy {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 7px 10px;
    background: var(--bg-light, #f7fafd);
    border: 1px solid var(--border-color, #dde6ee);
    border-radius: 3px;
    cursor: pointer;
    transition: all 200ms ease;
    color: #666;
}

.wpdm-btn-copy:hover {
    background: #e8f0f8;
    border-color: var(--admin-color, #3b74d5);
    color: var(--admin-color, #3b74d5);
}
.w3eden input[type=checkbox]{ margin-top: 2px; }

/* Responsive */
@media (max-width: 782px) {
    .wpdm-radio-group {
        flex-direction: column;
    }
}

/* Legacy overrides */
.w3eden .tooltip-inner{ border-radius: 4px !important; padding: 16px !important;  text-align: left; font-size: 12px; max-width: 250px; }
.w3eden input[type=radio]{ margin-top: 0; }
 .form-control.input-sm{ display: inline; }

    .ui-tabs .ui-tabs-nav li a{
        font-size: 10pt !important;
        outline: none !important;

    }
    .ui-tabs .ui-tabs-nav li{
        margin-bottom: 0 !important;
        border-bottom: 1px solid #dddddd !important;
    }

    .ui-tabs .ui-tabs-nav li.ui-state-active{
        border-bottom: 1px solid #ffffff !important;
    }
    .wdmiconfile{
        -webkit-border-radius: 6px;
        -moz-border-radius: 6px;
        border-radius: 6px;
    }


/* .w3eden input[type=radio], .w3eden label{ margin: 0 !important;}*/

#wpdm-files_length{
    display: none;
}
#wpdm-files_filter{
    margin-bottom:10px !important;
}
.adp-ui-state-highlight{
    width:50px;
    height:50px;
    background: #fff;
    float:left;
    padding: 4px;
    border:1px solid #aaa;
}
#wpdm-files tbody .ui-sortable-helper{
    background: transparent;

}
#wpdm-files tbody .ui-sortable-helper td{
    vertical-align: middle;
    background: #eeeeee;
}
input[type=text]{
    padding: 4px 7px;
    border-radius: 3px;
}


.dfile{background: #ffdfdf;}
.cfile{
    cursor: move;
}
.cfile img, .dfile img{cursor: pointer;}

#editorcontainer textarea{border:0px;width:99.9%;}
#icon_uploadUploader,#file_uploadUploader {background: transparent url('<?php echo plugins_url(); ?>/download-manager/images/browse.png') left top no-repeat; }
#icon_uploadUploader:hover,#file_uploadUploader:hover {background-position: left bottom; }
.frm td{line-height: 30px; border-bottom: 1px solid #EEEEEE; padding:5px; font-size:9pt;font-family: Tahoma;}

.fwpdmlock td{
    border:0px !important;
    vertical-align: middle !important;
}
#filelist {
    margin-top: 10px;
}
#filelist .file{
    margin-top: 5px;
    padding: 0px 10px;
    color:#444;
    display: block;
    margin-bottom: 5px;
    font-weight: normal;
}

table.widefat{
    border-bottom:0px;
}

.genpass{
    cursor: pointer;
}

h3,
h3.handle{
    cursor: default !important;
}


@-webkit-keyframes progress-bar-stripes {
    from {
        background-position: 40px 0;
    }
    to {
        background-position: 0 0;
    }
}

@-moz-keyframes progress-bar-stripes {
    from {
        background-position: 40px 0;
    }
    to {
        background-position: 0 0;
    }
}

@-ms-keyframes progress-bar-stripes {
    from {
        background-position: 40px 0;
    }
    to {
        background-position: 0 0;
    }
}

@-o-keyframes progress-bar-stripes {
    from {
        background-position: 0 0;
    }
    to {
        background-position: 40px 0;
    }
}

@keyframes progress-bar-stripes {
    from {
        background-position: 40px 0;
    }
    to {
        background-position: 0 0;
    }
}

.progress {
    height: 15px;
    margin-bottom: 10px;
    overflow: hidden;
    background-color: #f7f7f7;
    background-image: -moz-linear-gradient(top, #f5f5f5, #f9f9f9);
    background-image: -webkit-gradient(linear, 0 0, 0 100%, from(#f5f5f5), to(#f9f9f9));
    background-image: -webkit-linear-gradient(top, #f5f5f5, #f9f9f9);
    background-image: -o-linear-gradient(top, #f5f5f5, #f9f9f9);
    background-image: linear-gradient(to bottom, #f5f5f5, #f9f9f9);
    background-repeat: repeat-x;
    -webkit-border-radius: 4px;
    -moz-border-radius: 4px;
    border-radius: 4px;
    filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#fff5f5f5', endColorstr='#fff9f9f9', GradientType=0);
    -webkit-box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.1);
    -moz-box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.1);
    box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.1);
}

.progress .bar {
    float: left;
    width: 0;
    height: 100%;
    font-size: 12px;
    color: #ffffff;
    text-align: center;
    text-shadow: 0 -1px 0 rgba(0, 0, 0, 0.25);
    background-color: #0e90d2;
    background-image: -moz-linear-gradient(top, #149bdf, #0480be);
    background-image: -webkit-gradient(linear, 0 0, 0 100%, from(#149bdf), to(#0480be));
    background-image: -webkit-linear-gradient(top, #149bdf, #0480be);
    background-image: -o-linear-gradient(top, #149bdf, #0480be);
    background-image: linear-gradient(to bottom, #149bdf, #0480be);
    background-repeat: repeat-x;
    filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#ff149bdf', endColorstr='#ff0480be', GradientType=0);
    -webkit-box-shadow: inset 0 -1px 0 rgba(0, 0, 0, 0.15);
    -moz-box-shadow: inset 0 -1px 0 rgba(0, 0, 0, 0.15);
    box-shadow: inset 0 -1px 0 rgba(0, 0, 0, 0.15);
    -webkit-box-sizing: border-box;
    -moz-box-sizing: border-box;
    box-sizing: border-box;
    -webkit-transition: width 0.6s ease;
    -moz-transition: width 0.6s ease;
    -o-transition: width 0.6s ease;
    transition: width 0.6s ease;
}

.progress .bar + .bar {
    -webkit-box-shadow: inset 1px 0 0 rgba(0, 0, 0, 0.15), inset 0 -1px 0 rgba(0, 0, 0, 0.15);
    -moz-box-shadow: inset 1px 0 0 rgba(0, 0, 0, 0.15), inset 0 -1px 0 rgba(0, 0, 0, 0.15);
    box-shadow: inset 1px 0 0 rgba(0, 0, 0, 0.15), inset 0 -1px 0 rgba(0, 0, 0, 0.15);
}

.progress-striped .bar {
    background-color: #149bdf;
    background-image: -webkit-gradient(linear, 0 100%, 100% 0, color-stop(0.25, rgba(255, 255, 255, 0.15)), color-stop(0.25, transparent), color-stop(0.5, transparent), color-stop(0.5, rgba(255, 255, 255, 0.15)), color-stop(0.75, rgba(255, 255, 255, 0.15)), color-stop(0.75, transparent), to(transparent));
    background-image: -webkit-linear-gradient(45deg, rgba(255, 255, 255, 0.15) 25%, transparent 25%, transparent 50%, rgba(255, 255, 255, 0.15) 50%, rgba(255, 255, 255, 0.15) 75%, transparent 75%, transparent);
    background-image: -moz-linear-gradient(45deg, rgba(255, 255, 255, 0.15) 25%, transparent 25%, transparent 50%, rgba(255, 255, 255, 0.15) 50%, rgba(255, 255, 255, 0.15) 75%, transparent 75%, transparent);
    background-image: -o-linear-gradient(45deg, rgba(255, 255, 255, 0.15) 25%, transparent 25%, transparent 50%, rgba(255, 255, 255, 0.15) 50%, rgba(255, 255, 255, 0.15) 75%, transparent 75%, transparent);
    background-image: linear-gradient(45deg, rgba(255, 255, 255, 0.15) 25%, transparent 25%, transparent 50%, rgba(255, 255, 255, 0.15) 50%, rgba(255, 255, 255, 0.15) 75%, transparent 75%, transparent);
    -webkit-background-size: 40px 40px;
    -moz-background-size: 40px 40px;
    -o-background-size: 40px 40px;
    background-size: 40px 40px;
}

.progress.active .bar {
    -webkit-animation: progress-bar-stripes 2s linear infinite;
    -moz-animation: progress-bar-stripes 2s linear infinite;
    -ms-animation: progress-bar-stripes 2s linear infinite;
    -o-animation: progress-bar-stripes 2s linear infinite;
    animation: progress-bar-stripes 2s linear infinite;
}

.progress-danger .bar,
.progress .bar-danger {
    background-color: #dd514c;
    background-image: -moz-linear-gradient(top, #ee5f5b, #c43c35);
    background-image: -webkit-gradient(linear, 0 0, 0 100%, from(#ee5f5b), to(#c43c35));
    background-image: -webkit-linear-gradient(top, #ee5f5b, #c43c35);
    background-image: -o-linear-gradient(top, #ee5f5b, #c43c35);
    background-image: linear-gradient(to bottom, #ee5f5b, #c43c35);
    background-repeat: repeat-x;
    filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#ffee5f5b', endColorstr='#ffc43c35', GradientType=0);
}

.progress-danger.progress-striped .bar,
.progress-striped .bar-danger {
    background-color: #ee5f5b;
    background-image: -webkit-gradient(linear, 0 100%, 100% 0, color-stop(0.25, rgba(255, 255, 255, 0.15)), color-stop(0.25, transparent), color-stop(0.5, transparent), color-stop(0.5, rgba(255, 255, 255, 0.15)), color-stop(0.75, rgba(255, 255, 255, 0.15)), color-stop(0.75, transparent), to(transparent));
    background-image: -webkit-linear-gradient(45deg, rgba(255, 255, 255, 0.15) 25%, transparent 25%, transparent 50%, rgba(255, 255, 255, 0.15) 50%, rgba(255, 255, 255, 0.15) 75%, transparent 75%, transparent);
    background-image: -moz-linear-gradient(45deg, rgba(255, 255, 255, 0.15) 25%, transparent 25%, transparent 50%, rgba(255, 255, 255, 0.15) 50%, rgba(255, 255, 255, 0.15) 75%, transparent 75%, transparent);
    background-image: -o-linear-gradient(45deg, rgba(255, 255, 255, 0.15) 25%, transparent 25%, transparent 50%, rgba(255, 255, 255, 0.15) 50%, rgba(255, 255, 255, 0.15) 75%, transparent 75%, transparent);
    background-image: linear-gradient(45deg, rgba(255, 255, 255, 0.15) 25%, transparent 25%, transparent 50%, rgba(255, 255, 255, 0.15) 50%, rgba(255, 255, 255, 0.15) 75%, transparent 75%, transparent);
}

.progress-success .bar,
.progress .bar-success {
    background-color: #5eb95e;
    background-image: -moz-linear-gradient(top, #62c462, #57a957);
    background-image: -webkit-gradient(linear, 0 0, 0 100%, from(#62c462), to(#57a957));
    background-image: -webkit-linear-gradient(top, #62c462, #57a957);
    background-image: -o-linear-gradient(top, #62c462, #57a957);
    background-image: linear-gradient(to bottom, #62c462, #57a957);
    background-repeat: repeat-x;
    filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#ff62c462', endColorstr='#ff57a957', GradientType=0);
}

.progress-success.progress-striped .bar,
.progress-striped .bar-success {
    background-color: #62c462;
    background-image: -webkit-gradient(linear, 0 100%, 100% 0, color-stop(0.25, rgba(255, 255, 255, 0.15)), color-stop(0.25, transparent), color-stop(0.5, transparent), color-stop(0.5, rgba(255, 255, 255, 0.15)), color-stop(0.75, rgba(255, 255, 255, 0.15)), color-stop(0.75, transparent), to(transparent));
    background-image: -webkit-linear-gradient(45deg, rgba(255, 255, 255, 0.15) 25%, transparent 25%, transparent 50%, rgba(255, 255, 255, 0.15) 50%, rgba(255, 255, 255, 0.15) 75%, transparent 75%, transparent);
    background-image: -moz-linear-gradient(45deg, rgba(255, 255, 255, 0.15) 25%, transparent 25%, transparent 50%, rgba(255, 255, 255, 0.15) 50%, rgba(255, 255, 255, 0.15) 75%, transparent 75%, transparent);
    background-image: -o-linear-gradient(45deg, rgba(255, 255, 255, 0.15) 25%, transparent 25%, transparent 50%, rgba(255, 255, 255, 0.15) 50%, rgba(255, 255, 255, 0.15) 75%, transparent 75%, transparent);
    background-image: linear-gradient(45deg, rgba(255, 255, 255, 0.15) 25%, transparent 25%, transparent 50%, rgba(255, 255, 255, 0.15) 50%, rgba(255, 255, 255, 0.15) 75%, transparent 75%, transparent);
}

.progress-info .bar,
.progress .bar-info {
    background-color: #4bb1cf;
    background-image: -moz-linear-gradient(top, #5bc0de, #339bb9);
    background-image: -webkit-gradient(linear, 0 0, 0 100%, from(#5bc0de), to(#339bb9));
    background-image: -webkit-linear-gradient(top, #5bc0de, #339bb9);
    background-image: -o-linear-gradient(top, #5bc0de, #339bb9);
    background-image: linear-gradient(to bottom, #5bc0de, #339bb9);
    background-repeat: repeat-x;
    filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#ff5bc0de', endColorstr='#ff339bb9', GradientType=0);
}

.progress-info.progress-striped .bar,
.progress-striped .bar-info {
    background-color: #5bc0de;
    background-image: -webkit-gradient(linear, 0 100%, 100% 0, color-stop(0.25, rgba(255, 255, 255, 0.15)), color-stop(0.25, transparent), color-stop(0.5, transparent), color-stop(0.5, rgba(255, 255, 255, 0.15)), color-stop(0.75, rgba(255, 255, 255, 0.15)), color-stop(0.75, transparent), to(transparent));
    background-image: -webkit-linear-gradient(45deg, rgba(255, 255, 255, 0.15) 25%, transparent 25%, transparent 50%, rgba(255, 255, 255, 0.15) 50%, rgba(255, 255, 255, 0.15) 75%, transparent 75%, transparent);
    background-image: -moz-linear-gradient(45deg, rgba(255, 255, 255, 0.15) 25%, transparent 25%, transparent 50%, rgba(255, 255, 255, 0.15) 50%, rgba(255, 255, 255, 0.15) 75%, transparent 75%, transparent);
    background-image: -o-linear-gradient(45deg, rgba(255, 255, 255, 0.15) 25%, transparent 25%, transparent 50%, rgba(255, 255, 255, 0.15) 50%, rgba(255, 255, 255, 0.15) 75%, transparent 75%, transparent);
    background-image: linear-gradient(45deg, rgba(255, 255, 255, 0.15) 25%, transparent 25%, transparent 50%, rgba(255, 255, 255, 0.15) 50%, rgba(255, 255, 255, 0.15) 75%, transparent 75%, transparent);
}

.progress-warning .bar,
.progress .bar-warning {
    background-color: #faa732;
    background-image: -moz-linear-gradient(top, #fbb450, #f89406);
    background-image: -webkit-gradient(linear, 0 0, 0 100%, from(#fbb450), to(#f89406));
    background-image: -webkit-linear-gradient(top, #fbb450, #f89406);
    background-image: -o-linear-gradient(top, #fbb450, #f89406);
    background-image: linear-gradient(to bottom, #fbb450, #f89406);
    background-repeat: repeat-x;
    filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#fffbb450', endColorstr='#fff89406', GradientType=0);
}

.progress-warning.progress-striped .bar,
.progress-striped .bar-warning {
    background-color: #fbb450;
    background-image: -webkit-gradient(linear, 0 100%, 100% 0, color-stop(0.25, rgba(255, 255, 255, 0.15)), color-stop(0.25, transparent), color-stop(0.5, transparent), color-stop(0.5, rgba(255, 255, 255, 0.15)), color-stop(0.75, rgba(255, 255, 255, 0.15)), color-stop(0.75, transparent), to(transparent));
    background-image: -webkit-linear-gradient(45deg, rgba(255, 255, 255, 0.15) 25%, transparent 25%, transparent 50%, rgba(255, 255, 255, 0.15) 50%, rgba(255, 255, 255, 0.15) 75%, transparent 75%, transparent);
    background-image: -moz-linear-gradient(45deg, rgba(255, 255, 255, 0.15) 25%, transparent 25%, transparent 50%, rgba(255, 255, 255, 0.15) 50%, rgba(255, 255, 255, 0.15) 75%, transparent 75%, transparent);
    background-image: -o-linear-gradient(45deg, rgba(255, 255, 255, 0.15) 25%, transparent 25%, transparent 50%, rgba(255, 255, 255, 0.15) 50%, rgba(255, 255, 255, 0.15) 75%, transparent 75%, transparent);
    background-image: linear-gradient(45deg, rgba(255, 255, 255, 0.15) 25%, transparent 25%, transparent 50%, rgba(255, 255, 255, 0.15) 50%, rgba(255, 255, 255, 0.15) 75%, transparent 75%, transparent);
}
#access{
    width: 250px;
}

#nxt{
    background-color: #C1F4C1;
    background-image: -webkit-gradient(linear, 0 100%, 100% 0, color-stop(0.25, rgba(255, 255, 255, 0.15)), color-stop(0.25, transparent), color-stop(0.5, transparent), color-stop(0.5, rgba(255, 255, 255, 0.15)), color-stop(0.75, rgba(255, 255, 255, 0.15)), color-stop(0.75, transparent), to(transparent));
    background-image: -webkit-linear-gradient(45deg, rgba(255, 255, 255, 0.15) 25%, transparent 25%, transparent 50%, rgba(255, 255, 255, 0.15) 50%, rgba(255, 255, 255, 0.15) 75%, transparent 75%, transparent);
    background-image: -moz-linear-gradient(45deg, rgba(255, 255, 255, 0.15) 25%, transparent 25%, transparent 50%, rgba(255, 255, 255, 0.15) 50%, rgba(255, 255, 255, 0.15) 75%, transparent 75%, transparent);
    background-image: -o-linear-gradient(45deg, rgba(255, 255, 255, 0.15) 25%, transparent 25%, transparent 50%, rgba(255, 255, 255, 0.15) 50%, rgba(255, 255, 255, 0.15) 75%, transparent 75%, transparent);
    background-image: linear-gradient(45deg, rgba(255, 255, 255, 0.15) 25%, transparent 25%, transparent 50%, rgba(255, 255, 255, 0.15) 50%, rgba(255, 255, 255, 0.15) 75%, transparent 75%, transparent);
    -webkit-background-size: 40px 40px;
    -moz-background-size: 40px 40px;
    -o-background-size: 40px 40px;
    background-size: 40px 40px;
    display: none;
    border-bottom:1px solid #008000;
    color: #0C490C;font-family:'Courier New';padding:5px 10px;text-align: center;
}

#serr{
    display: none;margin-top: 5px;border:1px solid #800000;background: #FFEDED;color: #000;font-family:'Courier New';padding:5px 10px;text-align: left;
}
.action #nxt{
    width:100%;
    position: fixed;
    top:0px;left:0px;z-index:999999;
}
#nxt a{
    font-weight: bold;
    color:#0C490C;
}

.action-float{
    position:fixed;top:-33px;left:0px;width:100%;z-index:999999;text-align:right;
    background: rgba(0,0,0,0.9);
}

.action .inside,
.action-float .inside{
    margin: 0px;
}

.action-float #serr{
    width:500px;
    float: left;
    margin: 4px;
    z-index:999999;
    margin-top:-50px;
    border:1px solid #800000;
}
.action-float #nxt{
    width:500px;
    float: left;
    margin: 4px;
    z-index:999999;
    margin-top:-40px;
    border:1px solid #008000;
}

.wpdm-accordion > div{
    padding:10px;
}

/*.wpdmlock {*/
    /*opacity:0;*/
/*}*/
/*.wpdmlock+label {*/

    /*width:16px;*/
    /*height:16px;*/
    /*vertical-align:middle;*/
/*}*/

.w3eden .panel{
    padding: 0 !important;
}
.w3eden .wpdmlock{
    margin: 0 5px  0 0 !important;
}
.wpdm-unchecked{
    display: inline-block;
    float: left;
    width: 21px;
    height: 21px;
    padding: 0px;
    margin: 0px;
    cursor: hand;
    padding: 3px;
    margin-top: -4px !important;
    background-image: url('<?php echo plugins_url('/download-manager/assets/images/CheckBox.png'); ?>');
    background-position: -21px 0px;
}
.wpdm-checked{
    display: inline-block;
    float: left;
    width: 21px;
    height: 21px;
    padding: 0px;
    margin: 0px;
    cursor: hand;
    padding: 3px;
    margin-top: -4px !important;
    background-image: url('<?php echo plugins_url('/download-manager/assets/images/CheckBox.png'); ?>');
    background-position: 0px 0px;
}
.switch label { cursor: pointer; }
/*.switch input { display: none; }*/
p.field.switch{
    margin:0px;display:block;float:left;
}
.wpdm-accordion.w3eden .panel-default{
    margin-bottom: -2px !important;
    border-radius: 0;
}
    .wpdm-accordion.w3eden .panel-default .panel-heading{
        border-radius: 0;
    }
.w3eden .chzn-choices{
    background-image: none !important;
    border-radius: 3px;
}
 .w3eden .chzn-choices input{
     padding: 0;
     line-height: 10px;
 }
.w3eden .chzn-container-multi .chzn-drop{
    margin-top: 3px;
    border: 1px solid #5897fb !important;
    overflow: hidden;
    border-radius: 3px;
    padding: 10px 5px 10px 10px !important;
}
 .w3eden .chzn-container-multi .chzn-drop li{
     border-radius: 2px !important;
     margin-right: 10px !important;
 }
.w3eden .info.fa-solid{
    color: #666;
}
.w3eden .table td{
    vertical-align: middle !important;
}
</style>
