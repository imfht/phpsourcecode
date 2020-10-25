<?php
/**----------------------------------------------------------------------
 * OpenCenter V3
 * Copyright 2014-2018 http://www.ocenter.cn All rights reserved.
 * ----------------------------------------------------------------------
 * Author: wdx(wdx@ourstu.com)
 * Date: 2018/9/25
 * Time: 14:52
 * ----------------------------------------------------------------------
 */
namespace app\admin\model;

use think\Model;

class AdminLog extends Model
{
    protected $table = ADMIN . 'log';
    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';
    // 定义时间戳字段名
    protected $createTime = 'create_time';

    //自定义日志标题
    protected static $title = '';
    //自定义日志内容
    protected static $content = '';

    public static function setTitle($title)
    {
        self::$title = $title;
    }

    public static function setContent($content)
    {
        self::$content = $content;
    }

    public static function record()
    {
        $aid = get_aid();
        $username = model('admin')->where('id', $aid)->value('username');
        $module = request()->module();
        $action = request()->url();
        $param = request()->param() ? json_encode(request()->param()) : '';
        $title = self::$title;
        $content = self::$content;
        self::create([
            'admin_id'  => $aid,
            'username'  => $username,
            'module'    => $module,
            'action'    => $action,
            'param'     => $param,
            'title'     => $title,
            'content'   => $content,
            'ip'        => request()->ip(1),
            'user_agent' => request()->server('HTTP_USER_AGENT')
        ]);
    }
}