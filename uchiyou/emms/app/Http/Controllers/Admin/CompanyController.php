<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Auth;
use App\User;
use App\Http\Controllers\Controller;
use App\Company;
use Illuminate\Http\Request;
use App\Util\DateUtil;
use Illuminate\Support\Facades\Redirect;

class CompanyController extends Controller{
	
	public function getCompanyInfo(){
		$user = Auth::user();
		if($user->job_type != User::USER_JOB_MANAGER){
			abort(403,'只有管理员才能访问');
		}
		$company = Company::findOrFail($user->company_id);
		return view('elements/companyInfo',['company'=>$company]);		
	}
	
	public function postBaseInfo(Request $request){
		$this->validate($request, [
				'name' => 'required',
				'description' => 'required',
		]);
		$name = $request->input('name');
		$description = $request->input('description');
		$user = Auth::user();
		$company = Company::findOrFail($user->company_id);
		$company->name = $name;
		$company->description = $description;
		$company->save();
		return view('elements/companyInfo',['company'=>$company]);
	}
	public function postServiceInfo(Request $request){
		$this->validate($request, [
				'value' => 'required|numeric',
				'payMethod' => 'required',
		]);
		$value = $request->input('value');
		$payMethod = $request->input('payMethod');
		$user = Auth::user();
		$company = Company::findOrFail($user->company_id);
		$company->deadline = date(DateUtil::FORMAT,strtotime($company->deadline.'+'.$value.' month'));
		$company->save();
		return Redirect::back();
	}
}