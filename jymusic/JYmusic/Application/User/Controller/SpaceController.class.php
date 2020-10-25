<?php
// +-------------------------------------------------------------+
// | Author: 战神~~巴蒂 <378020023@qq.com> <http://www.jyuu.cn>  |
// +-------------------------------------------------------------+
namespace User\Controller;
class SpaceController extends UserController {
    /**
	* 关注用户
	*/    
    public function index(){

    
    }
       
    /**
	* 导航
	*/    
    public function channel(){
		$this->display();
    }
    
    
    /**
	* 导航
	*/    
    public function setbg(){
    	if(IS_POST){    		    		
    	 	$Member =   D('UserSpace');
	        $data   =   $Member->create();
	        if($data){
	        	$res = $Member->where('uid='.UID)->save();
		        if($res){
		            $this->success('修改成功！');
		        }else{
		            $this->error('修改失败！');
		        }
	            
	        }else{
	        	$this->error($Member->getError());
	        }        

    	}else{
    		$this->error('非法参数！');
    	}		        
    }
    /**
	* 开通个人空间
	*/ 
   	public function open($uid){
		if (intval($uid) == UID){
			if($this->openspace($uid)){
				M('Member')-> where(array('uid'=>$uid))->setField('space',1);
				$this->error('成功开通个人空间',U('Home/index?uid='.$uid));
			}else{
				$this->error('个人空间开通失败');
			}
		}else{
			$this->error('参数错误！');
		}
    
    }
        
    /**
	* 开通个人空间
	*/ 
   	public function skin (){
   		$path = '.'.__ROOT__.trim(C('VIEW_PATH'),'.').'User';	
   		$bnxml = @simplexml_load_file($path.'/space_skins/default_banner.xml');
    	if(is_object($bnxml)){
			$bnxml = json_encode($bnxml);
			$bnxml = json_decode($bnxml, true);
			$this->assign('default_banner',$bnxml['theme']);
		}
    	$bgxml = @simplexml_load_file($path.'/space_skins/default_bg.xml');
    	if(is_object($bgxml)){
			$bgxml = json_encode($bgxml);
			$bgxml = json_decode($bgxml, true);
			$this->assign('default_bg',$bgxml['theme']);
		}
		$this->display();
    
    }
       
    
}