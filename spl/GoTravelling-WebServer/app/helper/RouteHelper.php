<?php
/**
 * Created by PhpStorm.
 * User: spatra
 * Date: 15-4-29
 * Time: 下午8:39
 */

namespace Helper;

use App\Services\RouteValidator;
use Validator;
use App\Route;
use \Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use \DB;

trait RouteHelper
{
    /**
     * 根据请求的查询字符串条件，筛选路线数据
     *
     * @param $request　
     * @return array|\Illuminate\Database\Eloquent\Collection|null|static[]
     */
    protected function getRouteListOnCond(Request $request)
    {
        $respData = null;
        $currentType = $request->get('type', 'latest');

        switch($currentType)
        {
            // 当前用户的路线列表
            case 'mine':
                $respData = Route::where('creator_id', \Auth::user()['_id'])
                    ->orderBy('created_at', 'desc')
                    ->get(['_id', 'name', 'description', 'created_at', 'status', 'tag']);
                break;
            //按关键字（name和description）来查找路线
            case 'keyword':
                $respData = Route::getRoutesOnKeyword($request->get('query', null));
                break;
            //按景点(景点的name、description、address)来查找路线
            case 'sight':
                $respData = Route::getRoutesOnSights($request->get('query', null));
                break;
            // 最新的路线列表
            case 'latest':
                $respData = Route::orderBy('created_at', 'desc')
                    ->get(['_id', 'creator_id', 'name', 'description', 'created_at', 'tag']);
                break;
            // 按路线标签查找路线
            case 'tag':
                $respData = Route::getRoutesOnTag($request->get('query', null));
                break;
            default:
                \App::abort(404);
                break;
        }

        // 将路线列表转换为数组
        if( $respData instanceof Arrayable ){
            $respData = $respData->toArray();
        }

        // 判断是否需要分页
        if ( $request->has('per_page') && $request->has('offset') ) {
            $respData = PaginateHelper::paginateArrayData(
                $respData, count($respData),
                $request->get('per_page'), $request->get('offset')
            )->toArray();

            ($currentType === 'mine') or Route::addCreatorField($respData['data']);
        } else {
            ($currentType === 'mine') or Route::addCreatorField($respData);
        }

        return $respData;
    }

    /**
     * 获取用于验证 store 方法的请求数据的验证器 
     *
     * @param array $postData 请求提交的数据
     * @return \Illuminate\Validation\Validator
     */
    protected function getStoreValidator($postData)
    {
        $validate = Validator::make($postData, [
            'name' => 'required|max:255',
            'description' => 'max:255'
        ]);

        return $validate;
    }

    /**
     * 获取 show 的显示数据，可根据当前用户是不是路线的创建者而返回不同的显示数据
     *
     * @param int $routeId 路线的id
     * @return array
     */
    protected function getShowData($routeId)
    {
        $target = Route::with('creator')->findOrFail($routeId);
        // 暂定只有photo字段会受到isPublic字段的是否公开的影响
        $privateKeys = ['photo'];
        if ( $target['creator_id'] <> \Auth::user()['_id'] && false === $target['isPublic'] ) {
            foreach( $privateKeys as $key ) {
                unset($target[ $key ]);
            }
        }

        return $target->toArray();
    }

    /**
     * 构建新建路线的数据
     *
     * @param array $postData 请求提交的数据
     * @return mixed
     */
    protected function buildStoreData($postData)
    {
        $newRoute['name'] = $postData['name'];
        $newRoute['creator_id'] = \Auth::user()['_id'];
        $newRoute['tag'] = null;
        $newRoute['status'] = 'planning';
        $newRoute['isPublic'] = false;
        $newRoute['daily'] = [];
        $newRoute['transportation'] = [];

        if( !isset($postData['description']) ){
            $postData['description'] = '';
        }
        $newRoute['description'] = $postData['description'];

        return $newRoute;
    }

    /**
     * 保存新建的路线数据
     *
     * @param array $routeData 新建的路线数据
     * @return static
     */
    protected function storeRoute($routeData)
    {
        $newRoute = Route::create($routeData);

        return $newRoute;
    }

    /**
     * 获取 update 的数据验证器
     *
     * @param array $putData 请求提交的更新的数据
     * @return \Illuminate\Validation\Validator
     */
    protected function getUpdateValidator($putData)
    {
        Validator::resolver(function($translator, $data, $rules, $messages) {
            return new RouteValidator($translator, $data, $rules, $messages);
        });

        $validate = Validator::make($putData, [
            'name' => 'max:255',
            'description' => 'max:255',
            'status' => 'in:planning,travelling,finished',
            'isPublic' => 'boolean',
            'tag' => 'tag'
        ]);

        return $validate;
    }

    /**
     * 构建路线更新数据
     *
     * @param array $postData 请求提交的路线数据
     * @return array $routeData 构建好的路线更新数据
     */
    protected function buildUpdateData($postData)
    {
        $routeData = null;
        $keys = ['name', 'description', 'status', 'isPublic', 'tag'];
        foreach ($keys as $key) {
            if ( isset($postData[$key]) ) {
                $routeData[$key] = $postData[$key];
            }
        }
        return $routeData;
    }

    /**
     * 更新路线信息
     *
     * @param int $routeId 路线的id
     * @param array $routeData 请求提交的更新信息
     * @return int $effectRow
     */
    protected function updateRoute($routeId, $routeData)
    {
        $routeData = $this->buildUpdateData($routeData);
        // 若提交数据为空，则直接跳过
        if ( is_null($routeData) ) {
            return 1;
        } else {
            $effectRow = \DB::collection('routes')->where('_id', $routeId)
                ->where('creator_id', \Auth::user()['_id'])
                ->update($routeData);
            return $effectRow;
        }

    }

}
