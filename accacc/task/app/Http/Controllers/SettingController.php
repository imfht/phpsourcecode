<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;
use App\Http\Utils\ErrorCodeUtil;
use App\Services\SettingService;

/**
 * 设置控制器
 *
 * @author edison.an
 *        
 */
class SettingController extends Controller {
	
	/**
	 * The settings repository instance.
	 *
	 * @var SettingRepository
	 */
	protected $settings;
	
	/**
	 * Create a new controller instance.
	 *
	 * @param SettingRepository $settings        	
	 * @return void
	 */
	public function __construct(SettingService $settings) {
		$this->middleware ( 'auth', [ 
				'except' => [ 
						'welcome' 
				] 
		] );
		
		$this->settings = $settings;
	}
	
	/**
	 * 首页.
	 *
	 * @param Request $request        	
	 */
	public function index(Request $request) {
		$page_params = array ();
		
		$setting = $this->settings->forUser ( $request->user () );
		
		if (empty ( $setting )) {
			$setting = new Setting ();
		}
		return view ( 'settings.index', [ 
				'setting' => $setting 
		] );
	}
	
	/**
	 * 更新.
	 *
	 * @param Request $request        	
	 * @param Setting $setting        	
	 */
	public function update(Request $request, Setting $setting) {
		$this->validate ( $request, [ 
				'day_pomo_goal' => 'integer|min:1',
				'week_pomo_goal' => 'integer|min:1',
				'month_pomo_goal' => 'integer|min:1',
				'pomo_time' => 'integer|min:10|max:60',
				'pomo_rest_time' => 'integer|min:1|max:10',
				'is_start_kindle' => 'integer|min:0|max:1',
				'with_image_push' => 'integer|min:0|max:1',
				'kindle_email' => 'email' 
		] );
		
		if (empty ( $setting->user_id )) {
			$setting = $this->settings->forUser ( $request->user () );
			if (! empty ( $setting )) {
				echo 'error';
				exit ();
			}
			
			$setting = new Setting ();
			$setting->user_id = $request->user ()->id;
			$setting->save ( $request->all () );
		} else {
			$this->authorize ( 'destroy', $setting );
			$setting->update ( $request->all () );
		}
		
		if ($request->ajax () || $request->wantsJson ()) {
			$resp = $this->responseJson ( self::OK_CODE );
			return response ( $resp );
		} else {
			if ($request->has ( 'page_info' ) && $request->page_info == 'kindle_page') {
				return redirect ( '/kindles' )->with ( 'message', '操作成功!' );
			} else {
				return redirect ( '/settings' )->with ( 'message', '操作成功!' );
			}
		}
	}
}
