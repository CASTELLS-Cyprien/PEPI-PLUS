<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260119090757 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE order_status_history (id INT AUTO_INCREMENT NOT NULL, changed_by_id INT DEFAULT NULL, purchase_order_id INT DEFAULT NULL, status VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_471AD77E828AD0A0 (changed_by_id), INDEX IDX_471AD77EA45D7E6A (purchase_order_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE order_status_history ADD CONSTRAINT FK_471AD77E828AD0A0 FOREIGN KEY (changed_by_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE order_status_history ADD CONSTRAINT FK_471AD77EA45D7E6A FOREIGN KEY (purchase_order_id) REFERENCES `order` (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE order_status_history DROP FOREIGN KEY FK_471AD77E828AD0A0');
        $this->addSql('ALTER TABLE order_status_history DROP FOREIGN KEY FK_471AD77EA45D7E6A');
        $this->addSql('DROP TABLE order_status_history');
    }
}
