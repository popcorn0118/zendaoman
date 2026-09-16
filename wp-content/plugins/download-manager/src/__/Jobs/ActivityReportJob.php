<?php
/**
 * Activity Report Job Handler
 *
 * Sends periodic activity summary emails to administrators.
 *
 * @package WPDM\__\Jobs
 * @since 7.0.2
 */

namespace WPDM\__\Jobs;

use WPDM\__\ActivityReport;

class ActivityReportJob extends Job
{
    /**
     * Execute the activity report job
     *
     * @param object|array $data Job payload
     * @return bool
     */
    public function handle($data): bool
    {
        // Check if feature is enabled
        if (!(int) get_option('__wpdm_activity_report_enabled', 0)) {
            $this->log('Activity reports feature is disabled');
            return true; // Not an error, just disabled
        }

        $this->log('Starting activity report job');

        // Get configuration from options (fallback to job data)
        $frequency = get_option('__wpdm_activity_report_frequency', $this->get('frequency', 'weekly'));
        $sections = get_option('__wpdm_activity_report_sections', $this->get('sections', ['download_summary', 'top_packages']));
        $recipients = $this->getRecipients();

        if (empty($recipients)) {
            $this->log('No recipients configured for activity report');
            return true; // Not an error, just no recipients
        }

        // Ensure sections is an array
        if (!is_array($sections)) {
            $sections = ['download_summary', 'top_packages'];
        }

        // Generate and send report
        try {
            $report = new ActivityReport($frequency);
            $success = $report->sendReport($recipients, $sections);

            if ($success) {
                $this->log('Activity report sent successfully to ' . count($recipients) . ' recipient(s)');
            } else {
                $this->log('Failed to send activity report to some recipients', 'warning');
            }

            return true;
        } catch (\Throwable $e) {
            $this->log('Error generating activity report: ' . $e->getMessage(), 'error');
            throw $e;
        }
    }

    /**
     * Get recipients from options
     *
     * @return array
     */
    private function getRecipients(): array
    {
        $recipients = [];

        // Check if admin email should be included
        $includeAdmin = (int) get_option('__wpdm_activity_report_admin', 1);
        if ($includeAdmin) {
            $recipients[] = get_option('admin_email');
        }

        // Get additional emails
        $additionalEmails = get_option('__wpdm_activity_report_emails', '');
        if (!empty($additionalEmails)) {
            $emails = array_map('trim', explode(',', $additionalEmails));
            foreach ($emails as $email) {
                if (is_email($email) && !in_array($email, $recipients)) {
                    $recipients[] = $email;
                }
            }
        }

        // Fallback to job data if no options configured
        if (empty($recipients)) {
            $dataRecipients = $this->get('recipients', []);
            if (is_array($dataRecipients)) {
                $recipients = $dataRecipients;
            }
        }

        return $recipients;
    }

    /**
     * Get job name
     *
     * @return string
     */
    public static function getName(): string
    {
        return 'Activity Report';
    }

    /**
     * Get job description
     *
     * @return string
     */
    public static function getDescription(): string
    {
        return 'Sends periodic activity summary emails to administrators';
    }

    /**
     * Schedule the activity report job
     *
     * @return int|false Job ID on success, false on failure
     */
    public static function schedule()
    {
        // Cancel any existing job first
        self::cancelExisting();

        // Check if enabled
        if (!(int) get_option('__wpdm_activity_report_enabled', 0)) {
            return false;
        }

        $frequency = get_option('__wpdm_activity_report_frequency', 'weekly');
        $day = (int) get_option('__wpdm_activity_report_day', $frequency === 'weekly' ? 1 : 1);
        $hour = (int) get_option('__wpdm_activity_report_hour', 9);

        // Calculate next execution time
        $nextExecuteAt = self::calculateNextExecutionTime($frequency, $day, $hour);

        // Calculate interval in seconds
        $interval = $frequency === 'monthly' ? 30 * 86400 : 7 * 86400;

        // Create the job
        return \WPDM\__\CronJob::create(
            self::class,
            [
                'frequency' => $frequency,
                'scheduled_at' => time(),
            ],
            $nextExecuteAt,
            0, // Repeat indefinitely
            $interval,
            'reports',
            5,
            3,
            'wpdm_activity_report' // Unique code
        );
    }

    /**
     * Cancel existing activity report job
     *
     * @return bool
     */
    public static function cancelExisting(): bool
    {
        global $wpdb;

        return $wpdb->query($wpdb->prepare(
            "DELETE FROM {$wpdb->prefix}ahm_cron_jobs
             WHERE type = %s AND status IN ('pending', 'running')",
            self::class
        )) !== false;
    }

    /**
     * Calculate next execution time based on settings
     *
     * @param string $frequency
     * @param int $day
     * @param int $hour
     * @return int Unix timestamp
     */
    private static function calculateNextExecutionTime(string $frequency, int $day, int $hour): int
    {
        $now = time();
        $currentHour = (int) date('G');
        $currentDay = (int) date('N'); // 1 = Monday, 7 = Sunday
        $currentDayOfMonth = (int) date('j');

        if ($frequency === 'monthly') {
            // Monthly: Execute on specific day of month
            $day = max(1, min(28, $day)); // Limit to 28 to avoid issues with short months

            // Try this month first
            $thisMonth = strtotime(date('Y-m-') . sprintf('%02d', $day) . ' ' . sprintf('%02d', $hour) . ':00:00');

            if ($thisMonth > $now) {
                return $thisMonth;
            }

            // Next month
            return strtotime('+1 month', strtotime(date('Y-m-01') . ' ' . sprintf('%02d', $hour) . ':00:00')) + (($day - 1) * 86400);
        } else {
            // Weekly: Execute on specific day of week
            $day = max(1, min(7, $day)); // 1 = Monday, 7 = Sunday

            // Days until target day
            $daysUntil = $day - $currentDay;

            if ($daysUntil < 0) {
                $daysUntil += 7;
            } elseif ($daysUntil === 0 && $currentHour >= $hour) {
                $daysUntil = 7;
            }

            return strtotime('+' . $daysUntil . ' days', strtotime(date('Y-m-d') . ' ' . sprintf('%02d', $hour) . ':00:00'));
        }
    }

    /**
     * Send a test report
     *
     * @param string $email Email address to send test to
     * @return bool
     */
    public static function sendTestReport(string $email): bool
    {
        if (!is_email($email)) {
            return false;
        }

        $frequency = get_option('__wpdm_activity_report_frequency', 'weekly');
        $sections = get_option('__wpdm_activity_report_sections', [
            'download_summary',
            'top_packages',
            'user_activity',
        ]);

        if (!is_array($sections) || empty($sections)) {
            $sections = ['download_summary', 'top_packages', 'user_activity'];
        }

        try {
            $report = new ActivityReport($frequency);
            return $report->sendReport([$email], $sections);
        } catch (\Throwable $e) {
            error_log('[WPDM] Test report failed: ' . $e->getMessage());
            return false;
        }
    }
}
