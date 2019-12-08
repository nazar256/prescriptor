#!/usr/bin/env php
<?php
require __DIR__ . '/../vendor/autoload.php';
const MAX_PREALLOCATED_STATS = 1000000;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use App\LoadPortionGenerator;
use App\StatsAggregator;
use App\LoadRunner;

(new Application('prescription-load-tester', '1.0.0'))
    ->register('test')
    ->setDescription('Performs requests in sequential packs (not real concurrency)')
    ->addArgument('url_pattern', InputArgument::REQUIRED, 'url pattern of the resource, use {{ID}} placeholder')
    ->addArgument('min_id', InputArgument::OPTIONAL, 'lowest integer id', 1)
    ->addArgument('max_id', InputArgument::OPTIONAL, 'highest integer id', 10000)
    ->addOption('ttl', 't', InputOption::VALUE_OPTIONAL, 'integer, miliseconds, responses exceeding this timeoutare regarded as lost', 1000)
    ->addOption('duration', 'd', InputOption::VALUE_OPTIONAL, 'integer, seconds, how long to perform the test (can be exceeded, test will wait for the last requests to finish)', 300)
    ->addOption('concurrency', 'c', InputOption::VALUE_OPTIONAL, 'how many requests will be sent in pack (not real concurrency!)', 1)
    ->addOption('response-time', 'e', InputOption::VALUE_OPTIONAL, 'integer, miliseconds, estimated (average) minimal response time (for internal optimization)', 11)
    ->setCode(function (InputInterface $input, OutputInterface $output) {
        $urlPattern = $input->getArgument('url_pattern');
        $minId = (int)$input->getArgument('min_id');
        $maxId = (int)$input->getArgument('max_id');
        $ttlMs = (int)$input->getOption('ttl');
        $durationSec = (int)$input->getOption('duration');
        $concurrency = (int)$input->getOption('concurrency');
        $estimatedResponseTimeMs = (int)$input->getOption('response-time');
        $io = new SymfonyStyle($input, $output);

        $io->title('Starting the benchmark');

        $estimatedResponseTimeMs = $estimatedResponseTimeMs < $ttlMs ? $ttlMs : $estimatedResponseTimeMs;
        $esitmatedMaxRequests = (int)((($durationSec * 1000) / $estimatedResponseTimeMs) * $concurrency);
        // Do not allocate more than this limit because "memory exhausted" error may occure,
        // while actually we may not need to prealocate that many values as this is maximum calculated amount
        $esitmatedMaxRequests = $esitmatedMaxRequests > MAX_PREALLOCATED_STATS ? $esitmatedMaxRequests : MAX_PREALLOCATED_STATS;

        $io->text('preparing for test, preallocated requests: ' . $esitmatedMaxRequests);

        $statsAggregator = new StatsAggregator($esitmatedMaxRequests, [10, 25, 50, 75, 90, 95, 99, 100]);
        $loadGenerator = new LoadPortionGenerator($urlPattern, $minId, $maxId, $concurrency, $durationSec);
        $progressBar = $io->createProgressBar($durationSec);

        $loadRunner = new LoadRunner($statsAggregator, $loadGenerator->getGenerator(), $progressBar, $ttlMs);
        $stats = $loadRunner->exec();

        $io->text('collecting stats');

        $statsAggregator->collectStats($stats);

        $statsTable = [];
        foreach ($stats->getPercentiles() as $percentile => $responseTime) {
            $statsTable[] = [$percentile . '%', $responseTime . ' ms'];
        }
        $statsTable[] = ['errors', $stats->getErrors()];
        $timeouts = $stats->getTimeouts();
        $total = $stats->getTotalRequests();
        $statsTable[] = ['timeouts', $timeouts . ', ' . number_format(100 * $timeouts / $total, 1) . '%'];
        $statsTable[] = ['total requests', $total];

        $io->table([], $statsTable);

        $io->success('Finished');
    })
    ->getApplication()
    ->setDefaultCommand('test', true) // Single command application
    ->run();