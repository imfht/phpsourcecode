<?php

namespace App\Http\Controllers\Admin;

use App\Http\Resources\CommonCollection;
use App\Models\AdvertisementPosition;
use App\Validates\AdvertisementPositionValidate;
use Illuminate\Http\Request;

class AdvertisementPositionsController extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->middleware('auth:api');
    }

    public function advertisementPositionList(Request $request, AdvertisementPosition $model)
    {
        $per_page = $request->get('per_page', 10);

        $search_data = json_decode($request->get('search_data'), true);
        $name = isset_and_not_empty($search_data, 'name');
        if ($name) {
            $model = $model->columnLike('name', $name);
        }
        $type = isset_and_not_empty($search_data, 'type');
        if ($type) {
            $model = $model->typeSearch($type);
        }
        $order_by = isset_and_not_empty($search_data, 'order_by');
        if ($order_by) {
            $order_by = explode(',', $order_by);
            $model = $model->orderBy($order_by[0], $order_by[1]);
        }

        return new CommonCollection($model->paginate($per_page));
    }

    public function show(AdvertisementPosition $model)
    {
        return $this->success($model);
    }

    public function allAdvertisementPositions(AdvertisementPosition $model)
    {
        return $this->success(collect($model->get())->keyBy('id'));
    }


    public function addEdit(Request $request, AdvertisementPosition $model, AdvertisementPositionValidate $validate)
    {
        $request_data = $request->only('id', 'name', 'type', 'description');
        $advertisement_position_id = $request->post('id', 0);

        if (is_null($request_data['description'])) unset($request_data['description']);

        if ($advertisement_position_id > 0) {
            $model = $model->findOrFail($advertisement_position_id);
            $rest_validate = $validate->updateValidate($request_data, $advertisement_position_id);
        } else {
            $rest_validate = $validate->storeValidate($request_data);
        }


        if ($rest_validate['status'] === false) return $this->failed($rest_validate['message']);

        $res = $model->saveData($request_data);
        if ($res) return $this->message('操作成功');
        return $this->failed('内部错误');
    }

    public function destroy(AdvertisementPosition $model, AdvertisementPositionValidate $validate)
    {
        if (!$model) return $this->failed('找不到数据', 404);
        $rest_destroy_validate = $validate->destroyValidate($model);
        if ($rest_destroy_validate['status'] === true) {
            $rest_destroy = $model->destroyAction();
            if ($rest_destroy['status'] === true) return $this->message($rest_destroy['message']);
            return $this->failed($rest_destroy['message'], 500);
        } else {
            return $this->failed($rest_destroy_validate['message']);
        }
    }
}
