<?php
/**
 * 前往自己的Github Page
 *
 * @author chengxuan <i@chengxuan.li>
 */
class User_Github_GopageController extends AbsController {
    
    
    public function indexAction() {
        $repo = Model\Github::showDefaultBlogRepo();
        if(!empty($repo->name)) {
            return $this->redirect("http://{$repo->name}/");
        }
        
        return false;
    }
    
    
}
