<?php
/**
 * Created by PhpStorm.
 * User: zero
 * Date: 15-5-29
 * Time: 下午10:11
 */
namespace App\Http\Controllers;

use App\Route;
use App\RouteNote;
use Helper\ResponseHelper;
use Helper\RouteNoteHelper;

class RouteNoteController extends Controller
{
    use RouteNoteHelper;

    /**
     * 获取路线小记列表
     * 返回数据：
     * 成功：200
     * [
     *     {
     *         "_id": "小记的id"
     *         "content": "小记的内容"
     *         "images": "图片列表"
     *         [
     *             "name": "图片的文件名"
     *             ......
     *             // 注意是个数组
     *         ]
     *         "loc_name": "所处的地点的名称"
     *         "loc": "geo坐标结构"
     *         {
     *              "type": "Point"
     *              "coordinates": [ "经度", "纬度" ]
     *         }
     *         "created_at": "创建时间"
     *     }
     *     ......
     *     // 注意是个数组
     * ]
     *
     * @param $routeId
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index($routeId)
    {
        // 此处暂不作当前用户是不是创建者的判断

        $respData = RouteNote::where('route_id', $routeId)->get()->toArray();

        return response()->json($respData, 200);
    }

    /**
     * 查看某一路线小记的信息
     * 返回数据：
     * 成功：200
     * {
     *     "_id": "小记的id"
     *     "content": "小记的内容"
     *     "images": "图片列表"
     *     [
     *         "name": "图片的文件名"
     *         ......
     *         // 注意是个数组
     *     ]
     *     "loc_name": "所处的地点的名称"
     *     "loc": "geo坐标结构"
     *     {
     *          "type": "Point"
     *          "coordinates": [ "经度", "纬度" ]
     *     }
     *     "created_at": "创建时间"
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
        $noteData = RouteNote::where('_id', $id)
            ->where('route_id', $routeId)->first();

        if ( is_null($noteData) ) {
            return response()->json(['error' => '找不到相应的数据'], 404);
        } else {
            return response()->json($noteData, 200);
        }
    }

    /**
     * 新建路线小记
     *
     * 请求数据：
     * {
     *     "content": "小记的内容"【必选】
     *     "loc_name": "所处的地点的名称"【可选】
     *     "loc": "geo坐标结构"
     *     {
     *         "type": "Point"【必选】
     *         "coordinates": [ "经度", "纬度" ]【必选】
     *     }
     *     "images": "图片列表"【可选】
     *     [
     *         "图片数据"【必选】
     *         ......
     *         // 注意是个数组
     *     ]
     * }
     * 请求数据描述：
     *     １.图片数据：有２种提交方式，一种是文件上传、一种是通过base64编码的字符串
     * 返回数据：
     * 成功：201
     * {
     *     "_id": "新建小记的id"
     *     "content": "新建小记的内容"
     *     "loc_name": "所处的地点的名称"
     *     "loc": "geo坐标结构"
     *     {
     *          "type": "Point"
     *          "coordinates": [ "经度", "纬度" ]
     *     }
     *     "images": "图片列表"
     *     [
     *         "图片文件名"
     *         ......
     *         // 注意是个数组
     *     ]
     * 数据不存在：404
     * {
     *     "error": "相关的错误信息"
     * }
     * 数据验证失败：400
     * {
     *     "error": "相关的错误信息"
     *     "data": "请求提交的数据"
     * }
     *
     * @param $routeId
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function store($routeId)
    {
        Route::findOrFail($routeId);

        $postData = \Input::only('content', 'loc_name', 'loc', 'images');

        // 当 loc 字段存在且不是数组形式时，则认为该字段是被json编码，需要进行json解码成数组形式
        if ( !is_null($postData['loc']) && !is_array($postData['loc']) ) {
            $postData['loc'] = json_decode($postData['loc'], true);
        }

        $validate = $this->getStoreValidator($postData);
        if ( $validate->fails() ) {
            return ResponseHelper::responseErrorMessageOnJson($postData, $validate->getMessageBag());
        }

        $respData = $this->storeNoteData($postData,$routeId);
        if ( !is_null($postData['images']) ) {
            $this->saveImageList($respData['imageData'], \Input::hasFile('images'));
        }

        return response()->json($respData['newNote'], 201);
    }


    /**
     * 删除路线小记数据
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
     * @throws \Exception
     */
    public function destroy($routeId, $id)
    {
        // 检查路线是否存在及当前用户是不是创建者
        if ( 0 === Route::where('_id', $routeId)->where('creator_id', \Auth::user()['_id'])->count() ) {
            return response()->json(['error' => '找不到相应的数据'], 404);
        }

        // 暂时只删除小记数据，不删除图片文件
        $success = RouteNote::findOrFail($id)->delete();
        if ( true === $success ) {
            return response()->json('', 200);
        } else {
            return response()->json(['error' => '删除路线小记失败'], 404);
        }
    }
}