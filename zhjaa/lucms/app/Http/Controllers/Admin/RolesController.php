<?php

namespace App\Http\Controllers\Admin;

use App\Http\Resources\CommonCollection;
use App\Models\Role;
use App\Validates\RoleValidate;
use Illuminate\Http\Request;

class RolesController extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->middleware('auth:api');
    }

    public function roleList(Request $request, Role $model)
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

        return new CommonCollection($model->get());
    }

    public function show(Role $model)
    {
        return $this->success($model);
    }

    public function addEdit(Request $request, Role $model, RoleValidate $validate)
    {
        $request_data = $request->only('id', 'name', 'guard_name', 'description');
        $role_id = $request->post('id', 0);
        if ($role_id > 0) {
            $model = $model->findOrFail($role_id);
            $rest_validate = $validate->updateValidate($request_data, $role_id);
        } else {
            $rest_validate = $validate->storeValidate($request_data);
        }

        if ($rest_validate['status'] === false) return $this->failed($rest_validate['message']);

        $res = $model->saveData($request_data);

        if ($res) return $this->message('操作成功');
        return $this->failed('内部错误');
    }

    public function getRolePermissions(Role $model)
    {
        $permissions = $model->permissions()->get();
        $return = [];
        $permissions->each(function ($per) use (&$return) {
            $return[] = strval($per->id);
        });

        return $this->success($return);
    }

    public function giveRolePermissions(Request $request, Role $model)
    {
        $permissions = $request->post('permission');
        $model->syncPermissions($permissions);
        return $this->message('权限分配成功');
    }

    public function allRoles(Role $model)
    {
        $roles = $model->get();
        $return = [];
        $roles->each(function ($per) use (&$return) {
            $return[] = [
                'key' => strval($per->id),
                'label' => $per->name,
                'description' => $per->description
            ];
        });
        return $this->success($return);
    }

    public function destroy(Role $model, RoleValidate $validate)
    {
        if (!$model) return $this->failed('找不到角色', 404);
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
