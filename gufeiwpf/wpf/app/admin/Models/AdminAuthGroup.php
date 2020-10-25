<?php
namespace Wpf\App\Admin\Models;
class AdminAuthGroup extends \Wpf\App\Admin\Common\Models\CommonModel{
    
    const TYPE_ADMIN                = 1;                   // 管理员用户组类型标识
    
    public function initialize(){
        parent::initialize();
        
        $this->hasMany("id","Wpf\App\Admin\Models\AdminAuthGroupAccess","group_id");
        
        $this->addBehavior(new \Phalcon\Mvc\Model\Behavior\SoftDelete(
            array(
                'field' => 'status',
                'value' => -1
            )
        ));
    }
    
    public function onConstruct(){
        parent::onConstruct();
    }
    
    public function validation(){

        $this->validate(new \Phalcon\Mvc\Model\Validator\Uniqueness(
            array(
                "field"   => "title",
                "message" => "用户组重复"
            )
        ));
        
        $this->validate(new \Phalcon\Mvc\Model\Validator\PresenceOf(
            array(
                "field"   => "title",
                "message" => "用户组必须填写"
            )
        ));


        return $this->validationHasFailed() != true;
    }
    
    
    /**
     * 把用户添加到用户组,支持批量添加用户到用户组
     * @author 吴佳恒
     * 
     * 示例: 把uid=1的用户添加到group_id为1,2的组 `AuthGroupModel->addToGroup(1,'1,2');`
     */
    public function addToGroup($uid,$gid){
        $uid = is_array($uid)?implode(',',$uid):trim($uid,',');
        $gid = is_array($gid)?$gid:explode( ',',trim($gid,',') );

        //$Access = M(self::AUTH_GROUP_ACCESS);
        
        $Access = new \Wpf\App\Admin\Models\AdminAuthGroupAccess();
        if( isset($_REQUEST['batch']) ){
            //为单个用户批量添加用户组时,先删除旧数据
            
            $dellist = $Access->find("uid in ({$uid})");
            $del = $dellist->delete();
        }

        $uid_arr = explode(',',$uid);        
		$uid_arr = array_diff($uid_arr,$this->getDI()->get("config")->ADMIN_ADMINISTRATOR->toArray());
        $add = array();
        if( $del!==false ){
            $AdminMemberModel = new \Wpf\App\Admin\Models\AdminMember();
            foreach ($uid_arr as $u){
                foreach ($gid as $g){
                    if( is_numeric($u) && is_numeric($g) ){
                        
                        if(! $this->getInfo($g)){
                            return "用户组{$g}不存在";
                        }
                        if(! $AdminMemberModel->getInfo($u)){
                            return "用户{$u}不存在";
                        }
                        
                        $add = array('group_id'=>$g,'uid'=>$u);
                        $model = new \Wpf\App\Admin\Models\AdminAuthGroupAccess();
                        $model->save($add);
                    }
                }
            }
        }
        
        return true;
    }
    
}