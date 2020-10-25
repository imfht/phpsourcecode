<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200320080021 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE admin_module CHANGE name name VARCHAR(255) DEFAULT NULL COMMENT \'模块名称\', CHANGE url url VARCHAR(255) DEFAULT NULL COMMENT \'url\', CHANGE route_name route_name VARCHAR(255) DEFAULT NULL COMMENT \'路由名称\', CHANGE sort sort INT DEFAULT 0 COMMENT \'排序\', CHANGE icon icon VARCHAR(255) DEFAULT \'fa fa-folder\' COMMENT \'icon图标\', CHANGE status status TINYINT(1) DEFAULT \'1\' COMMENT \'状态 0停用 1启用\'');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE admin_module CHANGE name name VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE url url VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE route_name route_name VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE sort sort INT DEFAULT NULL, CHANGE icon icon VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE status status TINYINT(1) DEFAULT NULL');
    }
}
