<?php
/**
 * 消息提醒控制器 基础 Action
 * @package application.modules.message.actions
 * @version $Id$
 */
namespace application\modules\message\actions\api;

use application\core\utils\Ibos;
use application\core\utils\Page;
use CAction;
use application\core\utils\Env;
use application\modules\message\utils\AlarmUtil;
use CHtml;
use Exception;

class Base extends CAction
{
    /**
     * 增加、修改获得前端来的数据，整理好数据字段
     * @staticvar array $data 前端数据
     * @return ajaxReturn
     */
    public function getAlarmFormData()
    {
        static $data = null;
        if (null === $data) {
            // 接收参数
            $data = array(
                'title' => Env::getRequest('title'), // 标题
                'receiveuids' => Env::getRequest('receiveUids'), // 接收提醒的用户ID,逗号隔开
                'alarmtype' => Env::getRequest('alarmType'), // 提醒类型：0为自定义时间，1为关联事件时间
                'module' => Env::getRequest('module'), // 所属模块
                'eventid' => Env::getRequest('eventId'), // 关联事件id
//                'url' => Env::getRequest('url'), // 关联事件url
                'paramdata' => Env::getRequest('paramData'), // 关联事件url参数
                'body' => Env::getRequest('body'), // 备注内容
                'diffetime' => Env::getRequest('diffeTime'), // 差异量:分钟数,负数代表提前，正数代表增加
                'node' => Env::getRequest('node'), // 关联事件节点
                'stime' => Env::getRequest('sendTime'), // 自定义时间
                'timenode' => Env::getRequest('timeNode') // 事件时间节点
            );
        }
        return $data;
    }

    /**
     * 处理验证添加、修改通用提醒表单数据
     * @param $data
     */
    public function manageNotifyAlarmData(&$data)
    {
        // 处理参数达到入库的标准
        $data['title'] = !empty($data['title']) ? CHtml::encode($data['title']) : '';
//        $data['url'] = !empty($data['url']) ? CHtml::encode($data['url']) : '';
        $data['body'] =!empty($data['body']) ? CHtml::encode($data['body']) : '';

        $alarmTypeA = AlarmUtil::getAllAlarmType();

        // 提醒谁
        if(empty($data['receiveuids'])) {
            throw new Exception(Ibos::lang("Please select a remind people"));
        }

        // 隐性必填
        if(empty($data['title']) || empty($data['module']) || empty($data['node'])) {
            throw new Exception(Ibos::lang("Params error"));
        }

        // 判断闹钟类型参数
        if (!is_numeric($data['alarmtype']) || !in_array($data['alarmtype'], $alarmTypeA)) {
            throw new Exception(Ibos::lang("Params error"));
        }

        // 判断配置中是否有该节点
        if(!AlarmUtil::isExistAlarmNode($data['module'], $data['node'])) {
            throw new Exception(Ibos::lang("Params error"));
        }

        // 判断该节点是否支持提醒类型
        if(!AlarmUtil::isExistAlarmType($data['module'], $data['node'], $data['alarmtype'])) {
            throw new Exception(Ibos::lang("Params error"));
        }

        // 给提醒节点的赋值
        if($data['alarmtype'] == 1) {
            $alarmTimeNodeConfig = AlarmUtil::getAlarmTimeNodeConfig($data['module'], $data['node'], $data['timenode']);
            if(empty($alarmTimeNodeConfig['tableName'])
                || empty($alarmTimeNodeConfig['fieldName'])
                || empty($alarmTimeNodeConfig['idName'])) {
                throw new Exception(Ibos::lang("Params error"));
            }
            $data['tablename'] = $alarmTimeNodeConfig['tableName'];
            $data['fieldname'] = $alarmTimeNodeConfig['fieldName'];
            $data['idname'] = $alarmTimeNodeConfig['idName'];
        }

        // 组装url参数
        $data['url'] = AlarmUtil::getEventUrlByParamData($data['module'], $data['node'], $data['paramdata']);

        // 判断时间
        if(!AlarmUtil::checkAlarmTime(
            $data['module'],
            $data['node'],
            $data['alarmtype'],
            $data['eventid'],
            $data['stime'],
            $data['diffetime'],
            $data['timenode']
            )) {
            throw new Exception(Ibos::lang("Notily sendTime error"));
        }

        $data['showtime'] = AlarmUtil::getListShowTime($data['alarmtype'], $data['stime'], $data['module'], $data['node'], $data['eventid'], $data['diffetime'] ,$data['timenode']);
    }

    /**
     * 获取通用提醒列表
     * 处理前端传来的查询数据
     */
    public function getNotifyAlarmListSelectData()
    {
        static $data = null;
        if (null === $data) {
            $data = array(
                'module' => Env::getRequest('module'), // 所属模块
                'search' => Env::getRequest('search'), // 搜索提醒内容
                'eventid' => Env::getRequest('eventId'), // 事件id
                'offset' => Env::getRequest('offset'), // offset
                'pagesize' => Env::getRequest('pageSize'), // 一页多少条
                'node' => Env::getRequest('node'), // 事件节点
            );
            $data['search'] =!empty($data['search']) ? CHtml::encode($data['search']) : null;
            $data['eventid'] =!empty($data['eventid'])  ? $data['eventid'] : null;
            $data['pagesize'] = !empty($data['pagesize']) && is_numeric($data['pagesize']) ? $data['pagesize'] : page::DEFAULT_PAGE_SIZE;
            $data['offset'] = !empty($data['offset']) && is_numeric($data['offset']) ? $data['offset'] : 0;
        }
        return $data;
    }

    /**
     * 获取通用提醒关联事件时间
     * 处理前端传来的数据
     */
    public function getNotifyAlarmEventTimeData()
    {
        static $data = null;
        if(null === $data) {
            $data = array(
                'module' => Env::getRequest('module'), // 所属模块
                'node' => Env::getRequest('node'), // 事件节点
                'eventid' => Env::getRequest('eventId'), // 事件id
                'timenode' => Env::getRequest('timeNode'), // 时间节点
                'diffetime' => Env::getRequest('diffeTime'), // 差异时间，分钟数
            );
        }
        // 参数过滤
        if(empty($data['module']) || empty($data['node']) || empty($data['eventid']) || empty($data['timenode'])){
            throw new Exception(Ibos::lang("Params error"));
        }
        if(!is_numeric($data['diffetime'])){
            throw new Exception(Ibos::lang("Params error"));
        }
        return $data;
    }

    /**
     * 后续处理列表数据
     * @param $list
     */
    public function afterProcessingList(&$list)
    {
        foreach ($list as &$val) {
            // 加上处理好的人性化时间
            $val['showtime'] =  AlarmUtil::getListShowTime($val['alarmtype'], $val['stime'], $val['module'], $val['node'], $val['eventid'], $val['diffetime'] ,$val['timenode']);
        }
    }
}