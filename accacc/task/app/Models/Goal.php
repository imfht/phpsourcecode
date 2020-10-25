<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Goal extends Model {
	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [ 
			'name',
			'priority',
			'remindtime',
			'deadline',
			'status' 
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
	public function tasks() {
		return $this->hasMany ( Task::class );
	}
}
