<?php
/**
 * WPDM Cron Job Manager
 *
 * Manages scheduled jobs with support for queues, priorities, retries, and locking.
 *
 * @package WPDM\__
 * @since 7.0.1
 */

namespace WPDM\__;

use WPDM\__\Jobs\JobBuilder;
use WPDM\__\Jobs\JobHandler;

class CronJob
{
    /**
     * Singleton instance
     *
     * @var self|null
     */
    private static $instance;

    /**
     * Registered job handlers (whitelist for security)
     *
     * @var array
     */
    private static $registeredHandlers = [];

    /**
     * Current worker ID for job locking
     *
     * @var string|null
     */
    private static $workerId;

    /**
     * Job statuses
     */
    const STATUS_PENDING = 'pending';
    const STATUS_RUNNING = 'running';
    const STATUS_COMPLETED = 'completed';
    const STATUS_FAILED = 'failed';
    const STATUS_CANCELLED = 'cancelled';

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
     * Constructor - register default handlers
     */
    public function __construct()
    {
        self::$workerId = wp_generate_uuid4();
        $this->registerDefaultHandlers();
    }

    /**
     * Register default job handlers
     *
     * @return void
     */
    private function registerDefaultHandlers(): void
    {
        // Register built-in handlers
        $builtInHandlers = [
            \WPDM\__\Jobs\CleanupJob::class,
            \WPDM\__\Jobs\DeleteExpiredJob::class,
            \WPDM\__\Jobs\SendEmailJob::class,
            \WPDM\__\Jobs\ActivityReportJob::class,
        ];

        foreach ($builtInHandlers as $handler) {
            if (class_exists($handler)) {
                self::registerHandler($handler);
            }
        }

        // Allow add-ons to register their handlers
        do_action('wpdm_register_job_handlers');
    }

    /**
     * Register a job handler class (whitelist)
     *
     * @param string $handlerClass Fully qualified class name
     * @return void
     */
    public static function registerHandler(string $handlerClass): void
    {
        if (!in_array($handlerClass, self::$registeredHandlers, true)) {
            self::$registeredHandlers[] = $handlerClass;
        }
    }

    /**
     * Check if a handler is registered (whitelisted)
     *
     * @param string $handlerClass
     * @return bool
     */
    public static function isHandlerRegistered(string $handlerClass): bool
    {
        return in_array($handlerClass, self::$registeredHandlers, true);
    }

    /**
     * Get all registered handlers
     *
     * @return array
     */
    public static function getRegisteredHandlers(): array
    {
        return self::$registeredHandlers;
    }

    /**
     * Get the cron key for authentication
     *
     * @return string
     */
    public function cronKey(): string
    {
        $key = get_option('__wpdm_cron_key');
        if (!$key) {
            $key = wp_generate_password(32, false);
            update_option('__wpdm_cron_key', $key);
        }
        return $key;
    }

    /**
     * Create a job builder for fluent API
     *
     * Usage:
     *   CronJob::dispatch(MyJob::class)->withData(['foo' => 'bar'])->delay(3600)->create();
     *
     * @param string $handler Job handler class name
     * @return JobBuilder
     */
    public static function dispatch(string $handler): JobBuilder
    {
        return new JobBuilder($handler);
    }

    /**
     * Create a new job
     *
     * @param string $type Job handler class name
     * @param array $data Job payload
     * @param int $executeAt Unix timestamp when to execute
     * @param int $repeatExecution Number of times to repeat (0 = infinite)
     * @param int $interval Seconds between repeats
     * @param string $queue Queue name
     * @param int $priority Priority (1-10)
     * @param int $maxAttempts Maximum retry attempts
     * @param string|null $uniqueCode Unique code to prevent duplicates
     * @return int|false Job ID on success, false on failure
     */
    public static function create(
        string $type,
        array $data = [],
        int $executeAt = 0,
        int $repeatExecution = 1,
        int $interval = 0,
        string $queue = 'default',
        int $priority = 5,
        int $maxAttempts = 3,
        ?string $uniqueCode = null
    ) {
        global $wpdb;

        // Validate handler is registered
        if (!self::isHandlerRegistered($type)) {
            error_log("[WPDM CronJob] Attempted to create job with unregistered handler: {$type}");
            return false;
        }

        // Generate unique code if not provided
        if ($uniqueCode === null) {
            $uniqueCode = md5($type . $executeAt . wp_json_encode($data) . microtime(true));
        }

        // Check for duplicate job with same unique code
        $existing = $wpdb->get_var($wpdb->prepare(
            "SELECT ID FROM {$wpdb->prefix}ahm_cron_jobs WHERE code = %s AND status IN ('pending', 'running')",
            $uniqueCode
        ));

        if ($existing) {
            return (int) $existing; // Return existing job ID
        }

        $executeAt = $executeAt ?: time();

        $result = $wpdb->insert(
            "{$wpdb->prefix}ahm_cron_jobs",
            [
                'code' => $uniqueCode,
                'type' => $type,
                'queue' => $queue,
                'priority' => max(1, min(10, $priority)),
                'data' => wp_json_encode($data),
                'status' => self::STATUS_PENDING,
                'attempts' => 0,
                'max_attempts' => max(1, $maxAttempts),
                'execute_at' => $executeAt,
                'repeat_execution' => max(0, $repeatExecution),
                'execution_count' => 0,
                'interval_seconds' => max(0, $interval),
                'created_at' => time(),
                'created_by' => get_current_user_id(),
            ],
            ['%s', '%s', '%s', '%d', '%s', '%s', '%d', '%d', '%d', '%d', '%d', '%d', '%d', '%d']
        );

        if ($result === false) {
            error_log("[WPDM CronJob] Failed to create job: " . $wpdb->last_error);
            return false;
        }

        return $wpdb->insert_id;
    }

    /**
     * Delete a job by ID
     *
     * @param int $id Job ID
     * @return bool
     */
    public static function delete(int $id): bool
    {
        global $wpdb;
        return $wpdb->delete(
            "{$wpdb->prefix}ahm_cron_jobs",
            ['ID' => $id],
            ['%d']
        ) !== false;
    }

    /**
     * Cancel a pending job
     *
     * @param int $id Job ID
     * @return bool
     */
    public static function cancel(int $id): bool
    {
        global $wpdb;
        return $wpdb->update(
            "{$wpdb->prefix}ahm_cron_jobs",
            ['status' => self::STATUS_CANCELLED],
            ['ID' => $id, 'status' => self::STATUS_PENDING],
            ['%s'],
            ['%d', '%s']
        ) !== false;
    }

    /**
     * Retry a failed job
     *
     * @param int $id Job ID
     * @return bool
     */
    public static function retry(int $id): bool
    {
        global $wpdb;
        return $wpdb->update(
            "{$wpdb->prefix}ahm_cron_jobs",
            [
                'status' => self::STATUS_PENDING,
                'attempts' => 0,
                'execute_at' => time(),
                'error_message' => null,
                'locked_by' => null,
                'locked_at' => null,
            ],
            ['ID' => $id],
            ['%s', '%d', '%d', '%s', '%s', '%s'],
            ['%d']
        ) !== false;
    }

    /**
     * Execute all pending jobs (limited batch)
     *
     * @param int $limit Maximum jobs to process
     * @param string|null $queue Specific queue to process (null = all)
     * @return array Execution results
     */
    public function executeAll(int $limit = 10, ?string $queue = null): array
    {
        global $wpdb;

        $results = [
            'processed' => 0,
            'succeeded' => 0,
            'failed' => 0,
            'jobs' => [],
        ];

        $time = time();

        // Build query
        $sql = "SELECT * FROM {$wpdb->prefix}ahm_cron_jobs
                WHERE status = %s
                AND execute_at <= %d
                AND locked_by IS NULL";
        $params = [self::STATUS_PENDING, $time];

        if ($queue !== null) {
            $sql .= " AND queue = %s";
            $params[] = $queue;
        }

        $sql .= " ORDER BY priority DESC, execute_at ASC LIMIT %d";
        $params[] = $limit;

        $jobs = $wpdb->get_results($wpdb->prepare($sql, ...$params));

        foreach ($jobs as $job) {
            $result = $this->execute($job);
            $results['processed']++;

            if ($result['success']) {
                $results['succeeded']++;
            } else {
                $results['failed']++;
            }

            $results['jobs'][] = $result;
        }

        return $results;
    }

    /**
     * Execute a single job
     *
     * @param object|int $job Job object or ID
     * @return array Result with 'success', 'message', 'job_id' keys
     */
    public function execute($job): array
    {
        global $wpdb;

        // Fetch job if ID provided
        if (is_int($job) || is_numeric($job)) {
            $job = $wpdb->get_row($wpdb->prepare(
                "SELECT * FROM {$wpdb->prefix}ahm_cron_jobs WHERE ID = %d",
                (int) $job
            ));
        }

        if (!$job) {
            return ['success' => false, 'message' => 'Job not found', 'job_id' => 0];
        }

        // Try to acquire lock
        if (!$this->acquireLock($job)) {
            return ['success' => false, 'message' => 'Could not acquire lock', 'job_id' => $job->ID];
        }

        // Validate handler is registered
        if (!self::isHandlerRegistered($job->type)) {
            $this->markFailed($job, "Unregistered handler: {$job->type}");
            return ['success' => false, 'message' => 'Unregistered handler', 'job_id' => $job->ID];
        }

        // Validate handler class exists
        if (!class_exists($job->type)) {
            $this->markFailed($job, "Handler class not found: {$job->type}");
            return ['success' => false, 'message' => 'Handler class not found', 'job_id' => $job->ID];
        }

        // Execute the job
        try {
            $data = json_decode($job->data);
            $handler = new $job->type($data, $job);

            if (!($handler instanceof JobHandler)) {
                throw new \Exception("Handler must implement JobHandler interface");
            }

            $success = $handler->handle($data);

            if ($success) {
                $this->markCompleted($job);
                return ['success' => true, 'message' => 'Job completed', 'job_id' => $job->ID];
            } else {
                throw new \Exception("Job handler returned false");
            }
        } catch (\Throwable $e) {
            return $this->handleFailure($job, $e);
        }
    }

    /**
     * Acquire a lock on a job to prevent concurrent execution
     *
     * @param object $job
     * @return bool
     */
    private function acquireLock(object $job): bool
    {
        global $wpdb;

        $affected = $wpdb->query($wpdb->prepare(
            "UPDATE {$wpdb->prefix}ahm_cron_jobs
             SET locked_by = %s, locked_at = %d, status = %s, started_at = %d
             WHERE ID = %d AND locked_by IS NULL AND status = %s",
            self::$workerId,
            time(),
            self::STATUS_RUNNING,
            time(),
            $job->ID,
            self::STATUS_PENDING
        ));

        return $affected > 0;
    }

    /**
     * Release lock on a job
     *
     * @param object $job
     * @return void
     */
    private function releaseLock(object $job): void
    {
        global $wpdb;

        $wpdb->update(
            "{$wpdb->prefix}ahm_cron_jobs",
            ['locked_by' => null, 'locked_at' => null],
            ['ID' => $job->ID],
            ['%s', '%s'],
            ['%d']
        );
    }

    /**
     * Mark job as completed
     *
     * @param object $job
     * @return void
     */
    private function markCompleted(object $job): void
    {
        global $wpdb;

        $executionCount = $job->execution_count + 1;
        $repeatExecution = (int) $job->repeat_execution;

        // Check if job should repeat
        if ($repeatExecution === 0 || $executionCount < $repeatExecution) {
            // Schedule next execution
            $nextExecuteAt = time() + (int) $job->interval_seconds;

            $wpdb->update(
                "{$wpdb->prefix}ahm_cron_jobs",
                [
                    'status' => self::STATUS_PENDING,
                    'execution_count' => $executionCount,
                    'execute_at' => $nextExecuteAt,
                    'attempts' => 0,
                    'locked_by' => null,
                    'locked_at' => null,
                    'completed_at' => time(),
                    'error_message' => null,
                ],
                ['ID' => $job->ID],
                ['%s', '%d', '%d', '%d', '%s', '%s', '%d', '%s'],
                ['%d']
            );
        } else {
            // Job fully completed
            $wpdb->update(
                "{$wpdb->prefix}ahm_cron_jobs",
                [
                    'status' => self::STATUS_COMPLETED,
                    'execution_count' => $executionCount,
                    'locked_by' => null,
                    'locked_at' => null,
                    'completed_at' => time(),
                ],
                ['ID' => $job->ID],
                ['%s', '%d', '%s', '%s', '%d'],
                ['%d']
            );
        }
    }

    /**
     * Handle job failure with retry logic
     *
     * @param object $job
     * @param \Throwable $e
     * @return array
     */
    private function handleFailure(object $job, \Throwable $e): array
    {
        global $wpdb;

        $attempts = (int) $job->attempts + 1;
        $maxAttempts = (int) $job->max_attempts;

        // Notify handler of failure
        try {
            if (class_exists($job->type)) {
                $data = json_decode($job->data);
                $handler = new $job->type($data, $job);
                if ($handler instanceof JobHandler) {
                    $handler->failed($e, $data);
                }
            }
        } catch (\Throwable $handlerError) {
            error_log("[WPDM CronJob] Error in failure handler: " . $handlerError->getMessage());
        }

        $errorMessage = sprintf(
            "%s in %s:%d\nStack trace:\n%s",
            $e->getMessage(),
            $e->getFile(),
            $e->getLine(),
            $e->getTraceAsString()
        );

        if ($attempts < $maxAttempts) {
            // Schedule retry with exponential backoff
            $retryDelay = min(3600, pow(2, $attempts) * 60); // Max 1 hour
            $nextRetryAt = time() + $retryDelay;

            $wpdb->update(
                "{$wpdb->prefix}ahm_cron_jobs",
                [
                    'status' => self::STATUS_PENDING,
                    'attempts' => $attempts,
                    'execute_at' => $nextRetryAt,
                    'next_retry_at' => $nextRetryAt,
                    'locked_by' => null,
                    'locked_at' => null,
                    'error_message' => $errorMessage,
                ],
                ['ID' => $job->ID],
                ['%s', '%d', '%d', '%d', '%s', '%s', '%s'],
                ['%d']
            );

            return [
                'success' => false,
                'message' => "Job failed, retry {$attempts}/{$maxAttempts} scheduled",
                'job_id' => $job->ID,
            ];
        } else {
            // Max attempts reached, mark as failed
            $this->markFailed($job, $errorMessage);

            return [
                'success' => false,
                'message' => "Job failed after {$attempts} attempts",
                'job_id' => $job->ID,
            ];
        }
    }

    /**
     * Mark job as permanently failed
     *
     * @param object $job
     * @param string $errorMessage
     * @return void
     */
    private function markFailed(object $job, string $errorMessage): void
    {
        global $wpdb;

        $wpdb->update(
            "{$wpdb->prefix}ahm_cron_jobs",
            [
                'status' => self::STATUS_FAILED,
                'locked_by' => null,
                'locked_at' => null,
                'error_message' => $errorMessage,
                'completed_at' => time(),
            ],
            ['ID' => $job->ID],
            ['%s', '%s', '%s', '%s', '%d'],
            ['%d']
        );
    }

    /**
     * Get all jobs with optional filtering
     *
     * @param array $args Filter arguments: status, queue, limit, offset
     * @return array
     */
    public static function getAll(array $args = []): array
    {
        global $wpdb;

        $defaults = [
            'status' => null,
            'queue' => null,
            'limit' => 100,
            'offset' => 0,
            'orderby' => 'created_at',
            'order' => 'DESC',
        ];

        $args = wp_parse_args($args, $defaults);

        $sql = "SELECT * FROM {$wpdb->prefix}ahm_cron_jobs WHERE 1=1";
        $params = [];

        if ($args['status']) {
            $sql .= " AND status = %s";
            $params[] = $args['status'];
        }

        if ($args['queue']) {
            $sql .= " AND queue = %s";
            $params[] = $args['queue'];
        }

        $allowedOrderBy = ['created_at', 'execute_at', 'priority', 'ID'];
        $orderby = in_array($args['orderby'], $allowedOrderBy) ? $args['orderby'] : 'created_at';
        $order = strtoupper($args['order']) === 'ASC' ? 'ASC' : 'DESC';

        $sql .= " ORDER BY {$orderby} {$order}";
        $sql .= " LIMIT %d OFFSET %d";
        $params[] = (int) $args['limit'];
        $params[] = (int) $args['offset'];

        if (!empty($params)) {
            $sql = $wpdb->prepare($sql, ...$params);
        }

        return $wpdb->get_results($sql);
    }

    /**
     * Get job by ID
     *
     * @param int $id
     * @return object|null
     */
    public static function get(int $id): ?object
    {
        global $wpdb;

        return $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}ahm_cron_jobs WHERE ID = %d",
            $id
        ));
    }

    /**
     * Get job counts by status
     *
     * @return array
     */
    public static function getCounts(): array
    {
        global $wpdb;

        $results = $wpdb->get_results(
            "SELECT status, COUNT(*) as count FROM {$wpdb->prefix}ahm_cron_jobs GROUP BY status"
        );

        $counts = [
            'total' => 0,
            self::STATUS_PENDING => 0,
            self::STATUS_RUNNING => 0,
            self::STATUS_COMPLETED => 0,
            self::STATUS_FAILED => 0,
            self::STATUS_CANCELLED => 0,
        ];

        foreach ($results as $row) {
            $counts[$row->status] = (int) $row->count;
            $counts['total'] += (int) $row->count;
        }

        return $counts;
    }

    /**
     * Clean up old completed/cancelled/failed jobs
     *
     * @param int $olderThanDays Delete jobs older than this many days
     * @return int Number of deleted jobs
     */
    public static function cleanup(int $olderThanDays = 30): int
    {
        global $wpdb;

        $cutoff = time() - ($olderThanDays * 86400);

        return $wpdb->query($wpdb->prepare(
            "DELETE FROM {$wpdb->prefix}ahm_cron_jobs
             WHERE status IN (%s, %s, %s) AND completed_at < %d",
            self::STATUS_COMPLETED,
            self::STATUS_FAILED,
            self::STATUS_CANCELLED,
            $cutoff
        ));
    }

    /**
     * Release stale locks (jobs that have been running too long)
     *
     * @param int $staleLockMinutes Minutes after which a lock is considered stale
     * @return int Number of released locks
     */
    public static function releaseStale(int $staleLockMinutes = 30): int
    {
        global $wpdb;

        $cutoff = time() - ($staleLockMinutes * 60);

        return $wpdb->query($wpdb->prepare(
            "UPDATE {$wpdb->prefix}ahm_cron_jobs
             SET status = %s, locked_by = NULL, locked_at = NULL
             WHERE status = %s AND locked_at < %d",
            self::STATUS_PENDING,
            self::STATUS_RUNNING,
            $cutoff
        ));
    }

    /**
     * Pause all jobs in a queue
     *
     * @param string $queue
     * @return int Number of affected jobs
     */
    public static function pauseQueue(string $queue): int
    {
        global $wpdb;

        return $wpdb->query($wpdb->prepare(
            "UPDATE {$wpdb->prefix}ahm_cron_jobs
             SET status = %s
             WHERE queue = %s AND status = %s",
            self::STATUS_CANCELLED,
            $queue,
            self::STATUS_PENDING
        ));
    }
}
