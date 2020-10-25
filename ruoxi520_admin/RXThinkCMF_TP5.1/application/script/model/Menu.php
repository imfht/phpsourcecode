<?php


namespace app\script\model;

use app\common\model\BaseModel;

/**
 * 菜单-模型
 * @author zongjl
 * @date 2019/6/25
 * Class Menu
 * @package app\script\model
 */
class Menu extends BaseModel
{
    // 设置数据表
    protected $table = DB_PREFIX . 'menu';

    /**
     * 获取缓存信息
     * @param int $id 记录ID
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @author zongjl
     * @date 2019/6/25
     */
    public function getInfo($id)
    {
        return parent::getInfo($id);
    }
}
