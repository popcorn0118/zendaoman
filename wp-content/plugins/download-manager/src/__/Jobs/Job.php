<?php
/**
 * Base Job Class
 *
 * Abstract base class for job handlers with common functionality.
 *
 * @package WPDM\__\Jobs
 * @since 7.0.1
 */

namespace WPDM\__\Jobs;

abstract class Job implements JobHandler
{
    /**
     * Job data/payload
     *
     * @var object|array
     */
    protected $data;

    /**
     * Current job record from database
     *
     * @var object|null
     */
    protected $job;

    /**
     * Constructor
     *
     * @param object|array|null $data Job payload
     * @param object|null $job Job record from database
     */
    public function __construct($data = null, $job = null)
    {
        $this->data = $data;
        $this->job = $job;
    }

    /**
     * Handle job failure - default implementation logs the error
     *
     * @param \Throwable $e
     * @param object|array $data
     * @return void
     */
    public function failed(\Throwable $e, $data): void
    {
        error_log(sprintf(
            '[WPDM CronJob] Job %s failed: %s in %s:%d',
            static::class,
            $e->getMessage(),
            $e->getFile(),
            $e->getLine()
        ));
    }

    /**
     * Get job name - default implementation returns class name
     *
     * @return string
     */
    public static function getName(): string
    {
        $class = static::class;
        $parts = explode('\\', $class);
        return end($parts);
    }

    /**
     * Get job description - default implementation
     *
     * @return string
     */
    public static function getDescription(): string
    {
        return '';
    }

    /**
     * Log a message for this job
     *
     * @param string $message
     * @param string $level info|warning|error
     * @return void
     */
    protected function log(string $message, string $level = 'info'): void
    {
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log(sprintf('[WPDM CronJob][%s][%s] %s', $level, static::getName(), $message));
        }
    }

    /**
     * Get a value from job data
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    protected function get(string $key, $default = null)
    {
        if (is_object($this->data)) {
            return $this->data->$key ?? $default;
        }
        if (is_array($this->data)) {
            return $this->data[$key] ?? $default;
        }
        return $default;
    }
}
