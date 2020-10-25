<?php
/**
 * Created by PhpStorm.
 * User: wanghui
 * Date: 2018/6/4
 * Time: 下午1:20
 */

namespace App\Http\Controllers\Seller\Seller;

use App\Http\Controllers\Seller\BaseController;
use App\Models\Areas;
use App\Models\Seller;
use App\Models\SellerAddress;
use Illuminate\Http\Request;
use Validator;

/**
 * 商家地址管理
 * Class ExpressCompanyController
 * @package App\Http\Controllers\Admin\System
 */
class AddressController extends BaseController
{
    /**
     * 列表获取
     * @param Request $request
     * @return array
     * @throws \App\Exceptions\ApiException
     */
    public function index(Request $request)
    {
        $seller_id = $this->getUserId();
        list($page, $limit, $offset) = get_page_params();
        //搜索
        $where = array();
        $where[] = array('seller_id', $seller_id);
        $res_list = SellerAddress::select('id', 'full_name', 'tel', 'prov_name', 'city_name', 'area_name', 'address', 'default')
            ->where($where)
            ->orderBy('created_at', 'desc')
            ->offset($offset)
            ->limit($limit)
            ->get();
        if ($res_list->isEmpty()) {
            api_error(__('admin.content_is_empty'));
        }
        $total = SellerAddress::where($where)->count();
        return $this->success($res_list, $total);
    }

    /**
     * 根据id获取信息
     * @param Request $request
     * @return array
     */
    public function detail(Request $request)
    {
        $seller_id = $this->getUserId();
        $id = (int)$request->input('id');
        if ($id) {
            $data = SellerAddress::where(['id' => $id, 'seller_id' => $seller_id])->first();
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
        $id = (int)$request->input('id');
        //验证规则
        $validator = Validator::make($request->all(), [
            'full_name' => 'required',
            'tel' => 'required',
            'prov_id' => 'nullable|numeric',
            'city_id' => 'nullable|numeric',
            'area_id' => 'nullable|numeric',
            'address' => 'required'
        ], [
            'full_name.required' => '发货人不能为空',
            'tel.required' => '电话不能为空',
            'prov_id.numeric' => '省份只能是数字',
            'city_id.numeric' => '城市只能是数字',
            'area_id.numeric' => '地区只能是数字',
            'address.required' => '详细地址-不能为空',
        ]);
        $error = $validator->errors()->all();
        if ($error) {
            api_error(current($error));
        }

        $seller_id = $this->getUserId();
        $save_data = array();
        foreach ($request->only(['full_name', 'tel', 'prov_id', 'city_id', 'area_id', 'address', 'default']) as $key => $value) {
            $save_data[$key] = ($value || $value == 0) ? $value : null;
        }
        $save_data['prov_name'] = Areas::getAreaName($save_data['prov_id']);
        $save_data['city_name'] = Areas::getAreaName($save_data['city_id']);
        $save_data['area_name'] = Areas::getAreaName($save_data['area_id']);
        //如果是设置默认先把其他的全部取消默认
        if ($save_data['default'] == 1) {
            SellerAddress::where('seller_id', $seller_id)->update(['default' => SellerAddress::DEFAULT_OFF]);
        }

        if ($id) {
            $res = SellerAddress::where(['id' => $id, 'seller_id' => $seller_id])->update($save_data);
        } else {
            $save_data['seller_id'] = $seller_id;
            $result = SellerAddress::create($save_data);
            $res = $result->id;
        }
        if ($res) {
            return $this->success();
        } else {
            api_error(__('admin.save_error'));
        }
    }

    /**
     * 删除数据
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(Request $request)
    {
        $seller_id = $this->getUserId();
        $ids = $this->checkBatchId();
        $res = SellerAddress::where('seller_id', $seller_id)->whereIn('id', $ids)->delete();
        if ($res) {
            return $this->success();
        } else {
            api_error(__('admin.del_error'));
        }
    }

    /**
     * 地址列表
     * @param Request $request
     * @return array
     * @throws \App\Exceptions\ApiException
     */
    public function select(Request $request)
    {
        $seller_id = $this->getUserId();
        $where = array(
            'seller_id' => $seller_id
        );
        $res_list = SellerAddress::select('id', 'full_name', 'tel', 'prov_name', 'city_name', 'area_name', 'address')
            ->where($where)
            ->orderBy('default', 'desc')
            ->orderBy('id', 'asc')
            ->get();
        $data_list = array();
        foreach ($res_list as $value) {
            $data_list[$value['id']] = $value['full_name'] . ' ' . $value['tel'] . ' ' . $value['address'];
        }
        return $this->success($data_list);
    }
}
