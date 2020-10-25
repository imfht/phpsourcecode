<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model {
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
			'status',
			'parent_task_id',
			'goal_id',
			'is_top',
			'mode' 
	];
	
	/**
	 * The attributes that should be cast to native types.
	 *
	 * @var array
	 */
	protected $casts = [ 
			'user_id' => 'int',
			'parent_task_id' => 'int',
			'goal_id' => 'int' 
	];
	
	/**
	 * Get the user that owns the task.
	 */
	public function user() {
		return $this->belongsTo ( User::class );
	}
	
	/**
	 * Get all of the tags for the user.
	 */
	public function parentTask() {
		return $this->belongsTo ( Task::class, 'parent_task_id' );
	}
	public function childTasks() {
		return $this->hasMany ( Task::class, 'parent_task_id' );
	}
	public function goal() {
		return $this->belongsTo ( Goal::class, 'goal_id' );
	}
}
