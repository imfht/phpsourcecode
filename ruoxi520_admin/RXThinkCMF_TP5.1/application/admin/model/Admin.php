<?php
// +----------------------------------------------------------------------
// | RXThinkCMF框架 [ RXThinkCMF ]
// +----------------------------------------------------------------------
// | 版权所有 2017~2020 南京RXThinkCMF研发中心
// +----------------------------------------------------------------------
// | 官方网站: http://www.rxthink.cn
// +----------------------------------------------------------------------
// | Author: 牧羊人 <1175401194@qq.com>
// +----------------------------------------------------------------------

namespace app\admin\model;

use app\common\model\BaseModel;
use app\admin\model\Role as AdminRoleModel;

/**
 * 人员-模型
 * @author 牧羊人
 * @since 2020/7/11
 * Class Admin
 * @package app\admin\model
 */
class Admin extends BaseModel
{
    // 设置数据表
    protected $name = 'admin';

    // 开启单表缓存
    protected $is_cache = true;

    /**
     * 获取缓存信息
     * @param int $id 记录ID
     * @return \app\common\model\数据信息|mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @since 2020/7/11
     * @author 牧羊人
     */
    public function getInfo($id)
    {
        $info = parent::getInfo($id);
        if ($info) {
            // 头像
            if ($info['avatar']) {
                $info['avatar_url'] = get_image_url($info['avatar']);
            }

            // 入职日期
            if ($info['entry_date']) {
                $info['format_entry_date'] = datetime($info['entry_date'], 'Y-m-d');
            }

            // 性别
            if ($info['gender']) {
                $info['gender_name'] = config('admin.gender_list')[$info['gender']];
            }

            // 岗位
            if ($info['position_id']) {
                $positionModel = new Position();
                $positionInfo = $positionModel->getInfo($info['position_id']);
                $info['position_name'] = $positionInfo['name'];
            }

            // 职级
            if ($info['level_id']) {
                $levelMod = new Level();
                $levelInfo = $levelMod->getInfo($info['level_id']);
                $info['level_name'] = $levelInfo['name'];
            }

            // 所属城市
            if ($info['district_id']) {
                $cityMod = new City();
                $cityName = $cityMod->getCityName($info['district_id'], " ");
                $info['city_name'] = $cityName;
            }

            // 获取人员权限
            $adminRomMod = new AdminRom();
            $permissionList = $adminRomMod->getPermissionList($id);
            $info['permission'] = $permissionList;
        }
        return $info;
    }
}
