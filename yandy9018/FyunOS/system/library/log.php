<?php
final class Log {
	private $filename;
	private $filename_time;
	private $config;
	
	public function __construct($config,$filename,$filename_time='') {
		$this->config=$config;
		$this->filename = $filename;
		$this->filename_time = $filename_time;
	}
	
	public function write($message) {
		$file = DIR_LOGS . $this->filename;
		
		$handle = fopen($file, 'a+'); 
		
		fwrite($handle, date('Y-m-d G:i:s') . ' - ' . $message . "\n");
			
		fclose($handle); 
	}
	
	public function log_time($message) {
		if($this->config->get('config_debug')){
			if($this->filename_time!=''){
				$file = DIR_LOGS . $this->filename_time;
			
				$handle = fopen($file, 'a+');
			
				fwrite($handle, date('Y-m-d G:i:s') . ' | ' . $message . "\n");
					
				fclose($handle);
			}
		}
	}
	
	public function debug($message) {
		if($this->config->get('config_debug')){
			$file = DIR_LOGS . $this->filename;
		
			$handle = fopen($file, 'a+');
		
			fwrite($handle, date('Y-m-d G:i:s') . ' debug::info - ' . $message . "\n");
				
			fclose($handle);
		}
	}
}
?>