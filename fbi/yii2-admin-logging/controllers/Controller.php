<?php
/**
 * Created by PhpStorm.
 * User: 80053521
 * Date: 2015/1/8
 * Time: 17:28
 */

namespace fbi\adminLogging\controllers;

use Yii;

class Controller extends \yii\web\Controller{
	protected $notLogActions=[];
	protected $encryptDataActions=[];
	public function beforeAction($action){
		/** @var \fbi\adminLogging\components\User $user */
		$user = Yii::$app->user;
		if($this->checkShouldLog()){
			$user->logBegin($this->id,$this->action->id,Yii::$app->request->getMethod(),$this->getLogParams());
		}
		return true;
	}
	public function afterAction($action,$result){
		/** @var \fbi\adminLogging\components\User $user */
		$user = Yii::$app->user;
		if($this->checkShouldLog()) {
			$user->logEnd();
		}
		return $result;
	}
	protected function getLogParams(){
		return [
			'GET'=>$this->checkShouldEncrypt()?$this->md5($_GET):$_GET,
			'POST'=>$this->checkShouldEncrypt()?$this->md5($_POST):$_POST,
			'FILE'=>$this->checkShouldEncrypt()?$this->md5($_FILES):$_FILES,
		];
	}
	private function md5( $value ){
		if(is_scalar($value)){
			return md5($value);
		}
		foreach($value as &$v){
			$v=$this->md5($v);
		}
		return $value;
	}
	protected function checkShouldLog(){
		if(in_array($this->action->id,$this->notLogActions)){
			return false;
		}
		return true;
	}
	protected function checkShouldEncrypt(){
		if(in_array($this->action->id,$this->encryptDataActions)){
			return true;
		}
		return false;
	}
} 