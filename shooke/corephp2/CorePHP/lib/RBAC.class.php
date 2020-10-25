<?php
namespace Lib;
use Core\Config;
/*
功能:权限验证。
作者：风微萧 QQ:82523829;
创建时间：2015-2-6;
使用样例：
<?php
$config['AUTH_SESSION']=true;//开启session缓存
$config['AUTH_GROUP']=7;
$config['AUTH_ADMIN']=1;
$config['AUTH_NO_CHECK']=array(
'index'=> 
array('index','login','verify'),
'common'=>'*'
);
RBAC::init($config);
RBAC::createTable();//创建测试数据表和数据
if(RBAC::check()) 
	echo "!";
else
	echo "dd";
?>
*/
//权限认证类
class RBAC
{
	public static $model=null;//数据库链接
	public static $group=null;//分组开启标志
	public static $session=false;//是否开启缓存
	public static $config;

	/**
	 * 初始化验证
	 * @param unknown $config
	 * @param string $model
	 */
	public static function init($config=array(),$model=null){
	    //是否开启session缓存	    
	    if($config['AUTH_SESSION']){
	        !isset($_SESSION) && session_start();//开启session
	        self::$session = 'AUTH_SESSION_'.$config['AUTH_GROUP'];
	    }
		//数据库链接,如果没有传入链接对象则自动创建
		self::$model = isset($model) ? $model : model();	
		//判断是否有分组
		self::$group = CP_GROUP;			
		//用户组
		self::$config['AUTH_GROUP'] = intval($config['AUTH_GROUP']);
		//管理员用户组
		self::$config['AUTH_ADMIN'] = isset($config['AUTH_ADMIN']) ? $config['AUTH_ADMIN'] : 1;//默认是1
		//无需验证模块
		self::$config['AUTH_NO_CHECK'] = isset($config['AUTH_NO_CHECK']) ? $config['AUTH_NO_CHECK'] : null;
		//数据库表
		self::$config['AUTH_TABLE_NODE']=isset($config['AUTH_TABLE_NODE']) ? $config['AUTH_TABLE_NODE'] : 'node';//模块功能表
		self::$config['AUTH_TABLE_ACCESS']=isset($config['AUTH_TABLE_ACCESS']) ? $config['AUTH_TABLE_ACCESS'] : 'access';//用户组与模块功能关联表
		
	}

	/**
	 * 取得所有功能节点
	 *
	 * @return unknown
	 */
	public static function getNode(){		
		$table = self::$config['AUTH_TABLE_NODE'];
		$node = self::$model->table($table)->order('id ASC')->select();
		//转换数组，用主键做key
		foreach ($node as $one_node){
			$one_node['name'] = self::tolower($one_node['name']);//转换为小写，防止因为大小写出现验证失误
			$return[$one_node['id']]=$one_node;
		}
		
		return $return;
	}

	/**
	 * 取得用户组的权限
	 *
	 * @return unknown
	 */
	public static function getAccess(){
		$table = self::$config['AUTH_TABLE_ACCESS'];
		if (!self::$config['AUTH_GROUP']) {
			exit('未设置用户组');
		}
		$where = array(
		'user_group_id'=>self::$config['AUTH_GROUP'],
		);
		$access = self::$model->table($table)->where($where)->select();
		return $access;
	}

	/**
	 * 获取用户组权限
	 *
	 * @return unknown
	 */
	public static function getAllow(){
	    //设置session缓存后读取缓存数据返回
	    if (self::$session && $_SESSION[self::$session]){
	        return $_SESSION[self::$session];
	    }
	    //初始化方法 模块 分组节点
	    $actionNode = array();
	    $moduleNode = array();
	    $groupNode = array();
	    //获取权限和节点
		$access = self::getAccess();
		$node = self::getNode();		
		//没有权限配置返回 false
		if(!$access){
		    return false;
		}
		//拥有权限配置，进行处理
		if(self::$group){
    		foreach ($access as $one){
    		    $actionNode = $node[$one['node_id']];//方法node
    		    $moduleNode = $node[$actionNode['pid']];//模块node
    		    $groupNode = $node[$moduleNode['pid']];//分组node
    		    
    		    $action = $actionNode['name'];
    		    $module = $moduleNode['name'];
    		    $group = $groupNode['name'];
    		    if($group && $module && $action){
    		       $Allow[$group][$module][$action] = 1; 
    		    }		
    		}		
		}else{//未开启分组处理
		    foreach ($access as $one){
		        $actionNode = $node[$one['node_id']];//方法node
		        $moduleNode = $node[$actionNode['pid']];//模块node		       
		    
		        $action = $actionNode['name'];
		        $module = $moduleNode['name'];
		        if($module && $action){
		            $Allow[$module][$action] = 1;
		        }
		    }
		}
		if (self::$session){
		    $_SESSION[self::$session]=$Allow;
		}
		return $Allow;
	}
	/**
	 * 验证手工设置 管理员和无需验证模块
	 *
	 * @return unknown
	 */
	public static function noCheck(){
		//根据参数个数设置$group,$module,$action
		$args = func_num_args();
		switch ($args){
		    case 3:
		        list($group, $module, $action) = func_get_args();
		        break;
		    case 2:
		        list($module, $action) = func_get_args();
		        break;		
		}
		

		if (self::$config['AUTH_ADMIN'] == self::$config['AUTH_GROUP']) {//如果用户是管理员
			return true;
		}else {			
		    //取得分组、模块和操作方法
		    $group = empty($group) ? CP_GROUP : $group;
		    $module = empty($module) ? CP_MODULE : $module;
		    $action = empty($action) ? CP_ACTION : $action;
		    //将分组、模块和操作方法进行小写转换,防止因为大小写出现验证失误
		    $group = self::tolower($group);
		    $module = self::tolower($module);
		    $action = self::tolower($action);
		    
			if (self::$group) {//分组功能开启时				
				if(isset(self::$config['AUTH_NO_CHECK'])){//配置无需验证模块时
					//所有模块无需验证
					if(self::$config['AUTH_NO_CHECK'][$group]=='*'){
						return true;
					}
					//模块属于分组，并且模块内所有方法无需验证
					if(self::$config['AUTH_NO_CHECK'][$group][$module]=='*'){
						return true;
					}
					//操作方法在模块内无需验证
					if (is_array(self::$config['AUTH_NO_CHECK'][$group][$module]) && in_array($action,self::$config['AUTH_NO_CHECK'][$group][$module])) {
						return true;
					}
				}
			}else {//未开启分组功能
				//所有方法无需验证
				if (self::$config['AUTH_NO_CHECK'][$module]=='*') {
					return true;
				}
				//操作方法在无需验证数组中
				if (is_array(self::$config['AUTH_NO_CHECK'][$module]) && in_array($action,self::$config['AUTH_NO_CHECK'][$module])) {
					return true;
				}
				
			}//end 分组验证

		}//end 管理员

		//不在管理员和无需验证范围内返回false
		return false;
	}
	/**
	 * 权限验证
	 * RBAC::check()或RBAC::check('group','module','action')
	 * @return unknown
	 */
	public static function check(){
		//根据参数个数设置$group,$module,$action 并进行配置验证
		$args = func_num_args();
		switch ($args){
		    case 3:
		        list($group, $module, $action) = func_get_args();
		        //管理员或无需验证 返回true
		        if(self::noCheck($group, $module, $action)){
		            return true;
		        }
		        break;
		    case 2:
		        list($module, $action) = func_get_args();
		        //管理员或无需验证 返回true
		        if(self::noCheck($module, $action)){
		            return true;
		        }
		        break;
		    case 0:
		    default:
		        //管理员或无需验证 返回true
		        if(self::noCheck()){
		            return true;
		        }		        
		}
				
		//取得分组、模块和操作方法
		$group = empty($group) ? CP_GROUP : $group;
		$module = empty($module) ? CP_MODULE : $module;
		$action = empty($action) ? CP_ACTION : $action;
		//将分组、模块和操作方法进行小写转换,防止因为大小写出现验证失误
		$group = self::tolower($group);
		$module = self::tolower($module);
		$action = self::tolower($action);
		//需要验证
		$Allow = self::getAllow();
		
		return self::$group ? $Allow[$group][$module][$action] : $Allow[$module][$action];
		
	}
	
	/**
	 * 转换为小写，防止因为大小写出现验证失误
	 *
	 * @param unknown_type $str
	 * @return unknown
	 */
	public static function tolower($str){
		return strtolower($str);
	}
    /**
     * 创建权限表和测试数据
     * @param string $testData 默认true 可设置false禁止插入测试数据
     */
    public static function createTable($testData=true){
        if (file_exists('RBAC.lock')) return ;//如果安装过则跳过安装
        
        $pre = Config::get('DB_PREFIX');
        $access = self::$config['AUTH_TABLE_ACCESS'];//权限表名
        $node = self::$config['AUTH_TABLE_NODE'];//节点表名
        $create_access="CREATE TABLE IF NOT EXISTS `{$pre}{$access}` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id主键',
  `user_group_id` smallint(6) unsigned NOT NULL COMMENT '用户组id',
  `node_id` smallint(6) unsigned NOT NULL COMMENT '功能节点id',
  PRIMARY KEY (`id`),
  KEY `groupId` (`user_group_id`),
  KEY `nodeId` (`node_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ";
        $create_node="CREATE TABLE IF NOT EXISTS `{$pre}{$node}` (
  `id` smallint(6) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL COMMENT '节点名称',
  `title` varchar(50) DEFAULT NULL COMMENT '描述说明',
  `sort` smallint(6) unsigned DEFAULT NULL COMMENT '排序',
  `pid` smallint(6) unsigned NOT NULL COMMENT '父级',
  PRIMARY KEY (`id`),
  KEY `pid` (`pid`),
  KEY `name` (`name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ";
        $insert_access = "INSERT INTO `{$pre}{$access}` (`id`, `user_group_id`, `node_id`) VALUES
(25, 1, 69),
(32, 1, 37),
(33, 1, 36),
(34, 1, 35),
(35, 1, 31)";
        $insert_node = "INSERT INTO `{$pre}{$node}` (`id`, `name`, `title`, `sort`, `pid`) VALUES
(49, 'read', '查看', NULL, 30),
(40, 'index', '默认模块', NULL, 30),
(37, 'resume', '恢复', NULL, 30),
(36, 'forbid', '禁用', NULL, 30),
(35, 'foreverdelete', '删除', NULL, 30),
(34, 'update', '更新', NULL, 30),
(33, 'edit', '编辑', NULL, 30),
(32, 'insert', '写入', NULL, 30),
(31, 'check', '新增', NULL, 30),
(30, 'index', '公共模块', NULL, 1),
(69, 'c', '数据管理', NULL, 1),
(1, 'home', 'Rbac后台管理', NULL, 0)";
        self::$model->query($create_node);
        self::$model->query($create_access);
        if ($testData){            
            self::$model->query($insert_node);
            self::$model->query($insert_access);            
        }
        file_put_contents('RBAC.lock', '');
        
    }
    
}
