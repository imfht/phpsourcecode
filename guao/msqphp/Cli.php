<?php declare (strict_types = 1);
namespace msqphp;

final class Cli
{
    // 忽略前两个参数, 即文件路径 和 第一个选择调用类
    private static $args = [];

    private static function exception(string $message): void
    {
        throw new \Exception($message);
    }
    public static function run()
    {
        $GLOBALS['args'] <= 2 && static::exception('错误的命令,请从新输入');
        $temp = $GLOBALS['argv'];
        array_shift($temp);
        array_shift($temp);
        static::$args = $temp;
        unset($temp);
        switch ($GLOBALS['argv'][1]) {
            case 'framework':
                core\cli\CliFramework::run();
                break;
            case 'cron':
                core\cli\CliCron::run();
                break;
            case 'tool':
                core\cli\CliTool::run();
            case 'test':
                core\cli\CliText::run();
                break;
            default:
                static::exception('未知的cli命令');
        }
    }
    public static function getCliArgs(): array
    {
        return static::$args;
    }
    public static function forever(\Closure $func, array $params = []): void
    {
        set_time_limit(0);
        $bool = true;
        while ($bool) {
            $bool = $bool && call_user_func_array($func, $params);
        }
    }
    public static function memoryLimit(int $size, \Closure $func, array $params = []): void
    {
        $size < memory_get_usage(true) && call_user_func_array($func, $params);
    }
}
