<?php
namespace app\admin\controller;

/**
 * 内容管理控制器
 * @author zhanghd <zhanghd1987@foxmail.com>
 */
class Article extends Base
{
	public function index(){
		$map = [];
		$input = input();
		if(isset($input['name'])){
			$map['title'] = ['like','%'.$input['name'].'%'];
		}
		if(isset($input['status'])){
			$map['status'] = $input['status'];
		}
		$order = ['isrecomm desc, utime desc'];
		$list = $this->lists('Article', $map, $order);
		$this->assign('list',$list);
		return $this->fetch();
	}

	/**
	 * 添加文章
	 */
	public function add(){
		if(request()->isPost()){
			$data = input();
			$valid = \think\Loader::validate("Article")->scene('add');
			if(!$valid->check($data)){
				$this->error($valid->getError());
			}else{
				$time = time();
				$data['ctime'] = $time;
				$data['utime'] = $time;
				if(model('Article')->insert($data)){
					$this->success('文章添加成功',url('article/index'));
				}else{
					$this->error('文章添加失败');
				}
			}
		}else{
			$result = model('Category')->lists();
			$this->assign('tree',$result);
			return $this->fetch();
		}
	}
	
	/**
	 * 添加文章
	 */
	public function edit(){
		if(request()->isPost()){
			$data = input();
			$valid = \think\Loader::validate("Article")->scene('edit');
			if(!$valid->check($data)){
				$this->error($valid->getError());
			}else{
				$data['utime'] = time();
				if(model('Article')->where(['id'=>$data['id']])->update($data)){
					$this->success('文章添加成功',url('article/index'));
				}else{
					$this->error('文章添加失败');
				}
			}
		}else{
			$id = input('id');
			$find = model('Article')->where(['id'=>$id])->find();
			$result = model('Category')->lists();
			$this->assign('find',$find);
			$this->assign('tree',$result);
			return $this->fetch();
		}
	}

}
