<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Http\Utils\CommonUtil;

class PomoNotify implements ShouldQueue {
	use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
	protected $user;
	protected $message;
	
	/**
	 * Create a new job instance.
	 *
	 * @return void
	 */
	public function __construct($user, $message) {
		$this->user = $user;
		$this->message = $message;
	}
	
	/**
	 * Execute the job.
	 *
	 * @return void
	 */
	public function handle() {
		$needPomo = \Cache::store ( 'file' )->pull ( 'NEED_POMO' . $this->user->id );
		
		if (! empty ( $needPomo )) {
			if (isset ( $this->user->setting->ifttt_notify )) {
				$notifyResult = CommonUtil::iftttnotify ( '做番茄', $this->message, 'https://task.congcong.us/', $this->user->setting->ifttt_notify );
				\Log::info ( 'notify result:' . $notifyResult . '|message:' . $this->message . '|user:' . $this->user->name );
			}
		}
		return true;
	}
}