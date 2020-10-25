<?php
class WxSuggest extends Action{	
	private $cacheDir='';//缓存目录
	private $member='';//缓存目录
	public function __construct() {
		$this->auth=_instance('Action/home/WxAuth');
		$this->member=_instance('Action/home/WxMember');
	}	
	public function suggest(){
		$member	  	=$this->L('home/WxMember')->member_get_info();
		$where_str 	="member_id='".$member['member_id']."' ";
		$sql		 = "select * from fly_suggest where $where_str order by id desc";	
		$list		 = $this->C($this->cacheDir)->findAll($sql);
		$assignArray = array('list'=>$list);	
		return $assignArray;
		
	}
	public function suggest_show(){
		$assArr  = $this->suggest();
		$smarty  = $this->setSmarty();
		$smarty->assign($assArr);
		$smarty->display('home/suggest_show.html');	
	}

	
	
	public function suggest_show_one(){
		$id	  	 = $this->_REQUEST("id");
		$sql 		= "select * from fly_suggest where id='$id'";
		$one 		= $this->C($this->cacheDir)->findOne($sql);	
		$smarty 	= $this->setSmarty();
		$smarty->assign(array("one"=>$one));
		$smarty->display('home/suggest_show_one.html');	
	}	

	//增加地址
	public function suggest_add(){
		$member	 =$this->L('home/WxMember')->member_get_info();
		if(empty($_POST)){
			$smarty = $this->setSmarty();
			$smarty->assign(array('one'=>$member));
			$smarty->display('home/suggest_add.html');	
		}else{
			$member_id	= $member['memeber_id'];
			$title		= $this->_REQUEST("title");
			$content	= $this->_REQUEST("content");
			$adt		= date("Y-m-d H:i:s",time());
			
			$sql="insert into fly_suggest(member_id,title,content,adt) 
										values('$member_id','$title','$content','$adt');";
			
			$rtn=$this->C($this->cacheDir)->update($sql);
			
			if($rtn>0){
				$rtn_msg=array('code'=>'sucess','message'=>'操作成功'); 
			}else{
				$rtn_msg=array('code'=>'fail','message'=>'操作失败'); 
			}
			
			echo json_encode($rtn_msg);
		}
	}
	
}//
?>