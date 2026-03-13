<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260311073420 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX uniq_65d29b3228fd8608');
        $this->addSql('CREATE INDEX IDX_65D29B3228FD8608 ON payments (loan_id_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX IDX_65D29B3228FD8608');
        $this->addSql('CREATE UNIQUE INDEX uniq_65d29b3228fd8608 ON payments (loan_id_id)');
    }
}
