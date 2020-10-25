<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191106081815 extends AbstractMigration
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
        $this->addSql('ALTER TABLE servers CHANGE url url VARCHAR(255) NOT NULL COMMENT \'url链接\', CHANGE description description VARCHAR(255) DEFAULT NULL COMMENT \'描述\'');
        $this->addSql('ALTER TABLE tags CHANGE name name VARCHAR(255) NOT NULL COMMENT \'名称\', CHANGE description description VARCHAR(255) NOT NULL COMMENT \'描述\', CHANGE doc_description doc_description VARCHAR(255) DEFAULT NULL COMMENT \'外部文档描述\', CHANGE doc_url doc_url VARCHAR(255) NOT NULL COMMENT \'外部文档链接\'');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE info CHANGE title title VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_unicode_ci, CHANGE version version VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_unicode_ci, CHANGE description description VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE servers CHANGE url url VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, CHANGE description description VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE tags CHANGE name name VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, CHANGE description description VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, CHANGE doc_description doc_description VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_unicode_ci, CHANGE doc_url doc_url VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci');
    }
}
