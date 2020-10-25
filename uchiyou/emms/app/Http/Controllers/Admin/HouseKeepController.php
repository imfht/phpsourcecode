<?php
namespace App\Http\Controllers\Admin;

use App\Company;
use App\Http\Controllers\Controller;
use App\Util\ModelUtil;
use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class HouseKeepController extends Controller{
	
	public function index(){
		$companys = Company::orderBy('updated_at','desc')
		->offset(0)
		->limit(10)
		->get();
		return view('/housekeep/searchResult',['companys'=>$companys,'place'=>'index']);
	}
	public function search(Request $request){
		$user = Auth::user();
		if($user->job_type != User::USER_JOB_HOUSE_KEEPER){
			abort(403,'您没权执行该操作');
		}
		$content = $request->get('content');
		$companys = Company::where('name','like','%'.$content.'%')
		->get();
		return view('housekeep/searchResult',['companys'=>$companys]);
	}
	public function operateCompany($companyId,$operate){
		$user = Auth::user();
		if($user->job_type != User::USER_JOB_HOUSE_KEEPER){
			abort(403,'您没权执行该操作');
		}
		$company = Company::findOrFail($companyId);
		if($user->company_id == $company->id){
			return view('/housekeep/searchResult',['message'=>'不能关闭自己公司的服务']);
		}
		$delete=ModelUtil::DELETE_NORMAL;
		switch ($operate){
			case 'delete':
				$delete=ModelUtil::DELETE_SELF;
				break;
			case 'recover':
				$delete=ModelUtil::DELETE_NORMAL;
				break;
			default:
				abort(404,'暂未提供该操作');
		}
		$company->delete = $delete;
		$company->save();
		return Redirect::to('/admin/housekeep/home');
	}
}