<?php
// +----------------------------------------------------------------------
// | YFCMF [ WE CAN DO IT MORE SIMPLE]
// +----------------------------------------------------------------------
// | Copyright (c) 2016-2018 http://yfcmf.net All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: rainfer <rainfer520@qq.com>
// +----------------------------------------------------------------------

namespace app\common\model;

use app\common\model\Hook as HookModel;
use think\Model;

/**
 * 钩子-插件模型
 * @Author: rainfer <rainfer520@qq.com>
 */
class HookAddon extends Model
{
    protected $autoWriteTimestamp = true;

    /**
     * 启用插件钩子
     *
     * @param string $addon_name 插件名称
     *
     * @return bool
     */
    public function enable($addon_name = '')
    {
        return self::where('addon', $addon_name)->setField('status', 1);
    }

    /**
     * 禁用插件钩子
     *
     * @param string $addon_name 插件名称
     *
     * @return int
     */
    public function disable($addon_name = '')
    {
        return self::where('addon', $addon_name)->setField('status', 0);
    }

    /**
     * 添加钩子-插件对照
     *
     * @param array  $hooks      钩子
     * @param string $addon_name 插件名称
     *
     * @return bool
     * @throws \think\Exception
     */
    public function addHooks($hooks = [], $addon_name = '')
    {
        if ($hooks && is_array($hooks)) {
            // 添加钩子
            $hook_model = new HookModel();
            if (!$hook_model->addHooks($hooks, $addon_name)) {
                return false;
            }
            $data = [];
            foreach ($hooks as $name => $description) {
                if (is_numeric($name)) {
                    $name = $description;
                }
                $data[] = [
                    'hook'        => $name,
                    'addon'       => $addon_name,
                    'create_time' => request()->time(),
                    'update_time' => request()->time(),
                ];
            }
            return self::insertAll($data);
        }
        return false;
    }

    /**
     * 删除钩子
     *
     * @param string $addon_name 钩子名称
     *
     * @return bool
     */
    public function deleteHooks($addon_name = '')
    {
        if ($addon_name) {
            // 删除钩子
            $hook_model = new HookModel();
            if (!$hook_model->deleteHooks($addon_name)) {
                return false;
            }
            if (false === self::where('addon', $addon_name)->delete()) {
                return false;
            }
        }
        return true;
    }
}
