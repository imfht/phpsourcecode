<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Third extends Model {
	const SOURCE_FANFOU = 'fanfou';
	const SOURCE_TWITTER = 'twitter';
	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [ 
			'token',
			'token_value',
			'token_secret',
			'source',
			'third_id',
			'third_name' 
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
