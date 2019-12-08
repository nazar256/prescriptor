<?php

namespace App;

use Generator;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\HttpClient\CurlHttpClient;
use Symfony\Component\HttpClient\Exception\TransportException;
use Symfony\Contracts\HttpClient\ResponseInterface;

class LoadRunner
{
    private StatsAggregator $statsAggregator;
    private Generator $urlGenerator;
    private ProgressBar $progressBar;
    private int $timeoutMs;

    public function __construct(StatsAggregator $statsAggregator, Generator $urlGenerator, ProgressBar $progressBar, int $timeoutMs)
    {
        $this->statsAggregator = $statsAggregator;
        $this->urlGenerator = $urlGenerator;
        $this->progressBar = $progressBar;
        $this->timeoutMs = $timeoutMs;
    }

    public function exec(): Stats
    {
        $errorsCount = 0;
        $requestsCount = 0;
        $timeoutsCount = 0;
        $statsAggregator = $this->statsAggregator;
        $timeout = $this->timeoutMs / 1000;
        $startTime = microtime(true);

        foreach ($this->urlGenerator as $urls) {
            $responses = [];
            foreach ($urls as $url) {
                $client = new CurlHttpClient();
                $requestsCount++;
                $responses[] = $client->request('GET', $url, ['timeout' => $timeout, 'max_duration' => $timeout]);
            }
            /** @var ResponseInterface $response */
            foreach ($responses as $response) {
                try {
                    $statusCode = $response->getStatusCode();
                    $info = $response->getInfo();
                } catch (TransportException $e) {
                    if (strpos($e->getMessage(), 'Timeout was reached') !== null) {
                        $timeoutsCount++;
                    } else {
                        $errorsCount++;
                    }
                }

                if ($statusCode !== 200) {
                    $errorsCount++;
                }
                $statsAggregator->addResponseTime((int)($info['total_time_us'] / 1000));
                $this->progressBar->setProgress((int)(microtime(true) - $startTime));
            }
        }
        $this->progressBar->finish();

        $stats = new Stats();
        $stats->setErrors($errorsCount);
        $stats->setTotalRequests($requestsCount);
        $stats->setTimeouts($timeoutsCount);
        return $stats;
    }
}