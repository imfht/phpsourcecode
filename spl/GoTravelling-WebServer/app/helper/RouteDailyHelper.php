<?php
/**
 * Created by PhpStorm.
 * User: zero
 * Date: 15-5-3
 * Time: 下午9:24
 */
namespace Helper;

use App\Route;
use App\Services\RouteValidator;
use App\Sight;
use Illuminate\Http\Exception\HttpResponseException;
use Illuminate\Support\Facades\Auth;
use Validator;

trait RouteDailyHelper
{
    /**
     * 获取用于验证 store 方法的请求数据的验证器
     *
     * @param array $postData 请求提交的数据
     * @return \Illuminate\Validation\Validator
     */
    protected function getStoreValidator($postData)
    {
        Validator::resolver(function($translator, $data, $rules, $messages){
            return new RouteValidator($translator, $data, $rules, $messages);
        });

        $validate = Validator::make($postData, [
            'remark' => 'required|max:255',
            'sights' => 'array|sights'
        ]);

        return $validate;
    }

    /**
     * 构建新增的日程数据
     *
     * @param array $postData 请求提交的数据
     * @return mixed 按照数据库设定来构建好的日程数据
     */
    protected function buildDailyData($postData)
    {
        $newDaily['_id'] = time();
        $newDaily['remark'] = $postData['remark'];
        $newDaily['date'] = MongoHelper::buildMongoDate(null);
        if ( is_null($postData['sights']) ) {
            $newDaily['sights'] = [];
        } else {
            $newDaily['sights'] = $postData['sights'];
        }

        return $newDaily;
    }

    /**
     * 获取新建日程数据的验证器
     *
     * @param array $dailyData 新的日程数据 
     * @param $routeId 路线的id
     * @return int $effectRow 返回受影响的行数
     * @throws \Exception
     */
    protected function storeDaily($dailyData, $routeId)
    {
        $effectRow = \DB::collection('routes')->where('_id', $routeId)
            ->where('creator_id', \Auth::user()['_id'])
            ->push('daily', $dailyData);

        if( empty($dailyData['sights']) ){
            $sights = [];
        } else {
            $sights = array_fetch($dailyData['sights'], 'sights_id');
        }


        foreach($sights as $currentSightId){
            $check = Sight::addRelativeRoute($currentSightId, $routeId);
            if( $check === false ){
                throw new \Exception('路线与景点的关联建立失败');
            }
        }

        return $effectRow;
    }

    /**
     * 获取用于验证 update 方法的请求数据的验证器
     *
     * @param array $putData 请求提交的数据
     * @return \Illuminate\Validation\Validator
     */
    protected function getUpdateValidator($putData)
    {
        Validator::resolver(function($translator, $data, $rules, $messages) {
            return new RouteValidator($translator, $data, $rules, $messages);
        });

        $validate = Validator::make($putData, [
            'remark' => 'max:255',
            'sights' => 'array|sights'
        ]);

        return $validate;
    }

    /**
     * 构建日程更新的数据
     *
     * @param array $putData 提交的更新数据
     * @return null
     */
    protected function buildUpdateQuery($putData)
    {
        $keys = ['remark'];
        $updateQuery = null;
        foreach ($keys as $key) {
            if ( isset($putData['remark']) ) {
                $updateQuery['daily.$.'. $key] = $putData[$key];
            }
        }
        return $updateQuery;
    }

    /**
     * 更新日程数据
     *
     * @param int $routeId 所属路线的id
     * @param int $dailyId 待更新的日程的id
     * @param array $putData 更新的数据
     * @return int $effectRow 返回受影响的行数
     */
    protected function updateDaily($routeId, $dailyId, $putData)
    {
        $updateQuery = $this->buildUpdateQuery($putData);
        // 提交数据为空，则直接跳过
        if ( is_null($updateQuery) ) {
            return 1;
        } else {
            $effectRow = \DB::collection('routes')->where('_id', $routeId)
                ->where('creator_id', \Auth::user()['_id'])
                ->where('daily._id', intval($dailyId))
                ->update($updateQuery);
            return $effectRow;
        }
    }

    /**
     * 处理更新日程景点的逻辑
     *
     * @param int $routeId 路线的Id
     * @param int $dailyId 日程的id
     * @param array $requestSight 更新的景点数据 
     * @return mixed $respData 返回具体的处理状态或者时受影响的行数
     */
    protected function updateSights($routeId, $dailyId, $requestSight)
    {
        // 若景点更新数据为空，则直接返回空数组
        if ( is_null($requestSight) ) {
            return [];
        }

        $lock = Route::findOrFail($routeId)['lock'];
        if ( $lock ) {
            // 当文档被锁定时，不能进行操作
            $respData['error'] = 'Locking';
            $respData['status'] = 409;
        } else {
            // 进行文档锁定
            Route::where('_id', $routeId)->where('creator_id', \Auth::user()['_id'])
                ->update(['lock' => true]);

            // 获取对应日程的原始景点数据
            $dailyData = Route::where('_id', $routeId)
                ->where('daily._id', intval($dailyId))
                ->get(['daily.$.sights'])->first();
            $originSights = head($dailyData['daily'])['sights'];

            // 分析景点集合，得出不变的景点、新增的景点、删除的景点
            $sightsData = $this->resolveSightData($originSights, $requestSight);

            // 合并不变的和新增的景点，作为新的景点列表
            $newSightData = array_merge($sightsData['common'], $sightsData['add']);
            $effectRow['updateRoute'] = Route::where('_id', $routeId)->where('creator_id', \Auth::user()['_id'])
                ->where('daily._id', intval($dailyId))
                ->update(['daily.$.sights' => $newSightData]);

            // 获取该路线的全部景点
            $targetRoute = Route::findOrFail($routeId);
            $sights = [];
            foreach ($targetRoute['daily'] as $daily) {
                $sights = array_merge($sights, $daily['sights']);
            }

            // 检索所选路线的景点，判断被删除的景点是否还存在该路线的某一日程
            $unlinkSightIds = [];
            foreach( $sightsData['delete'] as $currSight ) {
                $exist = false;
                // 不检查没有 sight_id 字段的景点
                if ( isset($currSight['sights_id']) ) {
                    foreach ( $sights as $sight ) {
                        $longitude = $sight['loc']['coordinates'][0] === $currSight['loc']['coordinates'][0];
                        $latitude = $sight['loc']['coordinates'][1] === $currSight['loc']['coordinates'][1];
                        if ( $longitude && $latitude ) {
                            $exist = true;
                            break;
                        }
                    }
                    if ( !$exist ) {
                        array_push($unlinkSightIds, $currSight['sights_id']);
                    }
                }
            }

            // 删除景点表中的路线关联
            $effectRow['unlinkSight'] = Sight::whereIn('_id', $unlinkSightIds)->pull('routes', $routeId);

            // 在对应的景点中增加对该路线的关联（如果该关联不存在的话）
            $linkSightIds = array_fetch($sightsData['add'], 'sights_id');
            $effectRow['linkSight'] = \DB::collection('sights')->whereIn('_id', $linkSightIds)
                ->update([
                    '$addToSet' => ['routes' => $routeId]
                ]);

            // 解除对文档的锁定
            Route::where('_id', $routeId)->where('creator_id', \Auth::user()['_id'])
                ->update(['lock' => false]);

            $respData['effectRow'] = $effectRow;
        }
        return $respData;
    }

    /**
     * 分析得出不变的、新增的、删除的景点数据
     *
     * @param array $originSights 原始的景点列表
     * @param array $requestSight 提交的景点列表
     * @return array $sightsData 关联数组，包含不变的、新增的、删除的景点数据
     */
    private function resolveSightData($originSights, $requestSight)
    {
        // 判断两个景点是否相等，相等返回false，不相等返回true
        $compareSights = function ($lSight, $rSight)
        {
            $longitudeDiff = $lSight['loc']['coordinates'][0] - $rSight['loc']['coordinates'][0];
            $latitudeDiff = $lSight['loc']['coordinates'][1] - $rSight['loc']['coordinates'][1];

            $result = $longitudeDiff + $latitudeDiff;
            // 针对并集操作，若景点坐标一样，但原始数据缺少 sight_id 则应以新数据为准
            if ( $result == 0 && isset($lSight['sight_id']) ) {
                return false;
            } else {
                return true;
            }
        };

        // 注意：此处并集操作，原始数组需作为第一参数，避免覆盖更新 sight_id 的新景点数据

        // 将原始的景点和提交的景点做并集操作，求出不变的景点
        $sightsData['common'] = array_uintersect_assoc($originSights, $requestSight, $compareSights);
        // 将提交的景点和不变的景点做差集操作，求出新增的景点
        $sightsData['add'] = array_udiff_assoc($requestSight, $sightsData['common'], $compareSights);
        // 将原始的景点和不变的景点做差集操作，求出删除的景点
        $sightsData['delete'] = array_udiff_assoc($originSights, $sightsData['common'], $compareSights);

        return $sightsData;
    }
}