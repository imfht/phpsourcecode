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

use think\Model;

/**
 * 钩子模型
 * @Author: rainfer <rainfer520@qq.com>
 */
class Hook extends Model
{
    protected $autoWriteTimestamp = true;

    /**
     * 添加钩子
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
            $data = [];
            foreach ($hooks as $name => $description) {
                if (is_numeric($name)) {
                    $name        = $description;
                    $description = '';
                }
                if (self::where('name', $name)->find()) {
                    continue;
                }
                $data[] = [
                    'name'        => $name,
                    'addon'       => $addon_name,
                    'description' => $description,
                    'create_time' => request()->time(),
                    'update_time' => request()->time(),
                ];
            }
            if ($data && false === self::insertAll($data)) {
                return false;
            }
        }
        return true;
    }

    /**
     * 删除钩子
     *
     * @param string $addon_name 插件名称
     *
     * @return bool
     */
    public function deleteHooks($addon_name = '')
    {
        if ($addon_name) {
            if (false === self::where('addon', $addon_name)->delete()) {
                return false;
            }
        }
        return true;
    }
}
