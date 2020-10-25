<?php
namespace WebService\Controller;
use WebService\Controller\BaserestController;

/**
 * 问卷资源服务
 */
class QuestionnairesController extends BaserestController
{
	/**
	 * 输出指定问卷的元数据信息
	 */
	public function meta($questionnaireID)
	{
		$questionnarie = M('Questionnaires')->field(true)->find($questionnaireID);
		
		$filter = array(
			'openid'		 	=>	session('openid'),
			'questionnaire_id'	=> 	$questionnaireID,
		);

		$questionnarie['marked'] = M('Reply')->where($filter)->find() ? true : false; //查询当前用户对指定问卷是否作答过

		$this->response($questionnarie, 'json');
	}

	/**
	 * 输出指定问卷下的问题列表
	 */
	public function getQuestions($questionnaireID)
	{		
		/* 判断指定问卷是否过期 */
		$nowDate = date('Y-m-d');
		$expireDate = M('Questionnaires')->getFieldById($questionnaireID, 'expire_date');
		$expired = (strcmp($nowDate, $expireDate) < 0) ? false:true;

		if( !$expired ){ //未过期
			$data['questionsList'] = M('Questions')->field('id,name,options,score')->where("questionnaire_id=$questionnaireID")->order('sort')->select();
		}else{ //已过期
			$data['errorMsg'] = '指定的问卷已过期';
		}

		$this->response($data, 'json');
	}

	/**
	 * 提交指定问卷的作答
	 */
	public function submitReply($questionnaireID)
	{
		$replyJson = file_get_contents('php://input');

		$questionnarieType = M('Questionnaires')->getFieldById($questionnaireID, 'type');

		if( 'exam' == $questionnarieType ){ //考试卷需要对用户作答判分
			$judgement = $this->_judgeQuestionnaire($questionnaireID, json_decode($replyJson));

			$total_score = 0;
			foreach ($judgement as $judge) {
				$total_score += $judge['achieve_score'];
			}
		}else{
			$total_score = null;
		}

		/* 将用户作答记录入库 */
		$data = array(
			'questionnaire_id'	=>	$questionnaireID,
			'reply'				=>	$replyJson,
			'total_score'		=>	$total_score
		);
		$data = array_merge($data, session('user')); //合并上用户信息
        $data['headimgurl'] = substr_replace($data['headimgurl'], '46', -1);

		$status = M('Reply')->add($data);
		if( $status === false ){ //入库失败
			$msg = array(
				'errorMsg'	=>	"问卷入库失败，详情：" + M('Reply')->getDbError()
			);

			$this->response($msg, 'json');
		}else{ //入库成功
			$msg = array(
				'errorMsg'	=>	null,
				'total_score'	=>  $total_score
			);

			$this->response($msg, 'json');
		}
	}

	/**
	 * 读取用户在指定问卷下的判分情况
	 */
	public function getJudgement($questionnaireID)
	{
		$filter = array(
			'openid'			=> session('user.openid'), 
			'questionnaire_id'	=> $questionnaireID
		);
		$replyJson = M('reply')->where($filter)->getField('reply');

		$judgement = $this->_judgeQuestionnaire($questionnaireID, json_decode($replyJson));

		$this->response($judgement, 'json');
	}

	/**
	 * 输出指定问卷下的签名图列表
	 */
	public function getSignimages($questionnaireID)
	{
		$filter = array(
			'questionnaire_id'	=>	$questionnaireID,
			'status'			=>	'on',
		);

		$data['signimagesList'] = M('Signimages')->where($filter)->getField('path', true);
		
		$this->response($data, 'json');
	}

	/**
	 * 对用户作答判分, 判答标准初步约定为答案全匹配（针对考试卷）
	 * @param int $questionnaireId  问卷id
	 * @param assoc-array  $reply  用户作答
	 * @return assoc-array  结构为array( 问题ID => array(standard=>, score=>, reply=>, achieve_score=>), ... )
	 */
	private function _judgeQuestionnaire($questionnaireID, $replyList)
	{
		$judgement = array();
		$standardList = M('Questions')->where("questionnaire_id=$questionnaireID")->getField('id,standard,score');

		foreach ($replyList as $questionID => $answer) {
			$achieve_score = ($answer == $standardList[$questionID]['standard']) ? $standardList[$questionID]['score'] : 0;

			$reply = array(
				'reply'			=> $answer,
				'achieve_score'	=> $achieve_score
			);

			$judgement[$questionID] = array_merge($standardList[$questionID], $reply);
		}

		return $judgement;
	}

}