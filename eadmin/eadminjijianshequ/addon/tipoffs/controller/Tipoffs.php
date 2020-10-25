<?php 
namespace addon\tipoffs\controller;

use app\common\controller\AddonBase;
use app\common\logic\File as LogicFile;

class Tipoffs extends AddonBase {

	private $model = null;
	/**
	 * 构造方法
	 */
	public function _initialize()
	{
	
		parent::_initialize();
		
		$this->model = $this->commonLogic->setname('tipoffs');
		
	}
	
	public function contentShow() {
		//$this->param['id']
		//根据类型判断，是文章，还是评论
		$type = $this->param['type'];
		switch ( $type ) {
			case 0:
				$info = $this->model->setname('comment')->getDataInfo(['id' => $this->param['id']]);
				break;
			case 1:
				$info = $this->model->setname('article')->getDataInfo(['id' => $this->param['id']]);
				break;
			case 2:
				$info = $this->model->setname('topic')->getDataInfo(['id' => $this->param['id']]);
				break;
			default:
				$info = array('content' => '当前内容主体是用户，请查看补充项' );
				break;
		}
		$this->assign('info', $info);
		
		return $this->addonTemplate('entry');
	}

	public function verdict() {
		$tipoffs = $this->model->setname('tipoffs')->getDataInfo(['id' => $this->param['id']]);
		if (IS_POST) {
          	if (empty($tipoffs))  {
                   return $this->jump(RESULT_ERROR,'操作失败,当前举报信息不存在','',['data'=>$tipoffs]);
            }
			$lock = $this->param['lock'];
			if ($lock == 1) {
				//锁定用户
				$result = self::$datalogic->setname('user')->setDataValue(['id' => $tipoffs['defendantId']],'status',6);
			} else {
				$result = self::$datalogic->setname('user')->setDataValue(['id'=>$tipoffs['defendantId']],'status',1);
			}
			if ($result[0] != 'success'){
					return $this->jump(RESULT_ERROR,'操作失败','',['data'=>$result]);
			}

			$shield = $this->param['shieid'];
			if ($shield == 1) {
				//屏蔽当前内容
				$type = $tipoffs['contentType'];
				switch ( $type ) {
					case 0:
						$info = $this->model->setname('comment')->setDataValue(['id' => $tipoffs['contentId']] ,'status',0);
						break;
					case 1:
						$info = $this->model->setname('article')->setDataValue(['id' => $tipoffs['contentId']] ,'status',0);
						break;
					case 2:
						$info = $this->model->setname('topic')->setDataValue(['id' => $tipoffs['contentId']] ,'status',0);
						break;
					default:
						$info = array('content' => '当前内容主体是用户，请查看补充项' );
						break;
				}
				if ($info[0] != 'success'){
					return $this->jump(RESULT_ERROR,'操作失败','',['data'=>$info]);
				}
			} else {
				//放开屏蔽
				$type = $tipoffs['contentType'];
				switch ( $type ) {
					case 0:
						$info = $this->model->setname('comment')->setDataValue(['id' => $tipoffs['contentId']] ,'status',1);
						break;
					case 1:
						$info = $this->model->setname('article')->setDataValue(['id' => $tipoffs['contentId']] ,'status',1);
						break;
					case 2:
						$info = $this->model->setname('topic')->setDataValue(['id' => $tipoffs['contentId']] ,'status',1);
						break;
					default:
						$info = array('content' => '当前内容主体是用户，请查看补充项' );
						break;
				}
				if ($info[0] != 'success'){
					return $this->jump(RESULT_ERROR,'操作失败','',['data'=>$info]);
				}
			}
			return $this->jump(self::$datalogic->setname('tipoffs')->setDataValue(['id'=>$tipoffs['id']], 'status', 1));
		}
		$this->assign('tipoffs', $tipoffs);

		$type = $tipoffs['contentType'];
		switch ( $type ) {
			case 0:
				$this->model->setname('comment');
				break;
			case 1:
				$this->model->setname('article');
				break;
			case 2:
				$this->model->setname('topic');
				break;
			default:
				$info = null;
				break;
		}
		$info = $this->model->getDataInfo(['id' => $tipoffs['contentId']]);
		$this->assign('info', $info);

		$user = $this->model->setname('user')->getDataInfo(['id' => $tipoffs['defendantId']]);
		$this->assign('user', $user);

		return $this->addonTemplate('verdict');
	}

}
 ?>