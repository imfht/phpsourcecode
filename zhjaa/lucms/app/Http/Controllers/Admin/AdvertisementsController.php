<?php

namespace App\Http\Controllers\Admin;

use App\Http\Resources\AdvertisementCollection;
use App\Models\Advertisement;
use App\Validates\AdvertisementValidate;
use Illuminate\Http\Request;
use Purifier;

class AdvertisementsController extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->middleware('auth:api');
    }

    public function advertisementList(Request $request, Advertisement $model)
    {
        $per_page = $request->get('per_page', 10);

        $search_data = json_decode($request->get('search_data'), true);
        $name = isset_and_not_empty($search_data, 'name');
        if ($name) {
            $model = $model->columnLike('name', $name);
        }

        $enable = isset_and_not_empty($search_data, 'enable');
        if ($enable) {
            $model = $model->enable('enable', $enable);
        }

        $advertisement_position_ids = isset_and_not_empty($search_data, 'advertisement_position_ids');
        if ($advertisement_position_ids) {
            $model = $model->advertisementPositionSearch($advertisement_position_ids);
        }

        $order_by = isset_and_not_empty($search_data, 'order_by');
        if ($order_by) {
            $order_by = explode(',', $order_by);
            $model = $model->orderBy($order_by[0], $order_by[1]);
        }

        return new AdvertisementCollection($model->with('advertisementPosition')->paginate($per_page));
    }

    public function show(Advertisement $model)
    {
        $model->advertisementPosition;
        return $this->success($model);
    }

    public function store(Request $request, Advertisement $model, AdvertisementValidate $validate)
    {
        $request_data = $request->all();
        if (isset($request_data['cover_image']['attachment_id'])) {
            $attachement_id = $request_data['cover_image']['attachment_id'];
        } else {
            $attachement_id = 0;
        }
        $request_data['cover_image'] = $attachement_id;

        if ($request_data['advertisement_positions_type'] == 'model') {
            $model_column_value = $request_data['model_column_value'];
            if (!$model_column_value['column'] || !$model_column_value['model'] || !$model_column_value['value']) {
                return $this->failed('跳转模型类广告位，必须填写key');
            }
        } else {
            $request_data['model_column_value'] = [
                'model' => '',
                'column' => '',
                'value' => ''
            ];
        }
        $rest_validate = $validate->storeValidate($request_data);

        if ($rest_validate['status'] === false) return $this->failed($rest_validate['message']);


        $res = $model->storeAction($request_data);
        if ($res['status'] === true) return $this->message($res['message']);
        return $this->failed($res['message']);
    }

    public function update(Request $request, Advertisement $model, AdvertisementValidate $validate)
    {
        $request_data = $request->all();
        if (isset($request_data['cover_image']['attachment_id'])) {
            $attachement_id = $request_data['cover_image']['attachment_id'];
        } else {
            $attachement_id = 0;
        }
        $request_data['cover_image'] = $attachement_id;

        if ($request_data['advertisement_positions_type'] == 'model') {
            $model_column_value = $request_data['model_column_value'];
            if (!$model_column_value['column'] || !$model_column_value['model'] || !$model_column_value['value']) {
                return $this->failed('跳转模型类广告位，必须填写key');
            }
        } else {
            $request_data['model_column_value'] = [
                'model' => '',
                'column' => '',
                'value' => ''
            ];
        }
        $rest_validate = $validate->updateValidate($request_data, $model->id);

        if ($rest_validate['status'] === false) return $this->failed($rest_validate['message']);


        $res = $model->updateAction($request_data);
        if ($res['status'] === true) return $this->message($res['message']);
        return $this->failed($res['message']);
    }


    public function destroy(Advertisement $model, AdvertisementValidate $validate)
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
