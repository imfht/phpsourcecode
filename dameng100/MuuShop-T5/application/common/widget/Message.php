<?php
namespace app\common\widget;

use think\Controller;

//用户消息
class Message extends Controller{

    public function render($data='')
    {
        if(!is_login()){
            return '';
        }
        return $this->fetch('common@widget/message');
    }

} 