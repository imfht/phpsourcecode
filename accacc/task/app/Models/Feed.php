<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Feed extends Model {
	protected $fillable = [ 
			'feed_name',
			'feed_desc',
			'url',
			'favicon',
			'user_id',
			'category_id',
			'type',
			'orders',
			'status',
			'sub_count' 
	];
	protected $table = 'feeds';
	protected $appends = array ();
	protected $casts = [ 
			'user_id' => 'int' 
	];
	public static $recommend_categorys = array (
			1 => 'IT新闻',
			2 => '技术',
			3 => '英语',
			4 => '文学',
			5 => '财经',
			6 => '新闻',
			7 => '微信',
			8 => '微博' 
	);
	public function category() {
		return $this->belongsTo ( Category::class );
	}
	public function articles() {
		return $this->hasMany ( Article::class );
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
	public function user() {
		return $this->belongsTo ( User::class );
	}
}
