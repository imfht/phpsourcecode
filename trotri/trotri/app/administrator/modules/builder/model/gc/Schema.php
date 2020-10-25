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
use libsrv\Service;

/**
 * Schema class file
 * Builders、Types、Groups、Fields、Validators数据寄存器
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: Schema.php 1 2014-01-18 14:19:29Z huan.song $
 * @package modules.builder.model.gc
 * @since 1.0
 */
class Schema
{
	const SRV_NAME = 'builders';

	public
		$builderId     = 0,
		$builderName   = '',
		$tblName       = '',
		$tblProfile    = '',
		$tblEngine     = '',
		$tblCharset    = '',
		$tblComment    = '',
		$srvType       = '',
		$srvName       = '',
		$appName       = '',
		$modName       = '',
		$clsName       = '',
		$ctrlName      = '',
		$fkColumn      = '',
		$actIndexName  = '',
		$actViewName   = '',
		$actCreateName = '',
		$actModifyName = '',
		$actRemoveName = '',
		$indexRowBtns  = array(),
		$description   = '',
		$authorName    = '',
		$authorMail    = '';

	public
		$ucCtrlName        = '',
		$ucClsName         = '',
		$upTblName         = '',
		$langPrev          = '',
		$actSingleModify   = 'singlemodify',
		$actTrashIndexName = '',
		$actTrashName      = '',
		$hasTrash          = false,
		$hasSort           = false,
		$pkColumn          = '', // 将自增类型当主键
		$pkVarColumn       = '',
		$fkFuncName        = '',
		$fkVarName         = '';

	public
		$types             = array(),  // 表单字段类型
		$groups            = array(),  // 表单字段组数据
		$fields            = array(),  // 表单字段数据
		$listIndexColumns  = array(),  // 列表模板字段顺序
		$formViewColumns   = array(),  // 详情表单字段顺序
		$formCreateColumns = array(),  // 新增表单字段顺序
		$formModifyColumns = array();  // 编辑表单字段顺序

	/**
	 * 构造方法：初始化所有的全局变量
	 * @param integer $builderId
	 */
	public function __construct($builderId)
	{
		if (($this->builderId = (int) $builderId) <= 0) {
			Log::errExit(__LINE__, 'builder_id must be a integer.');
		}

		// 初始化工作开始
		Log::echoTrace('Initialization Schema Begin ...');

		$this->_initBuilders()->_initTypes()->_initGroups()->_initFields()->_initValidators()->_initViewColumns();

		if ($this->fkColumn) {
			$this->fkFuncName    = $this->column2Name($this->fkColumn);
			$this->fkVarName     = '$' . strtolower(substr($this->fkFuncName, 0, 1)) . substr($this->fkFuncName, 1);
			$this->fkFuncName    = 'get' . $this->fkFuncName;
		}

		if ($this->hasTrash) {
			$this->actTrashIndexName = 'trash' . strtolower($this->actIndexName);
			$this->actTrashName = 'trash';
		}

		// 初始化工作结束
		Log::echoTrace('Initialization Schema End');
	}

	/**
	 * 初始化生成代码数据
	 * @return instance of modules\builder\model\gc\Schema
	 */
	protected function _initBuilders()
	{
		$object = Service::getInstance('Builders', self::SRV_NAME);
		$tableName = $object->getTableName();

		Log::echoTrace('Query from ' . $tableName . ' Begin ...');

		$data = $object->findByPk($this->builderId);
		if (!$data) {
			Log::errExit(__LINE__, 'Query from ' . $tableName . ' Failed!');
		}

		$this->builderId     = (int) $data['builder_id'];
		$this->builderName   = trim($data['builder_name']);
		$this->tblName       = strtolower(trim($data['tbl_name']));
		$this->tblProfile    = trim($data['tbl_profile']);
		$this->tblEngine     = trim($data['tbl_engine']);
		$this->tblCharset    = trim($data['tbl_charset']);
		$this->tblComment    = trim($data['tbl_comment']);
		$this->srvType       = strtolower(trim($data['srv_type']));
		$this->srvName       = strtolower(trim($data['srv_name']));
		$this->appName       = strtolower(trim($data['app_name']));
		$this->modName       = strtolower(trim($data['mod_name']));
		$this->clsName       = strtolower(trim($data['cls_name']));
		$this->ctrlName      = strtolower(trim($data['ctrl_name']));
		$this->fkColumn      = strtolower(trim($data['fk_column']));
		$this->actIndexName  = strtolower(trim($data['act_index_name']));
		$this->actViewName   = strtolower(trim($data['act_view_name']));
		$this->actCreateName = strtolower(trim($data['act_create_name']));
		$this->actModifyName = strtolower(trim($data['act_modify_name']));
		$this->actRemoveName = strtolower(trim($data['act_remove_name']));
		$this->indexRowBtns  = (array) $data['index_row_btns'];
		$this->description   = $data['description'];
		$this->authorName    = trim($data['author_name']);
		$this->authorMail    = trim($data['author_mail']);

		$this->ucClsName     = ucfirst($this->clsName);
		$this->ucCtrlName    = ucfirst($this->ctrlName);
		$this->upTblName     = strtoupper($this->tblName);
		$this->langPrev      = strtoupper('MOD_' . $this->modName . '_' . $this->tblName);

		Log::echoTrace('Query from ' . $tableName . ' Successfully');
		return $this;
	}

	/**
	 * 初始化表单字段类型
	 * @return instance of modules\builder\model\gc\Schema
	 */
	protected function _initTypes()
	{
		$object = Service::getInstance('Types', self::SRV_NAME);
		$tableName = $object->getTableName();

		Log::echoTrace('Query from ' . $tableName . ' Begin ...');

		$data = $object->findAllByAttributes(array(), 'sort', 0, 1000);
		if (!$data) {
			Log::errExit(__LINE__, 'Query from ' . $tableName . ' Failed!');
		}

		foreach ($data as $rows) {
			$typeId = (int) $rows['type_id'];
			$this->types[$typeId] = array(
				'type_id'    => $typeId,
				'type_name'  => trim($rows['type_name']),
				'form_type'  => strtolower(trim($rows['form_type'])),
				'field_type' => strtoupper(trim($rows['field_type'])),
				'category'   => strtolower(trim($rows['category'])),
			);
		}

		Log::echoTrace('Query from ' . $tableName . ' Successfully');
		return $this;
	}

	/**
	 * 初始化表单字段组数据
	 * @return instance of modules\builder\model\gc\Schema
	 */
	protected function _initGroups()
	{
		$object = Service::getInstance('Groups', self::SRV_NAME);
		$tableName = $object->getTableName();

		Log::echoTrace('Query from ' . $tableName . ' Begin ...');

		$defaults = $object->findAllByAttributes(array('builder_id' => 0), 'sort', 0, 1000);
		$data = $object->findAllByAttributes(array('builder_id' => $this->builderId), 'sort', 0, 1000);
		if ($defaults === false || $data === false) {
			Log::errExit(__LINE__, 'Query from ' . $tableName . ' Failed!');
		}

		$data = array_merge($defaults, $data);
		foreach ($data as $rows) {
			$groupId = (int) $rows['group_id'];
			$this->groups[$groupId] = array(
				'group_id'   => $groupId,
				'group_name' => trim($rows['group_name']),
				'prompt'     => trim($rows['prompt']),
				'is_default' => ((int) $rows['builder_id'] > 0) ? false : true,
				'lang_key' => $this->langPrev . '_VIEWTAB_' . strtoupper($rows['group_name']) . '_PROMPT'
			);
		}

		Log::echoTrace('Query from ' . $tableName . ' Successfully');
		return $this;
	}

	/**
	 * 初始化表单字段数据
	 * @return instance of modules\builder\model\gc\Schema
	 */
	protected function _initFields()
	{
		$object = Service::getInstance('Fields', self::SRV_NAME);
		$tableName = $object->getTableName();

		Log::echoTrace('Query from ' . $tableName . ' Begin ...');

		$data = $object->findAllByAttributes(array('builder_id' => $this->builderId), 'sort', 0, 1000);
		if ($data === false) {
			Log::errExit(__LINE__, 'Query from ' . $tableName . ' Failed!');
		}

		foreach ($data as $rows) {
			$groupId = (int) $rows['group_id'];
			$typeId  = (int) $rows['type_id'];

			if (!isset($this->groups[$groupId])) {
				Log::errExit(__LINE__, 'Fields group_id "' . $groupId . '" Not Exists!');
			}

			if (!isset($this->types[$typeId])) {
				Log::errExit(__LINE__, 'Fields type_id "' . $typeId . '" Not Exists!');
			}

			$temp = array();

			$temp['field_id']              = (int) $rows['field_id'];
			$temp['field_name']            = strtolower(trim($rows['field_name']));
			$temp['column_length']         = trim($rows['column_length']);
			$temp['column_auto_increment'] = ($rows['column_auto_increment'] === 'y' ? true : false);
			$temp['column_unsigned']       = ($rows['column_unsigned'] === 'y' ? true : false);
			$temp['column_comment']        = trim($rows['column_comment']);
			$temp['builder_id']            = (int) $rows['builder_id'];
			$temp['group_id']              = $groupId;
			$temp['type_id']               = $typeId;
			$temp['sort']                  = (int) $rows['sort'];
			$temp['html_label']            = trim($rows['html_label']);
			$temp['form_prompt']           = trim($rows['form_prompt']);
			$temp['form_required']         = ($rows['form_required'] === 'y' ? true : false);
			$temp['form_modifiable']       = ($rows['form_modifiable'] === 'y' ? true : false);
			$temp['index_show']            = ($rows['index_show'] === 'y' ? true : false);
			$temp['index_sort']            = (int) $rows['index_sort'];
			$temp['form_create_show']      = ($rows['form_create_show'] === 'y' ? true : false);
			$temp['form_create_sort']      = (int) $rows['form_create_sort'];
			$temp['form_modify_show']      = ($rows['form_modify_show'] === 'y' ? true : false);
			$temp['form_modify_sort']      = (int) $rows['form_modify_sort'];
			$temp['form_search_show']      = ($rows['form_search_show'] === 'y' ? true : false);
			$temp['form_search_sort']      = (int) $rows['form_search_sort'];

			$temp['func_name']             = $this->column2Name(strtolower($temp['field_name']));
			$temp['var_name']              = '$' . strtolower(substr($temp['func_name'], 0, 1)) . substr($temp['func_name'], 1);
			$temp['up_field_name']         = strtoupper($temp['field_name']);
			$temp['lang_label']            = $this->langPrev . '_' . $temp['up_field_name'] . '_LABEL';
			$temp['lang_hint']             = $this->langPrev . '_' . $temp['up_field_name'] . '_HINT';

			$temp['__tid__']               = $this->groups[$groupId]['group_name'];
			$temp['form_type']             = $this->types[$typeId]['form_type'];
			$temp['type_category']         = $this->types[$typeId]['category'];
			$temp['field_type']            = $this->types[$typeId]['field_type'];

			if ($temp['field_type'] === 'ENUM') {
				$enums = array();
				foreach (explode('|', $temp['column_length']) as $value) {
					$constKey = $temp['up_field_name'] . '_' . strtoupper($value);
					switch ($value) {
						case 'y' :
							$langKey = 'SRV_ENUM_GLOBAL_YES';
							break;
						case 'n' :
							$langKey = 'SRV_ENUM_GLOBAL_NO';
							break;
						default :
							$langKey = 'SRV_ENUM_' . $this->upTblName . '_' . $constKey;
					}

					$enums[] = array(
						'const_key' => $constKey,
						'lang_key'  => $langKey,
						'value'     => $value
					);
				}

				$temp['enums'] = $enums;
			}

			if ($temp['field_name'] === 'trash') {
				$this->hasTrash = true;
			}

			if ($temp['field_name'] === 'sort') {
				$this->hasSort = true;
			}

			if ($temp['column_auto_increment']) {
				$this->pkColumn = $temp['field_name'];
				$this->pkVarColumn = ($pkVarColumn = $this->column2Name($this->pkColumn)) ? '$' . strtolower(substr($pkVarColumn, 0, 1)) . substr($pkVarColumn, 1) : '';
			}

			$this->fields[] = $temp;
		}

		Log::echoTrace('Query from ' . $tableName . ' Successfully');
		return $this;
	}

	/**
	 * 初始化表单字段数据
	 * @return instance of modules\builder\model\gc\Schema
	 */
	protected function _initValidators()
	{
		$object = Service::getInstance('Validators', self::SRV_NAME);
		$tableName = $object->getTableName();

		Log::echoTrace('Query from ' . $tableName . ' Begin ...');

		foreach ($this->fields as $key => $fields) {
			$data = $object->findAllByAttributes(array('field_id' => $fields['field_id']), 'sort', 0, 1000);
			if ($data === false) {
				Log::errExit(__LINE__, 'Query from ' . $tableName . ' Failed!');
			}

			$temp = array();
			foreach ($data as $rows) {
				$validatorId = (int) $rows['validator_id'];
				$validatorName = trim($rows['validator_name']);
				$temp[$validatorId] = array(
					'validator_id'    => $validatorId,
					'validator_name'  => $validatorName,
					'options'         => $rows['options'],
					'option_category' => strtolower(trim($rows['option_category'])),
					'message'         => trim($rows['message']),
					'when'            => strtolower(trim($rows['when'])),
					'lang_key'        => 'SRV_FILTER_' . $this->upTblName . '_' . $fields['up_field_name'] . '_' . strtoupper($validatorName)
				);
			}

			$this->fields[$key]['validators'] = $temp;
		}

		Log::echoTrace('Query from ' . $tableName . ' Successfully');
		return $this;
	}

	/**
	 * 初始化模板字段顺序
	 * @return instance of modules\builder\model\gc\Schema
	 */
	protected function _initViewColumns()
	{
		Log::echoTrace('Init View Columns Begin ...');

		$tmpListIndexShows = array();
		$tmpFormCreateShows = array();
		$tmpFormModifyShows = array();
		foreach ($this->fields as $rows) {
			if ($rows['index_show']) {
				$tmpListIndexShows[$rows['index_sort']][] = $rows['field_name'];
			}

			if ($rows['form_create_show']) {
				$tmpFormCreateShows[$rows['form_create_sort']][] = $rows['field_name'];
			}

			if ($rows['form_modify_show']) {
				$tmpFormModifyShows[$rows['form_modify_sort']][] = $rows['field_name'];
			}
		}

		ksort($tmpListIndexShows);
		ksort($tmpFormCreateShows);
		ksort($tmpFormModifyShows);

		$listIndexShows = array();
		$formViewShows = array();
		$formCreateShows = array();
		$formModifyShows = array();
		foreach ($tmpListIndexShows as $columnNames) {
			foreach ($columnNames as $columnName) {
				$listIndexShows[] = $columnName;
			}
		}

		foreach ($this->fields as $rows) {
			$formViewShows[] = $rows['field_name'];
		}

		foreach ($tmpFormCreateShows as $columnNames) {
			foreach ($columnNames as $columnName) {
				$formCreateShows[] = $columnName;
			}
		}

		foreach ($tmpFormModifyShows as $columnNames) {
			foreach ($columnNames as $columnName) {
				$formModifyShows[] = $columnName;
			}
		}

		$this->listIndexColumns = $listIndexShows;
		$this->formViewColumns = $formViewShows;
		$this->formCreateColumns = $formCreateShows;
		$this->formModifyColumns = $formModifyShows;

		Log::echoTrace('Init View Columns End');
	}

	/**
	 * 将字段名格式转换为函数名格式
	 * @param string $name
	 * @return string
	 */
	public function column2Name($name)
	{
		return str_replace(' ', '', ucwords(str_replace('_', ' ', strtolower($name))));
	}
}
