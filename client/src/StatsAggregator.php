<?php

namespace App;

class StatsAggregator
{
    private int $overallCount = 0;

    /** @var int[] */
    private array $responseTimesMs;

    /** @var int[] */
    private array $percentiles;

    private int $estimatedRecords;

    public function __construct(int $estimatedRecords, array $percentiles)
    {
        // Allocate the array here, we must not slow down method addResponseTimes with allocations during array resizing
        $this->responseTimesMs = array_fill(0, $estimatedRecords - 1, 0);
        // Caching this value we decrease count() calls
        $this->estimatedRecords = $estimatedRecords;
        $this->percentiles = $percentiles;
    }

    public function addResponseTime(int $responseTime)
    {
        $this->responseTimesMs[$this->overallCount] = $responseTime;
        $this->overallCount++;
    }

    public function collectStats(Stats $stats): void
    {
        if ($this->overallCount < count($this->responseTimesMs)) {
            $this->responseTimesMs = array_slice($this->responseTimesMs, 0, $this->overallCount);
        }
        sort($this->responseTimesMs);
        foreach ($this->percentiles as $percentile) {
            $index = ($percentile / 100) * (count($this->responseTimesMs) - 1);
            if (floor($index) == $index) {
                $result = ($this->responseTimesMs[$index - 1] + $this->responseTimesMs[$index]) / 2;
            } else {
                $result = $this->responseTimesMs[floor($index)];
            }
            $stats->setPercentile($percentile, $result);
        }
    }
}