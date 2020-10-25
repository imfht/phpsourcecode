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

use tfc\saf\Log;

/**
 * GcService class file
 * 生成“业务层业务处理类”
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: GcService.php 1 2014-01-18 14:19:29Z huan.song $
 * @package modules.builder.model.gc
 * @since 1.0
 */
class GcService extends AbstractGc
{
	const CLASS_COMMENT = '业务层：业务处理类';

	/**
	 * (non-PHPdoc)
	 * @see \modules\builder\model\gc\AbstractGc::_exec()
	 */
	protected function _exec()
	{
		Log::echoTrace('SRV Type "' . $this->schema->srvType . '" ...');

		if ($this->schema->srvType === 'normal') {
			$this->normal();
		}

		if ($this->schema->srvType === 'dynamic') {
			$this->dynamic();
		}
	}

	/**
	 * 创建AbstractService类
	 * @return void
	 */
	public function normal()
	{
		$fileManager = $this->fileManager;
		$schema = $this->schema;

		$filePath = $fileManager->services . DS . $schema->ucClsName . '.php';
		$stream = $fileManager->fopen($filePath);
		$fileManager->writeCopyrightComment($stream);

		fwrite($stream, "namespace {$schema->srvName}\\services;\n\n");
		fwrite($stream, "use libsrv\\AbstractService;\n");
		fwrite($stream, "use {$schema->srvName}\\library\\Constant;\n\n");
		// fwrite($stream, "use {$schema->srvName}\\db\\{$schema->ucClsName} AS Db{$schema->ucClsName};\n\n");

		$fileManager->writeClassComment($stream, $schema->ucClsName, self::CLASS_COMMENT, "{$schema->srvName}.services");

		fwrite($stream, "class {$schema->ucClsName} extends AbstractService\n");
		fwrite($stream, "{\n");
		// fwrite($stream, "\t/**\n");
		// fwrite($stream, "\t * @var instance of {$schema->srvName}\\db\\{$schema->ucClsName}\n");
		// fwrite($stream, "\t */\n");
		// fwrite($stream, "\tprotected \$_db{$schema->ucClsName} = null;\n\n");

		// fwrite($stream, "\t/**\n");
		// fwrite($stream, "\t * 构造方法：初始化数据库操作类\n");
		// fwrite($stream, "\t */\n");
		// fwrite($stream, "\tpublic function __construct()\n");
		// fwrite($stream, "\t{\n");
		// fwrite($stream, "\t\tparent::__construct();\n\n");
		// fwrite($stream, "\t\t\$this->_db{$schema->ucClsName} = new Db{$schema->ucClsName}();\n");
		// fwrite($stream, "\t}\n\n");

		fwrite($stream, "\t/**\n");
		fwrite($stream, "\t * 查询多条记录\n");
		fwrite($stream, "\t * @param array \$params\n");
		fwrite($stream, "\t * @param string \$order\n");
		fwrite($stream, "\t * @param integer \$limit\n");
		fwrite($stream, "\t * @param integer \$offset\n");
		fwrite($stream, "\t * @param string \$option\n");
		fwrite($stream, "\t * @return array\n");
		fwrite($stream, "\t */\n");
		fwrite($stream, "\tpublic function findAll(array \$params = array(), \$order = '', \$limit = 0, \$offset = 0, \$option = '')\n");
		fwrite($stream, "\t{\n");
		fwrite($stream, "\t\t\$limit = min(max((int) \$limit, 1), Constant::FIND_MAX_LIMIT);\n");
		fwrite($stream, "\t\t\$offset = max((int) \$offset, 0);\n\n");
		fwrite($stream, "\t\t\$rows = \$this->getDb()->findAll(\$params, \$order, \$limit, \$offset, \$option);\n");
		fwrite($stream, "\t\treturn \$rows;\n");
		fwrite($stream, "\t}\n\n");

		fwrite($stream, "\t/**\n");
		fwrite($stream, "\t * 通过主键，查询一条记录\n");
		fwrite($stream, "\t * @param integer {$schema->pkVarColumn}\n");
		fwrite($stream, "\t * @return array\n");
		fwrite($stream, "\t */\n");
		fwrite($stream, "\tpublic function findByPk({$schema->pkVarColumn})\n");
		fwrite($stream, "\t{\n");
		fwrite($stream, "\t\t\$row = \$this->getDb()->findByPk({$schema->pkVarColumn});\n");
		fwrite($stream, "\t\treturn \$row;\n");
		fwrite($stream, "\t}\n\n");

		$pkFuncColumn = $schema->column2Name($schema->pkColumn);
		foreach ($schema->fields as $rows) {
			if ($rows['field_name'] === $schema->pkColumn) {
				continue;
			}

			$funcName = 'get' . $schema->column2Name($rows['field_name']) . 'By' . $pkFuncColumn;
			fwrite($stream, "\t/**\n");
			fwrite($stream, "\t * 通过“主键ID”，获取“{$rows['html_label']}”\n");
			fwrite($stream, "\t * @param integer {$schema->pkVarColumn}\n");
			if ($rows['field_type'] === 'INT') {
				fwrite($stream, "\t * @return integer\n");
			}
			else {
				fwrite($stream, "\t * @return string\n");
			}

			fwrite($stream, "\t */\n");
			fwrite($stream, "\tpublic function {$funcName}({$schema->pkVarColumn})\n");
			fwrite($stream, "\t{\n");
			fwrite($stream, "\t\t\$value = \$this->getByPk('{$rows['field_name']}', {$schema->pkVarColumn});\n");
			if ($rows['field_type'] === 'INT') {
				fwrite($stream, "\t\treturn \$value ? (int) \$value : 0;\n");
			}
			else {
				fwrite($stream, "\t\treturn \$value ? \$value : '';\n");
			}

			fwrite($stream, "\t}\n\n");
		}

		fwrite($stream, "}\n");
		fclose($stream);
	}

	/**
	 * 创建DynamicService类
	 * @return void
	 */
	public function dynamic()
	{
		$fileManager = $this->fileManager;
		$schema = $this->schema;

		$filePath = $fileManager->services . DS . $schema->ucClsName . '.php';
		$stream = $fileManager->fopen($filePath);
		$fileManager->writeCopyrightComment($stream);

		fwrite($stream, "namespace {$schema->srvName}\\services;\n\n");
		fwrite($stream, "use libsrv\\DynamicService;\n\n");

		$fileManager->writeClassComment($stream, $schema->ucClsName, self::CLASS_COMMENT, "{$schema->srvName}.services");

		fwrite($stream, "class {$schema->ucClsName} extends DynamicService\n");
		fwrite($stream, "{\n");
		fwrite($stream, "\t/**\n");
		fwrite($stream, "\t * @var string 表名\n");
		fwrite($stream, "\t */\n");
		fwrite($stream, "\tprotected \$_tableName = '{$schema->tblName}';\n\n");

		$pkFuncColumn = $schema->column2Name($schema->pkColumn);
		foreach ($schema->fields as $rows) {
			if ($rows['field_name'] === $schema->pkColumn) {
				continue;
			}

			$funcName = 'get' . $schema->column2Name($rows['field_name']) . 'By' . $pkFuncColumn;
			fwrite($stream, "\t/**\n");
			fwrite($stream, "\t * 通过“主键ID”，获取“{$rows['html_label']}”\n");
			fwrite($stream, "\t * @param integer {$schema->pkVarColumn}\n");
			if ($rows['field_type'] === 'INT') {
				fwrite($stream, "\t * @return integer\n");
			}
			else {
				fwrite($stream, "\t * @return string\n");
			}

			fwrite($stream, "\t */\n");
			fwrite($stream, "\tpublic function {$funcName}({$schema->pkVarColumn})\n");
			fwrite($stream, "\t{\n");
			fwrite($stream, "\t\t\$value = \$this->getByPk('{$rows['field_name']}', {$schema->pkVarColumn});\n");
			if ($rows['field_type'] === 'INT') {
				fwrite($stream, "\t\treturn \$value ? (int) \$value : 0;\n");
			}
			else {
				fwrite($stream, "\t\treturn \$value ? \$value : '';\n");
			}

			fwrite($stream, "\t}\n\n");
		}

		fwrite($stream, "}\n");

		fclose($stream);
	}
}
