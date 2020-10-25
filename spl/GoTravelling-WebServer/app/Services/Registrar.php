<?php namespace App\Services;

use App\User;
use Validator;
use Illuminate\Contracts\Auth\Registrar as RegistrarContract;
use Illuminate\Http\Request;

class Registrar implements RegistrarContract {

	/**
	 * Get a validator for an incoming registration request.
	 *
	 * @param  array  $data
	 * @return \Illuminate\Contracts\Validation\Validator
	 */
	public function validator(array $data)
	{

      $this->treatRegisterData($data);

      Validator::extend('check_repeat', function($attr, $value){
          $count = User::where('username', $value)
              ->orWhere('cellphone_number', $value)
              ->count();

          if ( 0 == $count ) {
              return true;
          } else {
              return false;
          }
      });

      return Validator::make($data, [
          'username' => 'max:60|min:6|check_repeat',
          'cellphone_number' => 'size:11|alpha_num|check_repeat',
          'password' => 'required|confirmed|min:6',
          'method' => 'required'
       ]);
	}

	/**
	 * Create a new user instance after a valid registration.
	 *
	 * @param  array  $data
	 * @return User
	 */
	public function create(array $data)
	{
      $userData['password'] = bcrypt($data['password']);

      if( array_key_exists('username', $data) ) $userData['username'] = $data['username'];
      if( array_key_exists('cellphone_number', $data) ) $userData['cellphone_number'] = $data['cellphone_number'];

      $userData['head_image'] = 'default_head_image.png';

		return User::create($userData);
	}

    protected function treatRegisterData(&$data)
    {
        unset($data['method']);

        foreach($this->register_methods as $method ){
            if( array_key_exists($method, $data) ){
                $data['method'] = $method;
                break;
            }
        }

    }

    protected $register_methods = ['cellphone_number', 'username'];

}
