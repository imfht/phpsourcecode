<?php
namespace YesfTest\Http;

use PHPUnit\Framework\TestCase;
use Yesf\Yesf;
use Yesf\Http\Vars;

class VarsTest extends TestCase {
	public function testMimeType() {
		$mime = require(YESF_PATH . 'Data/mimeTypes.php');
		$this->assertSame($mime['flv'], Vars::mimeType('Flv'));
		$this->assertEquals('text/html; charset=' . Yesf::app()->getConfig('charset', Yesf::CONF_PROJECT), Vars::mimeType('html'));
		$this->assertEquals('application/octet-stream', Vars::mimeType('unknown'));
	}
}