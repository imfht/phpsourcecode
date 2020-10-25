<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191123162455 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE `schemas`');
        $this->addSql('ALTER TABLE info ADD created_at DATETIME DEFAULT NULL, ADD updated_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE servers ADD created_at DATETIME DEFAULT NULL, ADD updated_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE tags ADD created_at DATETIME DEFAULT NULL, ADD updated_at DATETIME DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE `schemas` (id INT AUTO_INCREMENT NOT NULL, paths_id INT NOT NULL COMMENT \'所属接口ID\', type TINYINT(1) DEFAULT \'1\' NOT NULL COMMENT \'类型 1integer 2string 3array 4object\', format TINYINT(1) DEFAULT NULL COMMENT \'格式 1integer 2string 3date 4password\', created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'参数结构体表\' ');
        $this->addSql('ALTER TABLE info DROP created_at, DROP updated_at');
        $this->addSql('ALTER TABLE servers DROP created_at, DROP updated_at');
        $this->addSql('ALTER TABLE tags DROP created_at, DROP updated_at');
    }
}
