<?php

namespace App\Http\Controllers\Admin;


use App\Http\Resources\CommonCollection;
use App\Models\AdminMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminMessagesController extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->middleware('auth:api');
    }

    public function list(Request $request, AdminMessage $adminMessage)
    {
        $per_page = $request->get('per_page', 10);
        $search_data = json_decode($request->get('search_data'), true);

        $adminMessage = $adminMessage->whereIn('admin_id', [Auth::id(), 0]);

        $status = isset_and_not_empty($search_data, 'status');
        if ($status) {
            $adminMessage = $adminMessage->columnEqualSearch('status', $status);
        }

        $type = isset_and_not_empty($search_data, 'type');
        if ($type) {
            $adminMessage = $adminMessage->columnEqualSearch('type', $type);
        }

        $order_by = isset_and_not_empty($search_data, 'order_by');
        if ($order_by) {
            $order_by = explode(',', $order_by);
            $adminMessage = $adminMessage->orderBy($order_by[0], $order_by[1]);
        }
        return new CommonCollection($adminMessage->with('user')->paginate($per_page));
    }

    public function readMessages(Request $request, AdminMessage $adminMessage)
    {
        $is_read_all = $request->is_read_all;
        $message_ids = $request->message_ids;
        if ($is_read_all) {
            $adminMessage->where('status', 'U')->whereIn('admin_id', [Auth::id(), 0])->update(['status' => 'R']);
        } else {
            $adminMessage->whereIn('id', explode(',', $message_ids))->update(['status' => 'R']);
        }
        return $this->message('操作成功');

    }

    public function destroy(AdminMessage $adminMessage)
    {
        if (!$adminMessage) return $this->failed('找不到数据', 404);
        $rest_destroy = $adminMessage->destroyAdminMessage(Auth::id());
        if ($rest_destroy['status'] === true) return $this->message($rest_destroy['message']);
        return $this->failed($rest_destroy['message']);
    }

    public function destroyMany(AdminMessage $adminMessage, $admin_message_ids)
    {
        $adminMessage->whereIn('id', explode(',', $admin_message_ids))->delete();
        return $this->message('操作成功');
    }


}
