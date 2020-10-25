<?php

namespace App\Http\Controllers\Admin;

use App\Http\Resources\CommonCollection;
use App\Models\IpFilter;
use App\Validates\IpFilterValidate;
use Illuminate\Http\Request;

class IpFiltersController extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->middleware('auth:api');
    }

    public function ipFilterList(Request $request, IpFilter $model)
    {
        $per_page = $request->get('per_page', 10);
        $search_data = json_decode($request->get('search_data'), true);
        $ip = isset_and_not_empty($search_data, 'ip');
        if ($ip) {
            $model = $model->columnLike('ip', $ip);
        }
        $type = isset_and_not_empty($search_data, 'type');
        if ($type) {
            $model = $model->typeSearch($type);
        }

        return new CommonCollection($model->paginate($per_page));
    }

    public function show(IpFilter $model)
    {
        return $this->success($model);
    }

    public function addEditIpFilter(Request $request, IpFilter $model, IpFilterValidate $validate)
    {
        $request_data = $request->all();
        $filter_id = $request->post('id', 0);


        if ($filter_id > 0) {
            $model = $model->findOrFail($filter_id);
            $rest_validate = $validate->updateValidate($request_data, $filter_id);
        } else {
            $rest_validate = $validate->storeValidate($request_data);
        }
        if ($rest_validate['status'] === false) return $this->failed($rest_validate['message']);

        $res = $model->saveData($request_data);

        if ($res) return $this->message('操作成功');
        return $this->failed('内部错误');

    }

    public function destroy(IpFilter $model)
    {
        if (!$model) return $this->failed('找不到数据', 404);
        $rest_destroy = $model->destroyAction();
        if ($rest_destroy['status'] === true) return $this->message($rest_destroy['message']);
        return $this->failed($rest_destroy['message'], 500);
    }
}
