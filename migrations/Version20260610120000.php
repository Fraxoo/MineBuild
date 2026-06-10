<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260610120000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Rename build hidden moderation fields to deleted soft-delete fields.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE build DROP CONSTRAINT FK_BDA0F2DB750EF4DA');
        $this->addSql('DROP INDEX IDX_BDA0F2DB750EF4DA');
        $this->addSql('ALTER TABLE build RENAME COLUMN hidden_reason TO deleted_reason');
        $this->addSql('ALTER TABLE build RENAME COLUMN hidden_by_id TO deleted_by_id');
        $this->addSql('UPDATE build SET deleted_at = hidden_at WHERE deleted_at IS NULL AND hidden_at IS NOT NULL');
        $this->addSql('ALTER TABLE build DROP hidden_at');
        $this->addSql('CREATE INDEX IDX_BUILD_DELETED_BY ON build (deleted_by_id)');
        $this->addSql('ALTER TABLE build ADD CONSTRAINT FK_BUILD_DELETED_BY FOREIGN KEY (deleted_by_id) REFERENCES "user" (id) NOT DEFERRABLE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE build DROP CONSTRAINT FK_BUILD_DELETED_BY');
        $this->addSql('DROP INDEX IDX_BUILD_DELETED_BY');
        $this->addSql('ALTER TABLE build ADD hidden_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('UPDATE build SET hidden_at = deleted_at WHERE hidden_at IS NULL AND deleted_at IS NOT NULL');
        $this->addSql('ALTER TABLE build RENAME COLUMN deleted_reason TO hidden_reason');
        $this->addSql('ALTER TABLE build RENAME COLUMN deleted_by_id TO hidden_by_id');
        $this->addSql('CREATE INDEX IDX_BDA0F2DB750EF4DA ON build (hidden_by_id)');
        $this->addSql('ALTER TABLE build ADD CONSTRAINT FK_BDA0F2DB750EF4DA FOREIGN KEY (hidden_by_id) REFERENCES "user" (id) NOT DEFERRABLE');
    }
}
