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
 * 配置管理-模型
 * @author 牧羊人
 * @since 2020/7/10
 * Class Config
 * @package app\admin\model
 */
class Config extends BaseModel
{
    // 设置数据表名
    protected $name = 'config';

    /**
     * 获取缓存信息
     * @param int $id 记录ID
     * @return \app\common\model\数据信息|mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @since 2020/7/10
     * @author 牧羊人
     */
    public function getInfo($id)
    {
        $info = parent::getInfo($id);
        if ($info) {
            // 类型名称
            $info['type_name'] = config('admin.config_type')[$info['type']];

            // 分组名称
            if ($info['group_id']) {
                $configGroupMod = new ConfigGroup();
                $groupInfo = $configGroupMod->getInfo($info['group_id']);
                $info['group_name'] = $groupInfo['name'];
            }

            // 类型解析
            switch ($info['type']) {
                case "image":
                    // 单图
                    $info['image_url'] = get_image_url($info['value']);
                    break;
                case "images":
                    $imgArr = unserialize($info['value']);
                    if ($imgArr) {
                        $imgList = [];
                        foreach ($imgArr as $val) {
                            $imgList[] = get_image_url($val);
                        }
                        $info['imgsList'] = $imgList;
                    }
                    break;
                case "ueditor":
                    if ($info['value']) {
                        while (strstr($info['value'], "[IMG_URL]")) {
                            $info['value'] = str_replace("[IMG_URL]", IMG_URL, $info['value']);
                        }
                    }
                    break;
            }
        }
        return $info;
    }
}
