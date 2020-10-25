<?php
/**
 * Created by PhpStorm.
 * User: zero
 * Date: 15-5-17
 * Time: 下午7:17
 */
namespace App\Http\Controllers;

use App\Route;
use Helper\ResponseHelper;
use Helper\RoutePhotoHelper;

class RoutePhotoController extends Controller
{
    use RoutePhotoHelper;

    /**
     *  添加旅行照片
     *
     * 请求数据：
     * {
     *    "photo": "图片数据，形式为：文件或base64编码字符串"【必选】
     * }
     * 返回数据：
     *      成功：201
     *     {
     *        "_id": "图片的id"
     *        "name": "图片的文件名"
     *     }
     *     失败：
     *         数据不存在：404
     *
     * @param int $routeId 路线的id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function store($routeId)
    {
        if ( 0 === Route::where('_id', $routeId)->where('creator_id', \Auth::user()['_id'])->count() ) {
            return response()->json(['error' => '找不到相应的数据'], 404);
        }

        $postData = \Input::only('photo');
        $validate = $this->getStoreValidator($postData);
        if ( $validate->fails() ) {
            return ResponseHelper::responseErrorMessageOnJson($postData, $validate->getMessageBag());
        }

        // 存储图片数据
        $storeResult = $this->storePhotoData($routeId);
        if ( 0 === $storeResult['effectRow'] ) {
            return response()->json(['error' => '保存旅行照片失败'], 404);
        } else {
            // 存储图片文件
            $this->savePhoto($postData['photo'], $storeResult['name'], \Input::hasFile('photo'));
            return response()->json(array_only($storeResult, ['_id', 'name']), 201);
        }
    }

    /**
     * 删除旅行照片
     *
     * 返回数据：
     *     成功：200
     *     数据不存在：404
     *
     * @param int $routeId 路线的id
     * @param int $id 旅行照片的id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function destroy($routeId, $id)
    {
        if ( 0 === Route::where('_id', $routeId)->where('creator_id', \Auth::user()['_id'])->count() ) {
            return response()->json(['error' => '找不到相应的数据'], 404);
        }

        $effectRow = \DB::collection('routes')->where('_id', $routeId)
            ->where('creator_id', \Auth::user()['_id'])
            ->where('photo._id', intval($id))
            ->pull('photo', ['_id' => intval($id)]);

        // 暂时只删除图片的数据关联，不删除图片文件

        if ( 0 === $effectRow ) {
            return response()->json(['error' => '删除旅行照片失败'], 404);
        } else {
            return response()->json('', 200);
        }
    }
}