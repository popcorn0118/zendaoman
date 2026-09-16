<?php
/**
 * User Dashboard - Profile/Home
 * Enterprise-grade design
 */

global $current_user, $wpdb;

$total_downloads = (int) $wpdb->get_var($wpdb->prepare(
    "SELECT COUNT(*) FROM {$wpdb->prefix}ahm_download_stats WHERE uid = %d",
    $current_user->ID
));

$today_downloads = (int) $wpdb->get_var($wpdb->prepare(
    "SELECT COUNT(*) FROM {$wpdb->prefix}ahm_download_stats WHERE uid = %d AND `year` = YEAR(CURDATE()) AND `month` = MONTH(CURDATE()) AND `day` = DAY(CURDATE())",
    $current_user->ID
));

$this_month_downloads = (int) $wpdb->get_var($wpdb->prepare(
    "SELECT COUNT(*) FROM {$wpdb->prefix}ahm_download_stats WHERE uid = %d AND `year` = YEAR(CURDATE()) AND `month` = MONTH(CURDATE())",
    $current_user->ID
));
?>

<?php do_action("wpdm_before_user_dashboard_summery"); ?>

<!-- Stats Grid -->
<div class="wpdm-stats-grid">
    <div class="wpdm-stat-card">
        <div class="wpdm-stat-icon wpdm-stat-icon--success">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
        </div>
        <div class="wpdm-stat-content">
            <span class="wpdm-stat-label"><?php esc_html_e('Total Downloads', 'download-manager'); ?></span>
            <span class="wpdm-stat-value"><?php echo number_format($total_downloads); ?></span>
        </div>
    </div>

    <div class="wpdm-stat-card">
        <div class="wpdm-stat-icon wpdm-stat-icon--info">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
        </div>
        <div class="wpdm-stat-content">
            <span class="wpdm-stat-label"><?php esc_html_e('This Month', 'download-manager'); ?></span>
            <span class="wpdm-stat-value"><?php echo number_format($this_month_downloads); ?></span>
        </div>
    </div>

    <div class="wpdm-stat-card">
        <div class="wpdm-stat-icon wpdm-stat-icon--warning">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
        </div>
        <div class="wpdm-stat-content">
            <span class="wpdm-stat-label"><?php esc_html_e('Today', 'download-manager'); ?></span>
            <span class="wpdm-stat-value"><?php echo number_format($today_downloads); ?></span>
        </div>
    </div>
</div>

<?php do_action("wpdm_after_user_dashboard_summery"); ?>

<?php
// Recommended Downloads
if (isset($params['recommended']) && (term_exists($params['recommended'], 'wpdmcategory') || $params['recommended'] == 'recent')) {
    $qparams = [
        'post_type' => 'wpdmpro',
        'posts_per_page' => 6,
        'orderby' => 'rand'
    ];

    if ($params['recommended'] != 'recent') {
        $qparams['tax_query'] = [['taxonomy' => 'wpdmcategory', 'field' => 'slug', 'terms' => explode(",", $params['recommended'])]];
    } else {
        $qparams['orderby'] = 'date';
    }

    $q = new WP_Query($qparams);
    if ($q->have_posts()) {
        ?>
        <div class="wpdm-card" style="margin-top: 1.5rem;">
            <div class="wpdm-card-header">
                <h3>
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                    <?php esc_html_e('Recommended Downloads', 'download-manager'); ?>
                </h3>
            </div>
            <div class="wpdm-card-body">
                <div class="wpdm-recommended-grid">
                    <?php
                    while ($q->have_posts()) {
                        $q->the_post();
                        if (WPDM()->package->userCanAccess(get_the_ID())) {
                            ?>
                            <a href="<?php the_permalink(); ?>" class="wpdm-recommended-item">
                                <div class="wpdm-recommended-thumb">
                                    <?php
                                    $thumb = wpdm_thumb(get_the_ID(), [300, 225], 'url');
                                    if ($thumb) {
                                        echo '<img src="' . esc_url($thumb) . '" alt="' . esc_attr(get_the_title()) . '" class="wpdm-recommended-img">';
                                    }
                                    ?>
                                </div>
                                <span class="wpdm-recommended-title"><?php the_title(); ?></span>
                            </a>
                            <?php
                        }
                    }
                    wp_reset_postdata();
                    ?>
                </div>
            </div>
        </div>
        <?php
    }
}
?>

<?php
// Favourites
if (isset($params['fav']) && (int)$params['fav'] == 1) {
    $myfavs = maybe_unserialize(get_user_meta(get_current_user_id(), '__wpdm_favs', true));
    if (!empty($myfavs)) {
        ?>
        <div class="wpdm-card" style="margin-top: 1.5rem;">
            <div class="wpdm-card-header">
                <h3>
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path></svg>
                    <?php esc_html_e('My Favourites', 'download-manager'); ?>
                </h3>
            </div>
            <div class="wpdm-table-wrap">
                <table class="wpdm-table">
                    <thead>
                        <tr>
                            <th><?php esc_html_e('Package Name', 'download-manager'); ?></th>
                            <th style="width: 100px; text-align: right;"><?php esc_html_e('Action', 'download-manager'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach ($myfavs as $fav) {
                            $fav_post = get_post($fav);
                            if (is_object($fav_post) && $fav_post->post_type == 'wpdmpro') {
                                ?>
                                <tr id="fav_<?php echo esc_attr($fav); ?>">
                                    <td>
                                        <a class="wpdm-table-link" href="<?php echo esc_url(get_permalink($fav_post->ID)); ?>">
                                            <?php echo esc_html($fav_post->post_title); ?>
                                        </a>
                                    </td>
                                    <td style="text-align: right;">
                                        <?php echo WPDM()->package->favBtn($fav, ['size' => 'btn-xs rem-fav fav_' . $fav, 'a2f_label' => __('Remove', 'download-manager'), 'rff_label' => __('Remove', 'download-manager')], false); ?>
                                    </td>
                                </tr>
                                <?php
                            }
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php
    }
}
?>

<!-- Recent Downloads -->
<div class="wpdm-card" style="margin-top: 1.5rem;">
    <div class="wpdm-card-header">
        <h3>
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
            <?php esc_html_e('Recent Downloads', 'download-manager'); ?>
        </h3>
    </div>
    <?php
    $recent = $wpdb->get_results($wpdb->prepare(
        "SELECT p.post_title, s.* FROM {$wpdb->prefix}posts p, {$wpdb->prefix}ahm_download_stats s WHERE s.uid = %d AND s.pid = p.ID ORDER BY s.timestamp DESC LIMIT 5",
        $current_user->ID
    ));
    ?>
    <?php if (!empty($recent)) : ?>
        <div class="wpdm-table-wrap">
            <table class="wpdm-table">
                <thead>
                    <tr>
                        <th><?php esc_html_e('Package Name', 'download-manager'); ?></th>
                        <th class="wpdm-hide-mobile"><?php esc_html_e('Download Time', 'download-manager'); ?></th>
                        <th class="wpdm-hide-mobile"><?php esc_html_e('IP Address', 'download-manager'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recent as $stat) : ?>
                        <tr>
                            <td>
                                <a class="wpdm-table-link" href="<?php echo esc_url(get_permalink($stat->pid)); ?>">
                                    <?php echo esc_html($stat->post_title); ?>
                                </a>
                            </td>
                            <td class="wpdm-hide-mobile">
                                <span class="wpdm-date"><?php echo date_i18n(get_option('date_format') . " H:i", $stat->timestamp + wpdm_tzoffset()); ?></span>
                            </td>
                            <td class="wpdm-hide-mobile">
                                <span class="wpdm-ip"><?php echo esc_html($stat->ip); ?></span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else : ?>
        <div class="wpdm-card-body">
            <div class="wpdm-empty-state wpdm-empty-state--compact">
                <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                <p><?php esc_html_e('No downloads yet', 'download-manager'); ?></p>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
jQuery(function($) {
    $('.rem-fav').on('click', function() {
        var ret = $(this).attr('class').match(/fav_([0-9]+)/);
        if (ret && ret[1]) {
            $('#fav_' + ret[1]).fadeOut(300, function() { $(this).remove(); });
        }
    });
});
</script>
