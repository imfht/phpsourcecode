<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Note;
use App\Models\Task;
use App\Models\Pomo;
use App\Models\Mind;
use App\Models\ArticleSub;
use App\Models\Statistics;
use DB;
use Log;

/**
 * statistics pomo note task etc.
 *
 * @author edison.an
 *        
 */
class StatisticsCron extends Command {
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'statistics_cron';
	
	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Statistic Task Pomo Note etc.';
	
	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle() {
		$date_type = 'day';
		
		$start_time = date ( 'Y-m-d', strtotime ( date ( 'Y-m-d' ) ) - 86400 );
		$end_time = date ( 'Y-m-d' );
		
		Log::info ( 'statistics:' . $start_time . '|' . $end_time );
		
		// get count infos
		$note_counts = Note::select ( 'user_id', DB::raw ( 'count(*) as total' ) )->where ( 'updated_at', '>', $start_time )->where ( 'updated_at', '<=', $end_time )->groupBy ( 'user_id' )->get ();
		$task_counts = Task::select ( 'user_id', DB::raw ( 'count(*) as total' ) )->where ( 'status', 2 )->where ( 'updated_at', '>', $start_time )->where ( 'updated_at', '<=', $end_time )->groupBy ( 'user_id' )->get ();
		$pomo_counts = Pomo::select ( 'user_id', DB::raw ( 'count(*) as total' ) )->where ( 'status', 2 )->where ( 'updated_at', '>', $start_time )->where ( 'updated_at', '<=', $end_time )->groupBy ( 'user_id' )->get ();
		$article_counts = ArticleSub::select ( 'user_id', DB::raw ( 'count(*) as total' ) )->where ( 'status', 'read' )->where ( 'updated_at', '>', $start_time )->where ( 'updated_at', '<=', $end_time )->groupBy ( 'user_id' )->get ();
		$mind_counts = Mind::select ( 'user_id', DB::raw ( 'count(*) as total' ) )->where ( 'created_at', '>', $start_time )->where ( 'created_at', '<=', $end_time )->groupBy ( 'user_id' )->get ();
		
		foreach ( $note_counts as $count_info ) {
			$param_arr = [ 
					'user_id' => $count_info ['user_id'],
					'data_type' => 'note',
					'date_type' => $date_type,
					'statistic_date' => $start_time 
			];
			
			$statistics = Statistics::where ( $param_arr )->first ();
			$param_arr ['total'] = $count_info ['total'];
			
			if (empty ( $statistics )) {
				$statistics = new Statistics ();
				$statistics->create ( $param_arr );
			} else {
				$statistics->update ( $param_arr );
			}
			Log::info ( $count_info ['user_id'] . $count_info ['total'] );
		}
		
		foreach ( $task_counts as $count_info ) {
			$param_arr = [ 
					'user_id' => $count_info ['user_id'],
					'data_type' => 'task',
					'date_type' => $date_type,
					'statistic_date' => $start_time 
			];
			
			$statistics = Statistics::where ( $param_arr )->first ();
			$param_arr ['total'] = $count_info ['total'];
			
			if (empty ( $statistics )) {
				$statistics = new Statistics ();
				$statistics->create ( $param_arr );
			} else {
				$statistics->update ( $param_arr );
			}
			Log::info ( $count_info ['user_id'] . $count_info ['total'] );
		}
		
		foreach ( $pomo_counts as $count_info ) {
			$param_arr = [ 
					'user_id' => $count_info ['user_id'],
					'data_type' => 'pomo',
					'date_type' => $date_type,
					'statistic_date' => $start_time 
			];
			
			$statistics = Statistics::where ( $param_arr )->first ();
			$param_arr ['total'] = $count_info ['total'];
			
			if (empty ( $statistics )) {
				$statistics = new Statistics ();
				$statistics->create ( $param_arr );
			} else {
				$statistics->update ( $param_arr );
			}
			Log::info ( $count_info ['user_id'] . $count_info ['total'] );
		}
		
		foreach ( $article_counts as $count_info ) {
			$param_arr = [ 
					'user_id' => $count_info ['user_id'],
					'data_type' => 'article',
					'date_type' => $date_type,
					'statistic_date' => $start_time 
			];
			
			$statistics = Statistics::where ( $param_arr )->first ();
			$param_arr ['total'] = $count_info ['total'];
			
			if (empty ( $statistics )) {
				$statistics = new Statistics ();
				$statistics->create ( $param_arr );
			} else {
				$statistics->update ( $param_arr );
			}
			Log::info ( $count_info ['user_id'] . $count_info ['total'] );
		}
		
		foreach ( $mind_counts as $count_info ) {
			$param_arr = [ 
					'user_id' => $count_info ['user_id'],
					'data_type' => 'mind',
					'date_type' => $date_type,
					'statistic_date' => $start_time 
			];
			
			$statistics = Statistics::where ( $param_arr )->first ();
			$param_arr ['total'] = $count_info ['total'];
			
			if (empty ( $statistics )) {
				$statistics = new Statistics ();
				$statistics->create ( $param_arr );
			} else {
				$statistics->update ( $param_arr );
			}
			Log::info ( $count_info ['user_id'] . $count_info ['total'] );
		}
	}
}
