<?php
/**
 * Created by PhpStorm.
 * User: wanghui
 * Date: 2018/5/5
 * Time: 上午10:17
 */

namespace App\Http\Controllers\Admin\System;

use App\Http\Controllers\Admin\BaseController;
use App\Models\Config;
use Illuminate\Support\Facades\Redis;
use Illuminate\Validation\Rule;
use Validator;
use Illuminate\Http\Request;

/**
 * 系统设置
 * Class ConfigController
 * @package App\Http\Controllers\Admin\System
 */
class ConfigController extends BaseController
{
    /**
     * 站点设置
     * @param Request $request
     */
    public function index(Request $request)
    {
        $config = array();
        $res_config = Config::where([])->orderBy('position', 'asc')->orderBy('id', 'asc')->get();
        if (!$res_config->isEmpty()) {
            foreach ($res_config as $val) {
                if (in_array($val['input_type'], ['radio', 'select'])) {
                    $val['select_value'] = explode(',', $val['select_value']);
                }
                $config[$val['tab_name']][] = $val;
            }
        }
        return $this->success($config);
    }

    /**
     * 站点设置保存
     * @param Request $request
     */
    public function update(Request $request)
    {
        $config = $request->input('config');
        if ($config) {
            foreach ($config as $id => $value) {
                Config::where('id', $id)->update(['value' => $value]);
            }
            $this->refreshCache();
        }
        return $this->success();
    }

    /**
     * 添加配置
     * @param Request $request
     */
    public function save(Request $request)
    {
        //验证规则
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'key_name' => [
                'required',
                Rule::unique('config')->ignore($request->id)
            ],
            'value' => 'required',
            'input_type' => 'required',
            'tab_name' => 'required',
            'position' => 'numeric'
        ], [
            'title.required' => '名称不能为空',
            'key_name.required' => '参数key名称不能为空',
            'key_name.unique' => '参数key名称已经存在',
            'value.required' => '参数值不能为空',
            'input_type.required' => '类型不能为空',
            'tab_name.required' => 'tab名称不能为空',
            'position.numeric' => '排序必须是数字'
        ]);
        $error = $validator->errors()->all();
        if ($error) {
            api_error(current($error));
        }

        $save_data = array();
        foreach ($request->only(['title', 'key_name', 'value', 'input_type', 'tab_name', 'position']) as $key => $value) {
            $save_data[$key] = ($value || $value == 0) ? $value : null;
        }

        $select_value = $request->input('select_value');
        if ($select_value) {
            $select_value = textarea_br_to_array($select_value);
            $save_data['select_value'] = join(',', $select_value);
        }

        $result = Config::create($save_data);
        $res = $result->id;

        if ($res) {
            $this->refreshCache();
            return $this->success();
        } else {
            api_error(__('admin.save_error'));
        }
    }

    /**
     * 更新缓存信息
     * @param Request $request
     */
    public function refreshCache()
    {
        $res_config = Config::all();
        $config = array();
        if ($res_config) {
            foreach ($res_config as $val) {
                $config[$val['key_name']] = $val['value'];
            }
            Redis::set('app_config:' . config('app.key'), json_encode($config));
        }
    }
}