<?php
/**
 * Delete Expired Packages Job
 *
 * Handles deletion/trashing of expired packages.
 *
 * @package WPDM\__\Jobs
 * @since 7.0.1
 */

namespace WPDM\__\Jobs;

class DeleteExpiredJob extends Job
{
    /**
     * Execute the delete expired job
     *
     * @param object|array $data Job payload
     * @return bool
     */
    public function handle($data): bool
    {
        // Check if feature is enabled
        if (!(int) get_option('__wpdm_delete_expired', 0)) {
            $this->log('Delete expired packages feature is disabled');
            return true; // Not an error, just disabled
        }

        $this->log('Starting delete expired packages job');

        $deleted = $this->deleteExpiredPackages();
        $this->log("Trashed {$deleted} expired packages");

        // Send notification if packages were deleted
        if ($deleted > 0 && $this->get('notify_admin', false)) {
            $this->notifyAdmin($deleted);
        }

        return true;
    }

    /**
     * Delete expired packages
     *
     * @return int Number of deleted packages
     */
    private function deleteExpiredPackages(): int
    {
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

            if (!$expireTime) {
                continue;
            }

            if ($expireTime < time() && get_post_status($item->post_id) === 'publish') {
                // Check if we should permanently delete or just trash
                $permanentDelete = $this->get('permanent_delete', false);

                if ($permanentDelete) {
                    wp_delete_post($item->post_id, true);
                } else {
                    wp_trash_post($item->post_id);
                }

                $deleted++;

                // Log individual deletion
                $this->log("Deleted/trashed package #{$item->post_id} (expired: {$item->expire_date})");
            }
        }

        return $deleted;
    }

    /**
     * Send notification to admin about deleted packages
     *
     * @param int $count Number of deleted packages
     * @return void
     */
    private function notifyAdmin(int $count): void
    {
        $adminEmail = get_option('admin_email');
        $siteName = get_bloginfo('name');

        $subject = sprintf(
            '[%s] %d expired package(s) have been removed',
            $siteName,
            $count
        );

        $message = sprintf(
            "Hello,\n\n%d expired package(s) have been automatically removed from your site.\n\nThis is an automated notification from WordPress Download Manager.\n\nBest regards,\n%s",
            $count,
            $siteName
        );

        wp_mail($adminEmail, $subject, $message);
    }

    /**
     * Get job name
     *
     * @return string
     */
    public static function getName(): string
    {
        return 'Delete Expired';
    }

    /**
     * Get job description
     *
     * @return string
     */
    public static function getDescription(): string
    {
        return 'Removes expired packages from the site';
    }
}
