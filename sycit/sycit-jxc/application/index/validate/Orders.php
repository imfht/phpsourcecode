<?php
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.sycit.cn
// +----------------------------------------------------------------------
// | Author: Peter.Zhang  <hyzwd@outlook.com>
// +----------------------------------------------------------------------
// | Date:   2017/9/1
// +----------------------------------------------------------------------
// | Title:  Orders.php
// +----------------------------------------------------------------------
namespace app\index\validate;

use think\Validate;

class Orders extends Validate
{
    protected $rule = [
        'qiyename|企业名称' => 'require',
        'StrOrderOne|销售单号' => 'require|number',
        'kehumingcheng|收货人' => 'require',
        'lianxidianhua|联系电话' => 'require|number',
        'fahuowuliu|发货物流' => 'require',
        'shouhuodizhi|收货地址' => 'require',
        'xiaoshouriqi|销售日期' => 'require|dateFormat:Y-m-d',
        'fahuoriqi|发货日期' => 'require|dateFormat:Y-m-d',
        'OrderQuantity|订单数量' => 'require|elt:0',
        '__token__|数据'    =>  'require|token'
    ];

    protected $message = [
        'qiyename.require' => ':attribute不能为空',

        'StrOrderOne.require' => ':attribute不能为空',
        'StrOrderOne.number' => ':attribute格式不对',

        'kehumingcheng.require' => ':attribute不能为空',

        'lianxidianhua.require' => ':attribute不能为空',
        'lianxidianhua.number' => ':attribute格式不对',

        'xiaoshouriqi.require' => ':attribute不能为空',
        'xiaoshouriqi.dateFormat' => ':attribute格式不对',

        'fahuoriqi.require' => ':attribute不能为空',
        'fahuoriqi.dateFormat' => ':attribute格式不对',

        'OrderQuantity.require' => ':attribute不能为空',
        'OrderQuantity.elt' => ':attribute为零',

        '__token__.require' => ':attribute出错',
        '__token__.token' => ':attribute失效，重新刷新'
    ];

    protected $scene = [
        //'add'  => ['qiyename','StrOrderOne','kehumingcheng','lianxidianhua','xiaoshouriqi','fahuoriqi','__token__'],
        'add'  => ['StrOrderOne','xiaoshouriqi','fahuoriqi','__token__'],
        'edit' => ['StrOrderOne','fahuoriqi','__token__']
    ];
}