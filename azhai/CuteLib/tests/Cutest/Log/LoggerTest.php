<?php
namespace Cutest\Log;

use \PHPUnit_Framework_TestCase as TestCase;
use \Cute\Log\FileLogger as MyLogger;


class LoggerTest extends TestCase
{
    const TEST_LOGGING_MSG = 'Is this {one} {type} message ?';
    protected $logdir = '';
    protected $filename = '';

    public function setUp()
    {
        $this->logdir = CUTE_ROOT . '/runtime/logs';
        $this->filename = $this->logdir . '/test_' . date('Ymd') . '.log';
    }

    public function test00Truncate()
    {
        if (file_exists($this->filename)) {
            file_put_contents($this->filename, '');
        }
    }

    public function test01Threshold()
    {
        $logger = new MyLogger('test', $this->logdir, 'INFO');
        $msg = str_replace('{type}', 'info', self::TEST_LOGGING_MSG);
        $logger->info(self::TEST_LOGGING_MSG, ['type' => 'info']);
        $logger->writeFiles();
        $this->assertEquals('INFO', self::readLastLog($this->filename, 4));
        $this->assertEquals($msg, $this->getLastMsg());
        $logger->debug(self::TEST_LOGGING_MSG, ['type' => 'debug']);
        $logger->writeFiles();
        $this->assertEquals('INFO', self::readLastLog($this->filename, 4));
        $this->assertEquals($msg, $this->getLastMsg());
    }

    protected static function readLastLog($filename, $loc = 0)
    {
        if (!is_readable($filename)) {
            return false;
        }
        $record = trim(shell_exec('tail -n 1 ' . $filename));
        if ($record === '') {
            return '';
        } else if ($loc <= 0 || $loc > 5) {
            return $record;
        } else {
            $pieces = explode(' ', $record, 5);
            return $pieces[ $loc - 1 ];
        }
    }

    protected function getLastMsg()
    {
        return self::readLastLog($this->filename, 5);
    }

    public function test02SafeLogging()
    {
        $logger = new MyLogger('test', $this->logdir);
        $this->parallelLogging($logger);
        $logger->writeFiles();
        $this->assertTrue(starts_with($this->getLastMsg(), ''));
    }

    public function parallelLogging(MyLogger& $logger)
    {
        $pid = pcntl_fork();
        if ($pid === -1) { //失败
        } elseif ($pid === 0) { //子进程
            for ($i = 0; $i < 700; $i++) {
                $logger->info(self::TEST_LOGGING_MSG,
                    ['type' => 'children', 'one' => $i + 1]);
            }
        } else {
            for ($j = 0; $j < 600; $j++) {
                $logger->debug(self::TEST_LOGGING_MSG,
                    ['type' => 'master', 'one' => $j + 1]);
            }
            pcntl_wait($status);
        }
    }
}
