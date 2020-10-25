<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NoteTagMap extends Model {
	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [ 
			'tag_id',
			'note_id' 
	];
	
	/**
	 * The attributes that should be cast to native types.
	 *
	 * @var array
	 */
	protected $casts = [ 
			'tag_id' => 'int',
			'note_id' => 'int' 
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
	public function note() {
		return $this->belongsTo ( Note::class );
	}
}
