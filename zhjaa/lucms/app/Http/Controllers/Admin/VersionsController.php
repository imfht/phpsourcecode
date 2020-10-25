<?php

namespace App\Http\Controllers\Admin;

use App\Models\SystemVersion;
use App\Validates\SystemVersionValidate;
use Illuminate\Http\Request;

class VersionsController extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->middleware('auth:api');
    }

    public function list(SystemVersion $systemVersion)
    {
        return $this->success($systemVersion->orderBy('version', 'desc')->get());
    }

    public function store(Request $request, SystemVersion $systemVersion, SystemVersionValidate $validate)
    {
        $data = $request->all();
        $rest_validate = $validate->storeValidate($data);
        if ($rest_validate['status'] === false) return $this->failed($rest_validate['message']);

        $res = $systemVersion->saveData($data);

        if ($res) return $this->message('操作成功');
        return $this->failed('内部错误');

    }
}
