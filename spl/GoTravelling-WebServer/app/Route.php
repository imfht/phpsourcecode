<?php
/**
 * Created by PhpStorm.
 * User: spatra
 * Date: 15-4-28
 * Time: 下午9:29
 */

namespace App;


use Carbon\Carbon;
use Jenssegers\Mongodb\Eloquent\SoftDeletes;
use \DB;
use Symfony\Component\VarDumper\VarDumper;

class Route extends \Eloquent
{
    use SoftDeletes;

    public static function getRouteIdOnName($routeName)
    {
        return array_fetch(Route::where('name', $routeName)->get(['_id'])->toArray(), '_id');
    }

    /**
     * 获取日程数据
     *
     * @param int $routeId 路线的主键id
     * @param int $dailyId 日程的编号id
     * @return null|array mixed 当存在的时候返回相关的数据（存于关联数组），不存在时返回false
     */
    public static function getDailyData($routeId, $dailyId)
    {
        $routeData = Route::where('_id', $routeId)
            ->where('daily._id', intval($dailyId))
            ->get(['daily.$'])->first();

        if (empty($routeData)) {
            return null;
        } else {
            return head($routeData['daily']);
        }
    }

    /**
     * 获取交通方式数据
     *
     * @param int $routeId 路线的主键id
     * @param int $transportId 交通方式的编号id
     * @return null|array mixed 当存在的时候返回相关的数据（存于关联数组），不存在时返回false
     */
    public static function getTransportData($routeId, $transportId)
    {
        $routeData = Route::where('_id', $routeId)
            ->where('transportation._id', intval($transportId))
            ->get(['transportation.$'])->first();

        if (empty($routeData)) {
            return null;
        } else {
            return head($routeData['transportation']);
        }
    }

    /**
     * 通过路线标签来获取路线列表
     *
     * @param $tag 路线标签
     * @return array|\Illuminate\Database\Eloquent\Collection|static[]
     */
    public static function getRoutesOnTag($tag)
    {
        $tag = trim($tag);
        if ( is_string($tag) && !empty($tag) ) {
            return Route::where('tag.label', $tag)
                ->get(['_id', 'creator_id', 'name', 'description', 'created_at', 'tag']);
        } else {
            return [];
        }
    }

    /**
     * 通过景点来查找合适的路线。
     *
     * @param string|array $mixed 查找景点的关键字，或景点的id所组成的数组
     * @return array
     */
    public static function getRoutesOnSights($mixed)
    {
        //总的查询数组
        $mongoFindConditions = ['$or' => []];

        if( is_string($mixed) ){

            array_push($mongoFindConditions['$or'], [
               'daily.sights.name' =>  new \MongoRegex('/'. $mixed. '/ig')
            ]);

            $mixed = Sight::getSightsByKeyword($mixed, true);
        }

        $result = Sight::whereIn('_id', $mixed)->get(['routes']);

        //提取结果中的 routes id ，并进行去重操作
        $routesId = [];
        foreach($result->toArray() as $current ){
            $routesId = array_merge($routesId, $current['routes']);
        }
        $routesId = array_flip( array_flip($routesId) );

        //将routes id 转换为 MongoId
        $mongoIds = [];
        foreach($routesId as $current){
            array_push($mongoIds, new \MongoId($current));
        }

        //将查询条件加入查询总的查询数组当中
        array_push($mongoFindConditions['$or'], [
            '_id' => ['$in' => $mongoIds]
        ]);

        return static::getArrayResultFromMongoCursor(
            DB::getMongoDB()->routes->find($mongoFindConditions)
        );
    }

    /**
     * 根据关键字查询路线.
     *
     *  当前的设定是，当 name 或 description 字段包含关键字则被判断为匹配.
     *
     * @param string $keyword 待查询的关键字
     * @return array 返回相关的数据的关联数组（没有匹配结果时返回空数组）
     */
    public static function getRoutesOnKeyword($keyword)
    {
        // 兼容部署服务器的php 5.4版本的empty用法
        $keyword = trim($keyword);
        if( is_string($keyword) && !empty($keyword) ){

            $reg = new \MongoRegex('/'. trim($keyword). '/gi');

            $result = DB::getMongoDB()->routes->find([
                '$or' => [
                    ['name' => $reg],
                    ['description' => $reg]
                ]
            ]);

            return static::getArrayResultFromMongoCursor($result);
        } else {

            return [];
        }

    }

    /**
     * 为路线数据增加创建者字段
     *
     * @param $source
     */
    public static function addCreatorField( & $source)
    {
        $creator_ids = array_fetch($source, 'creator_id');
        $users = User::whereIn('_id', $creator_ids)->get([
           'username', 'cellphone_number', '_id', 'head_image', 'status'
        ]);

        $hashUsers = [];
        foreach($users as $currentUser){
            $hashUsers[ $currentUser['_id'] ] = $currentUser->toArray();
        }

        foreach($source as $index=>$currentRecorder){
            $id = $currentRecorder['creator_id'];

            if( array_key_exists($id, $hashUsers) ){
                $source[$index]['creator'] = $hashUsers[$id];
            }
        }
    }

    /**
     * 返回创建者的关系.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function creator()
    {
        return $this->belongsTo('App\User', 'creator_id', '_id');
    }

    /**
     * 从MongoDB游标中获取结果，并返回相应的关联数组.
     *
     * @param \MongoCursor $cursor
     * @return array
     */
    public static function getArrayResultFromMongoCursor(\MongoCursor $cursor)
    {
        $respData = [];
        foreach($cursor as $current){
            $current['_id'] = (string)$current['_id'];
            $current['created_at'] = (string)Carbon::createFromTimestamp($current['created_at']->sec,
                \Config::get('timezone'));
            array_push(
                $respData, array_only($current, ['_id', 'name', 'creator_id', 'description', 'tag', 'created_at'])
            );
        }

        return $respData;
    }

    protected $dates = ['deleted_at'];

    protected $table = 'routes';

    protected $guarded = ['_id'];

    protected $hidden = ['_keyword'];
}