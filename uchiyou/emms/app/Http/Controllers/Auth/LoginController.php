<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use App\Company;
use App\TreeTrunk;
use App\Util\SessionUtil;
use App\Util\ModelUtil;
use App\User;
use Illuminate\Contracts\Logging\Log;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest', ['except' => 'logout']);
    }
    /*
     * 登陆成功后自定义跳转的路径
     */
    protected function redirectTo()
    {
    	$user = Auth::user();
    	#Log::useFiles(storage_path().'/logs/laravel.log')->info('登陆的用户信息:',$user);
    	print $user;
    	// 如果已经被删除，则退出当前登陆
    	if($user->delete > ModelUtil::DELETE_NORMAL){
    		Auth::logout();
    	}
    	$company = Company::findOrFail($user->company_id);
    	// 如果所在的公司被删除，那么该公司的所有员工不能登陆
    	if($company->delete > ModelUtil::DELETE_NORMAL){
    		//Auth::logout();
    	}
    	// 如果所在公司订阅的服务过期了，那么该公司的所有员工不能登陆
    	if($company->deadline < time()){
    		//Auth::logout();
    	}
    	$treeTrunk = null;
    	if($user->tree_trunk_id != 0 && false){
    		$treeTrunk = TreeTrunk::findOrFail($user->tree_trunk_id);
    	}else{
    		$treeTrunk = new TreeTrunk();
    		$treeTrunk->id = $user->tree_trunk_id;//0
    		$treeTrunk->name = $company->name;
    		$treeTrunk->parent_id = 0;
    	}
    	session([SessionUtil::COMPANY=>$company,SessionUtil::DEPARTMENT=>$treeTrunk]);
    	/* if($user->job_type== User::USER_JOB_HOUSE_KEEPER){
    		return '/admin/housekeep/home';
    	} */
    	//挑战 url
    	return '/home';
    }
}
