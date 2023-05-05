<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230505123241 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `character` ADD user_id INT NOT NULL, ADD game_id INT NOT NULL');
        $this->addSql('ALTER TABLE `character` ADD CONSTRAINT FK_937AB034A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE `character` ADD CONSTRAINT FK_937AB034E48FD905 FOREIGN KEY (game_id) REFERENCES game (id)');
        $this->addSql('CREATE INDEX IDX_937AB034A76ED395 ON `character` (user_id)');
        $this->addSql('CREATE INDEX IDX_937AB034E48FD905 ON `character` (game_id)');
        $this->addSql('ALTER TABLE gallery ADD game_id INT NOT NULL');
        $this->addSql('ALTER TABLE gallery ADD CONSTRAINT FK_472B783AE48FD905 FOREIGN KEY (game_id) REFERENCES game (id)');
        $this->addSql('CREATE INDEX IDX_472B783AE48FD905 ON gallery (game_id)');
        $this->addSql('ALTER TABLE game ADD mode_id INT NOT NULL, ADD dm_id INT NOT NULL');
        $this->addSql('ALTER TABLE game ADD CONSTRAINT FK_232B318C77E5854A FOREIGN KEY (mode_id) REFERENCES mode (id)');
        $this->addSql('ALTER TABLE game ADD CONSTRAINT FK_232B318CFADC156C FOREIGN KEY (dm_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_232B318C77E5854A ON game (mode_id)');
        $this->addSql('CREATE INDEX IDX_232B318CFADC156C ON game (dm_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE game DROP FOREIGN KEY FK_232B318C77E5854A');
        $this->addSql('ALTER TABLE game DROP FOREIGN KEY FK_232B318CFADC156C');
        $this->addSql('DROP INDEX IDX_232B318C77E5854A ON game');
        $this->addSql('DROP INDEX IDX_232B318CFADC156C ON game');
        $this->addSql('ALTER TABLE game DROP mode_id, DROP dm_id');
        $this->addSql('ALTER TABLE gallery DROP FOREIGN KEY FK_472B783AE48FD905');
        $this->addSql('DROP INDEX IDX_472B783AE48FD905 ON gallery');
        $this->addSql('ALTER TABLE gallery DROP game_id');
        $this->addSql('ALTER TABLE `character` DROP FOREIGN KEY FK_937AB034A76ED395');
        $this->addSql('ALTER TABLE `character` DROP FOREIGN KEY FK_937AB034E48FD905');
        $this->addSql('DROP INDEX IDX_937AB034A76ED395 ON `character`');
        $this->addSql('DROP INDEX IDX_937AB034E48FD905 ON `character`');
        $this->addSql('ALTER TABLE `character` DROP user_id, DROP game_id');
    }
}
