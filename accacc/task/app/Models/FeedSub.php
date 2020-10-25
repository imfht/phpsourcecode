<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeedSub extends Model {
	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [ 
			'feed_id',
			'status',
			'feed_name',
			'category_id',
			'feed_order' 
	];
	
	/**
	 * The attributes that should be cast to native types.
	 *
	 * @var array
	 */
	protected $casts = [ 
			'feed_id' => 'int',
			'user_id' => 'int' 
	];
	protected $appends = array ();
	
	/**
	 * Get the user that owns the task.
	 */
	public function feed() {
		return $this->belongsTo ( Feed::class );
	}
	public function user() {
		return $this->belongsTo ( User::class );
	}
	public function category() {
		return $this->belongsTo ( Category::class );
	}
	
	// public function getUnreadCountAttribute()
	// {
	// return $this->articles->where('status','unread')->count();
	// }
	
	// public function getReadCountAttribute()
	// {
	// return $this->articles->where('status','read')->count();
	// }
	
	// public function getTotalCountAttribute()
	// {
	// return $this->articles->count();
	// }
}
