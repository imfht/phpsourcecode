<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OauthInfo extends Model {
	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [ 
			'user_id',
			'expire',
			'access_token',
			'driver',
			'third_uid',
			'created_at',
			'updated_at' 
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
