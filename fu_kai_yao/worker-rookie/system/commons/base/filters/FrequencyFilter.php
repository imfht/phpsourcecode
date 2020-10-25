<?php
namespace system\commons\base\filters;

use workerbase\classs\RedisLock;
use workerbase\traits\Response;

/**
 * 请求频率过滤器
 * @author fukaiyao 2020-2-21
 */
class FrequencyFilter
{
    /**
     * 使用方式：
     * 控制器定义filters方法：
     *  public function filters()
        {
            //注册过滤器
            return [
                '\app\common\base\filters\FrequencyFilter'
            ];
        }
     *
     */

    use Response;

    /**
     * 过滤器初始化
     */
    public function init()
    {

    }

    /**
     * 执行过滤方法，并发处理：1秒内只能请求一次
     * @param array $args 请求参数
     * @return bool
     */
    public function preFilter($args)
    {
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $lock = RedisLock::getInstance()->lock($uri,  1, 1);
        if (!$lock) {
            return $this->showResponse(0, '您的操作过于频繁，请休息一会再试！');
        }
        return true;
    }
}