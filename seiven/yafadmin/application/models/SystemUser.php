<?php
/**
 * 后台账户
 * @author user
 *
 */
class SystemUser extends ActiveRecord\Model {
    // explicit table name since our table is not "books"
    static $table_name = 'admin_user';
    
    // explicit pk since our pk is not "id"
    static $primary_key = 'id';
    
    // explicit connection name since we always want our test db with this model
    // static $connection = 'test';
    
    // explicit database name will generate sql like so => my_db.my_book
    // static $db = 'my_db';
    /**
     * 登录
     */
    static function saveLoginStatus($user){
        $return = array(
            'status'=> false,
            'message'=> '未知错误' 
        );
        // 分组权限
        $group = SystemGroups::first($user->groupid);
        if($group){
            // 权限资源
            $rights = SystemRights::find('all', array(
                'conditions'=> array(
                    'id in (?)',
                    explode(',', $group->rightlist) 
                ) 
            ));
            $userRights = '';
            foreach($rights as $item){
                $userRights .= $item->content . ',';
            }
            $saveUser = array(
                'userinfo'=> $user,
                'rights'=> strtolower(trim($userRights, ',')) 
            );
            // 保存用户状态
            Yaf_Session::getInstance()->set(AdminController::admin_auth_session_key, serialize($saveUser));
            $return['status'] = true;
        }else{
            $return['message'] = '用户权限配置错误!';
        }
        return $return;
    }
    /**
     * 检测权限
     * @param unknown $controller
     * @param unknown $action
     */
    static function checkRight($uri){
        $uri = trim(strtolower($uri), '/');
        $uri = str_replace('admin@', '', str_replace('/', '@', $uri));
        $user = Yaf_Session::getInstance()->get(AdminController::admin_auth_session_key);
        if(!$user) return false;
        $user = unserialize($user);
        $userRight = ",,index@index,{$user['rights']},";
        return boolval(strpos($userRight, ",{$uri},"));
    }
}
