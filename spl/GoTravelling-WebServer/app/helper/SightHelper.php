<?php
/**
 * Created by PhpStorm.
 * User: spatra
 * Date: 15-5-5
 * Time: 下午3:31
 */

namespace Helper;

use App\Services\RouteValidator;
use App\Sight;
use Validator;

trait SightHelper
{
    /**
     * 生成相关的数据校验器.
     *
     * @param array $data 请求提交的数据
     * @return \Illuminate\Validation\Validator
     */
    protected function getStoreValidator(array $data)
    {
        Validator::extend('check_loc', function($attr, $value){
            $checkType = array_key_exists('type', $value) && $value['type'] === 'Point';
            $checkCoordinates = array_key_exists('coordinates', $value) && is_array($value['coordinates']);

            if( $checkCoordinates && $checkType ){
                $longitude = doubleval($value['coordinates'][0]);
                $dimensionality = doubleval($value['coordinates'][1]);

                return $longitude >= 0 && $longitude <= 180 && $dimensionality >= 0 && $dimensionality <= 180;
            } else {
                return false;
            }

        });

        $validator = Validator::make($data, [
            'province' => 'required|max:6',
            'city' => 'required|max:20',
            'name' => 'required|max:30',
            'loc' => 'required|check_loc',
            'description' => 'max:255',
            'address' => 'required|max:255'
        ]);

        return $validator;
    }

    /**
     * 辅助方法，根据数据库设定，构建新建的景点数据
     *
     * @param array $data 新的景点数据
     * @return array $respData 构建好的景点数据
     */
    protected function buildStoreData($data)
    {
        $respData = array_only($data, [
           'province', 'name', 'city', 'loc', 'address'
        ]);

        if( !isset($data['description']) ){
            $respData['description'] = '';
        }

        $respData['check_in'] = [];
        $respData['check_in_num'] = 0;

        return $respData;
    }

    /**
     * 获取更新景点信息的验证器 
     *
     * @param array $putData 请求提交的更新数据
     * @return \Illuminate\Validation\Validator
     */
    protected function getUpdateValidator($putData)
    {
        Validator::resolver(function($translator, $data, $rules, $messages){
            return new RouteValidator($translator, $data, $rules, $messages);
        });

        $validate = Validator::make($putData, [
            'type' => 'required|in:base,check_in,images',
            'province' => 'alpha|max:8',
            'city' => 'alpha|max:8',
            'loc' => 'array|loc',
            'name' => 'max:255',
            'description' => 'max:255',
            'address' => 'max:255',
        ]);

        // 当有图片上传时，增加必须的验证规则
        $validate->sometimes('images', 'required|image_list:2048', function($input) {
            return \Input::hasFile('images');
        });

        return $validate;
    }

    /**
     * 根据请求数据的 type 字段来执行对应的更新操作
     *
     * @param int $sightId 待更新的景点id
     * @param array $putData 包含 type 在内景点更新数据
     * @return array $resp 更新成功，返回空数组，否则，返回错误信息与状态码
     */
    protected function updateSightByCond($sightId, $putData)
    {
        $resp = [];
        switch ( $putData['type'] )
        {
            // 更新景点基本信息
            case 'base':
                $resp = $this->updateBase($sightId, $putData);
                break;
            // 景点签到
            case 'check_in':
                $resp = $this->updateCheckIn($sightId);
                break;
            // 图片上传
            case 'images':
                $resp = $this->updateImages($sightId, $putData);
                break;
            default:
                \App::abort(404);
        }
        return $resp;
    }

    /**
     * 更新景点的基本信息
     *
     * @param int $sightID 待更新的景点id
     * @param array $putData 更新的景点数据
     * @return array $resp 更新成功，返回空数组，否则，返回错误信息与状态码
     */
    protected function updateBase($sightID, $putData)
    {
        $baseKeys = ['province', 'city', 'loc', 'name', 'description', 'address'];
        $baseData = array_only($putData, $baseKeys);
        // 若无更新数据，则直接跳过
        if ( null == $baseData ) {
            $effectRow = 1;
        } else {
            $effectRow = \DB::collection('sights')->where('_id', $sightID)->update($baseData);
        }
        $resp = [];
        if ( 0 === $effectRow ) {
            $resp['error'] = '找不到相应的数据';
            $resp['status'] = 404;
        }
        return $resp;
    }

    /**
     * 处理景点签到
     *
     * @param int $sightId 签到的景点id
     * @return array $resp 更新成功，返回空数组，否则，返回错误信息与状态码
     */
    protected function updateCheckIn($sightId)
    {
        $currentUserId = \Auth::user()['_id'];

        $resp = [];
        if( Sight::where('_id', $sightId)->where('check_in', $currentUserId)->count() > 0 ){
            $resp['error'] = '当前用户已签到';
            $resp['status'] = 403;
            return $resp;
        }

        if( !Sight::addedCheckIn($sightId, $currentUserId)) {
            $resp['error'] = '签到失败';
            $resp['status'] = 404;
        }
        return $resp;
    }


    /**
     * 处理景点的图片上传，只有增加景点图片的功能
     *
     * @param $sightId 景点的id
     * @param $putData 待添加的景点图片数据
     * @return array 返回处理结果，成功时返回空数组，失败时返回相关错误信息和状态码
     */
    protected function updateImages($sightId, $putData)
    {
        $imageList = [];
        $time = time();
        // 设置新的图片文件名，并与相应的图片数据相关联
        foreach ($putData['images'] as $image) {
            $newImageName = hash('sha256', $sightId.'_'.$time). 'png';
            array_push($imageList, ['name' => $newImageName, 'data' => $image]);
            $time += 1;
        }

        // 保存图片数据
        $imageNames = array_fetch($imageList, 'name');
        $effectRow = \DB::collection('sights')->where('_id', $sightId)
            ->update([
                '$addToSet' => [
                    'images' => ['$each' => $imageNames]
                ]
            ]);

        $resp = [];
        if ( 0 === $effectRow ) {
            $resp['error'] = '上传景点图片失败';
            $resp['status'] = 403;
        } else {
            // 保存图片文件
            $this->saveImageList($imageList, \Input::hasFile('images'));
        }

        return $resp;
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
                $image['data']->move(public_path(). '/image/sight/', $image['name']);
            }
        } else {
            foreach ($imageData as $image) {
                $image['data'] = base64_decode($image['data']);
                file_put_contents(public_path(). '/image/sight/'. $image['name'], $image['data']);
            }
        }
    }
}
