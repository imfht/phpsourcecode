<?php
/**
 * Trotri
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright &copy; 2011-2013 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace views\bootstrap\widgets;

use tfc\mvc\form;
use tfc\saf\Text;
use posts\services\DataPosts;

/**
 * PostComments class file
 * 文档评论表单和列表
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: PostComments.php 1 2013-05-18 14:58:59Z huan.song $
 * @package views.bootstrap.widgets
 * @since 1.0
 */
class PostComments extends form\FormBuilder
{
	/**
	 * @var string 生成Html类型：表单
	 */
	const HTML_TYPE_FORM = 'form';

	/**
	 * @var string 生成Html类型：列表
	 */
	const HTML_TYPE_LIST = 'list';

	/**
	 * @var string 样式名
	 */
	public $className = 'form-control';

	/**
	 * (non-PHPdoc)
	 * @see \tfc\mvc\form\FormBuilder::_init()
	 */
	protected function _init()
	{
		$this->html_type = isset($this->html_type) ? trim($this->html_type) : '';
		$this->post_id = isset($this->post_id) ? (int) $this->post_id : 0;

		if ($this->html_type === self::HTML_TYPE_FORM) {
			$this->action = $this->getView()->getUrlManager()->getUrl('commentcreate', 'data', 'posts');
			$this->_tplVars['elements'] = array(
				'author_name' => array(
					'__object__' => 'tfc\\mvc\\form\\InputElement',
					'type' => 'text',
					'label' => Text::_('MOD_POSTS_POST_COMMENTS_AUTHOR_NAME_LABEL'),
					'required' => true,
					'class' => $this->className,
				),
				'author_mail' => array(
					'__object__' => 'tfc\\mvc\\form\\InputElement',
					'type' => 'email',
					'label' => Text::_('MOD_POSTS_POST_COMMENTS_AUTHOR_MAIL_LABEL'),
					'required' => true,
					'class' => $this->className,
				),
				'content' => array(
					'__object__' => 'tfc\\mvc\\form\\InputElement',
					'type' => 'textarea',
					'label' => Text::_('MOD_POSTS_POST_COMMENTS_CONTENT_LABEL'),
					'required' => true,
					'class' => $this->className,
					'rows' => 10,
				),
				'post_id' => array(
					'__object__' => 'tfc\\mvc\\form\\InputElement',
					'type' => 'hidden',
					'value' => $this->post_id,
				),
				'comment_pid' => array(
					'__object__' => 'tfc\\mvc\\form\\InputElement',
					'type' => 'hidden',
					'value' => 0,
				),
				'_button_save_' => array(
					'__object__' => 'tfc\\mvc\\form\\ButtonElement',
					'type' => 'button',
					'value' => Text::_('CFG_SYSTEM_GLOBAL_SUBMIT'),
					'class' => 'btn btn-default'
				),
			);
		}
		else {
			$this->_tplVars['elements'] = array();
		}

		parent::_init();
	}

	/**
	 * (non-PHPdoc)
	 * @see \tfc\mvc\form\FormBuilder::run()
	 */
	public function run()
	{
		if ($this->post_id <= 0) {
			return ;
		}

		if ($this->html_type === '') {
			return ;
		}

		$commentStatus = isset($this->comment_status) ? trim($this->comment_status) : '';
		if ($commentStatus !== DataPosts::COMMENT_STATUS_PUBLISH && $commentStatus !== DataPosts::COMMENT_STATUS_DRAFT) {
			return ;
		}

		$this->assign('is_publish', ($commentStatus === DataPosts::COMMENT_STATUS_PUBLISH ? 'true' : 'false'));
		$this->assign('response', Text::_('MOD_POSTS_POST_COMMENTS_RESPONSE'));

		$funcName = $this->html_type . 'Run';
		if (method_exists($this, $funcName)) {
			$this->$funcName($this->post_id, $commentStatus);
		}
	}

	/**
	 * 生成评论列表
	 * @param integer $postId
	 * @param integer $commentStatus
	 * @return void
	 */
	public function listRun($postId, $url)
	{
		$listId = 'post_comments_box';
		$url = $this->getView()->getUrlManager()->getUrl('commentindex', 'data', 'posts');

		$this->assign('list_id', $listId);
		$this->assign('url', $url);
		$this->assign('postid', $postId);
		$this->assign('prev', Text::_('CFG_SYSTEM_GLOBAL_PAGE_PREV'));
		$this->assign('next', Text::_('CFG_SYSTEM_GLOBAL_PAGE_NEXT'));

		$this->display('list.php');
		$this->display('list.js');
	}

	/**
	 * 生成Form表单
	 * @return void
	 */
	public function formRun()
	{
		$title = Text::_('MOD_POSTS_POST_COMMENTS_PUBLISH_LABEL');
		$hint = '&nbsp;&nbsp;' . Text::_('MOD_POSTS_POST_COMMENTS_AUTHOR_MAIL_HINT') . '&nbsp;&nbsp;' . Text::_('MOD_POSTS_POST_COMMENTS_PUBLISH_HINT');

		$this->assign('title', $title);
		$this->assign('hint', $hint);
		$this->assign('just_now', Text::_('MOD_POSTS_POST_COMMENTS_DT_CREATE_JUST_NOW'));
		$this->assign('response', Text::_('MOD_POSTS_POST_COMMENTS_RESPONSE'));
		$this->assign('auditing', Text::_('MOD_POSTS_POST_COMMENTS_AUDITING'));

		$this->assign('form_id', $this->getId());

		$this->assign('form_inputs', $this->getInputs());
		$this->assign('form_buttons', $this->getButtons());

		$this->assign('form_open', $this->openForm());
		$this->assign('form_close', $this->closeForm());

		$this->display('form.php');
		$this->display('form.js');
	}

	/**
	 * (non-PHPdoc)
	 * @see \tfc\mvc\form\FormBuilder::getInputs()
	 */
	public function getInputs()
	{
		$output = '';
		$inputElements = $this->getInputElements();
		foreach ($inputElements as $inputElement) {
			$output .= $this->getHtml()->tag('div', array('class' => 'form-group'), $inputElement->fetch());
		}

		return $output;
	}
}
