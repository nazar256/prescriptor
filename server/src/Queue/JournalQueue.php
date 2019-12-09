<?php


namespace App\Queue;


class JournalQueue
{
    private array $queue = [];

    //TODO: better ot use objet JournalRecord and queue it
    public function addJournal(string $name, \DateTime $dateTime, int $diseaseId)
    {
        $this->queue[] = ['name' => $name, 'date_time' => $dateTime->format('Y-m-d H:i:s'), 'disease_id' => $diseaseId];
    }

    public function popRecord(): ?array
    {
        return array_pop($this->queue);
    }
}