<?php
namespace Admin\Controller;

class MroleController extends CommonController {

	function _filter(&$map) {
		$map['name'] = array('like', "%" . $_POST['name'] . "%");
	}

	function after_insert($result){
		
		$data['id']=$result;
		$data['value']=D('Mroleconfig')->where(array('id'=>1))->getField('value');
		D('Mroleconfig')->add($data);
		
	}
   
	function after_foreverdelete($id){
		$condition = array ('id' => array ('in', explode ( ',', $id ) ) );
				if (false === M('Mroleconfig')->where ( $condition )->delete ()) {
				   
					$this->mtReturn(300, '删除失败！');

				} 
	}
	function after_selectedDelete($id){
		$condition = array ('id' => array ('in', explode ( ',', $id ) ) );
				if (false === M('Mroleconfig')->where ( $condition )->delete ()) {
				   
					$this->mtReturn(300, '删除失败！');

				} 
	}
	public function config(){
		 $Config = M('mroleconfig');
		 $id=I('id');
		 $map['id']=$id;
		if(IS_POST){
	
		
        
           
           if(false!==$data=$Config->create()){
           
           	$data['value']=json_encode($data['value']);
           	
           	 $res=$Config->save($data);
           	 if($res!==false){
           	 	 S('MROLE_CONFIG_DATA'.$id,null);
           	 	 $this->mtReturn(200,'配置保存成功！');
           	 }else{
           	 	 $this->mtReturn(300,'配置保存失败！');
           	 }
           	 
           	 
           }else{
           	 $this->mtReturn(300,'配置保存失败！');
           }
        }else{
        	
        	$info=$Config->where($map)->find();
        	$info['value']=json_decode($info['value'],true);
        	
        	S('MROLE_CONFIG_DATA'.$id,$info['value']);
        	$this->assign('info',$info);
			$this->display();
		}
		
	}

	public function _before_edit() {
		$Group = D('Mrole');
		//查找满足条件的列表数据
		$classTree = $Group->field('id,name,pid')->select();
		$list = list_to_tree($classTree,'id','pid','_child',0);
		$this->assign('list', $list);
	}

	public function _before_add() {
		$Group = D('Mrole');
		//查找满足条件的列表数据
		$classTree = $Group->field('id,name,pid')->select();
		$list = list_to_tree($classTree,'id','pid','_child',0);
		$this->assign('list', $list);
	}
    






	



}

?>