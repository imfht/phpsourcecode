<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 数据库队列与定时任务
 */
namespace app\system\controller;
use think\Controller;
use app\common\facade\Inform;

class Queuetasks extends Controller {

    /**
     * 微信定时模板消息
     * 通过第三方网站监控访问或通过Linux定时任务访问一下网址即可
     * //***.com/system/queuetasks/inform 
     */   
    public function inform(){
        $rel = Inform::smsQueue();
        return $rel ? 'SUCCESS' : 'FAIL';
    }
    
}