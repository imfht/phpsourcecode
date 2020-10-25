<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Services\Loginer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades;
use League\Flysystem\Adapter\Ftp;
use Request as CRequest;
use Response;
use Session;
use App\Article;
use App\Author;

class PublicController extends Controller {

	public function __construct(Loginer $loginer)
	{
		$this->loginer = $loginer;
	}

    public function login()
    {
		if(Session::get('serverIP') && Session::get('user') && Session::get('password'))
		{
			return redirect('main/index');
		}
		else
		{
			return view('public.login', ['user' => CRequest::old('user'), 'ip' => CRequest::old('ip')]);
		}
    }

    public function doLogin(Request $request)
    {
		$validate = $this->loginer->validator($request->all());
		if($validate->fails())
		{
			CRequest::flash();
			return redirect('public/login')->withErrors($validate);
		}
		else
		{
			$ftp = new Ftp([
				'host' => strpos($request->input('ip'), 'http://') !== false ? str_replace('http://', '', $request->input('ip')) : $request->input('ip'),
				'username' => $request->input('user'),
				'password' => $request->input('pass'),
			]);

			try
			{
				$ftp->connect();
			}
			catch(\Exception $e)
			{
				return redirect('error')->with('errors', ['登陆信息有误，请验证']);
			}

			Session::put('serverIP', $request->input('ip'));
			Session::put('user', $request->input('user'));
			Session::put('password', $request->input('pass'));

			return redirect('main/index');
		}
    }

    public function test()
    {
        $article = new Article();
        var_dump($article->author()->get());
    }

	public function logout()
	{
		Session::flush();
		return redirect('public/login');
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		//
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		//
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		//
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		//
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		//
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}

}
