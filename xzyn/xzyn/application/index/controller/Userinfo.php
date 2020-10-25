<?php
namespace app\index\controller;

use app\common\controller\BaseHome;
use app\common\model\User as Users;
use app\common\model\Archive;
use app\common\model\ArchiveReply;
use app\common\model\Focus;

class Userinfo extends BaseHome {
	private $cModel;

    public function initialize(){
        parent::initialize();
		$this->cModel = new Users();
    }

    public function index($uid) {
		$fw_jilu = cookie('fw_jilu_'.$uid);
		$ip = request()->ip();
		if( empty($fw_jilu) ){
			db('User')->where('id', $uid)->setInc('click');//浏览数量 +1
			cookie('fw_jilu_'.$uid, $ip, 86400);//有效期1天
		}else{
			if( $fw_jilu != $ip ){
				db('User')->where('id', $uid)->setInc('click');//浏览数量 +1
				cookie('fw_jilu_'.$uid, $ip, 86400);//有效期1天
			}
		}
		$udata = $this->cModel->where( ['id'=>$uid,'status'=>1] )->find();
		$this->assign('udata',$udata);	//用户数据
        return $this->fetch();
    }

    public function newarc(){	//发布/回复的文章
    	if (request()->isPost()){
	    	$data = input('post.');
	        $archive = new Archive();
			if( $data['type'] == 'newarc' ){
		        $where = ['status'=>1,'writer'=>$data['uid']];
		        $dataList = $archive->where($where)->order('id desc')->page($data['page'].', 5')->select();
			}else{
				$arcRep = new ArchiveReply();
				$arcRepNum = $arcRep->where( ['uid'=>$data['uid'],'audit'=>1] )->select();
				$repIdArr = [];
				foreach ($arcRepNum as $k => $v) {
					$repIdArr[] = $v['aid'];
				}
				$where[] = ['id','in',$repIdArr];
				$dataList = $archive->where($where)->where(['status'=>1])->order('id desc')->page($data['page'].', 5')->select();
			}
	        $this->assign('dataList', $dataList);
	        return $this->fetch('inc/new_arc');
		}
    }

    public function focus() {	//关注用户
        if (request()->isPost()){
    		$Focus = new Focus();
            $uid = input('post.uid');
			if(empty($this->uid)){
				return ajaxReturn('请登录关注','',2);
			}
			if( $uid == $this->uid ){
				return ajaxReturn('您不能关注自己');
			}
			$fdata = $Focus->where(['fuid'=>$this->uid,'uid'=>$uid])->find();
			if( empty($fdata) ){
				$save = $Focus->allowField(true)->save(['fuid'=>$this->uid,'uid'=>$uid]);
				if($save){
					return ajaxReturn('感谢您的关注','',1);
				}else{
					return ajaxReturn('关注失败');
				}
			}else{
				$delete = $Focus->where(['id'=>$fdata['id']])->delete();
				if($delete){
					return ajaxReturn('您已经取消关注','',1);
				}else{
					return ajaxReturn('取消关注失败');
				}
			}
        }
    }

    public function focuslist() {	//关注列表
        if (request()->isPost()){
    		$Focus = new Focus();
            $data = input('post.');
			$fidarr = [];
			if( $data['type'] == 'focus_ta' ){
				$fuserId = $Focus->where(['uid'=>$data['uid']])->order('id desc')->select();
				foreach ($fuserId as $k => $v) {
					$fidarr[] = $v['fuid'];
					$gz_time[$v['fuid']] = $v->getData('create_time');
				}
			}else{
				$fuserId = $Focus->where(['fuid'=>$data['uid']])->order('id desc')->select();
				foreach ($fuserId as $k => $v) {
					$fidarr[] = $v['uid'];
					$gz_time[$v['uid']] = $v->getData('create_time');
				}
			}
			$where[] = ['id','in',$fidarr];
			$fdata = $this->cModel->where( $where )->order('id desc')->page($data['page'].', 8')->select();
			foreach ($fdata as $k => $v) {
				$v['gz_time'] = $gz_time[$v['id']];
			}
//			$fdata = array_sort($fdata,'gz_time','desc');	//排序
			$this->assign('fuser',$fdata);	//关注的用户数据
			return $this->fetch('inc/focus_list');
        }
    }


}
