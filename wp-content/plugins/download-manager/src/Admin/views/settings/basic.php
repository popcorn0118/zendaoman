<style>
    .frm td {
        padding: 5px;
        border-bottom: 1px solid #eeeeee;

        font-size: 10pt;

    }

    h4 {
        color: #336699;
        margin-bottom: 0px;
    }

    em {
        color: #888;
    }

    .wp-switch-editor {
        height: 27px !important;
    }
</style>


<div class="panel panel-default">
    <div class="panel-heading"><?php _e( "Editor Settings" , "download-manager" ); ?></div>
    <div class="panel-body">
        <input type="hidden" value="0" name="__wpdm_gutenberg_editor">
        <label><input type="checkbox" name="__wpdm_gutenberg_editor" value="1" <?php checked(1, get_option('__wpdm_gutenberg_editor')); ?> > <?php echo __("Enable Gutenberg editor for WordPress Download Manager", "download-manager"); ?></label>
    </div>
</div>

<div class="panel panel-default">
    <div class="panel-heading"><?php _e("Access Settings", "download-manager"); ?></div>
    <div class="panel-body">

        <div class="form-group">
            <label><?php echo __("File Browser Root:", "download-manager"); ?></label><span
                    title="<?php echo __("Root dir for server file browser.<br/><b>*Don't add tailing slash (/)</b>", "download-manager"); ?>"
                    class="info infoicon">(?)</span>
            <div class="input-group">
                <span class="input-group-addon">
                    <?php echo get_home_path(); ?>
                </span>
                <input type="text" class="form-control"
                       value="<?php echo str_replace(get_home_path(), "", get_option('_wpdm_file_browser_root', str_replace("\\", "/", dirname(UPLOAD_DIR)))); ?>"
                       name="_wpdm_file_browser_root" id="_wpdm_file_browser_root"/>
                <span class="input-group-btn">
                                    <button class="btn btn-secondary ttip" title="<?php _e('Reset Base Dir'); ?>"
                                            type="button"
                                            onclick="jQuery('#_wpdm_file_browser_root').val('<?php echo str_replace(get_home_path(), "", get_option('_wpdm_file_browser_root', str_replace("\\", "/", dirname(UPLOAD_DIR)))); ?>');"><i
                                                class="fas fa-redo"></i></button>
                                </span>
            </div>
        </div>

        <div class="form-group">
            <label><?php echo __("File Browser Access:", "download-manager"); ?></label><br/>
            <input type="hidden" name="_wpdm_file_browser_access[]" value="[NONE]"/>
            <select style="width: 100%" name="_wpdm_file_browser_access[]" multiple="multiple" data-placeholder="<?php _e("Who will have access to server file browser", "download-manager"); ?>">
                <?php

                $currentAccess = maybe_unserialize(get_option('_wpdm_file_browser_access', array('administrator')));
                $selz = '';

                ?>

                <?php
                global $wp_roles;
                $roles = array_reverse($wp_roles->role_names);
                foreach ($roles as $role => $name) {


                    if ($currentAccess) $sel = (in_array($role, $currentAccess)) ? 'selected=selected' : '';
                    else $sel = '';


                    ?>
                    <option value="<?php echo $role; ?>" <?php echo $sel ?>> <?php echo $name; ?></option>
                <?php } ?>
            </select>
        </div>


    </div>
</div>

<div class="panel panel-default">
    <div class="panel-heading"><?php echo __("reCAPTCHA Enterprise Settings", "download-manager"); ?></div>
    <div class="panel-body">
        <div class="form-group">
            <label><?php echo __("Google Cloud Project ID", "download-manager"); ?></label>
            <input type="text" class="form-control" name="_wpdm_recaptcha_project_id" value="<?php echo esc_attr(get_option('_wpdm_recaptcha_project_id')); ?>">
            <em><?php echo __("Your Google Cloud Project ID (found in Google Cloud Console)", "download-manager"); ?></em>
        </div>
        <div class="form-group">
            <label><?php echo __("reCAPTCHA Enterprise Site Key", "download-manager"); ?></label>
            <input type="text" class="form-control" name="_wpdm_recaptcha_site_key" value="<?php echo esc_attr(get_option('_wpdm_recaptcha_site_key')); ?>">
            <em><?php echo sprintf(__("Create a reCAPTCHA Enterprise key from %sGoogle Cloud Console%s", "download-manager"), '<a target="_blank" href="https://console.cloud.google.com/security/recaptcha">', '</a>'); ?></em>
        </div>
        <div class="form-group">
            <label><?php echo __("reCAPTCHA Enterprise API Key", "download-manager"); ?></label>
            <input type="text" class="form-control" name="_wpdm_recaptcha_secret_key"
                   value="<?php echo esc_attr(get_option('_wpdm_recaptcha_secret_key')); ?>">
            <em><?php echo sprintf(__("Create an API key from %sGoogle Cloud Console > APIs & Services > Credentials%s", "download-manager"), '<a target="_blank" href="https://console.cloud.google.com/apis/credentials">', '</a>'); ?></em>
            <div style="margin-top: 8px; padding: 8px 12px; background: #fff8e5; border-left: 3px solid #ffb900; font-size: 12px;">
                <strong><?php echo __("Important:", "download-manager"); ?></strong>
                <?php echo __("If you restrict this API key, do NOT use HTTP referrer restrictions - they will not work. Verification happens server-side, so use IP address restrictions (your server's IP) instead.", "download-manager"); ?>
            </div>
        </div>
        <div class="form-group">
            <input type="hidden" value="0" name="__wpdm_recaptcha_regform">
            <label><input type="checkbox" name="__wpdm_recaptcha_regform"
                          value="1" <?php checked(1, get_option('__wpdm_recaptcha_regform')); ?> > <?php echo __("Enable sign up form CAPTCHA validation", "download-manager"); ?>
            </label>
        </div>
        <div class="form-group">
            <input type="hidden" value="0" name="__wpdm_recaptcha_loginform">
            <label><input type="checkbox" name="__wpdm_recaptcha_loginform"
                          value="1" <?php checked(1, get_option('__wpdm_recaptcha_loginform')); ?>> <?php echo __("Enable sign in form CAPTCHA validation", "download-manager"); ?>
            </label>
        </div>

        <?php
        $recaptcha_site_key = get_option('_wpdm_recaptcha_site_key');
        $recaptcha_api_key = get_option('_wpdm_recaptcha_secret_key');
        $recaptcha_project_id = get_option('_wpdm_recaptcha_project_id');
        if ($recaptcha_site_key && $recaptcha_api_key && $recaptcha_project_id): ?>
        <hr>
        <div class="form-group">
            <label><?php echo __("Test reCAPTCHA Integration", "download-manager"); ?></label>
            <div style="padding: 15px; background: #f9f9f9; border: 1px solid #e5e5e5; border-radius: 4px; margin-top: 10px;">
                <div id="wpdm-recaptcha-test-widget"></div>
                <input type="hidden" id="wpdm-recaptcha-test-token" value="">
                <div style="margin-top: 15px;">
                    <button type="button" id="wpdm-recaptcha-test-btn" class="btn btn-secondary" disabled>
                        <i class="fas fa-check-circle"></i> <?php echo __("Test Verification", "download-manager"); ?>
                    </button>
                    <span id="wpdm-recaptcha-test-status" style="margin-left: 10px;"></span>
                </div>
                <div id="wpdm-recaptcha-test-result" style="margin-top: 10px; display: none;"></div>
            </div>
            <em><?php echo __("Complete the CAPTCHA above and click 'Test Verification' to verify your integration is working correctly.", "download-manager"); ?></em>
        </div>
        <script src="https://www.google.com/recaptcha/enterprise.js?render=explicit&onload=wpdmRecaptchaTestOnload" async defer></script>
        <script>
            function wpdmRecaptchaTestOnload() {
                grecaptcha.enterprise.render('wpdm-recaptcha-test-widget', {
                    'sitekey': '<?php echo esc_js($recaptcha_site_key); ?>',
                    'theme': 'light',
                    'callback': function(token) {
                        document.getElementById('wpdm-recaptcha-test-token').value = token;
                        document.getElementById('wpdm-recaptcha-test-btn').disabled = false;
                        document.getElementById('wpdm-recaptcha-test-status').innerHTML = '<span style="color: #46b450;"><i class="fas fa-check"></i> <?php echo esc_js(__("CAPTCHA completed. Click to test.", "download-manager")); ?></span>';
                    },
                    'expired-callback': function() {
                        document.getElementById('wpdm-recaptcha-test-token').value = '';
                        document.getElementById('wpdm-recaptcha-test-btn').disabled = true;
                        document.getElementById('wpdm-recaptcha-test-status').innerHTML = '<span style="color: #dc3232;"><i class="fas fa-exclamation-triangle"></i> <?php echo esc_js(__("CAPTCHA expired. Please try again.", "download-manager")); ?></span>';
                    }
                });
            }

            jQuery(function($) {
                $('#wpdm-recaptcha-test-btn').on('click', function() {
                    var token = $('#wpdm-recaptcha-test-token').val();
                    var $btn = $(this);
                    var $result = $('#wpdm-recaptcha-test-result');

                    if (!token) {
                        $result.html('<div class="notice notice-error" style="padding: 10px;"><strong><?php echo esc_js(__("Error:", "download-manager")); ?></strong> <?php echo esc_js(__("Please complete the CAPTCHA first.", "download-manager")); ?></div>').show();
                        return;
                    }

                    $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> <?php echo esc_js(__("Testing...", "download-manager")); ?>');

                    $.ajax({
                        url: ajaxurl,
                        type: 'POST',
                        data: {
                            action: 'wpdm_test_recaptcha',
                            token: token,
                            _wpnonce: '<?php echo wp_create_nonce('wpdm_test_recaptcha'); ?>'
                        },
                        success: function(response) {
                            if (response.success) {
                                $result.html('<div class="notice notice-success" style="padding: 10px;"><strong><?php echo esc_js(__("Success!", "download-manager")); ?></strong> <?php echo esc_js(__("reCAPTCHA verification is working correctly.", "download-manager")); ?><br><small><?php echo esc_js(__("Risk Score:", "download-manager")); ?> ' + response.data.score + '</small></div>').show();
                            } else {
                                var errorHtml = '<div class="notice notice-error" style="padding: 10px;">';
                                errorHtml += '<strong><?php echo esc_js(__("Failed:", "download-manager")); ?></strong> ' + response.data.message;
                                if (response.data.error_code) {
                                    errorHtml += ' <code style="background: #f0f0f0; padding: 2px 6px; border-radius: 3px; font-size: 11px;">' + response.data.error_code + '</code>';
                                }
                                if (response.data.error_details) {
                                    errorHtml += '<div style="margin-top: 10px; padding: 10px; background: #fff8e5; border-left: 4px solid #ffb900; font-size: 13px;">';
                                    errorHtml += '<strong><i class="fas fa-lightbulb"></i> <?php echo esc_js(__("How to fix:", "download-manager")); ?></strong><br>';
                                    errorHtml += response.data.error_details;
                                    errorHtml += '</div>';
                                }
                                errorHtml += '</div>';
                                $result.html(errorHtml).show();
                            }
                            $btn.prop('disabled', false).html('<i class="fas fa-check-circle"></i> <?php echo esc_js(__("Test Verification", "download-manager")); ?>');
                            grecaptcha.enterprise.reset();
                            $('#wpdm-recaptcha-test-token').val('');
                            $('#wpdm-recaptcha-test-status').html('');
                        },
                        error: function() {
                            $result.html('<div class="notice notice-error" style="padding: 10px;"><strong><?php echo esc_js(__("Error:", "download-manager")); ?></strong> <?php echo esc_js(__("Request failed. Please try again.", "download-manager")); ?></div>').show();
                            $btn.prop('disabled', false).html('<i class="fas fa-check-circle"></i> <?php echo esc_js(__("Test Verification", "download-manager")); ?>');
                        }
                    });
                });
            });
        </script>
        <?php else: ?>
        <hr>
        <div class="form-group">
            <em style="color: #dc3232;"><i class="fas fa-info-circle"></i> <?php echo __("Enter all three credentials above and save settings to test your reCAPTCHA integration.", "download-manager"); ?></em>
        </div>
        <?php endif; ?>
    </div>
</div>

<div class="panel panel-default">
    <div class="panel-heading"><?php _e("Verification Settings", "download-manager"); ?></div>
    <div class="panel-body">

            <div class="form-group">
                <label><?php echo __("Blocked IPs", "download-manager"); ?></label>
                <textarea placeholder="<?php _e("One IP per line", "download-manager"); ?>" rows="5"
                          class="form-control"
                          name="__wpdm_blocked_ips"><?php echo esc_attr(get_option('__wpdm_blocked_ips')); ?></textarea>
                <em><?php _e("List IP Addresses to blacklist. One IP per line ( Ex: IPv4 - 192.168.23.12 or 192.168.23.1/24 or 192.168.23.* , IPv6 - 2a01:8760:2:3001::1 or 2620:112:3000::/44 )", "download-manager"); ?></em>
            </div>
            <div class="form-group">
                <label><?php echo __("Message for Blocked IPs", "download-manager"); ?></label>
                <textarea placeholder="<?php _e("Message to show when an IP is blocked", "download-manager"); ?>"
                          class="form-control"
                          name="__wpdm_blocked_ips_msg"><?php echo get_option('__wpdm_blocked_ips_msg'); ?></textarea>
                <em><?php _e("Message to show when an IP is blocked", "download-manager"); ?></em>
            </div>


    </div>
</div>


<div class="panel panel-default">
    <div class="panel-heading"><?php _e("Upload Settings", "download-manager"); ?></div>
    <div class="panel-body">

        <div class="form-group">
            <label><?php _e('Allowed file types to upload', 'download-manager'); ?></label><br/>
            <?php
            $allowed_file_types = get_option("__wpdm_allowed_file_types", '');
            ?>
            <input type="text" class="form-control" value="<?php echo  $allowed_file_types; ?>"
                   placeholder="<?php echo  esc_attr__('Keep empty to use wordpress defaults', 'download-manager'); ?>"
                   name="__wpdm_allowed_file_types"/>
            <em><?php _e('Enter the file extensions you want to allow to upload through WPDM ( ex: png,jpg,pdf )', 'download-manager'); ?></em>
            <br/>

        </div>

        <hr/>
        <div class="form-group">
            <input type="hidden" value="0" name="__wpdm_sanitize_filename"/>
            <label><input <?php checked(1, get_option('__wpdm_sanitize_filename', 0)); ?>
                          type="checkbox" value="1"
                          name="__wpdm_sanitize_filename"><?php _e("Sanitize Filename", "download-manager"); ?>
            </label><br/>
            <em><?php _e("Check the option if you want to sanitize uploaded file names to remove illegal chars", "download-manager"); ?></em>
            <br/>

        </div>

        <hr/>
        <div class="form-group">
            <input type="hidden" value="0" name="__wpdm_chunk_upload"/>
            <label><input <?php checked(1, get_option('__wpdm_chunk_upload', 0)); ?>
                          type="checkbox" value="1"
                          name="__wpdm_chunk_upload"><?php _e('Chunk Upload', 'download-manager'); ?></label><br/>
            <em><?php _e('Check the option if you want to enable chunk upload to override http upload limits', 'download-manager'); ?></em>
            <br/>

        </div>
        <div class="form-group">
            <label><?php _e('Chunk Size', 'download-manager'); ?></label><br/>
            <div class="input-group">
                <input class="form-control" value="<?php echo get_option('__wpdm_chunk_size', 1024); ?>" type="number"
                       name="__wpdm_chunk_size">
                <div class="input-group-addon">KB</div>
            </div>
            <br/>

        </div>


    </div>
</div>


<div class="panel panel-default">
    <div class="panel-heading"><?php echo __("Messages", "download-manager"); ?></div>
    <div class="panel-body">
        <div class="form-group">
            <label for="__wpdm_download_limit_exceeded"><?php echo __("Download Limit Exceeded Message for Packages:", "download-manager"); ?></label>
            <textarea id="__wpdm_download_limit_exceeded" name="__wpdm_download_limit_exceeded"
                      class="form-control"><?php echo stripslashes(get_option('__wpdm_download_limit_exceeded')); ?></textarea>
        </div>
        <div class="form-group">
            <label for="__wpdm_permission_denied_msg"><?php echo __("Permission Denied Message for Packages:", "download-manager"); ?></label>
            <textarea id="__wpdm_permission_denied_msg" name="__wpdm_permission_denied_msg"
                      class="form-control"><?php echo stripslashes(get_option('__wpdm_permission_denied_msg')); ?></textarea>
        </div>

        <div class="form-group">
            <label><?php echo __("Login Required Message:", "download-manager"); ?></label>
            <textarea class="form-control" cols="70" rows="6"
                      name="wpdm_login_msg"><?php echo get_option('wpdm_login_msg', false) ? stripslashes(get_option('wpdm_login_msg')) : ('<div class="w3eden"><div class="panel panel-default card"><div class="panel-body card-body"><span class="text-danger">Login is required to access this page</span></div><div class="panel-footer card-footer text-right"><a href="' . wp_login_url() . '?redirect_to=[this_url]" class="btn btn-danger wpdmloginmodal-trigger btn-sm"><i class="fa fa-lock"></i> Login</a></div></div></div>'); ?></textarea>
            <em class="note"><?php echo sprintf(__("If you want to show login form instead of message user short-code [wpdm_login_form]. To show login form in a modal popup, please follow %s the doc here %s", "download-manager"), "<a target='_blank' href='https://www.wpdownloadmanager.com/how-to-add-modal-popup-login-form-in-your-wordpress-site/'>", "</a>"); ?></em>

        </div>

    </div>
</div>

<div class="panel panel-default">
    <div class="panel-heading"><?php echo __("File Download", "download-manager"); ?></div>
    <div class="panel-body">

        <div class="form-group"><input type="hidden" name="__wpdm_mask_link" value="0">
            <label><input type="radio" <?php checked(get_option('__wpdm_mask_dlink', 1), 1); ?> name="__wpdm_mask_dlink"
                          value="1"> <?php _e("Mask Download Link", "download-manager"); ?> &nbsp; </label>
            <label><input type="radio" <?php checked(get_option('__wpdm_mask_dlink', 1), 0); ?> name="__wpdm_mask_dlink"
                          value="0"> <?php _e("Unmask Download Link", "download-manager"); ?></label><br/>
            <em><?php _e("Check this option if you want to mask/unmask file download link. If you unmask download link, bots will be able the find any public download link easily.", "download-manager"); ?></em>
        </div>
        <hr/>

        <div class="form-group">
            <input type="hidden" name="__wpdm_adblocked_off" value="0">
            <label><input onclick="if(jQuery(this).is(':checked')) jQuery('#abmsg').slideDown(); else jQuery('#abmsg').slideUp();" type="checkbox" <?php checked(get_option('__wpdm_adblocked_off', 0), 1); ?> name="__wpdm_adblocked_off" value="1"> <?php _e("Ad-blocker must be disabled to download", "download-manager"); ?></label>
            <div><em><?php _e("Users/Visitors must disable ad-blockers to download", "download-manager"); ?></em></div>
            <div id="abmsg" style="margin-top: 10px;border: 1px solid #eee;border-radius: 4px;background: #fcfcfc;padding: 10px 15px;display:<?php echo !(int)get_option('__wpdm_adblocked_off', 0) ? 'none' : 'block'; ?>;">
                <label><?php _e("Message to show when ad blocker is active", "download-manager"); ?>:</label><br/>
                <input class="form-control" type="text" name="__wpdm_adblocked_msg" value="<?php echo esc_attr(get_option('__wpdm_adblocked_msg', 'We noticed an ad blocker. Consider whitelisting us to support the site ❤️')); ?>" />
                <hr/>
                <strong><?php _e('When to show the notice?', 'download-manager') ?></strong><br/>
                <select name="__wpdm_adblocked_msgd" style="max-width: 100%;width: 310px;">
                    <option value="download"><?php _e("When user clicks on download link", "download-manager"); ?></option>
                    <option value="package" <?php selected(get_option('__wpdm_adblocked_msgd', 'download'), 'package') ?> ><?php _e("As soon as user opens the download page", "download-manager"); ?></option>
                    <option value="site" <?php selected(get_option('__wpdm_adblocked_msgd', 'download'), 'site') ?>><?php _e("After every page load", "download-manager"); ?></option>
                </select>
            </div>
        </div>
        <hr/>


        <div class="form-group">
            <label><?php echo __("Download Speed:", "download-manager"); ?></label>
            <div class="input-group">
                <input type=text class="form-control" name="__wpdm_download_speed"
                       value="<?php echo intval(get_option('__wpdm_download_speed', 4096)); ?>"/>
                <span class="input-group-addon">KB/s</span>
            </div>
        </div>
        <hr/>
        <em class="note"><?php _e("If you get broken download, then try enabling/disabling following options, as sometimes server may not support output buffering or partial downloads", "download-manager"); ?>
            :</em>
        <hr/>
        <div class="form-group">
            <label><?php _e("Resumable Downloads", "download-manager"); ?></label><br/>
            <select name="__wpdm_download_resume">
                <option value="1"><?php _e("Enabled", "download-manager"); ?></option>
                <option value="2" <?php selected(get_option('__wpdm_download_resume'), 2); ?>><?php _e("Disabled", "download-manager"); ?></option>
            </select>
        </div>
        <div class="form-group">
            <label><?php _e("Output Buffering", "download-manager"); ?></label><br/>
            <select name="__wpdm_support_output_buffer">
                <option value="1"><?php _e("Enabled", "download-manager"); ?></option>
                <option value="0" <?php selected(get_option('__wpdm_support_output_buffer'), 0); ?>><?php _e("Disabled", "download-manager"); ?></option>
            </select>
        </div>

        <div class="form-group">
            <hr/>
            <input type="hidden" value="0" name="__wpdm_open_in_browser"/>
            <label><input
                          type="checkbox" <?php checked(get_option('__wpdm_open_in_browser'), 1); ?> value="1"
                          name="__wpdm_open_in_browser"><?php _e("Open in Browser", "download-manager"); ?></label><br/>
            <em><?php _e("Try to Open in Browser instead of download when someone clicks on download link", "download-manager"); ?></em>
        </div>
        <div class="form-group">
            <hr/>
            <input type="hidden" value="0" name="__wpdm_mdl_off"/>
            <label><input
                          type="checkbox" <?php checked(get_option('__wpdm_mdl_off'), 1); ?> value="1"
                          name="__wpdm_mdl_off"><?php _e("Disable Master Download URL", "download-manager"); ?></label><br/>
            <em><?php _e("Enabling this option will disable any old and new master download link", "download-manager"); ?></em>
            <br/>

        </div>

    </div>
</div>


<div class="panel panel-default">
    <div class="panel-heading"><?php _e("Misc Settings", "download-manager"); ?></div>
    <div class="panel-body">


        <table cellpadding="5" cellspacing="0" class="frm" width="100%">

            <?php do_action('basic_settings'); ?>

        </table>

    </div>


</div>

<?php do_action('basic_settings_section'); ?>

<script>
    jQuery(function ($) {
        $('#wpdmap').change(function () {

            if (this.value == 1)
                $('#aps').slideDown();
            else
                $('#aps').slideUp();
        });
    });
</script>
<style>
    .w3eden textarea.form-control {
        min-width: 100%;
        max-width: 100%;
        width: 100%;
        height: 70px;
    }
</style>
