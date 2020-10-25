<?php namespace qeephp\tools;

use qeephp\http\AHttpRequest;

/**
 * sexhprof Client 
 */
class SeXhprof
{
	
	function __construct(array $config)
	{
		$this->config = $config;		
	}

    function save(array $xhprof_data)
	{
		if (empty($this->config['url']))
		{
			return dump($xhprof_data);
		}
		$data = array(
			'xhprof_url' => isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : $_SERVER['PHP_SELF'],
			'xhprof_data' => json_encode($xhprof_data),
			'xhprof_sname' => empty($this->config['sname']) ? val($_SERVER, 'SERVER_NAME','') : trim($this->config['sname']),
			'xhprof_get' => '',
			'xhprof_post' => '',
			'xhprof_cookie' => '',
		);
		
		if (!empty($this->config['get']) && isset($_GET))
		{
			$data['xhprof_get'] = json_encode($_GET);
		}
		if (!empty($this->config['post']) && isset($_GET))
		{
			$data['xhprof_post'] = json_encode($_GET);
		}
		if (!empty($this->config['cookie']) && isset($_POST))
		{
			$data['xhprof_cookie'] = json_encode($_COOKIE);
		}
		
		$response = AHttpRequest::post(trim($this->config['url']), array("Accept" => "application/json"),$data);
		/*@var \qeephp\http\AHttpResponse $response*/

		$response_body = $response->raw_body;			
		if (200 == $response->code && !empty($response_body))
		{
			$arr = json_decode($response_body,TRUE);
			if (is_array($arr) && !empty($arr['access_url']))
			{
				echo "  <a href='{$arr['access_url']}'>xhprof</a>";
				return;
			}
		}
		dump($response,'xhprof');
	}
	
}