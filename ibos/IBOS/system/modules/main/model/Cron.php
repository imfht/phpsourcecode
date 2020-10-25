<?php

/**
 * cron表对应数据层操作
 *
 * @package application.app.main.model
 * @version $Id$
 */

namespace application\modules\main\model;

use application\core\model\Model;
use application\core\model\Module;
use application\core\utils\Ibos;

class Cron extends Model
{

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return '{{cron}}';
    }

    /**
     * 查找下一条可执行的定时任务
     * @param integer $timestamp 用于对比的时间戳
     * @return array
     */
    public function fetchByNextRun($timestamp = TIMESTAMP)
    {
        /**
         * 可执行的计划任务的条件
         * 1. 时间到
         * 2. 计划任务没有被禁用
         * 3. 模块需要安装且不能被禁用
         */
        $timestamp = intval($timestamp);
        return Ibos::app()->db->createCommand()
            ->select('c.*')
            ->from($this->tableName().' c')
            ->leftJoin(Module::model()->tableName().' m', 'm.module = c.module')
            ->where("c.`available` > 0 AND c.`nextrun`<={$timestamp} AND m.disabled = 0")
            ->order('c.nextrun')
            ->queryRow();
    }

    /**
     * 按照下一次执行时间排序的下一条定时任务
     * @return array
     */
    public function fetchByNextCron()
    {
        return Ibos::app()->db->createCommand()
            ->select('c.*')
            ->from($this->tableName().' c')
            ->leftJoin(Module::model()->tableName().' m', 'm.module = c.module')
            ->where("c.`available` > 0 AND m.disabled = 0")
            ->order('c.nextrun')
            ->queryRow();
    }

}
