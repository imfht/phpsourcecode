<?php
/**
 * Trotri
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright &copy; 2011-2013 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace modules\builder\model\gc;

/**
 * GcModel class file
 * 生成“项目层模型类”
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: GcModel.php 1 2014-01-18 14:19:29Z huan.song $
 * @package modules.builder.model.gc
 * @since 1.0
 */
class GcModel extends AbstractGc
{
	/**
	 * (non-PHPdoc)
	 * @see \modules\builder\model\gc\AbstractGc::_exec()
	 */
	protected function _exec()
	{
		$fileManager = $this->fileManager;
		$schema = $this->schema;

		$filePath = $fileManager->model . DS . $schema->ucClsName . '.php';
		$stream = $fileManager->fopen($filePath);
		$fileManager->writeCopyrightComment($stream);

		fwrite($stream, "namespace modules\\{$schema->modName}\\model;\n\n");
		fwrite($stream, "use library\\BaseModel;\n");
		if ($schema->fkColumn) {
			fwrite($stream, "use tfc\\ap\\Ap;\n");
		}

		fwrite($stream, "use tfc\\saf\\Text;\n");
		fwrite($stream, "use {$schema->srvName}\\services\\Data{$schema->ucClsName};\n\n");

		$fileManager->writeClassComment($stream, $schema->ucClsName, $schema->builderName, "modules.{$schema->modName}.model");

		fwrite($stream, "class {$schema->ucClsName} extends BaseModel\n");
		fwrite($stream, "{\n");

		fwrite($stream, "\t/**\n");
		fwrite($stream, "\t * (non-PHPdoc)\n");
		fwrite($stream, "\t * @see \\library\\BaseModel::getViewTabsRender()\n");
		fwrite($stream, "\t */\n");
		fwrite($stream, "\tpublic function getViewTabsRender()\n");
		fwrite($stream, "\t{\n");
		fwrite($stream, "\t\t\$output = array(\n");
		foreach ($schema->groups as $rows) {
			if ($rows['group_name'] != 'main') {
				fwrite($stream, "\t\t\t'{$rows['group_name']}' => array(\n");
				fwrite($stream, "\t\t\t\t'tid' => '{$rows['group_name']}',\n");
				fwrite($stream, "\t\t\t\t'prompt' => Text::_('{$rows['lang_key']}')\n");
				fwrite($stream, "\t\t\t),\n");
			}
		}

		fwrite($stream, "\t\t);\n\n");
		fwrite($stream, "\t\treturn \$output;\n");
		fwrite($stream, "\t}\n\n");

		fwrite($stream, "\t/**\n");
		fwrite($stream, "\t * (non-PHPdoc)\n");
		fwrite($stream, "\t * @see \\library\\BaseModel::getElementsRender()\n");
		fwrite($stream, "\t */\n");
		fwrite($stream, "\tpublic function getElementsRender()\n");
		fwrite($stream, "\t{\n");
		fwrite($stream, "\t\t\$output = array(\n");
		foreach ($schema->fields as $rows) {
			$type = ($schema->fkColumn === $rows['field_name']) ? 'hidden' : $rows['form_type'];

			fwrite($stream, "\t\t\t'{$rows['field_name']}' => array(\n");
			fwrite($stream, "\t\t\t\t'__tid__' => '{$rows['__tid__']}',\n");
			fwrite($stream, "\t\t\t\t'type' => '{$type}',\n");
			fwrite($stream, "\t\t\t\t'label' => Text::_('{$rows['lang_label']}'),\n");
			fwrite($stream, "\t\t\t\t'hint' => Text::_('{$rows['lang_hint']}'),\n");
			if ($rows['form_required']) {
				fwrite($stream, "\t\t\t\t'required' => true,\n");
			}

			if ($rows['form_modifiable']) {
				fwrite($stream, "\t\t\t\t'disabled' => true,\n");
			}

			if (isset($rows['enums'])) {
				$enum = array_shift($rows['enums']);
				fwrite($stream, "\t\t\t\t'options' => Data{$schema->ucClsName}::get{$rows['func_name']}Enum(),\n");
				fwrite($stream, "\t\t\t\t'value' => Data{$schema->ucClsName}::{$enum['const_key']},\n");
			}

			fwrite($stream, "\t\t\t),\n");
		}

		fwrite($stream, "\t\t);\n\n");
		fwrite($stream, "\t\treturn \$output;\n");
		fwrite($stream, "\t}\n\n");

		fwrite($stream, "\t/**\n");
		fwrite($stream, "\t * 获取列表页“{$schema->fields[1]['html_label']}”的A标签\n");
		fwrite($stream, "\t * @param array \$data\n");
		fwrite($stream, "\t * @return string\n");
		fwrite($stream, "\t */\n");
		fwrite($stream, "\tpublic function get{$schema->fields[1]['func_name']}Link(\$data)\n");
		fwrite($stream, "\t{\n");
		fwrite($stream, "\t\t\$params = array(\n");
		fwrite($stream, "\t\t\t'id' => \$data['{$schema->pkColumn}'],\n");
		fwrite($stream, "\t\t);\n\n");
		fwrite($stream, "\t\t\$url = \$this->urlManager->getUrl(\$this->actNameView, \$this->controller, \$this->module, \$params);\n");
		fwrite($stream, "\t\t\$output = \$this->html->a(\$data['{$schema->fields[1]['field_name']}'], \$url);\n");
		fwrite($stream, "\t\treturn \$output;\n");
		fwrite($stream, "\t}\n\n");

		if ($schema->fkColumn) {
			$pkFuncColumn = $schema->column2Name($schema->pkColumn);
			fwrite($stream, "\t/**\n");
			fwrite($stream, "\t * 获取{$schema->fkColumn}值\n");
			fwrite($stream, "\t * @return integer\n");
			fwrite($stream, "\t */\n");
			fwrite($stream, "\tpublic function {$schema->fkFuncName}()\n");
			fwrite($stream, "\t{\n");
			fwrite($stream, "\t\t{$schema->fkVarName} = Ap::getRequest()->getInteger('{$schema->fkColumn}');\n");
			fwrite($stream, "\t\tif ({$schema->fkVarName} <= 0) {\n");
			fwrite($stream, "\t\t\t\$id = Ap::getRequest()->getInteger('id');\n");
			fwrite($stream, "\t\t\t{$schema->fkVarName} = \$this->getService()->{$schema->fkFuncName}By{$pkFuncColumn}(\$id);\n");
			fwrite($stream, "\t\t}\n\n");
			fwrite($stream, "\t\treturn {$schema->fkVarName};\n");
			fwrite($stream, "\t}\n\n");
		}

		fwrite($stream, "}\n");
		fclose($stream);
	}
}
