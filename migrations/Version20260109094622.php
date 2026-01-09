<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260109094622 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `order` ADD updated_by_id INT NOT NULL, ADD updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE `order` ADD CONSTRAINT FK_F5299398896DBBDE FOREIGN KEY (updated_by_id) REFERENCES `user` (id)');
        $this->addSql('CREATE INDEX IDX_F5299398896DBBDE ON `order` (updated_by_id)');
        $this->addSql('ALTER TABLE partner ADD user_id INT DEFAULT NULL, ADD created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE partner ADD CONSTRAINT FK_312B3E16A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_312B3E16A76ED395 ON partner (user_id)');
        $this->addSql('ALTER TABLE user ADD must_change_password TINYINT(1) NOT NULL, DROP created_at');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `order` DROP FOREIGN KEY FK_F5299398896DBBDE');
        $this->addSql('DROP INDEX IDX_F5299398896DBBDE ON `order`');
        $this->addSql('ALTER TABLE `order` DROP updated_by_id, DROP updated_at');
        $this->addSql('ALTER TABLE partner DROP FOREIGN KEY FK_312B3E16A76ED395');
        $this->addSql('DROP INDEX UNIQ_312B3E16A76ED395 ON partner');
        $this->addSql('ALTER TABLE partner DROP user_id, DROP created_at');
        $this->addSql('ALTER TABLE `user` ADD created_at DATETIME NOT NULL, DROP must_change_password');
    }
}
