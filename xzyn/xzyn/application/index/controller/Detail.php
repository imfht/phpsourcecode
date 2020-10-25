<?php
namespace app\index\controller;

use app\common\controller\BaseHome;
use app\common\model\Arctype;
use app\common\model\Archive;
use app\common\model\ZanLog;
use app\common\model\ArchiveReply;
use think\facade\Request;

class Detail extends BaseHome {
    public function initialize(){
        parent::initialize();
    }

    public function index($dirs, $id) {
        $arctypeModel = new Arctype();
        $arctype = $arctypeModel->where(['dirs'=>$dirs])->order('id DESC')->find();
        $archiveModel = new Archive();
        $archive = $archiveModel->where(['id'=>$id, 'status'=>1])->find();
        if (empty($archive)){
            //跳转404
        }
        $archive['content'] = $archive[$arctype->arctypeMod->mod]['content'];   //拓展模式表数据
        unset($archive[$arctype->arctypeMod->mod]);
        if($arctype['pid'] == '0'){
            $parent = $arctype;
        }else{
            $arctypeModel = new Arctype();
            $parent = $arctypeModel->topArctypeData($arctype['pid']);
        }
		$typelist = $arctypeModel->where(['pid'=>$parent['id'],'status'=>1])->order('id DESC')->select();
        $this->assign('typelist', $typelist);   //当前文章栏目顶级栏目信息
        $this->assign('parent', $parent);   //当前栏目顶级栏目信息
        $this->assign('arctype', $arctype);   //当前文章栏目信息
        $this->assign('archive', $archive);   //当前文章信息
        return $this->fetch($arctype['temparticle']);   //栏目模板
    }
	//加载回复列表
    public function replylist() {
		$data = input('get.');
		$ArchiveReply = new ArchiveReply();
		$reply_lists = $ArchiveReply->where(['aid'=>$data['aid'],'audit'=>1])->order(['pid'=>'DESC','id'=>'DESC'])->page($data['page'].', 50')->select();
		$treeClass = new \expand\Tree();
	    $reply_list = $treeClass->create($reply_lists);
		$this->assign('reply_list', $reply_list);
		$this->assign('archive',['id'=>$data['aid']]);
		return $this->fetch('inc/reply_list');
		// return p($reply_list);
	}

	public function arc_zan(){	//赞 [已升级]
		if(request()->isPost()){
			$zanlog = new ZanLog();
			$data = input('post.');
			if(empty($this->uid)){
				return ajaxReturn('亲！请登录点赞哦。','',2);
			}
			if( $data['type'] == 'reply'){
				$zanlogdata = $zanlog->where(['ar_id'=>$data['ar_id'],'uid'=>$this->uid])->find();
				if($zanlogdata){
					return ajaxReturn('您已经赞过啦！');
				}else{
					$zanadd = $zanlog->allowField(true)->save(['ar_id'=>$data['ar_id']]);//添加记录
					if($zanadd){
						return ajaxReturn('感谢您的点赞！','',1);
					}
				}
			}else if( $data['type'] == 'archive'){
				$zanlogdata = $zanlog->where(['a_id'=>$data['id'],'uid'=>$this->uid])->find();
				if($zanlogdata){
					return ajaxReturn('您已经赞过啦！');
				}else{
					$zanadd = $zanlog->allowField(true)->save(['a_id'=>$data['id']]);//添加记录
					if($zanadd){
						return ajaxReturn('感谢您的点赞！','',1);
					}
				}
			}
		}

	}

	public function arc_reply(){	//回复[已升级]
		if(request()->isPost()){
			$ArchiveReply = new ArchiveReply();
			$data = Request::param();
			if(empty($this->uid)){
				return ajaxReturn('亲！登录后才可以回复哦。','',2);
			}
			$result = $this->validate($data,'ArchiveReply.add');
			if( true !== $result ){
				return ajaxReturn($result);
			}else{
				$result = $ArchiveReply->allowField(true)->save($data);
			}
			if(confv('is_arc_audit','system') == 1){
            		$info = '回复成功，需要审核后才能显示。';
            	}else{
            		$info = '回复成功';
            	}
			if( $result ){
				return ajaxReturn($info,'',1);
			}else{
				return ajaxReturn('操作失败');
			}
		}
	}

	public function addArticle(){	//添加文章
		if(request()->isPost()){
			if(empty($this->uid)){
				return ajaxReturn('请登录发布','',2);
			}
			$data = Request::param();
			$data['status'] = confv('is_addarticle_audit','system');
			$archiveModel = new Archive();
			$result = $this->validate($data,'Archive.add');
			if( true !== $result ){
				return ajaxReturn($result);
			}else{
				$result = $archiveModel->allowField(true)->save($data);

				$datas['aid'] = $archiveModel->id;
				$datas['content'] = $data['content'];
				$addonData = $archiveModel->addonarticle()->allowField(true)->save($datas);//新增关联表数据
			}
            if ($result && $addonData){
            	$image = new \app\common\model\Image;
				if( !empty($data['imgurl']) ){
					$image->imgurl = $data['imgurl'];
					$image->save(['fid'=>$archiveModel->id]);
				}
            	if(confv('is_addarticle_audit','system') == 0){
            		$info = '发布成功，需要审核后才能显示。';
            	}else{
            		$info = '发布成功';
            	}
                return ajaxReturn($info, url('/category/post'));
            }else{
                return ajaxReturn('发布失败');
            }
		}else{
			$arctype = new Arctype();
			$arclist = $arctype->where(['status'=>1,'is_release'=>1])->order('sorts ASC,id ASC')->select();
			$this->assign('titles','添加');
			$this->assign('data',$data=0);
			$this->assign('arclist',$arclist);
			return $this->fetch();
		}
	}

}
