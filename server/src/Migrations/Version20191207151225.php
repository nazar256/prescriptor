<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191207151225 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE drug (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE drug_disease (drug_id INT NOT NULL, disease_id INT NOT NULL, INDEX IDX_C0FC8C4EAABCA765 (drug_id), INDEX IDX_C0FC8C4ED8355341 (disease_id), PRIMARY KEY(drug_id, disease_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE disease (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE disease_drug (disease_id INT NOT NULL, drug_id INT NOT NULL, INDEX IDX_3186CA97D8355341 (disease_id), INDEX IDX_3186CA97AABCA765 (drug_id), PRIMARY KEY(disease_id, drug_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE drug_disease ADD CONSTRAINT FK_C0FC8C4EAABCA765 FOREIGN KEY (drug_id) REFERENCES drug (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE drug_disease ADD CONSTRAINT FK_C0FC8C4ED8355341 FOREIGN KEY (disease_id) REFERENCES disease (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE disease_drug ADD CONSTRAINT FK_3186CA97D8355341 FOREIGN KEY (disease_id) REFERENCES disease (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE disease_drug ADD CONSTRAINT FK_3186CA97AABCA765 FOREIGN KEY (drug_id) REFERENCES drug (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE drug_disease DROP FOREIGN KEY FK_C0FC8C4EAABCA765');
        $this->addSql('ALTER TABLE disease_drug DROP FOREIGN KEY FK_3186CA97AABCA765');
        $this->addSql('ALTER TABLE drug_disease DROP FOREIGN KEY FK_C0FC8C4ED8355341');
        $this->addSql('ALTER TABLE disease_drug DROP FOREIGN KEY FK_3186CA97D8355341');
        $this->addSql('DROP TABLE drug');
        $this->addSql('DROP TABLE drug_disease');
        $this->addSql('DROP TABLE disease');
        $this->addSql('DROP TABLE disease_drug');
    }
}
