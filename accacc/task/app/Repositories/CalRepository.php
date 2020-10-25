<?php

namespace App\Repositories;

use App\Models\Cal;

class CalRepository {
	
	/**
	 * Get all of the cals for a given status.
	 *
	 * @param User $user        	
	 * @return Collection
	 */
	public function forByThemeAndStatus(string $theme, string $status, $needPage = false) {
		$cal = Cal::where ( 'status', $status )->where ( 'theme', $theme )->orderBy ( 'id', 'asc' );
		
		if ($needPage) {
			return $cal->paginate ( 10 );
		} else {
			return $cal->get ();
		}
	}
}
