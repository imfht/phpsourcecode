<?php
/**
 * Created by PhpStorm.
 * User: zero
 * Date: 15-6-11
 * Time: 下午3:08
 */
namespace Helper;

use App\CollectionRoute;
use App\Route;

trait CollectionRouteHelper
{
    /**
     * 获取新建路线收藏的数据验证器
     *
     * @param array $postData 请求提交的数据
     * @return \Illuminate\Validation\Validator
     */
    protected function getStoreValidator($postData)
    {
        $validate = \Validator::make($postData, [
            'route_id' => 'required|exists:routes,_id'
        ]);

        return $validate;
    }

    /**
     * 构建并保存新的路线收藏数据
     *
     * @param array $postData 请求提交的数据
     * @return static
     */
    protected function storeCollectionData($postData)
    {
        // 构造新的路线收藏数据
        $postData['owner_id'] = \Auth::user()['_id'];
        $targetRoute = Route::findOrFail($postData['route_id']);
        $postData['name'] = $targetRoute['name'];
        $postData['description'] = $targetRoute['description'];
        $postData['creator_id'] = $targetRoute['creator_id'];

        $newCollection = CollectionRoute::create($postData);

        return $newCollection;
    }
}