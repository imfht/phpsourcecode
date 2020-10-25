<?php
namespace Admin\Controller;
use Admin\Model\CategoryGoodsModel;
class CategoryGoodsController extends AdminController
{
    public function index(){
        
        $tree = D('CategoryGoods')->getTree(0,'','id,category_name,sort,pid,status');
        $this->assign('tree', $tree);
        C('_SYS_GET_CATEGORY_GOODS_TREE_', true); //标记系统获取分类树模板
        $this->assign('categoryList', $categoryList);
        $this->display();
     
    }
    /**
     * 显示分类树，仅支持内部调
     * @param  array $tree 分类树
     * @author 麦当苗儿 <zuojiazi@vip.qq.com>
     */
    public function tree($tree = null){
        C('_SYS_GET_CATEGORY_GOODS_TREE_') || $this->_empty();
        $this->assign('tree', $tree);
        $this->display('tree');
    }
    
    public function status($id){
        $where['id'] = $id;
        $CategoryModel = new CategoryGoodsModel();
        $cate =$CategoryModel->where($where)->find();
       
        $CategoryModel->where("id='{$id}'")->save(array('status'=>$cate['status']==1?-1:1));
      
        $this->success('操作成功',U('index'));
    }
    public function del($id){
        D('CategoryGoods')->delCategory($id);
        M('Goods')->where("cate_id='{$id}'")->delete();
        $this->success('删除成功',U('index'));
    }
    public function add($pid = 0){
       
        $Category = D('CategoryGoods');
        
        if(IS_POST){ //提交表单
            if(false !== $Category->update()){
                $this->success('新增成功！', U('index'));
            } else {
                $error = $Category->getError();
                $this->error(empty($error) ? '未知错误！' : $error);
            }
        } else {
            $cate = array();
            if($pid){
                /* 获取上级分类信息 */
                $cate = $Category->info($pid, 'id,category_name,status');
                if(!($cate && 1 == $cate['status'])){
                    $this->error('指定的上级分类不存在或被禁用！');
                }
            }
        
            /* 获取分类信息 */
            $this->assign('info',       null);
            $this->assign('category', $cate);
            $this->meta_title = '新增分类';
            $this->display('edit');
        }
    }
    /* 编辑分类 */
    public function edit($id = null, $pid = 0){
        $Category = D('CategoryGoods');
    
        if(IS_POST){ //提交表单
            if(false !== $Category->update()){
                $this->success('编辑成功！', U('index'));
            } else {
                $error = $Category->getError();
                $this->error(empty($error) ? '未知错误！' : $error);
            }
        } else {
            $cate = '';
            if($pid){
                /* 获取上级分类信息 */
                $cate = $Category->info($pid, 'id,category_name,status');
                if(!($cate && 1 == $cate['status'])){
                    $this->error('指定的上级分类不存在或被禁用！');
                }
            }
    
            /* 获取分类信息 */
            $info = $id ? $Category->info($id) : '';
    
            $this->assign('info',       $info);
            $this->assign('category',   $cate);
            $this->meta_title = '编辑分类';
            $this->display();
        }
    }
    public function remove(){
        $cate_id = I('id');
        if(empty($cate_id)){
            $this->error('参数错误!');
        }
    
        //判断该分类下有没有子分类，有则不允许删除
        $child = M('CategoryGoods')->where(array('pid'=>$cate_id))->field('id')->select();
        if(!empty($child)){
            $this->error('请先删除该分类下的子分类');
        }
    
        //判断该分类下有没有内容
        $goodsList = M('Goods')->where(array('cate_id'=>$cate_id))->field('id')->select();
        if(!empty($goodsList)){
            $this->error('请先删除该分类下的商品（包含回收站）');
        }
    
        //删除该分类信息
        $res = M('CategoryGoods')->delete($cate_id);
        if($res !== false){
            //记录行为
            action_log('update_category_goods', 'category_goods', $cate_id, UID);
            $this->success('删除分类成功！');
        }else{
            $this->error('删除分类失败！');
        }
    }
    
    /**
     * 操作分类初始化
     * @param string $type
     * @author huajie <banhuajie@163.com>
     */
    public function operate($type = 'move'){
        //检查操作参数
        if(strcmp($type, 'move') == 0){
            $operate = '移动';
        }elseif(strcmp($type, 'merge') == 0){
            $operate = '合并';
        }else{
            $this->error('参数错误！');
        }
        $from = intval(I('get.from'));
        empty($from) && $this->error('参数错误！');
    
        //获取分类
        $map = array('status'=>1, 'id'=>array('neq', $from));
        $list = M('CategoryGoods')->where($map)->field('id,pid,category_name')->select();
    
    
        //移动分类时增加移至根分类
        if(strcmp($type, 'move') == 0){
            //不允许移动至其子孙分类
            $list = tree_to_list(list_to_tree($list));
    
            $pid = M('CategoryGoods')->getFieldById($from, 'pid');
            $pid && array_unshift($list, array('id'=>0,'category_name'=>'根分类'));
        }
    
        $this->assign('type', $type);
        $this->assign('operate', $operate);
        $this->assign('from', $from);
        $this->assign('list', $list);
        $this->meta_title = $operate.'分类';
        $this->display();
    }
    
    /**
     * 移动分类
     * @author huajie <banhuajie@163.com>
     */
    public function move(){
        $to = I('post.to');
        $from = I('post.from');
        $res = M('CategoryGoods')->where(array('id'=>$from))->setField('pid', $to);
        if($res !== false){
            $this->success('分类移动成功！', U('index'));
        }else{
            $this->error('分类移动失败！');
        }
    }
    
    /**
     * 合并分类
     * @author huajie <banhuajie@163.com>
     */
    public function merge(){
        $to = I('post.to');
        $from = I('post.from');
        $Model = M('CategoryGoods');
    
     
    
        //合并文档
        $res = M('Goods')->where(array('cate_id'=>$from))->setField('cate_id', $to);
    
        if($res !== false){
            //删除被合并的分类
            $Model->delete($from);
            $this->success('合并分类成功！', U('index'));
        }else{
            $this->error('合并分类失败！');
        }
    
    }
}

?>