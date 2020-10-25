<?php 
namespace Addons\Comment\Controller;
use Home\Controller\AddonsController;
class CommentController extends AddonsController {

	/*添加评论*/
	public function addComment(){
		$model = D('Addons://Comment/Comment');
		if (IS_POST) {
			$this->CheckData();
			$data = $model->create();
			if($data){
				$result = $model->add($data);
				if ($result) {
					//评论成功后，统计评论次数
					$this->SetCommentNum();
					$this->success("评论成功！");
				}else{
					$this->error($model->getError());
				}
			}else{
				$this->error($model->getError());
			}
		}
	}

	//更新文章的评论数
	public function SetCommentNum(){
		if(empty($model_id)){
			return;
		}
		//获取model_id;
		$model_id = I('post.model_id');
		//查找模型对应的id,name,
		$model = D('Model')->where('id='.$model_id)->find();
		if($model){
			//模型存在,判断是否为独立模型，或者文档模型
			if(empty($model['extend'])){
				//独立模型
				$tabname = $model['name'];
			}else{
				//文档模型
				$centertab = D('Model')->where('id='.$model['extend'])->find();
				$tabname = $centertab['name'].'_'.$model['name'];
			}
			//查询评论条数
			$map['model_id'] = $model_id;
			$map['aid'] = I('post.aid');
			$where['model_id'] = $model_id;
			$where['id'] = I('post.aid');
			$commentNum = D('Addons://Comment/Comment')->where($map)->count();
			$data['comment'] = $commentNum;
			//根据上面获取的文档模型来写入一个评论条数
			D($centertab['name'])->where($where)->save($data);
		}
	}

	/*回复评论*/
	public function reply(){
		$model = D('Addons://Comment/CommentReply');
		if(IS_POST){
			$this->CheckData();
			$data = $model->create();
			if($data){
				$result = $model->add($data);
				if($result){
					//回复成功后。统计回复次数
					$this->ReplyCount();
					$this->success('回复成功！');
				}else {
					$this->error($model->getError());
				}
			}else {
				$this->error($model->getError());
			}
		}
	}

	//回复次数统计
	private function ReplyCount(){
		$model = D('Addons://Comment/Comment');
		$map['id'] = I('post.commentid');
		$where['commentid'] = I('post.commentid');
		if($model->where($map)->find()){
			//查找有本评论有多少个子评论
			$remodel = D('Addons://Comment/CommentReply');
			$count = $remodel->where($where)->count();
			$data['reply_num'] = $count;
			$model->where($map)->save($data);
		}

	}

	/*获取指定的回复信息--按照发布时间来排序*/
	public function getAllReply(){
		$model = D('Addons://Comment/CommentReply');
		echo $model->getAllReply(I('post.commentid'));
	}


	/*验证传递的数据*/
	public function CheckData(){
		session('prev_url',$_SERVER['HTTP_REFERER']);
		if(!session('user_auth'))

			$this->error('请登录后操作...');
		if(!I('post.content'))
			$this->error('评论内容不能为空！');
	}

	/*敏感词过滤*/
	public function disableword(){
		//获取敏感词
	}

}