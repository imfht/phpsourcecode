<?php
namespace Wpf\App\Admin\Models;
class AdminMenu extends \Wpf\App\Admin\Common\Models\CommonModel{
    
    public function initialize(){
        parent::initialize();
    }
    
    public function onConstruct(){
        parent::onConstruct();
    }
    
    public function validation(){

        $this->validate(new \Phalcon\Mvc\Model\Validator\PresenceOf(
            array(
                "field"  => "title",
                "message" => "标题不能为空"
            )            
        ));
        
        $this->validate(new \Phalcon\Mvc\Model\Validator\PresenceOf(
            array(
                "field"  => "url",
                "message" => "菜单url不能为空"
            )            
        ));

        return $this->validationHasFailed() != true;
    }
    
    public function cascadeDel($id){
        if((!$id) || (! $menu = $this->getInfo($id))){
            return false;
        }
        
        if($children = $this->find("pid = {$id}")->toArray()){
            foreach($children as $sub){
                $this->cascadeDel($sub['id']);
            }
        }
        
        if($menu->delete()){
            return true;
        }else{
            return false;
        }
        
        
    }
    
    //获取树的根到子节点的路径
	public function getPath($id = 0){
		$path = array();
        if(! $id){
            return array();
        }
        $nav = $this->findFirst(array(
            "id={$id}",
            "columns" => "id,pid,title"
        ));
        if($nav){
            $nav = $nav->toArray();
        }
        
		//$nav = $this->where("id={$id}")->field('id,pid,title')->find();
        
		$path[] = $nav;
		if($nav['pid'] >1){
			$path = array_merge($this->getPath($nav['pid']),$path);
		}
		return $path;
	}
}