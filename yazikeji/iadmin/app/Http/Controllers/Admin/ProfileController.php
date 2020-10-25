<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\AdminAuthLog;
use Illuminate\Support\Facades\Auth;
use Services\AdminsService;

class ProfileController extends Controller
{

    public function account()
    {
        return view('admin.profile.account');
    }

    public function updatePassword(Request $request, AdminsService $admin)
    {
        $result = $admin->updatePassword($request->all(), $request->user('admin')->id);

        if ($result === true) {
            return response()->json(['status'=>1]);
        } else {
            return response()->json(['status'=>0, 'message'=>$result]);
        }
    }

    public function loginHistory(Request $request)
    {
        $uid = $request->user('admin')->id;
        $histories = AdminAuthLog::where('admins_id', $uid)->orderBy('id', 'desc')->paginate(30);
        return view('admin.profile.loginHistory')->withHistories($histories);
    }
}
