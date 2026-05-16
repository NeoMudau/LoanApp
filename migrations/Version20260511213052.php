<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260511213052 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE customer ADD user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE customer ADD CONSTRAINT FK_81398E09A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_81398E09A76ED395 ON customer (user_id)');
        $this->addSql('ALTER TABLE "user" DROP CONSTRAINT fk_8d93d649a76ed395');
        $this->addSql('DROP INDEX uniq_8d93d649a76ed395');
        $this->addSql('ALTER TABLE "user" DROP user_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE customer DROP CONSTRAINT FK_81398E09A76ED395');
        $this->addSql('DROP INDEX UNIQ_81398E09A76ED395');
        $this->addSql('ALTER TABLE customer DROP user_id');
        $this->addSql('ALTER TABLE "user" ADD user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE "user" ADD CONSTRAINT fk_8d93d649a76ed395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE UNIQUE INDEX uniq_8d93d649a76ed395 ON "user" (user_id)');
    }
}
