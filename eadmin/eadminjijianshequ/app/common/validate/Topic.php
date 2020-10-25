<?php
// +----------------------------------------------------------------------
// | Author: Zaker <49007623@qq.com>
// +----------------------------------------------------------------------

namespace app\common\validate;

/**
 * 验证器
 */
class Topic extends ValidateBase
{

    // 验证规则
    protected $rule = [
        'title'   => 'require|checkTitle:6,32',
        'content' => 'require',


    ];

    // 验证提示
    protected $message = [
        'title.require'    => '标题不能为空',
        'title.checkTitle' => '标题长度为6-32个字符之间',
        'content.require'  => '内容必填',
    ];

    // 自定义验证规则
    protected function checkTitle($value, $rule, $data)
    {
        $strlen = mb_strlen($value, 'utf-8');

        $lenarr = explode(',', $rule);

        if ($strlen < $lenarr[0] || $strlen > $lenarr[1]) {

            return false;

        } else {

            return true;

        }


    }


    // 应用场景
    protected $scene = [
        'edit' => ['title', 'content'],
        'add'  => ['title', 'content'],
    ];
}
