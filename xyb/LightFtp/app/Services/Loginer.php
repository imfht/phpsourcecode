<?php namespace App\Services;
use Illuminate\Support\Facades\Validator;

class Loginer {

	/**
	 * Get a validator for an incoming registration request.
	 *
	 * @param  array  $data
	 * @return \Illuminate\Contracts\Validation\Validator
	 */
	public function validator(array $data)
	{
		$message = [
			'check_server' => ':attribute 必须为位IP或者URL'
		];

		Validator::extend('check_server', function($attribute, $value, $parameters) {
			return filter_var($value, FILTER_VALIDATE_IP) || filter_var($value, FILTER_VALIDATE_URL);
		});

		return \Validator::make($data, [
			'ip' => 'required|check_server',
			'user' => 'required',
			'pass' => 'required',
		], $message);
	}
}
