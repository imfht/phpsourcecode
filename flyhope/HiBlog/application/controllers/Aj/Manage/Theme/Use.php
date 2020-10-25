<?php
/**
 * 使用模板
 *
 * @package Controller
 * @author chengxuan <i@chengxuan.li>
 */
class Aj_Manage_Theme_UseController extends Aj_AbsController {
    
    public function indexAction() {
        $theme_id = Comm\Arg::post('theme_id');
        
        $result = Model\Blog::save(array(
            'theme_id' => $theme_id,
        ));
        
        Comm\Response::json(100000, '操作成功', ['result' => $result], false);
    }
    
    
}
