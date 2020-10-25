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
 * GcData class file
 * 生成“数据管理类”
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: GcData.php 1 2014-01-18 14:19:29Z huan.song $
 * @package modules.builder.model.gc
 * @since 1.0
 */
class GcData extends AbstractGc
{
	const CLASS_COMMENT = '业务层：数据管理类，寄存常量、选项';

	/**
	 * (non-PHPdoc)
	 * @see \modules\builder\model\gc\AbstractGc::_exec()
	 */
	protected function _exec()
	{
		$fileManager = $this->fileManager;
		$schema = $this->schema;

		$clsName = 'Data' . $schema->ucClsName;

		$filePath = $fileManager->services . DS . $clsName . '.php';
		$stream = $fileManager->fopen($filePath);
		$fileManager->writeCopyrightComment($stream);

		fwrite($stream, "namespace {$schema->srvName}\\services;\n\n");
		fwrite($stream, "use {$schema->srvName}\\library\\Lang;\n\n");

		$fileManager->writeClassComment($stream, $clsName, self::CLASS_COMMENT, "{$schema->srvName}.services");

		fwrite($stream, "class {$clsName}\n");
		fwrite($stream, "{\n");

		foreach ($schema->fields as $rows) {
			if (isset($rows['enums'])) {
				foreach ($rows['enums'] as $enums) {
					fwrite($stream, "\t/**\n");
					fwrite($stream, "\t * @var string {$rows['html_label']}：{$enums['value']}\n");
					fwrite($stream, "\t */\n");
					fwrite($stream, "\tconst {$enums['const_key']} = '{$enums['value']}';\n\n");
				}
			}
		}

		foreach ($schema->fields as $rows) {
			if (isset($rows['enums'])) {
				fwrite($stream, "\t/**\n");
				fwrite($stream, "\t * 获取“{$rows['html_label']}”所有选项\n");
				fwrite($stream, "\t * @return array\n");
				fwrite($stream, "\t */\n");
				fwrite($stream, "\tpublic static function get{$rows['func_name']}Enum()\n");
				fwrite($stream, "\t{\n");
				fwrite($stream, "\t\tstatic \$enum = null;\n\n");
				fwrite($stream, "\t\tif (\$enum === null) {\n");
				fwrite($stream, "\t\t\t\$enum = array(\n");
				foreach ($rows['enums'] as $enums) {
					fwrite($stream, "\t\t\t\tself::{$enums['const_key']} => Lang::_('{$enums['lang_key']}'),\n");
				}

				fwrite($stream, "\t\t\t);\n");
				fwrite($stream, "\t\t}\n\n");
				fwrite($stream, "\t\treturn \$enum;\n");
				fwrite($stream, "\t}\n\n");
			}
		}

		fwrite($stream, "}\n");
		fclose($stream);
	}
}
