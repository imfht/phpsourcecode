<?php
namespace Home\Controller;

class CategoryGoodsController extends HomeController
{
     public function index(){      
        $tree = D('CategoryGoods')->getTree(0,'id,name,category_name,sort,pid,icon');
        $this->assign('tree', $tree);     
        $this->meta_title = '分类';
        $this->display();;
     }
     /**
      * 显示分类树，仅支持内部调
      * @param  array $tree 分类树
      * @author 麦当苗儿 <zuojiazi@vip.qq.com>
      */
     public function tree($tree = null){
      
         $this->assign('tree', $tree);
         $this->display('tree');
     }
     public function childrenTree($tree = null){
     
         $this->assign('tree', $tree);
         $this->display('childrenTree');
     }
}

?>