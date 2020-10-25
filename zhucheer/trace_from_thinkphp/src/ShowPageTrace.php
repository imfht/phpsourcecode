<?php
namespace Cheer\TpTrace;

use DB;
class ShowPageTrace
{
	protected $tracePageTabs =  array('BASE'=>'基本','FILE'=>'文件','SQL'=>'SQL','DEBUG'=>'调试');
	
	public function show(){
		if( (request()->ajax() != true || request()->server('HTTP_USER_AGENT') == '') && 
			config('thinkphp_trace.show_page_trace') == true &&
			config('app.debug') == true
		){
			$tracePage = $this->showTrace();
		}
	}
	
	
	/**
	 * 显示页面Trace信息
	 * @access private
	 */
	public function showTrace() {

		$info = $this->getRequrieFile();
		$base = $this->getBaseInfo();
		$sql = $this->getSqlInfo();
		$log = $this->getLogInfo();
		
		$debug['SQL'] = $sql['queryList'];
		$debug['DEBUG'] = $log;
		
		$trace  =   array();
		$tabs   =   $this->tracePageTabs;
		foreach ($tabs as $name=>$title){
			switch(strtoupper($name)) {
				case 'BASE':// 基本信息
					$trace[$title]  =   $base;
					break;
				case 'FILE': // 文件信息
					$trace[$title]  =   $info;
					break;
				default:// 调试信息
					$name       =   strtoupper($name);
					if(strpos($name,'|')) {// 多组信息
						$array  =   explode('|',$name);
						$result =   array();
						foreach($array as $name){
							$result   +=   isset($debug[$name])?$debug[$name]:array();
						}
						$trace[$title]  =   $result;
					}else{
						$trace[$title]  =   isset($debug[$name])?$debug[$name]:'';
					}
			}
		}
		
		$html = response()->view('traceview::trace',['trace'=>$trace, 'queryTime'=>$sql['totalTime']])->getContent();
		echo $html;
	}
	
	/**
	 * 获取文件加载信息
	 */
	private function getRequrieFile(){
		// 系统默认显示信息
		$files  =  get_included_files();
		$info   =   array();
		foreach ($files as $key=>$file){
			$info[] = $file.' ( '.number_format(filesize($file)/1024,2).' KB )';
		}
		return $info;
	}

	/**
	 * 获取环境基本信息
	 */
	private function getBaseInfo(){
		$uri = request()->getRequestUri();
		$base   =   array(
				'请求信息'  =>  date('Y-m-d H:i:s',$_SERVER['REQUEST_TIME']).' '.$_SERVER['SERVER_PROTOCOL'].' '.$_SERVER['REQUEST_METHOD'].' : '.$uri,
				//'运行时间'  =>  $this->showTime(),
				'内存开销'  =>  memory_get_usage()?number_format((memory_get_usage() - $GLOBALS['_startUseMems'])/1024,2).' kb':'不支持',
				'文件加载'  =>  count(get_included_files()),
		
		);
		return $base;
	}
	
	/**
	 * 获取sql数据
	 */
	private function getSqlInfo(){
		$queryInfo = $GLOBALS['_querySql'];
		$queryList = [];
		$totalTime = 0;
		if(empty($queryInfo)){
			return  ['queryList'=>[], 'totalTime'=>0];;
		}		
		foreach($queryInfo as $item){
			$realSql = $item['sql'];
			if(!empty($item['bindings'])){
				foreach($item['bindings'] as $value){
					$valuePos = strpos($realSql, '?');
					$param = is_numeric($value) ? $value : ('"'.$value.'"');
					
					$realSql = substr_replace($realSql, $param, $valuePos, 1);
				}
			}
			$queryList[] = $realSql.'; ['.$item['time'].'s]';
			$totalTime += $item['time'];
		}
		$totalTime = round($totalTime, 2);
		
		return ['queryList'=>$queryList, 'totalTime'=>$totalTime];
	}
	
	
	/**
	 * 获取日志数据
	 */
	public function getLogInfo(){
		$logInfo = $GLOBALS['_debugInfo'];
		$logList = [];
		
		if(empty($logInfo)){
			return $logList;
		}

		foreach($logInfo as $item){
			$level = strtoupper($item['level']);
			if(is_object($item['message'])){
				$msg = $item['message']->getMessage();
			}else{
				$msg = strval($item['message']);
			}
 			$logList[] = $level.':'.$msg;
		}
		
		return $logList;
	}
	
	
	
	
}