<?php
/**
 * Trotri
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright &copy; 2011-2014 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace modules\builder\model;

use library\BaseModel;
use tfc\ap\Ap;
use tfc\ap\Singleton;
use tfc\saf\Text;
use tfc\saf\DbProxy;
use tfc\saf\Log;
use tdo\Metadata;
use libsrv\Service;
use libapp\Model;
use builders\services\DataBuilders;
use builders\services\DataFields;
use library\ErrorNo;
use library\Constant;
use tfc\ap\UserIdentity;

/**
 * Tblnames class file
 * 数据库表管理
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: Tblnames.php 1 2014-05-26 19:25:19Z Code Generator $
 * @package modules.builder.model
 * @since 1.0
 */
class Tblnames extends BaseModel
{
	/**
	 * @var string 业务层名
	 */
	protected $_srvName = Constant::SRV_NAME_BUILDERS;

	/**
	 * @var instance of tdo\Metadata
	 */
	protected $_metadata = null;

	/**
	 * 初始化MySQL表结构分析类
	 */
	public function _init()
	{
		$this->_metadata = new Metadata($this->getDbProxy());
	}

	/**
	 * (non-PHPdoc)
	 * @see \library\BaseModel::getElementsRender()
	 */
	public function getElementsRender()
	{
		$output = array(
			'stbl_name' => array(
				'label' => Text::_('MOD_BUILDER_BUILDER_TBLNAMES_STBL_NAME_LABEL'),
			),
			'tbl_name' => array(
				'label' => Text::_('MOD_BUILDER_BUILDER_TBLNAMES_TBL_NAME_LABEL'),
			),
			'already_gb' => array(
				'label' => Text::_('MOD_BUILDER_BUILDER_TBLNAMES_ALREADY_GB_LABEL'),
				'options' => $this->getAlreadyGbEnum(),
			),
		);

		return $output;
	}

	/**
	 * 查询数据列表
	 * @param array $params
	 * @return array
	 */
	public function search(array $params = array())
	{
		$stblName = isset($params['stbl_name']) ? trim($params['stbl_name']) : '';
		$alreadyGb = isset($params['already_gb']) ? trim($params['already_gb']) : '';
		if ($stblName === '') {
			$stblName = null;
		}

		$tableNames = $this->_metadata->getTableNames($stblName);
		$alreadyTblNames = Model::getInstance('Builders')->getTblNames();

		$tblPrefix = $this->getDbProxy()->getTblprefix();
		$tblPreLen = strlen($tblPrefix);

		$data = array();
		$p = 0;
		foreach ($tableNames as $tableName) {
			$sTableName = substr($tableName, $tblPreLen);
			$data[$p++] = array(
				'stbl_name' => $sTableName,
				'tbl_name' => $tableName,
				'already_gb' => in_array($sTableName, $alreadyTblNames) ? 'y' : 'n'
			);
		}

		$enum = $this->getAlreadyGbEnum();
		if (isset($enum[$alreadyGb])) {
			foreach ($data as $key => $rows) {
				if ($rows['already_gb'] !== $alreadyGb) {
					unset($data[$key]);
				}
			}
		}

		$ret = array(
			'err_no' => ErrorNo::SUCCESS_NUM,
			'data' => $data,
			'paginator' => array(
				'attributes' => array(
					'stbl_name' => $stblName,
					'already_gb' => $alreadyGb
				)
			)
		);

		return $ret;
	}

	/**
	 * 通过表Metadata生成Builders数据
	 * @param string $tblName
	 * @return void
	 */
	public function gb($tblName)
	{
		Ap::getResponse()->contentType();

		$tableNames = $this->_metadata->getTableNames($tblName);
		if (!in_array($tblName, $tableNames)) {
			Log::errExit(__LINE__, 'Table Name Not Exists!');
		}

		Log::echoTrace('Generate Begin, Table Name "' . $tblName . '"');
		$tableSchema = $this->_metadata->getTableSchema($tblName);
		$comments = $this->_metadata->getComments($tableSchema->name);
		$tblPrefix = $this->getDbProxy()->getTblprefix();
		$tblPreLen = strlen($tblPrefix);

		Log::echoTrace('Import to builders Begin ...');
		$params = array(
			'builder_name' => isset($comments['__table__']) ? $comments['__table__'] : $tableSchema->name,
			'tbl_name' => substr($tableSchema->name, $tblPreLen),
			'tbl_profile' => DataBuilders::TBL_PROFILE_N,
			'tbl_engine' => DataBuilders::TBL_ENGINE_INNODB,
			'tbl_charset' => DataBuilders::TBL_CHARSET_UTF8,
			'tbl_comment' => isset($comments['__table__']) ? $comments['__table__'] : '',
			'srv_type' => DataBuilders::SRV_TYPE_NORMAL,
			'srv_name' => 'undefined',
			'app_name' => 'administrator',
			'mod_name' => 'undefined',
			'ctrl_name' => substr($tableSchema->name, strrpos($tableSchema->name, '_') + 1),
			'cls_name' => substr($tableSchema->name, strrpos($tableSchema->name, '_') + 1),
			'fk_column' => '',
			'act_index_name' => 'index',
			'act_view_name' => 'view',
			'act_create_name' => 'create',
			'act_modify_name' => 'modify',
			'act_remove_name' => 'remove',
			'index_row_btns' => array(
				DataBuilders::INDEX_ROW_BTNS_PENCIL,
				DataBuilders::INDEX_ROW_BTNS_REMOVE,
			),
			'description' => '',
			'author_name' => UserIdentity::getNick(),
			'author_mail' => UserIdentity::getName()
		);

		$mod = Service::getInstance('Builders', $this->_srvName);
		$builderId = $mod->create($params);
		if ($builderId > 0) {
			Log::echoTrace('Import to builders Successfully ...');
		}
		else {
			$errors = $mod->getErrors();
			Log::errExit(__LINE__, 'Import to builders Failed! ' . serialize($errors));
		}

		Log::echoTrace('Import to builder_fields Begin ...');
		$sort = 0;

		foreach ($tableSchema->columns as $columnSchema) {
			$sort++;
			if ($columnSchema->type === 'integer') {
				$columnLength = $columnSchema->size;
			}
			elseif (stripos($columnSchema->dbType, 'enum') !== false) {
				$columnLength = str_replace(array('\'', ','), array('', '|'), substr(substr($columnSchema->dbType, 5), 0, -1));
			}
			elseif (stripos($columnSchema->dbType, 'char') !== false) {
				$columnLength = $columnSchema->size;
			}
			else {
				$columnLength = '';
			}

			if ($columnSchema->isPrimaryKey) {
				$formRequired = DataFields::FORM_REQUIRED_N;
			}
			elseif (stripos($columnSchema->dbType, 'enum') !== false) {
				$formRequired = DataFields::FORM_REQUIRED_N;
			}
			else {
				$formRequired = DataFields::FORM_REQUIRED_Y;
			}

			if ($columnLength === 'y|n') {
				$typeId = 4;
			}
			elseif (stripos($columnSchema->dbType, 'enum') !== false) {
				$typeId = 5;
			}
			elseif ($columnSchema->isPrimaryKey) {
				$typeId = 9;
			}
			elseif ($columnSchema->type === 'integer') {
				$typeId = 2;
			}
			elseif (in_array($columnSchema->dbType, array('text', 'longtext'))) {
				$typeId = 10;
			}
			else {
				$typeId = 1;
			}

			$params = array(
				'field_name' => $columnSchema->name,
				'column_length' => $columnLength,
				'column_auto_increment' => $columnSchema->isAutoIncrement ? DataFields::COLUMN_AUTO_INCREMENT_Y : DataFields::COLUMN_AUTO_INCREMENT_N,
				'column_unsigned' => (stripos($columnSchema->dbType, 'unsigned') !== false) ? DataFields::COLUMN_UNSIGNED_Y : DataFields::COLUMN_UNSIGNED_N,
				'column_comment' => isset($comments[$columnSchema->name]) ? $comments[$columnSchema->name] : '',
				'builder_id' => $builderId,
				'group_id' => 1,
				'type_id' => $typeId,
				'sort' => $sort,
				'html_label' => isset($comments[$columnSchema->name]) ? $comments[$columnSchema->name] : $columnSchema->name,
				'form_prompt' => '',
				'form_required' => $formRequired,
				'form_modifiable' => DataFields::FORM_MODIFIABLE_N,
				'index_show' => DataFields::INDEX_SHOW_Y,
				'index_sort' => $columnSchema->isPrimaryKey ? 1000 : $sort,
				'form_create_show' => $columnSchema->isPrimaryKey ? DataFields::FORM_CREATE_SHOW_N : DataFields::FORM_CREATE_SHOW_Y,
				'form_create_sort' => $sort,
				'form_modify_show' => $columnSchema->isPrimaryKey ? DataFields::FORM_MODIFY_SHOW_N : DataFields::FORM_MODIFY_SHOW_Y,
				'form_modify_sort' => $sort,
				'form_search_show' => DataFields::FORM_SEARCH_SHOW_Y,
				'form_search_sort' => $sort,
			);

			$mod = Service::getInstance('Fields', $this->_srvName);
			$fieldId = $mod->create($params);
			if ($fieldId > 0) {
				Log::echoTrace('Import to builder_fields "' . $columnSchema->name . '" Successfully ...');
			}
			else {
				$errors = $mod->getErrors();
				Log::errExit(__LINE__, 'Import to builder_fields "' . $columnSchema->name . '" Failed! ' . serialize($errors));
			}
		}

		Log::echoTrace('Import to builder_fields Successfully ...');
		Log::echoTrace('Generate End, Table Name "' . $tblName . '"');
		exit;
	}

	/**
	 * 获取“是否已经通过表Metadata生成Builders数据”所有选项
	 * @return array
	 */
	public function getAlreadyGbEnum()
	{
		return array(
			'y' => Text::_('CFG_SYSTEM_GLOBAL_YES'),
			'n' => Text::_('CFG_SYSTEM_GLOBAL_NO')
		);
	}

	/**
	 * 获取DbProxy
	 * @return tfc\saf\DbProxy
	 */
	public function getDbProxy()
	{
		$clusterName = \builders\library\Constant::DB_CLUSTER;
		$className = 'tfc\\saf\\DbProxy::' . $clusterName;
		if (($dbProxy = Singleton::get($className)) === null) {
			$dbProxy = new DbProxy($clusterName);
			Singleton::set($className, $dbProxy);
		}

		return $dbProxy;
	}
}
