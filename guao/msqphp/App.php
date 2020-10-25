<?php declare (strict_types = 1);
namespace msqphp;

use msqphp\base\number\Number;
use msqphp\core\response\Response;

final class App
{
    public static function init(): void
    {
        register_shutdown_function([__CLASS__, 'shutDown']);
    }
    // 应用运行
    public static function run(): void
    {
        define('ROUTE_START', microtime(true));
        //加载路由并运行
        core\route\Route::run();
        define('ROUTE_END', microtime(true));
    }
    // 关闭调用
    public static function shutDown(): void
    {
        // 记录或者打印信息
        if (APP_DEBUG) {
            // 避免测试的时候json或其他格式时候的内容输出.
            if (null === Response::$type || 'html' === Response::$type) {
                Response::debugArray(static::getFullInfo());
            }
        } else {
            $content = static::getSimalInfo();
            core\log\Log::recode('success', '运行成功' . PHP_EOL . (empty($content) ? '' : '{' . PHP_EOL . implode(PHP_EOL, $content) . PHP_EOL . '}'));
        }
    }
    private static function getSimalInfo(): array
    {
        $end_info                                                       = [];
        defined('PHP_START_TIME') && $end_info[]                        = "\t总用时          : " . (string) round(microtime(true) - PHP_START_TIME, 12) . '秒';
        defined('ROUTE_END') && defined('USER_FUNC_END') && $end_info[] = "\t路由用时        : " . (string) round(ROUTE_END - ROUTE_START - USER_FUNC_END + USER_FUNC_START, 12) . '秒';
        defined('USER_FUNC_END') && $end_info[]                         = "\t用户函数用时    : " . (string) round(USER_FUNC_END - USER_FUNC_START, 12) . '秒';
        $end_info[]                                                     = "\t内存峰值: " . Number::byte(memory_get_peak_usage(), false);

        return $end_info;
    }
    private static function getFullInfo(): array
    {
        defined('PHP_START_TIME') && $end_time = microtime(true);
        defined('PHP_START_MEM') && $end_mem   = memory_get_usage();

        $end_info = [];

        // 时间信息相关
        if (isset($end_time)) {
            $end_info[]                                                     = '时间信息:';
            $end_info[]                                                     = "\t现在时间戳      : " . (string) round(microtime(true), 12) . '秒';
            $end_info[]                                                     = "\t现在时间        : " . date('Y-m-d H:i:s');
            $end_info[]                                                     = "\t总用时          : " . (string) round($end_time - PHP_START_TIME, 12) . '秒';
            defined('ROUTE_END') && $end_info[]                             = "\t框架实际用时    : " . (string) round($end_time - PHP_START_TIME - ROUTE_END + ROUTE_START, 12) . '秒';
            defined('ROUTE_END') && defined('USER_FUNC_END') && $end_info[] = "\t路由用时        : " . (string) round(ROUTE_END - ROUTE_START - USER_FUNC_END + USER_FUNC_START, 12) . '秒';
            defined('USER_FUNC_END') && $end_info[]                         = "\t用户函数用时    : " . (string) round(USER_FUNC_END - USER_FUNC_START, 12) . '秒';
            unset($end_time);
        }
        // 内存信息相关
        if (isset($end_mem)) {
            $end_info[] = '内存信息:';
            $end_info[] = "\t开始内存: " . Number::byte(PHP_START_MEM, false);
            $end_info[] = "\t结束内存: " . Number::byte($end_mem, false);
            $end_info[] = "\t内存差值: " . Number::byte($end_mem - PHP_START_MEM, false);
            $end_info[] = "\t内存峰值: " . Number::byte(memory_get_peak_usage(), false);
            unset($end_mem);
        }
        // 常量相关
        if (function_exists('get_defined_constants')) {
            $end_info[] = '常量信息:';
            foreach (get_defined_constants(true)['user'] as $key => $value) {
                $end_info[] = "\t" . $key . "\t=>\t" . var_export($value, true);
            }
        }
        // 加载文件相关
        if (function_exists('get_required_files')) {
            $files     = get_required_files();
            $all_size  = 0;
            $file_info = [];
            foreach ($files as $file) {
                $byte        = filesize($file);
                $file_info[] = "\t" . '文件:' . $file . "\t\t" . '大小:' . Number::byte($byte, false);
                $all_size += $byte;
            }
            $end_info[] = '加载信息:';
            $end_info[] = "\t" . '总共加载文件:' . count($files) . '个, 大小:' . Number::byte($all_size, false);
            $end_info   = array_merge($end_info, $file_info);
            unset($file_info, $files, $all_size, $file, $byte);
        }
        // 自动加载相关
        if (!empty($composer = core\loader\AutoLoadRecord::getAllLoadedClasses())) {
            $end_info[] = '自动加载文件个数(不准确,可能少一到两个):' . count($composer);
            $end_info[] = '自动加载文件列表:';
            foreach ($composer as $file) {
                $end_info[] = "\t" . '文件:' . $file . "\t\t" . '大小:' . Number::byte(filesize($file), false);
            }
        }

        return $end_info;
    }
}
