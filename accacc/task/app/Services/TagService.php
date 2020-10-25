<?php

namespace App\Services;

use App\Models\Tag;

/**
 * 标签业务逻辑
 *
 * @author edison.an
 *        
 */
class TagService {
	/**
	 * get tag by name
	 *
	 * @param unknown $name        	
	 */
	public function forTagName($name) {
		return Tag::where ( 'name', $name )->first ();
	}
}
