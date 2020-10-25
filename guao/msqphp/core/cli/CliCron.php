<?php declare (strict_types = 1);
namespace msqphp\core\cli;

use msqphp\base\file\File;
use msqphp\Cli;
use msqphp\main\cron\Cron;

final class CliCron
{
    public static function run()
    {
    }
    private static function cron()
    {
        switch (array_shift(static::$args)) {
            case 'run':
            case 'start':
                core\cli\CliCron::run();
                break;
            case 'stop':
            case 'end':
                core\cli\CliCron::stop();
                break;
            case 'rerun':
                core\cli\CliCron::rerun();
                break;
            case 'status':
                core\cli\CliCron::status();
                break;
            default:
                static::exception('未知的cli命令');
        }
    }
    private static function isRuned(): bool
    {
        if (!is_file(Cron::getFilePath('cron_run'))) {
            return false;
        } else {
            return File::get(Cron::getFilePath('cron_run')) === 'true';
        }
    }
    public static function start(): void
    {
        if (static::isRuned()) {
            return;
        }
        File::write(Cron::getFilePath('cron_run'), 'true');

        Cron::update(time(), 3600);

        Cli::forever(function () {
            if (!static::isRuned()) {
                return false;
            }
            Cron::run();
            $next = abs(Cron::getNextRunTime() - time());
            Cli::memory_limit(5 * 1024 * 1024, function () {
                File::delete(Cron::getFilePath('cron_run'));
            });
            sleep($next > 10 ? 10 : $next);
            return true;
        });
    }
    public static function status(): void
    {

    }
    public static function stop(): void
    {
        File::delete(Cron::getFilePath('pid'));
    }
    public static function rerun(): void
    {
        static::stop();
        static::run();
    }
}
