<?php
/**
 * Created by PhpStorm.
 * User: zero
 * Date: 15-6-11
 * Time: 上午10:52
 */
namespace App\Http\Controllers;

use App\Collection;
use App\CollectionRoute;
use App\Route;
use App\User;
use Helper\CollectionRouteHelper;
use Helper\ResponseHelper;

class CollectionRouteController extends Controller
{
    use CollectionRouteHelper;

    /**
     * 获取路线收藏列表
     * 返回数据：
     * 成功：200
     * [
     *     {
     *         "_id": "收藏的id" 
     *         "owner_id": "收藏者的id"
     *         "route_id": "路线的id"
     *         "creator_id": "路线的创建者id"
     *         "name": "路线的名称"
     *         "description": "路线的描述"
     *         "created_at": "收藏的创建时间"
     *     }
     *     ......
     *     // 注意是个数组
     * ]
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index()
    {
        $respData = CollectionRoute::where('owner_id', \Auth::user()['_id'])->get()->toArray();
        return response()->json($respData, 200);
    }

    /**
     * 查看某一路线收藏
     * 返回数据：
     * 成功：200
     * {
     *     "_id": "收藏的id"
     *     "owner_id": "收藏者的id"
     *     "route_id": "路线的id"
     *     "creator_id": "路线的创建者id"
     *     "name": "路线的名称"
     *     "description": "路线的描述"
     *     "created_at": "收藏的创建时间"
     *     "creator": "路线的创建者的信息"
     *     {
     *         "_id": "创建者的id"
     *         "username": "创建者的用户名"
     *         "cellphone_number": "创建者的手机号码"
     *         "head_image": "创建者的头像"
     *     }
     * }
     * 数据不存在：404
     * {
     *     "error": "相关的错误信息"
     * }
     * 非收藏者查看：403
     * {
     *     "error": "相关的错误信息"
     * }
     *
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function show($id)
    {
        $respData = CollectionRoute::findOrFail($id);

        // 检查当前用户是不是该收藏的拥有者
        if ( $respData['owner_id'] != \Auth::user()['_id'] ) {
            return response()->json(['error' => '抱歉，您没有查看的权限'], 403);
        }

        $respData['creator'] = User::findOrFail($respData['creator_id']);

        return response()->json($respData, 200);
    }

    /**
     * 新建路线收藏
     * 请求数据：
     * {
     *     "route_id": "路线的id"【必选】
     * }
     * 返回数据：
     * 成功：200
     * {
     *     "_id": "新建收藏的id"
     *     "owner_id": "收藏者的id"
     *     "route_id": "路线的id"
     *     "creator_id": "路线的创建者id"
     *     "name": "路线的名称"
     *     "description": "路线的描述"
     *    "created_at": "收藏的创建时间"
     * }
     * 数据不存在：404
     * {
     *     "error": "相关的错误信息"
     * }
     * 验证失败：400
     * {
     *     "error": "相关的错误信息"
     *     "data": "请求提交的数据"
     * }
     * 收藏自己创建的路线：403
     * {
     *     "error": "相关的错误信息"
     * }
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function store()
    {
        $postData = \Input::only(['route_id']);

        // 检查要收藏的路线是不是当前用户创建的
        if ( Route::where('_id', $postData['route_id'])->where('creator_id', \Auth::user()['_id'])->count() ) {
            return response()->json(['error' => '抱歉，您没有查看的权限'], 403);
        }

        $validate = $this->getStoreValidator($postData);
        if ( $validate->fails() ) {
            return ResponseHelper::responseErrorMessageOnJson($postData, $validate->getMessageBag());
        }

        $respData = $this->storeCollectionData($postData);
        return response()->json($respData, 201);
    }

    /**
     * 删除路线收藏
     * 成功：200
     * 数据不存在：404
     * {
     *     "error": "相关的错误信息"
     * }
     * 非收藏者删除路线收藏：403
     * {
     *     "error": "相关的错误信息"
     * }
     *
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function destroy($id)
    {
        $targetCollection = CollectionRoute::findOrFail($id);

        // 检查当前用户是不是该收藏的拥有者
        if ( $targetCollection['owner_id'] != \Auth::user()['_id'] ) {
            return response()->json(['error' => '抱歉，您没有查看的权限'], 403);
        }

        if ( $targetCollection->delete() ) {
            return response()->json('', 200);
        } else {
            return response()-> json(['error' => '删除失败'], 404);
        }
    }
}