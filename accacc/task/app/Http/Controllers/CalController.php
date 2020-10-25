<?php

namespace App\Http\Controllers;

use App\Services\CalService;
use Illuminate\Http\Request;

/**
 * 日历订阅控制器
 *
 * @author edison.an
 *        
 */
class CalController extends Controller {
	/**
	 * CalService 实例.
	 *
	 * @var CalService
	 */
	protected $calService;
	
	/**
	 * 构造方法
	 *
	 * @param CalService $cals        	
	 * @return void
	 */
	public function __construct(CalService $calService) {
		$this->middleware ( 'auth', [ 
				'except' => [ 
						'ics',
						'taskics' 
				] 
		] );
		
		$this->calService = $calService;
	}
	
	/**
	 * 日历订阅首页
	 *
	 * @param Request $request        	
	 * @return \Illuminate\View\View|\Illuminate\Contracts\View\Factory
	 */
	public function index(Request $request) {
		// 处理个人日历提醒相关内容
		$personCalUrl = $this->calService->getPersonCalUrl ( $request->user () );
		
		// 处理公共日历相关内容
		$cals = array (
				array (
						'theme' => '2018 世界杯',
						'url' => 'webcal://task.congcong.us/calics/worldcup' 
				) 
		);
		
		return view ( 'cals.index', [ 
				'person_cal_url' => $personCalUrl,
				'cals' => $cals 
		] );
	}
	
	/**
	 * 根据主题获取日历订阅
	 *
	 * @param Request $request        	
	 * @param String $theme        	
	 */
	public function ics(Request $request, String $theme) {
		$icsInfo = $this->calService->getIcsByTheme ( $theme );
		
		header ( "Content-type:application/octet-stream" );
		header ( "Content-Disposition:attachment;filename = " . $icsInfo ['file_name'] . '.ics' );
		header ( "Accept-ranges:bytes" );
		header ( "Accept-length:" . strlen ( $icsInfo ['file_content'] ) );
		
		readfile ( config ( "app.storage_path" ) . '/' . $icsInfo ['file_name'] );
	}
	
	/**
	 * 获取个人任务日历订阅
	 *
	 * @param Request $request        	
	 * @param String $cal_token        	
	 */
	public function taskics(Request $request, String $cal_token) {
		$icsInfo = $this->calService->getIcsByCalToken ( $cal_token );
		
		header ( "Content-type:application/octet-stream" );
		header ( "Content-Disposition:attachment;filename = " . $icsInfo ['file_name'] . '.ics' );
		header ( "Accept-ranges:bytes" );
		header ( "Accept-length:" . strlen ( $icsInfo ['file_content'] ) );
		
		readfile ( config ( "app.storage_path" ) . '/' . $icsInfo ['file_name'] );
	}
}
