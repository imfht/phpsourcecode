<?php
namespace YesfTest\RD\Adapter;

use PDO;
use PHPUnit\Framework\TestCase;
use Yesf\Yesf;
use Yesf\Connection\Pool;

class MysqlTest extends TestCase {
	public static $pdo;
	public static function setUpBeforeClass() {
		$dsn = sprintf(
			'mysql:host=%s;port=%s;dbname=%s;charset=utf8',
			Yesf::app()->getConfig('connection.my.host'),
			Yesf::app()->getConfig('connection.my.port'),
			Yesf::app()->getConfig('connection.my.database')
		);
		self::$pdo = new PDO($dsn, Yesf::app()->getConfig('connection.my.user'), Yesf::app()->getConfig('connection.my.password'));
	}
	public static function getAdapter() {
		return Pool::getAdapter('my');
	}
	public function testGet() {
		$r1 = self::getAdapter()->get('SELECT * FROM `user` LIMIT 0,1');
		$r2 = self::$pdo->query('SELECT * FROM `user` LIMIT 0,1')->fetch(PDO::FETCH_ASSOC);
		$this->assertEquals($r1, $r2);
	}
	public function testGetColumn() {
		$r1 = self::getAdapter()->getColumn('SELECT count(*) as n FROM `user`', 'n');
		$r2 = self::$pdo->query('SELECT count(*) as n FROM `user`')->fetch(PDO::FETCH_ASSOC);
		$this->assertEquals($r1, $r2['n']);
	}
	public function testSelect() {
		$r1 = self::getAdapter()->query('SELECT * FROM `user` ORDER BY id ASC');
		$r2 = self::$pdo->query('SELECT * FROM `user` ORDER BY id ASC')->fetchAll(PDO::FETCH_ASSOC);
		$this->assertEquals($r1, $r2);
	}
	public function testInsert() {
		$name = uniqid();
		$password = uniqid();
		$password_hashed = password_hash($password, PASSWORD_DEFAULT);
		$r1 = self::getAdapter()->query('INSERT INTO `user` (`name`, `password`) VALUES
		(?, ?)', [$name, $password_hashed]);
		$this->assertEquals(1, $r1['_affected_rows']);
		$this->assertTrue(isset($r1['_insert_id']));
		$selected = self::$pdo->query('SELECT * FROM `user` WHERE id = ' . $r1['_insert_id'])->fetch(PDO::FETCH_ASSOC);
		$this->assertEquals($name, $selected['name']);
		$this->assertTrue(password_verify($password, $selected['password']));
	}
	public function testWhere() {
		$record = self::$pdo->query('SELECT id, name FROM `user` ORDER BY id DESC LIMIT 0,1')->fetch(PDO::FETCH_ASSOC);
		// Test Question-mark parameter marker
		$res1 = self::getAdapter()->get('SELECT name FROM `user` WHERE id = ?', [$record['id']]);
		$this->assertEquals($record['name'], $res1['name']);
		$res2 = self::getAdapter()->getColumn('SELECT name FROM `user` WHERE id = ?', [$record['id']], 'name');
		$this->assertEquals($record['name'], $res2);
		// Test Named parameter marker
		$res3 = self::getAdapter()->get('SELECT name FROM `user` WHERE id = :id', [
			'id' => $record['id']
		]);
		$this->assertEquals($record['name'], $res3['name']);
		$res4 = self::getAdapter()->getColumn('SELECT name FROM `user` WHERE id = :id', [
			'id' => $record['id']
		], 'name');
		$this->assertEquals($record['name'], $res4);
	}
	public function testDelete() {
		$record = self::$pdo->query('SELECT * FROM `user` ORDER BY id DESC LIMIT 0,1')->fetch(PDO::FETCH_ASSOC);
		$res = self::getAdapter()->query('DELETE FROM `user` WHERE id = ?', [$record['id']]);
		$this->assertEquals(1, $res['_affected_rows']);
		$selected = self::$pdo->query('SELECT count(*) as n FROM `user` WHERE id = ' . $record['id'])->fetchColumn();
		$this->assertEquals(0, intval($selected));
	}
}