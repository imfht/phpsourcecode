<?php
/*******************************************************************************
* [TuziCMS] 兔子CMS
* @Copyright (C) 2014-2015  http://tuzicms.com   All rights reserved.
* @Team  Yejiao.net
* @Author: 秦大侠 QQ:176881336
* @Licence http://www.tuzicms.com/license.txt
*******************************************************************************/
namespace Manage\Controller;
use Think\Controller;
use Common\Lib\Category; //引入类函数
use Common\Lib\Common; //引入类函数
use Common\Lib\String; //引入类函数
class SpecialController extends CommonController {
	/**
	 * 专题首页展示
	 */
	public function index() {
		$m=D('Special');
		
		//**分页实现代码
		$count=$m->count();// 查询满足要求的总记录数
		$Page = new \Think\Page($count,11);// 实例化分页类 传入总记录数和每页显示的记录数(25)
		$show = $Page->show();// 分页显示输出
		//**分页实现代码
		$arr = $m->limit($Page->firstRow.','.$Page->listRows)->order('id desc')->select();
		
		foreach($arr as $k2 => $v2){
			$arr[$k2]['url'] = 'Special'.'/'.'show'.'/'.'id'.'/'.$v2['id'];
		}
// 		dump($arr);
// 		exit;

		$this->assign('vlist', $arr);
		$this->assign('page',$show);// 赋值分页输出
		$this->assign('count',$count);
		$this->assign('module',MODULE_NAME);
		$this->display();
	}
	/**
	 * 添加
	 */
	public function add() {
		//获取Special_开头的模板文件名
		$_styleShowList = get_file_folder_List('./Public/Home/' .C('DEFAULT_THEME__HOME') , 2, 'Special_*');
// 		dump($_styleShowList);
// 		exit;
		
		$styleShowList = array();
		foreach ($_styleShowList as $v) {
			if (strpos($v, 'Special_index') === false) {
				$styleShowList[] = $v;
			}
		}
		
// 		dump($styleShowList);
// 		exit;
		$this->assign('templist', $styleShowList);
		$this->display();
	}

	/**
	 * 处理添加
	 */
	public function do_add() {
		//dump($_POST);
		//exit;
		$m=D('Special'); //先读取News数据库表模型文件
		if (!$m->create()){
			$this->error($m->geterror());
		}
		//**需要另外添加到数据库的在这里填写
		//注意，非表单自动创建提交的数据，要写在if判断之后才能提交到数据库
		//$m->special_addtime=time();
		//**上传附件代码  附件保存在网站目录里  附件名保存在数据库里面
		//$m->filename = $info[0]['savename']; // 将附件的名字保存在数据库的filename字段里
		//**上传附件代码  附件保存在网站目录里  附件名保存在数据库里面
		//**需要另外添加到数据库的在这里填写
		
		$arr=$m->add(); //自动修改 不需要定义id 因为post表单中已经有
		if ($arr){
			$this->success('添加成功',U('Special/index'));
		}else {
			$this->error('添加失败');
			//$this->error($m->geterror());
		}
	}

	/**
	 * 编辑
	 */
	public function edit() {
		//当前控制器名称
		$id = I('id', 0, 'intval');
// 		dump($id);
// 		exit;
		$m=D('special');//读取数据库模型model文件，关联模型。
		$arr=$m->find($id);
// 		    	dump($arr);
// 		    	exit;
		$this->assign('v',$arr);
		
		//获取Special_开头的模板文件名
		$_styleShowList = get_file_folder_List('./Public/Home/' .C('DEFAULT_THEME__HOME') , 2, 'Special_*');
		// 		dump($_styleShowList);
		// 		exit;
		$styleShowList = array();
		foreach ($_styleShowList as $v) {
			if (strpos($v, 'Special_index') === false) {
				$styleShowList[] = $v;
			}
		}
// 				dump($styleShowList);
// 				exit;
		$this->assign('templist', $styleShowList);

		$this->display();
	}


	/**
	 * 修改处理
	 */
	public function do_edit() {
// 		dump($_POST);
// 		exit;
		$m=D('Special'); //数据库表，配置文件中定义了表前缀，这里则不需要写
		$data['id']=I('post.id');
		$data['special_title']=I('post.special_title');
		$data['special_template']=I('post.special_template');
		$data['special_keywords']=I('post.special_keywords');
		$data['special_description']=I('post.special_description');
		$count=$m->save($data); //修改表单用save函数
		if ($count>0){
			$this->success('修改成功！',U('Special/index'));
		}
		else {
			$this->error('修改失败！');
		}
		
	}


	/**
	 * 回收站列表
	 */
	public function trach() {
		
		$where = array('special.status' => 1);
		$count = D('SpecialView')->where($where)->count();

		$page = new \Common\Lib\Page($count, 10);
		$page->rollPage = 7;
		$page->setConfig('theme','%HEADER% %FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END%');
		$limit = $page->firstRow. ',' .$page->listRows;
		$art = D('SpecialView')->nofield('content')->where($where)->limit($limit)->select();

		$this->assign('page', $page->show());
		$this->assign('vlist', $art);
		$this->assign('subcate', '');
		$this->assign('type', '回收站');
		$this->display('index');
	}

    /**
     * 删除处理
     */
    public function delete(){
    	$m=M('Special');
    	$id=I('get.id');
    	$count=$m->delete($id);
    	if ($count>0){
    		$this->success('删除成功');
    	}
    	else {
    		$this->error('删除失败');
    	}
		
    }

    /**
     * 批量删除处理
     */
    public function delall(){		
		//dump($_POST);
		//exit;
    	$m=D('Special'); //数据库表，配置文件中定义了表前缀，这里则不需要写
    	$id = I('post.id');   
    	//dump($id);
    	//exit;
    	if ($id==null){
    		$this->error('请选择删除项！');
    	}
    	//判断id是数组还是一个数值
    	if(is_array($id)){
    		$where = 'id in('.implode(',',$id).')';
    		//implode() 函数返回一个由数组元素组合成的字符串
    	}else{
    		$where = 'id='.$id;
    	}
    	//dump($where);
    	//exit;
    	$count=$m->where($where)->delete(); //修改表单用save函数
    	if ($count>0){
    		$this->success("成功删除{$count}条！");
    	}
    	else {
    		$this->error('批量删除失败！');
    	}
    
    }

	/**
	 * 还原
	 */
	public function restore() {
		
		$id = I('id',0 , 'intval');
		$batchFlag = I('get.batchFlag', 0, 'intval');
		//批量删除
		if ($batchFlag) {
			$this->restoreBatch();
			return;
		}

		if (false !== M('special')->where(array('id' => $id))->setField('status', 0)) {
			
			$this->success('还原成功', U('Special/trach'));
			
		}else {
			$this->error('还原失败');
		}
	}

	/**
	 * 批量还原
	 */
	public function restoreBatch() {
		
		$idArr = I('key',0 , 'intval');
		if (!is_array($idArr)) {
			$this->error('请选择要还原的项');
		}

		if (false !== M('special')->where(array('id' => array('in', $idArr)))->setField('status', 0)) {
			
			$this->success('还原成功', U('Special/trach'));
			
		}else {
			$this->error('还原失败');
		}
	}

	/**
	 * 彻底删除
	 */
	public function clear() {

		$id = I('id',0 , 'intval');
		$batchFlag = I('get.batchFlag', 0, 'intval');
		//批量删除
		if ($batchFlag) {
			$this->clearBatch();
			return;
		}

		if (M('special')->delete($id)) {
			// delete picture index
			
			M('attachmentindex')->where(array('arcid' => $id, 'modelid' => 0, 'desc' => 'special'))->delete();
			
			$this->success('彻底删除成功', U('Special/trach'));
		}else {
			$this->error('彻底删除失败');
		}
	}


	/**
	 * 批量彻底删除
	 */
	public function clearBatch() {

		$idArr = I('key',0 , 'intval');		
		if (!is_array($idArr)) {
			$this->error('请选择要彻底删除的项');
		}
		$where = array('id' => array('in', $idArr));
		if (M('special')->where($where)->delete()) {
			// delete picture index
			M('attachmentindex')->where(array('arcid' => array('in', $idArr), 'modelid' => 0, 'desc' => 'special'))->delete();
			$this->success('彻底删除成功', U('Special/trach'));
		}else {
			$this->error('彻底删除失败');
		}
	}

	
}



?>