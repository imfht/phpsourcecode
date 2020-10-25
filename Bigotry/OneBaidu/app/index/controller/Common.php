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

namespace app\index\controller;

/**
 * 公共控制器
 */
class Common extends IndexBase
{
    
    public function page($current_page = 0, $last_page = 0, $offset = 3, $page_number = 7)
    {
        
        $content = get_page_html($current_page, $last_page, $offset, $page_number);
        
        return throw_response_exception(['content' => $content]);
    }
}
