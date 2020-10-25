<?php declare(strict_types = 1);
namespace msqphp\main\cron;

use msqphp\base\file\File;

trait CronRunTrait
{
    use CronLockTrait, CronNextTimeTrait;

    // 执行定时任务
    public static function run() : void
    {
        // 现在时间小于等于下一次执行时间,则跳过
        if (static::getNextRunTime() >= $now = time()) {
            return;
        }
        // 文件已锁,跳过运行
        if (static::isLocked()) {
            static::recordLog(date('Y-m-d H:i:s', $now).'文件已锁,跳过运行');
        }
        // 获取任务信息
        $info_file = static::getFilePath('info');
        $cache_file = static::getFilePath('cache');
        $cache_middle_file = static::getFilePath('cache_middle');
        // 将缓存文件重命名,(获取缓存,放入另一个中间缓存,清空缓存),避免运行时锁文件过久
        is_file($cache_file) && File::rename($cache_file, $cache_middle_file);
        // 运行info和cache两个文件中的定时任务信息,并合并剩余信息
        $info = static::merage(static::runWithFile($info_file, true, false), static::sort(static::runWithFile($cache_file, false, true)));
        // 设置下次运行时间,下一个任务的执行时间,不存在则60秒后运行
        static::setNextRunTime(isset($info[0]) ? $info[0]['time'] : $now + 60);
        // 清空缓存并写入
        static::writeInfo($info);
        unset($info);
        // 删除中间缓存文件
        File::delete($cache_middle_file);
        // 解锁
        static::unlocked();
        static::recordLog(date('Y-m-d H:i:s', $now).'执行定时任务');
    }

    /**
     * 读取文件并执行,并返回剩余信息
     *
     * @param   string   $file    文件
     * @param   bool     $tidied  整理过
     * @param   bool     $delete  是否删除
     *
     * @return  array
     */
    private static function runWithFile(string $file, bool $tidied = false, bool $delete = false) : array
    {
        $result = [];
        $now    = time();
        // 文件存在
        if (is_file($file)) {
            // 只读
            $fp = fopen($file, 'r');
            // 获取一行
            while (false !== $info = fgets($fp)) {
                // 获得单挑信息
                $info = static::readOneInfo($info);
                // 如果已经过时,执行对应任务
                if ((int)$info['time'] <= $now) {
                    // 调用方法并移除
                    CronMethod::runMethod($info);
                } else {
                    // 如果整理过,将剩余数据直接放入结果数组,并返回
                    if ($tidied) {
                        while (false !== $info = fgets($fp)) {
                            $result[] = static::readOneInfo($info);
                        }
                        break;
                    // 放入结果数组,继续执行
                    } else {
                        $result[] = $info;
                    }
                }
            }
            // 关闭指针
            fclose($fp);
            // 删除
            $delete && File::delete($file);
        }
        return $result;
    }

    // 信息写入
    private static function writeInfo(array $info) : void
    {
        $resource = fopen(static::getFilePath('info'), 'w');
        for ($i = 0,$l = count($info); $i < $l; ++$i) {
            fwrite($resource, $info[$i]['time'] . '[' . $info[$i]['type'] . '](' . $info[$i]['value'] . ')' . PHP_EOL);
        }
        fclose($resource);
    }

    // 读取一条信息
    private static function readOneInfo(string $text) : array
    {
        //time[type](value)
        //->time[type   value)
        [$left, $value] = explode('](', $text, 2);
        //->time name type value)
        [$time, $type]  = explode('[', $left, 2);
        // 移除末尾)
        $value          = substr($value, 0, -1);
        // 返回值
        return ['time'=>$time,'value'=>$value,'type'=>$type];
    }

    // 信息合并
    private static function merage(array $info, array $append) : array
    {
        $result = [];
        while (isset($info[0]) && isset($append[0])) {
            $result[] = $info[0]['time'] <= $append[0]['time'] ? array_shift($info) : array_shift($append);
        }
        return array_merge($result, $info, $append);
    }

    // 信息排序
    private static function sort(array $arr) : array
    {
        // 数组长度
        $l = count($arr);
        if ($l <= 1) {
            return $arr;
        }
        $mid   = $arr[0];
        $left  = [];
        $right = [];
        for (--$l; $l > 0;--$l) {
            $mid['time'] > $arr[$l]['time'] && ($left[]=$arr[$l]) || ($right[]=$arr[$l]);
        }
        return array_merge(static::sort($left), [$mid], static::sort($right));
    }
}

trait CronLockTrait
{
    private static $lock_handler = null;

    // 文件锁,避免同时运行多个cron
    private static function isLocked() : bool
    {
        // 不为null,即已经获得锁文件资源,即为锁
        if (static::$lock_handler !== null) {
            return false;
        }
        // 文件路径,不存在,则创建
        $file = static::getFilePath('lock');
        !is_file($file) && File::write($file, '');
        // 新建文件资源
        $resource = fopen($file, 'w');
        // 写独占锁,能写入则没锁
        if (flock($resource, LOCK_EX)) {
            // 赋值
            static::$lock_handler = $resource;
            return false;
        // 已锁,返回true
        } else {
            fclose($resource);
            return true;
        }
    }

    // 解锁
    private static function unlocked() : void
    {
        if (null !== static::$lock_handler) {
            // 关闭资源
            fclose(static::$lock_handler);
            // 致null
            static::$lock_handler = null;
        }
    }
}

trait CronNextTimeTrait
{
    private static $next_run_time = null;
    // 得到下一次运行时间
    public static function getNextRunTime() : int
    {
        if (static::$next_run_time !== null) {
            return static::$next_run_time;
        }

        $next_file = static::getFilePath('next_run_time');
        // 存在,直接返回
        if (is_file($next_file)) {
            return static::$next_run_time = (int) File::get($next_file);
        // 文件不存在,写入60秒后执行
        } else {
            $time = time() + 60;
            File::write($next_file, $time);
            return static::$next_run_time = $time;
        }
    }
    // 设置下一次运行时间
    public static function setNextRunTime(int $time) : void
    {
        File::write(static::getFilePath('next_run_time'), $time);
        static::$next_run_time = $time;
    }
}