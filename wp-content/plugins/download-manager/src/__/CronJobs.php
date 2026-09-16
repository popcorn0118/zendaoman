<?php
/**
 * WPDM Cron Jobs Scheduler
 *
 * Handles WordPress cron scheduling and job execution triggers.
 *
 * @package WPDM\__
 * @since 7.0.1
 */

namespace WPDM\__;

class CronJobs
{
    /**
     * Singleton instance
     *
     * @var self|null
     */
    private static $instance;

    /**
     * Get singleton instance
     *
     * @return self
     */
    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor - setup cron hooks
     */
    public function __construct()
    {
        // Register custom cron intervals
        add_filter('cron_schedules', [$this, 'registerIntervals']);

        // Schedule the main cron event
        add_action('init', [$this, 'scheduleMainCron']);

        // Register cron action handlers
        add_action('__wpdm_cron', [$this, 'runScheduledTasks']);
        add_action('__wpdm_process_jobs', [$this, 'processJobQueue']);

        // HTTP endpoint for external cron triggers
        add_action('init', [$this, 'handleExternalTrigger']);

        // Admin AJAX handlers
        add_action('wp_ajax_wpdm_run_cron', [$this, 'ajaxRunCron']);
        add_action('wp_ajax_wpdm_job_action', [$this, 'ajaxJobAction']);
        add_action('wp_ajax_wpdm_create_sample_jobs', [$this, 'ajaxCreateSampleJobs']);
    }

    /**
     * Register custom cron intervals
     *
     * @param array $schedules
     * @return array
     */
    public function registerIntervals(array $schedules): array
    {
        $schedules['wpdm_every_minute'] = [
            'interval' => 60,
            'display' => __('Every Minute', 'download-manager'),
        ];

        $schedules['wpdm_every_5_minutes'] = [
            'interval' => 300,
            'display' => __('Every 5 Minutes', 'download-manager'),
        ];

        $schedules['wpdm_every_15_minutes'] = [
            'interval' => 900,
            'display' => __('Every 15 Minutes', 'download-manager'),
        ];

        $schedules['wpdm_every_30_minutes'] = [
            'interval' => 1800,
            'display' => __('Every 30 Minutes', 'download-manager'),
        ];

        $schedules['wpdm_hourly'] = [
            'interval' => 3600,
            'display' => __('Hourly', 'download-manager'),
        ];

        $schedules['wpdm_six_hourly'] = [
            'interval' => 21600,
            'display' => __('Every 6 Hours', 'download-manager'),
        ];

        $schedules['wpdm_daily'] = [
            'interval' => 86400,
            'display' => __('Daily', 'download-manager'),
        ];

        return $schedules;
    }

    /**
     * Schedule the main WPDM cron event
     *
     * @return void
     */
    public function scheduleMainCron(): void
    {
        // Main cron for scheduled tasks (cleanup, etc.)
        if (!wp_next_scheduled('__wpdm_cron')) {
            wp_schedule_event(time() + 3600, 'wpdm_six_hourly', '__wpdm_cron');
        }

        // Job queue processor - runs more frequently
        if (!wp_next_scheduled('__wpdm_process_jobs')) {
            wp_schedule_event(time() + 60, 'wpdm_every_5_minutes', '__wpdm_process_jobs');
        }
    }

    /**
     * Run scheduled maintenance tasks
     *
     * @return void
     */
    public function runScheduledTasks(): void
    {
        // Clean up old sessions
        $this->cleanupSessions();

        // Clean up cache files
        $this->cleanupCache();

        // Clean up old completed/failed jobs
        CronJob::cleanup(30);

        // Release stale job locks
        CronJob::releaseStale(30);

        // Allow add-ons to hook into scheduled tasks
        do_action('wpdm_scheduled_tasks');
    }

    /**
     * Process the job queue
     *
     * @return void
     */
    public function processJobQueue(): void
    {
        $cronJob = CronJob::getInstance();
        $results = $cronJob->executeAll(10);

        if ($results['processed'] > 0) {
            error_log(sprintf(
                '[WPDM CronJob] Processed %d jobs: %d succeeded, %d failed',
                $results['processed'],
                $results['succeeded'],
                $results['failed']
            ));
        }

        // Allow add-ons to hook into job processing
        do_action('wpdm_after_process_jobs', $results);
    }

    /**
     * Handle external cron trigger via URL
     *
     * Supports two endpoints:
     * - ?wpdm_cron=1&cronkey=XXX - Process job queue
     * - ?wpdm_task=cleanup&cronkey=XXX - Run specific task
     *
     * @return void
     */
    public function handleExternalTrigger(): void
    {
        $cronKey = wpdm_query_var('cronkey', 'txt');

        // Process job queue
        if (wpdm_query_var('wpdm_cron', 'int') === 1) {
            if (!$this->verifyCronKey($cronKey)) {
                wp_send_json_error(['message' => 'Invalid cron key'], 403);
            }

            $cronJob = CronJob::getInstance();
            $results = $cronJob->executeAll(20);

            do_action('wpdm_cron_job');

            wp_send_json_success([
                'message' => 'Cron executed',
                'processed' => $results['processed'],
                'succeeded' => $results['succeeded'],
                'failed' => $results['failed'],
            ]);
        }

        // Run specific task
        $task = wpdm_query_var('wpdm_task', 'txt');
        if ($task) {
            if (!$this->verifyCronKey($cronKey)) {
                wp_send_json_error(['message' => 'Invalid cron key'], 403);
            }

            $result = $this->runTask($task);
            wp_send_json($result);
        }
    }

    /**
     * Run a specific maintenance task
     *
     * @param string $task Task name
     * @return array Result
     */
    private function runTask(string $task): array
    {
        switch ($task) {
            case 'cleanup':
            case 'cleanup_cache':
                $this->cleanupCache();
                return ['success' => true, 'message' => 'Cache cleaned'];

            case 'cleanup_sessions':
                $deleted = $this->cleanupSessions();
                return ['success' => true, 'message' => "Deleted {$deleted} expired sessions"];

            case 'delete_expired':
                $deleted = $this->deleteExpiredPackages();
                return ['success' => true, 'message' => "Deleted {$deleted} expired packages"];

            case 'release_stale':
                $released = CronJob::releaseStale(30);
                return ['success' => true, 'message' => "Released {$released} stale locks"];

            case 'cleanup_jobs':
                $deleted = CronJob::cleanup(30);
                return ['success' => true, 'message' => "Cleaned up {$deleted} old jobs"];

            default:
                // Allow custom tasks via filter
                $result = apply_filters('wpdm_run_cron_task', null, $task);
                if ($result !== null) {
                    return $result;
                }
                return ['success' => false, 'message' => 'Unknown task'];
        }
    }

    /**
     * Verify cron key for external triggers
     *
     * @param string $providedKey
     * @return bool
     */
    private function verifyCronKey(string $providedKey): bool
    {
        if (empty($providedKey)) {
            return false;
        }

        $storedKey = WPDM()->cronJob->cronKey();
        return hash_equals($storedKey, $providedKey);
    }

    /**
     * Clean up expired sessions
     *
     * @return int Number of deleted sessions
     */
    private function cleanupSessions(): int
    {
        global $wpdb;

        $time = time();
        $deleted = $wpdb->query($wpdb->prepare(
            "DELETE FROM {$wpdb->prefix}ahm_sessions WHERE `expire` < %d",
            $time
        ));

        return $deleted !== false ? $deleted : 0;
    }

    /**
     * Clean up cache files
     *
     * @return void
     */
    private function cleanupCache(): void
    {
        if (!(int) get_option('__wpdm_auto_clean_cache', 0)) {
            return;
        }

        // Delete zip files in cache
        FileSystem::deleteFiles(WPDM_CACHE_DIR, false, '.zip');

        // Delete old txt files (older than 1 hour)
        FileSystem::deleteFiles(WPDM_CACHE_DIR, false, [
            'filetime' => time() - 3600,
            'ext' => '.txt'
        ]);
    }

    /**
     * Delete expired packages
     *
     * @return int Number of deleted packages
     */
    private function deleteExpiredPackages(): int
    {
        if (!(int) get_option('__wpdm_delete_expired', 0)) {
            return 0;
        }

        global $wpdb;

        $results = $wpdb->get_results($wpdb->prepare(
            "SELECT post_id, meta_value as expire_date
             FROM {$wpdb->prefix}postmeta
             WHERE meta_key = %s AND meta_value <> ''",
            '__wpdm_expire_date'
        ));

        $deleted = 0;
        foreach ($results as $item) {
            $expireTime = strtotime($item->expire_date);
            if ($expireTime && $expireTime < time() && get_post_status($item->post_id) === 'publish') {
                wp_trash_post($item->post_id);
                $deleted++;
            }
        }

        return $deleted;
    }

    /**
     * AJAX handler: Run cron manually from admin
     *
     * @return void
     */
    public function ajaxRunCron(): void
    {
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'Permission denied'], 403);
        }

        check_ajax_referer('wpdm_cron_nonce', 'nonce');

        $cronJob = CronJob::getInstance();
        $results = $cronJob->executeAll(20);

        wp_send_json_success([
            'message' => 'Cron executed successfully',
            'results' => $results,
        ]);
    }

    /**
     * AJAX handler: Job actions (retry, cancel, delete)
     *
     * @return void
     */
    public function ajaxJobAction(): void
    {
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'Permission denied'], 403);
        }

        check_ajax_referer('wpdm_job_action', 'nonce');

        $action = wpdm_query_var('job_action', 'txt');
        $jobId = wpdm_query_var('job_id', 'int');

        if (!$jobId) {
            wp_send_json_error(['message' => 'Invalid job ID']);
        }

        switch ($action) {
            case 'retry':
                $success = CronJob::retry($jobId);
                $message = $success ? 'Job scheduled for retry' : 'Failed to retry job';
                break;

            case 'cancel':
                $success = CronJob::cancel($jobId);
                $message = $success ? 'Job cancelled' : 'Failed to cancel job';
                break;

            case 'delete':
                $success = CronJob::delete($jobId);
                $message = $success ? 'Job deleted' : 'Failed to delete job';
                break;

            case 'run_now':
                $cronJob = CronJob::getInstance();
                $result = $cronJob->execute($jobId);
                $success = $result['success'];
                $message = $result['message'];
                break;

            default:
                wp_send_json_error(['message' => 'Invalid action']);
                return;
        }

        if ($success) {
            wp_send_json_success(['message' => $message]);
        } else {
            wp_send_json_error(['message' => $message]);
        }
    }

    /**
     * AJAX handler: Create sample jobs for testing
     *
     * @return void
     */
    public function ajaxCreateSampleJobs(): void
    {
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'Permission denied'], 403);
        }

        check_ajax_referer('wpdm_cron_nonce', 'nonce');

        $jobsCreated = [];

        // 1. Cleanup Job - runs immediately
        $jobId = CronJob::dispatch(\WPDM\__\Jobs\CleanupJob::class)
            ->withData([
                'cleanup_stats' => false,
                'note' => 'Sample cleanup job'
            ])
            ->onQueue('maintenance')
            ->priority(5)
            ->unique('sample_cleanup_' . date('Y-m-d'))
            ->create();

        if ($jobId) {
            $jobsCreated[] = "CleanupJob #{$jobId}";
        }

        // 2. Send Email Job - delayed by 5 minutes
        $jobId = CronJob::dispatch(\WPDM\__\Jobs\SendEmailJob::class)
            ->withData([
                'to' => get_option('admin_email'),
                'subject' => 'WPDM Test Email - ' . date('Y-m-d H:i:s'),
                'message' => '<h2>Hello!</h2><p>This is a test email from the WPDM Cron Job System.</p><p>If you received this email, the job queue is working correctly!</p>',
                'from_name' => get_bloginfo('name'),
            ])
            ->onQueue('emails')
            ->priority(8)
            ->delay(300) // 5 minutes
            ->attempts(2)
            ->unique('sample_email_' . date('Y-m-d-H'))
            ->create();

        if ($jobId) {
            $jobsCreated[] = "SendEmailJob #{$jobId} (delayed 5 min)";
        }

        // 3. Delete Expired Job - scheduled for 1 hour later
        $jobId = CronJob::dispatch(\WPDM\__\Jobs\DeleteExpiredJob::class)
            ->withData([
                'permanent_delete' => false,
                'notify_admin' => true,
                'note' => 'Sample expired packages check'
            ])
            ->onQueue('maintenance')
            ->priority(3)
            ->delay(3600) // 1 hour
            ->unique('sample_delete_expired_' . date('Y-m-d'))
            ->create();

        if ($jobId) {
            $jobsCreated[] = "DeleteExpiredJob #{$jobId} (delayed 1 hour)";
        }

        if (count($jobsCreated) > 0) {
            wp_send_json_success([
                'message' => sprintf(
                    'Created %d sample jobs: %s',
                    count($jobsCreated),
                    implode(', ', $jobsCreated)
                ),
                'jobs' => $jobsCreated,
            ]);
        } else {
            wp_send_json_error([
                'message' => 'No jobs created. They may already exist (duplicate unique codes).',
            ]);
        }
    }

    /**
     * Get the cron URL for external triggers
     *
     * @param string $task Optional specific task
     * @return string
     */
    public static function getCronUrl(string $task = ''): string
    {
        $cronKey = WPDM()->cronJob->cronKey();
        $params = ['cronkey' => $cronKey];

        if ($task) {
            $params['wpdm_task'] = $task;
        } else {
            $params['wpdm_cron'] = 1;
        }

        return add_query_arg($params, home_url('/'));
    }

    /**
     * Check if WordPress cron is disabled
     *
     * @return bool
     */
    public static function isWpCronDisabled(): bool
    {
        return defined('DISABLE_WP_CRON') && DISABLE_WP_CRON;
    }

    /**
     * Get status of scheduled cron events
     *
     * @return array
     */
    public static function getScheduledEvents(): array
    {
        return [
            '__wpdm_cron' => [
                'next_run' => wp_next_scheduled('__wpdm_cron'),
                'interval' => 'wpdm_six_hourly',
                'description' => 'Scheduled maintenance tasks',
            ],
            '__wpdm_process_jobs' => [
                'next_run' => wp_next_scheduled('__wpdm_process_jobs'),
                'interval' => 'wpdm_every_5_minutes',
                'description' => 'Job queue processor',
            ],
        ];
    }
}
