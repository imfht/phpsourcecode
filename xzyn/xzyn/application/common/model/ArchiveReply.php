<?php
namespace app\common\model;
// 帖子回复模型
use think\Model;
use app\common\model\Archive;
use app\common\model\ZanLog;

class ArchiveReply extends Model {
	// 新增自动完成列表
    protected $insert = ['uid','last_ip','audit'];

    public function User() {	//关联用户表
        return $this->hasOne('User', 'id', 'uid');
    }

    public function UserInfo() {	//关联用户表
        return $this->hasOne('UserInfo', 'uid', 'uid');
    }

    public function Archive() {	//关联文章表
        return $this->hasOne('Archive', 'id', 'aid');
    }

    protected function setUidAttr($value) {	//uid字段[修改器]
        if ($value){
            return $value;
        }else{
            return session('userId');
        }
    }

    protected function setLastIpAttr() {	//last_ip 字段[修改器]
        return request()->ip();
    }

    protected function setAuditAttr() {	// audit 审核字段[修改器]
    	if( confv('is_arc_audit','system') == 0 ){
    		return 1;
    	}else{
    		return 0;
    	}
    }

	public function getCreateTimeAttr($value) {	// create_time 创建时间[获取器]
		return time_line($value);
	}

    public function getReplyNumAttr($value,$data) {	// reply_num 评论数量 [获取器]
		$reply_num = 0;
		$reply_num = $this->where( ['pid'=>$data['id'], 'audit' => 1] )->count();
        return $reply_num;
    }

    public function getZanNumAttr($value,$data) {	// zan_num 赞数量 [获取器]
		$ZanLog = new ZanLog();
		$zan_num = 0;
		$zan_num = $ZanLog->where( ['ar_id'=>$data['id']] )->count();
        return $zan_num;
    }

	//最新回复列表 10条
	public function newReplyList($num){
		$ArchiveModel = new Archive;
		$replylist = $this->where(['audit'=>1])->limit($num)->order('create_time desc')->select();
		foreach ($replylist as $k => $v) {
			$ArchiveDirs = $ArchiveModel->where(['id'=>$v['aid']])->find();
			$replylist[$k]['arcurl'] = '/detail/'. $ArchiveDirs->arctype->dirs . '/' . $v['aid'];
		}
		return $replylist;
	}


}