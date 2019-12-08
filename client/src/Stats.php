<?php

namespace App;

class Stats
{
    /** @var int[]|float[] */
    private array $percentiles = [];

    private int $totalRequests = 0;
    private int $errors = 0;
    private int $timeouts = 0;

    /**
     * @param int $percentile
     * @param int|float $value
     */
    public function setPercentile(int $percentile, $value)
    {
        $this->percentiles[$percentile] = $value;
    }

    public function getPercentiles(): array
    {
        return $this->percentiles;
    }

    public function getErrors(): int
    {
        return $this->errors;
    }

    public function setErrors(int $errors)
    {
        $this->errors = $errors;
    }

    public function getTotalRequests(): int
    {
        return $this->totalRequests;
    }

    public function setTotalRequests(int $totalRequests): void
    {
        $this->totalRequests = $totalRequests;
    }

    public function getTimeouts(): int
    {
        return $this->timeouts;
    }

    public function setTimeouts(int $timeouts): void
    {
        $this->timeouts = $timeouts;
    }
}