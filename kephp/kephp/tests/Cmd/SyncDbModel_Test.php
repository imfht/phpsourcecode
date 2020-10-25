<?php

namespace tests\Cmd;

use Ke\Cli\Cmd\ScanTables2;
use Ke\Cli\Cmd\SyncDbModel;
use PHPUnit\Framework\TestCase;

class SyncDbModel_Test extends TestCase
{

	public function testAppDbNamespaces()
	{
		$cmd = new SyncDbModel();
		$cmd->setNamespaces('Test');
		$this->assertArrayHasKey('Test', $cmd->getNamespaces());

		$cmd->setNamespaces([
			'Test\\User' => 'HelloWorld',
		]);
		$this->assertArrayHasKey('Test\\User', $cmd->getNamespaces());
		$this->assertEquals('HelloWorld', $cmd->getNamespaces()['Test\\User']);
	}

	public function testAppClassSep()
	{
		$cmd = new SyncDbModel();
		$cmd->setClassSep('   ');
		$this->assertEquals('', $cmd->getClassSep());
		$cmd->setClassSep('\\');
		$this->assertEquals('\\', $cmd->getClassSep());
		$cmd->setClassSep('');
		$this->assertEquals('', $cmd->getClassSep());
	}

	public function testMakeClassName()
	{
		$cmd = new SyncDbModel();
		$cmd->setNamespaces([
			'test'    => 'HelloWorld', // Test => HelloWorld
			'any_crm' => 'CRM',
		]);

		$this->assertEquals('Db\\HelloWorld\\User_Log', $cmd->mkClassName('test_user_log'));
		$this->assertEquals('Db\\CRM\\Api_Log', $cmd->mkClassName('any_crm_api_log'));
		$this->assertEquals('Db\\CRM\\Domain', $cmd->mkClassName('any_crm_domain'));
	}

	public function testMakeClassName2()
	{
		$cmd = new SyncDbModel();
		$cmd->setNamespaces([
			'test'    => 'HelloWorld', // Test => HelloWorld
			'any_crm' => 'CRM',
		]);

		var_dump($cmd->mkClassName('my'));
		var_dump($cmd->mkClassName('any_crm'));
		var_dump($cmd->mkClassName('any_crm_category'));

//		$this->assertEquals('Db\\HelloWorld', $cmd->mkClassName('test'));
	}

//	public function testConst()
//	{
//		define('APP_DB_CLS_SEP', '');
//		define('APP_DB_NS', [
//			'test'    => 'HelloWorld',
//			'any_crm' => 'CRM',
//		]);
//		$cmd = new ScanTables2();
//		$cmd->prepare();
//
//		$this->assertEquals('Db\\HelloWorld\\UserLog', $cmd->mkClassName('test_user_log'));
//		$this->assertEquals('Db\\CRM\\ApiLog', $cmd->mkClassName('any_crm_api_log'));
//		$this->assertEquals('Db\\CRM\\Domain', $cmd->mkClassName('any_crm_domain'));
//	}
}
