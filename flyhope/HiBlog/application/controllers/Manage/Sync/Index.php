<?php
/**
 * 发布管理
 *
 * @package Controller
 * @author  chengxuan <i@chengxuan.li>
 */
class Manage_Sync_IndexController extends AbsController {
    
    
    public function indexAction() {
        var_dump(Api\Github\Respositories::init()->userRepos());
        exit;
    }
    
} 
 