<?php
namespace app\common\validate;

use think\Validate;

class Music extends Validate
{
    protected $rule = [
        'id' => 'require|number',
        'name|歌名' => 'require',
        'author|歌手名' => 'require',
        'src|音乐地址' => 'require|url',
        'cover|封面图片' => 'require|url',
        'status|状态' => 'require|in:0,1',
    ];

    protected $scene = [
        'add'   => ['name', 'author', 'src', 'cover', 'status'],
        'edit'  => ['name', 'author', 'src', 'cover', 'status','id'],
        'status' => ['status','id'],
        'name' => ['name','id'],
        'author' => ['author','id'],
        'src' => ['src','id'],
        'cover' => ['cover','id'],
        'status' => ['status','id'],
        'orderby' => ['orderby','id'],
    ];
}