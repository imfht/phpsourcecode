<?php
namespace app\common\model;
// 贴吧帖子模型
use think\Model;

class Post extends Model {
	// 新增自动完成列表
    protected $insert = ['uid','last_ip','audit'];

    public function User() {	//关联用户表
        return $this->hasOne('User', 'id', 'uid')->field('username, name');
    }

    public function PostReply() {	//关联回复表
        return $this->hasOne('PostReply', 'post_id', 'id')->field('username, name');
    }

    protected function setUidAttr($value) {	// uid 字段[修改器]
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
        return confv('is_post_audit','system');
    }

	public function getUpdateTimeAttr($value) {	// update_time 创建时间[获取器]
		return time_line($value);
	}



}