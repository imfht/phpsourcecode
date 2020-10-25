<?php
/**
 * Created by PhpStorm.
 * User: zero
 * Date: 15-5-3
 * Time: 下午6:08
 */
namespace App\Http\Controllers;

use App\Route;
use App\Services\DataPurifier;
use App\Sight;
use Helper\ResponseHelper;
use Helper\RouteDailyHelper;
use Illuminate\Http\Request;

class RouteDailyController extends Controller
{
    use RouteDailyHelper;

    public function __construct(Request $request)
    {
        DataPurifier::purifyForRest($request, $this->purifyFields);
    }

    /**
     * 查看某一日程信息
     *
     * 返回数据：
     * {
     *     "_id": "日程的id"
     *     "remark": "日程简介"
     *     "date": "创建时间"
     *     {
     *         "sec": "unix时间戳"
     *         "usec": "暂无用处"
     *     }
     *     "sights": "当天的景点列表"
     *     [
     *         {
     *             "sight_id": "景点的id"
     *             "name": "景点名称"
     *             "loc": "geo坐标结构"
     *             {
     *                 "type": "Point"
     *                 "coordinates": [ "经度", "纬度" ]
     *              }
     *         }
     *         ...... 
     *         // 注意是个数组
     *     ],
     * }
     * 数据不存在：404
     * {
     *     "error": "相关的错误信息"
     * }
     *
     *
     * @param $routeId
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function show($routeId, $id)
    {
        $dailyData = Route::getDailyData($routeId, $id);
        if ( is_null($dailyData) ) {
            return response()->json('找不到相应的数据', 404);
        } else {
            return response()->json($dailyData, 200);
        }
    }

    /**
     * 新建日程
     *
     * 请求数据：
     * {
     *     "remark": "日程描述"【必选】
     *     "sights": "当天的景点列表"【可选】
     *     [
     *         {
     *             "sight_id": "景点的id"【可选】
     *             "name": "景点名称"【必选】
     *             "loc": "geo坐标结构"【必选】
     *            {
     *                "type": "Point"
     *                "coordinates": [ "经度", "纬度" ]
     *            }
     *         }
     *     ],
     * }
     * 返回数据：
     * 成功：201
     * {
     *     "_id": "新建日程的id"
     *     "remark": "日程简介"
     *     "date": "创建时间"
     *     {
     *         "sec": "unix时间戳"
     *         "usec": "暂无用处"
     *     }
     *     "sights": "日程的景点列表"
     *     [
     *         {
     *             "sight_id": "景点的id"
     *             "name": "景点的名称"
     *             "loc": "geo坐标结构"
     *             {
     *                 "type": "Point"
     *                 "coordinates": [ "经度", "纬度" ]
     *             }
     *         }
     *         ......
     *         // 注意是个数组
     *     ]
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
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function store($routeId)
    {
        $postData = \Input::only(['remark', 'sights']);

        $validate = $this->getStoreValidator($postData);
        if ($validate->fails()) {
            return ResponseHelper::responseErrorMessageOnJson($postData, $validate->getMessageBag());
        }

        $dailyData = $this->buildDailyData($postData, $routeId);
        $effectRow = $this->storeDaily($dailyData, $routeId);
        if ( 0 === $effectRow ) {
            return response()->json(['error' => '找不到相应的数据'], 404);
        } else {
            $respData = last(Route::findOrFail($routeId)['daily']);
            return response()->json($respData, 201);
        }
    }

    /**
     * 更新日程信息
     *
     * 请求数据：
     * {
     *     "remark": "日程描述"【可选】
     *     "sights": "当天景点列表"【可选】
     *     [
     *         {
     *             "sight_id": "景点的id"【可选】
     *             "name": "景点名称"【必选】
     *             "loc": "geo坐标结构"
     *             {
     *                 "type": "Point"
     *                 "coordinates": [ "经度", "纬度" ]
     *             }
     *         }
     *         ......
     *         // 注意是个数组
     * }
     * 请求数据描述：
     *     sights：提交更新的景点列表会覆盖原有的日程景点列表
     * 返回数据：
     * 成功：200
     * {
     *     "_id": "日程的id"
     *     "remark": "日程描述"
     *     "sights": "当天景点列表"
     *     [
     *         {
     *             "sight_id": "景点的id"
     *             "name": "景点名称"
     *             "loc": "geo坐标结构"
     *             {
     *                 "type": "Point"
     *                 "coordinates": [ "经度", "纬度" ]
     *             }
     *         }
     *         ......
     *         // 注意是个数组
     *     ]
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
        // 检查该日程是否存在，以及当前用户是不是所属路线的创建者
        $count = Route::where('_id', $routeId)->where('creator_id', \Auth::user()['_id'])
            ->where('daily._id', intval($id))->count();
        if ( 0 === $count ) {
            return response()->json(['error' => '找不到相应的数据'], 404);
        }

        $putData = \Input::only(['remark', 'sights', 'accommodation']);

        $validate = $this->getUpdateValidator($putData);
        if ($validate->fails()) {
            return ResponseHelper::responseErrorMessageOnJson($putData, $validate->getMessageBag());
        }

        $effectRow = $this->updateDaily($routeId, $id, $putData);
        if ( 0 === $effectRow ) {
            return response()->json(['error' => '更新日程失败'], 404);
        }
        // 针对日程的景点列表做更新操作
        $result = $this->updateSights($routeId, $id, $putData['sights']);
        if ( isset($result['error']) ) {
            return response()->json(['error' => '更新日程景点失败'], 404);
        } else {
            $respData = Route::getDailyData($routeId, $id);
            return response()->json($respData, 200);
        }
    }

    /**
     * 删除日程
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
            ->where('daily._id', intval($id))
            ->pull('daily', ['_id' => intval($id)]);

        if ( 0 === $effectRow ) {
            return response()->json(['error' => '找不到相应的数据'], 404);
        } else {
            Sight::where('routes', $routeId)->pull('routes', $routeId);
            return response()->json('', 200);
        }
    }

    private $purifyFields = [
        'POST' => ['remark'],
        'PUT' => ['remark'],
    ];
}

