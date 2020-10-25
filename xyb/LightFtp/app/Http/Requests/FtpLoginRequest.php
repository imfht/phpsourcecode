<?php namespace App\Http\Requests;

class FtpLoginRequest extends Request {

	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize()
	{
		return true;
	}

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules()
	{
		return [
			'ip' => 'required',
			'user' => 'required',
			'pass' => 'required',
		];
	}
}
