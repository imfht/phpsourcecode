<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191127014001 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE info ADD tag VARCHAR(255) DEFAULT NULL COMMENT \'标签标示\', ADD is_current TINYINT(1) DEFAULT \'0\' COMMENT \'当前编辑使用的info\'');
        $this->addSql('ALTER TABLE paths ADD info_id INT NOT NULL COMMENT \'关联元数据表ID\'');
        $this->addSql('ALTER TABLE servers ADD info_id INT NOT NULL COMMENT \'关联元数据表ID\'');
        $this->addSql('ALTER TABLE tags ADD info_id INT NOT NULL COMMENT \'关联元数据表ID\'');
        $this->addSql('ALTER TABLE parameters ADD info_id INT NOT NULL COMMENT \'关联元数据表ID\'');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE info DROP tag, DROP is_current');
        $this->addSql('ALTER TABLE parameters DROP info_id');
        $this->addSql('ALTER TABLE paths DROP info_id');
        $this->addSql('ALTER TABLE servers DROP info_id');
        $this->addSql('ALTER TABLE tags DROP info_id');
    }
}
