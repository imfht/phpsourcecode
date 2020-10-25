<?php

namespace Admin\Controller;

use Think\Controller;

class CacheController extends CommonController {

    //清理链接缓存
    public function delLinkeCache() {
        S('nav', null);
        S('links', null);
        $this->success('清理链接缓存成功', U('/Admin/Admin/main'));
    }
    
     //清理站点信息缓存
    public function delSiteOptionCache() {
        S('option',null);
        $this->success('清理站点信息缓存成功', U('/Admin/Admin/main'));
    }
  

}
