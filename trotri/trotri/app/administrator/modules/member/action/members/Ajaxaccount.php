<?php
/**
 * Trotri
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright &copy; 2011-2014 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace modules\member\action\members;

use library\DataAction;
use tfc\ap\Ap;
use tfc\mvc\Mvc;
use tfc\auth\Role;
use tfc\saf\Text;
use libapp\Model;

/**
 * Ajaxaccount class file
 * Ajax展示和提交会员账户
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: Ajaxaccount.php 1 2014-09-29 23:33:28Z huan.song $
 * @package modules.member.action.members
 * @since 1.0
 */
class Ajaxaccount extends DataAction
{
	/**
	 * @var integer 允许的权限
	 */
	protected $_power = Role::UPDATE;

	/**
	 * (non-PHPdoc)
	 * @see \tfc\mvc\interfaces\Action::run()
	 */
	public function run()
	{
		$req = Ap::getRequest();

		$submitType = $req->getTrim('submit_type');
		$id = $req->getInteger('id');
		$columnName = $req->getTrim('column_name');
		$opType = $req->getTrim('op_type');
		$value = $req->getParam('value');
		if ($submitType === 'save') {
			$mod = Model::getInstance('Members');
			$ret = $mod->opAccount($columnName, $opType, $id, $value);
			$url = $this->applyParams($mod->getLLU(), $ret);
			$this->redirect($url);
			exit;
		}

		$loginName = $req->getTrim('login_name');

		$html = Mvc::getView()->getHtml();
		$title = Text::_('MOD_MEMBER_MEMBERS_OP_' . $columnName . '_' . $opType) . ': ' . $loginName . ' ,  ' . $value;
		$url = Mvc::getView()->getUrlManager()->getUrl('ajaxaccount', Mvc::$controller, Mvc::$module, array(
			'id' => $id,
			'column_name' => $columnName,
			'op_type' => $opType,
			'submit_type' => 'save',
			'value' => ''
		));

		$body  = $html->input('hidden', 'url', $url);
		$body .= $html->openTag('div', array('class' => 'form-group'));
		$body .= $html->tag('label', array('class' => 'col-lg-2 control-label'), Text::_('MOD_MEMBER_MEMBERS_OP_' . $opType));
		$body .= $html->tag('div', array('class' => 'col-lg-4'), $html->input('text', 'value', '', array('class' => 'form-control input-sm')));	
		if ($opType === 'reduce' || $opType === 'reduce_freeze' || $opType === 'freeze') {
			$body .= $html->tag('span', array('class' => 'control-label'), Text::_('MOD_MEMBER_MEMBERS_OP_REDUCE_HINT'));
		}

		$body .= $html->closeTag('div');

		$this->display(array(
			'title' => $title,
			'body' => $body
		));
	}
}
