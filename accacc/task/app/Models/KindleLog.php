<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KindleLog extends Model {
	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [ 
			'type',
			'path',
			'status' 
	];
	
	/**
	 * The attributes that should be cast to native types.
	 *
	 * @var array
	 */
	protected $casts = [ ];
	
	/**
	 * Get the user that owns the task.
	 */
	public function user() {
		return $this->belongsTo ( User::class );
	}
}
