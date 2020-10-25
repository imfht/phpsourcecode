<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Guard;
use Session,Cache;

class Authenticate
{
    /**
     * The Guard implementation.
     *
     * @var Guard
     */
    protected $auth;

    /**
     * Create a new middleware instance.
     *
     * @param  Guard $auth
     * @return void
     */
    public function __construct(Guard $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {  
    	$sys_id = Session::get('sys_id', '');
    	if (empty($sys_id)) {
    		return \Redirect::to('login/out'); 
    	} else {
            $bool = $this->check($request);
            if($bool){
    		    return $next($request);
            }else{
                $url=url('admin/system_role/updateCache');
                $content = <<<EOF
<div id="content" style="text-align:center;font-size:20px;margin-top:100px;"></div>
<script type="text/javascript" src="/lib/jquery/1.9.1/jquery.min.js"></script>
<script type="text/javascript">
    //获取当前页面的url
    if(window.location.href.indexOf('admin/welcome/index')>0){
        $('#content').html('欢迎登录智慧水站管理后台');
    }else{
        $('#content').html('没有操作权限,请点击按钮尝试刷新权限：<button id="refreshRole">刷新权限</button>');
    };
	$("#refreshRole").on("click",function(){
		$.ajax({
            url:'$url',
            type:"get",
            async:false,
            success:function(msg){
                window.location.reload();
            }
        });
	});
</script>
EOF;
                $status = 401;
                $value = 'text/html;charset=utf-8';
                return response($content,$status)->header('Content-Type', $value);
            }
    	}
    }
    //检测$bool false 弱验证  true 强验证
    private function check($request,$bool = true){
        $grade = \Session::get('grade',0);

        //将===改成==
        if($grade == 1){ //超级管理员拥有所有权限
            return true;
        }
        $role_id = \Session::get('system_role_id','');
        $menu = Cache::get('system_menu_role_'.$role_id,'');
        if(empty($menu)){
            return false;
        }
        $path = $request->path();
        if(empty($path) || $path == '/'){
            return true;
        }
        $path_arr = explode('/',$path);
        foreach($menu as $m){
            if(strpos($m['action_url'],'/') === 0){
                $m['action_url'] = substr($m['action_url'],1);
            }
            $m_arr = explode('/',$m['action_url']);
            if($bool && $m['action_url'] === $path){
                return true;
            }else if(!$bool && $m_arr[0] == $path_arr[0] && $m_arr[1] == $path_arr[1]){
                return true;
            }
        }
        return false;

    }
}
