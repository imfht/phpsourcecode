<?php 

class CommonWidget extends Widget 
{
	public function render($data)
	{
		$redirect = $data['redirect'];
		if($redirect){
			return $this->renderFile ("$redirect/index",$data);
		}
	}
}