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

namespace app\admin\service;

use app\admin\model\Link;
use app\common\service\BaseService;

/**
 * 友情链接-服务类
 * @author 牧羊人
 * @since 2020/7/10
 * Class LinkService
 * @package app\admin\service
 */
class LinkService extends BaseService
{
    /**
     * 初始化模型
     * @author 牧羊人
     * @date 2019/4/29
     */
    public function initialize()
    {
        parent::initialize();
        $this->model = new Link();
    }

    /**
     * 添加或编辑
     * @return array
     * @since 2020/7/10
     * @author 牧羊人
     */
    public function edit()
    {
        // 参数
        $data = request()->param();
        // 友链形式
        $form = (int)$data['form'];
        // 图片
        $image = trim($data['image']);
        // 图片验证
        if ($form == 2) {
            // 图片
            if (!$image) {
                return message('请上传图片', false);
            }
            if (strpos($image, "temp")) {
                $data['image'] = save_image($image, 'link');
            }
        } else {
            // 文字
            $data['image'] = '';
        }
        return parent::edit($data);
    }
}
