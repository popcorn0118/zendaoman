<?php
/**
 * Login Form Template - Split Panel Design
 * Modern two-column layout with decorative left panel
 */

if(!defined('ABSPATH')) die();
$logo = wpdm_valueof($params, 'logo');
if(!$logo) $logo = get_site_icon_url();
$site_name = get_bloginfo('name');

// Enqueue unified auth styles
wp_enqueue_style('wpdm-auth-forms', \WPDM\__\Template::locate_url('auth-forms.css', __DIR__), [], WPDM_VERSION);
?>

<div class="w3eden wpdm-auth-page" id="wpdmloginpage">
    <div class="wpdm-auth-split" id="wpdmlogin">
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
                    <?php if($logo && !is_user_logged_in()){ ?>
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
                    <div class="wpdm-auth-welcome-sub"><?php _e("Nice to see you again", "download-manager"); ?></div>
                    <div class="wpdm-auth-welcome-title"><?php _e("WELCOME BACK", "download-manager"); ?></div>
                    <div class="wpdm-auth-welcome-line"></div>
                    <div class="wpdm-auth-welcome-text">
                        <?php _e("Sign in to access your account, manage your downloads, and explore all the features available to you.", "download-manager"); ?>
                    </div>
                </div>
            </div>

            <!-- Right Panel - Form -->
            <div class="wpdm-auth-right">


                <div class="wpdm-auth-form-header">
                    <h1 class="wpdm-auth-form-title"><?php _e("Login Account", "download-manager"); ?></h1>
                    <p class="wpdm-auth-form-desc"><?php _e("Enter your credentials to access your account and continue where you left off.", "download-manager"); ?></p>
                </div>
                <?php do_action("wpdm_before_login_form"); ?>
                <form name="loginform" id="loginform" action="" method="post">
                    <input type="hidden" name="permalink" value="<?php the_permalink(); ?>" />

                    <!-- Alerts -->
                    <div id="__signin_msg">
                        <?php
                        $wpdm_signup_success = \WPDM\__\Session::get('__wpdm_signup_success');
                        if(isset($_GET['signedup'])){
                            if($wpdm_signup_success == '') $wpdm_signup_success = apply_filters("wpdm_signup_success", __("Your account has been created successfully.", "download-manager"));
                            ?>
                            <div class="wpdm-auth-alert success">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                                <span><?= $wpdm_signup_success; ?></span>
                            </div>
                            <?php
                        }
                        ?>
                    </div>

                    <?php if(isset($params['note_before']) && $params['note_before'] !== '') { ?>
                        <div class="wpdm-auth-alert info">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4"/><path d="M12 8h.01"/></svg>
                            <span><?= esc_html($params['note_before']); ?></span>
                        </div>
                    <?php } ?>

                    <!-- Email -->
                    <div class="wpdm-auth-field">
                        <div class="wpdm-auth-input-wrap">
                            <input type="text" name="wpdm_login[log]" id="user_login" class="wpdm-auth-input" required placeholder="<?php _e('Email ID', "download-manager"); ?>" autocomplete="username" />
                        </div>
                    </div>

                    <!-- Password -->
                    <div class="wpdm-auth-field">
                        <div class="wpdm-auth-input-wrap">
                            <input type="password" name="wpdm_login[pwd]" id="password" class="wpdm-auth-input has-toggle" required placeholder="<?php _e('Password', "download-manager"); ?>" autocomplete="current-password" />
                            <button type="button" class="wpdm-auth-pwd-toggle" onclick="wpdmTogglePwd()" aria-label="Toggle password visibility">
                                <svg id="wpdm-eye" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                <svg id="wpdm-eye-off" style="display:none" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                            </button>
                        </div>
                    </div>

                    <!-- Hidden hash -->
                    <?php $encrypted_params = \WPDM\__\Crypt::encrypt($params); ?>
                    <input type="hidden" name="__phash" id="__phash" value="<?php echo esc_attr($encrypted_params); ?>" />

                    <?php if(isset($params['note_after']) && $params['note_after'] !== '') { ?>
                        <div class="wpdm-auth-alert info">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4"/><path d="M12 8h.01"/></svg>
                            <span><?= esc_html($params['note_after']); ?></span>
                        </div>
                    <?php } ?>

                    <?php do_action("wpdm_login_form"); ?>
                    <?php do_action("login_form"); ?>

                    <!-- Remember & Forgot -->
                    <div class="wpdm-auth-meta">
                        <label class="wpdm-auth-remember">
                            <input type="checkbox" name="rememberme" id="rememberme" value="forever" />
                            <span class="wpdm-auth-check">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
                            </span>
                            <span><?php _e("Keep me signed in", "download-manager"); ?></span>
                        </label>
                        <a href="<?php echo wpdm_lostpassword_url(); ?>" class="wpdm-auth-forgot"><?php _e("Forgot password?", "download-manager"); ?></a>
                    </div>

                    <!-- Submit -->
                    <button type="submit" name="wp-submit" id="loginform-submit" class="wpdm-auth-btn">
                        <span><?php _e("Sign In", "download-manager"); ?></span>
                    </button>

                    <!-- Register Link -->
                    <?php if(isset($regurl) && $regurl != ''){ ?>
                        <div class="wpdm-auth-link">
                            <?php _e("Don't have an account?", "download-manager"); ?>
                            <a href="<?php echo esc_attr($regurl); ?>"><?php _e("Sign up", "download-manager"); ?></a>
                        </div>
                    <?php } ?>

                    <input type="hidden" name="redirect_to" value="<?= esc_url($log_redirect); ?>" />
                </form>

                <!-- Social Login -->
                <?php if(count($__wpdm_social_login) > 1) { ?>
                    <div class="wpdm-auth-divider"><?php echo isset($params['social_title']) ? esc_html($params['social_title']) : __("or continue with", "download-manager"); ?></div>
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
                <?php } ?>

                <?php do_action("wpdm_after_login_form"); ?>
            </div>
        </div>
    </div>
</div>

<script>
function wpdmTogglePwd() {
    var pwd = document.getElementById('password');
    var eye = document.getElementById('wpdm-eye');
    var eyeOff = document.getElementById('wpdm-eye-off');
    if (pwd.type === 'password') {
        pwd.type = 'text';
        eye.style.display = 'none';
        eyeOff.style.display = 'block';
    } else {
        pwd.type = 'password';
        eye.style.display = 'block';
        eyeOff.style.display = 'none';
    }
}

jQuery(function ($) {
    <?php if(!isset($params['form_submit_handler']) || $params['form_submit_handler'] !== false){ ?>
    var $btn = $('#loginform-submit');
    var btnHtml = $btn.html();

    $('#loginform').submit(function () {
        $btn.html('<span class="wpdm-auth-spinner"></span><span><?php _e("Signing in...", "download-manager"); ?></span>').attr('disabled', 'disabled');

        $(this).ajaxSubmit({
            error: function(error) {
                if(typeof error.responseJSON !== 'undefined') {
                    showErr(error.responseJSON.messages || error.responseJSON.message);
                    $btn.html(btnHtml).removeAttr('disabled');
                    <?php if((int)get_option('__wpdm_recaptcha_loginform', 0) === 1 && get_option('_wpdm_recaptcha_site_key') != ''){ ?>
                    try { grecaptcha.reset(); } catch (e) {}
                    <?php } ?>
                } else {
                    setTimeout(function () {
                        location.href = "<?= esc_url($log_redirect); ?>";
                    }, 1000);
                }
            },
            success: async function (res) {
                if (!res.success) {
                    showErr(res.message);
                    $btn.html(btnHtml).removeAttr('disabled');
                    <?php if((int)get_option('__wpdm_recaptcha_loginform', 0) === 1 && get_option('_wpdm_recaptcha_site_key') != ''){ ?>
                    try { grecaptcha.reset(); } catch (e) {}
                    <?php } ?>
                } else {
                    let proceed = await WPDM.doAction("wpdm_user_login", res);
                    $btn.html('<svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg><span>' + res.message + '</span>');
                    setTimeout(function () {
                        location.href = "<?= esc_url($log_redirect); ?>";
                    }, 1000);
                }
            }
        });
        return false;
    });
    <?php } ?>

    function showErr(msg) {
        $('#loginform .wpdm-auth-alert.error').remove();
        var html = '<div class="wpdm-auth-alert error">' +
            '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>' +
            '<span>' + msg + '</span></div>';
        $('#loginform').prepend(html);
    }

    $('body').on('click', '.wpdm-auth-alert.error', function(){
        $(this).slideUp(150, function() { $(this).remove(); });
    });
});
</script>
