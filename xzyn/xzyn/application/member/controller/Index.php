<?php
namespace app\member\controller;

use app\common\controller\BaseMember;
use app\common\model\User;
use app\common\model\Archive;
use app\common\model\ArchiveReply;
use app\common\model\ZanLog;
use app\common\model\Arctype;
use think\facade\Request;

class Index extends BaseMember {
    public function initialize(){
        parent::initialize();

    }

    public function index() {
		$Archive = new Archive();
		$arcRep = new ArchiveReply();
		$zanLog = new ZanLog();
		$arcNum = $Archive->where( ['writer'=>$this->uid,'status'=>1] )->count();
		$arcRepNum = $arcRep->where( ['uid'=>$this->uid,'audit'=>1] )->count();
		$where[] = ['a_id','<>',''];
		$zanLogNum = $zanLog->where( $where )->where( 'uid',$this->uid )->count();
		$this->assign('arcNum',$arcNum);//文章数量
		$this->assign('arcRepNum',$arcRepNum);//回复数量
		$this->assign('zanLogNum',$zanLogNum);//赞数量
        return $this->fetch();
    }

    public function archive($type) {	// 发布/回复/赞的文章列表
		$Archive = new Archive();
		if( $type == 'myreply' ){
			$title = '回复的文章';
			$arcRep = new ArchiveReply();
			$arcRepNum = $arcRep->where( ['uid'=>$this->uid,'audit'=>1] )->select();
			$repIdArr = [];
			foreach ($arcRepNum as $k => $v) {
				$repIdArr[] = $v['aid'];
			}
			$where[] = ['status','=',1];
			$where[] = ['id','in',$repIdArr];
			$arcLists = $Archive->where( $where )->order('id desc')->paginate(10);
		}elseif( $type == 'myzan' ){
			$title = '赞的文章';
			$zanLog = new ZanLog();
			$where[] = ['a_id','<>',''];
			$zanLogNum = $zanLog->where( $where )->where( 'uid',$this->uid )->select();
			$repIdArr = [];
			foreach ($zanLogNum as $k => $v) {
				$repIdArr[] = $v['a_id'];
			}
			$arcLists = $Archive->where( 'id','in',$repIdArr )->where( ['status'=>1] )->order('id desc')->paginate(10);
		}else{
			$title = '发布的文章';
			$arcLists = $Archive->where( ['writer'=>$this->uid,'status'=>1] )->order('id desc')->paginate(10);
		}
		foreach ($arcLists as $k => $v) {
			$arcLists[$k]['arctypeurl'] = url('@category/'.$v->arctype->dirs);   //文章栏目链接
			$arcLists[$k]['arcurl'] = url('@detail/'.$v->arctype->dirs.'/'.$v['id']);   //文章链接
            $imgarr = getImgs($v['addonarticle']['content']);
		}
		$this->assign('arcLists',$arcLists);
		$this->assign('title',$title);
        return $this->fetch();
    }

    public function edit($id) {	//编辑文章 [已升级]
		$Archive = new Archive();
		$arcData= $Archive->where( ['id'=>$id] )->find();
		if( !empty($arcData) ){
			if( $arcData['writer'] != $this->uid ){
				$this->error('不是您发布的文章，不能编辑。');
			}
		}
		if(request()->isPost()){
			$data = Request::param();
			$data['status'] = confv('is_addarticle_audit','system');
			if( empty($data['content']) ){
				return ajaxReturn('内容不能为空');
			}
			$result = $this->validate($data,'Archive.add');
			if( true !== $result ){
				return ajaxReturn($result);
			}else{
				$result = $Archive->allowField(true)->save($data,$data['id']);
				$datas['aid'] = $id;
				$datas['content'] = $data['content'];
				$addonData = $Archive->addonarticle()->allowField(true)->save($datas,['aid'=>$id]);//新增关联表数据
			}
            if ($result){
            	$image = new \app\common\model\Image;
				if( !empty($data['imgurl']) ){
					$imgdata = $image->where(['fid'=>$data['id']])->find();
					if( empty($imgdata) ){
						$image->imgurl = $data['imgurl'];
						$image->save(['fid'=>$id]);
					}else{
						$image->imgurl = $data['imgurl'];
						$image->save(['fid'=>$id],['fid'=>$id]);
					}

				}
            	if(confv('is_addarticle_audit','system') == 0){
            		$info = '编辑成功，需要审核后才能显示。';
            	}else{
            		$info = '编辑成功';
            	}
                return ajaxReturn($info, url('/userinfo/'.$this->uid));
            }else{
                return ajaxReturn('没有编辑内容');
            }
		}else{
			$arctype = new Arctype();
			$arclist = $arctype->where(['status'=>1,'is_release'=>1])->order('sorts ASC,id ASC')->select();

			$this->assign('arclist',$arclist);
			$this->assign('data',$arcData);
			$this->assign('titles','编辑');
	        return $this->fetch('add_article');
		}
    }

    public function delete() {	//删除文章[已升级]
        if (request()->isPost()){
        	$Archive = new Archive();
            $id = input('id');
			$arcData= $Archive->where( ['id'=>$id] )->find();
			if( !empty($arcData) ){
				if( $arcData['writer'] != $this->uid ){
					return ajaxReturn('不是您发布的文章，不能删除。');
				}
			}
			$arc_reply = new ArchiveReply;
        	$imgurl = $arcData['litpic'];
            $delete =$Archive->where('id',$id)->delete();	//删除 文章

			if( $delete ){
				db($arcData['mod'])->where('aid',$id)->delete();   //关联表数据
				$image = new \app\common\model\Image;
				$delimg = delimg($arcData->imgurl);	//删除图片
				if( $delimg ){
					$image->where(['fid'=>$id])->delete();	//删除图片记录
				}
				$arc_reply_id = $arc_reply->where(['aid'=>$id])->column('id');
				$where[] = ['ar_id','in',$arc_reply_id];
				db('ZanLog')->where($where)->delete();   //删除 文章回复 赞数据
				db('ZanLog')->where(['a_id'=>$id])->delete();   //删除 文章 赞数据
				$arc_reply->where(['aid'=>$id])->delete();	//删除 文章回复
				db('Collect')->where(['aid'=>$id])->delete();   //删除 被收藏文章的记录
	            return ajaxReturn('删除成功','',1);
			}else{
				return ajaxReturn('删除失败');
			}
        }

    }


}
