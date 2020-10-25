<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191106081541 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE info CHANGE title title VARCHAR(255) DEFAULT NULL COMMENT \'应用名称\', CHANGE version version VARCHAR(255) DEFAULT NULL COMMENT \'版本号\', CHANGE description description VARCHAR(255) DEFAULT NULL COMMENT \'应用描述\'');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE info CHANGE title title VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_unicode_ci, CHANGE version version VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_unicode_ci, CHANGE description description VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_unicode_ci');
    }
}
