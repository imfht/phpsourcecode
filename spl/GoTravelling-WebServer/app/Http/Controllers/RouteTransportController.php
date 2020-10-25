<?php
/**
 * Created by PhpStorm.
 * User: zero
 * Date: 15-5-7
 * Time: 下午8:17
 */
namespace App\Http\Controllers;

use App\Route;
use App\Services\DataPurifier;
use Helper\ResponseHelper;
use Helper\RouteTransportHelper;
use Illuminate\Http\Request;

class RouteTransportController extends Controller
{
    use RouteTransportHelper;

    public function __construct(Request $request)
    {
        DataPurifier::purifyForRest($request, $this->purifyFields);
    }

    /**
     * 查看某一交通方式的信息
     *
     * 返回数据：
     * 成功：200
     * {
     *     "from_name": "出发的景点的名称"
     *     "from_sight_id": "出发的景点的id"
     *     "from_loc": "geo坐标结构"
     *     {
     *         "type": "Point"
     *         "coordinates": [ "经度", "纬度" ]
     *     }
     *     "to_name": "目的景点的名称"
     *     "to_sight_id": "目的景点的id"
     *     "to_loc": "geo坐标结构"
     *     {
     *          "type": "Point"
     *          "coordinates": [ "经度", "纬度" ]
     *     }
     *     "description": "交通方式的描述"
     *    {
     *         "type": "交通方式的类型"
     *         "policy": "交通方式的策略"
     *         [
     *             {
     *                  "label": "策略的标签，中文，外部显示"
     *                  "name": "策略的名称，英文，内部使用"
     *             }
     *             ......
     *             // 注意是个数组
     *         ]
     *     }
     *     "prize": "价格"
     *     "consuming": "所需要的时间"
     * }
     * 验证失败：400
     * {
     *     "error": "相关的错误信息"
     *     "data": "请求提交的数据"
     * }
     * 数据不存在：404
     * {
     *     "error": "相关的错误信息"
     * }
     *
     * @param $routeId
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function show($routeId, $id)
    {
        $transportData = Route::getTransportData($routeId, $id);
        if ( is_null($transportData) ) {
            return response()->json(['error' => '找不到相应的数据'], 404);
        } else {
            return response()->json($transportData, 200);
        }
    }

    /**
     * 新建交通方式
     *
     * 请求数据：
     * {
     *     "from_name": "出发的景点的名称"【必选】
     *     "from_sight_id": "出发的景点的id"【可选】
     *     "from_loc": "geo坐标结构"【必选】
     *     {
     *         "type": "Point"
     *         "coordinates": [ "经度", "纬度" ]
     *     }
     *     "to_name": "目的景点的名称"【必选】
     *     "to_sight_id": "目的景点的id"【可选】
     *      "to_loc": "geo坐标结构"【必选】
     *      {
     *          "type": "Point"
     *          "coordinates": [ "经度", "纬度" ]
     *      }
     *      "description": "交通方式的描述"【必选】
     *     {
     *         "type": "交通方式的类型" 【必选】
     *         "policy": [ '交通方式的策略' ] 【可选】
     *     }
     *      "prize": "价格"【必选】
     *      "consuming": "所需要的时间"【必选】
     * }
     * 请求数据描述：
     * 1.交通方式必须为：drive（驾车）、bus（公交/地铁）、walk（步行）
     * 2.交通方式的策略对应如下：
     *     drive: least_block（躲避拥堵）、least_distance（最短距离）、least_cost（最小费用）、least_time（时间优先）
     *     bus: avoid_subway（不含地铁）、least_exchange（最少换乘）、least_walk（最小步行距离）、least_time（时间优先）
     *     walk: 此方式并无具体策略
     * 3.交通方式的策略可有0个或多个
     *
     * 返回数据：
     * 成功：201
     * {
     *     "_id": "新建交通方式的id"
     *     "from_name": "出发的景点的名称"
     *     "from_sight_id": "出发的景点的id"
     *     "from_loc": "geo坐标结构"
     *     {
     *         "type": "Point"
     *         "coordinates": [ "经度", "纬度" ]
     *     }
     *     "to_name": "目的景点的名称"
     *     "to_sight_id": "目的景点的id"
     *      "to_loc": "geo坐标结构"
     *      {
     *          "type": "Point"
     *          "coordinates": [ "经度", "纬度" ]
     *      }
     *      "description": "交通方式的描述"
     *    {
     *         "type": "交通方式的类型"
     *         "policy": "交通方式的策略"
     *         [
     *             {
     *                  "label": "策略的标签，中文，外部显示"
     *                  "name": "策略的名称，英文，内部使用"
     *             }
     *             ......
     *             // 注意是个数组
     *         ]
     *     }
     *      "prize": "价格"
     *      "consuming": "所需要的时间"
     * }
     * 验证失败：400
     * {
     *    "error": "相关的错误信息"
     *    "data": "请求提交的数据"
     * }
     * 数据不存在：404
     * {
     *     "error": "相关的错误信息"
     * }
     *
     *
     * @param $routeId
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function store($routeId)
    {
        $postData = \Input::only(['from_name', 'from_sight_id', 'from_loc', 'to_name', 'to_sight_id', 'to_loc',
            'description', 'prize', 'consuming']);
        $validate = $this->getStoreValidator($postData);
        if ( $validate->fails() ) {
            return ResponseHelper::responseErrorMessageOnJson($postData, $validate->getMessageBag());
        }

        $effectRow = $this->storeTransport($routeId, $postData);
        if ( 0 === $effectRow ) {
            return response()->json(['error' => '找不到相应的数据'], 404);
        } else {
            $newTransport = Route::findOrFail($routeId)['transportation'];
            $respData = last($newTransport);
            return response()->json($respData, 201);
        }
    }

    /**
     * 更新交通方式的信息
     *
     * 请求数据：
     * {
     *      "from_name": "出发的景点的名称"【可选】
     *      "from_sight_id": "出发的景点的id"【可选】
     *      "from_loc": "geo坐标结构"【可选】
     *      {
     *          "type": "Point"
     *          "coordinates": [ "经度", "纬度" ]          
     *      }
     *      "to_name": "目的景点的名称"【可选】
     *      "to_sight_id": "目的景点的名称"【可选】
     *      "to_loc": "geo坐标结构"【可选】
     *      {
     *           "type": "Point"
     *           "coordinates": [ "经度", "纬度" ]
     *      }
     *      "description": "交通方式的描述"【可选】
     *      {
     *           "type": "交通方式的类型" 【必选】
     *           "policy": [ '交通方式的策略' ] 【可选】
     *      }
     *      "prize": "价格"【可选】
     *      "consuming": "所需要的时间"【可选】
     * }
     * 请求数据描述：
     *     1. from_name和from_loc必须同时存在（若需要提交的话），to_name和to_loc也一样
     * 返回数据：   
     * 成功：200
     * {
     *      "from_name": "出发的景点的名称"
     *      "from_sight_id": "出发的景点的id"
     *      "from_loc": "geo坐标结构"
     *      {
     *          "type": "Point"
     *          "coordinates": [ "经度", "纬度" ]          
     *      }
     *      "to_name": "目的景点的名称"
     *      "to_sight_id": "目的景点的名称"
     *      "to_loc": "geo坐标结构"
     *      {
     *           "type": "Point"
     *           "coordinates": [ "经度", "纬度" ]
     *      }
     *      "description": "交通方式的描述"
     *    {
     *         "type": "交通方式的类型"
     *         "policy": "交通方式的策略"
     *         [
     *             {
     *                  "label": "策略的标签，中文，外部显示"
     *                  "name": "策略的名称，英文，内部使用"
     *             }
     *             ......
     *             // 注意是个数组
     *         ]
     *     }
     *      "prize": "价格"
     *      "consuming": "所需要的时间"
     * }
     * 验证失败：400
     * {
     *     "error": "相关的错误信息"
     *     "data": "请求提交的数据"
     * }
     * 数据不存在：404
     * {
     *     "error": "相关的错误信息"
     * }
     *
     * @param $routeId
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function update($routeId, $id)
    {
        // 检查该交通方式是否存在，以及当前用户是不是所属路线的创建者
        $count = Route::where('_id', $routeId)->where('creator_id', \Auth::user()['_id'])
            ->where('transportation._id', intval($id))
            ->count();
        if ( 0 === $count ) {
            return response()->json(['error' => '找不到相应的数据'], 404);
        }

        $putData = \Input::only(['from_name', 'from_sight_id', 'from_loc', 'to_name', 'to_sight_id', 'to_loc',
            'description', 'prize', 'consuming']);
        $validate = $this->getUpdateValidator($putData);
        if ( $validate->fails() ) {
            return ResponseHelper::responseErrorMessageOnJson($putData, $validate->getMessageBag());
        }

        $effectRow = $this->updateTransport($routeId, $id, $putData);
        if ( 0 === $effectRow ) {
            return response()->json(['error' => '更新交通方式失败'], 404);
        } else {
            $respData = Route::getTransportData($routeId, $id);
            return response()->json($respData, 200);
        }
    }

    /**
     * 删除交通方式
     *
     * 返回数据：
     * 成功：200
     * 数据不存在：404
     * {
     *     "error": "相关的错误信息"
     * }
     *
     * @param $routeId
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function destroy($routeId, $id)
    {
        $effectRow = \DB::collection('routes')->where('_id', $routeId)
            ->where('creator_id', \Auth::user()['_id'])
            ->where('transportation._id', intval($id))
            ->pull('transportation', ['_id' => intval($id)]);

        if ( 0 === $effectRow ) {
            return response()->json(['error' => '找不到相应的数据'], 404);
        } else {
            return response()->json('', 200);
        }
    }

    private $purifyFields = [
        'POST' => ['from_name', 'to_name', 'description', 'prize', 'consuming'],
        'PUT' => ['from_name', 'to_name', 'description', 'prize', 'consuming']
    ];
}