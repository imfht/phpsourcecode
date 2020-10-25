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

namespace app\admin\logic;

/**
 * 登录逻辑
 */
class Login extends AdminBase
{
    
    /**
     * 登录处理
     */
    public function loginHandle($username = '', $password = '', $verify = '')
    {
        
        $member = $this->validateMember->checkLoginData(compact('username','password','verify'));
        
        if (false === $member) : return [RESULT_ERROR, $this->validateMember->getError()]; endif;
            
        $this->logicMember->setMemberValue(['id' => $member['id']], TIME_UT_NAME, TIME_NOW);

        $auth = ['member_id' => $member['id'], TIME_UT_NAME => TIME_NOW];

        session('member_info', $member);
        session('member_auth', $auth);
        session('member_auth_sign', data_auth_sign($auth));

        action_log('登录', '登录操作，username：'. $username);

        return [RESULT_SUCCESS, '登录成功', url('index/index')];
    }
    
    /**
     * 注销当前用户
     */
    public function logout()
    {
        
        clear_login_session();
        
        return [RESULT_SUCCESS, '注销成功', url('login/login')];
    }
    
    /**
     * 清理缓存
     */
    public function clearCache()
    {
        
        \think\Cache::clear();
        
        return [RESULT_SUCCESS, '清理成功', url('index/index')];
    }
}
