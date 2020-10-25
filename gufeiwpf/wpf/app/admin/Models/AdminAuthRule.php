<?php
namespace Wpf\App\Admin\Models;
class AdminAuthRule extends \Wpf\App\Admin\Common\Models\CommonModel{
    
    const RULE_URL = 1;
    const RULE_MAIN = 2;
    
    public function initialize(){
        parent::initialize();
    }
    
    public function onConstruct(){
        parent::onConstruct();
    }
}