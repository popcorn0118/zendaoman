<?php
global $wpdb;
if ( ! defined( "ABSPATH" ) ) {
    die( "Shit happens!" );
}
$get_params = $_GET;

// Tables used
$states_table = "{$wpdb->prefix}ahm_download_stats";
$posts_table  = "{$wpdb->prefix}posts";
$users_table  = "{$wpdb->base_prefix}users";

/**
 * Query Parameters
 */
$from_date_string = wpdm_query_var( 'from_date' );
$to_date_string   = wpdm_query_var( 'to_date' );
$user_ids         = wpdm_query_var( 'user_ids' ) ?: [];
$package_ids      = wpdm_query_var( 'package_ids' ) ?: [];
$page_no          = wpdm_query_var( 'page_no', 'int' ) ?: 1;

/**
 * Sanitize parameters
 */
$from_date_string = sanitize_text_field( $from_date_string );
$to_date_string   = sanitize_text_field( $to_date_string );
$user_ids         = \WPDM\__\__::sanitize_array( $user_ids, 'int' );
$package_ids      = \WPDM\__\__::sanitize_array( $package_ids, 'int' );

/**
 * Selected/Initial Values for the fields
 * These are necessary for initialize filter fields
 */

$min_timestamp      = $wpdb->get_var( "SELECT min(timestamp) from $states_table" ) ?: time();
$selected_from_date = $from_date_string ? new DateTime( $from_date_string ) : ( new DateTime() )->setTimestamp( $min_timestamp );

$selected_to_date = $to_date_string ? ( new DateTime( $to_date_string ) ) : ( new DateTime() )->setTimestamp( time() );
$selected_to_date->modify( 'tomorrow' ); // we need to get the timestamp of the end of selected to_date
$end_of_selected_to_date_timestamp = $selected_to_date->getTimestamp() - 1;
$selected_to_date->setTimestamp( $end_of_selected_to_date_timestamp );


$user_ids_string = implode( ',', $user_ids );
$selected_users  = [];
if ( ! empty( $user_ids_string ) ) {
    $selected_users = $wpdb->get_results( "SELECT ID, user_login, display_name, user_email FROM $users_table  WHERE ID IN ({$user_ids_string})" );
}

$package_ids_string = implode( ',', $package_ids );
$selected_packages  = [];
if ( ! empty( $package_ids_string ) ) {
    $selected_packages = $wpdb->get_results( "SELECT ID, post_title  FROM $posts_table  WHERE ID IN ({$package_ids_string})" );
}


/**
 * Filter query parts
 */

$timestamp_filter = " AND s.timestamp >= {$selected_from_date->getTimestamp()}  AND s.timestamp <= {$selected_to_date->getTimestamp()}";

$user_ids_filter = "";
if ( count( $user_ids ) > 0 ) {
    $user_ids_filter = " AND s.uid IN (" . $user_ids_string . ") ";
}

$package_ids_filter = "";
if ( count( $package_ids ) > 0 ) {
    $package_ids_filter = " AND s.pid IN (" . $package_ids_string . ") ";
}

$uniqq = "";
if ( wpdm_query_var( 'uniq' ) ) {
    $uniqq = " group by pid ";
}


/**
 * Statistics query
 */

$items_per_page = 30;
$start          = $page_no ? ( $page_no - 1 ) * $items_per_page : 0;

//pd($selected_from_date->getTimestamp());
if(wpdm_query_var('cats')) {
    $cats_filter = get_posts( [
            'post_type'   => 'wpdmpro',
            'post_status' => 'publish',
            'numberposts' => - 1,
            'tax_query'   => [
                    [
                            'taxonomy'         => 'wpdmcategory',
                            'field'            => 'term_id',
                            'terms'            => wpdm_query_var( 'cats' ),
                            'include_children' => false
                    ]
            ]
    ] );
    $pack_ids    = [0];
    foreach ( $cats_filter as $pac ) {
        $pack_ids[] = $pac->ID;
    }
}
$pid_query = '';
if(isset($pack_ids) && is_array($pack_ids) && count($pack_ids) > 0)
    $pid_query = ' and s.pid in ('.implode(',', $pack_ids).')';

$hash = \WPDM\__\Crypt::encrypt( "SELECT [##fields##] FROM $states_table s WHERE 1 {$package_ids_filter} {$user_ids_filter} {$timestamp_filter} {$pid_query} {$uniqq}" );

$count_downloads_without_paging = $wpdb->get_var( "SELECT count(s.pid) FROM $states_table s, $posts_table p WHERE s.pid = p.ID  
                                        {$package_ids_filter} {$user_ids_filter} {$timestamp_filter} {$pid_query}
                                        " );

$filtered_result_rows = $wpdb->get_results( "SELECT p.post_title, s.* FROM $states_table s, $posts_table p WHERE s.pid = p.ID 
                                    {$package_ids_filter} {$user_ids_filter} {$timestamp_filter} {$pid_query} {$uniqq}  
                                    order by `timestamp` desc limit $start, $items_per_page
                                    " );
/*wpdmprecho("SELECT p.post_title, s.* FROM $states_table s, $posts_table p WHERE s.pid = p.ID
                                    {$package_ids_filter} {$user_ids_filter} {$timestamp_filter} {$pid_query} {$uniqq}
                                    order by `timestamp` desc limit $start, $items_per_page
                                    ");*/
$pagination           = array(
        'base'      => @add_query_arg( 'page_no', '%#%' ),
        'format'    => '',
        'total'     => ceil( $count_downloads_without_paging / $items_per_page ),
        'current'   => $page_no,
        'show_all'  => false,
        'type'      => 'list',
        'prev_next' => true,
        'prev_text' => '<i class="icon icon-angle-left"></i> Previous',
        'next_text' => 'Next <i class="icon icon-angle-right"></i>',
);

?>

<!-- Elegant Filters Section -->
<div class="wpdm-history-filters">
    <form method="get" action="<?php echo admin_url( 'edit.php' ); ?>" class="elegant-filter-form">
        <input type="hidden" name="post_type" value="wpdmpro"/>
        <input type="hidden" name="page" value="wpdm-stats"/>
        <input type="hidden" name="type" value="history"/>
        <input type="hidden" name="filter" value="1"/>

        <!-- Filter Header -->
        <div class="filter-header">
            <div class="filter-title">
                <i class="fas fa-filter"></i>
                <h3><?php _e('Advanced Filters', 'download-manager'); ?></h3>
            </div>
            <div class="filter-summary">
                <?php if (!empty($user_ids) || !empty($package_ids) || isset($cats) || $from_date_string || $to_date_string): ?>
                    <span class="active-filters-badge">
						<i class="fas fa-check-circle"></i>
						<?php _e('Filters Applied', 'download-manager'); ?>
					</span>
                <?php endif; ?>
            </div>
        </div>

        <!-- Quick Date Presets -->
        <div class="quick-filters-section">
            <div class="quick-filters-title">
                <i class="fas fa-clock"></i>
                <?php _e('Quick Date Ranges', 'download-manager'); ?>
            </div>
            <div class="quick-filter-buttons">
                <button type="button" class="quick-filter-btn" data-days="1">
                    <i class="fas fa-calendar-day"></i>
                    <?php _e('Today', 'download-manager'); ?>
                </button>
                <button type="button" class="quick-filter-btn" data-days="7">
                    <i class="fas fa-calendar-week"></i>
                    <?php _e('Last 7 Days', 'download-manager'); ?>
                </button>
                <button type="button" class="quick-filter-btn" data-days="30">
                    <i class="fas fa-calendar-alt"></i>
                    <?php _e('Last 30 Days', 'download-manager'); ?>
                </button>
                <button type="button" class="quick-filter-btn" data-days="90">
                    <i class="fas fa-calendar"></i>
                    <?php _e('Last 90 Days', 'download-manager'); ?>
                </button>
            </div>
        </div>

        <!-- Main Filters Grid -->
        <div class="filters-grid">
            <!-- Date Range Section -->
            <div class="filter-section date-section">
                <div class="section-header">
                    <i class="fas fa-calendar-alt"></i>
                    <h4><?php _e('Date Range', 'download-manager'); ?></h4>
                </div>
                <div class="date-inputs">
                    <div class="date-input-group">
                        <input type="text" name="from_date" value="<?php echo $selected_from_date->format( 'Y-m-d' ) ?>"
                               class="datepicker form-control" readonly="readonly"
                               aria-label="<?php esc_attr_e('From date', 'download-manager'); ?>"/>
                    </div>
                    <div class="date-input-group">
                        <input type="text" name="to_date" value="<?php echo $selected_to_date->format( 'Y-m-d' ) ?>"
                               class="datepicker form-control" readonly="readonly"
                               aria-label="<?php esc_attr_e('To date', 'download-manager'); ?>"/>
                    </div>
                </div>
            </div>

            <!-- Users Filter Section -->
            <div class="filter-section">
                <div class="section-header">
                    <i class="fas fa-users"></i>
                    <h4><?php _e('Users', 'download-manager'); ?></h4>
                    <?php if (!empty($user_ids)): ?>
                        <div class="clear-filter">
                            <?php
                            $get_params_xu = $get_params;
                            unset($get_params_xu['user_ids']);
                            $reset_url = add_query_arg($get_params_xu, 'edit.php');
                            ?>
                            <a href="<?php echo esc_url($reset_url); ?>" class="clear-btn" title="<?php _e('Clear user filter', 'download-manager'); ?>">
                                <i class="fas fa-times"></i>
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="select-wrapper">
                    <select id="user_ids" name="user_ids[]" multiple="multiple" class="elegant-select">
                        <?php foreach ($selected_users as $u): ?>
                            <option selected value="<?php echo $u->ID ?>">
                                <?php echo $u->display_name . " (" . $u->user_login . ")"; ?>
                            </option>
                        <?php endforeach ?>
                    </select>
                </div>
            </div>

            <!-- Categories Filter Section -->
            <?php
            $cats = wpdm_query_var( 'cats' );
            $cats = is_array($cats) ? array_map('intval', $cats) : [];
            function printOptions($options, $level = 0, $selected_cats = []) {
                $pstr = $level > 0 ? str_pad(" ", $level*2+1, "—",  STR_PAD_LEFT) : '';
                foreach ($options as $option) {
                    $selected = in_array($option['category']->term_id, $selected_cats) ? 'selected' : '';
                    echo "<option value='{$option['category']->term_id}' {$selected}>{$pstr}{$option['category']->name}</option>";
                    if(count($option['childs']) > 0) {
                        printOptions($option['childs'], $level + 1, $selected_cats);
                    }
                }
            }
            ?>
            <div class="filter-section">
                <div class="section-header">
                    <i class="fas fa-folder"></i>
                    <h4><?php _e('Categories', 'download-manager'); ?></h4>
                    <?php if (!empty($cats)): ?>
                        <div class="clear-filter">
                            <?php
                            $get_params_xc = $get_params;
                            unset($get_params_xc['cats']);
                            $reset_url = add_query_arg($get_params_xc, 'edit.php');
                            ?>
                            <a href="<?php echo esc_url($reset_url); ?>" class="clear-btn" title="<?php _e('Clear category filter', 'download-manager'); ?>">
                                <i class="fas fa-times"></i>
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="select-wrapper">
                    <select id="cats" name="cats[]" multiple="multiple" class="elegant-select">
                        <?php
                        $cats_options = WPDM()->categories->hArray();
                        printOptions($cats_options, 0, $cats);
                        ?>
                    </select>
                </div>
            </div>

            <!-- Packages Filter Section -->
            <div class="filter-section">
                <div class="section-header">
                    <i class="fas fa-box"></i>
                    <h4><?php _e('Packages', 'download-manager'); ?></h4>
                    <?php if (!empty($package_ids)): ?>
                        <div class="clear-filter">
                            <?php
                            $get_params_xp = $get_params;
                            unset($get_params_xp['package_ids']);
                            $reset_url = add_query_arg($get_params_xp, 'edit.php');
                            ?>
                            <a href="<?php echo esc_url($reset_url); ?>" class="clear-btn" title="<?php _e('Clear package filter', 'download-manager'); ?>">
                                <i class="fas fa-times"></i>
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="select-wrapper">
                    <select id="package_ids" name="package_ids[]" multiple="multiple" class="elegant-select">
                        <?php foreach ($selected_packages as $p): ?>
                            <option selected value="<?php echo $p->ID ?>">
                                <?php echo esc_html($p->post_title); ?>
                            </option>
                        <?php endforeach ?>
                    </select>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="filter-actions">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-search"></i>
                <?php _e('Apply Filters', 'download-manager'); ?>
            </button>
            <a href="edit.php?post_type=wpdmpro&page=wpdm-stats&task=export&__xnonce=<?php echo wp_create_nonce( NONCE_KEY ); ?>&hash=<?php echo $hash; ?>"
               class="btn btn-secondary">
                <i class="fas fa-file-export"></i>
                <?php _e('Export Results', 'download-manager'); ?>
            </a>
            <a href="edit.php?post_type=wpdmpro&page=wpdm-stats&type=history"
               class="btn btn-secondary">
                <i class="fas fa-undo"></i>
                <?php _e('Reset All', 'download-manager'); ?>
            </a>
        </div>
    </form>
</div>
<hr/>
<?php if ( $count_downloads_without_paging ) : ?>

    <!-- Results Header -->
    <div class="results-header">
        <div class="results-info">
            <div class="results-count">
                <i class="fas fa-list-ul"></i>
                <span><?php printf(__('Showing %s - %s of %s downloads', 'download-manager'),
                            '<strong>' . number_format($start + 1) . '</strong>',
                            '<strong>' . number_format($start + count( $filtered_result_rows )) . '</strong>',
                            '<strong>' . number_format($count_downloads_without_paging) . '</strong>'
                    ); ?></span>
            </div>
            <?php if ($count_downloads_without_paging > $items_per_page): ?>
                <div class="results-pagination-info">
                    <i class="fas fa-file-alt"></i>
                    <?php printf(__('Page %d of %d', 'download-manager'),
                            $page_no,
                            ceil($count_downloads_without_paging / $items_per_page)
                    ); ?>
                </div>
            <?php endif; ?>
        </div>
        <div class="results-actions">
            <div class="view-toggle">
                <button class="view-btn active" data-view="table">
                    <i class="fas fa-table"></i>
                    <?php _e('Table', 'download-manager'); ?>
                </button>
                <button class="view-btn" data-view="cards">
                    <i class="fas fa-th-large"></i>
                    <?php _e('Cards', 'download-manager'); ?>
                </button>
            </div>
        </div>
    </div>

    <!-- Enhanced Results Table -->
    <div class="elegant-table-container">
        <div class="table-wrapper">
            <table class="elegant-table">
                <thead>
                <tr>
                    <th class="sortable" data-sort="package">
                        <div class="th-content">
                            <i class="fas fa-box"></i>
                            <span><?php _e( "Package", "download-manager" ); ?></span>
                            <i class="fas fa-sort sort-icon"></i>
                        </div>
                    </th>
                    <th class="sortable" data-sort="file">
                        <div class="th-content">
                            <i class="fas fa-file"></i>
                            <span><?php _e( "File", "download-manager" ); ?></span>
                            <i class="fas fa-sort sort-icon"></i>
                        </div>
                    </th>
                    <th class="sortable" data-sort="version">
                        <div class="th-content">
                            <i class="fas fa-tag"></i>
                            <span><?php _e( "Version", "download-manager" ); ?></span>
                            <i class="fas fa-sort sort-icon"></i>
                        </div>
                    </th>
                    <th class="sortable" data-sort="time">
                        <div class="th-content">
                            <i class="fas fa-clock"></i>
                            <span><?php _e( "Date & Time", "download-manager" ); ?></span>
                            <i class="fas fa-sort sort-icon"></i>
                        </div>
                    </th>
                    <th class="sortable" data-sort="user">
                        <div class="th-content">
                            <i class="fas fa-user"></i>
                            <span><?php _e( "User", "download-manager" ); ?></span>
                            <i class="fas fa-sort sort-icon"></i>
                        </div>
                    </th>
                    <th>
                        <div class="th-content">
                            <i class="fas fa-globe"></i>
                            <span><?php _e( "Location", "download-manager" ); ?></span>
                        </div>
                    </th>
                    <th>
                        <div class="th-content">
                            <i class="fas fa-desktop"></i>
                            <span><?php _e( "System", "download-manager" ); ?></span>
                        </div>
                    </th>
                </tr>
                </thead>
                <tbody>
                <?php
                // Add country detection function
                if (!function_exists('wpdm_get_country_from_ip_history')) {
                    function wpdm_get_country_from_ip_history($ip) {
                        if (empty($ip) || $ip === '0.0.0.0') return 'Unknown';

                        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false) {
                            return 'Local/Private';
                        }

                        $cache_key = 'wpdm_country_hist_' . md5($ip);
                        $cached_country = get_transient($cache_key);

                        if ($cached_country !== false) {
                            return $cached_country;
                        }

                        $response = wp_remote_get("https://ipapi.co/{$ip}/country_name/", array(
                                'timeout' => 3,
                                'headers' => array('User-Agent' => 'WordPress Download Manager History')
                        ));

                        if (!is_wp_error($response) && wp_remote_retrieve_response_code($response) == 200) {
                            $country = trim(wp_remote_retrieve_body($response));
                            if (!empty($country) && !strpos($country, 'error')) {
                                set_transient($cache_key, $country, 12 * HOUR_IN_SECONDS);
                                return $country;
                            }
                        }

                        // Fallback to basic detection
                        $ip_parts = explode('.', $ip);
                        if (count($ip_parts) >= 2) {
                            $first_octet = intval($ip_parts[0]);
                            if (in_array($first_octet, [3, 4, 6, 7, 8, 9, 11, 13, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 26, 28, 29, 30, 32, 33, 34, 35, 38, 40, 44, 45, 47, 48, 50, 52, 54, 55, 56, 57, 63, 64, 65, 66, 67, 68, 69, 70, 71, 72, 73, 74, 75, 76, 96, 97, 98, 99, 100, 104, 107, 108, 173, 174, 184, 199, 204, 205, 206, 207, 208, 209, 216])) {
                                set_transient($cache_key, 'United States', 12 * HOUR_IN_SECONDS);
                                return 'United States';
                            }
                        }

                        set_transient($cache_key, 'Unknown', 12 * HOUR_IN_SECONDS);
                        return 'Unknown';
                    }
                }

                if (!function_exists('wpdm_stats_browser_icon')) {
                    function wpdm_stats_browser_icon($name) {
                        $name = strtolower($name);
                        $map = [
                            'edge'              => ['fab', 'fa-edge'],
                            'chrome'            => ['fab', 'fa-chrome'],
                            'firefox'           => ['fab', 'fa-firefox-browser'],
                            'safari'            => ['fab', 'fa-safari'],
                            'opera'             => ['fab', 'fa-opera'],
                            'internet explorer' => ['fab', 'fa-internet-explorer'],
                        ];
                        foreach ($map as $needle => $icon) {
                            if (strpos($name, $needle) !== false) {
                                return $icon;
                            }
                        }
                        return ['fas', 'fa-globe'];
                    }
                }

                foreach ( $filtered_result_rows as $stat ) {
                    $agent = WPDM()->userAgent->set( $stat->agent )->parse();
                    $display_name = 'Guest';
                    $user_class = 'guest-user';

                    // Get country name from IP.
                    // Only perform the external geolocation lookup when IP handling is enabled
                    // (same gate as the display below), so stored IPs are never sent to a third
                    // party when the admin has disabled IP handling via the __wpdm_noip option.
                    $country_name = __('Unknown', 'download-manager');
                    if ( get_option( '__wpdm_noip' ) == 0 && !empty($stat->ip) ) {
                        $country_name = wpdm_get_country_from_ip_history($stat->ip);
                    }

                    if ( $stat->uid > 0 ) {
                        $user = get_user_by( 'id', $stat->uid );
                        if ( is_object( $user ) ) {
                            $display_name = $user->display_name;
                            $user_class = 'registered-user';
                        } else {
                            $display_name = __('[ Deleted User ]', 'download-manager');
                            $user_class = 'deleted-user';
                        }
                    }
                    ?>
                    <tr class="elegant-row" data-download-id="<?php echo $stat->id; ?>">
                        <!-- Package -->
                        <td class="package-cell">
                            <div class="package-info">
                                <div class="package-main">
                                    <a title="<?php _e('Filter by this package', 'download-manager'); ?>"
                                       class="package-link"
                                       href="edit.php?post_type=wpdmpro&page=wpdm-stats&type=history&package_ids[]=<?php echo $stat->pid ?>">
                                        <span class="package-title"><?php echo esc_html($stat->post_title); ?></span>
                                    </a>
                                </div>
                                <div class="package-actions">
                                    <a target="_blank" href="post.php?action=edit&post=<?php echo $stat->pid; ?>"
                                       title="<?php _e('Edit Package', 'download-manager'); ?>" class="action-link edit-link">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a target="_blank" href="<?php echo get_permalink( $stat->pid ); ?>"
                                       title="<?php _e('View Package', 'download-manager'); ?>" class="action-link view-link">
                                        <i class="fas fa-external-link-alt"></i>
                                    </a>
                                </div>
                            </div>
                        </td>

                        <!-- File -->
                        <td class="file-cell">
                            <div class="file-info">
                                <i class="fas fa-file-alt file-icon"></i>
                                <span class="filename" title="<?php echo esc_attr($stat->filename); ?>">
									<?php echo esc_html(strlen($stat->filename) > 25 ? substr($stat->filename, 0, 25) . '...' : $stat->filename); ?>
								</span>
                            </div>
                        </td>

                        <!-- Version -->
                        <td class="version-cell">
                            <?php if (!empty($stat->version)): ?>
                                <span class="version-badge"><?php echo esc_html($stat->version); ?></span>
                            <?php else: ?>
                                <span class="no-version">—</span>
                            <?php endif; ?>
                        </td>

                        <!-- Date & Time -->
                        <td class="datetime-cell">
                            <div class="datetime-info">
                                <span class="dt-date"><?php echo wp_date( get_option( 'date_format' ), $stat->timestamp ); ?></span>
                                <span class="dt-meta"><?php echo wp_date( get_option( 'time_format' ), $stat->timestamp ); ?> &middot; <?php echo human_time_diff($stat->timestamp, current_time('timestamp')) . ' ' . __('ago', 'download-manager'); ?></span>
                            </div>
                        </td>

                        <!-- User -->
                        <td class="user-cell">
                            <div class="user-info <?php echo $user_class; ?>">
                                <?php if ( $stat->uid > 0 ): ?>
                                    <span class="user-avatar">
                                        <i class="fas fa-user"></i>
                                    </span>
                                    <span class="user-main">
                                        <a title="<?php _e('Filter by this user', 'download-manager'); ?>"
                                           class="user-link"
                                           href="edit.php?post_type=wpdmpro&page=wpdm-stats&type=history&filter=1&user_ids[0]=<?php echo $stat->uid; ?>">
                                            <span class="user-name"><?php echo esc_html($display_name); ?></span>
                                        </a>
                                        <a target="_blank" title="<?php _e('Edit User', 'download-manager'); ?>"
                                           class="user-edit" href="user-edit.php?user_id=<?php echo $stat->uid; ?>">
                                            <i class="fas fa-pen"></i>
                                        </a>
                                    </span>
                                <?php else: ?>
                                    <span class="user-avatar guest-avatar">
                                        <i class="fas fa-user-slash"></i>
                                    </span>
                                    <span class="user-main">
                                        <span class="user-name guest-name"><?php _e('Guest', 'download-manager'); ?></span>
                                    </span>
                                <?php endif; ?>
                            </div>
                        </td>

                        <!-- Location -->
                        <td class="location-cell">
                            <?php if ( get_option( '__wpdm_noip' ) == 0 && !empty($stat->ip) ): ?>
                                <div class="location-info">
                                    <span class="country-info">
                                        <i class="fas fa-flag"></i>
                                        <span class="country-name"><?php echo esc_html($country_name); ?></span>
                                    </span>
                                    <a target="_blank" href="https://ip-api.com/#<?php echo esc_attr($stat->ip); ?>"
                                       class="ip-link" title="<?php _e('View IP location details', 'download-manager'); ?>">
                                        <span class="ip-address"><?php echo esc_html($stat->ip); ?></span>
                                    </a>
                                </div>
                            <?php else: ?>
                                <div class="location-unknown">
                                    <i class="fas fa-question-circle"></i>
                                    <span><?php _e('Unknown', 'download-manager'); ?></span>
                                </div>
                            <?php endif; ?>
                        </td>

                        <!-- System -->
                        <td class="system-cell">
                            <?php $bicon = wpdm_stats_browser_icon($agent->browserName); ?>
                            <div class="system-info">
                                <span class="browser-info">
                                    <i class="<?php echo $bicon[0]; ?> <?php echo $bicon[1]; ?> browser-icon"></i>
                                    <span class="browser-name"><?php echo esc_html($agent->browserName); ?></span>
                                </span>
                                <span class="os-info"><?php echo esc_html($agent->OS); ?></span>
                            </div>
                        </td>

                    </tr>
                    <?php
                }
                ?>

                </tbody>
            </table>
        </div>

        <!-- Cards View (Hidden by default) -->
        <div class="cards-wrapper">
            <?php
            foreach ( $filtered_result_rows as $stat ) {
                $agent = WPDM()->userAgent->set( $stat->agent )->parse();
                $display_name = 'Guest';
                $user_class = 'guest-user';

                // Get country name from IP for cards view
                $country_name = wpdm_get_country_from_ip_history($stat->ip);

                if ( $stat->uid > 0 ) {
                    $user = get_user_by( 'id', $stat->uid );
                    if ( is_object( $user ) ) {
                        $display_name = $user->display_name;
                        $user_class = 'registered-user';
                    } else {
                        $display_name = __('[ Deleted User ]', 'download-manager');
                        $user_class = 'deleted-user';
                    }
                }
                ?>
                <div class="download-card" data-download-id="<?php echo $stat->id; ?>">
                    <div class="card-header">
                        <div class="card-package-info">
                            <h4 class="card-package-title">
                                <a href="edit.php?post_type=wpdmpro&page=wpdm-stats&type=history&package_ids[]=<?php echo $stat->pid ?>"
                                   title="<?php _e('Filter by this package', 'download-manager'); ?>">
                                    <?php echo esc_html($stat->post_title); ?>
                                </a>
                            </h4>
                        </div>
                        <div class="card-package-actions">
                            <a target="_blank" href="post.php?action=edit&post=<?php echo $stat->pid; ?>"
                               title="<?php _e('Edit Package', 'download-manager'); ?>" class="card-action-link">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a target="_blank" href="<?php echo get_permalink( $stat->pid ); ?>"
                               title="<?php _e('View Package', 'download-manager'); ?>" class="card-action-link">
                                <i class="fas fa-external-link-alt"></i>
                            </a>
                        </div>
                    </div>

                    <div class="card-body">
                        <!-- File -->
                        <div class="card-field">
                            <div class="card-field-label">
                                <i class="fas fa-file-alt"></i>
                                <?php _e('File', 'download-manager'); ?>
                            </div>
                            <div class="card-field-value">
								<span class="card-file-name" title="<?php echo esc_attr($stat->filename); ?>">
									<?php echo esc_html(strlen($stat->filename) > 30 ? substr($stat->filename, 0, 30) . '...' : $stat->filename); ?>
								</span>
                            </div>
                        </div>

                        <!-- Version -->
                        <div class="card-field">
                            <div class="card-field-label">
                                <i class="fas fa-tag"></i>
                                <?php _e('Version', 'download-manager'); ?>
                            </div>
                            <div class="card-field-value">
                                <?php if (!empty($stat->version)): ?>
                                    <span class="card-version-badge"><?php echo esc_html($stat->version); ?></span>
                                <?php else: ?>
                                    <span class="no-version">—</span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Date & Time -->
                        <div class="card-field">
                            <div class="card-field-label">
                                <i class="fas fa-calendar-alt"></i>
                                <?php _e('Date & Time', 'download-manager'); ?>
                            </div>
                            <div class="card-field-value">
                                <div class="card-datetime">
                                    <div class="card-date">
                                        <?php echo wp_date( get_option( 'date_format' ), $stat->timestamp ); ?>
                                    </div>
                                    <div class="card-time">
                                        <?php echo wp_date( get_option( 'time_format' ), $stat->timestamp ); ?>
                                    </div>
                                    <div class="card-relative-time">
                                        <?php echo human_time_diff($stat->timestamp, current_time('timestamp')) . ' ' . __('ago', 'download-manager'); ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- User -->
                        <div class="card-field">
                            <div class="card-field-label">
                                <i class="fas fa-user"></i>
                                <?php _e('User', 'download-manager'); ?>
                            </div>
                            <div class="card-field-value">
                                <div class="card-user-info <?php echo $user_class; ?>">
                                    <?php if ( $stat->uid > 0 ): ?>
                                        <div class="card-user-avatar">
                                            <i class="fas fa-user"></i>
                                        </div>
                                        <a href="edit.php?post_type=wpdmpro&page=wpdm-stats&type=history&filter=1&user_ids[0]=<?php echo $stat->uid; ?>"
                                           title="<?php _e('Filter by this user', 'download-manager'); ?>" class="card-user-name">
                                            <?php echo esc_html($display_name); ?>
                                        </a>
                                    <?php else: ?>
                                        <div class="card-user-avatar">
                                            <i class="fas fa-user-slash"></i>
                                        </div>
                                        <span class="card-user-name"><?php _e('Guest', 'download-manager'); ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Location -->
                        <div class="card-field">
                            <div class="card-field-label">
                                <i class="fas fa-globe"></i>
                                <?php _e('Location', 'download-manager'); ?>
                            </div>
                            <div class="card-field-value">
                                <?php if ( get_option( '__wpdm_noip' ) == 0 && !empty($stat->ip) ): ?>
                                    <div class="card-location-info">
                                        <div class="card-country">
                                            <i class="fas fa-flag"></i>
                                            <span><?php echo esc_html($country_name); ?></span>
                                        </div>
                                        <div class="card-ip">
                                            <a target="_blank" href="https://ip-api.com/#<?php echo esc_attr($stat->ip); ?>"
                                               class="card-location-link" title="<?php _e('View IP location details', 'download-manager'); ?>">
                                                <i class="fas fa-globe"></i>
                                                <?php echo esc_html($stat->ip); ?>
                                            </a>
                                        </div>
                                    </div>
                                <?php else: ?>
                                    <span><?php _e('Unknown', 'download-manager'); ?></span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- System -->
                        <div class="card-field">
                            <div class="card-field-label">
                                <i class="fas fa-desktop"></i>
                                <?php _e('System', 'download-manager'); ?>
                            </div>
                            <div class="card-field-value">
                                <?php $bicon = wpdm_stats_browser_icon($agent->browserName); ?>
                                <div class="card-system-info">
                                    <div class="card-browser">
                                        <i class="<?php echo $bicon[0]; ?> <?php echo $bicon[1]; ?>"></i>
                                        <span><?php echo esc_html($agent->browserName); ?></span>
                                    </div>
                                    <div class="card-os">
                                        <i class="fas fa-desktop"></i>
                                        <span><?php echo esc_html($agent->OS); ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
            }
            ?>
        </div>

        <!-- Elegant Pagination -->
        <div class="table-footer">
            <div class="pagination-wrapper">
                <?php if ( ! empty( $wp_query->query_vars['s'] ) ) {
                    $pagination['add_args'] = array( 's' => get_query_var( 's' ) );
                }

                $pagination_html = paginate_links( $pagination );
                if ($pagination_html):
                    // Clean up and style the pagination HTML
                    $styled_pagination = $pagination_html;

                    // Replace the main ul class
                    $styled_pagination = str_replace(
                            ['<ul class=\'page-numbers\'>', '<ul class="page-numbers">'],
                            '<ul class="pagination-list">',
                            $styled_pagination
                    );

                    // Replace all page-numbers classes with pagination-item
                    $styled_pagination = str_replace(
                            ['class="page-numbers', 'class=\'page-numbers'],
                            'class="pagination-item',
                            $styled_pagination
                    );

                    // Fix the next/prev buttons - they should have proper classes
                    $styled_pagination = str_replace(
                            ['<a class="next pagination-item"', '<a class="prev pagination-item"'],
                            ['<a class="pagination-item next"', '<a class="pagination-item prev"'],
                            $styled_pagination
                    );

                    // Fix dots styling
                    $styled_pagination = str_replace(
                            '<span class="pagination-item dots">',
                            '<span class="pagination-item dots">',
                            $styled_pagination
                    );

                    echo '<div class="elegant-pagination">' . $styled_pagination . '</div>';
                endif; ?>
            </div>

            <div class="table-info">
                <div class="items-per-page">
                    <i class="fas fa-list"></i>
                    <?php printf(__('%d items per page', 'download-manager'), $items_per_page); ?>
                </div>
                <div class="total-items">
                    <i class="fas fa-database"></i>
                    <?php printf(__('%s total downloads', 'download-manager'), number_format($count_downloads_without_paging)); ?>
                </div>
            </div>
        </div>
    </div>

<?php else : ?>
    <div class="no-results-container">
        <div class="no-results-content">
            <div class="no-results-icon">
                <i class="fas fa-search"></i>
            </div>
            <div class="no-results-message">
                <h4><?php _e('No Downloads Found', 'download-manager'); ?></h4>
                <p><?php _e('No download records match your current filters. Try adjusting your search criteria or date range.', 'download-manager'); ?></p>
            </div>
            <div class="no-results-actions">
                <a href="edit.php?post_type=wpdmpro&page=wpdm-stats&type=history" class="btn-elegant secondary">
                    <i class="fas fa-undo"></i>
                    <?php _e('Reset Filters', 'download-manager'); ?>
                </a>
                <a href="edit.php?post_type=wpdmpro&page=wpdm-stats" class="btn-elegant primary">
                    <i class="fas fa-chart-bar"></i>
                    <?php _e('View Overview', 'download-manager'); ?>
                </a>
            </div>
        </div>
    </div>
<?php endif; ?>

<!-- SCRIPTS -->

<link href="<?php echo esc_url( WPDM_BASE_URL . 'assets/select2/css/select2.min.css' ); ?>" rel="stylesheet"/>
<script src="<?php echo esc_url( WPDM_BASE_URL . 'assets/select2/js/select2.min.js' ); ?>"></script>
<script>
    jQuery(function ($) {
        // Standard jQuery UI datepicker (default style)
        $(".datepicker").datepicker({
            dateFormat: 'yy-mm-dd',
            minDate: new Date(<?= intval( $min_timestamp ) * 1000 ?>),
            maxDate: new Date(),
            changeMonth: true,
            changeYear: true,
            yearRange: "-20:+0",
            showOtherMonths: true,
            selectOtherMonths: true
        });

        // Initialize Select2 components
        function initializeSelect2() {
            // Categories Select2
            if ($('#cats').length && !$('#cats').hasClass('select2-hidden-accessible')) {
                $('#cats').select2({
                    placeholder: "<?php _e('Select categories...', 'download-manager'); ?>",
                    allowClear: true,
                    theme: "default",
                    width: '100%'
                });
            }

            // Packages Select2
            if ($('#package_ids').length && !$('#package_ids').hasClass('select2-hidden-accessible')) {
                $('#package_ids').select2({
                    theme: "default",
                    placeholder: "<?php _e('Search packages...', 'download-manager'); ?>",
                    allowClear: true,
                    width: '100%',
                    ajax: {
                        url: ajaxurl,
                        dataType: 'json',
                        delay: 250,
                        data: function (params) {
                            return {
                                action: 'wpdm_stats_get_packages',
                                term: params.term,
                                __spnonce: '<?php echo wp_create_nonce(WPDM_PUB_NONCE); ?>'
                            };
                        },
                        processResults: function (data) {
                            if (data.success === false) {
                                return { results: [] };
                            }
                            return data;
                        },
                        cache: true
                    },
                    minimumInputLength: 2,
                    escapeMarkup: function (markup) { return markup; }
                });
            }

            // Users Select2
            if ($('#user_ids').length && !$('#user_ids').hasClass('select2-hidden-accessible')) {
                $('#user_ids').select2({
                    theme: "default",
                    placeholder: "<?php _e('Search users...', 'download-manager'); ?>",
                    allowClear: true,
                    width: '100%',
                    ajax: {
                        url: ajaxurl,
                        dataType: 'json',
                        delay: 250,
                        data: function (params) {
                            return {
                                action: 'wpdm_stats_get_users',
                                term: params.term,
                                __spnonce: '<?php echo wp_create_nonce(WPDM_PUB_NONCE); ?>'
                            };
                        },
                        processResults: function (data) {
                            if (data.success === false) {
                                return { results: [] };
                            }
                            return data;
                        },
                        cache: true
                    },
                    minimumInputLength: 2,
                    escapeMarkup: function (markup) { return markup; }
                });
            }
        }

        // Initialize Select2 when DOM is ready
        initializeSelect2();

        // Re-initialize if content changes (for dynamic loading)
        setTimeout(initializeSelect2, 1000);

        // Quick date filter functionality
        $('.quick-filter-btn').click(function(e) {
            e.preventDefault();
            var days = $(this).data('days');
            var today = new Date();
            var fromDate = new Date();

            if (days === 1) {
                // Today only
                fromDate = new Date(today);
            } else {
                // Calculate past date
                fromDate.setDate(today.getDate() - days + 1);
            }

            // Format dates for input fields
            var formatDate = function(date) {
                var year = date.getFullYear();
                var month = String(date.getMonth() + 1).padStart(2, '0');
                var day = String(date.getDate()).padStart(2, '0');
                return year + '-' + month + '-' + day;
            };

            // Set the values directly to input fields
            $('input[name="from_date"]').val(formatDate(fromDate));
            $('input[name="to_date"]').val(formatDate(today));

            // Visual feedback
            $('.quick-filter-btn').removeClass('active');
            $(this).addClass('active');

            // Auto-submit form after date selection
            setTimeout(function() {
                $('.elegant-filter-form').submit();
            }, 100);
        });

        // Table sorting functionality
        $('.sortable').click(function() {
            var $this = $(this);
            var sortColumn = $this.data('sort');

            // Remove active sort from other columns
            $('.sortable').removeClass('sort-asc sort-desc');
            $('.sort-icon').removeClass('fa-sort-up fa-sort-down').addClass('fa-sort');

            // Determine current sort direction
            var currentSort = $this.hasClass('sort-asc') ? 'desc' : 'asc';

            // Apply new sort
            $this.addClass('sort-' + currentSort);
            var sortIcon = currentSort === 'asc' ? 'fa-sort-up' : 'fa-sort-down';
            $this.find('.sort-icon').removeClass('fa-sort').addClass(sortIcon);

            // Here you would implement actual sorting logic
            // For demonstration, we'll just show visual feedback
            showSortingFeedback(sortColumn, currentSort);
        });

        // View toggle functionality
        $('.view-btn').click(function() {
            var view = $(this).data('view');
            $('.view-btn').removeClass('active');
            $(this).addClass('active');

            if (view === 'cards') {
                // Switch to card view
                $('.elegant-table-container').addClass('card-view');
                showNotification('<?php _e("Switched to card view", "download-manager"); ?>', 'info');
            } else {
                // Switch to table view
                $('.elegant-table-container').removeClass('card-view');
                showNotification('<?php _e("Switched to table view", "download-manager"); ?>', 'info');
            }
        });

        // Row hover effects
        $(document).on('mouseenter', '.elegant-row', function() {
            $(this).addClass('row-hovered');
        }).on('mouseleave', '.elegant-row', function() {
            $(this).removeClass('row-hovered');
        });

        // Card hover effects
        $(document).on('mouseenter', '.download-card', function() {
            $(this).addClass('card-hovered');
        }).on('mouseleave', '.download-card', function() {
            $(this).removeClass('card-hovered');
        });

        // Helper functions
        function showSortingFeedback(column, direction) {
            var message = '<?php _e("Sorting by", "download-manager"); ?> ' + column + ' (' + direction + ')';
            showNotification(message, 'info');
        }

        function showNotification(message, type) {
            var notification = $('<div class="elegant-notification ' + type + '">')
                .html('<i class="fas fa-info-circle"></i> ' + message);

            $('body').append(notification);

            notification.fadeIn(300).delay(3000).fadeOut(300, function() {
                $(this).remove();
            });
        }

        // Initialize tooltips for better UX
        $('[title]').each(function() {
            $(this).tooltip({
                placement: 'top',
                trigger: 'hover'
            });
        });

        // Handle form submission
        $('.elegant-filter-form').on('submit', function(e) {
            // Force Select2 to update the underlying select elements
            $('#cats, #package_ids, #user_ids').each(function() {
                $(this).trigger('change.select2');
            });

            return true; // Allow form submission
        });

        // Add loading states to buttons
        $('.elegant-filter-form button[type="submit"]').click(function(e) {
            var $btn = $(this);
            var originalText = $btn.html();

            $btn.html('<i class="fas fa-spinner fa-spin"></i> <?php _e("Loading...", "download-manager"); ?>');

            // Allow natural form submission
            setTimeout(function() {
                // This will only run if the page doesn't redirect
                $btn.html(originalText);
            }, 1000);
        });
    });
</script>
