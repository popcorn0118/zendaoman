<?php
/**
 * Job Handler Interface
 *
 * All cron job handlers must implement this interface.
 *
 * @package WPDM\__\Jobs
 * @since 7.0.1
 */

namespace WPDM\__\Jobs;

interface JobHandler
{
    /**
     * Execute the job
     *
     * @param object|array $data Job payload data
     * @return bool True on success, false on failure
     */
    public function handle($data): bool;

    /**
     * Handle job failure
     *
     * Called when the job fails after all retry attempts
     *
     * @param \Throwable $e The exception that caused the failure
     * @param object|array $data Job payload data
     * @return void
     */
    public function failed(\Throwable $e, $data): void;

    /**
     * Get the job display name
     *
     * @return string
     */
    public static function getName(): string;

    /**
     * Get the job description
     *
     * @return string
     */
    public static function getDescription(): string;
}
