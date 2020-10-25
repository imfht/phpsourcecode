<?php
// +---------------------------------------------------------------------+
// | OneBase    | [ WE CAN DO IT JUST THINK ]                            |
// +---------------------------------------------------------------------+
// | Licensed   | http://www.apache.org/licenses/LICENSE-2.0 )           |
// +---------------------------------------------------------------------+
// | Author     | Bigotry <3162875@qq.com>                               |
// +---------------------------------------------------------------------+
// | Repository | https://gitee.com/Bigotry/OneBase                      |
// +---------------------------------------------------------------------+

use app\admin\logic\Log as LogicLog;
use think\Db;

/**
 * 记录行为日志
 */
function action_log($name = '', $describe = '')
{

    (new LogicLog())->logAdd($name, $describe);
}

/**
 * 清除登录 session
 */
function clear_login_session()
{
    
    session('member_info',      null);
    session('member_auth',      null);
    session('member_auth_sign', null);
}

/**
 * 检查session_id
 */
function check_session_id($member_id = 0)
{
    
    $session_id = Db::name('member')->where(['id' => $member_id])->value('session_id');

    if ($session_id == session_id()) {
        
        return true;
    }
    
    return false;
}

/**
 * 获取聊天内容
 */
function get_chat_contents()
{
    
    $file_path = ROOT_PATH . 'runtime' . DS . 'log' . DS . 'chat' . DS . date("Ym").'.txt';

    if (file_exists($file_path)) {

        $ob_chat_contents = file_get_contents($file_path);

        $ob_chat_contents_arr = array_filter(str2arr($ob_chat_contents, PHP_EOL));

        foreach ($ob_chat_contents_arr as &$v)
        {
            $v = json_decode($v, true);

            $v['msg'] = chat_contents_replace($v['msg']);
        }

    } else {

        $ob_chat_contents_arr = [];
    }

    return $ob_chat_contents_arr;
}

/**
 * 聊天内容替换
 */
function chat_contents_replace($str)
{

    $str = str_replace ( ">", '<；', $str );    
    $str = str_replace ( ">", '>；', $str );
    $str = str_replace ( "\n", '>；br/>；', $str );  
    $str = preg_replace ( "[\[em_([0-9]*)\]]", "<img src=\"/static/module/common/qqface/arclist/$1.gif\" />", $str );
    $str = preg_replace ( '/([[\s\S]*?])/i', '<img class="chat_imgs" src="$1" />', $str );
    $str = sr(sr($str, '['), ']');
    
    return $str;
}