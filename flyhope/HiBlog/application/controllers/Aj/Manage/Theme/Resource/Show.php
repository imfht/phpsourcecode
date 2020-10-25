<?php
/**
 * 显示一个模板资源内容
 *
 * @package Controller
 * @author chengxuan <i@chengxuan.li>
 */
class Aj_Manage_Theme_Resource_ShowController extends Aj_AbsController {
    
    //控制器入口
    public function indexAction() {
        $id = Comm\Arg::get('id', FILTER_VALIDATE_INT);
        $result = Model\Theme\Resource::show($id);
        if(empty($result)) {
            throw new Exception\Msg('指定模板资源不存在');
        }
        
        //判断是否为只读
        $main = Model\Theme\Main::show($result['tpl_id']);
        try {
            Model\User::validateAuth($main['user_id']);
            $readonly = false;
        } catch(Exception $e) {
            $readonly = true;
        }
        
        Comm\Response::json(100000, 'succ', array(
            'content'  => $result['content'],
            'readonly' => $readonly,
        ), false);
    }
    
    
}