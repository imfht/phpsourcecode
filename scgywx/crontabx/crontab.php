<?php
if($_SERVER['argc'] < 2){
	echo "usage: php crontab.php config.php [start|stop|restart]\n";
	exit;
}

$conf = $_SERVER['argv'][1];
$op = isset($_SERVER['argv'][2]) ? $_SERVER['argv'][2] : 'start';
$cron = new CrontabX($conf);
$cron->run($op);

class CrontabX
{
	private $file = null;
	private $error = null;
	private $pidfile = null;
	private $logfile = null;
	private $loglevel = 2;
	private $crontab = array();
	private $daemon = array();
	private $childs = array();
	private $daemonize = false;
	
	const LOG_NONE = 0;
	const LOG_ERROR = 1;
	const LOG_INFO = 2;
	
	public function __construct($conf)
	{
		$this->file = realpath($conf);
	}
	
	public function run($op)
	{
		if(!$this->load()){
			echo "parse config file, reason: $this->error\n";
			return ;
		}
		
		switch($op){
			case 'start':
				$this->start();
				break;
			case 'stop':
				$this->stop();
				break;
			case 'restart':
				$this->restart();
				break;
			default:
				echo "invalid op code\n";
				break;
		}
	}
	
	private function start()
	{
		if($this->daemonize){
			$pid = pcntl_fork();
			switch($pid){
				case -1: //error
					echo "fork error\n";
					return ;
				case 0: //child
					$sid = posix_setsid();
					if($sid < 0){
						echo "setsid error\n";
						return ;
					}
				
					global $STDOUT, $STDERR;
					fclose(STDOUT);
					fclose(STDERR);
					$STDOUT = fopen('/dev/null', "rw+");
					$STDERR = fopen('/dev/null', "rw+");
					break;
				default: //master
					exit(0);
			}
		}
		
		file_put_contents($this->pidfile, getmypid());
		
		$this->setupSignal();
		
		$this->runDaemon();
		
		$last = 0;
		do{
			$now = time();
			if($last != $now){
				$last = $now;
				$this->runCrontab();
				sleep(1);
			}else{
				$ms = 1000 - (int)(((microtime(true) - $now)) * 1000);
				if($ms > 0){
					usleep($ms * 1000);
				}
			}
			
			pcntl_signal_dispatch();
		}while(true);
		
		unlink($this->pidfile);
	}
	
	private function stop()
	{
		if(!file_exists($this->pidfile)){
			echo "pidfile not found!\n";
			return ;
		}
		
		$pid = file_get_contents($this->pidfile);
		if(!$pid){
			echo "crontab no start\n";
			return ;
		}
		
		echo $pid,"\n";
		posix_kill($pid, SIGTERM);
	}
	
	private function restart()
	{
		if(!file_exists($this->pidfile)){
			echo "pidfile not found!\n";
			return ;
		}
			
		$pid = file_get_contents($this->pidfile);
		if(!$pid){
			echo "crontab no start\n";
			return ;
		}
		
		echo $pid,"\n";
		posix_kill($pid, SIGUSR1);
	}
	
	private function load()
	{
		if(!file_exists($this->file)){
			$this->error = "file not found, file=$this->file";
			return false;
		}
		
		//检测配置文件
		$config = require($this->file);
		if(!isset($config['pidfile'])){
			$this->error = "pidfile empty";
			return false;
		}
		
		//检测日志文件
		if(!isset($config['logfile'])){
			$this->error = "logfile empty";
			return false;
		}
		
		//pid与日志文件
		$this->pidfile = $config['pidfile'];
		$this->logfile = $config['logfile'];
		$this->daemonize = $config['daemonize'];
		
		//守护进程与计划任务
		foreach($config['crontab'] as $index => $line){
			if(empty($line['name'])){
				$this->error = "the $index item's name is empty";
				return false;
			}
			
			$script = realpath($line['script']);
			if(empty($script) || !file_exists($script)){
				$this->error = "script file not found by $line[name]";
				return false;
			}
			
			if(empty($line['daemon'])){
				$wakeup = $this->parseWakeup($line['wakeup']);
				if(!$wakeup){
					$this->error = "invalid wakeup format by $line[name]";
					return false;
				}
				
				if(isset($this->crontab[$line['name']])){
					$this->error = "crontab name is exists by $line[name]";
					return false;
				}
				
				$this->crontab[$line['name']] = array(
					'name' => $line['name'],
					'script' => $script,
					'wakeup' => $wakeup
				);
			}else{
				if(isset($this->daemon[$line['name']])){
					$this->error = "daemon name is exists by $line[name]";
					return false;
				}
				
				$this->daemon[$line['name']] = array(
					'name' => $line['name'],
					'script' => $script,
					'childs' => (int)$line['childs'],
				); 
			}
		}
		
		return true;
	}
	
	private function setupSignal()
	{
		pcntl_signal(SIGPIPE, SIG_IGN);
		if($this->daemonize){
			pcntl_signal(SIGHUP, SIG_IGN);
		}
		
		pcntl_signal(SIGUSR1, array($this, 'procSignal'));
		pcntl_signal(SIGCHLD, array($this, 'procSignal'));
		pcntl_signal(SIGTERM, array($this, 'procSignal'));
		pcntl_signal(SIGINT, array($this, 'procSignal'));
	}
	
	private function procSignal($sig)
	{
		//$this->log("recv sig=$sig");
		switch($sig){
			case SIGINT:
			case SIGTERM:
				//stop
				posix_kill(0, SIGKILL);
				break;
			case SIGUSR1:
				//restart or reload
				break;
			case SIGCHLD:
				//child exit
				$status = 0;
				$pid = pcntl_wait($status, WNOHANG);
				if($pid == -1){
					return ;
				}
				
				$info = $this->childs[$pid];
				unset($this->childs[$pid]);
				
				//进程信息未找到
				if(!$info){
					$this->log("not found child process information by pid=$pid");
					return ;
				}
				
				$this->log("child process exit, name=$info[name], pid=$pid, status=$status");
				if(isset($info['daemon'])){
					//daemon退出，需要重启新进程
					$cfg = $this->daemon[$info['name']];
					$this->spawn($cfg['script'], array(
						'name' => $cfg['name'],
						'daemon' => true
					));
				}else{
					//crontab退出，忽略
				}
				
				break;
			default:
				$this->log("not defined signal handler", self::LOG_ERROR);
				break;
		}
	}
	
	private function log($str, $level = self::LOG_INFO)
	{
		if($this->logfile && $this->loglevel >= $level){
			$levelstr = array("", "ERROR", "INFO");
			$level = $levelstr[$level];
			$pid = getmypid();
			$msg = date('Y-m-d H:i:s') . " [$level][$pid] " . $str . "\n";
			if($this->daemonize){
				file_put_contents($this->logfile, $msg, FILE_APPEND);
			}else{
				echo $msg;
			}
		}
	}
	
	private function parseWakeup($str)
	{
		$str = str_replace('  ', ' ', trim($str));
		$argv = explode(' ', $str);
		$argc = count($argv);
		
		if($argc == 5){
			//5个参数只支持到分钟，秒默认就是0了
			$names = array('minute', 'hour', 'day', 'month', 'week');
			$wakeup = array(
				'second' => array(
					array('op' => 'eq', 'v1' => 0)
				)
			);
		}else if($argc == 6){
			//6个参数支持到秒
			$names = array('second', 'minute', 'hour', 'day', 'month', 'week');
			$wakeup = array();
		}else{
			return false;
		}
		
		foreach($argv as $pos => $val){
			$options = explode(',', $val);
			$name = $names[$pos];
			foreach($options as $val){
				if($val == '*'){//任意值
					$item = array(
						'op' => 'any',
					);
				}else if(is_numeric($val)){//相等
					$item = array(
						'op' => 'eq',
						'v1' => $val,
					);
				}else if(preg_match('/^(\d+)\-(\d+)$/', $val, $matches)){//范围
					$item = array(
						'op' => 'range',
						'v1' => $matches[1],
						'v2' => $matches[2],
					);
				}else if(preg_match('/^\*\/(\d+)$/', $val, $matches)){//取模
					$item = array(
						'op' => 'mod',
						'v1' => $matches[1],
					);
				}else{
					return false;
				}
				
				$wakeup[$name][] = $item;
			}
		}
		
		return $wakeup;
	}
	
	private function runDaemon()
	{
		foreach($this->daemon as $item){
			for($i = 0; $i < $item['childs']; ++$i){
				$pid = $this->spawn($item['script'], array(
					'name' => $item['name'],
					'daemon' => true,
				));
			}
		}
	}
	
	private function runCrontab()
	{
		$args = explode('-', date('s-i-H-d-m-w'));
		$params = array(
			'second' => $args[0],
			'minute' => $args[1],
			'hour' => $args[2],
			'day' => $args[3],
			'month' => $args[4],
			'week' => $args[5]
		);
		
		foreach($this->crontab as $item){
			if($this->canWakeup($item['wakeup'], $params)){
				$pid = $this->spawn($item['script'], array(
					'name' => $item['name']
				));
			}
		}
	}
	
	private function canWakeup($wakeups, $params)
	{
		foreach($wakeups as $name => $conds){
			$val = $params[$name];
			$ret = false;
			
			foreach($conds as $cond){
				if($cond['op'] == 'any'){
					$ret = true;
					break;
				}else if($cond['op'] == 'eq'){
					if($val == $cond['v1']){
						$ret = true;
						break;
					}
				}else if($cond['op'] == 'range'){
					if($val >= $cond['v1'] && $val <= $cond['v2']){
						$ret = true;
						break;
					}
				}else if($cond['op'] == 'mod'){
					if($val % $cond['v1'] == 0){
						$ret = true;
						break;
					}
				}
			}
			
			if(!$ret){
				return false;
			}
		}
		
		return true;
	}
	
	private function spawn($file, $params)
	{
		$pid = pcntl_fork();
		if($pid == -1){
			$this->log("fork child error", self::LOG_ERROR);
			return false;
		}else if($pid == 0){
			pcntl_signal(SIGUSR1, SIG_DFL);
			pcntl_signal(SIGCHLD, SIG_DFL);
			pcntl_signal(SIGTERM, SIG_DFL);
			pcntl_signal(SIGINT, SIG_DFL);
					
			include $file;
			
			exit;
		}else{
			$this->log("child process started, name=$params[name], pid=$pid");
			$this->childs[$pid] = $params;
			return $pid;
		}
	}
}