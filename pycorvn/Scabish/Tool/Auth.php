<?php 
namespace Scabish\Tool;

use SCS;
use Scabish\Tool\Cookie;
use Scabish\Tool\Identity;

/**
 * Scabish\Tool\Auth
 * 后台登录授权组件
 *
 * @copyright 2016 Focrs, Co.,Ltd
 * @author keluo <keluo@focrs.com>
 * @since 2016-12-14
 */
class Auth {

    private $_info = null;
    
    private static $_instance;
    
    private $_name = 'sid'; // id标识存储在cookie中的名称
    
    public function __construct() {}
    
    public static function Instance() {
        if(!(self::$_instance instanceof self)) {
            self::$_instance = new self();
           
        }
        return self::$_instance;
    }
    
    /**
     * 获取登录ID
     * @return integer
     */
    public function GetId() {
        return intval(Identity::Instance(SCS::Instance()->key)->Get(Cookie::Get($this->_name)));
    }
    
    public function Info() {
        if(is_null($this->_info)) {
            $this->_info = \Scabish\Abyss\Client::Instance($this->GetId())->Passport();
        }
        
        return $this->_info;
    }
    
    /**
     * 设置为登录状态
     * @param integer $id adminID
     * @param ingeter $expire 过期时间(秒)
     */
    public function Login($id, $expire) {
        Cookie::Set($this->_name, Identity::Instance(SCS::Instance()->key)->Set($id), time()+intval($expire));
    }
    
    /**
     * 设置为退出状态
     */
    public function Logout() {
        Cookie::Delete($this->_name);
    }
    
    /**
     * 检查权限，包含路由权限和特殊权限两种
     * @param string $item 检查项
     * @param string $type route|special, 路由|特殊权限
     * @return string|boolean
     */
    public function Check($item = '', $type = 'route') {
        if($type == 'route') { // 路由权限
            if(!($staffId = $this->GetId())) return '{USER_NOT_LOGIN}';
            $auth = self::Info()->role->fdAuth ? : [];
            $actions = isset($auth->action) && is_array($auth->action) ? $auth->action : [];
            $item = $item ? : SCS::Request()->route;
            return in_array(strtolower($item), $actions) ? : '{USER_NOT_AUTH}';
        } elseif($type == 'special') { // 特殊权限
            if(!($staffId = $this->GetId())) return false;
            $auth = self::Info()->role->fdAuth ? : [];
            $specials = (isset($auth['special']) && is_array($auth['special'])) ? $auth['special'] : [];
            return in_array($item, $specials);
        }
        return false;
    }
}