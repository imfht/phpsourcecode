<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Statistics extends Model {
	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [ 
			'user_id',
			'date_type',
			'data_type',
			'total',
			'statistic_date' 
	];
	protected $table = 'statistics';
	
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
}
