<?php
/**
 * Lost Password Form Template - Split Panel Design
 * Modern two-column layout with decorative left panel
 */

if(!defined("ABSPATH")) die();
$logo = get_site_icon_url();
$site_name = get_bloginfo('name');

// Enqueue unified auth styles
wp_enqueue_style('wpdm-auth-forms', \WPDM\__\Template::locate_url('auth-forms.css', __DIR__), [], WPDM_VERSION);
?>

<div class="w3eden wpdm-auth-page" id="wpdmlostpasspage">
    <div class="wpdm-auth-split">
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
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4"/><path d="M12 8h.01"/></svg>
                        </div>
                    <?php } ?>
                    <span><?php echo esc_html($site_name); ?></span>
                </div>

                <div class="wpdm-auth-welcome">
                    <div class="wpdm-auth-welcome-sub"><?php _e("Forgot your password?", "download-manager"); ?></div>
                    <div class="wpdm-auth-welcome-title"><?php _e("RESET PASSWORD", "download-manager"); ?></div>
                    <div class="wpdm-auth-welcome-line"></div>
                    <div class="wpdm-auth-welcome-text">
                        <?php _e("No worries! Enter your email address and we'll send you a link to reset your password.", "download-manager"); ?>
                    </div>
                </div>
            </div>

            <!-- Right Panel - Form -->
            <div class="wpdm-auth-right">
                <div class="wpdm-auth-form-header">
                    <h1 class="wpdm-auth-form-title"><?php _e("Reset Password", "download-manager"); ?></h1>
                    <p class="wpdm-auth-form-desc"><?php _e("Enter your email address below and we'll send you instructions to reset your password.", "download-manager"); ?></p>
                </div>

                <form name="resetPassword" id="resetPassword" action="<?php echo admin_url('/admin-ajax.php?action=resetPassword'); ?>" method="post">
                    <?php wp_nonce_field(NONCE_KEY, '__wpdm_reset_pass'); ?>

                    <div id="__reset_msg"></div>

                    <div class="wpdm-auth-field">
                        <div class="wpdm-auth-input-wrap">
                            <input type="text" name="user_login" id="user_login" class="wpdm-auth-input" required placeholder="<?php _e('Enter your email address', "download-manager"); ?>" autocomplete="username" />
                        </div>
                    </div>

                    <button type="submit" name="wp-submit" id="resetPassword-submit" class="wpdm-auth-btn">
                        <span><?php _e("Send Reset Link", "download-manager"); ?></span>
                    </button>

                    <div class="wpdm-auth-back">
                        <a href="<?php the_permalink(); ?>">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m15 18-6-6 6-6"/></svg>
                            <span><?php _e("Back to sign in", "download-manager"); ?></span>
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
jQuery(function ($) {
    var $btn = $('#resetPassword-submit');
    var btnHtml = $btn.html();
    var sent = 0;

    $('#resetPassword').submit(function (e) {
        e.preventDefault();
        if (sent === 1) return false;

        $btn.html('<span class="wpdm-auth-spinner"></span><span><?php _e("Sending...", "download-manager"); ?></span>').attr('disabled', 'disabled');

        $(this).ajaxSubmit({
            success: function (res) {
                if (res.match(/error/)) {
                    showMsg('error', '<?php _e("Account not found. Please check your email address.", "download-manager"); ?>');
                    $btn.html(btnHtml).removeAttr('disabled');
                } else {
                    sent = 1;
                    showMsg('success', '<?php _e("Reset link sent! Check your inbox.", "download-manager"); ?>');
                    $btn.html('<svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg><span><?php _e("Link Sent!", "download-manager"); ?></span>');
                }
            },
            error: function() {
                showMsg('error', '<?php _e("Something went wrong. Please try again.", "download-manager"); ?>');
                $btn.html(btnHtml).removeAttr('disabled');
            }
        });
        return false;
    });

    function showMsg(type, msg) {
        var icon = type === 'success'
            ? '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>'
            : '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>';
        $('#__reset_msg').html('<div class="wpdm-auth-alert ' + type + '">' + icon + '<span>' + msg + '</span></div>');
    }

    $('body').on('click', '.wpdm-auth-alert.error', function() {
        $(this).slideUp(150, function() { $(this).remove(); });
    });
});
</script>
