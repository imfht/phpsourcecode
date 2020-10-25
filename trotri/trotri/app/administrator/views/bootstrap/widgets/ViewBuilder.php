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

/**
 * ViewBuilder class file
 * 视图展示类，基于Bootstrap-v3前端开发框架
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: ViewBuilder.php 1 2013-05-18 14:58:59Z huan.song $
 * @package views.bootstrap.widgets
 * @since 1.0
 */
class ViewBuilder extends FormBuilder
{
	/**
	 * (non-PHPdoc)
	 * @see \views\bootstrap\widgets\FormBuilder::initElements()
	 */
	public function initElements()
	{
		$columns = isset($this->_tplVars['columns']) ? (array) $this->_tplVars['columns'] : array();
		if ($columns === array()) {
			return $this;
		}

		$this->_tplVars['elements'] = isset($this->_tplVars['elements']) ? (array) $this->_tplVars['elements'] : array();
		foreach ($columns as $columnName) {
			$this->_tplVars['elements'][$columnName]['readonly'] = true;
			$this->_tplVars['elements'][$columnName]['disabled'] = true;
		}

		parent::initElements();
	}
}
