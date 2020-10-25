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

use app\admin\model\ItemCate;
use app\common\service\BaseService;

/**
 * 栏目管理-服务类
 * @author 牧羊人
 * @since 2020/7/10
 * Class ItemCateService
 * @package app\admin\service
 */
class ItemCateService extends BaseService
{
    /**
     * 初始化模型
     * @author 牧羊人
     * @date 2019/5/5
     */
    public function initialize()
    {
        parent::initialize();
        $this->model = new ItemCate();
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
        // 是否有封面
        $is_cover = $data['is_cover'];
        // 封面地址
        $cover = trim($data['cover']);
        //封面验证
        if ($is_cover == 1 && !$data['id'] && !$cover) {
            return message('请上传栏目封面', false);
        }
        if ($is_cover == 1) {
            if (strpos($cover, "temp")) {
                $data['cover'] = save_image($cover, 'itemcate');
            }
        } elseif ($is_cover == 2) {
            $data['cover'] = '';
        }
        return parent::edit($data);
    }
}
