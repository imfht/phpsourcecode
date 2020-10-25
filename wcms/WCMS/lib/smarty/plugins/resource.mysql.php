<?php

/**
 * MySQL Resource
 *
 * Resource Implementation based on the Custom API to use
 * MySQL as the storage resource for Smarty's templates and configs.
 *
 * Table definition:
 * <pre>CREATE TABLE IF NOT EXISTS `templates` (
 * `name` varchar(100) NOT NULL,
 * `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
 * `source` text,
 * PRIMARY KEY (`name`)
 * ) ENGINE=InnoDB DEFAULT CHARSET=utf8;</pre>
 *
 * Demo data:
 * <pre>INSERT INTO `templates` (`name`, `modified`, `source`) VALUES ('test.tpl', "2010-12-25 22:00:00", '{$x="hello world"}{$x}');</pre>
 *
 * @package Resource-examples
 * @author Rodney Rehm
 */
class Smarty_Resource_Mysql extends Smarty_Resource_Custom {
	// PDO instance
	protected $db;
	// prepared fetch() statement
	protected $fetch;
	// prepared fetchTimestamp() statement
	protected $mtime;
	
	public function __construct() {
		
		$dbConfig = require 'database.local.php';
		$connect = $dbConfig ['type'] . ":host=" . $dbConfig ['host'] . ";port=" . $dbConfig ['port'] . ";dbname=" . $dbConfig ['dbname'];
		
		try {
			$charset = "SET NAMES '" . $dbConfig ['charset'] . "'";
			
			$this->db = new Pdo ( $connect, $dbConfig ['username'], $dbConfig ['password'], array (PDO::MYSQL_ATTR_INIT_COMMAND => $charset, PDO::ATTR_PERSISTENT => $dbConfig ['presistent'] ) );
		
		} catch ( PDOException $e ) {
			
			echo 'Connection failed:PDO can\'t connect,please check';
			exit ();
		}
		$this->fetch = $this->db->prepare ( 'SELECT modified, source FROM w_templates WHERE name = :name' );
		$this->mtime = $this->db->prepare ( 'SELECT modified FROM w_templates WHERE name = :name' );
	}
	
	/**
	 * Fetch a template and its modification time from database
	 *
	 * @param string $name template name
	 * @param string $source template source
	 * @param integer $mtime template modification timestamp (epoch)
	 * @return void
	 */
	protected function fetch($name, &$source, &$mtime) {
		$this->fetch->execute ( array ('name' => $name ) );
		$row = $this->fetch->fetch ();
		$this->fetch->closeCursor ();
		if ($row) {
			$source = stripslashes ( $row ['source'] ); #取消转义
			$mtime = strtotime ( $row ['modified'] );
		} else {
			$source = null;
			$mtime = null;
		}
	}
	
	/**
	 * Fetch a template's modification time from database
	 *
	 * @note implementing this method is optional. Only implement it if modification times can be accessed faster than loading the comple template source.
	 * @param string $name template name
	 * @return integer timestamp (epoch) the template was modified
	 */
	protected function fetchTimestamp($name) {
		$this->mtime->execute ( array ('name' => $name ) );
		$mtime = $this->mtime->fetchColumn ();
		$this->mtime->closeCursor ();
		return strtotime ( $mtime );
	}
}
