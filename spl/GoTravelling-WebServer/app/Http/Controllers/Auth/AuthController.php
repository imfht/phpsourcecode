<?php namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Helper\ValidateHelper;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\Registrar;
use Helper\AuthHelper;
use Illuminate\Http\Request;
use Helper\ResponseHelper;

class AuthController extends Controller {

	/*
	|--------------------------------------------------------------------------
	| Registration & Login Controller
	|--------------------------------------------------------------------------
	|
	| This controller handles the registration of new users, as well as the
	| authentication of existing users. By default, this controller uses
	| a simple trait to add these behaviors. Why don't you explore it?
	|
	*/

    use AuthHelper;

	public function __construct(Guard $auth, Registrar $registrar)
	{
		$this->auth = $auth;
		$this->registrar = $registrar;

		$this->middleware('guest', ['except' => 'getLogout']);
	}

    public function validate(Request $request, array $rules, array $messages = array())
    {
        $validator = $this->getValidationFactory()->make($request->all(), $rules, $messages);

        if ( $validator->fails() ) {
            $respData['data'] = $request->all();
            $respData['error'] = $errorMessageArray = ValidateHelper::changeValidatorMessageToArray($validator->getMessageBag());
            return ResponseHelper::chooseResponse($request, [
                'json'  =>  response()->json($respData, 200),
                'base' =>  redirect($this->loginPath())
                ->withInput($respData['data'])
                ->withErrors($respData['error'])
            ]);
        }
    }

    protected $redirectPath = '/';
}
