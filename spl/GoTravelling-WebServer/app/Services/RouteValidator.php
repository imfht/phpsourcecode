<?php
/**
 * Created by PhpStorm.
 * User: zero
 * Date: 15-5-8
 * Time: 下午3:31
 */
namespace App\Services;

use App\RouteTag;
use App\Sight;
use Illuminate\Validation\Validator;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
  * 此类基于Laravel自带的Validator，提供了一些与路线相关资源字段的验证方法
  *
  * 使用说明：验证方法均以 validate 作为前缀，其后的即为对应的验证规则
  * 示例：
  *     验证规则：image_list:2048
  *     对应验证方法：validateImageList
  *     该方法的 $arrtibute 参数为被验证的字段名
  *     该方法的 $value 参数为被验证的字段所对应的值
  *     该方法的 parameters 参数为 [ '0' => 2048 ]
  *
  */
class RouteValidator extends Validator
{
    /**
     * 针对路线标签的验证方法
     *
     * @param $attribute
     * @param $value
     * @param $parameters
     * @return bool
     */
    public function validateTag($attribute, $value, $parameters)
    {
        if ( !is_array($value) ) {
            return false;
        }

        $tagKeys = ['name', 'label'];
        if ( !$this->arrayMustHasKeys($value, $tagKeys, true) ) {
            return false;
        }

        // 标签必须符合数据库中的预设值
        $checkExist = RouteTag::where('name', $value['name'])->where('label', $value['label'])->count();
        if ( $checkExist ) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 针对 loc 数据结构的验证方法
     *
     * @param $attribute
     * @param $value
     * @param $parameters
     * @return bool
     */
    public function validateLoc($attribute, $value, $parameters)
    {
        if ( !is_array($value) ) {
            return false;
        }

        $checkType = array_key_exists('type', $value) && $value['type'] === 'Point';
        $checkCoordinates = $checkType && array_key_exists('coordinates', $value) && is_array($value['coordinates']);

        $checkCoordinates = $checkCoordinates && count($value['coordinates']);

        if( $checkCoordinates && $checkType ){
            $longitude = doubleval($value['coordinates'][0]);
            $dimensionality = doubleval($value['coordinates'][1]);

            return $longitude >= 0 && $longitude <= 180 && $dimensionality >= 0 && $dimensionality <= 180;
        } else {
            return false;
        }
    }

    /**
     * 针对 sight_id 的验证方法
     *
     * @param $attribute
     * @param $value
     * @param $parameters
     * @return bool
     */
    public function validateSightId($attribute, $value, $parameters)
    {
        $count = Sight::where('_id', $value)->count();
        if ( 0 == $count ) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * 针对 sights 数据列表的验证方法
     *
     * @param $attribute
     * @param $value
     * @param $parameters
     * @return bool
     */
    public function validateSights($attribute, $value, $parameters)
    {
        if ( !is_array($value) ) {
            return false;
        }

        $baseKeys = ['loc', 'name'];
        foreach( $value as $val ) {
            if ( !$this->arrayMustHasKeys($val, $baseKeys) ) {
                return false;
            }

            if ( !$this->validateLoc('loc', $val['loc'], null) ) {
                return false;
            }
        }

        $sightIds = array_fetch($value, 'sights_id');
        //检查对应的 sight_id 是否存在
        if( Sight::whereIn('_id', $sightIds)->count() == count($sightIds) ){
            return true;
        } else {
            return false;
        }
    }

    /**
     * 针对图片列表的验证方法
     *
     * @param $attribute
     * @param $value
     * @param $parameters
     * @return bool
     */
    public function validateImageList($attribute, $value, $parameters)
    {
        if ( !is_array($value) ) {
            return false;
        }

        foreach ($value as $val) {
            // 图片格式的验证
            if ( !$this->validateImage('', $val) ) {
                return false;
            }
            // 图片大小的验证
            if ( !$this->validateMax('', $val, $parameters) ) {
                return false;
            }
        }

        return true;
    }

    /**
     * 针对 location 字段的验证方法
     *
     * @param $attribute
     * @param $value
     * @param $parameters
     * @return bool
     */
    public function validateLocation($attribute, $value, $parameters)
    {
        if ( !is_array($value) ) {
            return false;
        }

        $keys = ['name'];
        if ( !$this->arrayMustHasKeys($value, $keys, true) ) {
            return false;
        }

        return true;
    }

    /**
     * 针对路线交通方式的 description 字段的验证方法
     *
     * @param $attribute
     * @param $value
     * @param $parameters
     * @return bool
     */
    public function validateDesc($attribute, $value, $parameters)
    {
        if ( !is_array($value) ) {
            return false;
        }

        // 检查方式类型
        $typeList = ['drive', 'bus', 'walk'];
        if ( !$this->validateIn('', $value['type'], $typeList) ) {
            return false;
        }

        // 如果方式策略为空，则不做验证
        if ( empty($value['policy']) ) {
            return true;
        }
        
        $policyList = [
            'drive' => ['least_block', 'least_distance', 'least_cost', 'least_time'],
            'bus' => ['avoid_subway', 'least_exchange', 'least_walk', 'least_time'],
        ];
        // 除了 walk 没有方式策略，drive 和 bus 都应该有相应的策略
        if ( $value['type'] != 'walk' ) {
            if ( !is_array( $value['policy']) ) {
                return false;
            } else {
                foreach ($value['policy'] as $policy) {
                    if ( !$this->validateIn('', $policy, $policyList[ $value['type'] ]) ) {
                        return false;
                    }
                }
            }
        }

        return true;
    }

    /**
     * 判断数组中是否存在给定的一组 key 值
     *
     * @param array $dataArray 被验证的数据数组
     * @param array $keys 一组给定的 key 值
     * @param bool $defined 若为 true，则该 key 对应的值必须为非空; 若为 false 则不作要求
     * @return bool
     */
    protected function arrayMustHasKeys($dataArray, array $keys, $defined = false)
    {
        foreach($keys as $currentKey){
            if ( !array_key_exists( $currentKey, $dataArray ) ) {
                return false;
            }

            if( $defined && is_null($dataArray[ $currentKey ]) ){
                return false;
            }
        }
        return true;
    }
}