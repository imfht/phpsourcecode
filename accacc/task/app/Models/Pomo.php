<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pomo extends Model {
	const DEFAULT_INTERVAL = 1500; // 25min
	const DEFAULT_REST_INTERVAL = 300; // 5min
	const STATUS_INIT = 1;
	const STATUS_PROCESSING = 2;
	const STATUS_FINISHED = 3;
	const STATUS_RESTING = 4;
	
	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [ 
			'name',
			'status',
			'user_id' 
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
	 * Get the user that owns the pomos.
	 */
	public function user() {
		return $this->belongsTo ( User::class );
	}
}
