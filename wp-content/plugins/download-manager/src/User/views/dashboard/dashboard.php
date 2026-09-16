<?php
/**
 * User Dashboard - Main Layout
 * Slack-style modern interface
 */

global $current_user;
$store = get_user_meta(get_current_user_id(), '__wpdm_public_profile', true);
$avatar_url = isset($store['logo']) && $store['logo'] != '' ? $store['logo'] : get_avatar_url($current_user->user_email, ['size' => 256]);
?>

<div class="w3eden wpdm-dashboard">
    <div class="wpdm-dashboard-frame">
        <!-- Sidebar -->
        <aside class="wpdm-sidebar" id="wpdm-sidebar">
            <!-- User Profile (Top) -->
            <div class="wpdm-sidebar-header">
                <div class="wpdm-user-profile">
                    <div class="wpdm-user-avatar">
                        <img src="<?php echo esc_url($avatar_url); ?>" alt="<?php echo esc_attr($current_user->display_name); ?>">
                        <span class="wpdm-user-status"></span>
                    </div>
                    <div class="wpdm-user-info">
                        <span class="wpdm-user-name"><?php echo esc_html($current_user->display_name); ?></span>
                        <span class="wpdm-user-role"><?php echo esc_html(ucfirst($current_user->roles[0] ?? 'User')); ?></span>
                    </div>
                </div>
            </div>

            <!-- Navigation -->
            <nav class="wpdm-sidebar-nav">
                <?php
                if (is_array($this->dashboard_menu)) {
                    foreach ($this->dashboard_menu as $section_id => $section) {
                        if (isset($section['title']) && $section['title'] != '') {
                            echo '<div class="wpdm-nav-group-title">' . esc_html($section['title']) . '</div>';
                        }
                        echo '<div class="wpdm-nav-group">';
                        foreach ($section['items'] as $page_id => $menu_item) {
                            $menu_url = get_permalink(get_the_ID()) . ($page_id != '' ? '?udb_page=' . $page_id : '');
                            if (isset($params['flaturl']) && $params['flaturl'] == 1) {
                                $menu_url = get_permalink(get_the_ID()) . $page_id . ($page_id != '' ? '/' : '');
                            }
                            $is_active = ($udb_page == $page_id) ? 'active' : '';
                            $icon = isset($menu_item['icon']) ? $menu_item['icon'] : (isset($default_icons[$page_id]) ? $default_icons[$page_id] : 'fas fa-circle');
                            ?>
                            <a class="wpdm-nav-link <?php echo $is_active; ?>" href="<?php echo esc_url($menu_url); ?>">
                                <i class="<?php echo esc_attr($icon); ?>"></i>
                                <span><?php echo esc_html($menu_item['name']); ?></span>
                            </a>
                            <?php
                        }
                        echo '</div>';
                    }
                }
                ?>
            </nav>

            <?php do_action("wpdm_user_dashboard_sidebar"); ?>

            <!-- Logout (Bottom) -->
            <div class="wpdm-sidebar-footer">
                <a class="wpdm-logout-link" href="<?php echo esc_url(wpdm_logout_url()); ?>">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
                    <span><?php esc_html_e('Logout', 'download-manager'); ?></span>
                </a>
            </div>
        </aside>

        <!-- Mobile Menu Toggle -->
        <button type="button" class="wpdm-mobile-toggle" id="wpdm-mobile-toggle">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="3" y1="12" x2="21" y2="12"></line><line x1="3" y1="6" x2="21" y2="6"></line><line x1="3" y1="18" x2="21" y2="18"></line></svg>
        </button>

        <!-- Main Content -->
        <main class="wpdm-main">
            <div class="wpdm-main-inner">
                <?php echo isset($dashboard_contents) ? $dashboard_contents : ''; ?>
            </div>
        </main>
    </div>

    <!-- Mobile Overlay -->
    <div class="wpdm-overlay" id="wpdm-overlay"></div>
</div>

<script>
jQuery(function($) {
    var $sidebar = $('#wpdm-sidebar');
    var $overlay = $('#wpdm-overlay');
    var $toggle = $('#wpdm-mobile-toggle');

    $toggle.on('click', function() {
        $sidebar.addClass('open');
        $overlay.addClass('visible');
        $('body').css('overflow', 'hidden');
    });

    $overlay.on('click', function() {
        $sidebar.removeClass('open');
        $overlay.removeClass('visible');
        $('body').css('overflow', '');
    });
});
</script>
