<?php
/**
 * YICMS
 * ============================================================================
 * 版权所有 2014-2017 YICMS，并保留所有权利。
 * 网站地址: http://www.yicms.vip
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！
 * 不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * Created by PhpStorm.
 * Author: kenuo
 * Date: 2017/11/20
 * Time: 下午2:29
 */

namespace App\Observers;

use App\Models\Admin;

class AdminObserver
{
    /**
     * @param Admin $admin
     */
    public function updating(Admin $admin)
    {
        $admin->clearRuleAndMenu();
    }

    /**
     * 监听用户删除事件
     */
    public function deleting(Admin $admin)
    {
        $admin->clearRuleAndMenu();
    }
}