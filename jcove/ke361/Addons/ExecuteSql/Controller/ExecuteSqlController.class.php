<?php

namespace Addons\ExecuteSql\Controller;
use Home\Controller\AddonsController;
use Think\Db;
class ExecuteSqlController extends AddonsController{
    public function executeSql(){
        $sql = I('post.sql');
        $orginal = C('ORIGINAL_TABLE_PREFIX');
        $prefix  = C('DB_PREFIX');
        $sql = str_replace(" `{$orginal}", " `{$prefix}", $sql);
        $db = Db::getInstance();
        $res = $db->execute($sql);
       
        if(false!==$res){
            $this->success('操作成功');
        }else {
            $this->error('执行失败：'.$db->getError());
        }
    }
}
