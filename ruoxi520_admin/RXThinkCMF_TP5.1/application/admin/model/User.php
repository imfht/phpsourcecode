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

/**
 * 会员-模型
 * @author 牧羊人
 * @since 2020/7/11
 * Class User
 * @package app\admin\model
 */
class User extends BaseModel
{
    // 设置数据表名
    protected $name = 'user';

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
            // 会员头像
            if ($info['avatar']) {
                $info['avatar_url'] = get_image_url($info['avatar']);
            }

            // 会员性别
            if ($info['gender']) {
                $info['gender_name'] = config('admin.gender_list')[$info['gender']];
            }

            // 设备类型
            if ($info['device']) {
                $info['device_name'] = config("admin.user_device")[$info['device']];
            }

            // 用户来源
            if ($info['source']) {
                $info['source_name'] = config("admin.user_source")[$info['source']];
            }

            // 二维码
            if ($info['qrcode']) {
                $info['qrcode_url'] = get_image_url($info['qrcode']);
            }

//            // 注册时间
//            if ($info['register_time']) {
//                $info['format_register_time'] = datetime($info['register_time']);
//            }
//
//            // 登录时间
//            if ($info['login_time']) {
//                $info['format_login_time'] = datetime($info['login_time']);
//            }
        }
        return $info;
    }
}
