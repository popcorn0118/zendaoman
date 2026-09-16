<?php
/**
 * Activity Report Generator
 *
 * Collects download statistics and generates activity reports.
 *
 * @package WPDM\__
 * @since 7.0.2
 */

namespace WPDM\__;

class ActivityReport
{
    /**
     * Start date of the current reporting period (timestamp)
     *
     * @var int
     */
    private $startDate;

    /**
     * End date of the current reporting period (timestamp)
     *
     * @var int
     */
    private $endDate;

    /**
     * Start date of the previous period for comparison (timestamp)
     *
     * @var int
     */
    private $previousStartDate;

    /**
     * End date of the previous period for comparison (timestamp)
     *
     * @var int
     */
    private $previousEndDate;

    /**
     * Report frequency
     *
     * @var string weekly|monthly
     */
    private $frequency;

    /**
     * Constructor
     *
     * @param string $frequency 'weekly' or 'monthly'
     */
    public function __construct(string $frequency = 'weekly')
    {
        $this->frequency = $frequency;
        $this->calculateDateRanges($frequency);
    }

    /**
     * Calculate date ranges based on frequency
     *
     * @param string $frequency
     * @return void
     */
    private function calculateDateRanges(string $frequency): void
    {
        if ($frequency === 'monthly') {
            // Previous calendar month
            $this->startDate = strtotime('first day of last month midnight');
            $this->endDate = strtotime('last day of last month 23:59:59');

            // Month before that for comparison
            $this->previousStartDate = strtotime('first day of -2 months midnight');
            $this->previousEndDate = strtotime('last day of -2 months 23:59:59');
        } else {
            // Weekly: Last 7 complete days (Monday to Sunday of previous week)
            $this->startDate = strtotime('monday last week midnight');
            $this->endDate = strtotime('sunday last week 23:59:59');

            // Week before that for comparison
            $this->previousStartDate = strtotime('monday -2 weeks midnight');
            $this->previousEndDate = strtotime('sunday -2 weeks 23:59:59');
        }
    }

    /**
     * Get download summary statistics
     *
     * @return array
     */
    public function getDownloadSummary(): array
    {
        global $wpdb;

        // Current period downloads
        $currentDownloads = (int) $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM {$wpdb->prefix}ahm_download_stats
             WHERE timestamp >= %d AND timestamp <= %d",
            $this->startDate,
            $this->endDate
        ));

        // Previous period downloads
        $previousDownloads = (int) $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM {$wpdb->prefix}ahm_download_stats
             WHERE timestamp >= %d AND timestamp <= %d",
            $this->previousStartDate,
            $this->previousEndDate
        ));

        // Daily average
        $days = max(1, ($this->endDate - $this->startDate) / 86400);
        $dailyAverage = round($currentDownloads / $days, 1);

        // Peak day
        $peakDay = $wpdb->get_row($wpdb->prepare(
            "SELECT DATE(FROM_UNIXTIME(timestamp)) as date, COUNT(*) as count
             FROM {$wpdb->prefix}ahm_download_stats
             WHERE timestamp >= %d AND timestamp <= %d
             GROUP BY DATE(FROM_UNIXTIME(timestamp))
             ORDER BY count DESC
             LIMIT 1",
            $this->startDate,
            $this->endDate
        ));

        return [
            'total' => $currentDownloads,
            'previous' => $previousDownloads,
            'change' => $this->calculateChange($currentDownloads, $previousDownloads),
            'change_class' => $currentDownloads >= $previousDownloads ? 'positive' : 'negative',
            'daily_average' => $dailyAverage,
            'peak_day' => $peakDay ? date_i18n(get_option('date_format'), strtotime($peakDay->date)) : '-',
            'peak_day_count' => $peakDay ? (int) $peakDay->count : 0,
        ];
    }

    /**
     * Get top downloaded packages
     *
     * @param int $limit
     * @return array
     */
    public function getTopPackages(int $limit = 5): array
    {
        global $wpdb;

        $results = $wpdb->get_results($wpdb->prepare(
            "SELECT pid, COUNT(*) as downloads
             FROM {$wpdb->prefix}ahm_download_stats
             WHERE timestamp >= %d AND timestamp <= %d
             GROUP BY pid
             ORDER BY downloads DESC
             LIMIT %d",
            $this->startDate,
            $this->endDate,
            $limit
        ));

        $packages = [];
        $maxDownloads = 0;

        foreach ($results as $row) {
            if ((int) $row->downloads > $maxDownloads) {
                $maxDownloads = (int) $row->downloads;
            }
        }

        foreach ($results as $index => $row) {
            $post = get_post($row->pid);
            if (!$post) continue;

            $totalDownloads = (int) get_post_meta($row->pid, '__wpdm_download_count', true);
            $barWidth = $maxDownloads > 0 ? round(($row->downloads / $maxDownloads) * 100) : 0;

            $packages[] = [
                'rank' => $index + 1,
                'id' => $row->pid,
                'title' => $post->post_title,
                'url' => get_permalink($row->pid),
                'edit_url' => admin_url('post.php?post=' . $row->pid . '&action=edit'),
                'downloads' => (int) $row->downloads,
                'total_downloads' => $totalDownloads,
                'bar_width' => $barWidth,
            ];
        }

        return $packages;
    }

    /**
     * Get trending packages (biggest growth)
     *
     * @param int $limit
     * @return array
     */
    public function getTrendingPackages(int $limit = 5): array
    {
        global $wpdb;

        // Get downloads for current period by package
        $currentData = $wpdb->get_results($wpdb->prepare(
            "SELECT pid, COUNT(*) as downloads
             FROM {$wpdb->prefix}ahm_download_stats
             WHERE timestamp >= %d AND timestamp <= %d
             GROUP BY pid",
            $this->startDate,
            $this->endDate
        ), OBJECT_K);

        // Get downloads for previous period by package
        $previousData = $wpdb->get_results($wpdb->prepare(
            "SELECT pid, COUNT(*) as downloads
             FROM {$wpdb->prefix}ahm_download_stats
             WHERE timestamp >= %d AND timestamp <= %d
             GROUP BY pid",
            $this->previousStartDate,
            $this->previousEndDate
        ), OBJECT_K);

        $trending = [];

        foreach ($currentData as $pid => $current) {
            $currentCount = (int) $current->downloads;
            $previousCount = isset($previousData[$pid]) ? (int) $previousData[$pid]->downloads : 0;

            // Calculate growth percentage
            if ($previousCount > 0) {
                $growth = (($currentCount - $previousCount) / $previousCount) * 100;
            } else if ($currentCount > 0) {
                $growth = 100; // New downloads (was 0, now has some)
            } else {
                continue;
            }

            // Only include packages with positive growth
            if ($growth > 0) {
                $trending[$pid] = [
                    'pid' => $pid,
                    'current' => $currentCount,
                    'previous' => $previousCount,
                    'growth' => $growth,
                ];
            }
        }

        // Sort by growth percentage
        uasort($trending, function ($a, $b) {
            return $b['growth'] <=> $a['growth'];
        });

        // Limit and enrich with post data
        $trending = array_slice($trending, 0, $limit, true);
        $packages = [];

        foreach ($trending as $item) {
            $post = get_post($item['pid']);
            if (!$post) continue;

            $packages[] = [
                'id' => $item['pid'],
                'title' => $post->post_title,
                'url' => get_permalink($item['pid']),
                'current_downloads' => $item['current'],
                'previous_downloads' => $item['previous'],
                'growth' => round($item['growth'], 1),
                'growth_text' => '+' . round($item['growth'], 1) . '%',
            ];
        }

        return $packages;
    }

    /**
     * Get user activity statistics
     *
     * @return array
     */
    public function getUserActivity(): array
    {
        global $wpdb;

        // New users registered this period
        $newUsers = (int) $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM {$wpdb->users}
             WHERE user_registered >= %s AND user_registered <= %s",
            date('Y-m-d H:i:s', $this->startDate),
            date('Y-m-d H:i:s', $this->endDate)
        ));

        // Unique downloaders (registered users)
        $registeredDownloaders = (int) $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(DISTINCT uid) FROM {$wpdb->prefix}ahm_download_stats
             WHERE timestamp >= %d AND timestamp <= %d AND uid > 0",
            $this->startDate,
            $this->endDate
        ));

        // Total unique downloaders (by IP or user)
        $totalDownloaders = (int) $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(DISTINCT COALESCE(NULLIF(uid, 0), ip))
             FROM {$wpdb->prefix}ahm_download_stats
             WHERE timestamp >= %d AND timestamp <= %d",
            $this->startDate,
            $this->endDate
        ));

        $guestDownloaders = $totalDownloaders - $registeredDownloaders;

        // Top downloaders
        $topDownloaders = $wpdb->get_results($wpdb->prepare(
            "SELECT uid, COUNT(*) as downloads
             FROM {$wpdb->prefix}ahm_download_stats
             WHERE timestamp >= %d AND timestamp <= %d AND uid > 0
             GROUP BY uid
             ORDER BY downloads DESC
             LIMIT 5",
            $this->startDate,
            $this->endDate
        ));

        $topDownloadersList = [];
        foreach ($topDownloaders as $row) {
            $user = get_user_by('ID', $row->uid);
            if ($user) {
                $topDownloadersList[] = [
                    'id' => $row->uid,
                    'name' => $user->display_name,
                    'email' => $user->user_email,
                    'downloads' => (int) $row->downloads,
                ];
            }
        }

        return [
            'new_users' => $newUsers,
            'unique_downloaders' => $totalDownloaders,
            'registered_downloaders' => $registeredDownloaders,
            'guest_downloaders' => $guestDownloaders,
            'registered_ratio' => $totalDownloaders > 0
                ? round(($registeredDownloaders / $totalDownloaders) * 100, 1)
                : 0,
            'top_downloaders' => $topDownloadersList,
        ];
    }

    /**
     * Get category breakdown
     *
     * @return array
     */
    public function getCategoryBreakdown(): array
    {
        global $wpdb;

        // Get downloads per category
        $results = $wpdb->get_results($wpdb->prepare(
            "SELECT t.term_id, t.name, COUNT(ds.ID) as downloads
             FROM {$wpdb->prefix}ahm_download_stats ds
             INNER JOIN {$wpdb->term_relationships} tr ON ds.pid = tr.object_id
             INNER JOIN {$wpdb->term_taxonomy} tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
             INNER JOIN {$wpdb->terms} t ON tt.term_id = t.term_id
             WHERE ds.timestamp >= %d AND ds.timestamp <= %d
             AND tt.taxonomy = 'wpdmcategory'
             GROUP BY t.term_id
             ORDER BY downloads DESC",
            $this->startDate,
            $this->endDate
        ));

        // Get previous period for comparison
        $previousResults = $wpdb->get_results($wpdb->prepare(
            "SELECT t.term_id, COUNT(ds.ID) as downloads
             FROM {$wpdb->prefix}ahm_download_stats ds
             INNER JOIN {$wpdb->term_relationships} tr ON ds.pid = tr.object_id
             INNER JOIN {$wpdb->term_taxonomy} tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
             INNER JOIN {$wpdb->terms} t ON tt.term_id = t.term_id
             WHERE ds.timestamp >= %d AND ds.timestamp <= %d
             AND tt.taxonomy = 'wpdmcategory'
             GROUP BY t.term_id",
            $this->previousStartDate,
            $this->previousEndDate
        ), OBJECT_K);

        $categories = [];
        $totalDownloads = 0;

        foreach ($results as $row) {
            $totalDownloads += (int) $row->downloads;
        }

        foreach ($results as $row) {
            $previousCount = isset($previousResults[$row->term_id])
                ? (int) $previousResults[$row->term_id]->downloads
                : 0;

            $categories[] = [
                'id' => $row->term_id,
                'name' => $row->name,
                'url' => get_term_link((int) $row->term_id, 'wpdmcategory'),
                'downloads' => (int) $row->downloads,
                'percentage' => $totalDownloads > 0
                    ? round(($row->downloads / $totalDownloads) * 100, 1)
                    : 0,
                'change' => $this->calculateChange((int) $row->downloads, $previousCount),
            ];
        }

        return $categories;
    }

    /**
     * Get revenue summary (if Premium Packages is active)
     *
     * @return array|null
     */
    public function getRevenueSummary(): ?array
    {
        global $wpdb;

        // Check if Premium Packages is active
        if (!class_exists('\\WPDMPremiumPackage') && !function_exists('wpdmpp_effective_price')) {
            return null;
        }

        // Check if orders table exists
        $table = $wpdb->prefix . 'ahm_orders';
        if ($wpdb->get_var("SHOW TABLES LIKE '{$table}'") !== $table) {
            return null;
        }

        // Current period revenue
        $currentRevenue = $wpdb->get_row($wpdb->prepare(
            "SELECT SUM(total) as revenue, COUNT(*) as orders
             FROM {$wpdb->prefix}ahm_orders
             WHERE date >= %d AND date <= %d
             AND payment_status = 'Completed'",
            $this->startDate,
            $this->endDate
        ));

        // Previous period revenue
        $previousRevenue = $wpdb->get_var($wpdb->prepare(
            "SELECT SUM(total) FROM {$wpdb->prefix}ahm_orders
             WHERE date >= %d AND date <= %d
             AND payment_status = 'Completed'",
            $this->previousStartDate,
            $this->previousEndDate
        ));

        $revenue = $currentRevenue ? (float) $currentRevenue->revenue : 0;
        $orders = $currentRevenue ? (int) $currentRevenue->orders : 0;
        $previousRev = (float) $previousRevenue;

        // Top selling products
        $topProducts = $wpdb->get_results($wpdb->prepare(
            "SELECT oi.pid, SUM(oi.price * oi.quantity) as revenue, SUM(oi.quantity) as quantity
             FROM {$wpdb->prefix}ahm_orders o
             INNER JOIN {$wpdb->prefix}ahm_order_items oi ON oi.oid = o.order_id
             WHERE o.date >= %d AND o.date <= %d
             AND o.payment_status = 'Completed'
             GROUP BY oi.pid
             ORDER BY revenue DESC
             LIMIT 5",
            $this->startDate,
            $this->endDate
        ));

        $topProductsList = [];
        foreach ($topProducts as $row) {
            $post = get_post($row->pid);
            if ($post) {
                $topProductsList[] = [
                    'id' => $row->pid,
                    'title' => $post->post_title,
                    'revenue' => (float) $row->revenue,
                    'quantity' => (int) $row->quantity,
                ];
            }
        }

        // Get currency
        $currency = function_exists('wpdmpp_currency_sign') ? wpdmpp_currency_sign() : '$';

        return [
            'revenue' => $revenue,
            'revenue_formatted' => $currency . number_format($revenue, 2),
            'previous_revenue' => $previousRev,
            'change' => $this->calculateChange($revenue, $previousRev),
            'change_class' => $revenue >= $previousRev ? 'positive' : 'negative',
            'orders' => $orders,
            'average_order' => $orders > 0 ? $currency . number_format($revenue / $orders, 2) : $currency . '0.00',
            'top_products' => $topProductsList,
            'currency' => $currency,
        ];
    }

    /**
     * Get storage usage statistics
     *
     * @return array
     */
    public function getStorageUsage(): array
    {
        global $wpdb;

        // Get all packages
        $packages = get_posts([
            'post_type' => 'wpdmpro',
            'posts_per_page' => -1,
            'post_status' => 'publish',
            'fields' => 'ids',
        ]);

        $totalSize = 0;
        $fileCount = 0;
        $largestPackages = [];

        foreach ($packages as $pid) {
            $files = get_post_meta($pid, '__wpdm_files', true);
            if (!is_array($files)) continue;

            $packageSize = 0;
            foreach ($files as $file) {
                $absPath = WPDM()->fileSystem->absPath($file);
                if (file_exists($absPath)) {
                    $size = filesize($absPath);
                    $packageSize += $size;
                    $fileCount++;
                }
            }

            if ($packageSize > 0) {
                $largestPackages[$pid] = $packageSize;
                $totalSize += $packageSize;
            }
        }

        // Sort and get top 5 largest
        arsort($largestPackages);
        $largestPackages = array_slice($largestPackages, 0, 5, true);

        $largest = [];
        foreach ($largestPackages as $pid => $size) {
            $post = get_post($pid);
            if ($post) {
                $largest[] = [
                    'id' => $pid,
                    'title' => $post->post_title,
                    'size' => $this->formatBytes($size),
                    'size_bytes' => $size,
                ];
            }
        }

        // New packages this period
        $newPackages = (int) $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM {$wpdb->posts}
             WHERE post_type = 'wpdmpro'
             AND post_status = 'publish'
             AND post_date >= %s AND post_date <= %s",
            date('Y-m-d H:i:s', $this->startDate),
            date('Y-m-d H:i:s', $this->endDate)
        ));

        return [
            'total_size' => $this->formatBytes($totalSize),
            'total_size_bytes' => $totalSize,
            'file_count' => $fileCount,
            'package_count' => count($packages),
            'new_packages' => $newPackages,
            'largest_packages' => $largest,
        ];
    }

    /**
     * Generate the complete report HTML
     *
     * @param array $sections List of sections to include
     * @return string
     */
    public function generateReport(array $sections): string
    {
        $data = $this->collectData($sections);
        return $this->renderEmailContent($data, $sections);
    }

    /**
     * Collect all report data
     *
     * @param array $sections
     * @return array
     */
    public function collectData(array $sections): array
    {
        $data = [
            'frequency' => $this->frequency,
            'date_range' => $this->getDateRangeText(),
            'period_label' => $this->frequency === 'monthly' ? __('Monthly', 'download-manager') : __('Weekly', 'download-manager'),
        ];

        if (in_array('download_summary', $sections)) {
            $data['download_summary'] = $this->getDownloadSummary();
        }

        if (in_array('top_packages', $sections)) {
            $data['top_packages'] = $this->getTopPackages();
        }

        if (in_array('trending_packages', $sections)) {
            $data['trending_packages'] = $this->getTrendingPackages();
        }

        if (in_array('user_activity', $sections)) {
            $data['user_activity'] = $this->getUserActivity();
        }

        if (in_array('category_breakdown', $sections)) {
            $data['category_breakdown'] = $this->getCategoryBreakdown();
        }

        if (in_array('revenue_summary', $sections)) {
            $revenueSummary = $this->getRevenueSummary();
            if ($revenueSummary !== null) {
                $data['revenue_summary'] = $revenueSummary;
            }
        }

        if (in_array('storage_usage', $sections)) {
            $data['storage_usage'] = $this->getStorageUsage();
        }

        return $data;
    }

    /**
     * Render email content HTML
     *
     * @param array $data
     * @param array $sections
     * @return string
     */
    private function renderEmailContent(array $data, array $sections): string
    {
        ob_start();
        include __DIR__ . '/views/email-templates/activity-report-content.php';
        return ob_get_clean();
    }

    /**
     * Send the activity report email
     *
     * @param array $recipients
     * @param array $sections
     * @return bool
     */
    public function sendReport(array $recipients, array $sections = []): bool
    {
        if (empty($recipients)) {
            return false;
        }

        if (empty($sections)) {
            $sections = get_option('__wpdm_activity_report_sections', [
                'download_summary',
                'top_packages',
                'user_activity',
            ]);
        }

        $data = $this->collectData($sections);
        $reportContent = $this->renderEmailContent($data, $sections);

        $params = [
            'report_period' => $data['period_label'],
            'date_range' => $data['date_range'],
            'report_content' => $reportContent,
            'settings_url' => admin_url('edit.php?post_type=wpdmpro&page=settings&tab=activity-reports'),
        ];

        $success = true;
        foreach ($recipients as $email) {
            $params['to_email'] = $email;
            $params['recipient_email'] = $email;

            $result = Email::send('activity-report', $params);
            if (!$result) {
                $success = false;
            }
        }

        return $success;
    }

    /**
     * Get human-readable date range text
     *
     * @return string
     */
    public function getDateRangeText(): string
    {
        $format = get_option('date_format');
        return date_i18n($format, $this->startDate) . ' - ' . date_i18n($format, $this->endDate);
    }

    /**
     * Calculate percentage change between two values
     *
     * @param float|int $current
     * @param float|int $previous
     * @return string
     */
    private function calculateChange($current, $previous): string
    {
        if ($previous == 0) {
            if ($current > 0) {
                return '+100%';
            }
            return '0%';
        }

        $change = (($current - $previous) / $previous) * 100;
        $prefix = $change >= 0 ? '+' : '';
        return $prefix . round($change, 1) . '%';
    }

    /**
     * Format bytes to human readable
     *
     * @param int $bytes
     * @return string
     */
    private function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);

        return round($bytes, 2) . ' ' . $units[$pow];
    }

    /**
     * Get start date timestamp
     *
     * @return int
     */
    public function getStartDate(): int
    {
        return $this->startDate;
    }

    /**
     * Get end date timestamp
     *
     * @return int
     */
    public function getEndDate(): int
    {
        return $this->endDate;
    }
}
