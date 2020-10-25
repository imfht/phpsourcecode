<?php
/*
* @copyright (c) 2012-3000 IKPHP All Rights Reserved
* @author 小麦  修改时间4月3日 空间后台管理
* @Email:810578553@qq.com
*/
namespace Space\Controller;
use Common\Controller\
BackendController;
/**
 * 后台小组控制器
 * @author 麦苗 <810578553@qq.com>
 */
class AdminController extends BackendController {
	public function _initialize() {
		parent::_initialize ();
		//读取配置
		//$this->fcache('space_setting');
		$this->user_mod = D('Common/User');
		$this->note_mod = D ( 'UserNote' );
		$this->photo_mod = D ( 'UserPhoto' );
		$this->album_mod = D ( 'UserPhotoAlbum' ); //相册模型
		

	}
	//个人空间全局配置 
	public function setting() {
		//TODO
	}
	//日记管理
	public function notes() {
		$ik = I ( 'get.ik', 'list', 'trim' );
		$this->assign ( 'ik', $ik );
		
		switch ($ik) {
			case "list" :
				$this->notelist ();
				break;
			case "delete" :
				$this->delete_note ();
				break;				
			case "recommend" :
				$this->recommend_note ();
				break;
			case "audit" :
				$this->audit_note ();
				break;
		}
	}
	//日记列表
	public function notelist() {
	    
		//显示列表
		$map['title'] = array('neq','');
    	$pagesize = 20;
    	$count = $this->note_mod->where($map)->order('addtime DESC')->count('noteid');
    	$pager = $this->_pager($count, $pagesize);
    	$arrNotes =  $this->note_mod->where($map)->order('addtime DESC')->limit($pager->firstRow.','.$pager->listRows)->select();
    		
    	foreach($arrNotes as $key=>$item){
    		$arrNote[] =  $item;
    		$arrNote[$key]['title']   = getsubstrutf8(clearText($item['title']),0,20);
    		$arrNote[$key]['content'] = getsubstrutf8(clearText($item['content']),0,30);
    		$arrNote[$key]['addtime'] = date('Y-m-d H:i:s',$item['addtime']);
    		$arrNote[$key]['username'] = $this->user_mod->get('username',$item['userid']);
    	}
    	$this->assign('pageUrl', $pager->show());
    	
    	$this->assign('list', $arrNote);		
		$this->title ( '日记管理' );
		$this->display ( 'notes' );
	}
	//删除日记
	public function delete_note(){
		$itemid = I('post.itemid');
		$id = I('get.id', 0);
		//单条推荐
		if($id){
			
			$this->note_mod->deleteOneNote($id);
			$this->redirect ( 'space/admin/notes');
		}
		//多条推荐
		if($itemid){
			$arrid = explode(',', $itemid);
			foreach ($arrid as $item){
				$this->note_mod->deleteOneNote($item);
			}	
    		$arrJson = array('r'=>0, 'html'=> '操作成功');
    		echo json_encode($arrJson);
		}
	}
	//审核日记
	public function audit_note(){
		$itemid = I('post.itemid');
		$id = I('get.id', 0);
		//单条推荐
		if($id){
			$isaudit = $this->note_mod->where(array('noteid'=>$id))->getField('isaudit');
			$isaudit = $isaudit == 1 ? 0 : 1;
			$this->note_mod->where(array('noteid'=>$id))->setField('isaudit', $isaudit);
			$this->redirect ( 'space/admin/notes');
		}
		//多条推荐
		if($itemid){
			$isaudit = I('get.isaudit');
			$arrid = explode(',', $itemid);
			foreach ($arrid as $item){
				$this->note_mod->where(array('noteid'=>$item))->setField('isaudit', $isaudit);
			}	
    		$arrJson = array('r'=>0, 'html'=> '操作成功');
    		echo json_encode($arrJson);
		}
	}	
	//推荐日记
	public function recommend_note(){
		$itemid = I('post.itemid');
		$id = I('get.id', 0);
		//单条推荐
		if($id){
			$isrecommend = $this->note_mod->where(array('noteid'=>$id))->getField('isrecommend');
			$isrecommend = $isrecommend == 1 ? 0 : 1;
			$this->note_mod->where(array('noteid'=>$id))->setField('isrecommend', $isrecommend);
			$this->redirect ( 'space/admin/notes');
		}
		//多条推荐
		if($itemid){
			$isrecommend = I('get.isrecommend');
			$arrid = explode(',', $itemid);
			foreach ($arrid as $item){
				$this->note_mod->where(array('noteid'=>$item))->setField('isrecommend', $isrecommend);
			}	
    		$arrJson = array('r'=>0, 'html'=> '操作成功');
    		echo json_encode($arrJson);
		}
	}	
	//相册管理
	public function albums() {
		$ik = I ( 'get.ik', 'list', 'trim' );
		$this->assign ( 'ik', $ik );
		
		switch ($ik) {
			case "list" :
				$this->albumlist ();
				break;			
			case "recommend" :
				$this->recommend_album ();
				break;								
		}
	}
	//相册列表
	public function albumlist() {
		//显示列表
		$map = array();
    	$pagesize = 20;
    	$count = $this->album_mod->where($map)->order('addtime DESC')->count('albumid');
    	$pager = $this->_pager($count, $pagesize);
    	$arrAlbums =  $this->album_mod->field('userid,albumid,albumname,albumdesc,addtime')->where($map)->order('addtime DESC')->limit($pager->firstRow.','.$pager->listRows)->select();

    	foreach($arrAlbums as $key=>$item){
    		$arrAlbum[$key] = $this->album_mod->getOneAlbum($item['albumid']);
    		$arrAlbum[$key]['albumname']  = getsubstrutf8(clearText($item['albumname']),0,20);
    		$arrAlbum[$key]['albumdesc']  = getsubstrutf8(clearText($item['albumdesc']),0,30);
    		$arrAlbum[$key]['addtime']    = date('Y-m-d H:i:s',$item['addtime']);
    		$arrAlbum[$key]['username']   = $this->user_mod->get('username',$item['userid']);
    	}
    	$this->assign('pageUrl', $pager->show());
    	$this->assign('list', $arrAlbum);
    			
		$this->title ( '相册管理' );
		$this->display ( 'albums' );
	}


	//推荐相册
	public function recommend_album(){
		$itemid = I('post.itemid');
		$id = I('get.id', 0);
		//单条推荐
		if($id){
			$isrecommend = $this->album_mod->where(array('albumid'=>$id))->getField('isrecommend');
			$isrecommend = $isrecommend == 1 ? 0 : 1;
			$this->album_mod->where(array('albumid'=>$id))->setField('isrecommend', $isrecommend);
			$this->redirect ( 'space/admin/albums');
		}
		//多条推荐
		if($itemid){
			$isrecommend = I('get.isrecommend');
			$arrid = explode(',', $itemid);
			foreach ($arrid as $item){
				$this->album_mod->where(array('albumid'=>$item))->setField('isrecommend', $isrecommend);
			}	
    		$arrJson = array('r'=>0, 'html'=> '操作成功');
    		echo json_encode($arrJson);
		}
	}

	//照片管理
	public function photos() {
		$ik = I ( 'get.ik', 'list', 'trim' );
		$this->assign ( 'ik', $ik );
		
		switch ($ik) {
			case "list" :
				$this->photolist ();
				break;			
			case "recommend" :
				$this->recommend_photo ();
				break;	
			case "delete" :
				$this->delete_photo ();
				break;												
		}
	}
	//照片列表
	public function photolist(){
		$albumid = I('id');
		if(!empty($albumid)){
			$strAlbum =  $this->album_mod->getOneAlbum($albumid);
			//读取所有相册里的照片
			//显示列表
			$map = array('albumid' => $albumid);
	    	$pagesize = 20;
	    	$count = $this->photo_mod->where($map)->order('addtime DESC')->count('albumid');
	    	$pager = $this->_pager($count, $pagesize);
	    	$arrDatas =  $this->photo_mod->where($map)->order('addtime DESC')->limit($pager->firstRow.','.$pager->listRows)->select();
	
	    	foreach($arrDatas as $key=>$item){
	    		$arrData[$key] = $this->photo_mod->getOnePhoto($item['photoid']);
	    		$arrData[$key]['photodesc']  = getsubstrutf8(clearText($item['photodesc']),0,30);
	    		$arrData[$key]['addtime']    = date('Y-m-d H:i:s',$item['addtime']);
	    		$arrData[$key]['username']   = $this->user_mod->get('username',$item['userid']);
	    	}
	    	$this->assign('pageUrl', $pager->show());
	    	$this->assign('list', $arrData);
			
			$this->title ( $strAlbum['albumname'] .' - 照片管理' );
			$this->display ( 'photos' );
		}else{
			$this->error('没有找到该相册');
		}
	}
	//推荐照片
	public function recommend_photo(){
		$itemid = I('post.itemid');
		$id = I('get.id', 0);
		//单条推荐
		if($id){
			$strPhoto = $this->photo_mod->where(array('photoid'=>$id))->getField('photoid,albumid,isrecommend', true);
			$isrecommend = $strPhoto[$id]['isrecommend'] == 1 ? 0 : 1;
			$this->photo_mod->where(array('photoid'=>$id))->setField('isrecommend', $isrecommend);
			$this->redirect ( 'space/admin/photos',array('ik'=>'list','id'=>$strPhoto[$id]['albumid']));
		}
		//多条推荐
		if($itemid){
			$isrecommend = I('get.isrecommend');
			$arrid = explode(',', $itemid);
			foreach ($arrid as $item){
				$this->photo_mod->where(array('photoid'=>$item))->setField('isrecommend', $isrecommend);
			}	
    		$arrJson = array('r'=>0, 'html'=> '操作成功');
    		echo json_encode($arrJson);
		}	
	}
	//删除照片
	public function delete_photo(){
		$itemid = I('post.itemid');
		$id = I('get.id', 0);
		//单条删除
		if($id){
			$albumid = $this->photo_mod->where(array('photoid'=>$id))->getField('albumid');
			$this->photo_mod->delPhoto($id);
			$this->redirect ( 'space/admin/photos', array('ik'=>'list','id'=>$albumid));
		}
		//多条删除
		if($itemid){
			$arrid = explode(',', $itemid);
			foreach ($arrid as $item){
				$this->photo_mod->delPhoto($item);
			}	
    		$arrJson = array('r'=>0, 'html'=> '操作成功');
    		echo json_encode($arrJson);
		}
	}		

}