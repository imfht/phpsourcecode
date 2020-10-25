<?php
// +----------------------------------------------------------------------
// | openWMS (开源wifi营销平台)
// +----------------------------------------------------------------------
// | Copyright (c) 2015-2025 http://cnrouter.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.gnu.org/licenses/gpl-2.0.html )
// +----------------------------------------------------------------------
// | Author: PhperHong <phperhong@cnrouter.com>
// +----------------------------------------------------------------------
namespace admin\Controller;
use Think\Controller;
class EmptyController extends Controller {
    function _initialize(){    
        $cop = C('COPYRIGHT');
        $cop['web_site'] = C('WEB_SITE');
        $this->assign($cop);
        $this->display('Empty:index');
    }
    public function index(){       
        
        
    }

}