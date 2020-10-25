<?php

namespace App\Http\Controllers;

use App\Services\StatisticsService;
use GuzzleHttp\json_encode;
use Illuminate\Http\Request;
use App\Repositories\StatisticsRepository;
use function GuzzleHttp\json_encode;

/**
 * 统计控制器
 *
 * @author edison.an
 *        
 */
class StatisticsController extends Controller {
	
	/**
	 * The statistics repository instance.
	 *
	 * @var StatisticsRepository
	 */
	protected $statistics;
	
	/**
	 * Create a new controller instance.
	 *
	 * @param StatisticsRepository $tasks        	
	 * @return void
	 */
	public function __construct(StatisticsRepository $statistics) {
		$this->middleware ( 'auth' );
		
		$this->statistics = $statistics;
	}
	
	/**
	 * Display a list of all of the user's task.
	 *
	 * @param Request $request        	
	 */
	public function index(Request $request, $add_content = '') {
		$bar_basic_arr = array (
				'tooltip' => array (
						'show' => true 
				),
				'legend' => array (
						'data' => '销量' 
				),
				'xAxis' => array (),
				// array(
				// 'type'=>'category',
				// 'data'=>array()
				// )
				'yAxis' => array (),
				// array(
				// 'type'=>'value',
				// )
				'series' => array () 
		);
		// array(
		// 'name'=>'数量',
		// 'type'=>'bar',
		// 'data'=>array()
		// )
		
		
		
		$pie_basic_arr = array (
				'tooltip' => array (
						'trigger' => 'item',
						'formatter' => '{a} <br/>{b} : {c} ({d}%)' 
				),
				'legend' => array (
						'orient' => 'vertical',
						'x' => 'left',
						'data' => array () 
				),
				'calculable' => true,
				'series' => array () 
		);
		
		$days = 30;
		
		$start_date = date ( 'Y-m-d', strtotime ( "-30 days" ) );
		$end_date = date ( 'Y-m-d' );
		
		$basic_arr = array ();
		for($i = $days; $i >= 0; $i --) {
			$basic_arr [date ( 'Y-m-d', strtotime ( "-$i days" ) )] = 0;
		}
		
		$task_arr = $pomo_arr = $note_arr = $mind_arr = $article_arr = $basic_arr;
		
		$task_statistics = $this->statistics->forUserSpecial ( $request->user (), 'day', 'task', $start_date, $end_date );
		$pomo_statistics = $this->statistics->forUserSpecial ( $request->user (), 'day', 'pomo', $start_date, $end_date );
		$note_statistics = $this->statistics->forUserSpecial ( $request->user (), 'day', 'note', $start_date, $end_date );
		$article_statistics = $this->statistics->forUserSpecial ( $request->user (), 'day', 'article', $start_date, $end_date );
		$mind_statistics = $this->statistics->forUserSpecial ( $request->user (), 'day', 'mind', $start_date, $end_date );
		
		$count_arr = array (
				'task_count' => array (
						'value' => 0,
						'name' => '任务量' 
				),
				'pomo_count' => array (
						'value' => 0,
						'name' => '番茄量' 
				),
				'note_count' => array (
						'value' => 0,
						'name' => '笔记数' 
				),
				'article_count' => array (
						'value' => 0,
						'name' => '阅读数' 
				),
				'mind_count' => array (
						'value' => 0,
						'name' => '思维导图数' 
				) 
		);
		
		foreach ( $task_statistics as $statistic ) {
			$task_arr [date ( 'Y-m-d', strtotime ( $statistic->statistic_date ) )] = $statistic->total;
			$count_arr ['task_count'] ['value'] = $count_arr ['task_count'] ['value'] + $statistic->total;
		}
		$task_bar_statistics = $bar_basic_arr;
		$task_bar_statistics ['legend'] ['data'] = array (
				'任务量' 
		);
		$task_bar_statistics ['xAxis'] [] = array (
				'type' => 'category',
				'data' => array_keys ( $task_arr ) 
		);
		$task_bar_statistics ['yAxis'] [] = array (
				'type' => 'value' 
		);
		$task_bar_statistics ['series'] [] = array (
				'name' => '任务量',
				'type' => 'bar',
				'data' => array_values ( $task_arr ) 
		);
		
		foreach ( $pomo_statistics as $statistic ) {
			$pomo_arr [date ( 'Y-m-d', strtotime ( $statistic->statistic_date ) )] = $statistic->total;
			$count_arr ['pomo_count'] ['value'] = $count_arr ['pomo_count'] ['value'] + $statistic->total;
		}
		$pomo_bar_statistics = $bar_basic_arr;
		$pomo_bar_statistics ['legend'] ['data'] = array (
				'番茄量' 
		);
		$pomo_bar_statistics ['xAxis'] [] = array (
				'type' => 'category',
				'data' => array_keys ( $pomo_arr ) 
		);
		$pomo_bar_statistics ['yAxis'] [] = array (
				'type' => 'value' 
		);
		$pomo_bar_statistics ['series'] [] = array (
				'name' => '番茄量',
				'type' => 'bar',
				'data' => array_values ( $pomo_arr ) 
		);
		
		foreach ( $note_statistics as $statistic ) {
			$note_arr [date ( 'Y-m-d', strtotime ( $statistic->statistic_date ) )] = $statistic->total;
			$count_arr ['note_count'] ['value'] = $count_arr ['note_count'] ['value'] + $statistic->total;
		}
		
		$note_bar_statistics = $bar_basic_arr;
		$note_bar_statistics ['legend'] ['data'] = array (
				'笔记量' 
		);
		$note_bar_statistics ['xAxis'] [] = array (
				'type' => 'category',
				'data' => array_keys ( $note_arr ) 
		);
		$note_bar_statistics ['yAxis'] [] = array (
				'type' => 'value' 
		);
		$note_bar_statistics ['series'] [] = array (
				'name' => '笔记量',
				'type' => 'bar',
				'data' => array_values ( $note_arr ) 
		);
		
		foreach ( $article_statistics as $statistic ) {
			$article_arr [date ( 'Y-m-d', strtotime ( $statistic->statistic_date ) )] = $statistic->total;
			$count_arr ['article_count'] ['value'] = $count_arr ['article_count'] ['value'] + $statistic->total;
		}
		$article_bar_statistics = $bar_basic_arr;
		$article_bar_statistics ['legend'] ['data'] = array (
				'阅读量' 
		);
		$article_bar_statistics ['xAxis'] [] = array (
				'type' => 'category',
				'data' => array_keys ( $article_arr ) 
		);
		$article_bar_statistics ['yAxis'] [] = array (
				'type' => 'value' 
		);
		$article_bar_statistics ['series'] [] = array (
				'name' => '笔记量',
				'type' => 'bar',
				'data' => array_values ( $article_arr ) 
		);
		
		foreach ( $mind_statistics as $statistic ) {
			$mind_arr [date ( 'Y-m-d', strtotime ( $statistic->statistic_date ) )] = $statistic->total;
			$count_arr ['mind_count'] ['value'] = $count_arr ['mind_count'] ['value'] + $statistic->total;
		}
		$mind_bar_statistics = $bar_basic_arr;
		$mind_bar_statistics ['legend'] ['data'] = array (
				'导图量' 
		);
		$mind_bar_statistics ['xAxis'] [] = array (
				'type' => 'category',
				'data' => array_keys ( $mind_arr ) 
		);
		$mind_bar_statistics ['yAxis'] [] = array (
				'type' => 'value' 
		);
		$mind_bar_statistics ['series'] [] = array (
				'name' => '笔记量',
				'type' => 'bar',
				'data' => array_values ( $mind_arr ) 
		);
		
		$count_pie_statistics = $pie_basic_arr;
		$count_pie_statistics ['series'] [] = array (
				'name' => '数量汇总',
				'type' => 'pie',
				'radius' => '55%',
				'center' => array (
						'50%',
						'60%' 
				),
				'data' => array_values ( $count_arr ) 
		);
		foreach ( $count_arr as $count_info ) {
			$count_pie_statistics ['legend'] ['data'] [] = $count_info ['name'];
		}
		
		return view ( 'statistics.index', [ 
				'task_bar_statistics' => \json_encode ( $task_bar_statistics ),
				'pomo_bar_statistics' => \json_encode ( $pomo_bar_statistics ),
				'note_bar_statistics' => \json_encode ( $note_bar_statistics ),
				'article_bar_statistics' => \json_encode ( $article_bar_statistics ),
				'mind_bar_statistics' => \json_encode ( $mind_bar_statistics ),
				'count_pie_statistics' => \json_encode ( $count_pie_statistics ),
				'start_date' => $start_date,
				'end_date' => $end_date 
		] );
	}
}
