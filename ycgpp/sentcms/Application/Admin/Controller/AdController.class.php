<?php
// +----------------------------------------------------------------------
// | SentCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.tensent.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: molong <molong@tensent.cn> <http://www.tensent.cn>
// +----------------------------------------------------------------------
namespace Admin\Controller;

/**
 * 后台广告控制器
 * @author molong <molong@tensent.cn>
 */
class AdController extends \Common\Controller\AdminController {

	public function _initialize(){
		$this->assign('_extra_menu',array(
		    '已装插件后台'=> D('Addons')->getAdminList(),
		));
		$this->show_type = array(
			'1'   => '幻灯片',
			'2'   => '对联',
			'3'   => '图片列表',
			'4'   => '图文列表',
			'5'   => '文字列表',
			'6'   => '代码广告',
		);
		S('ad_show_type',$this->show_type);
		parent::_initialize();
	}

	public function index(){
		$this->setMeta('广告位管理');
		$build = new \OT\Builder();

		$place = D('AdPlace');

		$data = $place->where($map)->select();
		$this->assign('data',$data);

		// $build->title('广告位管理')
		// 	->buttonNew(U('add'))//->buttonDelete(U('delete'))
		// 	->keyId()->keyTitle()->keyText('name','标识')->keyCreateTime()->keyUpdateTime()
		// 	->keyDoAction('adlist?id=###','广告列表')
		// 	->keyDoAction('edit?id=###','编辑')
		// 	->keyDoAction('delete?id=###','删除')
		// 	->data($data)
		// 	->pagination($totalCount, $listRows)
		// 	->display();
		$this->display();
	}

	public function add(){
		if (IS_POST) {
			$place = D('AdPlace');
			$data = $place->create();
			if ($data) {
				$result = $place->add();
				if ($result) {
					$this->success('添加成功！',U('Ad/index'));
				}else{
					$this->error('添加失败！');
				}
			}else{
				$this->error($place->getError());
			}
		}else{
			$this->display();
		}
	}

	public function edit(){
		if (IS_POST) {
			$place = D('AdPlace');
			$data = $place->create();
			if ($data) {
				$result = $place->save();
				if ($result) {
					$this->success('修改成功！',U('Ad/index'));
				}else{
					$this->error('修改失败！');
				}
			}else{
				$this->error($place->getError());
			}
		}else{
			$place = D('AdPlace');
			$id = I('id','trim,intval');
			$data = $place->where(array('id'=>$id))->find();
			$build = new \OT\Builder('config');

			$data['howdo'] = "前台使用方法{:W('Home/Ad/run',array('".$data['name']."'))}调用该广告位";
			$build->title('广告位添加')
				->keyText('title','广告位名称')
				->keyText('name','标识')
				->keySelect('show_type','展示方式','代码广告位只能有一条广告，对联广告只能有两条，如果超过数额，根据时间先后选择后更新的数据',$this->show_type)
				->keyTime('start_time','开始时间')
				->keyTime('end_time','结束时间')
				->keyCreateTime()
				->keyUpdateTime()
				->keyRadio('status','状态','',array('1'=>'开启','0'=>'关闭'))
				->keyText('template','广告位模板','您可以在广告文件（./Application/Home/View/Default/Ad/）下创建新的广告模板')
				->keyTextArea('howdo','使用方法')
				->keyHidden('id')
				->buttonSubmit()
				->buttonBack()
				->data($data)
				->display();
		}
	}

	public function delete(){
		$id = I('id','trim,intval','');
		if (!$id) {
			$this->error('无此广告位！');
		}

		$place = D('AdPlace');

		$result = $place->where(array('id'=>$id))->delete();
		if ($result) {
			$this->success('删除成功！');
		}else{
			$this->error('删除失败！');
		}
	}

	public function adlist(){
		$this->setMeta('广告管理');
		$id = I('id','','trim,intval');
		if (!$id) {
			$this->error('无此广告位！');
		}
		$build = new \OT\Builder();

		$ad = D('Ad');

		$map['place_id'] = $id;
		$data = $ad->where($map)->select();
		$this->assign('data',$data);
		$this->display();
	}

	public function addad(){
		if (IS_POST) {
			$ad = D('Ad');
			$data = $ad->create();
			if ($data) {
				$result = $ad->add();
				if ($result) {
					$this->success('添加成功！',U('Ad/adlist',array('id'=>$data['place_id'])));
				}else{
					$this->error('添加失败！');
				}
			}else{
				$this->error($ad->getError());
			}
		}else{
			$build = new \OT\Builder('config');

			$data['place_id'] = I('place_id','trim,intval');

			if (!$data['place_id']) {
				$this->error('无此广告位！');
			}
			$build->title('广告添加')
				->keyText('title','广告名称')
				->keyText('background','背景颜色','背景可以是图片，也可以是色彩，颜色用32位标注，如：#00ff00')
				->keySingleImage('cover_id','广告图片')
				->keyMultiImage('photolist','辅助图片')
				->keyText('url','广告链接')
				->keyTextArea('listurl','辅助链接','给辅助图片添加链接，一行一个链接')
				->keyCreateTime()
				->keyUpdateTime()
				->keyTextArea('content','广告描述')
				->keyRadio('status','状态','',array('1'=>'开启','0'=>'关闭'))
				->keyHidden('place_id')
				->data($data)
				->buttonSubmit()
				->buttonBack()
				->display();
		}
	}

	public function editad(){
		if (IS_POST) {
			$ad = D('Ad');
			$data = $ad->create();
			if ($data) {
				$result = $ad->save();
				if ($result) {
					$this->success('修改成功！',U('Ad/adlist',array('id'=>$data['place_id'])));
				}else{
					$this->error('修改失败！');
				}
			}else{
				$this->error($ad->getError());
			}
		}else{
			$ad = D('Ad');
			$id = I('id','','trim,intval');
			$place_id = I('place_id','','trim,intval');
			if ($id) {
				$map['id'] = $id;
			}
			if ($place_id) {
				$map['place_id'] = $place_id;
			}
			$data = $ad->where($map)->find();
			$build = new \OT\Builder('config');

			$build->title('广告编辑')
				->keyText('title','广告名称')
				->keyText('background','背景颜色','背景可以是图片，也可以是色彩，颜色用32位标注，如：#00ff00')
				->keySingleImage('cover_id','广告图片')
				->keyMultiImage('photolist','辅助图片')
				->keyText('url','广告链接')
				->keyTextArea('listurl','辅助链接','给辅助图片添加链接，一行一个链接')
				->keyCreateTime()
				->keyUpdateTime()
				->keyTextArea('content','广告描述')
				->keyRadio('status','状态','',array('1'=>'开启','0'=>'关闭'))
				->keyHidden('id')
				->keyHidden('place_id')
				->data($data)
				->buttonSubmit()
				->buttonBack()
				->display();
		}
	}

	public function delad(){
		$ad = D('Ad');
		$id = I('id','0','trim,intval');

		if (!$id) {
			$this->error("非法操作");
		}
		$result = $ad->where(array('id'=>$id))->delete();
		if ($result) {
			$this->success("删除成功！");
		}else{
			$this->error("删除失败！");
		}
	}
}