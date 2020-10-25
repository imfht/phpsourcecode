<?php
namespace Admin\Controller;

class CateController extends CommonController {
	public $cateListAll = array();
	
	
	public function _initialize(){
		$this->cateChildList(0,$nb);//从父级=0开始递归
		 $this->assign('list',$this->cateListAll);
		
		parent::_initialize();
		
	}
	
	
	public function index(){
	
		//S('catetype',null);
        $this->display();
		
	}
	

protected function cateChildList($pid,$nb)
 {
    $cate=M('cate');
    $map['pid']=$pid;
    if(isset($_REQUEST ['type'])){
    	S('catetype',$_REQUEST ['type']);
		//$_SESSION['catetype']=$_REQUEST ['type'];
						
	}else{
		if(S('catetype')==''){
			S('catetype',1);
		}
		
	}
	$map ['type'] = S('catetype');				
	//dump($map);
    $parent=$cate->where($map)->order('ordid asc,id desc')->select();
    if($parent)
    {
        $nb = $nb."&nbsp;";
        foreach($parent as $item)
        {
            $item['name']=$nb.'├ '.$item['name'];
            
            $this->cateListAll[]=$item;
            $this->cateChildList($item['id'],$nb);
        }
    }
    
 }
	
	public function before_insert($data){
		
		
        $data['spid'] = D('cate')->get_spid($data['pid']);
       
        return $data;
	}
   public function before_update($data){
		$old_pid = D('cate')->field('img,pid')->where(array('id'=>$data['id']))->find();
   	if ($data['pid'] != $old_pid['pid']) {
   	 //不能把自己放到自己或者自己的子目录们下面
            $wp_spid_arr = D('cate')->get_child_ids($data['id'], true);
            if (in_array($data['pid'], $wp_spid_arr)) {
                $this->mtReturn(300, '不能放到自己的子节点中!');
            }
        $data['spid'] = D('cate')->get_spid($data['pid']);
   	}
        return $data;
	}
   
	
	


}

?>