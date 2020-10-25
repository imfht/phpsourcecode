<?php declare (strict_types = 1);
namespace msqphp\core\wrong;

use msqphp\core;

final class Wrong
{
    public static function init(): void
    {
        // 错误处理方式
        if (APP_DEBUG) {
            // 设置错误级别最高
            error_reporting(E_ALL);
            // 错误显示
            ini_set('display_errors', 'On');
            // 取消日志记录
            ini_set('log_errors', 'Off');
        } else {
            // 设置错误级别最低
            error_reporting(0);
            // 错误不显示
            ini_set('display_errors', 'Off');
            // 开启日志记录
            ini_set('log_errors', 'On');
        }
        // 载入错误类,设置错误函数处理方式
        static::register();
    }

    public static function register(): void
    {
        set_error_handler('\msqphp\core\wrong\Wrong::errorHandler', E_ALL);
        set_exception_handler('\msqphp\core\wrong\Wrong::exceptionHandler');
    }

    public static function unregister(): void
    {
        restore_error_handler();
        restore_exception_handler();
    }

    public static function exceptionHandler($e): void
    {
        if (APP_DEBUG) {
            if ('cli' === \msqphp\Environment::getRunMode()) {
                $content = [];
                foreach (static::getTraces($e) as $trace) {
                    $content[] = "\t" . $trace['num'] . $trace['file'] . '第' . $trace['line'] . '执行' . $trace['code'];
                }
                $message = 'exception:' . $e->getMessage() . PHP_EOL . (empty($content) ? '' : '{' . PHP_EOL . implode(PHP_EOL, $content) . PHP_EOL . '}');
                echo $message . PHP_EOL;
            } else {
                echo '<style type="text/css">*{margin: 0;padding: 0;}.exception{width: 80%;display: block;margin:0 auto;}.exception h3 {border: none;background: #F3F3F3;border-radius: 10px 10px 10px 10px;font-size: 1em;line-height: 3em;text-align: center;margin: 1em 0;}.exception .table{background: #F3F3F3;border: none;border-radius: 10px 10px 10px 10px;font-size: 1em;width: 100%;display: block;padding: 1em 0;margin:0 auto;}.exception table{margin:0 auto;}.exception .table h4{text-align: center;}.exception th{text-align: center;}.exception td{background: #FFFFCC;}.exception tr .num,.exception tr .line{width: 2em;text-align: center;}</style>';
                echo '<div class="exception"><h3>', $e->getMessage(), '</h3><div class="table"><h4>PHP DEBUG</h4><table align="center" border="1" cellspacing="0"><tr><th class="num">No.</th><th class="file">File</th><th class="line">Line</th><th class="code">Code</th></tr>';
                foreach (static::getTraces($e) as $trace) {
                    echo '<tr><td class="num">', $trace['num'], '</td><td class="file">', $trace['file'], '</td><td class="line">', $trace['line'], '</td><td class="code">', $trace['code'], '</td></tr>';
                }
                echo '</table></div></div>';
            }
        } else {

            $content = [];
            foreach (static::getTraces($e) as $trace) {
                $content[] = "\t" . $trace['num'] . $trace['file'] . '第' . $trace['line'] . '执行' . $trace['code'];
            }
            core\log\Log::record('exception',
                'exception:' . $e->getMessage() . PHP_EOL . (empty($content) ? '' : '{' . PHP_EOL . implode(PHP_EOL, $content) . PHP_EOL . '}'));
        }
    }
    private static function getTraces($e): array
    {
        $result = [['num' => 0, 'file' => $e->getFile(), 'line' => $e->getLine(), 'code' => 'throw']];

        $i = 1;
        foreach ($e->getTrace() as $trace) {
            $result[] = [
                'num'  => $i,
                'file' => $trace['file'] ?? '',
                'line' => $trace['line'] ?? '',
                'code' => isset($trace['type']) ? $trace['class'] . $trace['type'] . $trace['function'] . '()' : $trace['function'] . '()',
            ];
            $i++;
        }

        return $result;
    }
    public static function errorHandler(int $errno, string $errstr, string $errfile, int $errline): void
    {
        if (APP_DEBUG) {
            if ('cli' === \msqphp\Environment::getRunMode()) {
                echo '错误代码:' . $errno . "\n" . '错误信息:' . $errstr . "\n" . '错误文件:' . $errfile . "\n" . '错误行号:' . $errline . "\n";
            } else {
                echo '<style type="text/css">.error{border: 1px solid black;}.error td {border: 1px solid black;}</style><table class="error"><tr><td>文件</td><td>行号</td><td>错误代码</td><td>错误信息</td></tr><tr><td>' . $errfile . '</td><td>' . $errline . '</td><td>' . $errno . '</td><td>' . $errstr . '</td></tr></table>';
            }
        }
        core\log\Log::record('error', '错误代码:' . $errno . "\n" . '错误信息:' . $errstr . "\n" . '错误文件:' . $errfile . "\n" . '错误行号:' . $errline . "\n");
    }
}
