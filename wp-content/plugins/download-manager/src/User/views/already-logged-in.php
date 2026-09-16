<?php
/**
 * Already Logged In View
 *
 * Displayed when a logged-in user visits the login page.
 *
 * @package WPDM
 */

if (!defined('ABSPATH')) die();

// Get color scheme to prevent flickering
$color_scheme = get_option('__wpdm_color_scheme', 'system');
$color_class = '';
if ($color_scheme === 'dark') {
    $color_class = 'dark-mode';
} elseif ($color_scheme === 'light') {
    $color_class = 'light-mode';
}

// Get user avatar URL
$avatar_url = get_avatar_url(get_current_user_id(), ['size' => 200, 'default' => 'mp']);

// Enqueue auth styles for consistency
wp_enqueue_style('wpdm-auth-forms', \WPDM\__\Template::locate_url('auth-forms.css', __DIR__), [], WPDM_VERSION);
?>

<div class="w3eden wpdm-auth-page <?php echo esc_attr($color_class); ?>" id="wpdm-already-logged-in">
    <div class="wpdm-auth-split">
        <div class="wpdm-auth-panel wpdm-auth-panel--centered">
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
                    <?php $logo = get_site_icon_url(); if($logo){ ?>
                        <img src="<?php echo esc_attr($logo); ?>" alt="<?php echo esc_attr(get_bloginfo('name')); ?>" />
                    <?php } else { ?>
                        <div class="wpdm-auth-brand-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4"/><path d="M12 8h.01"/></svg>
                        </div>
                    <?php } ?>
                    <span><?php echo esc_html(get_bloginfo('name')); ?></span>
                    </a>
                </div>

                <div class="wpdm-auth-welcome">
                    <div class="wpdm-auth-welcome-sub"><?php _e("You're signed in", "download-manager"); ?></div>
                    <div class="wpdm-auth-welcome-title"><?php _e("WELCOME BACK", "download-manager"); ?></div>
                    <div class="wpdm-auth-welcome-line"></div>
                    <div class="wpdm-auth-welcome-text">
                        <?php _e("You're already logged in. Visit your dashboard to manage downloads or logout to switch accounts.", "download-manager"); ?>
                    </div>
                </div>
            </div>

            <!-- Right Panel - Content -->
            <div class="wpdm-auth-right">
                <div class="wpdm-logged-in-content">
                    <!-- Avatar with success ring -->
                    <div class="wpdm-logged-in-avatar-wrap">
                        <div class="wpdm-logged-in-avatar">
                            <img src="<?php echo esc_url($avatar_url); ?>" alt="<?php echo esc_attr($current_user->display_name); ?>" />
                            <span class="wpdm-avatar-check">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="20 6 9 17 4 12"></polyline>
                                </svg>
                            </span>
                        </div>
                    </div>

                    <!-- User info -->
                    <div class="wpdm-logged-in-info">
                        <h3 class="wpdm-logged-in-name"><?php echo esc_html($current_user->display_name); ?></h3>
                        <p class="wpdm-logged-in-email"><?php echo esc_html($current_user->user_email); ?></p>
                    </div>

                    <!-- Action buttons -->
                    <div class="wpdm-logged-in-actions">
                        <a href="<?php echo esc_url(wpdm_user_dashboard_url()); ?>" class="wpdm-auth-btn">
                            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="3" width="7" height="9"></rect>
                                <rect x="14" y="3" width="7" height="5"></rect>
                                <rect x="14" y="12" width="7" height="9"></rect>
                                <rect x="3" y="16" width="7" height="5"></rect>
                            </svg>
                            <span><?php _e("Go to Dashboard", "download-manager"); ?></span>
                        </a>
                    </div>

                    <div class="wpdm-logged-in-footer">
                        <span><?php _e("Not you?", "download-manager"); ?></span>
                        <a href="<?php echo esc_url(wpdm_logout_url()); ?>" class="wpdm-logout-link">
                            <?php _e("Sign out", "download-manager"); ?>
                            <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                                <polyline points="16 17 21 12 16 7"></polyline>
                                <line x1="21" y1="12" x2="9" y2="12"></line>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Already Logged In - Uses auth-forms.css base styles */

    .wpdm-auth-panel--centered {
        min-height: auto;
    }

    .wpdm-logged-in-content {
        text-align: center;
        padding: 40px 0 20px;
    }

    /* Avatar wrapper with glow effect */
    .wpdm-logged-in-avatar-wrap {
        display: flex;
        justify-content: center;
        margin-bottom: 24px !important;
    }

    .wpdm-logged-in-avatar {
        position: relative;
        width: 120px;
        height: 120px;
    }

    .wpdm-logged-in-avatar::before {
        content: '';
        position: absolute;
        inset: -4px;
        background: linear-gradient(135deg, #10b981 0%, #6366f1 50%, #8b5cf6 100%);
        border-radius: 50%;
        animation: wpdm-gradient-spin 3s linear infinite;
        width: calc(100% + 8px);
        height: calc(100% + 8px);
    }

    @keyframes wpdm-gradient-spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    .wpdm-logged-in-avatar::after {
        content: '';
        position: absolute;
        inset: 0;
        background: var(--wpdm-auth-card-bg, #fff);
        border-radius: 50%;
    }

    .wpdm-logged-in-avatar img {
        position: relative;
        z-index: 1;
        width: 100%;
        height: 100%;
        border-radius: 50%;
        object-fit: cover;
        border: 4px solid var(--wpdm-auth-card-bg, #fff);
    }

    /* Success check badge */
    .wpdm-avatar-check {
        position: absolute;
        z-index: 2;
        bottom: 0;
        right: 0;
        width: 32px;
        height: 32px;
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        border-radius: 50%;
        border: 3px solid var(--wpdm-auth-card-bg, #fff);
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.4);
    }

    .wpdm-avatar-check svg {
        width: 14px;
        height: 14px;
        color: #fff;
    }

    /* User info */
    .wpdm-logged-in-info {
        margin-bottom: 32px !important;
    }

    .wpdm-logged-in-name {
        margin: 0 0 8px 0 !important;
        font-size: 26px;
        font-weight: 700;
        color: var(--wpdm-auth-text, #1e293b);
        line-height: 1.3;
    }

    .wpdm-logged-in-email {
        margin: 0;
        font-size: 14px;
        color: var(--wpdm-auth-text-muted, #64748b);
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 14px;
        background: var(--wpdm-auth-bg, #f8fafc);
        border-radius: 20px;
    }

    /* Action buttons */
    .wpdm-logged-in-actions {
        margin-bottom: 28px !important;
    }

    .wpdm-logged-in-actions .wpdm-auth-btn {
        display: inline-flex;
        gap: 10px;
        padding: 14px 32px;
        font-size: 15px;
    }

    /* Footer with logout link */
    .wpdm-logged-in-footer {
        padding-top: 24px !important;
        border-top: 1px solid var(--wpdm-auth-border, #e2e8f0);
        font-size: 14px;
        color: var(--wpdm-auth-text-muted, #64748b);
    }

    .wpdm-logout-link {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        margin-left: 8px;
        color: var(--wpdm-auth-text-muted, #64748b);
        font-weight: 500;
        text-decoration: none;
        transition: all 0.2s ease;
    }

    .wpdm-logout-link:hover {
        color: #ef4444;
    }

    /* Dark mode overrides */
    .wpdm-auth-page.dark-mode .wpdm-logged-in-avatar::after,
    .wpdm-auth-page.dark-mode .wpdm-logged-in-avatar img,
    .wpdm-auth-page.dark-mode .wpdm-avatar-check {
        border-color: var(--wpdm-auth-card-bg, #1e293b);
    }

    .wpdm-auth-page.dark-mode .wpdm-logged-in-email {
        background: rgba(255, 255, 255, 0.05);
    }

    @media (prefers-color-scheme: dark) {
        .wpdm-auth-page:not(.light-mode) .wpdm-logged-in-avatar::after,
        .wpdm-auth-page:not(.light-mode) .wpdm-logged-in-avatar img,
        .wpdm-auth-page:not(.light-mode) .wpdm-avatar-check {
            border-color: var(--wpdm-auth-card-bg, #1e293b);
        }

        .wpdm-auth-page:not(.light-mode) .wpdm-logged-in-email {
            background: rgba(255, 255, 255, 0.05);
        }
    }

    /* Responsive */
    @media (max-width: 480px) {
        .wpdm-logged-in-content {
            padding: 30px 0 10px;
        }

        .wpdm-logged-in-avatar {
            width: 100px;
            height: 100px;
        }

        .wpdm-avatar-check {
            width: 28px;
            height: 28px;
        }

        .wpdm-avatar-check svg {
            width: 12px;
            height: 12px;
        }

        .wpdm-logged-in-name {
            font-size: 22px;
        }

        .wpdm-logged-in-actions .wpdm-auth-btn {
            width: 100%;
            justify-content: center;
        }
    }
</style>
