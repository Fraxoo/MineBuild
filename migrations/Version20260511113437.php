<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260511113437 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE build (id UUID NOT NULL, title VARCHAR(255) NOT NULL, description TEXT NOT NULL, dimensions_x INT DEFAULT NULL, dimensions_y INT DEFAULT NULL, dimensions_z INT DEFAULT NULL, difficulty VARCHAR(255) NOT NULL, time_estimated_min INT NOT NULL, game_version VARCHAR(255) NOT NULL, game_mode VARCHAR(255) NOT NULL, visibility VARCHAR(255) NOT NULL, hidden_reason TEXT DEFAULT NULL, hidden_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, views_count INT DEFAULT 0 NOT NULL, likes_count INT DEFAULT 0 NOT NULL, saves_count INT DEFAULT 0 NOT NULL, downloads_count INT DEFAULT 0 NOT NULL, ratings_count INT DEFAULT 0 NOT NULL, rating_avg NUMERIC(3, 2) DEFAULT \'0.00\' NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, author_id UUID NOT NULL, hidden_by_id UUID DEFAULT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE INDEX IDX_BDA0F2DBF675F31B ON build (author_id)');
        $this->addSql('CREATE INDEX IDX_BDA0F2DB750EF4DA ON build (hidden_by_id)');
        $this->addSql('CREATE TABLE build_asset (id UUID NOT NULL, type VARCHAR(255) NOT NULL, url TEXT NOT NULL, filename VARCHAR(255) NOT NULL, size_bytes INT NOT NULL, downloads_count INT DEFAULT 0 NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, build_id UUID NOT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE INDEX IDX_42772D5717C13F8B ON build_asset (build_id)');
        $this->addSql('CREATE TABLE build_category (created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, build_id UUID NOT NULL, category_id UUID NOT NULL, PRIMARY KEY (build_id, category_id))');
        $this->addSql('CREATE INDEX IDX_E26F43B717C13F8B ON build_category (build_id)');
        $this->addSql('CREATE INDEX IDX_E26F43B712469DE2 ON build_category (category_id)');
        $this->addSql('CREATE TABLE build_image (id UUID NOT NULL, url TEXT NOT NULL, alt VARCHAR(255) DEFAULT NULL, sort_order INT DEFAULT 0 NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, build_id UUID NOT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE INDEX IDX_85E5735417C13F8B ON build_image (build_id)');
        $this->addSql('CREATE TABLE build_like (created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, build_id UUID NOT NULL, user_id UUID NOT NULL, PRIMARY KEY (build_id, user_id))');
        $this->addSql('CREATE INDEX IDX_AB1F2D5917C13F8B ON build_like (build_id)');
        $this->addSql('CREATE INDEX IDX_AB1F2D59A76ED395 ON build_like (user_id)');
        $this->addSql('CREATE TABLE build_material (id UUID NOT NULL, name VARCHAR(255) NOT NULL, quantity INT NOT NULL, color VARCHAR(255) DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, build_id UUID NOT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE INDEX IDX_989D2FE317C13F8B ON build_material (build_id)');
        $this->addSql('CREATE TABLE build_rating (rating INT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, build_id UUID NOT NULL, user_id UUID NOT NULL, PRIMARY KEY (build_id, user_id))');
        $this->addSql('CREATE INDEX IDX_4F1B27DD17C13F8B ON build_rating (build_id)');
        $this->addSql('CREATE INDEX IDX_4F1B27DDA76ED395 ON build_rating (user_id)');
        $this->addSql('CREATE TABLE build_save (created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, build_id UUID NOT NULL, user_id UUID NOT NULL, PRIMARY KEY (build_id, user_id))');
        $this->addSql('CREATE INDEX IDX_521A573417C13F8B ON build_save (build_id)');
        $this->addSql('CREATE INDEX IDX_521A5734A76ED395 ON build_save (user_id)');
        $this->addSql('CREATE TABLE build_tag (created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, build_id UUID NOT NULL, tag_id UUID NOT NULL, PRIMARY KEY (build_id, tag_id))');
        $this->addSql('CREATE INDEX IDX_1220448717C13F8B ON build_tag (build_id)');
        $this->addSql('CREATE INDEX IDX_12204487BAD26311 ON build_tag (tag_id)');
        $this->addSql('CREATE TABLE category (id UUID NOT NULL, name VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_CATEGORY_NAME ON category (name)');
        $this->addSql('CREATE TABLE comment (id UUID NOT NULL, content TEXT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, build_id UUID NOT NULL, author_id UUID NOT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE INDEX IDX_9474526C17C13F8B ON comment (build_id)');
        $this->addSql('CREATE INDEX IDX_9474526CF675F31B ON comment (author_id)');
        $this->addSql('CREATE TABLE moderation_action (id UUID NOT NULL, target_type VARCHAR(255) NOT NULL, action VARCHAR(255) NOT NULL, reason TEXT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, moderator_id UUID NOT NULL, build_id UUID DEFAULT NULL, comment_id UUID DEFAULT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE INDEX IDX_B05D8128D0AFA354 ON moderation_action (moderator_id)');
        $this->addSql('CREATE INDEX IDX_B05D812817C13F8B ON moderation_action (build_id)');
        $this->addSql('CREATE INDEX IDX_B05D8128F8697D13 ON moderation_action (comment_id)');
        $this->addSql('CREATE TABLE notification (id UUID NOT NULL, type VARCHAR(255) NOT NULL, message TEXT NOT NULL, read_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, recipient_id UUID NOT NULL, actor_id UUID NOT NULL, build_id UUID DEFAULT NULL, comment_id UUID DEFAULT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE INDEX IDX_BF5476CAE92F8F78 ON notification (recipient_id)');
        $this->addSql('CREATE INDEX IDX_BF5476CA10DAF24A ON notification (actor_id)');
        $this->addSql('CREATE INDEX IDX_BF5476CA17C13F8B ON notification (build_id)');
        $this->addSql('CREATE INDEX IDX_BF5476CAF8697D13 ON notification (comment_id)');
        $this->addSql('CREATE TABLE report (id UUID NOT NULL, target_type VARCHAR(255) NOT NULL, reason_code VARCHAR(255) NOT NULL, message TEXT NOT NULL, status VARCHAR(255) NOT NULL, handled_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, reporter_id UUID NOT NULL, build_id UUID DEFAULT NULL, comment_id UUID DEFAULT NULL, handled_by_id UUID DEFAULT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE INDEX IDX_C42F7784E1CFE6F5 ON report (reporter_id)');
        $this->addSql('CREATE INDEX IDX_C42F778417C13F8B ON report (build_id)');
        $this->addSql('CREATE INDEX IDX_C42F7784F8697D13 ON report (comment_id)');
        $this->addSql('CREATE INDEX IDX_C42F7784FE65AF40 ON report (handled_by_id)');
        $this->addSql('CREATE TABLE role (id UUID NOT NULL, code VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_ROLE_CODE ON role (code)');
        $this->addSql('CREATE TABLE tag (id UUID NOT NULL, name VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_TAG_NAME ON tag (name)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_TAG_SLUG ON tag (slug)');
        $this->addSql('CREATE TABLE "user" (id UUID NOT NULL, username VARCHAR(180) NOT NULL, password VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, avatar_url TEXT DEFAULT NULL, bio TEXT DEFAULT NULL, is_active BOOLEAN NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, role_id UUID NOT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE INDEX IDX_8D93D649D60322AC ON "user" (role_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_IDENTIFIER_USERNAME ON "user" (username)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_USER_EMAIL ON "user" (email)');
        $this->addSql('CREATE TABLE user_follow (created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, follower_id UUID NOT NULL, following_id UUID NOT NULL, PRIMARY KEY (follower_id, following_id))');
        $this->addSql('CREATE INDEX IDX_D665F4DAC24F853 ON user_follow (follower_id)');
        $this->addSql('CREATE INDEX IDX_D665F4D1816E3A3 ON user_follow (following_id)');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT GENERATED BY DEFAULT AS IDENTITY NOT NULL, body TEXT NOT NULL, headers TEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, available_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, delivered_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE INDEX IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750 ON messenger_messages (queue_name, available_at, delivered_at, id)');
        $this->addSql('ALTER TABLE build ADD CONSTRAINT FK_BDA0F2DBF675F31B FOREIGN KEY (author_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE');
        $this->addSql('ALTER TABLE build ADD CONSTRAINT FK_BDA0F2DB750EF4DA FOREIGN KEY (hidden_by_id) REFERENCES "user" (id) NOT DEFERRABLE');
        $this->addSql('ALTER TABLE build_asset ADD CONSTRAINT FK_42772D5717C13F8B FOREIGN KEY (build_id) REFERENCES build (id) ON DELETE CASCADE NOT DEFERRABLE');
        $this->addSql('ALTER TABLE build_category ADD CONSTRAINT FK_E26F43B717C13F8B FOREIGN KEY (build_id) REFERENCES build (id) ON DELETE CASCADE NOT DEFERRABLE');
        $this->addSql('ALTER TABLE build_category ADD CONSTRAINT FK_E26F43B712469DE2 FOREIGN KEY (category_id) REFERENCES category (id) ON DELETE CASCADE NOT DEFERRABLE');
        $this->addSql('ALTER TABLE build_image ADD CONSTRAINT FK_85E5735417C13F8B FOREIGN KEY (build_id) REFERENCES build (id) ON DELETE CASCADE NOT DEFERRABLE');
        $this->addSql('ALTER TABLE build_like ADD CONSTRAINT FK_AB1F2D5917C13F8B FOREIGN KEY (build_id) REFERENCES build (id) ON DELETE CASCADE NOT DEFERRABLE');
        $this->addSql('ALTER TABLE build_like ADD CONSTRAINT FK_AB1F2D59A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE');
        $this->addSql('ALTER TABLE build_material ADD CONSTRAINT FK_989D2FE317C13F8B FOREIGN KEY (build_id) REFERENCES build (id) ON DELETE CASCADE NOT DEFERRABLE');
        $this->addSql('ALTER TABLE build_rating ADD CONSTRAINT FK_4F1B27DD17C13F8B FOREIGN KEY (build_id) REFERENCES build (id) ON DELETE CASCADE NOT DEFERRABLE');
        $this->addSql('ALTER TABLE build_rating ADD CONSTRAINT FK_4F1B27DDA76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE');
        $this->addSql('ALTER TABLE build_save ADD CONSTRAINT FK_521A573417C13F8B FOREIGN KEY (build_id) REFERENCES build (id) ON DELETE CASCADE NOT DEFERRABLE');
        $this->addSql('ALTER TABLE build_save ADD CONSTRAINT FK_521A5734A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE');
        $this->addSql('ALTER TABLE build_tag ADD CONSTRAINT FK_1220448717C13F8B FOREIGN KEY (build_id) REFERENCES build (id) ON DELETE CASCADE NOT DEFERRABLE');
        $this->addSql('ALTER TABLE build_tag ADD CONSTRAINT FK_12204487BAD26311 FOREIGN KEY (tag_id) REFERENCES tag (id) ON DELETE CASCADE NOT DEFERRABLE');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526C17C13F8B FOREIGN KEY (build_id) REFERENCES build (id) ON DELETE CASCADE NOT DEFERRABLE');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526CF675F31B FOREIGN KEY (author_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE');
        $this->addSql('ALTER TABLE moderation_action ADD CONSTRAINT FK_B05D8128D0AFA354 FOREIGN KEY (moderator_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE');
        $this->addSql('ALTER TABLE moderation_action ADD CONSTRAINT FK_B05D812817C13F8B FOREIGN KEY (build_id) REFERENCES build (id) ON DELETE SET NULL NOT DEFERRABLE');
        $this->addSql('ALTER TABLE moderation_action ADD CONSTRAINT FK_B05D8128F8697D13 FOREIGN KEY (comment_id) REFERENCES comment (id) ON DELETE SET NULL NOT DEFERRABLE');
        $this->addSql('ALTER TABLE notification ADD CONSTRAINT FK_BF5476CAE92F8F78 FOREIGN KEY (recipient_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE');
        $this->addSql('ALTER TABLE notification ADD CONSTRAINT FK_BF5476CA10DAF24A FOREIGN KEY (actor_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE');
        $this->addSql('ALTER TABLE notification ADD CONSTRAINT FK_BF5476CA17C13F8B FOREIGN KEY (build_id) REFERENCES build (id) ON DELETE SET NULL NOT DEFERRABLE');
        $this->addSql('ALTER TABLE notification ADD CONSTRAINT FK_BF5476CAF8697D13 FOREIGN KEY (comment_id) REFERENCES comment (id) ON DELETE SET NULL NOT DEFERRABLE');
        $this->addSql('ALTER TABLE report ADD CONSTRAINT FK_C42F7784E1CFE6F5 FOREIGN KEY (reporter_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE');
        $this->addSql('ALTER TABLE report ADD CONSTRAINT FK_C42F778417C13F8B FOREIGN KEY (build_id) REFERENCES build (id) ON DELETE SET NULL NOT DEFERRABLE');
        $this->addSql('ALTER TABLE report ADD CONSTRAINT FK_C42F7784F8697D13 FOREIGN KEY (comment_id) REFERENCES comment (id) ON DELETE SET NULL NOT DEFERRABLE');
        $this->addSql('ALTER TABLE report ADD CONSTRAINT FK_C42F7784FE65AF40 FOREIGN KEY (handled_by_id) REFERENCES "user" (id) ON DELETE SET NULL NOT DEFERRABLE');
        $this->addSql('ALTER TABLE "user" ADD CONSTRAINT FK_8D93D649D60322AC FOREIGN KEY (role_id) REFERENCES role (id) NOT DEFERRABLE');
        $this->addSql('ALTER TABLE user_follow ADD CONSTRAINT FK_D665F4DAC24F853 FOREIGN KEY (follower_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE');
        $this->addSql('ALTER TABLE user_follow ADD CONSTRAINT FK_D665F4D1816E3A3 FOREIGN KEY (following_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE build DROP CONSTRAINT FK_BDA0F2DBF675F31B');
        $this->addSql('ALTER TABLE build DROP CONSTRAINT FK_BDA0F2DB750EF4DA');
        $this->addSql('ALTER TABLE build_asset DROP CONSTRAINT FK_42772D5717C13F8B');
        $this->addSql('ALTER TABLE build_category DROP CONSTRAINT FK_E26F43B717C13F8B');
        $this->addSql('ALTER TABLE build_category DROP CONSTRAINT FK_E26F43B712469DE2');
        $this->addSql('ALTER TABLE build_image DROP CONSTRAINT FK_85E5735417C13F8B');
        $this->addSql('ALTER TABLE build_like DROP CONSTRAINT FK_AB1F2D5917C13F8B');
        $this->addSql('ALTER TABLE build_like DROP CONSTRAINT FK_AB1F2D59A76ED395');
        $this->addSql('ALTER TABLE build_material DROP CONSTRAINT FK_989D2FE317C13F8B');
        $this->addSql('ALTER TABLE build_rating DROP CONSTRAINT FK_4F1B27DD17C13F8B');
        $this->addSql('ALTER TABLE build_rating DROP CONSTRAINT FK_4F1B27DDA76ED395');
        $this->addSql('ALTER TABLE build_save DROP CONSTRAINT FK_521A573417C13F8B');
        $this->addSql('ALTER TABLE build_save DROP CONSTRAINT FK_521A5734A76ED395');
        $this->addSql('ALTER TABLE build_tag DROP CONSTRAINT FK_1220448717C13F8B');
        $this->addSql('ALTER TABLE build_tag DROP CONSTRAINT FK_12204487BAD26311');
        $this->addSql('ALTER TABLE comment DROP CONSTRAINT FK_9474526C17C13F8B');
        $this->addSql('ALTER TABLE comment DROP CONSTRAINT FK_9474526CF675F31B');
        $this->addSql('ALTER TABLE moderation_action DROP CONSTRAINT FK_B05D8128D0AFA354');
        $this->addSql('ALTER TABLE moderation_action DROP CONSTRAINT FK_B05D812817C13F8B');
        $this->addSql('ALTER TABLE moderation_action DROP CONSTRAINT FK_B05D8128F8697D13');
        $this->addSql('ALTER TABLE notification DROP CONSTRAINT FK_BF5476CAE92F8F78');
        $this->addSql('ALTER TABLE notification DROP CONSTRAINT FK_BF5476CA10DAF24A');
        $this->addSql('ALTER TABLE notification DROP CONSTRAINT FK_BF5476CA17C13F8B');
        $this->addSql('ALTER TABLE notification DROP CONSTRAINT FK_BF5476CAF8697D13');
        $this->addSql('ALTER TABLE report DROP CONSTRAINT FK_C42F7784E1CFE6F5');
        $this->addSql('ALTER TABLE report DROP CONSTRAINT FK_C42F778417C13F8B');
        $this->addSql('ALTER TABLE report DROP CONSTRAINT FK_C42F7784F8697D13');
        $this->addSql('ALTER TABLE report DROP CONSTRAINT FK_C42F7784FE65AF40');
        $this->addSql('ALTER TABLE "user" DROP CONSTRAINT FK_8D93D649D60322AC');
        $this->addSql('ALTER TABLE user_follow DROP CONSTRAINT FK_D665F4DAC24F853');
        $this->addSql('ALTER TABLE user_follow DROP CONSTRAINT FK_D665F4D1816E3A3');
        $this->addSql('DROP TABLE build');
        $this->addSql('DROP TABLE build_asset');
        $this->addSql('DROP TABLE build_category');
        $this->addSql('DROP TABLE build_image');
        $this->addSql('DROP TABLE build_like');
        $this->addSql('DROP TABLE build_material');
        $this->addSql('DROP TABLE build_rating');
        $this->addSql('DROP TABLE build_save');
        $this->addSql('DROP TABLE build_tag');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE comment');
        $this->addSql('DROP TABLE moderation_action');
        $this->addSql('DROP TABLE notification');
        $this->addSql('DROP TABLE report');
        $this->addSql('DROP TABLE role');
        $this->addSql('DROP TABLE tag');
        $this->addSql('DROP TABLE "user"');
        $this->addSql('DROP TABLE user_follow');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
