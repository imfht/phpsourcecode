<?php

namespace App\Http\Controllers;

use App\Common\StFetch;
use App\Common\StValidator;
use App\Feedback;
use App\PasswordReset;
use App\User;
use Exception;
use Illuminate\Support\Facades\Auth;


use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Common\IDMaker;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthenticateController extends Controller
{

    /**
     * @param $credentials
     * @param $token
     * @return \Illuminate\Http\JsonResponse
     */
    protected function signInAttempt($credentials, &$token)
    {
        try {
            // attempt to verify the credentials and create a token for the user
            if (! $token = JWTAuth::attempt($credentials)) {
                return response()->json(['error' => 'invalid_credentials'], 401);
            }
        } catch (JWTException $e) {
            // something went wrong whilst attempting to encode the token
            return response()->json(['error' => 'could_not_create_token'], 500);
        }

        return null;
    }


    public function signIn(Request $request){

        StValidator::make($request->all(),[
            'name'=>'required|string',
            'password'=>'required|string',
        ]);

        $credentials = StFetch::fetch($request,['name','password'],['privilege' => 1]);
        if($response = $this->signInAttempt($credentials,$token)){
            return $response;
        }

        // all good so return the token
        return [
            'error' => 0,
            'token' => $token,
        ];
    }

    public function signUp(Request $request)
    {
        StValidator::make($request->all(),[
            'name'=>'required|string',
            'email'=>'required|string',
            'password'=>'required|string',
            'code'=>'required|string',
        ]);

        if($request['code'] != 'No cross, no crown.'){
            return ['error' => 'bad code'];
        }

        try{
            User::create([
                'name' => $request['name'],
                'email' => $request['email'],
                'password' => bcrypt($request['password']),
                'privilege' => 1,
            ]);

            return ['error' => 0];
        } catch(Exception $e) {
            return ['error' => 'fail to create user'];
        }

    }



    public function refresh(Request $request)
    {
        StValidator::make($request->all(),[
            'token'=>'required|string',
        ]);

        try {
            if(!$token = JWTAuth::refresh($request['token'])){
                return response()->json(['error' => 'refresh_token_error'], 401);
            }
        } catch (JWTException $e) {
            // something went wrong whilst attempting to encode the token
            return response()->json(['error' => 'bad_token_to_refresh'], 500);
        }

        return [
            'error' => 0,
            'token' => $token,
        ];
    }

    public function signOut(Request $request){
        StValidator::make($request->all(),[
            'token'=>'required|string',
        ]);

        try {
            JWTAuth::invalidate($request->token);
        } catch (JWTException $e){
            return ['error' => 'invalidate_fail'];
        }
        return ['error' => 0];
    }

}
