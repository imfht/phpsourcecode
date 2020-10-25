<?php

namespace app\webhooks\controller;

/**
 * 钩子系统
 * GIT钩子通知此URL
 * 实现自动拉取功能
 */
class Index
{
    public function oschina()
    {
        $json = file_get_contents("php://input");
        file_put_contents(ROOT_PATH.'/runtime/git.oschina_notify_log.txt', date('Y-m-d H:i:s').PHP_EOL, FILE_APPEND);
        file_put_contents(ROOT_PATH.'/runtime/git.oschina_notify_log.txt', $json.PHP_EOL, FILE_APPEND);
        $arr = json_decode($json, true);
        if(!isset($_GET['xiaobaiDebug'])){
            if(!isset($arr['password']) || 'youwen2017' == $arr['password']){
                file_put_contents(ROOT_PATH.'/runtime/git.oschina_notify_log.txt', "password wrong".PHP_EOL, FILE_APPEND);
                exit('0');
            }
            if(!$this->check_sign()){
                file_put_contents(ROOT_PATH.'/runtime/git.oschina_notify_log.txt', "sign wrong".PHP_EOL, FILE_APPEND);
                exit();
            }
        }
        file_put_contents(ROOT_PATH.'/runtime/gitPullSwitch.txt', date('Y-m-d H:i:s').PHP_EOL, FILE_APPEND);
    }
    // 权限问题 导致不成功
    public function oschina_promiss()
    {
        $json = file_get_contents("php://input");
        $arr = json_decode($json, ture);

        if(!isset($arr['password']) || 'youwen2017' == $arr['password']){
            exit('0');
        }
        if(!$this->check_sign()){
            exit();
        }
        $ret = $this->exec_shell();
    }

    private function check_sign()
    {
        return true;
    }

    private function exec_shell()
    {
        return exec("cd /alidata/www/demo.bauth.cn/ && git pull");
    }

    /**
     * [check_function description]
     * @return [type] [description]
     * @author baiyouwen
     * @see http://www.cnblogs.com/gaohj/p/3267692.html
     */
    public function check_function()
    {
        $command= ['exec', 'system', 'shell_exec', 'passthru'];
        foreach ($command as $key => $value) {
            if(function_exists($value)){
                echo $value,'-allow',"<Br/>";
            }else{
                echo $value,'-not allow',"<Br/>";
            }
        }
    }

    /**
     * `` 和 shell_exec是一样的
     * @return [type] [description]
     * @author baiyouwen
     */
    public function haha()
    {
        echo `pwd`;
    }
}
