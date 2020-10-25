<?php 
namespace Admin\Controller;
use Admin\Controller\CommonController;

/**
* 处理问题相关请求
*/
class QuestionsController extends CommonController
{
	protected function _initialize()
	{
		parent::_initialize();

    	$this->bcItemPush('问卷管理', U('Questionnaire/index'));
    	$this->bcItemPush('问题管理', U('Questions/index', array('questionnaire_id'=>I('questionnaire_id/d'))));		
	}
	
	/* 问题列表 */
	public function	index()
	{
		//输出顶部该问卷的概要信息
        $questionnaire = M('Questionnaires')->field('id,type,name,description')->find(I('get.questionnaire_id/d'));
        $questionnaire['signimageNum'] = M('signimages')->where("questionnaire_id=$questionnaire[id]")->count();
		$this->assign('questionnaire', $questionnaire);
		$this->assign('questions', M('Questions')->field(true)->where(I('get.'))->order('sort')->select());

		$this->display();
	}

	/* 问题添加 */
	public function add()
	{
        $this->bcItemPush('添加问题');

		$this->assign('questionnaire', M('Questionnaires')->field('id,type,name,description')->find(I('get.questionnaire_id/d')));
		$this->assign('extendJs', 'questions-optionManager.js'); //改选extendJs文件

        if( IS_GET ){ //访问页面
        	/* 填充好当前默认排序 */
        	$question['sort'] = M('Questions')->where( I('get.') )->count('id') + 1;
        	$this->assign('question', $question);

			$this->display();
        }else{ //表单提交
	        $questions = D('Questions');
        	
        	if( $data = $questions->create() ){
	        	$data['options'] = I('post.options', '', ''); //临时关闭I函数的过滤器，因为选项中包含特殊字符

	        	$state = $questions->add($data);
		        
		        if( $state===false ){
	        		$this->error('问题添加失败，错误信息：'.$questions->getDbError());
		        }else{
		        	$this->success('问题添加成功', '', 0);
		        }	        	
	        }else{ //表单提交不完整
	        	$this->_formBack(); //把不完整的表单数据反馈回去

	            $this->assign('errorNote', $questions->getError());

				$this->display();
	        }
        }
	}

	/* 问题编辑 */
	public function edit()
	{
        $this->bcItemPush('编辑问题');

		$this->assign('questionnaire', M('Questionnaires')->field('id,type,name,description')->find(I('get.questionnaire_id/d')));
        $this->assign('extendJs', 'questions-optionManager.js'); //改选extendJs文件
        
        if( IS_GET ){ //访问页面
        	$questions = D('Questions');
        	$question = $questions->field(true)->find(I('get.id/d'));

			$this->assign('question', $question);
			$this->assign('optionsList', json_decode($question['options'], true)); //选项列表
			$isText = ( $question['options'] == '{"0":{"type":"text","text":""}}' ) ? true:false; //判断该题是否是文本输入型问题
			$this->assign('standardList', $questions->getStandardList($question['standard'], $isText)); //标准答案列表

			$this->display();
        }else{ //表单提交
	        $questions = D('Questions');
        	
        	if( $data = $questions->create() ){
	        	$data['options'] = I('post.options', '', ''); //临时关闭I函数的过滤器，因为选项中包含特殊字符
	        	
	        	$state = $questions->save($data);

		        if( $state===false ){
	        		$this->error('问题编辑失败，错误信息：'.$questions->getDbError());
		        }else{
	        		$this->success('问题编辑成功', U('Questions/index', array('questionnaire_id'=>I('get.questionnaire_id/d'))), 0);
		        }	        	
	        }else{ //表单提交不完整
	        	$this->_formBack(); //把不完整的表单数据反馈回去

	            $this->assign('errorNote', $questions->getError());

				$this->display();
	        }
        }
	}

	/* 问题删除 */
	public function delete()
	{
        if( is_array(I('id')) ){ //批量删除
            $id = implode(',', I('id', array(), 'intval'));
        }else{ //单个删除
            $id = I('id/d');
        }

        $questions = M('Questions');
        $state = $questions->delete($id);

        if( $state===false ){
	        $this->error('问卷删除失败，错误信息: '.$questions->getDbError());
        }else{
	        $this->redirect('/questions/index', array('questionnaire_id'=>I('questionnaire_id/d')));
        }
	}

	/* 问题重新排序 */
	public function sort()
	{
		$sort = I('post.sort', array(), 'intval') OR die;

		if( D('Questions')->sortQuestions($sort) ){
			$this->success('问题排序成功，即将返回');
		}else{
			$this->error('问题排序出错，错误信息：'.D('Questions')->getDbError());
		};
	}

	
	/* 把不完整的表单数据反馈回去 */
	private function _formBack()
	{
        $this->assign('question', I('post.'));

        if( $options = I('post.options', '', '') ){ //已配置好选项
			$this->assign('optionsList', json_decode($options, true));

			if( I('post.standard') != '' ){ //已配置好标准答案
				$isText = ( $options == '{"0":{"type":"text","text":""}}' ) ? true:false; //判断该题是否是文本输入型问题
				$this->assign('standardList', D('Questions')->getStandardList(I('post.standard'), $isText));
			}
        }
	}

}
?>