<?php

namespace App\Http\Controllers\Admin;


use App\Http\Resources\CommonCollection;
use App\Models\ApiMessage;
use App\Models\User;
use App\Validates\ApiMessageValidate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApiMessagesController extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->middleware('auth:api');
    }

    public function list(Request $request, ApiMessage $model)
    {
        $per_page = $request->get('per_page', 10);
        $search_data = json_decode($request->get('search_data'), true);

        $phone = isset_and_not_empty($search_data, 'phone');
        if ($phone) {
            $user_ids = User::where('phone', 'like', '%' . $phone . '%')->pluck('id')->toArray();
            $model = $model->columnInSearch('user_id', $user_ids);
        }

        $status = isset_and_not_empty($search_data, 'status');
        if ($status) {
            $model = $model->columnEqualSearch('status', $status);
        }

        $type = isset_and_not_empty($search_data, 'type');
        if ($type) {
            $model = $model->columnEqualSearch('type', $type);
        }

        $order_by = isset_and_not_empty($search_data, 'order_by');
        if ($order_by) {
            $order_by = explode(',', $order_by);
            $model = $model->orderBy($order_by[0], $order_by[1]);
        }
        return new CommonCollection($model->with('user')->paginate($per_page));
    }

    public function userSearch($phone, User $user)
    {
        return $this->success($user->enable()->columnLike('phone', '%' . $phone)->select('id', 'name', 'phone')->get());
    }

    public function store(Request $request, ApiMessage $model, ApiMessageValidate $validate)
    {
        $request_data = $request->all();
        if (!$request_data['url']) {
            $request_data['url'] = '';
        }
        $is_send_to_all = true;
        if ($request_data['users']) {
            $is_send_to_all = false;
        } else {
            unset($request_data['users']);
        }
        $rest_validate = $validate->storeValidate($request_data);
        if ($rest_validate['status'] === true) {
            $admin_id = Auth::id();
            if ($is_send_to_all) {
                $model->allMessage($request_data['title'], $request_data['content'], $admin_id, $request_data['url'], $request_data['is_alert_at_home'], $request_data['type']);
            } else {
                $date = date('Y-m-d H:i:s');

                foreach ($request_data['users'] as $user_id) {
                    $insert_data[] = [
                        'user_id' => $user_id,
                        'admin_id' => $admin_id,
                        'type' => $request_data['type'],
                        'title' => $request_data['title'],
                        'content' => $request_data['content'],
                        'url' => $request_data['url'],
                        'is_alert_at_home' => $request_data['is_alert_at_home'],
                        'created_at' => $date
                    ];

                }
                $model->manyMessage($insert_data);
            }
            return $this->message('消息发送成功');
        } else {
            return $this->failed($rest_validate['message']);
        }
    }


}
