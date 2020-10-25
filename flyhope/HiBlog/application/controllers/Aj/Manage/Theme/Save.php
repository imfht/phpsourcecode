<?php
/**
 * 保存模板内容
 *
 * @package Controller
 * @author chengxuan <i@chengxuan.li>
 */
class Aj_Manage_Theme_SaveController extends Aj_AbsController {
    
    //控制器入口
    public function indexAction() {
        $id = Comm\Arg::post('id', FILTER_VALIDATE_INT);
        $content = Comm\Arg::post('content');
        
        $result = Model\Theme\Resource::update($id, $content);
        
        Comm\Response::json(100000, '保存成功', ['result' => $result], false);
    }
    
}
