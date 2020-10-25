<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class GameController extends Controller{
	public function actionIndex(){
		$games=Games::model()->findByAttributes(array('id'=>1));
		$gamesServerId=unserialize($games->server_id);
		$gamesAlias=$games->alias;
		$this->render('index',array(
			'gamesServerId'=>$gamesServerId,
			'gamesAlias'=>$gamesAlias,
		));
	}
}
?>
