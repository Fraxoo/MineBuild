<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260618112528 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE moderation_action ADD report_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE moderation_action ADD CONSTRAINT FK_B05D81284BD2A4C0 FOREIGN KEY (report_id) REFERENCES report (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_B05D81284BD2A4C0 ON moderation_action (report_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE moderation_action DROP CONSTRAINT FK_B05D81284BD2A4C0');
        $this->addSql('DROP INDEX UNIQ_B05D81284BD2A4C0');
        $this->addSql('ALTER TABLE moderation_action DROP report_id');
    }
}
