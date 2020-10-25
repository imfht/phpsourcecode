<?php
/**
 * Created by PhpStorm.
 * User: spatra
 * Date: 15-4-28
 * Time: 下午8:21
 */
namespace App\Http\Controllers;


use App\Services\DataPurifier;
use App\Sight;
use Helper\ResponseHelper;
use Illuminate\Http\Request;
use App\Route;
use Helper\RouteHelper;

class RouteController extends Controller
{
    use RouteHelper;

    public function __construct(Request $request)
    {
        DataPurifier::purifyForRest($request, $this->purifyField);
        $this->currentUser = \Auth::user();
    }

    /**
     * 【API模式下】
     * 返回路线列表.
     *
     * 相关的查询参数：
     *   type: 查询的类型（latest: 最新的，所有的路线按时间降序排列；mine: 自己所创建的; keyword: 按关键字查询, sight：按景点查询), 可选（默认为latest)
     *   Keyword:  按关键字查询时存在，用于指定查询所用的关键字
     *
     *   分页相关：
     *     per_page: 每一页显示的记录条数
     *     offset: 当前已显示的总记录条数
     *
     * 返回的参数格式（不启用分页时）：
     * mine:（按created_at字段的值，降序排列）
     * {
     *     "_id": "路线的id"
     *     "name": "路线名称"
     *     "description": "路线简介"
     *     "status": "路线状态"
     *     "created_at": "创建时间"
     *     "tag": "路线标签列表"
     *     {
     *          "name": "标签名称，外部显示使用"
     *          "label": "标签内部名称，内部使用"
     *     }
     *     ......
     *     // 注意是个数组
     * }
     * latest:（按created_at字段的值，降序排列）
     * [
     *     {
     *         "_id": "路线的id"
     *         "name": "路线名称"
     *         "description": "路线简介"
     *         "creator_id": "创建者的id"
     *         "creator": "创建者的信息"
     *         {
     *              "_id": "创建者的id"
     *              "username": "创建者的用户名"
     *              "cellphone_number": "创建者的手机号码"
     *              "head_image": "创建者的头像"【仅为头像文件名】
     *          }
     *          "tag": "路线标签"
     *          {
     *               "name": "标签的名称，外部显示使用"
     *               "label": "标签的内部标记，用于内部使用"
     *          }
     *      }
     *      ......
     *      // 注意是个数组    
     * ]
     * tag、keyword、sight:
     * [
     *      {
     *           "_id" : "路线id"
     *           "name": "路线的名称"
     *           "description": "路线的描述"
     *           "created_at": "路线的创建时间"
     *           "creator_id" : "创建者id"
     *           "creator": 
     *           {
     *               "_id": "创建者id"
     *               "username": "创建者用户名"
     *               "cellphone_number": "创建者的手机号码"
     *               "head_image": "创建者头像文件名"
     *            }
     *       }
     *       ......
     *       // 注意是个数组
     *  ]
     *
     * 启用分页时的数据格式：
     * {
     *     "total": "数据库中的数据总数"
     *     "per_page": "每一页显示的记录条数"
     *     "current_page": "当前页码"
     *     "last_page": "最后一页的页码"
     *     "next_page_url": "下一页的链接"
     *     "prev_page_url": "前一页的链接"
     *     "from": "当前页的记录的起始编号"
     *     "to": "当前页的记录的最后编号"
     *     "data": "数据列表，结构与不启用分页时所返回的数据结构一致"
     * }
     *
     * 【普通页面访问】
     *   返回路线单页App所对应的启动页面
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(Request $request)
    {
        return ResponseHelper::chooseResponse($request, [
            'json' => response()->json($this->getRouteListOnCond($request)),
            'base' => view('route.index')
        ]);
    }

    public function create(Request $request)
    {
        return ResponseHelper::chooseResponse($request,[
            'base' => response('view'),
            'json' => response()->json(null, 404)
        ]);
    }

    /**
     * 新建路线 
     *
     * 请求数据：
     * {
     *    "name": "路线名称" 【必填】
     *    "description": "路线描述"【可选】
     * }
     * 返回数据：
     *  {
     *    "_id": "路线的id"
     *    "name": "路线的名称"
     *    "description": "路线的简介"
     *    "creator_id": "创建者的id"
     *    "isPublic": "标识路线是否公开，默认值：false（不公开）"
     *    "status": "路线的状态"
     *    "daily": "日程列表，新建后默认为空"
     *    [
     *        {
     *            "_id": "日程的id"
     *            "remark": "当前日程的备注"
     *            "date": "当前日程的具体日期，ISO格式"
     *            {
     *                "sec": "unix时间戳"
     *                "usec": "暂无用处"
     *             }
     *             "sights":
     *             [
     *                 {
     *                      "sight_id": "景点的id"
     *                      "name": "景点名称"
     *                      "loc": "geo坐标结构"
     *                      {
     *                          "type": "Point"
     *                          "coordinates": [ "经度", "纬度" ]
     *                      }
     *                 }
     *                 ......
     *                 // 注意是个数组
     *             ]
     *        }
     *        ......
     *        // 注意是个数组
     *    ],
     *    "transportation": "交通方式列表，新建后默认为空" 
     *    [
     *        {
     *            "_id": "交通方式的id"
     *            "from_name": "出发地点的名称"
     *            "from_sight_id": "出发地点所关联的景点，如果存在的话"
     *            "from_loc": "geo坐标结构"
     *            {
     *                "type": "Point"
     *                "coordinates": [ "经度", "纬度" ]
     *            }
     *            "to_name": "目的地点的名称"
     *            "to_sight_id": "目的地点所关联的景点，如果存在的话"
     *            "to_loc": "geo坐标结构"
     *            {
     *                "type": "Point"
     *                "coordinates": [ "经度", "纬度" ]
     *            }
     *            "description": "交通方式的描述"
     *            "prize": "费用"
     *            "consuming": "耗时，单位为分钟"
     *        }
     *        ......
     *        // 注意是个数组
     *    ],
     *    "photo": "旅行照片列表，新建后默认为空"
     *    [
     *        {
     *            "_id": "图片的id"
     *            "name": "图片的文件名"
     *         }
     *         ......
     *         // 注意是个数组
     *    ]
     * }
     * 
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function store()
    {
        $postData = \Input::only(['name', 'description']);

        $validate = $this->getStoreValidator($postData);
        if ( $validate->fails() ) {
            return ResponseHelper::responseErrorMessageOnJson($postData, $validate->getMessageBag());
        }

        $newRoute = $this->buildStoreData($postData);
        $respData = $this->storeRoute($newRoute);
        return response()->json($respData, 201);
    }

    /**
     * 查看某一路线信息
     *
     * 当该路线是公开的时候，创建者和非创建者看到的信息是一样额；
     * 当该路线是非公开的时候，非创建者将看不到以下信息：
     *     1.photo：旅行照片 
     *
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function show($id)
    {
        $respData = $this->getShowData($id);
        return response()->json($respData, 200);
    }

    public function edit()
    {

    }

    /**
     * 更新路线信息
     *
     * 请求数据：
     * {
     *     "name": "路线名称"【可选】
     *     "description": "路线简介"【可选】
     *     "isPublic": "标识路线是否公开，true为公开，false为非公开"【可选】
     *     "status": "路线的状态"【可选】
     *     "tag": "路线标签"【可选】
     *     {
     *         "name": "标签的名称，外部显示使用"【必选】
     *         "label": "标签的内部标识，内部使用"【必选】
     *     }
     * }
     * 请求数据描述：
     *     1.status的值必须为以下之一：planning、travelling、finished
     * 返回数据：
     * 成功：200
     * {
     *     "_id": "路线的id"
     *     "name": "路线的名称"
     *     "description": "路线简介"
     *     "isPublic": "标识路线是否公开，true为公开，false为非公开"
     *     "status": "路线的状态"
     *     "creator_id": "创建者的id"
     *     "tag": "路线的标签"
     *     {
     *         "name": "标签的名称，外部显示使用"
     *         "label": "标签的内部标识，内部使用"
     *     }
     *     "creator": "创建者的信息"
     *     {
     *         "_id": "创建者的id"
     *         "username": "创建者的用户名"
     *         "cellphone_number": "创建者的手机号码"
     *         "head_image": "创建者的头像"
     *     }
     *     "created_at": "路线的创建时间"
     *    "daily": "日程列表"
     *    [
     *        {
     *            "_id": "日程的id"
     *            "remark": "当前日程的备注"
     *            "date": "当前日程的具体日期，ISO格式"
     *            {
     *                "sec": "unix时间戳"
     *                "usec": "暂无用处"
     *             }
     *             "sights":
     *             [
     *                 {
     *                      "sight_id": "景点的id"
     *                      "name": "景点名称"
     *                      "loc": "geo坐标结构"
     *                      {
     *                          "type": "Point"
     *                          "coordinates": [ "经度", "纬度" ]
     *                      }
     *                 }
     *                 ......
     *                 // 注意是个数组
     *             ]
     *        }
     *        ......
     *        // 注意是个数组
     *    ],
     *    "transportation": "交通方式列表" 
     *    [
     *        {
     *            "_id": "交通方式的id"
     *            "from_name": "出发地点的名称"
     *            "from_sight_id": "出发地点所关联的景点，如果存在的话"
     *            "from_loc": "geo坐标结构"
     *            {
     *                "type": "Point"
     *                "coordinates": [ "经度", "纬度" ]
     *            }
     *            "to_name": "目的地点的名称"
     *            "to_sight_id": "目的地点所关联的景点，如果存在的话"
     *            "to_loc": "geo坐标结构"
     *            {
     *                "type": "Point"
     *                "coordinates": [ "经度", "纬度" ]
     *            }
     *            "description": "交通方式的描述"
     *            "prize": "费用"
     *            "consuming": "耗时，单位为分钟"
     *        }
     *        ......
     *        // 注意是个数组
     *    ],
     *    "photo": "旅行照片列表"
     *    [
     *        {
     *            "_id": "图片的id"
     *            "name": "图片的文件名"
     *         }
     *         ......
     *         // 注意是个数组
     *    ]
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
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function update($id)
    {
        // 检查该路线是否存在，以及当前用户是不是该路线的创建者
        if ( 0 === Route::where('_id', $id)->where('creator_id', \Auth::user()['_id'])->count() ) {
            return response()->json(['error' => '找不到相应的数据'], 404);
        }

        $putData = \Input::only(['name', 'description', 'status', 'isPublic', 'tag']);

        $validate = $this->getUpdateValidator($putData);
        if ( $validate->fails() ) {
            return ResponseHelper::responseErrorMessageOnJson($putData, $validate->getMessageBag());
        }

        $effectRow = $this->updateRoute($id, $putData);
        if ( 0 === $effectRow ) {
            return response()->json(['error' => '更新路线失败'], 404);
        } else {
            $respData = Route::with(['creator' => function($query){
                $query->get(['_id', 'username', 'cellphone_number', 'head_image']);
            }])->findOrFail($id);
            return response()->json($respData, 200);
        }
    }

    /**
     * 删除路线
     *
     * 返回数据：
     * 成功：200
     * 数据不存在：404
     * {
     *    "error": "相关的错误信息"
     * }
     *
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function destroy($id)
    {
        $route = Route::where('creator_id', \Auth::user()['_id'])
            ->findOrFail($id);

        // 删除该路线的景点关联
        $effectRow = Sight::where('routes', $id)->pull('routes', $id);

        if ( $route->delete() ) {
            return response()->json('', 200);
        } else {
            return response()->json(['error' => '删除路线失败'], 404);
        }
    }

    private $currentUser;

    private $purifyField = [
        'GET' => ['type','query'],
        'POST' => ['name', 'description'],
        'PUT' => ['name', 'description']
    ];
}