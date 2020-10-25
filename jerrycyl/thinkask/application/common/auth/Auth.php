<?php
/*
+--------------------------------------------------------------------------
|   thinkask [#开源系统#]
|   ========================================
|   http://www.thinkask.cn
|   ========================================
|   如果有兴趣可以加群{开发交流群} 485114585
|   ========================================
|   更改插件记得先备份，先备份，先备份，先备份
|   ========================================
+---------------------------------------------------------------------------
 */
namespace app\common\auth;
use think\Exception;

class Auth 
{
    public static function Auth($Auth = '')
    {
        if ($Auth == '') {
            // throw new Exception('未指定验证类型', 8001);
        } else {
            $Auth = ucfirst($Auth);
        }
        // show($Auth);
        $class = '\\app\\common\\auth\\'. $Auth;
        if (!class_exists($class)) {
            throw new Exception($Auth . '验证类型不存在', 8002);
        }
        return new $class;
    }

  
}
