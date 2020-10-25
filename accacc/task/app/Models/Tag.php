<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model {
	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [ 
			'name',
			'status' 
	];
	
	/**
	 * The attributes that should be cast to native types.
	 *
	 * @var array
	 */
	protected $casts = [ ];
	// 'user_id' => 'int',
	
}
