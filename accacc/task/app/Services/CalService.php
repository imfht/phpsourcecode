<?php

namespace App\Services;

use App\Http\Utils\ICSUtil;
use App\Http\Utils\ICSUtil2;
use App\Models\Setting;
use App\Models\User;
use App\Repositories\CalRepository;
use App\Repositories\PomoRepository;
use App\Repositories\SettingRepository;
use App\Repositories\TaskRepository;

/**
 * 日历订阅相关Service
 *
 * @author edison.an
 *        
 */
class CalService {
	protected $cals;
	protected $settings;
	protected $tasks;
	
	/**
	 *
	 * @param PomoRepository $pomos        	
	 */
	public function __construct(CalRepository $cals, SettingRepository $settings, TaskRepository $tasks) {
		$this->cals = $cals;
		$this->settings = $settings;
		$this->tasks = $tasks;
	}
	
	/**
	 * 获取个人日历订阅地址
	 *
	 * @param User $user        	
	 * @return string
	 */
	public function getPersonCalUrl(User $user) {
		$cal_token = '';
		$setting = $this->settings->forUser ( $user );
		if (isset ( $setting ['cal_token'] ) && ! empty ( $setting ['cal_token'] )) {
			$cal_token = $setting ['cal_token'];
		} else {
			$default_cal_token = md5 ( $user->id . '_' . time () );
			if (empty ( $setting )) {
				$setting = new Setting ();
				$setting->user_id = $user->id;
				$setting->save ( array (
						'cal_token' => $default_cal_token 
				) );
			} else if (empty ( $setting ['cal_token'] )) {
				$setting->update ( array (
						'cal_token' => $default_cal_token 
				) );
			}
			$cal_token = $default_cal_token;
		}
		
		return 'webcal://task.congcong.us/taskics/' . $cal_token;
	}
	
	/**
	 * 根据主题获取相关日历订阅
	 *
	 * @param User $user        	
	 * @return string
	 */
	public function getIcsByTheme($theme) {
		date_default_timezone_set ( "Asia/Shanghai" );
		
		$cals = $this->cals->forByThemeAndStatus ( $theme, 1 );
		
		$task_props = array ();
		foreach ( $cals as $cal ) {
			$task_props [] = array (
					'description' => $cal->desc,
					'dtend' => $cal->dtend,
					'dtstart' => $cal->dtstart,
					'location' => $cal->location,
					'summary' => $cal->summary,
					'url' => $cal->url 
			);
		}
		
		$ics = new ICSUtil ( $task_props );
		$ics->cal_name = $theme;
		$ics_file_contents = $ics->to_string ();
		
		$file_name = 'task_ics_' . md5 ( $theme );
		file_put_contents ( config ( "app.storage_path" ) . '/' . $file_name, $ics_file_contents );
		
		return array (
				'file_name' => $file_name,
				'file_content' => $ics_file_contents 
		);
	}
	
	/**
	 * 根据日历token获取个人日历订阅
	 *
	 * @param User $user        	
	 * @param string $cal_token        	
	 * @return string[]
	 */
	public function getIcsByCalToken($cal_token) {
		date_default_timezone_set ( "Asia/Shanghai" );
		
		// 获取该用户user_id
		$setting = $this->settings->forCalToken ( $cal_token );
		if (isset ( $setting ['user_id'] )) {
			$user_id = $setting ['user_id'];
		} else {
			echo 'error cal_token';
			exit ();
		}
		
		// 根据开始时间和结束时间，查询需要提醒内容
		$start_time = date ( 'Y-m-d H:i:s', time () - 15768000 );
		$end_time = date ( 'Y-m-d H:i:s', strtotime ( $start_time ) + 31536000 );
		$tasks = $this->tasks->forUserByUserIdRemindTime ( $user_id, $start_time, $end_time );
		
		$task_props = array ();
		foreach ( $tasks as $task ) {
			$task_props [] = array (
					'dtend' => $task->remindtime,
					'dtstart' => $task->remindtime,
					'due' => $task->remindtime,
					'completed' => $task->remindtime,
					'summary' => $task->name,
					'repeat' => 1,
					'status' => 'NEEDS-ACTION' 
			);
		}
		
		$ics = new ICSUtil2 ( $task_props );
		$ics_file_contents = $ics->to_string ();
		
		file_put_contents ( config ( "app.storage_path" ) . '/task_ics_' . $user_id, $ics_file_contents );
		
		return array (
				'file_name' => "task_ics_" . $user_id,
				'file_content' => $ics_file_contents 
		);
	}
}
