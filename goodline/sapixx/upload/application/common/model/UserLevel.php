<?php
/**
 * 用户层级关系表 Table<ai_user_level>
 */
namespace app\common\model;
use think\Model;
use think\facade\Validate;

class UserLevel extends Model{

    protected $pk = 'id';

     /**
     * 用户信息关联
    * @return void
     */
    public function user(){
        return $this->hasOne('User','id','user_id');
    }

     /**
     * 用户信息关联
    * @return void
     */
    public function parentUser(){
        return $this->hasOne('User','id','parent_id');
    }
    
    /**
     * 创建用户关系一
     * @param integer $user_id   当前用户ID
     * @param integer $parent_id 上级用户ID
     * @return void
     */
    public function addLevel(int $user_id,int $parent_id){
        if($parent_id){
            $data[0]['level']     = 1;
            $data[0]['parent_id'] = $parent_id;
            $data[0]['user_id']   = $user_id;
            $levellist = self::where(['user_id' => $parent_id])->select();
            foreach ($levellist as $value) {
                $level  = $value['level'] + 1;
                $data[] = ['level' => $level,'parent_id' => $value['parent_id'],'user_id' => $user_id];
            }
            return self::insertAll($data);
        }
        return true;
    }

    /**
     * 查询某个用户的粉丝
     * @param int   $uid
     * @param array $level  [1,2,3,4]层级
     * @return void
     */
    public static function levelUser($uid,$level){
        $lists = self::where(['parent_id' => $uid,'level' => $level])->order('id desc')->paginate(20,false);
        if(empty($lists)){
            return;
        }
        $data = [];
        foreach ($lists as $key => $value) {
            $data[$key]['face']        = $value->user->face;
            $data[$key]['nickname']    = $value->user->nickname;
            $data[$key]['level']       = $value->level;
            $data[$key]['parent_id']   = $value->parent_id;
            $data[$key]['user_id']     = $value->user->id;
            $data[$key]['invite_code'] = $value->user->invite_code;
            $data[$key]['create_time'] = date('Y-m-d H:i',$value->user->create_time);
        }
        return $data;
    }

    /**
     * 查询某个用户的邀请溯源
     * @param int $uid 用户ID
     * @return void
     */
    public static function children_user(int $uid){
        $level = self::where(['user_id' => $uid])->field('parent_id,level,user_id')->select()->toArray();
        if(empty($level)){
            return [];
        }
        $uid = array_column($level,'parent_id');
        $info = model('User')->field('id,nickname,invite_code,face,phone_uid')->whereIn('id',$uid)->select()->toArray();
        if(empty($info)){
            return [];
        }
        $uida = [];
        foreach ($level as $value) {
            $uida[$value['parent_id']]['level']   = $value['level'];
            $uida[$value['parent_id']]['user_id'] = $value['user_id'];
        }
        $level_user = [];
        foreach ($info as $value) {
            $level_user[$uida[$value['id']]['level']]['user_id']     = $uida[$value['id']]['user_id'];
            $level_user[$uida[$value['id']]['level']]['invite_code'] = $value['invite_code'];
            $level_user[$uida[$value['id']]['level']]['nickname']    = $value['nickname'];
            $level_user[$uida[$value['id']]['level']]['face']        = $value['face'];
            $level_user[$uida[$value['id']]['level']]['phone_uid']   = $value['phone_uid'];
            $level_user[$uida[$value['id']]['level']]['parent_id']   = $value['id'];
            $level_user[$uida[$value['id']]['level']]['level']       = $uida[$value['id']]['level'];
        }
        return $level_user;
    }

    /**
     * 查询伞下用户
     * @param array $agent 代理级别
     * @param int $children 用户ID
     * @return void
     */
    public static function pyramid($uid){
        $level = self::where(['parent_id' => $uid,'level' => 1])->select();
        $level_user = [];
        $i = 0;
        foreach ($level as $value) {
            $level_user[$i]['user_id']     = $value['user_id'];
            $level_user[$i]['parent_id']   = $value['parent_id'];
            $level_user[$i]['level']       = $value['level'];
            $level_user[$i]['invite_code'] = $value->User['invite_code'];
            $level_user[$i]['nickname']    = $value->User['nickname'];
            ++$i;
        }  
        if(empty($level_user)){
            return $level_user;
        }
        $data = [];
        foreach ($level_user as $key => $value) {
            $data[$key]['name']     = $value['nickname'];
            $data[$key]['title']    = $value['invite_code'];
            $data[$key]['id']       = $value['user_id'];
            $data[$key]['isParent'] = true;
        }
        return $data;
    }
}