<?php
/**
 * 通用提醒
 * User: liuzimu
 * Date: 2017/10/18
 * Time: 16:04
 */
namespace application\modules\message\utils;

use application\core\utils\Convert;
use application\core\utils\Ibos;

class AlarmUtil {

    const ALARM_TYPE_CUSTOM = 0; // 自定义时间
    const ALARM_TYPE_ASSOCIATED = 1; // 关联事件时间
    /**
     * 获得闹钟提醒的全部配置
     */
    public static function getAllAlarmConfig()
    {
        $configFilePath = "application.modules.message.config.alarm.config";
        $file = Ibos::getPathOfAlias($configFilePath) . '.php';
        return require($file);
    }

    /**
     * 获得模块节点下的闹钟关联事件配置
     * @param $module
     * @param $node
     */
    public static function getAlarmConfig($module, $node)
    {
        static $config = null;
        if($config === null){
            $config = self::getAllAlarmConfig();
        }
        if(empty($config[$module][$node])) {
            return false;
        }
        return $config[$module][$node];
    }

    /**
     * 检查时间是合法提醒时间
     * @param $module 模块
     * @param $node 节点
     * @param $alarmType 传参的通用闹钟提醒类型
     * @param $eventId 事件id
     * @param $sendTime 自定义时间时间戳
     * @param $diffeTime 差异时间分钟数
     */
    public static function checkAlarmTime($module, $node, $alarmType, $eventId, $sendTime, $diffeTime, $timeNode)
    {
        switch ($alarmType) {
            case 0: // 自定义时间
                if(!is_numeric($sendTime)) {
                    return false; // 不是时间戳
                }
                if($sendTime < TIMESTAMP) { // TODO 看前端传回的数值再修改
                    // 提醒时间小于当前时间，提醒无意义
                    return false;
                }
                break;
            case 1: // 关联事件时间
                $eventTime = self::getEventTimeByModuleAndNode($module, $node, $eventId, $diffeTime ,$timeNode);
                if($eventTime === false) {
                    return false;
                }
                if($eventTime < TIMESTAMP) {
                    // 提醒时间小于当前时间，提醒无意义
                    return false;
                }
                break;
            default:
                return false;
                break;
        }
        return true;
    }

    /**
     * 根据模块和事件节点以及事件节点以及差异时间获取事件时间戳
     * @param $module
     * @param $node
     * @param $eventId
     * @param $diffeTime
     * @param $timeNode
     */
    public static function getEventTimeByModuleAndNode($module, $node, $eventId, $diffeTime ,$timeNode)
    {
        /**
         * 业务逻辑
         * 关联事件时间指的是该时间是关于一个事件的时间，如会议开始时
         * 1.判断配置中有没有该关联事件的时间节点
         * 2.使用表名、id名、查询字段名、事件的id查出该时间(如会议开始时是查询会议表的begin字段)
         */
        if(empty($eventId) || !is_numeric($diffeTime)) {
            return false;
        }

        $alarmTimeNodeConfig = self::getAlarmTimeNodeConfig($module, $node, $timeNode);
        if(empty($alarmTimeNodeConfig['tableName'])
            || empty($alarmTimeNodeConfig['fieldName'])
            || empty($alarmTimeNodeConfig['idName'])) {
            return false;
        }
        $tableName = $alarmTimeNodeConfig['tableName'];
        $fieldName = $alarmTimeNodeConfig['fieldName'];
        $idName = $alarmTimeNodeConfig['idName'];

        // 查询关联事件的时间
        $eventTime = self::getEventTime($tableName, $fieldName, $idName, $eventId, $diffeTime);
        return $eventTime;
    }

    /**
     * 将分钟的差异时间转换为时间戳加减，符号要保留
     * @param $diffeTime
     */
    public static function getDiffeTimeTimeStamp($diffeTime)
    {
        return $diffeTime * 60;
    }

    /**
     * 获得模块下的事件节点的事件节点配置
     * @param $module
     * @param $node
     * @param $timeNode
     */
    public static function getAlarmTimeNodeConfig($module, $node, $timeNode)
    {
        $alarmNodeConfig = self::getAlarmConfig($module, $node);

        if( empty($alarmNodeConfig['timeNodes']) ){
            return array();
        }
        foreach ($alarmNodeConfig['timeNodes'] as $timeNodeItem) {
            if($timeNodeItem['timeNode'] == $timeNode){
                return $timeNodeItem;
            }
        }

        return array();
    }

    /**
     * 根据配置的表名和字段名从数据库中查找事件时间
     * @param $tableName
     * @param $fieldName
     * @param $eventId
     */
    public static function getEventTime($tableName, $fieldName, $idName, $eventId, $diffeTime)
    {
        $eventTime = Ibos::app()->db->createCommand()
            ->select($fieldName)
            ->from($tableName)
            ->where("`{$idName}` = :eventId", array(':eventId' => $eventId))
            ->queryRow();

        if(empty($eventTime[$fieldName]) || !is_numeric($eventTime[$fieldName])) {
            return false;
        }
        $sendTime = $eventTime[$fieldName] + self::getDiffeTimeTimeStamp($diffeTime);
        // 查询关联事件的时间
        return $sendTime;
    }

    /**
     * 是否存在通用闹钟提醒节点
     * @param $module
     * @param $node
     * @return bool
     */
    public static function isExistAlarmNode($module, $node)
    {
        $alarmNodeConfig = AlarmUtil::getAlarmConfig($module, $node);
        if(!$alarmNodeConfig) {
            return false;
        }
        return true;
    }

    /**
     * 节点配置是否支持该通用闹钟类型
     * @param $module
     * @param $node
     * @param $alarmType
     * @return bool
     */
    public static function isExistAlarmType($module, $node, $alarmType)
    {
        $alarmNodeConfig = AlarmUtil::getAlarmConfig($module, $node);
        // 判断该节点是否支持闹钟提醒类型
        if(!in_array($alarmType, (array) $alarmNodeConfig['alarmType'])) {
            return false;
        }
        return true;
    }

    /**
     * 获得所有通用闹钟提醒类型
     * @return array
     */
    public static function getAllAlarmType()
    {
        return array(
            self::ALARM_TYPE_CUSTOM,
            self::ALARM_TYPE_ASSOCIATED
        );
    }

    /**
     * 获得添加、修改配置的前端部分
     * @param $module
     * @param $node
     */
    public static function getAlarmConfigView($module, $node, $eventId)
    {
        // 获取参数
        $nodeConfig = self::getAlarmConfig($module, $node);
        if(empty($nodeConfig)) {
            return array();
        }

        foreach ($nodeConfig['timeNodes'] as $key => $timeNodeConifg) {
            $sentTime = 0;
            if(!empty($eventId)){
                $sentTime = AlarmUtil::getEventTimeByModuleAndNode($module, $node, $eventId, 0, $timeNodeConifg['timeNode']);
            }
            // 如果事件时间为空的话，取消该配置的选择
            if($sentTime === false || $sentTime <= 0){
                unset($nodeConfig['timeNodes'][$key]);
                continue;
            }
            unset($nodeConfig['timeNodes'][$key]['tableName']);
            unset($nodeConfig['timeNodes'][$key]['fieldName']);
            unset($nodeConfig['timeNodes'][$key]['idName']);
            $nodeConfig['timeNodes'][$key]['eventTime'] = $sentTime;
        }

        return $nodeConfig;
    }

    /**
     * 根据前端传的paramData构建url
     * @param $module
     * @param $node
     * @param $parmaData 参数数组或json对象
     */
    public static function getEventUrlByParamData($module, $node, $parmaData)
    {
        // 获取节点 配置
        $nodeConfig = self::getAlarmConfig($module, $node);
        if(empty($nodeConfig) || empty($nodeConfig['eventUrl']) ) {
            return '';
        }

        if(empty($parmaData)) {
            return '';
        }

        $parmaData = is_array($parmaData) ? $parmaData : json_decode($parmaData, true);

        // 构建url
        return Ibos::app()->urlManager->createUrl($nodeConfig['eventUrl'], $parmaData);
    }

    /**
     * 获得列表的人性化时间
     * @param $alarmType
     * @param $stime
     * @param $module
     * @param $node
     * @param $eventId
     * @param $diffeTime
     * @param $timeNode
     */
    public static function getListShowTime($alarmType, $stime, $module, $node, $eventId, $diffeTime ,$timeNode)
    {
        if($alarmType == 1) {
            // 关联事件时间
            $showTime = self::getEventTimeByModuleAndNode($module, $node, $eventId, $diffeTime ,$timeNode);
        } else {
            // 自定义时间
            $showTime = $stime;
        }
        if(!is_numeric($showTime) || $showTime <= 0){
            return Ibos::lang('Alarm time err');
        }
        return Convert::formatDate($showTime, 'u');
    }
}