<?php namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesCommands;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use League\Flysystem\Adapter\Ftp;

abstract class Controller extends BaseController {
	protected $ftp;

	use DispatchesCommands, ValidatesRequests;

	public function __construct()
	{
		$this->middleware('login');

		if(\Session::get('serverIP') && \Session::get('user') && \Session::get('password'))
		{
			$this->ftp = new Ftp([
				'host' => strpos(\Session::get('serverIP'), 'http://') !== false ? str_replace('http://', '', \Session::get('serverIP')) : \Session::get('serverIP'),
				'username' => \Session::get('user'),
				'password' => \Session::get('password'),
			]);
			$this->ftp->connect();
		}
	}
}
