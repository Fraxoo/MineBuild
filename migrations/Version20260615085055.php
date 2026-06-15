<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260615085055 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE moderation_action ADD target_user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE moderation_action ADD CONSTRAINT FK_B05D81286C066AFE FOREIGN KEY (target_user_id) REFERENCES "user" (id) ON DELETE SET NULL NOT DEFERRABLE');
        $this->addSql('CREATE INDEX IDX_B05D81286C066AFE ON moderation_action (target_user_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE moderation_action DROP CONSTRAINT FK_B05D81286C066AFE');
        $this->addSql('DROP INDEX IDX_B05D81286C066AFE');
        $this->addSql('ALTER TABLE moderation_action DROP target_user_id');
    }
}
