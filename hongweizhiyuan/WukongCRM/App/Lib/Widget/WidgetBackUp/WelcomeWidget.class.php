<?php 

class WelcomeWidget extends Widget 
{
	public function render($data)
	{
		return $this->renderFile ("index");
	}
}