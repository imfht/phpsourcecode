<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

class UploadBase64Controller extends Controller {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		//
	}

	public function upload()
	{
		$imgstr = $_POST['imgstr'];
		preg_match("/^data:image\/([A-Za-z-+\/]+);base64,(.+)$/",$imgstr,$matches);
		$filetype 	= $matches[1];
		$base 		= $matches[2];
		$datestamp	= date('Y_m_d_H_i_s');
		$randkey	= rand(100,1000);
		$datestamp	= $datestamp.$randkey;

		if($filetype == "jpeg"){
			$filetype = "jpg";
		}
		
		$savname 	= $datestamp.".".$filetype;
		file_put_contents('/alidata/www/laravelupload/upimg/'.$savname, base64_decode($base));
		return $savname;
	}

	function download($filename)
	{
		$filepath = '/alidata/www/learavelupload/upimg/'.$filename;
		header('Content-Description: File Transfer');
		header('Content-Type: application/octet-stream');
		header('Content-Disposition: attachment; filename='.basename($filepath));
		header('Content-Transfer-Encoding: binary');
		header('Expires: 0');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Pragma: public');
		header('Content-Length: ' . filesize($filepath));
		readfile($filepath);
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
