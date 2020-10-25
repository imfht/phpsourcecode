<?php

/**
 * 复制主题
 *
 * @package Controller
 * @author  chengxuan <i@chengxuan.li>
 */
class Aj_Manage_Theme_CopyController extends Aj_AbsController {
    
    //控制器入口
    public function indexAction() {
        $alias_id = Comm\Arg::post('alias_id', FILTER_VALIDATE_INT);
        $name = Comm\Arg::post('name');
        $id = Model\Theme\Main::create($alias_id, $name);
        Comm\Response::json(100000, '操作成功', ['id' => $id], false);
    }
    
}
