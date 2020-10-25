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
use tdo\CommandBuilder;

/**
 * GcDb class file
 * 生成“数据库操作类”
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: GcDb.php 1 2014-01-18 14:19:29Z huan.song $
 * @package modules.builder.model.gc
 * @since 1.0
 */
class GcDb extends AbstractGc
{
	const CLASS_COMMENT = '业务层：数据库操作类';

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
	 * 创建DB类
	 * @return void
	 */
	public function normal()
	{
		$fileManager = $this->fileManager;
		$schema = $this->schema;
		$tableName = "\$tableName = \$this->getTblprefix() . TableNames::get{$schema->ucClsName}();";
		$fieldNames = array();
		foreach ($schema->fields as $rows) {
			$fieldNames[] = $rows['field_name'];
		}

		$commandBuilder = new CommandBuilder();
		$quotePkColumn = $commandBuilder->quoteColumnName($schema->pkColumn);
		$placeHolders = CommandBuilder::PLACE_HOLDERS;

		$filePath = $fileManager->db . DS . $schema->ucClsName . '.php';
		$stream = $fileManager->fopen($filePath);
		$fileManager->writeCopyrightComment($stream);

		fwrite($stream, "namespace {$schema->srvName}\\db;\n\n");
		fwrite($stream, "use tdo\\AbstractDb;\n");
		fwrite($stream, "use {$schema->srvName}\\library\\Constant;\n");
		fwrite($stream, "use {$schema->srvName}\library\TableNames;\n\n");

		$fileManager->writeClassComment($stream, $schema->ucClsName, self::CLASS_COMMENT, "{$schema->srvName}.db");

		fwrite($stream, "class {$schema->ucClsName} extends AbstractDb\n");
		fwrite($stream, "{\n");
		fwrite($stream, "\t/**\n");
		fwrite($stream, "\t * @var string 数据库配置名\n");
		fwrite($stream, "\t */\n");
		fwrite($stream, "\tprotected \$_clusterName = Constant::DB_CLUSTER;\n\n");

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
		fwrite($stream, "\t\t\$commandBuilder = \$this->getCommandBuilder();\n");
		fwrite($stream, "\t\t{$tableName}\n");
		$command = '\'' . str_replace($commandBuilder->quoteTableName('$tableName'), $commandBuilder->quoteTableName('\' . $tableName . \''), $commandBuilder->createFind('$tableName', $fieldNames, '', '', 0, 0, '\' . $option . \'')) . '\'';
		$command = str_replace('$OPTION', '$option', $command);
		fwrite($stream, "\t\t\$sql = {$command};\n\n");
		fwrite($stream, "\t\t\$condition = '1';\n");
		fwrite($stream, "\t\t\$attributes = array();\n\n");

		$fields = $schema->fields;
		$aiFields = array();
		foreach ($fields as $key => $rows) {
			if ($rows['column_auto_increment']) {
				$aiFields[$key] = $rows;
				unset($fields[$key]);
				continue;
			}
		}

		$fields = array_merge($fields, $aiFields);
		foreach ($fields as $rows) {
			$joinStr = $rows['column_auto_increment'] ? 'AND' : 'AND';
			$isInt = ($rows['field_type'] === 'INT') ? true : false;
			fwrite($stream, "\t\tif (isset(\$params['{$rows['field_name']}'])) {\n");
			if ($isInt) {
				fwrite($stream, "\t\t\t{$rows['var_name']} = (int) \$params['{$rows['field_name']}'];\n");
				fwrite($stream, "\t\t\tif ({$rows['var_name']} > 0) {\n");
				fwrite($stream, "\t\t\t\t\$condition .= ' $joinStr " . $commandBuilder->quoteColumnName($rows['field_name']) . " = ' . \$commandBuilder::PLACE_HOLDERS;\n");
				fwrite($stream, "\t\t\t\t\$attributes['{$rows['field_name']}'] = {$rows['var_name']};\n");
			}
			else {
				fwrite($stream, "\t\t\t{$rows['var_name']} = trim(\$params['{$rows['field_name']}']);\n");
				fwrite($stream, "\t\t\tif ({$rows['var_name']} !== '') {\n");
				fwrite($stream, "\t\t\t\t\$condition .= ' $joinStr " . $commandBuilder->quoteColumnName($rows['field_name']) . " LIKE ' . \$commandBuilder::PLACE_HOLDERS;\n");
				fwrite($stream, "\t\t\t\t\$attributes['{$rows['field_name']}'] = '%' . {$rows['var_name']} . '%';\n");
			}

			fwrite($stream, "\t\t\t}\n");
			fwrite($stream, "\t\t}\n\n");
		}

		fwrite($stream, "\t\t\$sql = \$commandBuilder->applyCondition(\$sql, \$condition);\n");
		fwrite($stream, "\t\t\$sql = \$commandBuilder->applyOrder(\$sql, \$order);\n");
		fwrite($stream, "\t\t\$sql = \$commandBuilder->applyLimit(\$sql, \$limit, \$offset);\n\n");

		fwrite($stream, "\t\tif (\$option === 'SQL_CALC_FOUND_ROWS') {\n");

		fwrite($stream, "\t\t\t\$ret = \$this->fetchAllNoCache(\$sql, \$attributes);\n\n");
		foreach ($fields as $rows) {
			if ($rows['field_type'] !== 'INT') {
				fwrite($stream, "\t\t\tif (isset(\$attributes['{$rows['field_name']}'])) {\n");
				fwrite($stream, "\t\t\t\t\$attributes['{$rows['field_name']}'] = {$rows['var_name']};\n");
				fwrite($stream, "\t\t\t}\n\n");
			}
		}

		fwrite($stream, "\t\t\tif (is_array(\$ret)) {\n");
		fwrite($stream, "\t\t\t\t\$ret['attributes'] = \$attributes;\n");
		fwrite($stream, "\t\t\t\t\$ret['order']      = \$order;\n");
		fwrite($stream, "\t\t\t\t\$ret['limit']      = \$limit;\n");
		fwrite($stream, "\t\t\t\t\$ret['offset']     = \$offset;\n");
		fwrite($stream, "\t\t\t}\n");
		fwrite($stream, "\t\t}\n");
		fwrite($stream, "\t\telse {\n");
		fwrite($stream, "\t\t\t\$ret = \$this->fetchAll(\$sql, \$attributes);\n");
		fwrite($stream, "\t\t}\n\n");
		fwrite($stream, "\t\treturn \$ret;\n");
		fwrite($stream, "\t}\n\n");

		fwrite($stream, "\t/**\n");
		fwrite($stream, "\t * 通过主键，查询一条记录\n");
		fwrite($stream, "\t * @param integer {$schema->pkVarColumn}\n");
		fwrite($stream, "\t * @return array\n");
		fwrite($stream, "\t */\n");
		fwrite($stream, "\tpublic function findByPk({$schema->pkVarColumn})\n");
		fwrite($stream, "\t{\n");
		fwrite($stream, "\t\tif (({$schema->pkVarColumn} = (int) {$schema->pkVarColumn}) <= 0) {\n");
		fwrite($stream, "\t\t\treturn false;\n");
		fwrite($stream, "\t\t}\n\n");
		fwrite($stream, "\t\t{$tableName}\n");
		$command = str_replace($commandBuilder->quoteTableName('$tableName'), $commandBuilder->quoteTableName('\' . $tableName . \''), $commandBuilder->createFind('$tableName', $fieldNames, "{$quotePkColumn} = {$placeHolders}"));
		fwrite($stream, "\t\t\$sql = '{$command}';\n");
		fwrite($stream, "\t\treturn \$this->fetchAssoc(\$sql, {$schema->pkVarColumn});\n");
		fwrite($stream, "\t}\n\n");

		fwrite($stream, "\t/**\n");
		fwrite($stream, "\t * 新增一条记录\n");
		fwrite($stream, "\t * @param array \$params\n");
		fwrite($stream, "\t * @param boolean \$ignore\n");
		fwrite($stream, "\t * @return integer\n");
		fwrite($stream, "\t */\n");
		fwrite($stream, "\tpublic function create(array \$params = array(), \$ignore = false)\n");
		fwrite($stream, "\t{\n");
		$requiredNames = '';
		foreach ($schema->fields as $rows) {
			if (!$rows['column_auto_increment']) {
				$requiredNames .= ', \'' . $rows['field_name'] . '\'';
			}
		}

		foreach ($schema->fields as $rows) {
			if ($rows['column_auto_increment']) {
				continue;
			}

			if ($rows['field_type'] === 'INT') {
				fwrite($stream, "\t\t{$rows['var_name']} = isset(\$params['{$rows['field_name']}']) ? (int) \$params['{$rows['field_name']}'] : 0;\n");
			}
			else {
				fwrite($stream, "\t\t{$rows['var_name']} = isset(\$params['{$rows['field_name']}']) ? trim(\$params['{$rows['field_name']}']) : '';\n");
			}
		}

		$fields = array();
		foreach ($schema->fields as $rows) {
			if ($rows['column_auto_increment']) {
				continue;
			}

			if ($rows['field_type'] === 'INT') {
				$fields[] = "{$rows['var_name']} < 0";
			}
			else {
				$fields[] = "{$rows['var_name']} === ''";
			}
		}

		fwrite($stream, "\n\t\tif (" . implode(' || ', $fields) . ") {\n\t\t\treturn false;\n\t\t}\n");

		fwrite($stream, "\n");
		fwrite($stream, "\t\t{$tableName}\n");
		fwrite($stream, "\t\t\$attributes = array(\n");
		foreach ($schema->fields as $rows) {
			if ($rows['column_auto_increment']) {
				continue;
			}

			fwrite($stream, "\t\t\t'{$rows['field_name']}' => {$rows['var_name']},\n");
		}

		fwrite($stream, "\t\t);\n\n");
		fwrite($stream, "\t\t\$sql = \$this->getCommandBuilder()->createInsert(\$tableName, array_keys(\$attributes), \$ignore);\n");
		fwrite($stream, "\t\t\$lastInsertId = \$this->insert(\$sql, \$attributes);\n");
		fwrite($stream, "\t\treturn \$lastInsertId;\n\t}\n\n");

		fwrite($stream, "\t/**\n");
		fwrite($stream, "\t * 通过主键，编辑一条记录\n");
		fwrite($stream, "\t * @param integer {$schema->pkVarColumn}\n");
		fwrite($stream, "\t * @param array \$params\n");
		fwrite($stream, "\t * @return integer\n");
		fwrite($stream, "\t */\n");
		fwrite($stream, "\tpublic function modifyByPk({$schema->pkVarColumn}, array \$params = array())\n");
		fwrite($stream, "\t{\n");
		fwrite($stream, "\t\tif (({$schema->pkVarColumn} = (int) {$schema->pkVarColumn}) <= 0) {\n");
		fwrite($stream, "\t\t\treturn false;\n");
		fwrite($stream, "\t\t}\n\n");

		fwrite($stream, "\t\t\$attributes = array();\n\n");
		foreach ($schema->fields as $rows) {
			if ($rows['column_auto_increment']) {
				continue;
			}

			fwrite($stream, "\t\tif (isset(\$params['{$rows['field_name']}'])) {\n");
			if ($rows['field_type'] === 'INT') {
				fwrite($stream, "\t\t\t{$rows['var_name']} = (int) \$params['{$rows['field_name']}'];\n");
				fwrite($stream, "\t\t\tif ({$rows['var_name']} > 0) {\n");
			}
			else {
				fwrite($stream, "\t\t\t{$rows['var_name']} = trim(\$params['{$rows['field_name']}']);\n");
				fwrite($stream, "\t\t\tif ({$rows['var_name']} !== '') {\n");
			}

			fwrite($stream, "\t\t\t\t\$attributes['{$rows['field_name']}'] = {$rows['var_name']};\n");
			fwrite($stream, "\t\t\t}\n\t\t\telse {\n\t\t\t\treturn false;\n\t\t\t}\n");
			fwrite($stream, "\t\t}\n\n");
		}

		fwrite($stream, "\t\t\$rowCount = 0;\n\n");

		fwrite($stream, "\t\tif (\$attributes === array()) {\n");
		fwrite($stream, "\t\t\treturn \$rowCount;\n");
		fwrite($stream, "\t\t}\n");

		fwrite($stream, "\n");
		fwrite($stream, "\t\t{$tableName}\n");

		fwrite($stream, "\t\t\$sql = \$this->getCommandBuilder()->createUpdate(\$tableName, array_keys(\$attributes), '{$quotePkColumn} = {$placeHolders}');\n");
		fwrite($stream, "\t\t\$attributes['{$schema->pkColumn}'] = {$schema->pkVarColumn};\n");
		fwrite($stream, "\t\t\$rowCount = \$this->update(\$sql, \$attributes);\n");
		fwrite($stream, "\t\treturn \$rowCount;\n\t}\n\n");

		fwrite($stream, "\t/**\n");
		fwrite($stream, "\t * 通过主键，删除一条记录\n");
		fwrite($stream, "\t * @param integer {$schema->pkVarColumn}\n");
		fwrite($stream, "\t * @return integer\n");
		fwrite($stream, "\t */\n");
		fwrite($stream, "\tpublic function removeByPk({$schema->pkVarColumn})\n");
		fwrite($stream, "\t{\n");
		fwrite($stream, "\t\tif (({$schema->pkVarColumn} = (int) {$schema->pkVarColumn}) <= 0) {\n");
		fwrite($stream, "\t\t\treturn false;\n");
		fwrite($stream, "\t\t}\n\n");
		fwrite($stream, "\t\t{$tableName}\n");

		fwrite($stream, "\t\t\$sql = \$this->getCommandBuilder()->createDelete(\$tableName, '{$quotePkColumn} = {$placeHolders}');\n");
		fwrite($stream, "\t\t\$rowCount = \$this->delete(\$sql, {$schema->pkVarColumn});\n");
		fwrite($stream, "\t\treturn \$rowCount;\n\t}\n");

		fwrite($stream, "}\n");

		fclose($stream);
	}

	/**
	 * 创建DynamicDB类
	 * @return void
	 */
	public function dynamic()
	{
		$fileManager = $this->fileManager;
		$schema = $this->schema;

		$filePath = $fileManager->db . DS . $schema->ucClsName . '.php';
		$stream = $fileManager->fopen($filePath);
		$fileManager->writeCopyrightComment($stream);

		fwrite($stream, "namespace {$schema->srvName}\\db;\n\n");
		fwrite($stream, "use tdo\\DynamicDb;\n");
		fwrite($stream, "use {$schema->srvName}\\library\\Constant;\n\n");

		$fileManager->writeClassComment($stream, $schema->ucClsName, self::CLASS_COMMENT, "{$schema->srvName}.db");

		fwrite($stream, "class {$schema->ucClsName} extends DynamicDb\n");
		fwrite($stream, "{\n");
		fwrite($stream, "\t/**\n");
		fwrite($stream, "\t * @var string 数据库配置名\n");
		fwrite($stream, "\t */\n");
		fwrite($stream, "\tprotected \$_clusterName = Constant::DB_CLUSTER;\n");
		fwrite($stream, "}\n");

		fclose($stream);
	}
}
