<?php

namespace App\Repositories;

use App\Models\User;
use App\Models\Statistics;

class StatisticsRepository {
	/**
	 * Get all of the notes for a given user.
	 *
	 * @param User $user        	
	 * @return Collection
	 */
	public function forUserSpecial(User $user, $date_type, $data_type, $start_date, $end_date) {
		return Statistics::where ( 'user_id', $user->id )->where ( 'date_type', $date_type )->where ( 'data_type', $data_type )->where ( 'statistic_date', '>', $start_date )->where ( 'statistic_date', '<=', $end_date )->orderBy ( 'created_at', 'desc' )->get ();
	}
}
