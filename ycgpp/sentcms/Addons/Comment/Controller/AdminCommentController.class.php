<?php
namespace Addons\Comment\Controller;

use Admin\Builder\AdminListBuilder;
use Admin\Builder\AdminConfigBuilder;
use Admin\Builder\AdminSortBuilder;
class AdminCommentController extends \Admin\Controller\AdminController{
	public function Commentlist(){
		$model = D('Addons://Comment/CommentReply');
		$map['commentid'] = I('get.uid');
		$result = $model->where($map)->page(1, 20)->order('create_time desc')->select();
		$count = $model->where($map)->count();
		$builder = new \OT\Builder();
        $builder->title('显示评论列表')
            ->setStatusUrl(U('setRuleStatus'))->buttonDelete('/admin.php?s=/addons/execute/_addons/Comment/_controller/AdminComment/_action/DeleteComment/Type/RepAll','全部删除')
            ->keyId()->keyTitle('nickname','昵称')->keyTitle('content','内容')->keyTitle('ip','IP')->keyTitle('create_time','回复时间')
            ->keyDoAction('addons/execute?_addons=Comment&_controller=AdminComment&_action=DeleteComment&type=remodel&uid=###','删除')
            ->data($result)
            ->pagination($count, 20)
            ->display();
	}

	public function DeleteComment(){

		$model = D('Addons://Comment/Comment');
		$remodel = D('Addons://Comment/CommentReply');
		//如果为全部删除
		if(I('get.Type')){
			$ids = implode(',',I('post.ids'));
			$map['id'] = array('IN',$ids);
			$remap['commentid'] = $map['id'];
			if(I('get.Type') == 'All'){
				$type = $model;
				//查找回复
				if($remodel->where($remap)->select()){
					$remodel->where($remap)->delete();
				}
			}elseif(I('get.Type') == 'RepAll'){
				$type = $remodel;
			}
			$type->where($map)->delete();
			$this->success('全部删除成功！');
			return;
		}
		//查找评论信息
		$map['id'] = I('get.uid');
		$remap['commentid'] = $map['id'];
		if(I('get.type') == 'model'){
			if($model->where($map)->find()){
				//查询回复评论
				if($remodel->where($remap)->select()){
					//删除所有回复评论
					$remodel->where($remap)->delete();
				}
				//删除评论
				if($model->where($map)->delete()){
					$this->success('删除成功！');
				}else{
					$this->error('删除失败！');
				}

			}else{
				$this->error('不存在此评论！');
			}
		}elseif(I('get.type') == 'remodel'){
			if($remodel->where($map)->select()){
				//删除所有回复评论
				$remodel->where($map)->delete();

			}
			$this->success('删除成功！');
		}
	}
}
