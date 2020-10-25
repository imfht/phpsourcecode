<?php
/**
 * Created by PhpStorm.
 * User: zero
 * Date: 15-6-2
 * Time: 下午8:02
 */
namespace Helper;

use App\RouteNote;
use App\Services\RouteValidator;

trait RouteNoteHelper
{

    /**
     * 获取新建路线小记的数据验证器
     *
     * @param array $postData 请求提交的小记数据
     * @return \Illuminate\Validation\Validator
     */
    protected function getStoreValidator($postData)
    {
        \Validator::resolver(function($translator, $data, $rules, $messages) {
            return new RouteValidator($translator, $data, $rules, $messages);
        });

        $validate = \Validator::make($postData, [
            'content' => 'required|max:255',
            'loc_name' => 'max:255',
            'loc' => 'loc',
            'images' => 'array'
        ]);

        // loc_name 和 loc 字段出现时，必需同时出现
        $validate->sometimes(['loc_name', 'loc'], 'required', function($input) {
            $hasLocName = is_null(\Input::get('loc_name'));
            $hasLoc = is_null(\Input::get('loc'));
           return !( $hasLoc && $hasLocName );
        });

        // 当图片是通过文件上传时，增加相应的验证规则
        $validate->sometimes('images', 'image_list:2048', function($input) {
           return \Input::hasFile('images');
        });

        return $validate;
    }

    /**
     * 保存新建的小记数据
     *
     * @param array $postData 请求提交的小记数据
     * @param int $routeId 路线的id
     * @return mixed
     */
    protected function storeNoteData($postData, $routeId)
    {
        $storeData = $postData;
        $storeData['route_id'] = $routeId;
        $storeData['images'] = [];
        $imageData = [];
        // 设置图片文件的文件名，构造新的图片名与图片数据的关联
        if ( !is_null($postData['images']) ) {
            $time = time();
            foreach ($postData['images'] as $image) {
                $newFileName = hash('sha256', $routeId. '_'. $time). '.png';
                array_push($storeData['images'], $newFileName);
                // data为图片数据，name为对应的新文件名
                array_push($imageData, ['data' => $image, 'name' => $newFileName]);
                $time += 1;
            }
        }

        $respData['newNote'] = RouteNote::create($storeData)->toArray();
        $respData['imageData'] = $imageData;

        return $respData;
    }

    /**
     * 保存图片列表文件
     *
     * @param array $imageData 关联数组，包含图片数据（data字段）与其对应的文件名（name字段）
     * @param bool $isFile 标识是否通过文件上传的方式提交，默认为是
     */
    protected function saveImageList(array $imageData, $isFile = true)
    {
        if ( $isFile ) {
            foreach ($imageData as $image) {
                $image['data']->move(public_path(). '/image/routeNote/', $image['name']);
            }
        } else {
            foreach ($imageData as $image) {
                $image['data'] = base64_decode($image['data']);
                file_put_contents(public_path(). '/image/routeNote/'. $image['name'], $image['data']);
            }
        }
    }
}