<?php
class CommonAction extends Action {
    Public function _initialize()
    {
        
        if(!isset($_SESSION[C('USER_AUTH_KEY')])){
            $this->redirect('Login/index');
        }
        
        $notAuth=in_array(MODULE_NAME,explode(',',C('NOT_AUTH_MODULE')))||in_array(ACTION_NAME,explode(',',C('NOT_AUTH_ACTION')));
            
        if(C('USER_AUTH_ON')&&!$notAuth)
        {
            import('ORG.Util.RBAC');
            RBAC::AccessDecision()||$this->error('没有权限');
            
        }
        
    }
}
?>