<?php
/**
 * Created by PhpStorm.
 * User: zero
 * Date: 15-5-17
 * Time: 下午7:17
 */
namespace Helper;

trait RoutePhotoHelper
{
    /**
     * 获取添加旅行图片的数据验证器
     *
     * @param array $postData 请求提交的数据
     * @return \Illuminate\Validation\Validator
     */
    protected function getStoreValidator($postData)
    {
        $validate = \Validator::make($postData, [
            'photo' => 'required'
        ]);

        // 若上传的是图片文件，则增加相关的验证规则
        $validate->sometimes('photo', 'image|max:2048', function($input) {
            return \Input::hasFile('photo');
        });

        return $validate;
    }

    /**
     * 存储图片数据，将图片与某一路线关联起来
     *
     * @param int $routeId 路线的id
     * @return mixed $storeResp 包含数据库操作时受影响的行数，以及新的图片文件名的关联数组
     */
    protected function storePhotoData($routeId)
    {
        $updateData['_id'] = time();
        $updateData['name'] = hash('sha256',$routeId. '_'. time()). '.png';
        $effectRow = \DB::collection('routes')->where('_id', $routeId)
            ->where('creator_id', \Auth::user()['_id'])
            ->push('photo', $updateData);

        $storeResp = $updateData;
        $storeResp['effectRow'] = $effectRow;
        return $storeResp;
    }

    /**
     * 保存图片文件
     *
     * @param mixed $photo 图片文件数据，要么是UploadFile对象，要么是base64编码的字符串
     * @param string $photoName 新的图片文件名
     * @param bool $isFile 默认为true，表示该图片是通过文件上传发送的，若是base64编码，则为false
     */
    protected function savePhoto($photo, $photoName, $isFile = true)
    {
        if ( $isFile ) {
            $photo->move(public_path(). '/image/routePhoto', $photoName);
        } else {
            $photo = base64_decode($photo);
            file_put_contents(public_path(). '/image/routePhoto/'. $photoName, $photo);
        }
    }
}