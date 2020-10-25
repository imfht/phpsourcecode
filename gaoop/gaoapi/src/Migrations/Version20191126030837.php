<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191126030837 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE paths DROP category');
        $this->addSql('ALTER TABLE parameters CHANGE category category INT DEFAULT 1 COMMENT \'参数获取方式 1query 2header 3path 4cookie 5body (jsonn)\', CHANGE format format INT DEFAULT 1 COMMENT \'参数格式 1string 2password 3integer 4boolean 5date 6datetime\'');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE parameters CHANGE category category INT DEFAULT 1 COMMENT \'参数获取方式 1query 2header 3path 4cookie\', CHANGE format format INT DEFAULT 1 COMMENT \'参数格式 1字符串 2密码框\'');
        $this->addSql('ALTER TABLE paths ADD category INT DEFAULT 1 NOT NULL COMMENT \'参数形式 1parameter 2body(json)\'');
    }
}
