<?php
/**
 * Created by PhpStorm.
 * User: 80053521
 * Date: 2015/1/8
 * Time: 17:26
 */
namespace fbi\adminLogging\components;
use Yii;
class User extends \yii\web\User{
	private $msg=[];
	private $controller;
	private $action;
	private $method;
	private $actionMap=[
		'site/login'
	];
	public function logBegin($controller,$action,$method,$msg){
		$this->controller=$controller;
		$this->action=$action;
		$this->method=$method;
		$this->msg[]=[
			'time'=>date('Y-m-d H:i:s'),
			'msg'=>$msg
		];
	}
	public function log($msg){
		$this->msg[]=[
			'time'=>date('Y-m-d H:i:s'),
			'msg'=>$msg
		];
	}
	public function logEnd(){
		$logPath=\Yii::getAlias('@runtime/logs/admin/'.date('Ym').'.log');
		!is_dir(dirname($logPath)) && mkdir(dirname($logPath),0777,true);
		file_put_contents($logPath,$this->formatLog().PHP_EOL,FILE_APPEND);
		$this->resetMsg();
	}

	public function resetMsg(){
		$this->msg=[];
	}

	private function formatLog(){
		$lineEnd=PHP_EOL;
		$logEnd=$logBegin="#########";
		$lines = [$logBegin];
		$lines[]=$this->getLogBrief();
		$step=1;
		foreach($this->msg as $msg){
			$lines[]=implode($lineEnd,[
				'-------['.$step.']------',
				($step++).'.'."[{$msg['time']}]",
				var_export($msg['msg'],true)
			]);
		}
		$lines[]=$logEnd;
		return implode($lineEnd,$lines);
	}
	private function getLogBrief(){
		$route=$this->controller."/".$this->action;
		$actionName=isset($this->actionMap[$route])?$this->actionMap[$route]:'';
		return sprintf("[%s] %s:%s %s[id=%s][email=%s][role=%s] %s %s[%s]",
			date('Y-m-d H:i:s',YII_BEGIN_TIME),
			Yii::$app->getRequest()->getUserIP(),
			Yii::$app->getRequest()->getUserPort(),
			$this->getIsGuest()?'guest':$this->identity->username,
			$this->getId(),
			$this->getIsGuest()?'guest':$this->identity->email,
			$this->getIsGuest()?'guest':$this->identity->role,
			$this->method,
			$route,
			$actionName
		);
	}

	/**
	 * @inheritdoc
	 */
	public function afterLogin($identity, $cookieBased, $duration){
		parent::afterLogin($identity, $cookieBased, $duration);
		$this->log($identity->username.'logined!');
	}

	/**
	 * @inheritdoc
	 */
	public function afterLogout($identity){
		parent::afterLogout($identity);
		$this->log($identity->username.' logined out!');
	}
} 