<?php
/**
 * Created by PhpStorm.
 * User: zero
 * Date: 15-5-20
 * Time: 下午7:47
 */
namespace App\Http\Controllers;

use App\RouteTag;

class RouteTagController extends Controller
{
    /**
     * 获取路线的预设的所有标签列表
     *
     * 返回数据：
     * [
     *     {
     *         "name": "路线标签的名称，外部显示使用"
     *         "label": "路线标签的内部标记，内部使用"
     *     }
     *     ......
     *     // 注意是个数组
     * ]
     *
     *
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getTag()
    {
        return response()->json(RouteTag::all()->toArray(), 200);
    }
}