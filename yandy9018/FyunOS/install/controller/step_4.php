<?php
class ControllerStep4 extends Controller {
	public function index() {
		$this->children = array(
			'header',
			'footer'
		);
		
		$this->template = 'step_4.tpl';
		// added install counter for shopilex
		if (extension_loaded('curl')) {
			$curl = curl_init();
			curl_setopt($curl, CURLOPT_URL, 'http://counter.shopilex.com?src=' . $_SERVER['HTTP_HOST']);
			curl_exec($curl);
			if (!curl_errno($curl)) {
				curl_close($curl);
			}
			
		}
		$this->response->setOutput($this->render(TRUE));
	}
}
?>