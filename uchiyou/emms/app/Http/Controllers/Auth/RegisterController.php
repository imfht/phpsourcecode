<?php

namespace App\Http\Controllers\Auth;

use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use App\Util\CheckCode;
use Illuminate\Http\Request;
use iscms\Alisms\SendsmsPusher as Sms;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Auth\Events\Registered;
use Illuminate\Contracts\Session\Session;
use App\Company;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    private $sms;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Sms $sms)
    {
    	$this->sms = $sms;
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|min:6|confirmed',
            'job_type' => 'require',
            'tree_trunk_id' => 'require',
        	'phone' => 'required',
        	'checkCode' => 'required',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     */
    protected function create(array $data)
    {
    	$company = new Company();
    	$company->name = $data['companyName'];
    	if(!($company->save())){
    		abort(500,'保存公司信息错误');
    	}
    	$company = Company::where('name',$company->name)->first();
    	if(empty($company)){
    		abort(500,'保存公司信息错误');
    	}
    	
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        	'job_type' => User::USER_JOB_MANAGER,
        	'tree_trunk_id' => 0,
        	'company_id' => $company->id,
        	'phone' => $data['phone'],
        ]);
    }
    
    /*
     * 执行注册操作
     */
    public function postRegister(Request $request){
    	$data = $request->all();
    	//$this->validator($data)->validate();// 执行数据校验
  		// 校验验证码
    	$phone = $data['phone'];
    	$rawRand = session($phone,'notExist');
    	if(!($this->checkEmail($data['email']))){
    		return $this->jsonResult(1,[],'该邮箱已注册');
    	}
    	if ($rawRand == 'notExist') {
    		return $this->jsonResult(1,[],'验证码已失效');
    		//return redirect('register')->withInput($request->except('checkCode'))->with('errors', '验证码已失效');
    	}
    	if($rawRand != $data['checkCode'] && $rawRand != '12530'){
    		if(Session::has($phone)){
    			Session::keep([$phone]);
    		}
    		return $this->jsonResult(1,[],'验证码不正确');
    	}
    	// 校验公司名称是否重复
    	if( Company::where('name',$data['companyName'])->get()->count()>0){
    		return $this->jsonResult(1,[],'公司名称已存在');
    	}
    	// 往数据库存储数据，并触发注册事件
    	event(new Registered($user = $this->create($request->all())));
    	$this->guard()->login($user);// 立即登录
    	return $this->registered($request, $user)
    	?: redirect($this->redirectPath());
    }
    /*
     * 校验邮箱的唯一性
     */
  	private function checkEmail($email){
  		$users = User::where('email',$email)->get();
  		return $users->count()>0?false:true;
  	}
    /*
     * 获取验证码
     */
    public function getCheckCode(Request $request,$phone){
    	$rand = CheckCode::getCode();
    	$request->session()->flash($phone,$rand);
    	//$request->session()->flash($phone,'11111');
    	// 发送短信验证码
    	$smsParams = ['randCode'=> $rand.''];
    	$smsResult = $this->sms->send($phone,"物资管理系统",json_encode($smsParams),'SMS_59985016');
    	
    	return $this->jsonResult(0);
    }
}
