<?php
/**
 * Created by PhpStorm.
 * User: dust2
 * Date: 14-12-22
 * Time: 下午8:40
 */

namespace Libraries;

use Input;
use Mews\Purifier\Purifier;

class MarkDownPurifier
{
    /**
     * 自定义过滤器方法,将mews/purifier简单封装,执行过滤操作
     * @param array $inputField
     */
    public static function purify(array $inputField)
    {
        $input = Input::all();

        foreach ($inputField as $field) {
            if (Input::has($field)) {
                $input[$field] = Purifier::clean($input[$field], 'TeamMindMap');
            }
        }

        //重置Input::all()为过滤后的值
        Input::replace($input);
    }

}