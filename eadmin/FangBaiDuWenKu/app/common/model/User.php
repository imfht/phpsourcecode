<?php
// +----------------------------------------------------------------------
// | Author: Zaker <49007623@qq.com>
// +----------------------------------------------------------------------

namespace app\common\model;

/**
 * 会员模型
 */
class User extends ModelBase
{
    
	protected $insert = ['regtime'=>TIME_NOW];
    /**
     * 上级获取器
     */
    public function getLeaderNicknameAttr()
    {
        
        return model('User')->getValue(['id' => $this->data['leader_id']], 'nickname', '无');
    }
    public function getStatutextAttr($value,$data)
    {
    	$status = [DATA_DELETE => '删除', DATA_DISABLE => '禁用', DATA_NORMAL => '启用',2 => '邮箱认证',3 => '手机认证',4 => '认证',5 => '邮箱手机认证'];
    
    	return $status[$this->data[DATA_COMMON_STATUS]];
    
    }
}
