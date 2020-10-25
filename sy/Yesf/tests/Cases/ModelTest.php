<?php
namespace YesfTest;

use PDO;
use PHPUnit\Framework\TestCase;
use Yesf\Yesf;
use Yesf\DI\Container;
use YesfApp\Model\User;

class ModelTest extends TestCase {
	public static $pdo;
	public static $model;
	public static function setUpBeforeClass() {
		$dsn = sprintf(
			'mysql:host=%s;port=%s;dbname=%s;charset=utf8',
			Yesf::app()->getConfig('connection.my.host'),
			Yesf::app()->getConfig('connection.my.port'),
			Yesf::app()->getConfig('connection.my.database')
		);
		self::$pdo = new PDO($dsn, Yesf::app()->getConfig('connection.my.user'), Yesf::app()->getConfig('connection.my.password'));
		self::$model = Container::getInstance()->get(User::class);
	}
	public function testGet() {
		$user = self::$pdo->query('SELECT * FROM `user` LIMIT 0,1')->fetch(PDO::FETCH_ASSOC);
		$res = self::$model->get($user['id']);
		$this->assertEquals($user, $res);
		$res = self::$model->get([
			'name' => $user['name']
		]);
		$this->assertEquals($user, $res);
	}
	public function testList() {
		$user = self::$pdo->query('SELECT name FROM `user` LIMIT 0,1')->fetchAll(PDO::FETCH_ASSOC);
		$res = self::$model->list([
			'name' => $user[0]['name']
		], 1, 0, ['name']);
		$this->assertEquals($user, $res);
	}
	public function testSet() {
		$user = self::$pdo->query('SELECT * FROM `user` LIMIT 0,1')->fetch(PDO::FETCH_ASSOC);
		$newName = uniqid();
		self::$model->set([
			'password' => '123456',
			'name' => $newName
		], ['name'], $user['id']);
		$res = self::$pdo->query("SELECT name, password FROM `user` WHERE id = {$user['id']} LIMIT 0,1")->fetch(PDO::FETCH_ASSOC);
		$this->assertEquals($newName, $res['name']);
		$this->assertNotEquals('123456', $res['password']);
		$newPassword = uniqid();
		self::$model->set([
			'password' => $newPassword
		], [
			'name' => $newName
		]);
		$res = self::$pdo->query("SELECT password FROM `user` WHERE id = {$user['id']} LIMIT 0,1")->fetch(PDO::FETCH_ASSOC);
		$this->assertEquals($newPassword, $res['password']);
	}
	public function testAdd() {
		$name = uniqid();
		$password = uniqid();
		$password_hashed = password_hash($password, PASSWORD_DEFAULT);
		$res = self::$model->add([
			'name' => $name,
			'password' => $password_hashed,
			'not_exists' => '123'
		], ['name', 'password']);
		$this->assertNotNull($res);
		$selected = self::$pdo->query('SELECT * FROM `user` WHERE id = ' . $res)->fetch(PDO::FETCH_ASSOC);
		$this->assertEquals($name, $selected['name']);
		$this->assertTrue(password_verify($password, $selected['password']));
	}
	public function testDel() {
		$record = self::$pdo->query('SELECT * FROM `user` LIMIT 0,1')->fetch(PDO::FETCH_ASSOC);
		$res = self::$model->del($record['id']);
		$this->assertEquals(1, $res);
		$selected = self::$pdo->query('SELECT count(*) as n FROM `user` WHERE id = ' . $record['id'])->fetchColumn();
		$this->assertEquals(0, intval($selected));
	}
}