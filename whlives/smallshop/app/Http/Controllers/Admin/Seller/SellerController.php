<?php
/**
 * Created by PhpStorm.
 * User: wanghui
 * Date: 2018/6/4
 * Time: 下午1:20
 */

namespace App\Http\Controllers\Admin\Seller;

use App\Http\Controllers\Admin\BaseController;
use App\Models\Seller;
use App\Models\SellerProfile;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Validator;

/**
 * 商家管理
 * Class ExpressCompanyController
 * @package App\Http\Controllers\Admin\System
 */
class SellerController extends BaseController
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
        $username = $request->input('username');
        $title = $request->input('title');

        if ($username) {
            $m_id = Seller::where('username', $username)->value('id');
            if ($m_id) {
                $where[] = array('seller_id', $m_id);
            } else {
                api_error(__('admin.content_is_empty'));
            }
        }
        if ($title) {
            $m_id = Seller::where('title', $title)->value('id');
            if ($m_id) {
                $where[] = array('seller_id', $m_id);
            } else {
                api_error(__('admin.content_is_empty'));
            }
        }
        $res_list = SellerProfile::select('seller_id', 'tel', 'email')
            ->where($where)
            ->orderBy('seller_id', 'desc')
            ->offset($offset)
            ->limit($limit)
            ->get();
        if ($res_list->isEmpty()) {
            api_error(__('admin.content_is_empty'));
        }
        $seller_ids = array();
        foreach ($res_list as $value) {
            $seller_ids[] = $value['seller_id'];
        }
        if ($seller_ids) {
            $seller_res = Seller::whereIn('id', array_unique($seller_ids))->select('id', 'username', 'title', 'image', 'level', 'status', 'created_at')->get();
            if (!$seller_res->isEmpty()) {
                $seller = array_column($seller_res->toArray(), null, 'id');
            }
        }
        $data_list = array();
        foreach ($res_list->toArray() as $value) {
            if (isset($seller[$value['seller_id']])) {
                $_item = array_merge($value, $seller[$value['seller_id']]);
                $data_list[] = $_item;
            }
        }
        $total = SellerProfile::where($where)->count();
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
            $data = Seller::find($id);
            $data = array_merge($data->toArray(), $data->profile->toArray());
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
            'username' => [
                'required',
                Rule::unique('seller')->ignore($id)
            ],
            'title' => 'required',
            'image' => 'required',
            'invoice' => 'numeric|required',
            'pct' => 'numeric|required|between:0,100',
            'tel' => 'required',
            'email' => 'email',
            'prov_id' => 'nullable|numeric',
            'city_id' => 'nullable|numeric',
            'area_id' => 'nullable|numeric'
        ], [
            'username.required' => '用户名不能为空',
            'username.unique' => '用户已经存在',
            'image.required' => 'logo不能为空',
            'invoice.numeric' => '发票只能是数字',
            'invoice.required' => '发票不能为空',
            'pct.numeric' => '手续费只能是数字',
            'pct.required' => '手续费不能为空',
            'pct.between' => '手续费区间错误',
            'tel.required' => '联系电话不能为空',
            'email.email' => 'email格式错误',
            'prov_id.numeric' => '省份只能是数字',
            'city_id.numeric' => '城市只能是数字',
            'area_id.numeric' => '地区只能是数字',
        ]);
        $error = $validator->errors()->all();
        if ($error) {
            api_error(current($error));
        }

        $seller_data = array();
        foreach ($request->only(['username', 'title', 'image', 'invoice', 'pct']) as $key => $value) {
            $seller_data[$key] = ($value || $value == 0) ? $value : null;
        }

        $profile_data = array();
        foreach ($request->only(['business_license', 'tel', 'email', 'prov_id', 'city_id', 'area_id', 'address', 'content']) as $key => $value) {
            $profile_data[$key] = ($value || $value == 0) ? $value : null;
        }

        //判断密码是否有了
        $password = $request->input('password');
        if (!$id && !$password) {
            api_error(__('admin.admin_password_empty'));
        }
        if ($password) {
            $seller_data['password'] = $password;
        }
        $res = Seller::saveData($seller_data, $profile_data, $id);
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
            $res = Seller::whereIn('id', $ids)->update(['status' => $status]);
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
        if (in_array(1, $ids)) {
            api_error(__('admin.default_seller_no_del'));
        }
        $res = Seller::whereIn('id', $ids)->delete();
        if ($res) {
            return $this->success();
        } else {
            api_error(__('admin.del_error'));
        }
    }

    /**
     * 获取下拉列表
     * @param Request $request
     * @return array
     * @throws \App\Exceptions\ApiException
     */
    public function select(Request $request)
    {
        $seller = Seller::where('status', Seller::STATUS_ON)
            ->pluck('title', 'id');
        return $this->success($seller);
    }
}
