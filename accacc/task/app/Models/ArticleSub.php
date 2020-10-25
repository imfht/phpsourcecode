<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArticleSub extends Model {
	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [ 
			'feed_id',
			'article_id',
			'status' 
	];
	
	/**
	 * The attributes that should be cast to native types.
	 *
	 * @var array
	 */
	protected $casts = [ 
			'user_id' => 'int',
			'feed_id' => 'int',
			'article_id' => 'int',
			'star_ind' => 'int' 
	];
	
	/**
	 * Get the user that owns the task.
	 */
	public function article() {
		return $this->belongsTo ( Article::class );
	}
	public function user() {
		return $this->belongsTo ( User::class );
	}
}
