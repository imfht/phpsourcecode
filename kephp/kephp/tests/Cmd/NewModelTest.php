<?php

namespace tests\Cmd;

use Ke\Adm\Db;
use Ke\Cli\Argv;
use Ke\Cli\Cmd\NewModel;
use Ke\Cli\Cmd\SyncDbModel;
use Ke\Cli\Console;
use PHPUnit\Framework\TestCase;

require_once './MpPlatformTable.php';
require_once './MpPlatformModel_Test.php';

class NewModelTest extends TestCase
{

	protected function setUp(): void
	{
		Db::define([
			'default' => [
				'adapter'  => 'mysql',
				'host'     => 'localhost',
				'db'       => 'um_mp_platform',
				'user'     => 'root',
				'password' => '',
				'charset'  => 'utf8mb4',
			],
		]);
	}

	public function testNewModel()
	{
		$table = 'mp_platform';
		$syncCmd = new SyncDbModel([]);
		$syncCmd->setDebug(true);
		$className = $syncCmd->mkClassName($table);

		$console = Console::getConsole();

		/**
		 * @var NewModel $testCmd
		 */
		$testCmd = $console->seekCommand(Argv::new("new-model {$className['db']} -t={$table}"));
		$testCmd->setDebug(true);

		echo($testCmd->buildModel($table, $className['db'], ''));
	}

	public function testGenerateOrUpdateModelNotExists()
	{
		$syncCmd = new SyncDbModel([]);
		$syncCmd->setDebug(true);
		$className = [
			'db'    => MpPlatformTable::class,
			'model' => 'Cmd\\MpPlatformModel_NotExists',
		];

		$content = $syncCmd->generateOrUpdateModel($className['model'], $className['db']);

		echo($content);
	}

	public function testGenerateOrUpdateModelExists()
	{
		$syncCmd = new SyncDbModel([]);
		$syncCmd->setDebug(false);
		$className = [
			'db'    => MpPlatformTable::class,
			'model' => 'Cmd\\MpPlatformModel_Test',
		];

		$syncCmd->generateOrUpdateModel($className['model'], $className['db']);
	}

	public function testVerexport()
	{
		$syncCmd = new SyncDbModel([]);
		$syncCmd->setDebug(true);
		$content = $syncCmd->varexport([
			'a' => 'a',
			'b' => 'b',
			'o' => [
				'd' => 'd',
				'c' => 'c',
			]
		]);
		echo($content);
	}
}
