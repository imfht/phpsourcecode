<?php
namespace auth;
use think\Db;
/**
 *此类已被改造，和tp3.2.3版本内置的有所不同
 */
class Auth
{
	private $prefix='';
    //默认配置
    protected $_config = array(
        'auth_on'           => true, // 认证开关
        'auth_type'         => 1, // 认证方式，1为实时认证；2为登录认证。
        'auth_group'        => 'auth_group', // 用户组数据表名
        'auth_group_access' => 'auth_group_access', // 用户-用户组关系表
        'auth_rule'         => 'auth_rule' // 权限规则表     
    );
	
	/**
	 * $prefix表前缀
	 */
    public function __construct($prefix='')
    {
    	$this->prefix=$prefix;

       	$prefix  =$prefix;
        $this->_config['auth_group']        = $prefix . $this->_config['auth_group'];
        $this->_config['auth_rule']         = $prefix . $this->_config['auth_rule'];    
        $this->_config['auth_group_access'] = $prefix . $this->_config['auth_group_access'];
        if (config('AUTH_CONFIG')) {
            //可设置配置项 AUTH_CONFIG, 此配置项为数组。
            $this->_config = array_merge($this->_config, config('AUTH_CONFIG'));
        }
		
    }

    /**
     * 检查权限
     * @param name string|array  需要验证的规则列表,支持逗号分隔的权限规则或索引数组
     * @param uid  int           认证用户的id
     * @param string mode        执行check的模式
     * @param relation string    如果为 'or' 表示满足任一条规则即通过验证;如果为 'and'则表示需满足所有规则才能通过验证
     * @return boolean           通过验证返回true;失败返回false
     */
    public function check($name, $uid, $type = 1, $mode = 'url', $relation = 'or')
    {	
        if (!$this->_config['auth_on']) {
            return true;
        }

        $authList = $this->getAuthList($uid, $type); //获取用户需要验证的所有有效规则列表
   
        if (is_string($name)) {
            $name = strtolower($name);
            if (strpos($name, ',') !== false) {
                $name = explode(',', $name);
            } else {
                $name = array($name);
            }
        }
        $list = array(); //保存验证通过的规则名
        if ($mode == 'url') {
            $REQUEST = unserialize(strtolower(serialize($_REQUEST)));
        }
		
        foreach ($authList as $auth) {
            $query = preg_replace('/^.+\?/U', '', $auth);
            if ($mode == 'url' && $query != $auth) {
                parse_str($query, $param); //解析规则中的param
                $intersect = array_intersect_assoc($REQUEST, $param);
                $auth      = preg_replace('/\?.*$/U', '', $auth);
                if (in_array($auth, $name) && $intersect == $param) {
                    //如果节点相符且url参数满足
                    $list[] = $auth;
                }
            } else if (in_array($auth, $name)) {
                $list[] = $auth;
            }
        }
		
        if ($relation == 'or' and !empty($list)) {
            return true;
        }
        $diff = array_diff($name, $list);
        if ($relation == 'and' and empty($diff)) {
            return true;
        }
        return false;
    }

    /**
     * 根据用户id获取用户组,返回值为数组
     * @param  uid int     用户id
     * @return array       用户所属的用户组 array(
     *     array('uid'=>'用户id','group_id'=>'用户组id','title'=>'用户组名称','rules'=>'用户组拥有的规则id,多个,号隔开'),
     *     ...)
     */
    public function getGroups($uid)
    {
        static $groups = array();
        if (isset($groups[$uid])) {
            return $groups[$uid];
        }

        $groups = Db::name($this->_config['auth_group_access'] . ' a')
            ->where("a.uid='$uid' and g.status='1'")
            ->join('__'.strtoupper($this->_config['auth_group']).'__' . " g", " a.group_id=g.id")
            ->field('uid,group_id,title,rules')->select();
        $groups[$uid] = $groups ?: array();
        return $groups[$uid];
    }

    /**
     * 获得权限列表
     * @param integer $uid  用户id
     * @param integer $type
     */
    protected function getAuthList($uid, $type)
    {
        static $_authList = array(); //保存用户验证通过的权限列表
        $t                = implode(',', (array) $type);
        if (isset($_authList[$uid . $t])) {
            return $_authList[$uid . $t];
        }
		
        if ($this->_config['auth_type'] == 2) {
        	$session=session('_AUTH_LIST_'.$this->prefix.$uid.$t);
        	if(isset($session))
            return $session;
        }

        //读取用户所属用户组
        $groups = $this->getGroups($uid);
        $ids    = array(); //保存用户所属用户组设置的所有权限规则id
 
        foreach ($groups as $g) {
        	//dump($g);
        	if(isset($g['rules']))
            $ids = array_merge($ids, explode(',', trim($g['rules'], ',')));
        }
    
        $ids = array_unique($ids);
        if (empty($ids)) {
            $_authList[$uid . $t] = array();
            return array();
        }

        $map=array(
            'menu_id'=>array('in',$ids),           
        );

        // 读取用户组所有权限规则
        $rules = Db::name($this->_config['auth_rule'])->where($map)->field('name')->select();

        // 循环规则，判断结果。
        $authList = array(); //
        foreach ($rules as $rule) {              
                $authList[] = strtolower($rule['name']);           
        }
        $_authList[$uid . $t] = $authList;
        if ($this->_config['auth_type'] == 2) {
            //规则列表结果保存到session
            session('_AUTH_LIST_'.$this->prefix.$uid.$t,$authList);
         }
        return array_unique($authList);
    }



}
