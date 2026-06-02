<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260602120232 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE build DROP game_version');
        $this->addSql('ALTER TABLE build ALTER visibility SET DEFAULT \'PUBLIC\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE build ADD game_version VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE build ALTER visibility DROP DEFAULT');
    }
}
