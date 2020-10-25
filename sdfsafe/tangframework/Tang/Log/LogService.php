<?php
// +-----------------------------------------------------------------------------------
// | TangFrameWork 致力于WEB快速解决方案
// +-----------------------------------------------------------------------------------
// | Copyright (c) 2012-2014 http://www.tangframework.com All rights reserved.
// +-----------------------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +-----------------------------------------------------------------------------------
// | HomePage ( http://www.tangframework.com/ )
// +-----------------------------------------------------------------------------------
// | Author: wujibing<283109896@qq.com>
// +-----------------------------------------------------------------------------------
// | Version: 1.0
// +-----------------------------------------------------------------------------------
namespace Tang\Log;
use Tang\Services\ServiceProvider;

/**
 * 日志服务
 * Class LogService
 * @package Tang\Log
 */
class LogService extends ServiceProvider
{
    /**
     * 严重错误: 导致系统崩溃无法使用
     */
    const EMERG = 'EMERG';
    /**
     * 警戒性错误: 必须被立即修改的错误
     */
    const ALERT = 'ALERT';
    /**
     * 临界值错误: 超过临界值的错误，例如一天24小时，而输入的是25小时这样
     */
    const CRIT = 'CRIT';
    /**
     * 一般错误: 一般性错误
     */
    const ERR = 'ERR';
    /**
     * 警告性错误: 需要发出警告的错误
     */
    const WARNING = 'WARNING';
    /**
     * 通知: 程序可以运行但是还不够完美的错误
     */
    const NOTICE = 'NOTIC';
    /**
     * 信息: 程序输出信息
     */
    const INFO = 'INFO';
    /**
     * SQL日志
     */
    const SQL = 'SQL';

    /**
     * @return \Tang\Log\ILogManager
     */
    public static function getService()
    {
        return parent::getService();
    }

    /**
     * @see ServiceProvider::register
     */
    protected static function register()
    {
        $instance =  parent::initObject('log','\Tang\Log\ILogManager');
        //默认采用file日志
        $config = static::$config->replaceGet('log',array('record'=>true,'recordLevel'=>array(),'defaultDriver'=>'file','file'=>array('directory'=>'Log','filePath' => '%Y-%m/TangLog-%y-%m-%d.log'),'database'=>array('tableName' => 'log','source'=>'')));
        $config['file']['dataDirctory'] = static::$config->get('dataDirctory');
        $instance->setConfig($config);
        return $instance;
    }
}