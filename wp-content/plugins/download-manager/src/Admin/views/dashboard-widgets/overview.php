<?php
/**
 * User: shahnuralam
 * Date: 5/7/17
 * Time: 2:19 AM
 */
global $wpdb;
if(!defined('ABSPATH')) die('!');

// Cache key based on current hour (refreshes every hour)
$cache_key = 'wpdm_overview_' . wp_date('Y-m-d-H');
$overview_data = get_transient($cache_key);

if (!$overview_data) {
    // Gather stats with single optimized query where possible
    $packs = wp_count_posts('wpdmpro');
    $total_packages = $packs->publish;
    $total_downloads = (int) $wpdb->get_var("SELECT SUM(meta_value) FROM {$wpdb->prefix}postmeta WHERE meta_key='__wpdm_download_count'");
    $total_categories = wp_count_terms('wpdmcategory');
    $total_subscribers = (int) $wpdb->get_var("SELECT COUNT(DISTINCT email) FROM {$wpdb->prefix}ahm_emails");

    $today_start = strtotime(wp_date("Y-m-d 0:0:0"));
    $today_end = time();
    $subscribed_today = (int) $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(DISTINCT email) FROM {$wpdb->prefix}ahm_emails WHERE date > %d AND date < %d",
        $today_start,
        $today_end
    ));

    // Use year, month, day columns (same as download-trends.php)
    $downloads_today = (int) $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(*) FROM {$wpdb->prefix}ahm_download_stats WHERE year = %d AND month = %d AND day = %d",
        wp_date('Y'),
        wp_date('n'),  // 'n' returns month without leading zero (1-12)
        wp_date('j')   // 'j' returns day without leading zero (1-31)
    ));

    $overview_data = [
        'total_packages' => $total_packages,
        'total_downloads' => $total_downloads,
        'total_categories' => $total_categories,
        'total_subscribers' => $total_subscribers,
        'subscribed_today' => $subscribed_today,
        'downloads_today' => $downloads_today,
    ];

    // Cache for 30 minutes
    set_transient($cache_key, $overview_data, 30 * MINUTE_IN_SECONDS);
}

extract($overview_data);
?>
<style>
    .wpdm-overview-widget {
        --wpdm-primary: #6366f1;
        --wpdm-primary-light: #eef2ff;
        --wpdm-success: #10b981;
        --wpdm-success-light: #ecfdf5;
        --wpdm-warning: #f59e0b;
        --wpdm-warning-light: #fffbeb;
        --wpdm-info: #0ea5e9;
        --wpdm-info-light: #f0f9ff;
        --wpdm-purple: #8b5cf6;
        --wpdm-purple-light: #f5f3ff;
        --wpdm-text: #1e293b;
        --wpdm-text-muted: #64748b;
        --wpdm-border: #e2e8f0;
        --wpdm-bg: #f8fafc;
        --wpdm-radius: 10px;
        --wpdm-shadow: 0 1px 3px 0 rgb(0 0 0 / 0.08);
        margin: -12px;
    }

    .wpdm-overview-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 12px;
        padding: 16px;
        background: var(--wpdm-bg);
    }

    .wpdm-stat-card {
        background: #fff;
        border-radius: var(--wpdm-radius);
        padding: 16px;
        display: flex;
        align-items: flex-start;
        gap: 12px;
        box-shadow: var(--wpdm-shadow);
        border: 1px solid var(--wpdm-border);
        transition: transform 150ms ease, box-shadow 150ms ease;
    }

    .wpdm-stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgb(0 0 0 / 0.1);
    }

    .wpdm-stat-icon {
        width: 44px;
        height: 44px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .wpdm-stat-icon svg {
        width: 22px;
        height: 22px;
    }

    .wpdm-stat-icon--primary {
        background: var(--wpdm-primary-light);
        color: var(--wpdm-primary);
    }

    .wpdm-stat-icon--success {
        background: var(--wpdm-success-light);
        color: var(--wpdm-success);
    }

    .wpdm-stat-icon--warning {
        background: var(--wpdm-warning-light);
        color: var(--wpdm-warning);
    }

    .wpdm-stat-icon--info {
        background: var(--wpdm-info-light);
        color: var(--wpdm-info);
    }

    .wpdm-stat-icon--purple {
        background: var(--wpdm-purple-light);
        color: var(--wpdm-purple);
    }

    .wpdm-stat-content {
        flex: 1;
        min-width: 0;
    }

    .wpdm-stat-value {
        font-size: 24px;
        font-weight: 700;
        color: var(--wpdm-text);
        line-height: 1.2;
        margin-bottom: 2px;
    }

    .wpdm-stat-label {
        font-size: 12px;
        color: var(--wpdm-text-muted);
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .wpdm-stat-badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        font-size: 11px;
        font-weight: 600;
        padding: 3px 8px;
        border-radius: 12px;
        margin-top: 6px;
    }

    .wpdm-stat-badge--success {
        background: var(--wpdm-success-light);
        color: var(--wpdm-success);
    }

    @media (max-width: 782px) {
        .wpdm-overview-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="wpdm-overview-widget">
    <div class="wpdm-overview-grid">

        <!-- Total Packages -->
        <div class="wpdm-stat-card">
            <div class="wpdm-stat-icon wpdm-stat-icon--primary">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m20.25 7.5-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z" />
                </svg>
            </div>
            <div class="wpdm-stat-content">
                <div class="wpdm-stat-value"><?php echo number_format($total_packages); ?></div>
                <div class="wpdm-stat-label"><?php _e("Packages", "download-manager"); ?></div>
            </div>
        </div>

        <!-- Total Downloads -->
        <div class="wpdm-stat-card">
            <div class="wpdm-stat-icon wpdm-stat-icon--success">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />
                </svg>
            </div>
            <div class="wpdm-stat-content">
                <div class="wpdm-stat-value"><?php echo number_format($total_downloads); ?></div>
                <div class="wpdm-stat-label"><?php _e("Downloads", "download-manager"); ?></div>
            </div>
        </div>

        <!-- Total Categories -->
        <div class="wpdm-stat-card">
            <div class="wpdm-stat-icon wpdm-stat-icon--warning">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12.75V12A2.25 2.25 0 0 1 4.5 9.75h15A2.25 2.25 0 0 1 21.75 12v.75m-8.69-6.44-2.12-2.12a1.5 1.5 0 0 0-1.061-.44H4.5A2.25 2.25 0 0 0 2.25 6v12a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9a2.25 2.25 0 0 0-2.25-2.25h-5.379a1.5 1.5 0 0 1-1.06-.44Z" />
                </svg>
            </div>
            <div class="wpdm-stat-content">
                <div class="wpdm-stat-value"><?php echo number_format($total_categories); ?></div>
                <div class="wpdm-stat-label"><?php _e("Categories", "download-manager"); ?></div>
            </div>
        </div>

        <!-- Total Subscribers -->
        <div class="wpdm-stat-card">
            <div class="wpdm-stat-icon wpdm-stat-icon--info">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                </svg>
            </div>
            <div class="wpdm-stat-content">
                <div class="wpdm-stat-value"><?php echo number_format($total_subscribers); ?></div>
                <div class="wpdm-stat-label"><?php _e("Subscribers", "download-manager"); ?></div>
            </div>
        </div>

        <!-- Downloads Today -->
        <div class="wpdm-stat-card">
            <div class="wpdm-stat-icon wpdm-stat-icon--success">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />
                </svg>
            </div>
            <div class="wpdm-stat-content">
                <div class="wpdm-stat-value"><?php echo number_format($downloads_today); ?></div>
                <div class="wpdm-stat-label"><?php _e("Downloads Today", "download-manager"); ?></div>
                <?php if ($downloads_today > 0): ?>
                <div class="wpdm-stat-badge wpdm-stat-badge--success">
                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="23 6 13.5 15.5 8.5 10.5 1 18"></polyline>
                        <polyline points="17 6 23 6 23 12"></polyline>
                    </svg>
                    <?php _e("Active", "download-manager"); ?>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Subscribed Today -->
        <div class="wpdm-stat-card">
            <div class="wpdm-stat-icon wpdm-stat-icon--purple">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg>
            </div>
            <div class="wpdm-stat-content">
                <div class="wpdm-stat-value"><?php echo number_format($subscribed_today); ?></div>
                <div class="wpdm-stat-label"><?php _e("Subscribed Today", "download-manager"); ?></div>
                <?php if ($subscribed_today > 0): ?>
                <div class="wpdm-stat-badge wpdm-stat-badge--success">
                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="23 6 13.5 15.5 8.5 10.5 1 18"></polyline>
                        <polyline points="17 6 23 6 23 12"></polyline>
                    </svg>
                    <?php _e("Active", "download-manager"); ?>
                </div>
                <?php endif; ?>
            </div>
        </div>

    </div>
</div>
