<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cal extends Model {
	protected $fillable = [ 
			'status',
			'subject',
			'url',
			'image_url',
			'content',
			'published' 
	];
	protected $table = 'cals';
	protected $appends = array ();
	protected $casts = [ ];
}
