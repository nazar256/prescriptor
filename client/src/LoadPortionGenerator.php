<?php

namespace App;

use Generator;

class LoadPortionGenerator
{
    private string $urlPattern;
    private int $minId;
    private int $maxId;
    private int $durationSec;
    private int $concurrency;

    public function __construct(string $urlPattern, int $minId, int $maxId, int $concurrency, int $durationSec)
    {
        $this->urlPattern = $urlPattern;
        $this->minId = $minId;
        $this->maxId = $maxId;
        $this->concurrency = $concurrency;
        $this->durationSec = $durationSec;
    }

    /**
     * Tracks time, generates urls for specified time
     * @return Generator
     */
    public function getGenerator(): Generator
    {
        $start = microtime(true);
        do {
            $urls = [];
            for ($i = 0; $i < $this->concurrency; $i++) {
                $randomId = rand($this->minId, $this->maxId);
                $randomName = uniqid('name_', true);
                $urls[] = str_replace(['{{ID}}', '{{NAME}}'], [$randomId, $randomName], $this->urlPattern);
            }
            yield $urls;
        } while ((microtime(true) - $start) < $this->durationSec);
    }
}