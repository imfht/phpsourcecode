<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/27
 * Time: 8:49
 */
class AdvController extends  Action{


    public function getAdvById(){
        $advSer=new AdvService();
        $rs=$advSer->getAdvById($_GET['id']);
        $this->sendNotice("SUCCESS",$rs,true);
    }


    public function getAdvByType(){
        $advSer=new AdvService();
        $rs=$advSer->getAdvByType($_GET['typeid']);
        $this->sendNotice("SUCCESS",$rs,true);
    }
}