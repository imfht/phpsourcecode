<?php

namespace App\Repositories;

use App\Models\User;
use App\Models\Tag;

class TagRepository {
	/**
	 * get tag by name
	 *
	 * @param unknown $name        	
	 */
	public function forTagName($name) {
		return Tag::where ( 'name', $name )->first ();
	}
}
