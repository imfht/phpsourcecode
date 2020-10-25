<?php
/**
 * Created by PhpStorm.
 * User: wanghui
 * Date: 2019/02/26
 * Time: 3:33 PM
 */

namespace App\Http\Controllers\V1\Member;

use App\Http\Controllers\V1\BaseController;
use App\Models\Address;
use App\Models\Areas;
use Illuminate\Http\Request;

class AddressController extends BaseController
{
    /**
     * 我的地址
     * @return array
     * @throws \App\Exceptions\ApiException
     */
    public function index()
    {
        $m_id = $this->getUserId();
        $res_list = Address::select('id', 'full_name', 'tel', 'prov_name', 'city_name', 'area_name', 'address', 'default')->where('m_id', $m_id)->get();
        if ($res_list->isEmpty()) {
            api_error(__('api.content_is_empty'));
        }
        return $this->success($res_list);
    }
    /**
     * 地址添加
     * @param Request $request
     */
    public function add(Request $request)
    {
        $m_id = $this->getUserId();
        $full_name = $request->post('full_name');
        $tel = $request->post('tel');
        $prov_id = (int)$request->post('prov_id');
        $city_id = (int)$request->post('city_id');
        $area_id = (int)$request->post('area_id');
        $address = $request->post('address');
        $default = (int)$request->post('default');

        if (!$full_name || !$tel || !check_mobile($tel) || !$prov_id || !$city_id || !$address) {
            api_error(__('api.missing_params'));
        }

        $address = array(
            'm_id' => $m_id,
            'full_name' => $full_name,
            'tel' => $tel,
            'prov_id' => $prov_id,
            'prov_name' => Areas::getAreaName($prov_id),
            'city_id' => $city_id,
            'city_name' => Areas::getAreaName($city_id),
            'area_id' => $area_id,
            'area_name' => $area_id ? Areas::getAreaName($area_id) : '',
            'address' => $address,
            'default' => $default
        );
        //如果是设置默认先把其他的全部取消默认
        if ($default == 1) {
            Address::where('m_id', $m_id)->update(['default' => Address::DEFAULT_OFF]);
        }
        $res = Address::create($address);
        if ($res) {
            return $this->success(true);
        } else {
            api_error(__('api.fail'));
        }
    }

    /**
     * 获取地址信息
     * @param Request $request
     * @return array
     * @throws \App\Exceptions\ApiException
     */
    public function detail(Request $request)
    {
        $m_id = $this->getUserId();
        $id = (int)$request->post('id');
        if (!$id) {
            api_error(__('api.missing_params'));
        }
        $address = Address::select('id', 'full_name', 'tel', 'prov_id', 'city_id', 'area_id', 'address', 'default')->where(['id' => $id, 'm_id' => $m_id])->first();
        if ($address) {
            return $this->success($address);
        } else {
            api_error(__('api.address_error'));
        }
    }

    /**
     * 地址编辑
     * @param Request $request
     */
    public function edit(Request $request)
    {
        $m_id = $this->getUserId();
        $id = (int)$request->post('id');
        $full_name = $request->post('full_name');
        $tel = $request->post('tel');
        $prov_id = (int)$request->post('prov_id');
        $city_id = (int)$request->post('city_id');
        $area_id = (int)$request->post('area_id');
        $address = $request->post('address');
        $default = (int)$request->post('default');

        if (!$id || !$full_name || !$tel || !check_mobile($tel) || !$prov_id || !$city_id || !$address) {
            api_error(__('api.missing_params'));
        }

        $address = array(
            'full_name' => $full_name,
            'tel' => $tel,
            'prov_id' => $prov_id,
            'prov_name' => Areas::getAreaName($prov_id),
            'city_id' => $city_id,
            'city_name' => Areas::getAreaName($city_id),
            'area_id' => $area_id,
            'area_name' => $area_id ? Areas::getAreaName($city_id) : '',
            'address' => $address,
            'default' => $default
        );
        //如果是设置默认先把其他的全部取消默认
        if ($default == 1) {
            Address::where('m_id', $m_id)->update(['default' => Address::DEFAULT_OFF]);
        }
        $res = Address::where(['id' => $id, 'm_id' => $m_id])->update($address);
        if ($res) {
            return $this->success(true);
        } else {
            api_error(__('api.fail'));
        }
    }

    /**
     * 删除地址信息
     * @param Request $request
     * @return array
     * @throws \App\Exceptions\ApiException
     */
    public function delete(Request $request)
    {
        $m_id = $this->getUserId();
        $id = (int)$request->post('id');
        if (!$id) {
            api_error(__('api.missing_params'));
        }
        $res = Address::where(['id' => $id, 'm_id' => $m_id])->delete();
        if ($res) {
            return $this->success(true);
        } else {
            api_error(__('api.fail'));
        }
    }

    /**
     * 设置默认
     * @param Request $request
     * @return array
     * @throws \App\Exceptions\ApiException
     */
    public function default(Request $request)
    {
        $m_id = $this->getUserId();
        $id = (int)$request->post('id');
        if (!$id) {
            api_error(__('api.missing_params'));
        }
        Address::where('m_id', $m_id)->update(['default' => Address::DEFAULT_OFF]);
        $res = Address::where(['id' => $id, 'm_id' => $m_id])->update(['default' => 1]);
        if ($res) {
            return $this->success(true);
        } else {
            api_error(__('api.fail'));
        }
    }

}
