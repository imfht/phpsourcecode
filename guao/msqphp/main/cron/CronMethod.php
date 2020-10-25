<?php declare(strict_types = 1);
namespace msqphp\main\cron;

use msqphp\base;

final class CronMethod
{
    private static function exception(string $message) : void
    {
        throw new CronException($message);
    }
    /**
     * @param array  $info = [
     *   'name'    => (string)
     *   'value'   => (string|??)
     *   'type'    => (string)
     *   'time'    => (int)
     * ]
     *
     * @throws CronException
     * @return void
     */

    public static function runMethod(array $info) : void
    {
        switch ($info['type']) {
            case 'deleteFile':
                static::deleteFile($info);
                break;
            case 'clearCache':
                static::clearCache();
                break;
            case 'clearView':
                static::clearView();
                break;
            case 'callback':
                static::callback();
                break;
            default:
                static::exception($info['type'].'未知的事件code码');
        }
        Cron::recordLog(date('Y-m-d H:i:s').'执行任务[类型:]'.$info['type'].'[值:]'.$info['value']);
    }
    // 删除文件
    private static function deleteFile(array $info) : void
    {
        try {
            base\file\File::delete($info['value'], true);
        } catch (base\file\FileException $e) {
            static::exception('定时任务执行失败,文件'.$info['value'].'无法删除,原因:'.$e->getMessage());
        }
    }
    private static function callback(array $info) : void
    {
        [$class,$method] = explode('@', $info['value'], 2);
        if (false !== str_pos($method, '?')) {
            [$method, $args] = explode('@', $method, 2);
            $args = false !== str_pos($args, '&') ? explode('&', $args) : [$args];
            foreach ($args as $key => $value) {
                if ($value[0] === '(') {
                    switch(substr($value, 1, strpos($value, ')'))) {
                        case 'int':
                            $value = (int)$value;
                            break;
                        case 'bool':
                            $value = strtolower((string)$value) === 'true' ? true : false;
                            break;
                        case 'string':
                        default:
                            $value = (string)$value;
                            break;
                    }
                }
            }
        }
        call_user_func_array([$class,$method], $args ?? []);
    }
}