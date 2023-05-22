<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230522080059 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE `character` (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, game_id INT NOT NULL, name VARCHAR(64) NOT NULL, picture VARCHAR(128) DEFAULT NULL, stats LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json)\', inventory LONGTEXT DEFAULT NULL, notes LONGTEXT DEFAULT NULL, updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_937AB034A76ED395 (user_id), INDEX IDX_937AB034E48FD905 (game_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE gallery (id INT AUTO_INCREMENT NOT NULL, game_id INT NOT NULL, picture VARCHAR(128) NOT NULL, main_picture INT DEFAULT NULL, INDEX IDX_472B783AE48FD905 (game_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE game (id INT AUTO_INCREMENT NOT NULL, mode_id INT NOT NULL, dm_id INT NOT NULL, name VARCHAR(64) NOT NULL, status INT NOT NULL, updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_232B318C77E5854A (mode_id), INDEX IDX_232B318CFADC156C (dm_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE game_users (id INT AUTO_INCREMENT NOT NULL, game_id INT NOT NULL, user_id INT NOT NULL, status INT NOT NULL, INDEX IDX_26B0DC66E48FD905 (game_id), INDEX IDX_26B0DC66A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE mode (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(128) NOT NULL, json_stats LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(128) NOT NULL, login VARCHAR(64) NOT NULL, password VARCHAR(64) NOT NULL, picture VARCHAR(128) DEFAULT NULL, updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', roles LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), UNIQUE INDEX UNIQ_8D93D649AA08CB10 (login), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE `character` ADD CONSTRAINT FK_937AB034A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE `character` ADD CONSTRAINT FK_937AB034E48FD905 FOREIGN KEY (game_id) REFERENCES game (id)');
        $this->addSql('ALTER TABLE gallery ADD CONSTRAINT FK_472B783AE48FD905 FOREIGN KEY (game_id) REFERENCES game (id)');
        $this->addSql('ALTER TABLE game ADD CONSTRAINT FK_232B318C77E5854A FOREIGN KEY (mode_id) REFERENCES mode (id)');
        $this->addSql('ALTER TABLE game ADD CONSTRAINT FK_232B318CFADC156C FOREIGN KEY (dm_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE game_users ADD CONSTRAINT FK_26B0DC66E48FD905 FOREIGN KEY (game_id) REFERENCES game (id)');
        $this->addSql('ALTER TABLE game_users ADD CONSTRAINT FK_26B0DC66A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `character` DROP FOREIGN KEY FK_937AB034A76ED395');
        $this->addSql('ALTER TABLE `character` DROP FOREIGN KEY FK_937AB034E48FD905');
        $this->addSql('ALTER TABLE gallery DROP FOREIGN KEY FK_472B783AE48FD905');
        $this->addSql('ALTER TABLE game DROP FOREIGN KEY FK_232B318C77E5854A');
        $this->addSql('ALTER TABLE game DROP FOREIGN KEY FK_232B318CFADC156C');
        $this->addSql('ALTER TABLE game_users DROP FOREIGN KEY FK_26B0DC66E48FD905');
        $this->addSql('ALTER TABLE game_users DROP FOREIGN KEY FK_26B0DC66A76ED395');
        $this->addSql('DROP TABLE `character`');
        $this->addSql('DROP TABLE gallery');
        $this->addSql('DROP TABLE game');
        $this->addSql('DROP TABLE game_users');
        $this->addSql('DROP TABLE mode');
        $this->addSql('DROP TABLE user');
    }
}
