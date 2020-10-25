<?php
// 应用 公共 前置行为的中间件

namespace app\http\middleware;

class Common
{
    public function handle($request, \Closure $next)
    {
		define('UID', cache('user_data')['id']);   //设置登陆用户ID常量
		define('H_NAME', request()->domain());	//获取当前域名,包含"http://"
		define('M_NAME', request()->module());	//当前模块名称
        define('C_NAME', request()->controller());	//当前控制器名称
		define('A_NAME', request()->action());	//当前操作名称
		define('M_C_A_NAME', strtolower(request()->module() . '/' . request()->controller() . '/' . request()->action()) );	//当前 模块 控制器 操作名称，转换成小写。
        define('REQ_TYPE', request()->method());	//当前请求类型

        // POST 提交 验证提交参数
		// if ( request()->method() == 'POST' ) {
		// 	$post_data = input('post.');
		// 	$class_name = request()->controller();	//当前控制器名称
		// 	$app_path = env('APP_PATH');	//应用目录
		// 	$class_path = $app_path.'common/validate/'. $class_name . '.php';
		// 	if( file_exists($class_path) ){		// 判断验证类文件是否存在
		// 		$validate = validate('app\common\validate\\'.$class_name);
		// 		$action_name = request()->action();	  // 当前操作名称
		// 		if (count($post_data) == 2){
		// 			if ( array_key_exists('id',$post_data) ) {
		// 				foreach ($post_data as $k =>$v){
		// 					if ( $k !== 'id' ) {
		// 						$fv = $k;
		// 					}
		// 				}
		// 				$is_validate = $validate->scene($fv)->check($post_data);
		// 			}
		// 		}else{
		// 			if ( $action_name == 'edit' ) {
		// 				$is_validate = $validate->scene('edit')->check($post_data);
		// 			}elseif( $action_name == 'create' ){
		// 				$is_validate = $validate->scene('add')->check($post_data);
		// 			}
		// 		}
		// 		if ( isset($is_validate) ) {
		// 			if (!$is_validate) {
		// 				return x_return(0,'',$validate->getError());
		// 			}
		// 		}

		// 	}
		// }



        return $next($request);
    }
}