<?php
/**
 * User Dashboard - Download History
 * Enterprise-grade design
 */

use WPDM\__\__;

if (!defined('ABSPATH')) die();

global $wpdb, $current_user, $wp_query;

$items_per_page = 20;
$current_page = isset($_GET['pgd']) ? max(1, absint($_GET['pgd'])) : 1;
$start = ($current_page - 1) * $items_per_page;

// Get total count for pagination
$total_items = (int) $wpdb->get_var($wpdb->prepare(
    "SELECT COUNT(*) FROM {$wpdb->prefix}ahm_download_stats WHERE uid = %d",
    $current_user->ID
));

// Get paginated results
$downloads = $wpdb->get_results($wpdb->prepare(
    "SELECT p.post_title, s.* FROM {$wpdb->prefix}posts p, {$wpdb->prefix}ahm_download_stats s
     WHERE s.uid = %d AND s.pid = p.ID
     ORDER BY s.timestamp DESC
     LIMIT %d, %d",
    $current_user->ID,
    $start,
    $items_per_page
));

$total_pages = ceil($total_items / $items_per_page);
?>

<?php do_action("wpdm_before_download_history"); ?>

<?php if (class_exists('\WPDM\AddOn\DownloadLimit')): ?>
<!-- Download Limit Info -->
<div class="row mb-3">
    <div class="col-md-6 mb-2">
        <div class="card h-100">
            <div class="card-body py-3">
                <div class="media">
                    <div class="mr-3 text-info">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                    </div>
                    <div class="media-body">
                        <small class="text-muted d-block"><?php esc_html_e('Reset Timer', 'download-manager'); ?></small>
                        <strong><?php echo do_shortcode("[wpdm_download_limit_reset_timer]"); ?></strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6 mb-2">
        <div class="card h-100">
            <div class="card-body py-3">
                <div class="media">
                    <div class="mr-3 text-warning">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                    </div>
                    <div class="media-body">
                        <small class="text-muted d-block"><?php esc_html_e('Download Limit', 'download-manager'); ?></small>
                        <strong><?php echo do_shortcode("[wpdm_user_download_count]"); ?> / <?php echo do_shortcode("[wpdm_user_download_limit]"); ?></strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Download History Card -->
<div class="wpdm-card">
    <div class="wpdm-card-header">
        <h3>
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
            <?php esc_html_e('Download History', 'download-manager'); ?>
        </h3>
        <?php if ($total_items > 0): ?>
        <span class="wpdm-badge wpdm-badge--info"><?php echo number_format($total_items); ?> <?php esc_html_e('total', 'download-manager'); ?></span>
        <?php endif; ?>
    </div>

    <?php if (!empty($downloads)): ?>
    <div class="wpdm-table-wrap">
        <table class="wpdm-table">
            <thead>
                <tr>
                    <th><?php esc_html_e('Package / File', 'download-manager'); ?></th>
                    <th class="wpdm-hide-mobile"><?php esc_html_e('Download Time', 'download-manager'); ?></th>
                    <th class="wpdm-hide-mobile" style="width: 120px;"><?php esc_html_e('IP Address', 'download-manager'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($downloads as $stat): ?>
                <tr>
                    <td>
                        <div class="wpdm-product-cell">
                            <a class="wpdm-table-link wpdm-product-name" href="<?php echo esc_url(get_permalink($stat->pid)); ?>">
                                <?php echo esc_html($stat->post_title); ?>
                            </a>
                            <?php if (!empty($stat->filename)): ?>
                            <span class="wpdm-file-meta">
                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
                                <?php echo esc_html(__::mask($stat->filename, '...', -25, false) ?: 'Package'); ?>
                            </span>
                            <?php endif; ?>
                            <span class="wpdm-mobile-meta wpdm-show-mobile">
                                <?php echo date_i18n(get_option('date_format') . ' H:i', $stat->timestamp + __::timezoneOffset()); ?>
                            </span>
                        </div>
                    </td>
                    <td class="wpdm-hide-mobile">
                        <span class="wpdm-date"><?php echo date_i18n(get_option('date_format') . ' H:i', $stat->timestamp + __::timezoneOffset()); ?></span>
                    </td>
                    <td class="wpdm-hide-mobile">
                        <span class="wpdm-ip"><?php echo esc_html($stat->ip); ?></span>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <?php if ($total_pages > 1): ?>
    <div class="wpdm-card-footer">
        <div class="wpdm-pagination">
            <?php
            $pagination_args = array(
                'base' => add_query_arg('pgd', '%#%'),
                'format' => '',
                'total' => $total_pages,
                'current' => $current_page,
                'show_all' => false,
                'end_size' => 1,
                'mid_size' => 2,
                'prev_next' => true,
                'prev_text' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"></polyline></svg>',
                'next_text' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>',
                'type' => 'plain',
            );
            echo paginate_links($pagination_args);
            ?>
        </div>
    </div>
    <?php endif; ?>

    <?php else: ?>
    <div class="wpdm-card-body">
        <div class="wpdm-empty-state wpdm-empty-state--compact">
            <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
            <h4><?php esc_html_e('No download history', 'download-manager'); ?></h4>
            <p><?php esc_html_e("You haven't downloaded any files yet.", 'download-manager'); ?></p>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php do_action("wpdm_after_download_history"); ?>

<style>
.wpdm-file-meta {
    display: inline-flex;
    align-items: center;
    gap: 0.375rem;
    font-size: 0.8125rem;
    color: #64748b;
}
.wpdm-file-meta svg {
    color: #94a3b8;
}
.wpdm-mobile-meta {
    display: none;
    font-size: 0.75rem;
    color: #94a3b8;
    margin-top: 0.25rem;
}
@media (max-width: 768px) {
    .wpdm-show-mobile {
        display: block;
    }
}
.wpdm-pagination {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 0.25rem;
    flex-wrap: wrap;
}
.wpdm-pagination a,
.wpdm-pagination span {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 36px;
    height: 36px;
    padding: 0 0.75rem;
    font-size: 0.875rem;
    font-weight: 500;
    color: #475569;
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 0.5rem;
    text-decoration: none;
    transition: all 0.15s ease;
}
.wpdm-pagination a:hover {
    background: #f1f5f9;
    border-color: #cbd5e1;
    color: #1e293b;
}
.wpdm-pagination .current {
    background: var(--wpdm-primary, #008fef);
    border-color: var(--wpdm-primary, #008fef);
    color: #fff;
}
.wpdm-pagination .prev,
.wpdm-pagination .next {
    padding: 0;
    min-width: 36px;
}
.wpdm-pagination .dots {
    border: none;
    background: transparent;
    color: #94a3b8;
}
</style>
