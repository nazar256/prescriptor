<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Types;
use Doctrine\Migrations\AbstractMigration;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

final class Version20191208212229 extends AbstractMigration implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    public function getDescription(): string
    {
        return 'Creates clickhouse table';
    }

    public function up(Schema $schema): void
    {
        $connection = $this->container->get('doctrine.dbal.clickhouse_connection');

        $fromSchema = $connection->getSchemaManager()->createSchema();
        $toSchema = clone $fromSchema;

        $newTable = $toSchema->createTable('request_log');
        $newTable->addColumn('name', Types::STRING);
        $newTable->addColumn('date_time', Types::DATETIME_MUTABLE);
        $newTable->addColumn('disease_id', Types::INTEGER);
        $newTable->setPrimaryKey(['disease_id', 'name', 'date_time']);

        $sqlArray = $fromSchema->getMigrateToSql($toSchema, $connection->getDatabasePlatform());
        foreach ($sqlArray as $sql) {
            $connection->exec($sql);
        }
    }

    public function down(Schema $schema): void
    {
        $connection = $this->container->get('doctrine.dbal.clickhouse_connection');

        $fromSchema = $connection->getSchemaManager()->createSchema();
        /** @var Schema $toSchema */
        $toSchema = clone $fromSchema;

        $toSchema->dropTable('request_log');

        $sqlArray = $fromSchema->getMigrateToSql($toSchema, $connection->getDatabasePlatform());
        foreach ($sqlArray as $sql) {
            $connection->exec($sql);
        }
    }
}
