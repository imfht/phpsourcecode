<?php

namespace App\Http\Controllers\Admin;

use App\Http\Resources\CommonCollection;
use App\Models\AppVersion;
use App\Validates\AppVersionValidate;
use Auth;
use Illuminate\Http\Request;

class AppVersionsController extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->middleware('auth:api');
    }

    public function list(Request $request, AppVersion $appVersion)
    {
        $per_page = $request->get('per_page', 10);

        $search_data = json_decode($request->get('search_data'), true);
        $port = isset_and_not_empty($search_data, 'port');
        if ($port) {
            $appVersion = $appVersion->columnEqualSearch($port);
        }
        $system = isset_and_not_empty($search_data, 'system');
        if ($system) {
            $appVersion = $appVersion->columnEqualSearch($system);
        }

        $order_by = isset_and_not_empty($search_data, 'order_by');
        if ($order_by) {
            $order_by = explode(',', $order_by);
            $appVersion = $appVersion->orderBy($order_by[0], $order_by[1]);
        }

        $appVersion = $appVersion->paginate($per_page);
        return new CommonCollection($appVersion);

    }

    public function show(AppVersion $appVersion)
    {
        return $this->success($appVersion);
    }

    public function store(Request $request, AppVersion $appVersion, AppVersionValidate $validate)
    {
        $insert_data = $request->all();

        if (isset($insert_data['package']['attachment_id'])) {
            $attachement_id = $insert_data['package']['attachment_id'];
        } else {
            $attachement_id = 0;
        }
        $insert_data['package'] = $attachement_id;

        $rest_validate = $validate->storeValidate($insert_data);

        if ($rest_validate['status'] === false) return $this->failed($rest_validate['message']);


        $res = $appVersion->storeAppVersion($insert_data);
        if ($res['status'] === true) return $this->message($res['message']);
        return $this->failed($res['message']);

    }

    public function update(Request $request,AppVersion $appVersion,AppVersionValidate $validate)
    {
        if (!$appVersion) return $this->failed('找不到数据', 404);

        $update_data = $request->all();


        if (isset($update_data['package']['attachment_id'])) {
            $attachement_id = $update_data['package']['attachment_id'];
        } else {
            $attachement_id = 0;
        }
        $update_data['package'] = $attachement_id;

        $rest_validate = $validate->updateValidate($update_data, $appVersion->id);

        if ($rest_validate['status'] === false) return $this->failed($rest_validate['message']);


        $res = $appVersion->updateAppVersion($update_data);
        if ($res['status'] === true) return $this->message($res['message']);
        return $this->failed($res['message']);
    }

    public function destroy(AppVersion $appVersion)
    {
        if (!$appVersion) return $this->failed('找不到数据', 404);
        $rest_destroy = $appVersion->destroyAppVersion();
        if ($rest_destroy['status'] === true) return $this->message($rest_destroy['message']);
        return $this->failed($rest_destroy['message'], 500);
    }

}
