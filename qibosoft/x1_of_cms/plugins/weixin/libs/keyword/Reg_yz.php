<?php
namespace plugins\weixin\libs\keyword;

use plugins\weixin\index\Api;

class Reg_yz extends Api
{
    public function run(){
		$this->reply_keyword();
    }
    
    protected function reply_keyword(){
        $content = $this->From_content;
        if(preg_match("/^验证码$/",$content)){
            $num = rand(100000,999999);
            cache('weixin_yznum_'.$num,''.$this->user_appId,600);
			echo $this->give_text("你的验证码是：$num");
            exit;
        }
    }
    
}