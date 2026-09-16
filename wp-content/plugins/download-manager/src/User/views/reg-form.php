<?php
/**
 * Registration Form Template - Split Panel Design
 * Modern two-column layout with decorative left panel
 */

if(!defined('ABSPATH')) die('!');
$logo = wpdm_valueof($params, 'logo');
if(!$logo) $logo = get_site_icon_url();
$site_name = get_bloginfo('name');

// Enqueue unified auth styles
wp_enqueue_style('wpdm-auth-forms', \WPDM\__\Template::locate_url('auth-forms.css', __DIR__), [], WPDM_VERSION);
?>

<div class="w3eden wpdm-auth-page" id="wpdmregpage">
    <div class="wpdm-auth-split" id="wpdmreg">
        <?php if(get_option('users_can_register')): ?>
            <div class="wpdm-auth-panel">
                <!-- Left Panel - Decorative -->
                <div class="wpdm-auth-left">
                    <div class="wpdm-auth-grid"></div>
                    <div class="wpdm-auth-circles">
                        <div class="wpdm-auth-circle"></div>
                        <div class="wpdm-auth-circle"></div>
                        <div class="wpdm-auth-circle"></div>
                        <div class="wpdm-auth-circle"></div>
                    </div>

                    <div class="wpdm-auth-brand">
                        <a href="<?php echo home_url(); ?>">
                        <?php if($logo){ ?>
                            <img src="<?php echo esc_attr($logo); ?>" alt="<?php echo esc_attr($site_name); ?>" />
                        <?php } else { ?>
                            <div class="wpdm-auth-brand-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4"/><path d="M12 8h.01"/></svg>
                            </div>
                        <?php } ?>
                        <span><?php echo esc_html($site_name); ?></span>
                        </a>
                    </div>

                    <div class="wpdm-auth-welcome">
                        <div class="wpdm-auth-welcome-sub"><?php _e("Start your journey", "download-manager"); ?></div>
                        <div class="wpdm-auth-welcome-title"><?php _e("JOIN US", "download-manager"); ?></div>
                        <div class="wpdm-auth-welcome-line"></div>
                        <div class="wpdm-auth-welcome-text">
                            <?php _e("Create your free account today to access exclusive downloads, manage your files, and unlock all features.", "download-manager"); ?>
                        </div>
                    </div>
                </div>

                <!-- Right Panel - Form -->
                <div class="wpdm-auth-right wpdm-auth-right--scroll">
                    <div class="wpdm-auth-form-header wpdm-auth-form-header--compact">
                        <h1 class="wpdm-auth-form-title"><?php _e("Create Account", "download-manager"); ?></h1>
                        <p class="wpdm-auth-form-desc"><?php _e("Fill in your details below to get started with your free account.", "download-manager"); ?></p>
                    </div>

                    <form method="post" action="" id="registerform" name="registerform">
                        <?php wp_nonce_field(WPDM_PUB_NONCE, 'wdpmregnonce'); ?>

                        <div id="__signup_msg"></div>

                        <?php if(!$_social_only): ?>
                            <?php if(isset($params['note_before']) && trim($params['note_before']) != ''): ?>
                                <div class="wpdm-auth-alert info">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4"/><path d="M12 8h.01"/></svg>
                                    <span><?php echo esc_html($params['note_before']); ?></span>
                                </div>
                            <?php endif; ?>

                            <?= $form_html; ?>

                            <?php if(isset($params['note_after']) && trim($params['note_after']) != ''): ?>
                                <div class="wpdm-auth-alert info">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4"/><path d="M12 8h.01"/></svg>
                                    <span><?php echo esc_html($params['note_after']); ?></span>
                                </div>
                            <?php endif; ?>

                            <?php do_action("wpdm_register_form"); ?>
                            <?php do_action("register_form"); ?>

                            <button type="submit" class="wpdm-auth-btn wpdm-auth-btn--margin-top" id="registerform-submit" name="wp-submit">
                                <span><?php _e("Create Account", "download-manager"); ?></span>
                            </button>
                        <?php endif; ?>

                        <!-- Social Login -->
                        <?php if(count($__wpdm_social_login) > 1): ?>
                            <div class="wpdm-auth-divider"><?php echo isset($params['social_title']) ? esc_html($params['social_title']) : __("or sign up with", "download-manager"); ?></div>
                            <div class="wpdm-auth-social">
                                <?php if(isset($__wpdm_social_login['google'])): ?>
                                    <button type="button" class="wpdm-auth-social-btn google" onclick="return _PopupCenter('<?php echo home_url('/?sociallogin=google'); ?>', 'Google', 400, 400);" title="Google">
                                        <svg viewBox="0 0 24 24" fill="currentColor"><path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/><path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/><path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/><path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/></svg>
                                    </button>
                                <?php endif; ?>
                                <?php if(isset($__wpdm_social_login['facebook'])): ?>
                                    <button type="button" class="wpdm-auth-social-btn facebook" onclick="return _PopupCenter('<?php echo home_url('/?sociallogin=facebook'); ?>', 'Facebook', 400, 400);" title="Facebook">
                                        <svg viewBox="0 0 24 24" fill="currentColor"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                                    </button>
                                <?php endif; ?>
                                <?php if(isset($__wpdm_social_login['twitter'])): ?>
                                    <button type="button" class="wpdm-auth-social-btn twitter" onclick="return _PopupCenter('<?php echo home_url('/?sociallogin=twitter'); ?>', 'Twitter', 400, 400);" title="X">
                                        <svg viewBox="0 0 24 24" fill="currentColor"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                                    </button>
                                <?php endif; ?>
                                <?php if(isset($__wpdm_social_login['linkedin'])): ?>
                                    <button type="button" class="wpdm-auth-social-btn linkedin" onclick="return _PopupCenter('<?php echo home_url('/?sociallogin=linkedin'); ?>', 'LinkedIn', 400, 400);" title="LinkedIn">
                                        <svg viewBox="0 0 24 24" fill="currentColor"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
                                    </button>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>

                        <!-- Login Link -->
                        <?php if($loginurl != ''): ?>
                            <div class="wpdm-auth-link">
                                <?php _e("Already have an account?", "download-manager"); ?>
                                <a href="<?php echo esc_attr($loginurl); ?>"><?php _e("Sign in", "download-manager"); ?></a>
                            </div>
                        <?php endif; ?>
                    </form>
                </div>
            </div>
        <?php else: ?>
            <div class="wpdm-auth-panel">
                <div class="wpdm-auth-left">
                    <div class="wpdm-auth-grid"></div>
                    <div class="wpdm-auth-circles">
                        <div class="wpdm-auth-circle"></div>
                        <div class="wpdm-auth-circle"></div>
                        <div class="wpdm-auth-circle"></div>
                        <div class="wpdm-auth-circle"></div>
                    </div>
                    <div class="wpdm-auth-brand">
                        <?php if($logo){ ?>
                            <img src="<?php echo esc_attr($logo); ?>" alt="<?php echo esc_attr($site_name); ?>" />
                        <?php } ?>
                        <span><?php echo esc_html($site_name); ?></span>
                    </div>
                    <div class="wpdm-auth-welcome">
                        <div class="wpdm-auth-welcome-title"><?php _e("REGISTRATION", "download-manager"); ?></div>
                        <div class="wpdm-auth-welcome-line"></div>
                    </div>
                </div>
                <div class="wpdm-auth-right">
                    <div class="wpdm-auth-disabled">
                        <div class="wpdm-auth-disabled-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"/></svg>
                        </div>
                        <p><?php _e("Registration is currently disabled.", "download-manager"); ?></p>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
jQuery(function ($) {
    $('#__reg_nonce').val('<?php echo wp_create_nonce(NONCE_KEY); ?>');

    <?php if(!isset($params['form_submit_handler']) || $params['form_submit_handler'] !== false): ?>
    var $btn = $('#registerform-submit');
    var btnHtml = $btn.html();

    $('#registerform').submit(function (e) {
        e.preventDefault();

        if ($('#reg_password').val() !== $('#reg_confirm_pass').val()) {
            $('#reg_confirm_pass').parent('.input-wrapper').addClass('input-error');
            showErr('<?php echo esc_js(__("Passwords do not match.", "download-manager")); ?>');
            return false;
        } else {
            $('#reg_confirm_pass').parent('.input-wrapper').removeClass('input-error');
        }

        $btn.html('<span class="wpdm-auth-spinner"></span><span><?php _e("Creating...", "download-manager"); ?></span>').attr('disabled', 'disabled');

        var form_data = $(this).serializeArray();

        $(this).ajaxSubmit({
            success: function (res) {
                if (res.success == false) {
                    showErr(res.message);
                    $btn.html(btnHtml).removeAttr('disabled');
                } else if (res.success == true) {
                    WPDM.doAction("wpdm_new_signup", form_data);
                    $btn.html('<svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg><span><?php _e("Success!", "download-manager"); ?></span>');
                    setTimeout(function () {
                        location.href = res.redirect_to;
                    }, 1500);
                } else {
                    alert(res);
                }
            },
            error: function() {
                showErr('<?php echo esc_js(__("Something went wrong. Try again.", "download-manager")); ?>');
                $btn.html(btnHtml).removeAttr('disabled');
            }
        });

        return false;
    });
    <?php endif; ?>

    function showErr(msg) {
        $('#registerform .wpdm-auth-alert.error').remove();
        var html = '<div class="wpdm-auth-alert error">' +
            '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>' +
            '<span>' + msg + '</span></div>';
        $('#__signup_msg').html(html);
    }

    $('body').on('click', '.wpdm-auth-alert.error', function() {
        $(this).slideUp(150, function() { $(this).remove(); });
    });

    <?php
    if($error = \WPDM\__\Session::get('wpdm_signup_error')):
    ?>
    showErr('<?php echo esc_js($error); ?>');
    <?php
    \WPDM\__\Session::clear('wpdm_signup_error');
    endif;
    ?>
});
</script>
