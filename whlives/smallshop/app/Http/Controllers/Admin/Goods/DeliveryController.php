<?php
/**
 * Created by PhpStorm.
 * User: wanghui
 * Date: 2018/6/4
 * Time: 下午1:20
 */

namespace App\Http\Controllers\Admin\Goods;

use App\Http\Controllers\Admin\BaseController;
use App\Models\Delivery;
use Illuminate\Http\Request;
use Validator;

/**
 * 配送方式
 * Class ExpressCompanyController
 * @package App\Http\Controllers\Admin\System
 */
class DeliveryController extends BaseController
{
    /**
     * 列表获取
     * @param Request $request
     * @return array
     * @throws \App\Exceptions\ApiException
     */
    public function index(Request $request)
    {
        list($page, $limit, $offset) = get_page_params();
        //搜索
        $where = array();
        $title = $request->input('title');
        $seller_id = (int)$request->input('seller_id');
        if ($title) $where[] = array('title', 'like', '%' . $title . '%');
        if ($seller_id) $where[] = array('seller_id', $seller_id);
        $res_list = Delivery::select('id', 'title', 'open_default', 'price_type', 'status', 'created_at')
            ->where($where)
            ->orderBy('id', 'desc')
            ->offset($offset)
            ->limit($limit)
            ->get();
        if ($res_list->isEmpty()) {
            api_error(__('admin.content_is_empty'));
        }
        $data_list = array();
        foreach ($res_list as $value) {
            $_item = $value;
            $_item['open_default'] = Delivery::OPEN_DEFAULT_DESC[$value['open_default']];
            $_item['price_type'] = Delivery::PRICE_TYPE_DESC[$value['price_type']];
            $data_list[] = $_item;
        }
        $total = Delivery::where($where)->count();
        return $this->success($data_list, $total);
    }

    /**
     * 根据id获取信息
     * @param Request $request
     * @return array
     */
    public function detail(Request $request)
    {
        $id = (int)$request->input('id');
        if ($id) {
            $data = Delivery::find($id);
            //分组地区信息
            $select_area_id = array();
            $group_data = array();
            $group_area_id = json_decode($data['group_area_id'], true);
            $group_json = json_decode($data['group_json'], true);
            if ($group_area_id && $group_json) {
                foreach ($group_json as $key => $value) {
                    $value['list_id'] = $key;
                    $value['prov_id'] = $group_area_id[$key];
                    $select_area_id = array_merge($select_area_id, $group_area_id[$key]);
                    $group_data[] = $value;
                }
            }
            $data['group_area_id'] = $select_area_id;
            $data['group_json'] = $group_data;
        }
        if (!$data) {
            api_error(__('admin.content_is_empty'));
        }
        return $this->success($data);
    }

    /**
     * 添加编辑
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
    public function save(Request $request)
    {
        //验证规则
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'type' => 'required|numeric',
            'free_type' => 'required|numeric',
            'free_price' => 'required|price',
            'first_weight' => 'required|numeric',
            'first_price' => 'required|price',
            'second_weight' => 'required|numeric',
            'second_price' => 'required|price',
            'price_type' => 'required|numeric',
        ], [
            'title.required' => '标题不能为空',
            'type.required' => '类型不能为空',
            'type.numeric' => '类型只能是数字',
            'free_type.required' => '包邮类型不能为空',
            'free_type.numeric' => '包邮类型只能是数字',
            'free_price.required' => '包邮金额/件不能为空',
            'free_price.price' => '包邮金额/件格式错误',
            'first_weight.required' => '首重/件数不能为空',
            'first_weight.numeric' => '首重/件数只能是数字',
            'first_price.required' => '首重/件费用不能为空',
            'first_price.price' => '首重/件费用格式错误',
            'second_weight.required' => '续重/件数不能为空',
            'second_weight.numeric' => '续重/件数只能是数字',
            'second_price.required' => '续重/件费用不能为空',
            'second_price.price' => '续重/件费用格式错误',
            'price_type.required' => '费用类型不能为空',
            'price_type.numeric' => '费用类型只能是数字',
        ]);
        $error = $validator->errors()->all();
        if ($error) {
            api_error(current($error));
        }

        $save_data = array();
        foreach ($request->only(['title', 'seller_id', 'type', 'free_type', 'free_price', 'first_weight', 'first_price', 'second_weight', 'second_price', 'price_type', 'open_default', 'content']) as $key => $value) {
            $save_data[$key] = ($value || $value == 0) ? $value : null;
        }
        if (!isset($save_data['open_default'])) $save_data['open_default'] = Delivery::OPEN_DEFAULT_OFF;
        //组装其他地区

        $group_data = array();
        foreach ($request->only(['group_area_id', 'group_type', 'group_free_type', 'group_free_price', 'group_first_weight', 'group_first_price', 'group_second_weight', 'group_second_price']) as $key => $value) {
            $group_data[$key] = ($value || $value == 0) ? $value : null;
        }
        $group_area_id = array();
        $group_json = array();
        if (isset($group_data['group_area_id'])) {
            foreach ($group_data['group_area_id'] as $key => $value) {
                if ($value) {
                    $group_area_id[] = array_values($value);
                    $_item = array(
                        'type' => $group_data['group_type'][$key],
                        'free_type' => $group_data['group_free_type'][$key],
                        'free_price' => $group_data['group_free_price'][$key],
                        'first_weight' => $group_data['group_first_weight'][$key],
                        'first_price' => $group_data['group_first_price'][$key],
                        'second_weight' => $group_data['group_second_weight'][$key],
                        'second_price' => $group_data['group_second_price'][$key],
                    );
                    $group_json[] = $_item;
                }
            }
        }
        $save_data['group_area_id'] = json_encode($group_area_id);
        $save_data['group_json'] = json_encode($group_json);
        $save_data['status'] = Delivery::STATUS_ON;

        $id = (int)$request->input('id');
        if ($id) {
            $res = Delivery::where('id', $id)->update($save_data);
        } else {
            $result = Delivery::create($save_data);
            $res = $result->id;
        }
        if ($res) {
            return $this->success();
        } else {
            api_error(__('admin.save_error'));
        }
    }

    /**
     * 修改状态
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function status(Request $request)
    {
        $ids = $this->checkBatchId();
        $status = (int)$request->input('status');
        if ($ids && isset($status)) {
            $res = Delivery::whereIn('id', $ids)->update(['status' => $status]);
            if ($res) {
                return $this->success();
            } else {
                api_error(__('admin.fail'));
            }
        } else {
            api_error(__('admin.invalid_params'));
        }
    }

    /**
     * 删除数据
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(Request $request)
    {
        $ids = $this->checkBatchId();
        $res = Delivery::whereIn('id', $ids)->delete();
        if ($res) {
            return $this->success();
        } else {
            api_error(__('admin.del_error'));
        }
    }

    /**
     * 修改单个字段值
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function fieldUpdate(Request $request)
    {
        $id = (int)$request->input('id');
        $field = $request->input('field');
        $field_value = $request->input('field_value');
        $field_arr = ['position'];//支持修改的字段
        if (in_array($field, $field_arr)) {
            if ($field == 'position') $field_value = (int)$field_value;
            if ($id && $field && $field_value) {
                $res = Delivery::where('id', $id)->update([$field => $field_value]);
                if ($res) {
                    return $this->success();
                } else {
                    api_error(__('admin.fail'));
                }
            }
        }
        api_error(__('admin.invalid_params'));
    }

    /**
     * 获取下拉列表
     * @param Request $request
     * @return array
     * @throws \App\Exceptions\ApiException
     */
    public function select(Request $request)
    {
        $seller_id = (int)$request->input('seller_id');
        $result = array();
        if ($seller_id) {
            $result = Delivery::where('seller_id', $seller_id)->orderBy('id', 'desc')
                ->pluck('title', 'id');
        }
        return $this->success($result);
    }
}