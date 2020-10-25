<?php
/**
 * Created by PhpStorm.
 * User: wanghui
 * Date: 2018/6/4
 * Time: 下午1:20
 */

namespace App\Http\Controllers\Admin\Member;

use App\Http\Controllers\Admin\BaseController;
use App\Models\Member;
use App\Models\MemberGroup;
use App\Models\MemberProfile;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Validator;

/**
 * 会员
 * Class ExpressCompanyController
 * @package App\Http\Controllers\Admin\System
 */
class MemberController extends BaseController
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
        $group_id = (int)$request->input('group_id');
        $username = $request->input('username');
        $nickname = $request->input('nickname');
        if ($group_id) $where[] = array('group_id', $group_id);
        if ($username) $where[] = array('username', $username);
        if ($nickname) $where[] = array('nickname', $nickname);

        $res_list = Member::select('id', 'username', 'nickname', 'headimg', 'group_id', 'status', 'created_at')
            ->where($where)
            ->orderBy('id', 'desc')
            ->offset($offset)
            ->limit($limit)
            ->get();
        if ($res_list->isEmpty()) {
            api_error(__('admin.content_is_empty'));
        }
        $m_ids = $group_ids = array();
        foreach ($res_list as $value) {
            $m_ids[] = $value['id'];
            $group_ids[] = $value['group_id'];
        }
        if ($m_ids) {
            $profile_res = MemberProfile::whereIn('member_id', array_unique($m_ids))->select('member_id', 'full_name', 'tel', 'email', 'sex')->get();
            if (!$profile_res->isEmpty()) {
                $profile = array_column($profile_res->toArray(), null, 'member_id');

            }
        }
        if ($group_ids) {
            $group = MemberGroup::whereIn('id', array_unique($group_ids))->pluck('title', 'id');
        }
        $data_list = array();
        foreach ($res_list->toArray() as $value) {
            if (isset($profile[$value['id']])) {
                $value['group_name'] = isset($group[$value['group_id']]) ? $group[$value['group_id']] : '';
                $value['sex'] = MemberProfile::SEX_DESC[$profile[$value['id']]['sex']];
                $_item = array_merge($value, $profile[$value['id']]);
                $data_list[] = $_item;
            }
        }
        $total = Member::where($where)->count();
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
            $data = Member::find($id);
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
                Rule::unique('member')->ignore($id)
            ],
            'nickname' => 'required',
            'group_id' => 'required|numeric',
            'email' => 'nullable|email',
            'sex' => 'numeric',
            'prov_id' => 'nullable|numeric',
            'city_id' => 'nullable|numeric',
            'area_id' => 'nullable|numeric'
        ], [
            'username.required' => '用户名不能为空',
            'username.unique' => '用户已经存在',
            'nickname.required' => '昵称不能为空',
            'group_id.required' => '用户组不能为空',
            'group_id.numeric' => '用户组只能是数字',
            'email.email' => 'email格式错误',
            'sex.numeric' => '性别只能是数字',
            'prov_id.numeric' => '省份只能是数字',
            'city_id.numeric' => '城市只能是数字',
            'area_id.numeric' => '地区只能是数字',
        ]);
        $error = $validator->errors()->all();
        if ($error) {
            api_error(current($error));
        }

        $member_data = array();
        foreach ($request->only(['username', 'nickname', 'headimg', 'group_id']) as $key => $value) {
            $member_data[$key] = ($value || $value == 0) ? $value : null;
        }

        $profile_data = array();
        foreach ($request->only(['full_name', 'tel', 'email', 'sex', 'prov_id', 'city_id', 'area_id']) as $key => $value) {
            $profile_data[$key] = ($value || $value == 0) ? $value : null;
        }

        //判断密码是否有了
        $password = $request->input('password');
        if (!$id && !$password) {
            api_error(__('密码不能为空'));
        }
        if ($password) {
            $member_data['password'] = $password;
        }
        $res = Member::saveData($member_data, $profile_data, $id);
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
            $res = Member::whereIn('id', $ids)->update(['status' => $status]);
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
        $res = Member::whereIn('id', $ids)->delete();
        if ($res) {
            return $this->success();
        } else {
            api_error(__('admin.del_error'));
        }
    }
}
