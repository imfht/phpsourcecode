<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model {
	protected $fillable = [ 
			'name',
			'user_id',
			'category_order' 
	];
	protected $table = 'categories';
	protected $appends = array ();
	protected $casts = [ 
			'user_id' => 'int' 
	];
	public function feedSubs() {
		return $this->hasMany ( FeedSub::class )->orderBy ( 'created_at' );
	}
	
	// public function getUnreadCountAttribute()
	// {
	// return DB::table('feed_subs')->join('articles', 'feeds.id', '=', 'articles.feed_id')->where('feeds.category_id', $this->id)->where('articles.status', 'unread')->count();
	// }
	
	// public function getReadCountAttribute()
	// {
	// return DB::table('feed_subs')->join('articles', 'feeds.id', '=', 'articles.feed_id')->where('feeds.category_id', $this->id)->where('articles.status', 'unread')->count();
	// }
	
	// public function getTotalCountAttribute()
	// {
	// return DB::table('feed_subs')->join('articles', 'feeds.id', '=', 'articles.feed_id')->where('feeds.category_id', $this->id)->count();
	// }
	public function user() {
		return $this->belongsTo ( User::class );
	}
}
