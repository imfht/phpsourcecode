<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model {
	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [ 
			'day_pomo_goal',
			'week_pomo_goal',
			'month_pomo_goal',
			'pomo_time',
			'pomo_rest_time',
			'kindle_email',
			'is_start_kindle',
			'with_image_push',
			'cal_token',
			'ifttt_notify' 
	];
	
	/**
	 * The attributes that should be cast to native types.
	 *
	 * @var array
	 */
	protected $casts = [ 
			'user_id' => 'int' 
	];
	
	/**
	 * Get the user that owns the task.
	 */
	public function user() {
		return $this->belongsTo ( User::class );
	}
}
