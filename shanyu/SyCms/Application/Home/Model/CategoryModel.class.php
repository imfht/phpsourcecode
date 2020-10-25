<?php
namespace Home\Model;
use Think\Model;

class CategoryModel extends Model{

    public function getCategory($cname){
        $map['name']=$cname;
        $category=$this->where($map)->find();

        $router=C('URL_ROUTER_ON');
        if($router) $category['url']=U('/'.$category['name'],'','html',true);
        else{
            if($v['mid']==0) $v['url']=U(ucfirst($v['name'].'/index'),'','html',true);
            elseif($v['mid']==2) $v['url']=U('Page/index',array($v['id']),'html',true);
            else $v['url']=U('List/index',array($v['id']),'html',true);
        }

        if(!empty($category['setting'])){
            $category['setting']=unserialize($category['setting']);
        }
        return $category;
    }




}