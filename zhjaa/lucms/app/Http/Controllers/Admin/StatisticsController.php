<?php

namespace App\Http\Controllers\Admin;

use App\Models\AdminMessage;

class StatisticsController extends AdminController
{

    public function __construct()
    {
        parent::__construct();
        $this->middleware('auth:api');

    }

    public function base(AdminMessage $adminMessage)
    {

        $return = [
            'unread_message' => $adminMessage->where('status', 'U')->count(),
        ];
        return $this->success($return);
    }


}
