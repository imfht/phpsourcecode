<?php
/**
 * Created by PhpStorm.
 * User: spatra
 * Date: 15-4-28
 * Time: 下午9:29
 */

namespace App;

use \DB;
use \MongoId;

class Sight extends \Eloquent
{
    /**
     * 新增一个签到的用户.
     *
     * @param int $sightId 景点id
     * @param int $userId 用户id
     * @return boolean 成功签到则返回true，否则返回false
     */
    public static function addedCheckIn($sightId, $userId)
    {
        $result = DB::getMongoDB()->sights->update([
           '_id' => new MongoId($sightId)
        ], [
            '$push' => ['check_in' => $userId],
            '$inc' => [ 'check_in_num' => 1]
        ]);

        return $result['updatedExisting'];
    }

    /**
     * 通过景点名称，获取景点id
     *
     * @param $sightName
     * @return mixed
     */
    public static function getSightId($sightName)
    {
        return Sight::where('name', $sightName)->firstOrFail()['_id'];
    }

    /**
     * 为景点添加关联的路线.
     *
     * @param int $sightId  待操作的景点iｄ
     * @param int $routeId 待操作的路线id
     * @return bool 操作成功返回true， 操作失败返回false
     */
    public static function addRelativeRoute($sightId, $routeId)
    {
        $effectRow = DB::collection('sights')->where('_id', $sightId)->push('routes', $routeId);

        if( 0 === $effectRow ){
            return false;
        } else {
            return true;
        }
    }

    public static function getSightsByKeyword($keyword, $onlyId = false)
    {
        $keyword = trim($keyword);
        if( empty($keyword) ) return [];

        $regKeyword = new \MongoRegex('/'. $keyword. '/');
        $result = DB::getMongoDB()->sights->find([
           '$or' => [
               ['name' => $regKeyword],
               ['description' => $regKeyword],
               ['address' => $regKeyword]
           ]
        ]);

        $respData = [];

        if( $onlyId ){
            foreach($result as $current){
                $respData[] = (string)$current['_id'];
            }
        } else {
            foreach($result as $current){
                $current['_id'] = (string)$current['_id'];
                $respData[] = $current;
            }
        }

        return $respData;
    }

    protected $table = 'sights';

    protected $guarded = ['_id'];
}