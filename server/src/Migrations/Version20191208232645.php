<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

final class Version20191208232645 extends AbstractMigration implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    public function getDescription(): string
    {
        return 'Use buffer table for clickhouse journal';
    }

    public function up(Schema $schema): void
    {
        $connection = $this->container->get('doctrine.dbal.clickhouse_connection');
        $db = $connection->getDatabase();
        $connection->exec('
            CREATE TABLE request_log_buffer AS request_log 
            ENGINE = Buffer(' . $db . ', request_log, 16, 10, 100, 10000, 1000000, 10000000, 100000000);'
        );
    }

    public function down(Schema $schema): void
    {
        $connection = $this->container->get('doctrine.dbal.clickhouse_connection');
        $connection->exec('DROP TABLE request_log_buffer;');
    }
}
