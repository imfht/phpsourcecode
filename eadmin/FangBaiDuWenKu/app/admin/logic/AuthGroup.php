<?php
// +----------------------------------------------------------------------
// | Author: Zaker <49007623@qq.com>
// +----------------------------------------------------------------------

namespace app\admin\logic;

/**
 * 权限组逻辑
 */
class AuthGroup extends AdminBase
{
    
    // 权限组模型
    public static $authGroupModel    = null;
    
    /**
     * 构造方法
     */
    public function __construct()
    {
        
        parent::__construct();
        
        self::$authGroupModel = model($this->name);
    }
    
    /**
     * 获取权限分组列表
     */
    public function getAuthGroupList($where = [], $field = true, $order = '', $paginate = false)
    {
        
        return self::$authGroupModel->getList($where, $field, $order, $paginate);
    }
    
    /**
     * 权限组添加
     */
    public function groupAdd($data = [])
    {
        
        $validate = validate($this->name);
        
        $validate_result = $validate->scene('add')->check($data);
        
        if (!$validate_result) : return [RESULT_ERROR, $validate->getError()]; endif;
        
        $url = url('authgroupList');
        
        return self::$authGroupModel->setInfo($data) ? [RESULT_SUCCESS, '权限组添加成功', $url] : [RESULT_ERROR, self::$authGroupModel->getError()];
    }
    
    /**
     * 权限组编辑
     */
    public function groupEdit($data = [])
    {
        
        $validate = validate($this->name);
        
        $validate_result = $validate->scene('edit')->check($data);
        
        if (!$validate_result) : return [RESULT_ERROR, $validate->getError()]; endif;
        
        $url = url('authgroupList');
        
        return self::$authGroupModel->setInfo($data) ? [RESULT_SUCCESS, '权限组编辑成功', $url] : [RESULT_ERROR, self::$authGroupModel->getError()];
    }
    
    /**
     * 权限组删除
     */
    public function groupDel($where = [])
    {
        
        return self::$authGroupModel->deleteInfo($where) ? [RESULT_SUCCESS, '权限组删除成功'] : [RESULT_ERROR, self::$authGroupModel->getError()];
    }
    /**
     * 权限组批量删除
     */
    public function groupAlldel($ids)
    {
    	 
    
    	return self::$authGroupModel->deleteAllInfo(['id'=>array('in',$ids)]) ? [RESULT_SUCCESS, '权限组删除成功'] : [RESULT_ERROR, self::$authGroupModel->getError()];
    }
    /**
     * 获取权限组信息
     */
    public function getGroupInfo($where = [], $field = true)
    {
        
        return self::$authGroupModel->getInfo($where, $field);
    }

    /**
     * 设置用户组权限节点
     */
    public function setGroupRules($data = [])
    {
        
        $data['rules'] = !empty($data['rules']) ? implode(',', array_unique($data['rules'])) : '';
        
        $url = url('groupList');
        
        return self::$authGroupModel->setInfo($data) ? [RESULT_SUCCESS, '权限设置成功', $url] : [RESULT_ERROR, self::$authGroupModel->getError()];
    }
    
    /**
     * 选择权限组
     */
    public function selectAuthGroupList($group_list = [], $member_group_list = [])
    {
        
        $member_group_ids = array_extract($member_group_list, 'group_id');
        
        foreach ($group_list as &$info) {
            
            in_array($info['id'], $member_group_ids) ? $info['tag'] = 'active' :  $info['tag'] = '';
        }
            
        return $group_list;
    }
    
}
