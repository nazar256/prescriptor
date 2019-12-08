<?php

namespace App\Command;

use App\Entity\Disease;
use App\Entity\Drug;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class FixturesLoadCommand extends Command
{
    private const ARG_DISEASES = 'diseases';
    private const ARG_DRUGS = 'drugs';
    private const FLUSH_EVERY = 500;

    protected static $defaultName = 'app:fixtures-load:diseases';

    /** @var EntityManagerInterface */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
    }

    protected function configure()
    {
        $this
            ->setDescription('Can fill a database with a large number of fixtures')
            ->addArgument(self::ARG_DISEASES, InputArgument::OPTIONAL, 'How many diseases to create', 10000)
            ->addArgument(self::ARG_DRUGS, InputArgument::OPTIONAL, 'How many drugs per disease to create', 3);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title("Provisioning database with dummy data");

        $diseasesToCreate = $input->getArgument(self::ARG_DISEASES);
        $drugsPerDisease = $input->getArgument(self::ARG_DRUGS);
        $progressBar = new ProgressBar($output, (int)$diseasesToCreate / self::FLUSH_EVERY);
        $this->entityManager->getConnection()->getConfiguration()->setSQLLogger(null);

        $notCleanedRecords = 0;
        $progressBar->start();
        for ($diseaseNum = 0; $diseaseNum < $diseasesToCreate; $diseaseNum++) {
            $disease = (new Disease())
                ->setName(uniqid('', true));

            for ($drugNum = 0; $drugNum < $drugsPerDisease; $drugNum++) {
                $drug = (new Drug())
                    ->setName(uniqid('', true))
                    ->addDisease($disease);
                $disease->addDrug($drug);
                $this->entityManager->persist($drug);
            }
            $notCleanedRecords++;
            $this->entityManager->persist($disease);
            if ($notCleanedRecords > self::FLUSH_EVERY) {
                $this->entityManager->flush();
                $this->entityManager->clear();
                gc_collect_cycles();
                $progressBar->advance();
                $notCleanedRecords = 0;
            }
        }
        $this->entityManager->flush();

        $io->success('Done!');

        return 0;
    }
}
