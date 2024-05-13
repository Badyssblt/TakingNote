<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240429183039 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user_groups_user DROP FOREIGN KEY FK_59EF81EDA76ED395');
        $this->addSql('ALTER TABLE user_groups_user DROP FOREIGN KEY FK_59EF81EDFD7B02B');
        $this->addSql('DROP TABLE user_groups_user');
        $this->addSql('ALTER TABLE user CHANGE roles roles JSON NOT NULL COMMENT \'(DC2Type:json)\'');
        $this->addSql('ALTER TABLE user_permission ADD user_groups_id INT NOT NULL');
        $this->addSql('ALTER TABLE user_permission ADD CONSTRAINT FK_472E5446FD7B02B FOREIGN KEY (user_groups_id) REFERENCES user_groups (id)');
        $this->addSql('CREATE INDEX IDX_472E5446FD7B02B ON user_permission (user_groups_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE user_groups_user (user_groups_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_59EF81EDFD7B02B (user_groups_id), INDEX IDX_59EF81EDA76ED395 (user_id), PRIMARY KEY(user_groups_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE user_groups_user ADD CONSTRAINT FK_59EF81EDA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_groups_user ADD CONSTRAINT FK_59EF81EDFD7B02B FOREIGN KEY (user_groups_id) REFERENCES user_groups (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_permission DROP FOREIGN KEY FK_472E5446FD7B02B');
        $this->addSql('DROP INDEX IDX_472E5446FD7B02B ON user_permission');
        $this->addSql('ALTER TABLE user_permission DROP user_groups_id');
        $this->addSql('ALTER TABLE `user` CHANGE roles roles JSON NOT NULL COMMENT \'(DC2Type:json)\'');
    }
}
