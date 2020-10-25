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
 * GcFormProcessor class file
 * 生成“表单数据处理类”
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: GcFormProcessor.php 1 2014-01-18 14:19:29Z huan.song $
 * @package modules.builder.model.gc
 * @since 1.0
 */
class GcFormProcessor extends AbstractGc
{
	const CLASS_COMMENT = '业务层：表单数据处理类';

	/**
	 * (non-PHPdoc)
	 * @see \modules\builder\model\gc\AbstractGc::_exec()
	 */
	protected function _exec()
	{
		$fileManager = $this->fileManager;
		$schema = $this->schema;

		$clsName = 'Fp' . $schema->ucClsName;

		$fieldNames = $validatorNames = '';
		foreach ($schema->fields as $rows) {
			if (!$rows['column_auto_increment']) {
				$fieldNames .= ', \'' . $rows['field_name'] . '\'';
			}

			if ($rows['validators']) {
				$validatorNames .= ', \'' . $rows['field_name'] . '\'';
			}
		}

		$filePath = $fileManager->services . DS . $clsName . '.php';
		$stream = $fileManager->fopen($filePath);
		$fileManager->writeCopyrightComment($stream);

		fwrite($stream, "namespace {$schema->srvName}\\services;\n\n");
		fwrite($stream, "use libsrv\\FormProcessor;\n");
		fwrite($stream, "use tfc\\validator;\n");
		fwrite($stream, "use {$schema->srvName}\\library\\Lang;\n\n");

		$fileManager->writeClassComment($stream, $clsName, self::CLASS_COMMENT, "{$schema->srvName}.services");

		fwrite($stream, "class {$clsName} extends FormProcessor\n");
		fwrite($stream, "{\n");

		fwrite($stream, "\t/**\n");
		fwrite($stream, "\t * (non-PHPdoc)\n");
		fwrite($stream, "\t * @see \\libsrv\\FormProcessor::_process()\n");
		fwrite($stream, "\t */\n");
		fwrite($stream, "\tprotected function _process(array \$params = array())\n");
		fwrite($stream, "\t{\n");
		fwrite($stream, "\t\tif (\$this->isInsert()) {\n");
		fwrite($stream, "\t\t\tif (!\$this->required(\$params{$fieldNames})) {\n");
		fwrite($stream, "\t\t\t\treturn false;\n");
		fwrite($stream, "\t\t\t}\n");
		fwrite($stream, "\t\t}\n\n");
		fwrite($stream, "\t\t\$this->isValids(\$params{$fieldNames});\n");
		fwrite($stream, "\t\treturn !\$this->hasError();\n");
		fwrite($stream, "\t}\n\n");

		fwrite($stream, "\t/**\n");
		fwrite($stream, "\t * (non-PHPdoc)\n");
		fwrite($stream, "\t * @see \\libsrv\\FormProcessor::_cleanPreProcess()\n");
		fwrite($stream, "\t */\n");
		fwrite($stream, "\tprotected function _cleanPreProcess(array \$params)\n");
		fwrite($stream, "\t{\n");
		if ($schema->hasTrash) {
			fwrite($stream, "\t\tif (isset(\$params['trash'])) {\n");
			fwrite($stream, "\t\t\tunset(\$params['trash']);\n");
			fwrite($stream, "\t\t}\n\n");
		}

		fwrite($stream, "\t\t\$rules = array(\n");
		foreach ($schema->fields as $rows) {
			if ($rows['column_auto_increment']) {
				continue;
			}

			// $comment = ($rows['field_name'] === 'sort') ? '// ' : '';
			$comment = '';
			if ($rows['field_type'] === 'INT') {
				fwrite($stream, "\t\t\t$comment'{$rows['field_name']}' => 'intval',\n");
			}
			elseif ($rows['form_type'] === 'checkbox') {
				fwrite($stream, "\t\t\t$comment'{$rows['field_name']}' => '\\libsrv\\Clean::trims',\n");
			}
			else {
				fwrite($stream, "\t\t\t$comment'{$rows['field_name']}' => 'trim',\n");
			}
		}

		fwrite($stream, "\t\t);\n\n");
		fwrite($stream, "\t\t\$ret = \$this->clean(\$rules, \$params);\n");
		fwrite($stream, "\t\treturn \$ret;\n");
		fwrite($stream, "\t}\n\n");

		foreach ($schema->fields as $rows) {
			if (!isset($rows['validators']) || !is_array($rows['validators']) || $rows['validators'] === array()) {
				continue;
			}

			fwrite($stream, "\t/**\n");
			fwrite($stream, "\t * 获取“{$rows['html_label']}”验证规则\n");
			fwrite($stream, "\t * @param mixed \$value\n");
			fwrite($stream, "\t * @return array\n");
			fwrite($stream, "\t */\n");
			fwrite($stream, "\tpublic function get{$rows['func_name']}Rule(\$value)\n");
			fwrite($stream, "\t{\n");
			if (!$rows['form_required']) {
				// fwrite($stream, "\t\tif (\$value === '') { return array(); }\n\n");
			}

			if (isset($rows['enums'])) {
				fwrite($stream, "\t\t\$enum = Data{$schema->ucClsName}::get{$rows['func_name']}Enum();\n");
			}

			fwrite($stream, "\t\treturn array(\n");
			foreach ($rows['validators'] as $validators) {
				$validatorName = $validators['validator_name'];
				if (isset($rows['enums']) && $validatorName === 'InArray') {
					$options = "array_keys(\$enum)";
					$message = "sprintf(Lang::_('{$validators['lang_key']}'), implode(', ', \$enum))";
					fwrite($stream, "\t\t\t'{$validatorName}' => new validator\\InArrayValidator(\$value, {$options}, {$message}),\n");
				}
				else {
					$options = $validators['options'];
					switch ($validators['option_category']) {
						case 'integer' :
							$options = (int) $options;
							break;
						case 'boolean' :
							$options = 'true';
							break;
						case 'array' :
							$options = 'array()';
							break;
						case 'string' :
						default :
							$options = "'" . (string) $options . "'";
							break;
					}
					fwrite($stream, "\t\t\t'{$validatorName}' => new validator\\{$validatorName}Validator(\$value, {$options}, Lang::_('{$validators['lang_key']}')),\n");
				}
			}

			fwrite($stream, "\t\t);\n");
			fwrite($stream, "\t}\n\n");
		}

		fwrite($stream, "}\n");
		fclose($stream);
	}
}
