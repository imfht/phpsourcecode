<?php
namespace app\lib;

class Bauth{
	private $_uid; //userid
    private $_allowIds; // allow ids
    private $_allowList = [];  // allow list

    private $_module = '';  // admin shop
    private $_rootPower = false;



    public function __construct($uid, $module)
    {
        $this->_uid = $uid;
        if(in_array($uid, [1])){
            $this->_rootPower = true;
        }
        $this->_module = strtolower($module);
    }

    public function authInit()
    {
        // 获取有权限的ID
        $this->_allowIds = $this->_loadAllowIdsByUid($this->_uid);
        $this->_allowIds .= ','.$this->_publicIds();
        // 获取有权限的菜单
        $menuList = (new Menu($this->_rootPower, $this->_allowIds))->getMenu($this->_module, ['hide'=>['in', '0,1'], 'is_dev'=>['in', '0,1']]);
        // 生成权限数组
        $this->_allowList = $this->_generateList($menuList);
        $pub = $this->_publicAllow();
        $this->_allowList=array_merge_recursive_distinct($this->_allowList, $pub);
        return $this->_allowList;
    }

    /**
     * 用户都看到并且访问的菜单项
     * @author EchoEasy
     */
    private function _publicIds()
    {
        $public = ['1', '189'];
        return implode(',', $public);
    }

    /**
     * 用户都可访问的权限  可以不显示在菜单
     * @author EchoEasy
     */
    private function _publicAllow()
    {
        $con = [
            'admin' => [
                'index' => [
                    'index' => '',
                    'introduce' => '',
                    'developer' => '',
                    'datetimepiker_demo' => '',
                    'ajaxsubmit_demo' => '',
                    'ajaxfileupload_demo' => '',
                    'umeditor_demo' => '',
                    'multiple_demo' => '',
                    'menu_demo' => '',
                ],
                'administrator'=>[
                    'updatenickname' => '',
                    'submitnickname' => '',
                    'updatepassword' => '',
                    'submitpassword' => '',
                ]
            ],
        ];
        $pub = config('PUBLIC_ALLOW') == NULL? []: config('PUBLIC_ALLOW');
        $con = array_merge_recursive_distinct($con, $pub);
        return $con;
    }

    /**
     * 获取用户所属角色组可访问的菜单ID
     * @author EchoEasy
     */
    private function _loadAllowIdsByUid($uid)
    {
        $sql = "SELECT rules FROM `think_auth_group` AS g, `think_auth_group_access` AS a WHERE a.uid=$this->_uid AND g.id=a.group_id AND g.status=1";
        $ret = db()->query($sql);
        $rules = '';
        if($ret){
            $rules = $this->_getAllRules($ret);
        }
        return $rules;
    }
    // 允许访问的菜单ID并集
    private function _getAllRules($ret)
    {
        $rules = '';
        foreach($ret as $value){
            $rules .= $value['rules'].',';
        }
        $rules = rtrim($rules, ',');
        $tempArr = array_unique(explode(',', $rules));
        $rules = implode(',', $tempArr);
        return $rules;
    }


    /**
     * 生成权限数组
     * @param    [type]                   $menus [description]
     * @return   [type]                          [description]
     * @author EchoEasy
     * @DateTime 2016-12-24T12:11:16+0800
     */
    private function _generateList($menus)
    {
        if(empty($menus))
            return [];
        $allowArr = [];
        foreach ($menus as $key => $value) {
            $m = strtolower($value['module']);
            $c = strtolower($value['controller']);
            $a = strtolower($value['action']);
            switch ($value['type']) {
                case '1':
                case '3':
                    $allowArr[$m][$c][$a]['type'] = $value['type'];
                    $allowArr[$m][$c][$a]['param'] = [];
                    if(!empty($value['url']))
                        parse_str($value['url'], $allowArr[$m][$c][$a]['param']);
                    break;
                default:
                    $allowArr[$m][$c][$a] = '';
                    break;
            }
        }
        return $allowArr;
    }

//获取属性的方法
    public function getAllowId()
    {
        return $this->_allowIds;
    }

    public function getPower()
    {
        return $this->_rootPower;
    }

    public function setAllowList($list)
    {
        $this->_allowList = $list;
        return true;
    }

    /**
     * 权限检查
     * @param    [type]                   $controller [description]
     * @param    [type]                   $action     [description]
     * @param    array                    $params     [description]
     * @return   [type]                               [description]
     * @author EchoEasy
     * @DateTime 2016-12-24T12:12:06+0800
     */
    public function check($controller, $action, $params=[])
    {
        $controller = strtolower($controller);
        $action = strtolower($action);
        // 是否为超级用户
        if($this->_rootPower){
            return true;
        }
        // 检查是否有访问权限
        if(!isset($this->_allowList[$this->_module][$controller][$action])){
            return false;
        }
        // 检查参数
        $actionAuth = $this->_allowList[$this->_module][$controller][$action];
        if(!empty($actionAuth)){
            if($actionAuth['type'] == "1"){
                foreach ($actionAuth['param'] as $key => $value) {
                    if(empty($params[$key])){ // 缺少必须的参数
                        return false;
                    }
                    if(!empty($value)){ // 必须的参数不合格
                        if($value != $params[$key]) return false;
                    }
                }
            }else if($actionAuth['type'] == "3"){
                foreach ($actionAuth['param'] as $key => $value) {
                    if(empty($params[$key])){ // 缺少必须的参数
                        return false;
                    }
                    // 正则验证参数
                    if (!preg_match($value, $params[$key])){
                        return false;
                    }
                }
            }
        }
        return true;
    }

//后期 不重要的扩展
    public function checkUrl($url)
    {
        // $this->_parseUrl($url);
    }
    // 支持直接URL认证
    private function _parseUrl($url)
    {

    }
}