<?php
/**
 * Job Builder
 *
 * Fluent interface for creating and scheduling jobs.
 *
 * @package WPDM\__\Jobs
 * @since 7.0.1
 */

namespace WPDM\__\Jobs;

use WPDM\__\CronJob;

class JobBuilder
{
    /**
     * Job handler class name
     *
     * @var string
     */
    private $handler;

    /**
     * Job payload data
     *
     * @var array
     */
    private $data = [];

    /**
     * Queue name
     *
     * @var string
     */
    private $queue = 'default';

    /**
     * Job priority (1-10, higher = sooner)
     *
     * @var int
     */
    private $priority = 5;

    /**
     * Delay in seconds before execution
     *
     * @var int
     */
    private $delay = 0;

    /**
     * Maximum retry attempts
     *
     * @var int
     */
    private $maxAttempts = 3;

    /**
     * Number of times to repeat (0 = infinite for scheduled jobs)
     *
     * @var int
     */
    private $repeat = 1;

    /**
     * Interval between repeats in seconds
     *
     * @var int
     */
    private $interval = 0;

    /**
     * Unique job code (to prevent duplicates)
     *
     * @var string|null
     */
    private $uniqueCode = null;

    /**
     * Constructor
     *
     * @param string $handler Job handler class name
     */
    public function __construct(string $handler)
    {
        $this->handler = $handler;
    }

    /**
     * Set job payload data
     *
     * @param array $data
     * @return self
     */
    public function withData(array $data): self
    {
        $this->data = $data;
        return $this;
    }

    /**
     * Set queue name
     *
     * @param string $queue
     * @return self
     */
    public function onQueue(string $queue): self
    {
        $this->queue = $queue;
        return $this;
    }

    /**
     * Set job priority (1-10)
     *
     * @param int $priority
     * @return self
     */
    public function priority(int $priority): self
    {
        $this->priority = max(1, min(10, $priority));
        return $this;
    }

    /**
     * Delay execution by seconds
     *
     * @param int $seconds
     * @return self
     */
    public function delay(int $seconds): self
    {
        $this->delay = max(0, $seconds);
        return $this;
    }

    /**
     * Delay execution until a specific timestamp
     *
     * @param int $timestamp
     * @return self
     */
    public function delayUntil(int $timestamp): self
    {
        $this->delay = max(0, $timestamp - time());
        return $this;
    }

    /**
     * Set maximum retry attempts
     *
     * @param int $attempts
     * @return self
     */
    public function attempts(int $attempts): self
    {
        $this->maxAttempts = max(1, $attempts);
        return $this;
    }

    /**
     * Set unique code to prevent duplicate jobs
     *
     * @param string $code
     * @return self
     */
    public function unique(string $code): self
    {
        $this->uniqueCode = $code;
        return $this;
    }

    /**
     * Run every N minutes
     *
     * @param int $minutes
     * @return self
     */
    public function everyMinutes(int $minutes): self
    {
        $this->repeat = 0; // Infinite
        $this->interval = $minutes * 60;
        return $this;
    }

    /**
     * Run hourly
     *
     * @return self
     */
    public function hourly(): self
    {
        return $this->everyMinutes(60);
    }

    /**
     * Run every N hours
     *
     * @param int $hours
     * @return self
     */
    public function everyHours(int $hours): self
    {
        return $this->everyMinutes($hours * 60);
    }

    /**
     * Run daily
     *
     * @return self
     */
    public function daily(): self
    {
        return $this->everyMinutes(1440);
    }

    /**
     * Run weekly
     *
     * @return self
     */
    public function weekly(): self
    {
        return $this->everyMinutes(10080);
    }

    /**
     * Repeat N times with interval
     *
     * @param int $times Number of times to repeat
     * @param int $intervalSeconds Seconds between each execution
     * @return self
     */
    public function repeat(int $times, int $intervalSeconds = 0): self
    {
        $this->repeat = max(1, $times);
        $this->interval = max(0, $intervalSeconds);
        return $this;
    }

    /**
     * Create the job and add to queue
     *
     * @return int|false Job ID on success, false on failure
     */
    public function create()
    {
        return CronJob::create(
            $this->handler,
            $this->data,
            time() + $this->delay,
            $this->repeat,
            $this->interval,
            $this->queue,
            $this->priority,
            $this->maxAttempts,
            $this->uniqueCode
        );
    }

    /**
     * Alias for create()
     *
     * @return int|false
     */
    public function dispatch()
    {
        return $this->create();
    }
}
