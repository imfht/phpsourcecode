<?php
use Testify\Testify;

require '../framework/bootstrap.inc.php';
require IA_ROOT . '/framework/library/testify/Testify.php';

$test_frame = new Testify('Frame的测试用例');
$test_frame->test('测试获取得分', function() {
	global $test_frame;
	$frame = new Frame();
	$test_frame->assertEquals(0, $frame->getScore());
});
$test_frame->run();



class Frame {
	public function getScore() {
		return 0;
	}
	
	public function add($throw) {
		
	}
}