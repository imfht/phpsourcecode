<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Feedback extends Model {
	protected $fillable = [ 
			'from',
			'content' 
	];
	protected $table = 'feedbacks';
	protected $casts = [ 
			'user_id' => 'int' 
	];
	public function user() {
		return $this->belongsTo ( User::class );
	}
}
