<?php use WPDM\__\__;
use WPDM\__\Session;

if(!defined('ABSPATH')) die();

wp_enqueue_style('wpdm-auth-forms', \WPDM\__\Template::locate_url('auth-forms.css', __DIR__), [], WPDM_VERSION);

$site_icon = get_site_icon_url();
$site_name = get_bloginfo('name');
?>
<div class="w3eden">

<!-- Modal Login Form -->
<div class="wpdm-login-modal" id="wpdmloginmodal" role="dialog" aria-labelledby="wpdmloginmodal-title" aria-hidden="true">
    <div class="wpdm-login-modal__backdrop"></div>
    <div class="wpdm-login-modal__container">
        <div class="wpdm-auth-split wpdm-login-modal__split">
            <div class="wpdm-auth-panel wpdm-login-modal__panel">

                <!-- Left decorative panel -->
                <div class="wpdm-auth-left">
                    <div class="wpdm-auth-grid"></div>
                    <div class="wpdm-auth-circles">
                        <div class="wpdm-auth-circle"></div>
                        <div class="wpdm-auth-circle"></div>
                        <div class="wpdm-auth-circle"></div>
                        <div class="wpdm-auth-circle"></div>
                    </div>
                    <div class="wpdm-auth-brand">
                        <a href="<?php echo esc_url(home_url('/')); ?>">
                            <?php if ($site_icon): ?>
                                <img src="<?php echo esc_url($site_icon); ?>" alt="<?php echo esc_attr($site_name); ?>" id="wpdm_modal_login_logo" />
                            <?php else: ?>
                                <div class="wpdm-auth-brand-icon" id="wpdm_modal_login_logo">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" /></svg>
                                </div>
                            <?php endif; ?>
                            <?php echo esc_html($site_name); ?>
                        </a>
                    </div>
                    <div class="wpdm-auth-welcome">
                        <div class="wpdm-auth-welcome-sub"><?php _e('Welcome back', 'download-manager'); ?></div>
                        <div class="wpdm-auth-welcome-title"><?php _e('Sign In', 'download-manager'); ?></div>
                        <div class="wpdm-auth-welcome-line"></div>
                        <div class="wpdm-auth-welcome-text"><?php _e('Sign in to access your downloads, manage your account, and continue where you left off.', 'download-manager'); ?></div>
                    </div>
                </div>

                <!-- Right form panel -->
                <div class="wpdm-auth-right">

                    <!-- Close button -->
                    <button type="button" class="wpdm-login-modal__close" id="wpdm-modal-close" aria-label="<?php esc_attr_e('Close', 'download-manager'); ?>">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" /></svg>
                    </button>

                    <div class="wpdm-auth-form-header">
                        <div class="wpdm-auth-form-title" id="wpdmloginmodal-title"><?php _e('Login Account', 'download-manager'); ?></div>
                        <div class="wpdm-auth-form-desc"><?php _e('Enter your credentials to access your account', 'download-manager'); ?></div>
                    </div>

                    <?php do_action("wpdm_before_login_form"); ?>

                    <form name="loginform" id="modalloginform" action="" method="post" class="login-form">

                        <input type="hidden" name="permalink" value="<?php the_permalink(); ?>" />

                        <?php echo WPDM()->user->login->formFields(); ?>

                        <?php if(isset($params['note_after'])) { ?>
                            <div class="wpdm-auth-alert info">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z" /></svg>
                                <?php echo esc_html($params['note_after']); ?>
                            </div>
                        <?php } ?>

                        <?php do_action("wpdm_login_form"); ?>
                        <?php do_action("login_form"); ?>

                        <div class="wpdm-auth-meta">
                            <label class="wpdm-auth-remember">
                                <input name="rememberme" type="checkbox" id="rememberme" value="forever" />
                                <span class="wpdm-auth-check">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" /></svg>
                                </span>
                                <?php _e('Remember Me', 'download-manager'); ?>
                            </label>
                            <a class="wpdm-auth-forgot" href="<?php echo esc_url(wpdm_lostpassword_url()); ?>"><?php _e('Forgot Password?', 'download-manager'); ?></a>
                        </div>

                        <input type="hidden" name="redirect_to" id="wpdm_modal_login_redirect_to" value="<?php echo esc_url(__::valueof($_SERVER, 'REQUEST_URI', ['validate' => 'escs'])); ?>" />

                        <button type="submit" name="wp-submit" id="wpdmloginmodal-submit" class="wpdm-auth-btn">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9" /></svg>
                            <?php _e('Sign In', 'download-manager'); ?>
                        </button>

                    </form>

                    <?php do_action("wpdm_after_login_form"); ?>

                    <?php
                    $__wpdm_social_login = get_option('__wpdm_social_login');
                    $__wpdm_social_login = is_array($__wpdm_social_login) ? $__wpdm_social_login : array();
                    if(is_array($__wpdm_social_login) && count($__wpdm_social_login) > 1) { ?>

                        <div class="wpdm-auth-divider"><?php _e('or continue with', 'download-manager'); ?></div>

                        <div class="wpdm-auth-social">
                            <?php if(isset($__wpdm_social_login['google'])){ ?>
                                <button type="button" onclick="return _PopupCenter('<?php echo esc_url(home_url('/?sociallogin=google')); ?>', 'Google', 400,400);" title="Google" class="wpdm-auth-social-btn google">
                                    <svg viewBox="0 0 24 24"><path fill="currentColor" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92a5.06 5.06 0 0 1-2.2 3.32v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.1z"/><path fill="currentColor" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/><path fill="currentColor" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/><path fill="currentColor" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/></svg>
                                </button>
                            <?php } ?>
                            <?php if(isset($__wpdm_social_login['facebook'])){ ?>
                                <button type="button" onclick="return _PopupCenter('<?php echo esc_url(home_url('/?sociallogin=facebook')); ?>', 'Facebook', 400,400);" title="Facebook" class="wpdm-auth-social-btn facebook">
                                    <svg viewBox="0 0 24 24"><path fill="currentColor" d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                                </button>
                            <?php } ?>
                            <?php if(isset($__wpdm_social_login['twitter'])){ ?>
                                <button type="button" onclick="return _PopupCenter('<?php echo esc_url(home_url('/?sociallogin=twitter')); ?>', 'Twitter', 400,400);" title="X" class="wpdm-auth-social-btn twitter">
                                    <svg viewBox="0 0 24 24"><path fill="currentColor" d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                                </button>
                            <?php } ?>
                            <?php if(isset($__wpdm_social_login['linkedin'])){ ?>
                                <button type="button" onclick="return _PopupCenter('<?php echo esc_url(home_url('/?sociallogin=linkedin')); ?>', 'LinkedIn', 400,400);" title="LinkedIn" class="wpdm-auth-social-btn linkedin">
                                    <svg viewBox="0 0 24 24"><path fill="currentColor" d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
                                </button>
                            <?php } ?>
                        </div>

                    <?php } ?>

                    <?php if(isset($regurl) && $regurl != ''){ ?>
                        <div class="wpdm-auth-link">
                            <?php _e("Don't have an account?", 'download-manager'); ?>
                            <a href="<?php echo esc_url($regurl); ?>"><?php _e('Register Now', 'download-manager'); ?></a>
                        </div>
                    <?php } ?>

                </div>

            </div>
        </div>
    </div>
</div>

</div>

<style>
    /* Modal overlay & container */
    .wpdm-login-modal {
        display: none;
        position: fixed;
        inset: 0;
        z-index: 999999;
        overflow-y: auto;
        -webkit-overflow-scrolling: touch;
    }

    .wpdm-login-modal--open {
        display: block;
    }

    .wpdm-login-modal__backdrop {
        position: fixed;
        inset: 0;
        background: rgba(15, 23, 42, 0.6);
        backdrop-filter: blur(4px);
        -webkit-backdrop-filter: blur(4px);
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .wpdm-login-modal--open .wpdm-login-modal__backdrop {
        opacity: 1;
    }

    .wpdm-login-modal__container {
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 100%;
        padding: 24px;
    }

    .wpdm-login-modal__split {
        position: relative;
        z-index: 1;
        padding: 0;
        min-height: auto;
        opacity: 0;
        transform: scale(0.95) translateY(10px);
        transition: opacity 0.3s ease, transform 0.3s ease;
    }

    .wpdm-login-modal--open .wpdm-login-modal__split {
        opacity: 1;
        transform: scale(1) translateY(0);
    }

    .wpdm-login-modal__panel {
        max-width: 820px;
        min-height: 480px;
    }

    /* Close button */
    .wpdm-login-modal__close {
        position: absolute;
        top: 12px;
        right: 12px;
        width: 32px;
        height: 32px;
        border-radius: 8px;
        border: 1px solid var(--auth-border, #e2e8f0);
        background: var(--auth-bg, #ffffff);
        color: var(--auth-text, #1e293b);
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.15s ease;
        z-index: 2;
        padding: 0;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
    }

    .wpdm-login-modal__close svg {
        width: 16px;
        height: 16px;
    }

    .wpdm-login-modal__close:hover {
        background: var(--auth-input-bg, #f1f5f9);
        border-color: var(--auth-text-muted, #94a3b8);
    }

    /* Body scroll lock */
    body.wpdm-modal-open {
        overflow: hidden;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .wpdm-login-modal__container {
            padding: 16px;
            align-items: flex-start;
            padding-top: 40px;
        }

        .wpdm-login-modal__panel {
            max-width: 100%;
            min-height: auto;
        }
    }

    /* Dark mode */
    .w3eden.dark-mode .wpdm-login-modal__close {
        background: #1e293b;
        border-color: #475569;
        color: #f1f5f9;
    }

    .w3eden.dark-mode .wpdm-login-modal__close:hover {
        background: #334155;
        border-color: #64748b;
    }

    .w3eden.dark-mode .wpdm-login-modal__backdrop {
        background: rgba(0, 0, 0, 0.7);
    }

    @media (prefers-color-scheme: dark) {
        .w3eden:not(.light-mode) .wpdm-login-modal__close {
            background: #1e293b;
            border-color: #475569;
            color: #f1f5f9;
        }

        .w3eden:not(.light-mode) .wpdm-login-modal__close:hover {
            background: #334155;
            border-color: #64748b;
        }

        .w3eden:not(.light-mode) .wpdm-login-modal__backdrop {
            background: rgba(0, 0, 0, 0.7);
        }
    }
</style>

<script>
    jQuery(function ($) {
        var llbl = $('#wpdmloginmodal-submit').html();
        var __lm_redirect_to = location.href;
        var __lm_logo = "<?php echo esc_js(get_site_icon_url()); ?>";
        var $body = $('body');
        var $modal = $('#wpdmloginmodal');

        function openModal() {
            $modal.addClass('wpdm-login-modal--open');
            $body.addClass('wpdm-modal-open');
            setTimeout(function() {
                $('#user_login').trigger('focus');
            }, 300);
        }

        function closeModal() {
            $modal.removeClass('wpdm-login-modal--open');
            $body.removeClass('wpdm-modal-open');
        }

        // Form submit
        $('#modalloginform').submit(function () {
            var $btn = $('#wpdmloginmodal-submit');
            $btn.html('<span class="wpdm-auth-spinner"></span> <?php _e("Signing In...", "download-manager"); ?>');
            $btn.prop('disabled', true);
            $(this).ajaxSubmit({
                error: function(error) {
                    var msg = error.responseJSON ? error.responseJSON.messages : '<?php _e("Login failed. Please try again.", "download-manager"); ?>';
                    $('#modalloginform .wpdm-auth-alert').remove();
                    $('#modalloginform').prepend('<div class="wpdm-auth-alert error"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" /></svg>' + msg + '</div>');
                    $btn.html(llbl).prop('disabled', false);
                    <?php if((int)get_option('__wpdm_recaptcha_loginform', 0) === 1 && get_option('_wpdm_recaptcha_site_key') != ''){ ?>
                    try { grecaptcha.reset(); } catch (e) {}
                    <?php } ?>
                },
                success: function (res) {
                    if (!res.success) {
                        $('#modalloginform .wpdm-auth-alert').remove();
                        $('#modalloginform').prepend('<div class="wpdm-auth-alert error"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" /></svg>' + res.message + '</div>');
                        $btn.html(llbl).prop('disabled', false);
                        <?php if((int)get_option('__wpdm_recaptcha_loginform', 0) === 1 && get_option('_wpdm_recaptcha_site_key') != ''){ ?>
                        try { grecaptcha.reset(); } catch (e) {}
                        <?php } ?>
                    } else {
                        $btn.html('<span class="wpdm-auth-spinner"></span> ' + res.message);
                        location.href = __lm_redirect_to;
                    }
                }
            });
            return false;
        });

        // Dismiss alerts on click
        $body.on('click', '#modalloginform .wpdm-auth-alert', function(){
            $(this).slideUp(200, function() { $(this).remove(); });
        });

        // Open modal triggers (data-target and class-based)
        $body.on('click', 'a[data-target="#wpdmloginmodal"], .wpdmloginmodal-trigger', function (e) {
            e.preventDefault();
            if($(this).data('redirect') !== undefined) {
                __lm_redirect_to = $(this).data('redirect');
            }
            if($(this).data('logo') !== undefined) {
                __lm_logo = $(this).data('logo');
            }
            if(__lm_logo !== "") {
                var $logo = $('#wpdm_modal_login_logo');
                if ($logo.is('img')) {
                    $logo.attr('src', __lm_logo);
                } else {
                    $logo.replaceWith(WPDM.el('img', {src: __lm_logo, alt: "Logo", id: "wpdm_modal_login_logo", style: "width:36px;height:36px;border-radius:8px;object-fit:cover"}));
                }
            }
            openModal();
        });

        // Close modal
        $body.on('click', '#wpdm-modal-close, .wpdm-login-modal__backdrop', function () {
            closeModal();
        });

        // Close on Escape key
        $(document).on('keydown', function(e) {
            if (e.key === 'Escape' && $modal.hasClass('wpdm-login-modal--open')) {
                closeModal();
            }
        });

        // Ctrl+L shortcut to open
        $(window).keydown(function(event) {
            if(event.ctrlKey && event.keyCode === 76) {
                openModal();
                event.preventDefault();
            }
        });

        // Backwards compatibility: support .modal('show') calls
        $modal.modal = function(action) {
            if (action === 'show') openModal();
            else if (action === 'hide') closeModal();
        };
    });
</script>
