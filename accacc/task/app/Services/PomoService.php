<?php

namespace App\Services;

use App\Models\Pomo;
use App\Models\User;
use Illuminate\Support\Facades\Session;
use App\Repositories\PomoRepository;
use App\Repositories\SettingRepository;
use App\Jobs\PomoNotify;
use App\Jobs\Job;

/**
 * 番茄工作法业务逻辑
 *
 * @author edison.an
 *        
 */
class PomoService {
	protected $pomos;
	protected $settings;
	
	/**
	 */
	public function __construct(PomoRepository $pomos, SettingRepository $settings) {
		$this->pomos = $pomos;
		$this->settings = $settings;
	}
	public function startPomo($user) {
		$active_pomo = $this->pomos->forUserActivePomo ( $user );
		if (empty ( $active_pomo )) {
			$active_pomo = $this->pomos->create ( [ 
					'name' => '',
					'status' => 1,
					'user_id' => $user->id 
			] );
		}
		$currentPomoInfo = $this->getCurrentPomoInfo ( $user, $active_pomo );
		$this->pomonotify ( $user, $currentPomoInfo ['current_pomo_status'] == Pomo::STATUS_PROCESSING ? '您已经完成了一个番茄，快来记录一下吧~' : '休息完成，快来开始下一个番茄吧~', $currentPomoInfo ['current_pomo_remain'] );
		return $currentPomoInfo;
	}
	
	/**
	 * 获取该用户番茄状态
	 *
	 * @param unknown $user        	
	 * @return number[]|\App\Models\Pomo[]|unknown[]
	 */
	public function getCurrentPomoInfo($user, $active_pomo = '') {
		// 获取当前用户设置
		$setting = $user->setting;
		
		// 1默认等待中 2进行中 3已经完成 4休息中 5休息结束
		$current_pomo_status = Pomo::STATUS_INIT;
		$current_pomo_remain = 0;
		
		$pomo_time = isset ( $setting->pomo_time ) && ! empty ( $setting->pomo_time ) ? $setting->pomo_time * 60 : Pomo::DEFAULT_INTERVAL;
		$pomo_rest_time = isset ( $setting->pomo_time ) && ! empty ( $setting->pomo_rest_time ) ? $setting->pomo_rest_time * 60 : Pomo::DEFAULT_REST_INTERVAL;
		
		if (empty ( $active_pomo )) {
			$active_pomo = Pomo::where ( 'user_id', $user->id )->where ( 'status', 1 )->first ();
		}
		if (! empty ( $active_pomo )) {
			$pomo_start_time = strtotime ( $active_pomo->created_at );
			$remain_time = $pomo_start_time + $pomo_time - time ();
			if ($remain_time > 0) {
				$current_pomo_status = Pomo::STATUS_PROCESSING;
				$current_pomo_remain = $remain_time;
			} else {
				$current_pomo_status = Pomo::STATUS_FINISHED;
			}
		} else {
			$rest_start_time = Session::get ( 'rest_start_time', 0 );
			if (! empty ( $rest_start_time )) {
				$remain_time = $rest_start_time + $pomo_rest_time - time ();
				if ($remain_time > 0) {
					$current_pomo_status = Pomo::STATUS_RESTING;
					$current_pomo_remain = $remain_time;
				}
			}
		}
		
		return array (
				'active_pomo' => empty ( $active_pomo ) ? new Pomo () : $active_pomo,
				'current_pomo_status' => $current_pomo_status,
				'current_pomo_remain' => $current_pomo_remain 
		);
	}
	
	/**
	 * 依据番茄状态获取信息头提示
	 *
	 * @param unknown $pomo_status        	
	 * @return number[]|string[]
	 */
	public function getTipInfo($pomo_status) {
		$tip_type = 0;
		$tip_message = '';
		
		if ($pomo_status == Pomo::STATUS_FINISHED) {
			$tip_type = 1;
			$tip_message = '您已经完成了一个番茄，快来记录一下吧~';
		} else {
			$hour = date ( 'H' );
			if ($hour < 10 && $hour > 6 && ! isset ( $_COOKIE [date ( 'Ymd' ) . 'morning_tip'] )) {
				$tip_type = 2;
				$tip_message = '一日之计在于晨，写个<a href="' . url ( '/notes', array (
						'add_content',
						'#今日小目标#' 
				) ) . '">今日小目标</a>吧';
			} else if ($hour > 18 && $hour < 22 && ! isset ( $_COOKIE [date ( 'Ymd' ) . 'afternoon_tip'] )) {
				$tip_type = 3;
				$tip_message = '今天过得怎么样，写个<a href="' . url ( '/notes', array (
						'add_content',
						'#每日总结#' 
				) ) . '">每日总结</a>吧';
			}
		}
		
		return array (
				'tip_type' => $tip_type,
				'tip_message' => $tip_message 
		);
	}
	public function pomonotify($user, $message, $delay) {
		\Cache::store ( 'file' )->put ( 'NEED_POMO' . $user->id, 'OK', $delay + 300 );
		PomoNotify::dispatch ( $user, $message )->delay ( now ()->addSecond ( $delay ) );
	}
	public function clearpomonotify($user) {
		\Cache::store ( 'file' )->pull ( 'NEED_POMO' . $user->id );
	}
}
