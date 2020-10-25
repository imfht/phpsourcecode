<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191227121626 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE info CHANGE title title VARCHAR(255) NOT NULL COMMENT \'应用名称\', CHANGE tag tag VARCHAR(255) NOT NULL COMMENT \'标签标示\'');
        $this->addSql('ALTER TABLE log ADD body LONGTEXT DEFAULT NULL COMMENT \'接口变更详情\', DROP paths_id, DROP name, DROP action, DROP description, DROP type');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE info CHANGE title title VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_unicode_ci COMMENT \'应用名称\', CHANGE tag tag VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_unicode_ci COMMENT \'标签标示\'');
        $this->addSql('ALTER TABLE log ADD paths_id INT NOT NULL COMMENT \'关联接口表ID\', ADD name VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_unicode_ci COMMENT \'参数\', ADD action VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_unicode_ci COMMENT \'更新动作, 添加、更新、移除\', ADD description TINYTEXT DEFAULT NULL COLLATE utf8mb4_unicode_ci COMMENT \'更新描述\', ADD type INT DEFAULT NULL COMMENT \'类型 1接口 2参数\', DROP body');
    }
}
