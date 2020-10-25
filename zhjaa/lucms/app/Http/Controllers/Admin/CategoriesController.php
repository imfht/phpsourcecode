<?php

namespace App\Http\Controllers\Admin;

use App\Models\Category;
use App\Validates\CategoryValidate;
use Illuminate\Http\Request;

class CategoriesController extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->middleware('auth:api');
    }

    public function categoryList(Request $request, Category $model)
    {
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

        return $this->success($model->get());
    }

    public function allCategories(Category $model)
    {
        return $this->success(collect($model->get())->keyBy('id'));
    }

    public function show(Category $model)
    {
        return $this->success($model);
    }

    public function addEditCategory(Request $request, Category $model, CategoryValidate $validate)
    {
        $request_data = $request->all();
        $category_id = $request->post('id', 0);
        if (isset($request_data['cover_image']['attachment_id'])) {
            $attachement_id = $request_data['cover_image']['attachment_id'];
        } else {
            $attachement_id = 0;
        }
        $request_data['cover_image'] = $attachement_id;
        if (is_null($request_data['description'])) unset($request_data['description']);

        if ($category_id > 0) {
            $model = $model->findOrFail($category_id);
            $rest_validate = $validate->updateValidate($request_data, $category_id);
            if ($rest_validate['status'] === false) return $this->failed($rest_validate['message']);
            $res = $model->updateAction($request_data);
        } else {
            $rest_validate = $validate->storeValidate($request_data);
            if ($rest_validate['status'] === false) return $this->failed($rest_validate['message']);
            $res = $model->storeAction($request_data);
        }

        if ($res['status'] === true) return $this->message($res['message']);
        return $this->failed($res['message']);
    }

    public function destroy(Category $model, CategoryValidate $validate)
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
