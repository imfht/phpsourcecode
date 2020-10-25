<?php

namespace App\Http\Controllers\Admin;

use App\Http\Resources\CommonCollection;
use App\Models\Tag;
use App\Validates\TagValidate;
use Illuminate\Http\Request;

class TagsController extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->middleware('auth:api');
    }

    public function tagList(Request $request, Tag $model)
    {
        $per_page = $request->get('per_page', 10);
        $search_data = json_decode($request->get('search_data'), true);
        $name = isset_and_not_empty($search_data, 'name');
        if ($name) {
            $model = $model->columnLike('name', $name);
        }

        $order_by = isset_and_not_empty($search_data, 'order_by');
        if ($order_by) {
            $order_by = explode(',', $order_by);
            $model = $model->orderBy($order_by[0], $order_by[1]);
        }

        return new CommonCollection($model->paginate($per_page));
    }

    public function show(Tag $model)
    {
        return $this->success($model);
    }

    public function addEditTag(Request $request, Tag $model, TagValidate $validate)
    {
        $request_data = $request->all();
        $tag_id = $request->post('id', 0);


        if ($tag_id > 0) {
            $model = $model->findOrFail($tag_id);
            $rest_validate = $validate->updateValidate($request_data, $tag_id);
        } else {
            $rest_validate = $validate->storeValidate($request_data);
        }
        if ($rest_validate['status'] === false) return $this->failed($rest_validate['message']);

        $res = $model->saveData($request_data);

        if ($res) return $this->message('操作成功');
        return $this->failed('内部错误');

    }

    public function destroy(Tag $model, TagValidate $validate)
    {
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
