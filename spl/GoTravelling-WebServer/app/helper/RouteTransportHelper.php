<?php
/**
 * Created by PhpStorm.
 * User: zero
 * Date: 15-5-7
 * Time: 下午8:19
 */
namespace Helper;

use App\Services\RouteValidator;
use Illuminate\Routing\Route;
use Validator;

trait RouteTransportHelper
{
    /**
     * 获取 store 的数据验证器
     *
     * @param array $postData 请求提交的新建数据
     * @return \Illuminate\Validation\Validator
     */
    protected function getStoreValidator($postData)
    {
        Validator::resolver(function($translator, $data, $rules, $messages){
           return new RouteValidator($translator, $data, $rules, $messages);
        });

        $validate = Validator::make($postData, [
            'from_name' => 'required|max:255',
            'from_sight_id' => 'sight_id',
            'from_loc' => 'required|array|loc',
            'to_name' => 'required|max:255',
            'to_sight_id' => 'sight_id',
            'to_loc' => 'required|array|loc',
            'description' => 'required|desc',
            'prize' => 'required|numeric|min:0',
            'consuming' => 'required|numeric|min:0'
        ]);

        return $validate;
    }

    /**
     * 保存新建的交通方式
     *
     * @param int $routeId 路线的id
     * @param array $transportData 新建的交通方式数据
     * @return int $effectRow 受影响的行数
     */
    protected function storeTransport($routeId, $transportData)
    {
        $transportData['_id'] = time();

        // policy 字段不一定存在，默认值为空数组
        if ( isset($transportData['description']['policy']) ) {
            $transportData['description']['policy'] = $this->buildPolicy($transportData['description']['policy']);
        } else {
            $transportData['description']['policy'] = [];
        }

        $effectRow = \DB::collection('routes')->where('_id', $routeId)
            ->where('creator_id', \Auth::user()['_id'])
            ->push('transportation', $transportData);
        return $effectRow;
    }


    /**
     * 格式化交通方式描述的策略字段如下：
     * {
     *     "label": "策略的标签，中文，外部显示",
     *     "name": "策略的名称，英文，内部使用",
     * }
     *
     * @param array $policy 交通方式描述的策略，英文描述
     * @return array 格式化的交通方式策略
     */
    protected function buildPolicy($policy)
    {
        if ( !is_array($policy) ) {
            $policy = array($policy);
        }

        // 交通方式的策略的 name 和对应的 label
        $policyMap = [
            'least_block' => '躲避拥堵', 'least_distance' => '最短距离',
            'least_cost' => '最小费用', 'least_time' => '时间优先',
            'least_walk' => '最小步行距离', 'least_exchange' => '最少换乘',
            'avoid_subway' => '不含地铁',
        ];
        $policyList = [];
        foreach ($policy as $item) {
            $temp['name'] = $item;
            if ( array_key_exists($item, $policyMap) ) {
                $temp['label'] = $policyMap[ $item ];
            } else {
                $temp['label'] = '';
            }
            array_push($policyList, $temp);
        }

        return $policyList;
    }

    /**
     * 获取更新交通方式的验证器
     *
     * @param array $putData 请求提交的数据
     * @return \Illuminate\Validation\Validator
     */
    protected function getUpdateValidator($putData)
    {
        Validator::resolver(function($translator, $data, $rules, $messages){
            return new RouteValidator($translator, $data, $rules, $messages);
        });

        $validate = Validator::make($putData, [
            'from_name' => 'max:255',
            'from_sight_id' => 'sight_id',
            'from_loc' => 'array|loc',
            'to_name' => 'max:255',
            'to_sight_id' => 'sight_id',
            'to_loc' => 'array|loc',
            'description' => 'desc',
            'prize' => 'numeric|min:0',
            'consuming' => 'numeric|min:0'
        ]);

        // 当 from_name 或 from_loc 存在时，给２个字段增加 required 验证规则
        $validate->sometimes(['from_name', 'from_loc'], 'required', function($input){
            return !( is_null($input->get('from_name', null)) && is_null($input->get('from_loc', null)) );
        });
        // 同上
        $validate->sometimes(['to_name', 'to_loc'], 'required', function($input){
            return !( is_null($input->get('to_name', null)) && is_null($input->get('to_loc', null)) );
        });

        return $validate;
    }

    /**
     * 构建用于更新交通方式的数据
     *
     * @param array $putData 请求提交的交通方式数据
     * @return null| array $updateData 若提交数据为空，则返回 null，否则返回构造好的数据
     */
    protected function buildUpdateData($putData)
    {
        $putData['description']['policy'] = $this->buildPolicy($putData['description']['policy']);
//        dd($putData);
        $index = 'transportation.$.';
        $updateData = null;
        foreach ($putData as $key => $data) {
            if ( !is_null($data) ) {
                $updateData[ $index. $key ] = $data;
            }
        }
        return $updateData;
    }

    /**
     * 更新交通方式信息
     *
     * @param int $routeId 所属路线的id
     * @param int $transportId 交通方式的id
     * @param array $transportData 已构建的交通方式数据
     * @return int $effectRow 受影响的行数
     */
    protected function updateTransport($routeId, $transportId, $transportData)
    {
        $updateData = $this->buildUpdateData($transportData);
        // 若更新数据为空，则直接返回０
        if ( is_null($updateData) ) {
            return 1;
        } else {
            $effectRow = \DB::collection('routes')->where('_id', $routeId)
                ->where('creator_id', \Auth::user()['_id'])
                ->where('transportation._id', intval($transportId))
                ->update($updateData);
            return $effectRow;
        }
    }
}