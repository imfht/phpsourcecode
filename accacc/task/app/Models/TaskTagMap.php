<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskTagMap extends Model {
	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [ 
			'task_id',
			'note_id' 
	];
	
	/**
	 * The attributes that should be cast to native types.
	 *
	 * @var array
	 */
	protected $casts = [ 
			'tag_id' => 'int',
			'task_id' => 'int' 
	];
	
	/**
	 * Get the user that owns the task.
	 */
	public function tag() {
		return $this->belongsTo ( Tag::class );
	}
	
	/**
	 * Get all of the tags for the user.
	 */
	public function task() {
		return $this->belongsTo ( Task::class );
	}
	
	/**
	 */
	public function taskTagMaps() {
		return $this->hasMany ( TaskTagMap::class );
	}
}
