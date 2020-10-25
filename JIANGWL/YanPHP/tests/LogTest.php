<?php
/**
 * User: weilongjiang(江炜隆)<willliam@jwlchina.cn>
 * Date: 2017/8/24
 * Time: 11:22
 */

namespace TestNamespace;

use Yan\Core\Config;
use Yan\Core\Log;

class LogTest extends BaseTestCase
{
    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        Config::initialize();
        Log::initialize();
    }

    public function testLog()
    {
        $this->assertTrue(Log::log('INFO', 'log test message'));
    }

    public function testDebug()
    {
        $this->assertTrue(Log::debug('debug test message'));
    }

    public function testInfo()
    {
        $this->assertTrue(Log::debug('info test message'));
    }

    public function testWarning()
    {
        $this->assertTrue(Log::debug('warning test message'));
    }

    public function testError()
    {
        $this->assertTrue(Log::debug('error test message'));
    }
}