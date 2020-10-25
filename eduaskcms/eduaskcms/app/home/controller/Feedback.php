<?php
namespace app\home\controller;

use app\common\controller\Home;

class Feedback extends Home
{
    public function initialize()
    {
        
        call_user_func(array('parent',__FUNCTION__)); 
    }
    
    
    
    public function show()
    {
        if ($this->request->isPost() && $this->Form->check_token()) {
            
            if(captcha_check(input('post.captcha'))){
                $this->Form->data[$this->m]['truename'] = trim(htmlspecialchars($this->Form->data[$this->m]['truename']));
                $this->Form->data[$this->m]['mobile']   = trim(htmlspecialchars($this->Form->data[$this->m]['mobile']));
                $this->Form->data[$this->m]['content'] = htmlspecialchars($this->Form->data[$this->m]['content']);
                $this->Form->data[$this->m]['title']    = /*$this->Form->data[$this->m]['title'] ? */trim(htmlspecialchars($this->Form->data[$this->m]['title']))/*:menu($this->args['menu_id'],'title')*/;
                $this->Form->data[$this->m]['user_id']  = $this->Auth->user('id');
                $this->Form->data[$this->m]['ip']       = $this->request->ip();
                $this->Form->data[$this->m]['menu_id']  = intval($this->args['menu_id']);
                
                $rslt  = $this->mdl->isUpdate(false)->save($this->Form->data[$this->m]);
                if ($rslt) {
                    return $this->message('success','恭喜你！留言成功！');
                } else {
                    $this->assign->error = $this->mdl->getError();
                }
            }else{
                $this->assign->error[] = '验证码填写错误';
            }
        }
        
        call_user_func(array('parent',__FUNCTION__)); 
    }
    
    public function view()
    {
        call_user_func(array('parent',__FUNCTION__)); 
    }
    
    
}
