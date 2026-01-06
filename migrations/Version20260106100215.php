<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260106100215 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE `order` (id INT AUTO_INCREMENT NOT NULL, collaborator_id INT DEFAULT NULL, order_number VARCHAR(255) NOT NULL, status VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_F529939830098C8C (collaborator_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE order_line (id INT AUTO_INCREMENT NOT NULL, stock_id INT DEFAULT NULL, purchase_order_id INT DEFAULT NULL, quantity INT NOT NULL, INDEX IDX_9CE58EE1DCD6110 (stock_id), INDEX IDX_9CE58EE1A45D7E6A (purchase_order_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE packaging (id INT AUTO_INCREMENT NOT NULL, label VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE partner (id INT AUTO_INCREMENT NOT NULL, company_name VARCHAR(255) NOT NULL, contact_details LONGTEXT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE plant (id INT AUTO_INCREMENT NOT NULL, latin_name VARCHAR(255) NOT NULL, common_name VARCHAR(255) NOT NULL, type VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE season (id INT AUTO_INCREMENT NOT NULL, year INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE stock (id INT AUTO_INCREMENT NOT NULL, plant_id INT DEFAULT NULL, packaging_id INT DEFAULT NULL, season_id INT DEFAULT NULL, partner_id INT DEFAULT NULL, updated_by_id INT DEFAULT NULL, quantity INT NOT NULL, INDEX IDX_4B3656601D935652 (plant_id), INDEX IDX_4B3656604E7B3801 (packaging_id), INDEX IDX_4B3656604EC001D1 (season_id), INDEX IDX_4B3656609393F8FE (partner_id), INDEX IDX_4B365660896DBBDE (updated_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `user` (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, first_name VARCHAR(255) NOT NULL, is_active TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750 (queue_name, available_at, delivered_at, id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE `order` ADD CONSTRAINT FK_F529939830098C8C FOREIGN KEY (collaborator_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE order_line ADD CONSTRAINT FK_9CE58EE1DCD6110 FOREIGN KEY (stock_id) REFERENCES stock (id)');
        $this->addSql('ALTER TABLE order_line ADD CONSTRAINT FK_9CE58EE1A45D7E6A FOREIGN KEY (purchase_order_id) REFERENCES `order` (id)');
        $this->addSql('ALTER TABLE stock ADD CONSTRAINT FK_4B3656601D935652 FOREIGN KEY (plant_id) REFERENCES plant (id)');
        $this->addSql('ALTER TABLE stock ADD CONSTRAINT FK_4B3656604E7B3801 FOREIGN KEY (packaging_id) REFERENCES packaging (id)');
        $this->addSql('ALTER TABLE stock ADD CONSTRAINT FK_4B3656604EC001D1 FOREIGN KEY (season_id) REFERENCES season (id)');
        $this->addSql('ALTER TABLE stock ADD CONSTRAINT FK_4B3656609393F8FE FOREIGN KEY (partner_id) REFERENCES partner (id)');
        $this->addSql('ALTER TABLE stock ADD CONSTRAINT FK_4B365660896DBBDE FOREIGN KEY (updated_by_id) REFERENCES `user` (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `order` DROP FOREIGN KEY FK_F529939830098C8C');
        $this->addSql('ALTER TABLE order_line DROP FOREIGN KEY FK_9CE58EE1DCD6110');
        $this->addSql('ALTER TABLE order_line DROP FOREIGN KEY FK_9CE58EE1A45D7E6A');
        $this->addSql('ALTER TABLE stock DROP FOREIGN KEY FK_4B3656601D935652');
        $this->addSql('ALTER TABLE stock DROP FOREIGN KEY FK_4B3656604E7B3801');
        $this->addSql('ALTER TABLE stock DROP FOREIGN KEY FK_4B3656604EC001D1');
        $this->addSql('ALTER TABLE stock DROP FOREIGN KEY FK_4B3656609393F8FE');
        $this->addSql('ALTER TABLE stock DROP FOREIGN KEY FK_4B365660896DBBDE');
        $this->addSql('DROP TABLE `order`');
        $this->addSql('DROP TABLE order_line');
        $this->addSql('DROP TABLE packaging');
        $this->addSql('DROP TABLE partner');
        $this->addSql('DROP TABLE plant');
        $this->addSql('DROP TABLE season');
        $this->addSql('DROP TABLE stock');
        $this->addSql('DROP TABLE `user`');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
