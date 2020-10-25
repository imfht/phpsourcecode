<?php
namespace Home\Model;
use Think\Model;
class CategoryGoodsModel extends Model{
   
    
    public function delCategory($category_id){
        $this->delete($category_id);
    }
    
    public function getGoodsCategory($state=1){
        $where = "type=1 "; 
        $where .= $state?" and status='1'":"1";
        return $this->where($where)->select();
    }
     
    public function getName($category_id){
        $category = $this->find($category_id);
        return $category['category_name'];
    }
    public function getTree($id = 0, $field = true){
      /* 获取当前分类信息 */
        if($id){
            $info = $this->info($id);
            $id   = $info['id'];
        }
         /* 获取所有分类 */
        $map  = array('status' => array('gt', 0));
        $list = $this->field($field)->where($map)->order('sort')->select();
        foreach ($list as $k=>$v){
            $icon = get_cover($v['icon'],'path');
            $list[$k]['icon'] = $icon ? $icon : '';
        }
        $list = list_to_tree($list, $pk = 'id', $pid = 'pid', $child = '_', $root = $id);
       /* 获取返回数据 */
        if(isset($info)){ //指定分类则返回当前分类极其子分类
            $info['_'] = $list;
        } else { //否则返回所有分类
            $info = $list;
        }
        return $info;
    }
    /**
     * 获取指定分类子分类ID
     * @param  string $cate 分类ID
     * @return string       id列表
     * @author 麦当苗儿 <zuojiazi@vip.qq.com>
     */
    public function getChildrenId($cate){
        $field = 'id,name,pid,category_name';
        $category = D('CategoryGoods')->getTree($cate, $field);
        $ids[]    = $cate;
        foreach ($category['_'] as $key => $value) {
            $ids[] = $value['id'];
        }
        return implode(',', $ids);
    }
    /**
     * 获取指定分类所有子分类及孙分类ID
     * @param  string $cate 分类ID
     * @return string       id列表
     * @author 麦当苗儿 <zuojiazi@vip.qq.com>
     */
    public function getAllChildrenId($cate){
        $field = 'id,name,pid,category_name';
        $category = D('CategoryGoods')->getTree($cate, $field);
        $ids[]    = $cate;
        foreach ($category['_'] as $key => $value) {
            $ids[] = $value['id'];
            foreach ($value['_'] as $k => $v){
                $ids[] = $v['id'];
            }
        }
        return implode(',', $ids);
    }
    public function info($id, $field = true){
        /* 获取分类信息 */
        $map = array();
        if(is_numeric($id)){ //通过ID查询
            $map['id'] = $id;
        } else { //通过标识查询
            $map['name'] = $id;
        }
        return $this->field($field)->where($map)->find();
    }
    
}