<?php

class HandlerTest extends \PHPUnit\Framework\TestCase
{
    public function testLogSuccess()
    {
        $log_path = __DIR__ . '/../logs/test.log';
        file_exists($log_path) && unlink($log_path);
        $handler = new \Lxj\Monolog\Co\Stream\Handler(
            $log_path,
            \Monolog\Logger::DEBUG,
            true,
            null,
            100,
            8,
            true
        );
        $formatter = new \Monolog\Formatter\LineFormatter("%message%\n");
        $handler->setFormatter($formatter);
        $monolog = new \Monolog\Logger('test');
        $monolog->pushHandler($handler);
        $monolog->info('test info');
        $monolog->warning('test warning');
        $monolog->debug('test debug');
        $monolog->notice('test notice');
        $monolog->error('test error');
        $monolog->critical('test critical');
        $monolog->alert('test alert');
        $monolog->emergency('test emergency');

        \swoole_event::wait();

        $start = time();

        while (!$logContent = file_get_contents($log_path)) {
            if (time() - $start > 5) {
                break;
            }
        }

        foreach (['test info',
            'test warning',
            'test debug',
            'test notice',
            'test error',
            'test critical',
            'test alert',
            'test emergency'] as $log) {
            $this->assertStringContainsString($log, $logContent);
        }

        unlink($log_path);
    }
}
