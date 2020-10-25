<?php
/**
 * 创建分类
 *
 * @author chengxuan <i@chengxuan.li>
 */
class Aj_Manage_Category_CreateController extends Aj_AbsController {

    public function indexAction() {
        $name = Comm\Arg::post('name');
        $alias = Comm\Arg::post('alias');
        Model\Category::create($name, $alias);
        Comm\Response::json(100000, '操作成功', null, false);
    }

}