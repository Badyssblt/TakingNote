<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240502193806 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE friends ADD sender_id INT NOT NULL');
        $this->addSql('ALTER TABLE friends ADD CONSTRAINT FK_21EE7069F624B39D FOREIGN KEY (sender_id) REFERENCES `user` (id)');
        $this->addSql('CREATE INDEX IDX_21EE7069F624B39D ON friends (sender_id)');
        $this->addSql('ALTER TABLE user CHANGE roles roles JSON NOT NULL COMMENT \'(DC2Type:json)\'');
        $this->addSql('ALTER TABLE user_permission CHANGE role role JSON NOT NULL COMMENT \'(DC2Type:json)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user_permission CHANGE role role JSON NOT NULL COMMENT \'(DC2Type:json)\'');
        $this->addSql('ALTER TABLE `user` CHANGE roles roles JSON NOT NULL COMMENT \'(DC2Type:json)\'');
        $this->addSql('ALTER TABLE friends DROP FOREIGN KEY FK_21EE7069F624B39D');
        $this->addSql('DROP INDEX IDX_21EE7069F624B39D ON friends');
        $this->addSql('ALTER TABLE friends DROP sender_id');
    }
}
