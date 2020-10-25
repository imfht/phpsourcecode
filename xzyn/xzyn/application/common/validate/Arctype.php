<?php
namespace app\common\validate;
//文章分类验证器
use think\Validate;

class Arctype extends Validate
{
    protected $rule = [
        'id' => 'require|number',
        'pid|上级分类' => 'require|integer',
        'typename|分类名称' => 'require',
        'mid|分类模型' => 'require|integer',
        'dirs|分类目录' => 'require|alphaDash',
        'target' => 'require',
        'templist|列表页模板' => 'require|alphaDash',
        'temparticle|内容页模板' => 'require|alphaDash',
        'pagesize|分页条数' => 'require|integer|>=:1',
        'sorts|排序' => 'require|integer|>=:1',
        'status|状态' => 'require|in:0,1',
        'is_release|允许发布' => 'require|in:0,1',
        'is_daohang|导航显示' => 'require|in:0,1',
    ];

    protected $scene = [
        'add'   => ['pid', 'typename', 'mid', 'dirs', 'target',  'pagesize', 'sorts', 'status','is_release','is_daohang'],
        'edit'  => ['id','pid', 'typename', 'mid', 'dirs', 'target',  'pagesize', 'sorts', 'status','is_release','is_daohang'],
        'status' => ['status','id'],
        'typename' => ['typename','id'],
        'dirs' => ['dirs','id'],
        'is_release' => ['is_release','id'],
        'is_daohang' => ['is_daohang','id'],
    ];
}