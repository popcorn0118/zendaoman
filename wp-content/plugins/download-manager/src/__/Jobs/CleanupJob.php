<?php
/**
 * Cleanup Job
 *
 * Handles cleanup of expired sessions and cache files.
 *
 * @package WPDM\__\Jobs
 * @since 7.0.1
 */

namespace WPDM\__\Jobs;

use WPDM\__\FileSystem;

class CleanupJob extends Job
{
    /**
     * Execute the cleanup job
     *
     * @param object|array $data Job payload
     * @return bool
     */
    public function handle($data): bool
    {
        $this->log('Starting cleanup job');

        // Clean up expired sessions
        $sessionsDeleted = $this->cleanupSessions();
        $this->log("Deleted {$sessionsDeleted} expired sessions");

        // Clean up cache files
        $this->cleanupCache();
        $this->log('Cache cleanup completed');

        // Clean up old download stats (optional, based on settings)
        if ($this->get('cleanup_stats', false)) {
            $statsDeleted = $this->cleanupOldStats();
            $this->log("Deleted {$statsDeleted} old download stats");
        }

        return true;
    }

    /**
     * Clean up expired sessions
     *
     * @return int Number of deleted sessions
     */
    private function cleanupSessions(): int
    {
        global $wpdb;

        $deleted = $wpdb->query($wpdb->prepare(
            "DELETE FROM {$wpdb->prefix}ahm_sessions WHERE `expire` < %d",
            time()
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
        if (!defined('WPDM_CACHE_DIR')) {
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
     * Clean up old download statistics
     *
     * @return int Number of deleted records
     */
    private function cleanupOldStats(): int
    {
        global $wpdb;

        $daysToKeep = $this->get('stats_retention_days', 365);
        $cutoff = time() - ($daysToKeep * 86400);

        $deleted = $wpdb->query($wpdb->prepare(
            "DELETE FROM {$wpdb->prefix}ahm_download_stats WHERE `timestamp` < %d",
            $cutoff
        ));

        return $deleted !== false ? $deleted : 0;
    }

    /**
     * Get job name
     *
     * @return string
     */
    public static function getName(): string
    {
        return 'Cleanup';
    }

    /**
     * Get job description
     *
     * @return string
     */
    public static function getDescription(): string
    {
        return 'Cleans up expired sessions and cache files';
    }
}
