<?php
/**
 * Trotri
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright &copy; 2011-2014 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace modules\builder\action\validators;

use library\actions;
use libapp\Model;

/**
 * Create class file
 * 新增数据
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: Create.php 1 2014-05-28 11:06:31Z Code Generator $
 * @package modules.builder.action.validators
 * @since 1.0
 */
class Create extends actions\Create
{
	/**
	 * (non-PHPdoc)
	 * @see \tfc\mvc\interfaces\Action::run()
	 */
	public function run()
	{
		$mod = Model::getInstance('Validators');
		$fieldId = $mod->getFieldId();
		if ($fieldId <= 0) {
			$this->err404();
		}

		$messageEnum = $mod->getMessageEnum();
		$optionCategoryEnum = $mod->getOptionCategoryEnum();

		$mod = Model::getInstance('Fields');
		$builderId = $mod->getBuilderIdByFieldId($fieldId);
		if ($builderId <= 0) {
			$this->err404();
		}

		$this->assign('message_enum', json_encode($messageEnum));
		$this->assign('option_category_enum', json_encode($optionCategoryEnum));
		$this->assign('field_id', $fieldId);
		$this->assign('builder_id', $builderId);
		$this->execute('Validators');
	}
}
