<?php defined('SYSPATH') or die('No direct script access.');
//公用继承控制器
class Controller_Common extends Controller {
	

	//初始化方法
	public function before()
	{
		parent::before();
		$this->_config = kohana::$config->load('itxti');
	}
	
	
	protected function error($data = array() , $code = 0) {
		$result = $this->_make_output('error',$code, $data);
		return $result;
	}
	
	protected function success($data = array() , $code = 1) {
		$result = $this->_make_output('success',$code,$data);
		return $result;
	}
	
	
	protected function _make_output($type,$code = 0,$data = array()) {
		$result = array(
            'result' => $type,
            'code' => $code,
            'data' => $data ? $data : array()
        );

		$format = strtolower($this->request->query('format'));
		switch ($format) {
			case 'xml':
				echo 'xml';
				break;
			default:
				$this->response->headers(array('Content-type'=>'application/json'))->body(json_encode($result));
				break;
		}
	}
	

}
