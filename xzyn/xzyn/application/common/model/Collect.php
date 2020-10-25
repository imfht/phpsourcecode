<?php
namespace app\common\model;
// 收藏记录模型
use think\Model;

class Collect extends Model {
	// 新增自动完成列表
    protected $insert = [];
	// 设置json类型字段
	protected $json = [];

    public function User() {	//关联用户表
        return $this->hasOne('User', 'id', 'uid')->field('username, name');
    }

//  protected function setUidAttr($value) {	//uid字段[修改器]
//      if ($value){
//          return $value;
//      }else{
//          return session('userId');
//      }
//  }
	//获取aid数组
	public function aidArr($uid = ''){
		$data = $this->where(['uid'=>$uid])->select();
		$aidarr = [];
		if( !empty($data) ){
			foreach ($data as $k => $v) {
				$aidarr[] = $v['aid'];
			}
		}
		return $aidarr;
	}


}