<?php
/**
 * Reset Password Form Template - Split Panel Design
 * Modern two-column layout with decorative left panel
 * Matches login-form.php design
 */

if(!defined('ABSPATH')) die();

use WPDM\__\__;
use WPDM\__\Crypt;

$login = Crypt::decrypt(__::query_var('login'));
$user = check_password_reset_key(__::query_var('key'), $login);

$logo = get_site_icon_url();
$site_name = get_bloginfo('name');

// Enqueue unified auth styles
wp_enqueue_style('wpdm-auth-forms', \WPDM\__\Template::locate_url('auth-forms.css', __DIR__), [], WPDM_VERSION);
?>

<?php if(!is_wp_error($user)): ?>

<div class="w3eden wpdm-auth-page" id="wpdmloginpage">
    <div class="wpdm-auth-split" id="wpdmlogin">
        <div class="wpdm-auth-panel wpdm-auth-panel--short">
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
                    <?php if($logo){ ?>
                        <img src="<?php echo esc_attr($logo); ?>" alt="<?php echo esc_attr($site_name); ?>" />
                    <?php } else { ?>
                        <div class="wpdm-auth-brand-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                        </div>
                    <?php } ?>
                    <span><?php echo esc_html($site_name); ?></span>
                </div>

                <div class="wpdm-auth-welcome">
                    <div class="wpdm-auth-welcome-sub"><?php _e("Secure your account", "download-manager"); ?></div>
                    <div class="wpdm-auth-welcome-title"><?php _e("NEW PASSWORD", "download-manager"); ?></div>
                    <div class="wpdm-auth-welcome-line"></div>
                    <div class="wpdm-auth-welcome-text">
                        <?php _e("Create a strong password to protect your account. Use a mix of letters, numbers, and symbols.", "download-manager"); ?>
                    </div>
                </div>
            </div>

            <!-- Right Panel - Form -->
            <div class="wpdm-auth-right">
                <div class="wpdm-auth-form-header">
                    <h1 class="wpdm-auth-form-title"><?php _e("Reset Password", "download-manager"); ?></h1>
                    <p class="wpdm-auth-form-desc"><?php _e("Enter your new password below. Make sure it's at least 8 characters long.", "download-manager"); ?></p>
                </div>

                <form name="updatePasswordForm" id="updatePassword" action="<?php echo admin_url('/admin-ajax.php?action=updatePassword'); ?>" method="post">
                    <?php wp_nonce_field('wpdm_password_reset_' . $user->ID, '__wpdm_update_pass'); ?>
                    <input type="hidden" name="key" value="<?php echo esc_attr(__::query_var('key')); ?>">
                    <input type="hidden" name="login" value="<?php echo esc_attr(__::query_var('login')); ?>">

                    <!-- New Password -->
                    <div class="wpdm-auth-field">
                        <div class="wpdm-auth-input-wrap">
                            <input type="password" name="password" id="password" class="wpdm-auth-input has-toggle" required placeholder="<?php _e('New Password', "download-manager"); ?>" autocomplete="new-password" />
                            <button type="button" class="wpdm-auth-pwd-toggle" onclick="wpdmTogglePwd('password')" aria-label="Toggle password visibility">
                                <svg class="eye-open" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                <svg class="eye-closed" style="display:none" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                            </button>
                        </div>
                    </div>

                    <!-- Confirm Password -->
                    <div class="wpdm-auth-field">
                        <div class="wpdm-auth-input-wrap">
                            <input type="password" name="cpassword" id="cpassword" class="wpdm-auth-input has-toggle" required placeholder="<?php _e('Confirm Password', "download-manager"); ?>" autocomplete="new-password" />
                            <button type="button" class="wpdm-auth-pwd-toggle" onclick="wpdmTogglePwd('cpassword')" aria-label="Toggle password visibility">
                                <svg class="eye-open" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                <svg class="eye-closed" style="display:none" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                            </button>
                        </div>
                    </div>

                    <!-- Submit -->
                    <button type="submit" name="wp-submit" id="updatePassword-submit" class="wpdm-auth-btn">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 2l-2 2m-7.61 7.61a5.5 5.5 0 1 1-7.778 7.778 5.5 5.5 0 0 1 7.777-7.777zm0 0L15.5 7.5m0 0l3 3L22 7l-3-3m-3.5 3.5L19 4"/></svg>
                        <span><?php _e("Update Password", "download-manager"); ?></span>
                    </button>

                    <!-- Login Link -->
                    <div class="wpdm-auth-link">
                        <?php _e("Remember your password?", "download-manager"); ?>
                        <a href="<?php echo wpdm_login_url(); ?>"><?php _e("Sign in", "download-manager"); ?></a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function wpdmTogglePwd(fieldId) {
    var pwd = document.getElementById(fieldId);
    var wrap = pwd.closest('.wpdm-auth-input-wrap');
    var eyeOpen = wrap.querySelector('.eye-open');
    var eyeClosed = wrap.querySelector('.eye-closed');
    if (pwd.type === 'password') {
        pwd.type = 'text';
        eyeOpen.style.display = 'none';
        eyeClosed.style.display = 'block';
    } else {
        pwd.type = 'password';
        eyeOpen.style.display = 'block';
        eyeClosed.style.display = 'none';
    }
}

jQuery(function ($) {
    var $btn = $('#updatePassword-submit');
    var btnHtml = $btn.html();

    $('#updatePassword').submit(function () {
        // Validate passwords match
        if ($('#password').val() !== $('#cpassword').val()) {
            showErr('<?php _e("Passwords do not match. Please try again.", "download-manager"); ?>');
            return false;
        }

        // Validate password length
        if ($('#password').val().length < 6) {
            showErr('<?php _e("Password must be at least 6 characters long.", "download-manager"); ?>');
            return false;
        }

        $btn.html('<span class="wpdm-auth-spinner"></span><span><?php _e("Updating...", "download-manager"); ?></span>').attr('disabled', 'disabled');

        $(this).ajaxSubmit({
            success: function (res) {
                if (res.success) {
                    $('#updatePassword').html(
                        '<div class="wpdm-auth-alert success">' +
                        '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>' +
                        '<div><strong><?php _e("Password Updated!", "download-manager"); ?></strong><br/><?php _e("Your password has been changed successfully.", "download-manager"); ?></div>' +
                        '</div>' +
                        '<a href="<?php echo wpdm_login_url(); ?>" class="wpdm-auth-btn">' +
                        '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/></svg>' +
                        '<span><?php _e("Go to Login", "download-manager"); ?></span>' +
                        '</a>'
                    );
                } else {
                    showErr(res.message || '<?php _e("Failed to update password. Please try again.", "download-manager"); ?>');
                    $btn.html(btnHtml).removeAttr('disabled');
                }
            },
            error: function() {
                showErr('<?php _e("An error occurred. Please try again.", "download-manager"); ?>');
                $btn.html(btnHtml).removeAttr('disabled');
            }
        });
        return false;
    });

    function showErr(msg) {
        $('#updatePassword .wpdm-auth-alert.error').remove();
        var html = '<div class="wpdm-auth-alert error">' +
            '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>' +
            '<span>' + msg + '</span></div>';
        $('#updatePassword').prepend(html);
    }

    $('body').on('click', '.wpdm-auth-alert.error', function(){
        $(this).slideUp(150, function() { $(this).remove(); });
    });
});
</script>

<?php else: ?>

<!-- Error State - Invalid/Expired Link -->
<div class="w3eden wpdm-auth-page" id="wpdmloginpage">
    <div class="wpdm-auth-split" id="wpdmlogin">
        <div class="wpdm-auth-panel wpdm-auth-panel--short">
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
                    <?php if($logo){ ?>
                        <img src="<?php echo esc_attr($logo); ?>" alt="<?php echo esc_attr($site_name); ?>" />
                    <?php } else { ?>
                        <div class="wpdm-auth-brand-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                        </div>
                    <?php } ?>
                    <span><?php echo esc_html($site_name); ?></span>
                </div>

                <div class="wpdm-auth-welcome">
                    <div class="wpdm-auth-welcome-sub"><?php _e("Something went wrong", "download-manager"); ?></div>
                    <div class="wpdm-auth-welcome-title"><?php _e("LINK EXPIRED", "download-manager"); ?></div>
                    <div class="wpdm-auth-welcome-line"></div>
                    <div class="wpdm-auth-welcome-text">
                        <?php _e("Password reset links are valid for a limited time for security reasons.", "download-manager"); ?>
                    </div>
                </div>
            </div>

            <!-- Right Panel - Error Message -->
            <div class="wpdm-auth-right">
                <div class="wpdm-auth-form-header">
                    <h1 class="wpdm-auth-form-title" style="color: #dc2626;"><?php _e("Invalid Link", "download-manager"); ?></h1>
                    <p class="wpdm-auth-form-desc"><?php _e("This password reset link is invalid or has expired.", "download-manager"); ?></p>
                </div>

                <div class="wpdm-auth-alert error" style="cursor: default;">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                    <span><?php echo esc_html($user->get_error_message()); ?></span>
                </div>

                <p style="font-size: 13px; color: #64748b; line-height: 1.6; margin-bottom: 24px;">
                    <?php _e("Don't worry! You can request a new password reset link. Just click the button below to start over.", "download-manager"); ?>
                </p>

                <a href="<?php echo WPDM()->user->login->lostPasswordURL(); ?>" class="wpdm-auth-btn">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    <span><?php _e("Request New Link", "download-manager"); ?></span>
                </a>

                <div class="wpdm-auth-link">
                    <?php _e("Remember your password?", "download-manager"); ?>
                    <a href="<?php echo wpdm_login_url(); ?>"><?php _e("Sign in", "download-manager"); ?></a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php endif; ?>
