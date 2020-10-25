<?php
namespace Wpf\App\Admin\Models;
class TestModel extends \Phalcon\Mvc\Collection{
    
    public function initialize(){
        //var_dump($this->_ctable);
        
        //$this->setSource($this->_ctable);
        //$this->setSource($this->getDI()->get("cache")->get("tablename"));
        
        //$this->keepSnapshots(false);
        parent::initialize();
    }
    
    public function onConstruct(){
        //$this->setSource($this->getDI()->get("cache")->get("tablename"));
        
        parent::onConstruct();
        
    }
    
    
    public function getSource(){
        if($this->getDI()->get("cache")->exists("tablename")){
            return $this->getDI()->get("cache")->get("tablename");
        }else{
            return "photo";
        }
        
    }
}