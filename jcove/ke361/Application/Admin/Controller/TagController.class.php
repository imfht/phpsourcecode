<?php
namespace Admin\Controller;

use Admin\Model\TagModel;
class TagController extends AdminController
{
    public function index(){
        
        $this->assign('_list',$this->lists(D('Tag')));
        $this->display();
    }
    public function add(){
        if(IS_POST){
            $TagModel = new TagModel();
            if($TagModel->edit()){
                $this->success('添加成功',U('index'));
            }else {
                $this->error('添加过程遇到错误');
            }
        }else{
            $this->display();
        }
    }
    /**
     * 删除数据
     */
    public function del(){
        $id = array_unique((array)I('id',0));
        $id = is_array($id) ? implode(',',$id) : $id;
        if ( empty($id) ) {
            $this->error('请选择要操作的数据!');
        }
        $where['id'] =   array('in',$id);
        $this->delete('Tag',$where);
    }
    
}

?>