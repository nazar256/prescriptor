<?php


namespace App\Queue;


use Doctrine\Bundle\DoctrineBundle\Registry;
use FOD\DBALClickHouse\Connection;
use Symfony\Component\HttpKernel\Event\TerminateEvent;

class KernelTerminateJournalListener
{
    private $journalQueue;
    private $clickhouse;

    public function __construct(JournalQueue $journalQueue, Connection $clickhouse)
    {
        $this->journalQueue = $journalQueue;
        $this->clickhouse = $clickhouse;
    }

    public function onKernelTerminate(TerminateEvent $event)
    {
        $record = $this->journalQueue->popRecord();
        while ($record) {
            $this->clickhouse->insert('request_log_buffer', $record);
            $record = $this->journalQueue->popRecord();
        }
    }
}