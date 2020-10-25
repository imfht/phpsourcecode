<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191109023045 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE methods');
        $this->addSql('ALTER TABLE paths ADD method INT NOT NULL, DROP method_id, CHANGE url url VARCHAR(255) NOT NULL, CHANGE tag_id tag_id INT NOT NULL, CHANGE summary summary VARCHAR(255) NOT NULL, CHANGE operation_id operation_id VARCHAR(255) NOT NULL, CHANGE description description VARCHAR(255) DEFAULT NULL, CHANGE is_security is_security TINYINT(1) DEFAULT NULL, CHANGE status status TINYINT(1) DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE methods (id INT AUTO_INCREMENT NOT NULL, value VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_general_ci, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'http请求方法表\' ');
        $this->addSql('ALTER TABLE paths ADD method_id INT NOT NULL COMMENT \'请求方法\', DROP method, CHANGE url url VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci COMMENT \'请求路径\', CHANGE tag_id tag_id INT NOT NULL COMMENT \'关联标签ID\', CHANGE summary summary VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci COMMENT \'摘要\', CHANGE operation_id operation_id VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci COMMENT \'swagger-ui操作唯一符\', CHANGE description description VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_unicode_ci COMMENT \'描述\', CHANGE is_security is_security TINYINT(1) DEFAULT \'1\' COMMENT \'是否开启安全校验\', CHANGE status status TINYINT(1) DEFAULT \'1\' COMMENT \'状态 -1删除 1正常\'');
    }
}
