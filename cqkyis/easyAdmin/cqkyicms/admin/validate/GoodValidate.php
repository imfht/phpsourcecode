<?php
/**
 * 重庆柯一网络有限公司 版权所有
 * 开发团队:柯一网络 柯一CMS项目组
 * 创建时间: 2018/5/14 15:06
 * 联系电话:023-52889123 QQ：563088080
 * 惟一官网：www.cqkyi.com
 */

namespace app\admin\validate;


use think\Validate;

class GoodValidate extends Validate
{

    protected $rule=[
        'good_name'  =>  'require',
        'mall_price'=>'require',
        'cate_id'=>'require',
        '__token__' => 'token',

    ];
    protected $message  =   [
        'good_name.require' => '商品名称不能为空',
        'mall_price.require' => '商品价格不能为空',
        'cate_id.require' => '商品分类不能为空',



    ];

}