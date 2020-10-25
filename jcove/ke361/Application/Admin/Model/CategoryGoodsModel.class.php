<?php
namespace Admin\Model;
use Think\Model;
class CategoryGoodsModel extends Model{
 
    protected $_auto = array(
        array('status', 1, self::MODEL_INSERT),
    );
    public function delCategory($category_id){
        $this->delete($category_id);
    }
    
    public function getGoodsCategory($status=1){
      
        $where .= $status?" status='1'":"1";
      
        $res = $this->where($where)->order('pid,sort')->select();
      
        return $res;
    }
    public function getName($category_id){
        $category = $this->find($category_id);
        return $category['category_name'];
    }
    public function getTree($id = 0,$where='', $field = true){
        /* 获取当前分类信息 */
        if($id){
            $info = $this->info($id);
            $id   = $info['id'];
        }
    
       
        $map  = array('status' => array('gt', -1));
        if(!empty($where)){
            $map = $where;
        }
        $list = $this->field($field)->where($map)->order('sort')->select();
        $list = list_to_tree($list, $pk = 'id', $pid = 'pid', $child = '_', $root = $id);
    
        /* 获取返回数据 */
        if(isset($info)){ //指定分类则返回当前分类极其子分类
            $info['_'] = $list;
        } else { //否则返回所有分类
            $info = $list;
        }
    
        return $info;
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
    public function update(){
        $data = $this->create();
        if(!$data){ //数据对象创建错误
            return false;
        }
    
        /* 添加或更新数据 */
        if(empty($data['id'])){
            $res = $this->add();
        }else{
            $res = $this->save();
        }
    
        //更新分类缓存
        S('sys_category_goods_list', null);
    
        //记录行为
        action_log('update_category_goods', 'category_goods', $data['id'] ? $data['id'] : $res, UID);
    
        return $res;
    }
}