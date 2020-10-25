<?php

namespace UnitTest\Service;

/**
 * 测试套件基类
 *
 * @author 李静波
 */
class BaseTestSuite {
	private $tests;
	private $results;
	
	/**
	 *
	 * @var \Think\Model $db
	 */
	protected $db;

	function __construct() {
		$this->db = M();
		$this->tests=[];
	}

	protected function setup() {
		$this->db->startTrans();
		$this->results = [];
	}

	protected function teardown() {
		$this->db->rollback();
	}

	protected function addTest($test) {
		$this->tests[] = $test;
	}

	public function run() {
		$this->setup();
		
		foreach ( $this->tests as $test ) {
			$rc = $test->run($this->db);
			$this->results[] = $rc;
		}
		
		$this->teardown();
	}

	public function getResults() {
		return $this->results;
	}
}