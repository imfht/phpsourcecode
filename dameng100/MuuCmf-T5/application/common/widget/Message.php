<?php
namespace app\common\widget;

use think\Controller;

//用户消息
class Message extends Controller{

    public function render()
    {
        return $this->fetch('common@widget/message');
    }

} 