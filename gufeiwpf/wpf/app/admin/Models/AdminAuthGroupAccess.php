<?php
namespace Wpf\App\Admin\Models;
class AdminAuthGroupAccess extends \Wpf\App\Admin\Common\Models\CommonModel{
    
    public function initialize(){
        parent::initialize();
        
        $this->belongsTo("uid", "Wpf\App\Admin\Models\AdminMember", "id");
        
        $this->belongsTo("group_id","Wpf\App\Admin\Models\AdminAuthGroup","id");
    }
    
    public function onConstruct(){
        parent::onConstruct();
    }
    
    
    /**
     * 得到用户帐号组权限信息
     * AdminAuthGroupAccess::getAccessGroupInfo()
     * 
     * @param mixed $uid
     * @return
     */
    public function getAccessGroupInfo($uid){
        
        $return = array();
        
        $AdminAuthGroup = "Wpf\App\Admin\Models\AdminAuthGroup";
        
        if($access = $this->find("uid='$uid'")){
            foreach($access as $value){
                $array = array();
                $ac = $value->toArray();
                $array['uid'] = $ac['uid'];
                $array['group_id'] = $ac['group_id'];
                
                $gr = $value->$AdminAuthGroup->toArray();
                $array['title'] = $gr['title'];
                $array['rules'] = $gr['rules'];
                
                $return[] = $array;
                
            }
        }
        
        return $return;
        
        /*
        $user_groups = $this->getDI()->get("modelsManager")->createBuilder()
            ->addfrom($this->_config['AUTH_GROUP_ACCESS'],"a")
            ->join($this->_config['AUTH_GROUP'],"a.group_id=g.id","g")
            ->where("a.uid='$uid'")
            ->andwhere("g.status='1'")
            ->columns('uid,group_id,title,rules')
            ->getQuery()
            ->execute();
        
        $user_groups = $user_groups->toArray();
        
        return $user_groups;
        */
    }
}