<?php
/**
 * 数据监控
 *
 * @author		嬴益虎 <Yingyh@whoneed.com>
 * @copyright	Copyright 2012
 *
 */

class MonitorController extends MyAdminController
{
	// 后台首页
	public function actionIndex()
	{
		echo 'Not allow!';
	}

	/**
	 * 接口调用统计
	 */
	public function actionInterfaceCallStatistics()
	{
		$server_name	= trim($_GET['server_name']);
		$module_name	= trim($_GET['module_name']);
		$interface_name	= trim($_GET['interface_name']);
		$date_time		= trim($_GET['date_time']);
		$ip				= trim($_GET['ip']);
		
		// 默认当天
		if(empty($date_time))	$date_time	= date('Ymd');
		$arrIP	= array();
		if($ip)
		{
			$arrIP	= explode(',', $ip);
		}else{
			$arrIP[] = '127.0.0.1';
		}

		$data = array();
		$data['code']			= 0;
		$data['server_name']	= $server_name;
		$data['module_name']	= $module_name;
		$data['interface_name']	= $interface_name;
		$data['date_time']		= $date_time;
		$data['monitor_data']	= array();

		$arrT	= array();
		foreach($arrIP as $ip)
		{
			$ret = $this->get_server_module_list($server_name, $module_name, $interface_name, $date_time, '/index/interface', $ip);
			if($ret['code'] == 200 && $ret['content'])
			{
				$content = json_decode($ret['content'], true);
				if($content['code'] == 0 && $content['data'])
				{
					$arrT[] = $content['data'];
				}
			}
		}

		if($arrT)
		{
			$data['monitor_data'] = $this->formatInterfaceData($arrT);
		}
		
		$this->display('interface', $data);
	}

	/**
	 * 数据累计统计
	 */
	public function actionCountCallStatistics()
	{
		$server_name	= trim($_GET['server_name']);
		$module_name	= trim($_GET['module_name']);
		$interface_name	= trim($_GET['interface_name']);
		$date_time		= trim($_GET['date_time']);
		$ip				= trim($_GET['ip']);
		
		// 默认当天
		if(empty($date_time))	$date_time	= date('Ymd');
		$arrIP	= array();
		if($ip)
		{
			$arrIP	= explode(',', $ip);
		}else{
			$arrIP[] = '127.0.0.1';
		}

		$data = array();
		$data['code']			= 0;
		$data['server_name']	= $server_name;
		$data['module_name']	= $module_name;
		$data['interface_name']	= $interface_name;
		$data['date_time']		= $date_time;
		$data['monitor_data']	= array();

		$arrT	= array();
		foreach($arrIP as $ip)
		{
			$ret = $this->get_server_module_list($server_name, $module_name, $interface_name, $date_time, '/index/index', $ip);
			if($ret['code'] == 200 && $ret['content'])
			{
				$content = json_decode($ret['content'], true);
				if($content['code'] == 0 && $content['data'])
				{
					$arrT[] = $content['data'];
				}
			}
		}
	
		if($arrT)
		{
			$data['monitor_data'] = $this->formatCountData($arrT);
		}

		$this->display('count', $data);
	}

	/**
	 * 数据累计统计
	 */
	public function actionTest()
	{
		$server_name	= 'yii_server';
		$module			= 'test_module';
		$interface		= 'test_interface';
	
		WMonitorClient::tick($server_name, $module, $interface);
		WMonitorClient::coutReport($server_name, $module, $interface, 1);
		WMonitorClient::report($server_name, $module, $interface, true);
	}
	//================== inner function
	//获取模块列表
	public function get_server_module_list($server_name = '', $module_name = '', $interface_name = '', $date_time = '', $url = '', $ip = '', $port = 50101)
	{
		$url = "http://{$ip}:{$port}{$url}?server_name={$server_name}&module_name={$module_name}&interface_name={$interface_name}&date_time={$date_time}";
		return MyFunction::get_url($url);
	}

	public function formatCountData($arrT = array())
	{
		$arrRet	= array('module_list' => array(), 'monitor_count' => array());

		foreach($arrT as $v)
		{
			$this->format_module_list($arrRet['module_list'], $v['module_list']);
			$this->format_monitor_count($arrRet['monitor_count'], $v['monitor_count']);
		}

		if($arrRet['monitor_count'])
		{
			$arrT = array();
			ksort($arrRet['monitor_count']);

			foreach($arrRet['monitor_count'] as $k => $v)
			{
				$arrT2 = array();
				$arrT2[] = intval($k.'000');
				$arrT2[] = $v;

				$arrT[] = $arrT2;
			}

			$arrRet['monitor_count'] = $arrT;
		}

		return $arrRet;
	}

	public function formatInterfaceData($arrT = array())
	{
		$arrRet	= array('module_list' => array(), 'monitor_interface' => array());

		foreach($arrT as $v)
		{
			$this->format_module_list($arrRet['module_list'], $v['module_list']);
			$this->format_monitor_interface($arrRet['monitor_interface'], $v['monitor_interface']);
		}

		if($arrRet['monitor_interface'])
		{
			$arrSucc		= array();
			$arrFail		= array();
			$arrSuccTime	= array();
			$arrFailTime	= array();

			ksort($arrRet['monitor_interface']);

			foreach($arrRet['monitor_interface'] as $k => $v)
			{
				$arrSuccT		= array();
				$arrFailT		= array();
				$arrSuccTimeT	= array();
				$arrFailTimeT	= array();
				
				// 成功请求数
				$arrSuccT[] = intval($k.'000');
				$arrSuccT[] = $v['suc_count'];
				$arrSucc[]	= $arrSuccT;
				
				// 成功耗时
				$arrSuccTimeT[] = intval($k.'000');
				$arrSuccTimeT[] = floatval($v['suc_count'] == 0 ? 0 : number_format($v['suc_cost_time']/$v['suc_count'], 3));
				$arrSuccTime[]	= $arrSuccTimeT;				

				// 失败请求数
				$arrFailT[] = intval($k.'000');
				$arrFailT[] = $v['fail_count'];
				$arrFail[]	= $arrFailT;

				// 失败耗时
				$arrFailTimeT[] = intval($k.'000');
				$arrFailTimeT[] = floatval($v['fail_count'] == 0 ? 0 : number_format($v['fail_cost_time']/$v['fail_count'], 3));
				$arrFailTime[]	= $arrFailTimeT;
			}

			$arrRet['monitor_interface'] = array();
			$arrRet['monitor_interface']['succ']		= $arrSucc;
			$arrRet['monitor_interface']['fail']		= $arrFail;
			$arrRet['monitor_interface']['succTime']	= $arrSuccTime;
			$arrRet['monitor_interface']['failTime']	= $arrFailTime;
		}

		return $arrRet;
	}

	public function format_module_list(&$to, $from = array())
	{
		if($from)
		{
			foreach($from as $k => $v)
			{
				if(!isset($to[$k])) $to[$k] = array();
				
				if($v && is_array($v))
				{
					foreach($v as $v1)
					{
						$to[$k][] = $v1;
					}
				}
				$to[$k] = array_unique($to[$k]);
			}
		}
	}

	public function format_monitor_count(&$to, $from = array())
	{
		if($from)
		{
			foreach($from as $k => $v)
			{
				if(!isset($to[$k])) $to[$k] = 0;
				$to[$k] += $v;
			}
		}
	}

	public function format_monitor_interface(&$to, $from = array())
	{
		if($from)
		{
			foreach($from as $k => $v)
			{
				if(!isset($to[$k]))
				{
					$to[$k] = array();
					$to[$k]['suc_count']		= 0;
					$to[$k]['suc_cost_time']	= 0;
					$to[$k]['fail_count']		= 0;
					$to[$k]['fail_cost_time']	= 0;
				}

				$to[$k]['suc_count']		+= $v['suc_count'];
				$to[$k]['suc_cost_time']	+= $v['suc_cost_time'];
				$to[$k]['fail_count']		+= $v['fail_count'];
				$to[$k]['fail_cost_time']	+= $v['fail_cost_time'];
			}
		}
	}
}
?>
