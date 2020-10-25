<?php
/**
 * 主题编辑
 *
 * @package Controller
 * @author  chengxuan <i@chengxuan.li>
 */
class Manage_Theme_EditController extends AbsController {
    
    public function indexAction() {
        $id = \Comm\Arg::get('id', FILTER_VALIDATE_INT);
        
        //获取模板基础内容
        $theme = Model\Theme\Main::show($id);
        if(empty($theme)) {
            throw new \Exception\Msg('指定模板不存在');
        }
        
        //权限验证
        Model\User::validateAuth($theme['user_id']);
        
        //获取资源内容
        $resource = Model\Theme\Resource::showByTheme($theme);
        
        $this->viewDisplay(array(
            'theme'    => $theme,
            'resource' => $resource,
        ));
    }
}
