<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Common\Helper;
use Session;
use Illuminate\Http\Request;

class MainController extends Controller {

	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		if(!Session::has('pwd'))
			Session::set('pwd', $this->ftp->getRoot());

		$content = $this->_getContent();

		return view('main.index', compact('content'));
	}

	private function _getContent()
	{
		$pwd = Session::get('pwd');
		$content = $this->ftp->listContents($pwd);

		$rawData = $this->ftp->getRawData($pwd);

		$rawDataArr = [];
		$result = [];
		foreach($rawData as $k => $item)
		{
			$item = preg_replace('/\s+/', ',', $item);
			$tmp = explode(',', $item);

			$tmp[8] = isset($tmp[9]) ? implode('', [$tmp[8], $tmp[9]]) : $tmp[8];
			// 组合对应的权限和文件/文件夹名称
			$rawDataArr[$tmp[8]] = $tmp[0];
		}

		// 获取文件修改时间
		foreach($content as $key => &$item)
		{
			if($item['type'] == 'file')
			{
				$temp = $this->ftp->getTimestamp($item['path']);
				$item['last_update_time'] = date('Y-m-d H:i:s', $temp['timestamp']);
			}

			$rawIndex = str_replace(' ', '', basename($item['path']));

			$item['power'] =
				array_key_exists($rawIndex, $rawDataArr) ? $rawDataArr[$rawIndex] : '---';
			$item['path'] = basename($item['path']);
		}
		$this->ftp->disconnect();

		foreach($content as $k => $val)
		{
			$result[$content[$k]['type']][] = $content[$k];
		}

		return $result;
	}

	public function changPwd(Request $request)
	{
		Session::set('pwd', $request->input('pwd'));
		echo json_encode(['content' => $this->_getContent(), 'msg' => 'ok', 'pwd'=>$request->input('pwd')]);
	}

	public function backToPrev()
	{
		$pwd = str_replace('\\', '/', dirname(Session::get('pwd')));
		Session::set('pwd', $pwd);
		echo json_encode(['content' => $this->_getContent(), 'msg' => 'ok', 'pwd'=>$pwd]);
	}

    public function download(Request $request)
    {
        $path = $request->input('path');
        $filename = basename($path);
        if(!is_dir('F://temp123'))
            mkdir('F://temp123');
        ftp_get($this->ftp->getConnection(), 'F://temp123/'.$filename, $path, FTP_ASCII);
		exec('D://Notepad++/notepad++.exe F://temp123/'.$filename);
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
}
