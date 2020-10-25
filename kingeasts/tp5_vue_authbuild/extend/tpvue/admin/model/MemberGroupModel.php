<?php
// 用户分组模型
// +----------------------------------------------------------------------
// | PHP version 5.6+
// +----------------------------------------------------------------------
// | Copyright (c) 2012-2014 http://www.bcahz.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: White to black <973873838@qq.com>
// +----------------------------------------------------------------------
namespace tpvue\admin\model;


class MemberGroupModel extends BaseModel
{
	protected $type       = [
        // 设置birthday为时间戳类型（整型）
        'auth_time' => 'timestamp:Y/m/d H:i:s',
        'group_id'    =>  'integer',
        'status'    =>  'integer',
    ];
    //定义时间戳字段名 
    //protected $createTime = 'create_time';
    //protected $updateTime = 'update_time';
    
    /**
     * 获取指定用户的用户组
     * @param  integer $member_id [description]
     * @return [type]             [description]
     */
    public function getMemberGroup($member_id=0,$field="*")
    {
    	$auth_member_group_model = new AuthMemberGroup();
    	$group_ids=$auth_member_group_model->where(array('member_id'=>$member_id))->column('group_id');
    	if ($group_ids) {
    		$this->where(array('id'=>array('in',$group_ids)));
	    	if ($field!='*') {
	    		$return=$this->column($field);
	    	}else{
	    		$return=$this->select();
	    	}
	    	return $return;
    	}else{
    		return false;
    	}
    	
    }
}