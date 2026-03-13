<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260311072654 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE loan DROP active');
        $this->addSql('ALTER TABLE loan DROP overdue');
        $this->addSql('ALTER TABLE loan ALTER status TYPE VARCHAR(20)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE loan ADD active BOOLEAN NOT NULL');
        $this->addSql('ALTER TABLE loan ADD overdue BOOLEAN NOT NULL');
        $this->addSql('ALTER TABLE loan ALTER status TYPE VARCHAR(15)');
    }
}
