<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017/5/21
 * Time: 上午1:45
 */

namespace Inhere\Queue\Driver;

/**
 * Class DbQueue
 * @package Inhere\Queue\Driver
 */
class DbQueue extends BaseQueue
{
    /**
     * @var \PDO
     */
    private $db;

    /**
     * @var string
     */
    private $table = 'msg_queue';

    protected function init()
    {
        $this->driver = Queue::DRIVER_DB;

        if (!$this->id) {
            $this->id = $this->driver;
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function doPush($data, $priority = self::PRIORITY_NORM)
    {
        return $this->db->exec(sprintf(
            "INSERT INTO {$this->table} (`queue`, `data`, `priority`, `created_at`) VALUES (%s, %s, %d, %d)",
            $this->id,
            $data,
            $priority,
            time()
        ));
    }

    /**
     * {@inheritDoc}
     */
    protected function doPop($priority = null, $block = false)
    {
        if (!$this->isPriority($priority)) {
            $sql = "SELECT `id`,`data` FROM {$this->table} WHERE queue = %s ORDER BY `priority` ASC, `id` DESC LIMIT 1";
            $sql = sprintf($sql, $this->id);
        } else {
            $sql = "SELECT `id`,`data` FROM {$this->table} WHERE queue = %s AND `priority` = %d ORDER BY `priority` ASC, `id` DESC LIMIT 1";
            $sql = sprintf($sql, $this->id, $priority);
        }

        $data = null;
        $st = $this->db->query($sql);

        if ($row = $st->fetch(\PDO::FETCH_ASSOC)) {
            $data = $row['data'];
        }

        return $data;
    }


    /**
     * @param int $priority
     * @return int
     */
    public function count($priority = self::PRIORITY_NORM)
    {
        $count = 0;
        $sql = sprintf("SELECT COUNT(*) AS `count` FROM {$this->table} WHERE `priority` = %d", $priority);
        $st = $this->db->query($sql);

        if ($row = $st->fetch(\PDO::FETCH_ASSOC)) {
            $count = $row['count'];
        }

        return $count;
    }

    /**
     * {@inheritDoc}
     */
    public function close()
    {
        parent::close();

        $this->db = null;
    }

    /**
     * @return string
     */
    public function getTable(): string
    {
        return $this->table;
    }

    /**
     * @param string $table
     */
    public function setTable(string $table)
    {
        $this->table = $table;
    }

    /**
     * @return \PDO
     */
    public function getDb(): \PDO
    {
        return $this->db;
    }

    /**
     * @param \PDO $db
     */
    public function setDb(\PDO $db)
    {
        $this->db = $db;
    }

    /**
     *
     * ```php
     * $dqe->createTable($dqe->createMysqlTableSql());
     * ```
     * @param string $sql
     * @return int
     */
    public function createTable($sql)
    {
        return $this->db->exec($sql);
    }

    /**
     * @return string
     */
    public function createMysqlTableSql()
    {
        $tName = $this->table;
        return <<<EOF
CREATE TABLE IF NOT EXISTS `$tName` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`queue` CHAR(48) NOT NULL COMMENT 'queue name',
	`data` TEXT NOT NULL COMMENT 'task data',
	`priority` TINYINT(2) UNSIGNED NOT NULL DEFAULT 1,
	`created_at` INT(10) UNSIGNED NOT NULL,
	`started_at` INT(10) UNSIGNED NOT NULL DEFAULT 0,
	`finished_at` INT(10) UNSIGNED NOT NULL DEFAULT 0,
	KEY (`queue`, `created_at`),
	PRIMARY KEY (`id`)
)
COLLATE='utf8_general_ci'
ENGINE=InnoDB
EOF;
    }

    /**
     * @return int
     */
    public function createSqliteTableSql()
    {
        $tName = $this->table;
        return <<<EOF
CREATE TABLE IF NOT EXISTS `$tName` (
	`id` INTEGER PRIMARY KEY NOT NULL,
	`queue` CHAR(48) NOT NULL COMMENT 'queue name',
	`data` TEXT NOT NULL COMMENT 'task data',
	`priority` INTEGER(2) NOT NULL DEFAULT 1,
	`created_at` INTEGER(10) NOT NULL,
	`started_at` INTEGER(10) NOT NULL DEFAULT 0,
	`finished_at` INTEGER(10) NOT NULL DEFAULT 0
);
CREATE INDEX idxQueue on $tName(queue);
CREATE INDEX idxCreatedAt on $tName(created_at);
EOF;
    }
}
