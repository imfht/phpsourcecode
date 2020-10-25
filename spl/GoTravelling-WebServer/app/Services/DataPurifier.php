<?php
/**
 * Created by PhpStorm.
 * User: zero
 * Date: 15-6-4
 * Time: 上午10:11
 */
namespace App\Services;

use Illuminate\Http\Request;

class DataPurifier
{
    /**
     * 提供给资源控制器的过滤方法，根据请求方法的类型来选择过滤的字段
     *
     * @param Request $request 请求对象
     * @param array $purifyFields 关联数组，key为请求方法，value为需要过滤的字段
     */
    public static function purifyForRest(Request $request, array $purifyFields)
    {
        $requestMethod = $request->getMethod();
        if ( array_key_exists($requestMethod, $purifyFields) ) {
            static::purify($purifyFields[ $requestMethod ]);
        }
    }

    /**
     * 对第三方插件 mewebstudio/Purifier 的简单封装，针对输入域的字段进行过滤
     * @param array $inputField 需过滤的输入域字段列表 
     */
    public static function purify($inputField)
    {
        $input = \Input::all();

        foreach ($inputField as $field) {
            if ( \Input::has($field) ) {
                $input[ $field ] = clean($input[$field], 'gt'); // 使用自定义的 gt 过滤配置，文件在 config/purifier.php
            }
        }

        // 使用过滤后的输入域数据替代原有的数据
        \Input::replace($input);
    }
}