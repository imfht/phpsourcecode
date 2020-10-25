<?php

namespace App\Http\Controllers\Admin;

use App\Http\Resources\CommonCollection;
use App\Models\Log;
use Illuminate\Http\Request;

class LogsController extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->middleware('auth:api');
    }

    public function logList(Request $request, Log $log)
    {
        $per_page = $request->get('per_page', 10);

        $search_data = json_decode($request->get('search_data'), true);
        $type = isset_and_not_empty($search_data, 'type');
        if ($type) {
            $log = $log->typeSearch($type);
        }
        $table_name = isset_and_not_empty($search_data, 'table_name');
        if ($table_name) {
            $log = $log->tableNameSearch($table_name);
        }

        $user_name = isset_and_not_empty($search_data, 'user_name');
        if ($user_name) {
            $log = $log->userNameSearch($user_name);
        }

        $order_by = isset_and_not_empty($search_data, 'order_by');
        if ($order_by) {
            $order_by = explode(',', $order_by);
            $log = $log->orderBy($order_by[0], $order_by[1]);
        }

        return new CommonCollection($log->with('user')->recent()->paginate($per_page));

    }
}
