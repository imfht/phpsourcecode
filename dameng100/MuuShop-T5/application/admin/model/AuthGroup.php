<?php
namespace app\admin\model;

use think\Model;
use think\Db;

/**
 * 用户组模型类
 */
class AuthGroup extends Model {
    const TYPE_ADMIN                = 1;                   // 管理员用户组类型标识
    const MEMBER                    = 'member';
    const UCENTER_MEMBER            = 'ucenter_member';
    const AUTH_GROUP_ACCESS         = 'auth_group_access'; // 关系表表名
    const AUTH_EXTEND               = 'auth_extend';       // 动态权限扩展信息表
    const AUTH_GROUP                = 'auth_group';        // 用户组表名
    const AUTH_EXTEND_CATEGORY_TYPE = 1;              // 分类权限标识
    const AUTH_EXTEND_MODEL_TYPE    = 2; //分类权限标识


    public $error;
    //自定义初始化
    public function initialize()
    {
        //需要调用`Model`的`initialize`方法
        parent::initialize();
        //TODO:自定义的初始化
        $this->error = '发生错误';
    }
    /**
     * 返回用户组列表
     * 默认返回正常状态的管理员用户组列表
     * @param array $where   查询条件,供where()方法使用
     */
    public function getGroups($where=array()){
        $map = array('status'=>1,'type'=>self::TYPE_ADMIN,'module'=>'admin');
        $map = array_merge($map,$where);
        return $this->where($map)->select();
    }

    /**
     * 把用户添加到用户组,支持批量添加用户到用户组
     * 示例: 把uid=1的用户添加到group_id为1,2的组 `AuthGroupModel->addToGroup(1,'1,2');`
     */
    public function addToGroup($uid=0,$group_id=''){

        if($uid && $group_id){
            $uid = is_array($uid)?implode(',',$uid):trim($uid,',');
            $group_id = is_array($group_id)?$group_id:explode( ',',trim($group_id,',') );

            $Access = Db::name(self::AUTH_GROUP_ACCESS);
            //if( isset($_REQUEST['batch']) ){
                //为单个用户批量添加用户组时,先删除旧数据
                $del = $Access->where(['uid'=>['in',$uid]])->delete();
            //}

            $uid_arr = explode(',',$uid);
            
            $add = [];
            //if( $del!==false ){
                foreach ($uid_arr as $u){
                    foreach ($group_id as $g){
                        if( is_numeric($u) && is_numeric($g) ){
                            $add[] = ['group_id'=>$g,'uid'=>$u];
                        }
                    }
                }
                $res = $Access->insertAll($add);
            //}
        }else{
            return false;
        }
        
        if (!$res) {
            if( count($uid_arr)==1 && count($group_id)==1 ){
                //无写入时的错误提示
                $this->error = lang('_CANNEL_ALL_THE_GROUP_');
            }
            return false;
        }else{
            return true;
        }
    }

    /**
     * 返回用户所属用户组信息
     * @param  int    $uid 用户id
     * @return array  用户所属的用户组 array(
     *                                         array('uid'=>lang('_USER_ID_'),'group_id'=>lang('_USER_GROUP_ID_'),'title'=>lang('_USER_GROUP_NAME_'),'rules'=>'用户组拥有的规则id,多个,号隔开'),
     *                                         ...)   
     */
    static public function getUserGroup($uid){
        static $groups = array();
        if (isset($groups[$uid]))
            return $groups[$uid];
        $prefix = config('database.prefix');
        $user_groups = Db::table($prefix.self::AUTH_GROUP_ACCESS)
            ->alias('a')
            ->field('uid,group_id,title,description,rules')
            ->join ($prefix.self::AUTH_GROUP.' g', 'a.group_id=g.id')
            ->where("a.uid='$uid' and g.status='1'")
            ->select();

        $groups[$uid]=$user_groups?$user_groups:array();
        return $groups[$uid];
    }
    
    /**
     * 返回用户拥有管理权限的扩展数据id列表
     * 
     * @param int     $uid  用户id
     * @param int     $type 扩展数据标识
     * @param int     $session  结果缓存标识
     * @return array
     */
    static public function getAuthExtend($uid,$type,$session){
        if ( !$type ) {
            return false;
        }
        if ( $session ) {
            $result = session($session);
        }
        if ( $uid == UID && !empty($result) ) {
            return $result;
        }
        $prefix = config('database.prefix');
        $result = Db::table($prefix.self::AUTH_GROUP_ACCESS.' g')
                        ->join($prefix.self::AUTH_EXTEND.' c on g.group_id=c.group_id')
                        ->where("g.uid='$uid' and c.type='$type' and !isnull(extend_id)")
                        ->getfield('extend_id',true);
        if ( $uid == UID && $session ) {
            session($session,$result);
        }
        return $result;
    }

    /**
     * 返回用户拥有管理权限的分类id列表
     * 
     * @param int     $uid  用户id
     * @return array
     *  
     *  array(2,4,8,13) 
     *
     * @author 朱亚杰 <zhuyajie@topthink.net>
     */
    static public function getAuthCategories($uid){
        return self::getAuthExtend($uid,self::AUTH_EXTEND_CATEGORY_TYPE,'AUTH_CATEGORY');
    }



    /**
     * 获取用户组授权的扩展信息数据
     * 
     * @param int     $gid  用户组id
     * @return array
     */
    static public function getExtendOfGroup($gid,$type){
        if ( !is_numeric($type) ) {
            return false;
        }
        return Db::name(self::AUTH_EXTEND)->where( array('group_id'=>$gid,'type'=>$type) )->value('extend_id',true);
    }

    /**
     * 获取用户组授权的分类id列表
     * 
     * @param int     $gid  用户组id
     * @return array
     *  
     */
    static public function getCategoryOfGroup($gid){
        return self::getExtendOfGroup($gid,self::AUTH_EXTEND_CATEGORY_TYPE);
    }
    

    /**
     * 批量设置用户组可管理的扩展权限数据
     *
     * @param int|string|array $gid   用户组id
     * @param int|string|array $cid   分类id
     * 
     */
    static public function addToExtend($gid,$cid,$type){
        $gid = is_array($gid)?implode(',',$gid):trim($gid,',');
        $cid = is_array($cid)?$cid:explode( ',',trim($cid,',') );

        $Access = Db::name(self::AUTH_EXTEND);
        $del = $Access->where( array('group_id'=>array('in',$gid),'type'=>$type) )->delete();

        $gid = explode(',',$gid);
        $add = array();
        if( $del!==false ){
            foreach ($gid as $g){
                foreach ($cid as $c){
                    if( is_numeric($g) && is_numeric($c) ){
                        $add[] = array('group_id'=>$g,'extend_id'=>$c,'type'=>$type);
                    }
                }
            }
            $Access->insertAll($add);
        }
        if ($Access->getDbError()) {
            return false;
        }else{
            return true;
        }
    }

    /**
     * 批量设置用户组可管理的分类
     *
     * @param int|string|array $gid   用户组id
     * @param int|string|array $cid   分类id
     * 
     * @author 朱亚杰 <zhuyajie@topthink.net>
     */
    static public function addToCategory($gid,$cid){
        return self::addToExtend($gid,$cid,self::AUTH_EXTEND_CATEGORY_TYPE);
    }


    /**
     * 将用户从用户组中移除
     * @param int|string|array $gid   用户组id
     * @param int|string|array $cid   分类id
     * @author 朱亚杰 <xcoolcc@gmail.com>
     */
    public function removeFromGroup($uid,$gid){
        return Db::name(self::AUTH_GROUP_ACCESS)->where( array( 'uid'=>$uid,'group_id'=>$gid) )->delete();
    }

    /**
     * 获取某个用户组的用户列表
     *
     * @param int $group_id   用户组id
     */
    static public function memberInGroup($group_id){
        $prefix   = config('database.prefix');
        $l_table  = $prefix.self::MEMBER;
        $r_table  = $prefix.self::AUTH_GROUP_ACCESS;
        $r_table2 = $prefix.self::UCENTER_MEMBER;
        $list     = Db::table($l_table.' m') 
                        ->field('m.uid,u.username,m.last_login_time,m.last_login_ip,m.status')
                       ->join($r_table.' a', 'm.uid=a.uid')
                       ->join($r_table2.' u', 'm.uid=u.id')
                       ->where(array('a.group_id'=>$group_id))
                       ->select();
        return $list;
    }

    /**
     * 检查id是否全部存在
     * @param array|string $group_id  用户组id列表
     */
    public function checkId($modelname,$group_id,$msg = '以下id不存在:'){
        if(is_array($group_id)){
            $count = count($group_id);
            $ids   = implode(',',$group_id);
        }else{
            $group_id   = explode(',',$group_id);
            $count = count($group_id);
            $ids   = $group_id;
        }

        $s = Db::name($modelname)->where(['id'=>['in',$ids]])->field('id')->find();

        if($s){
            return true;
        }else{
            $diff = implode(',',array_diff($group_id,$s));
            $this->error = $msg.$diff;
            return false;
        }
    }

    /**
     * 检查用户组是否全部存在
     * @param array|string $gid  用户组id列表
     * @author 朱亚杰 <zhuyajie@topthink.net>
     */
    public function checkGroupId($gid){
        return $this->checkId('AuthGroup',$gid, '以下用户组id不存在:');
    }
}

