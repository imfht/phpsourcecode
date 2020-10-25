<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mind extends Model {
	protected $fillable = [ 
			'status',
			'name',
			'image_url',
			'content',
			'orders',
			'parent_mind_id',
			'copy_mind_id',
			'is_root' 
	];
	protected $table = 'minds';
	protected $appends = array ();
	protected $casts = [ 
			'user_id' => 'int' 
	];
	
	/**
	 * 拷贝节点
	 */
	public function copyMind() {
		return $this->belongsTo ( Mind::class, 'copy_mind_id' );
	}
	/**
	 * 父亲节点
	 */
	public function parentMind() {
		return $this->belongsTo ( Mind::class, 'parent_mind_id' );
	}
	
	/**
	 * 子节点们
	 */
	public function childrenMinds() {
		return $this->hasMany ( Mind::class, 'parent_mind_id' );
	}
	
	/**
	 * 所属用户
	 */
	public function user() {
		return $this->belongsTo ( User::class );
	}
}
