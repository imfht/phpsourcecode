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
 * GcViews class file
 * 生成“项目层模板类”
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: GcViews.php 1 2014-01-18 14:19:29Z huan.song $
 * @package modules.builder.model.gc
 * @since 1.0
 */
class GcViews extends AbstractGc
{
	/**
	 * (non-PHPdoc)
	 * @see \modules\builder\model\gc\AbstractGc::_exec()
	 */
	protected function _exec()
	{
		$this->viwIndex();
		$this->viwView();
		$this->viwCreate();
		$this->viwModify();
		$this->viwSidebar();
		$this->viwIndexBtns();

		if ($this->schema->hasTrash) {
			$this->viwTrashIndex();
			$this->viwTrashIndexBtns();
		}
	}

	/**
	 * 创建 Index View
	 * @return void
	 */
	public function viwIndex()
	{
		$fileManager = $this->fileManager;
		$schema = $this->schema;

		$tmpFileName = $schema->ctrlName . '_' . $schema->actIndexName;
		$filePath = $fileManager->view . DS . $tmpFileName . '.php';
		$stream = $fileManager->fopen($filePath);

		fwrite($stream, "<?php\n");
		fwrite($stream, "use views\\bootstrap\\components\\ComponentsConstant;\n");
		fwrite($stream, "use views\\bootstrap\\components\\ComponentsBuilder;\n\n");
		fwrite($stream, "class TableRender extends views\\bootstrap\\components\\TableRender\n");
		fwrite($stream, "{\n");
		fwrite($stream, "\tpublic function get{$schema->fields[1]['func_name']}Link(\$data)\n");
		fwrite($stream, "\t{\n");
		fwrite($stream, "\t\treturn \$this->elements_object->get{$schema->fields[1]['func_name']}Link(\$data);\n");
		fwrite($stream, "\t}\n\n");
		fwrite($stream, "\tpublic function getOperate(\$data)\n");
		fwrite($stream, "\t{\n");
		fwrite($stream, "\t\t\$params = array(\n");
		fwrite($stream, "\t\t\t'id' => \$data['{$schema->pkColumn}'],\n");
		if ($schema->fkColumn) {
			fwrite($stream, "\t\t\t'{$schema->fkColumn}' => \$data['{$schema->fkColumn}']\n");
		}

		fwrite($stream, "\t\t);\n\n");

		$outputs = array();
		if (in_array('pencil', $schema->indexRowBtns)) {
			fwrite($stream, "\t\t\$modifyIcon = \$this->getModifyIcon(\$params);\n");
			$outputs[] = '$modifyIcon';
		}

		if (in_array('trash', $schema->indexRowBtns)) {
			fwrite($stream, "\t\t\$trashIcon = \$this->getTrashIcon(\$params);\n");
			$outputs[] = '$trashIcon';
		}
		elseif (in_array('remove', $schema->indexRowBtns)) {
			fwrite($stream, "\t\t\$removeIcon = \$this->getRemoveIcon(\$params);\n");
			$outputs[] = '$removeIcon';
		}

		$output = implode(' . ', $outputs);
		if ($output === '') {
			$output = "''";
		}

		fwrite($stream, "\n");
		fwrite($stream, "\t\t\$output = {$output};\n");
		fwrite($stream, "\t\treturn \$output;\n");
		fwrite($stream, "\t}\n");
		fwrite($stream, "}\n\n");
		fwrite($stream, "\$tblRender = new TableRender(\$this->elements);\n");
		fwrite($stream, "?>\n\n");

		fwrite($stream, "<?php \$this->display('{$schema->modName}/{$tmpFileName}_btns'); ?>\n\n");
		fwrite($stream, "<?php\n");

		fwrite($stream, "\$this->widget(\n");
		fwrite($stream, "\t'views\\bootstrap\\widgets\\TableBuilder',\n");
		fwrite($stream, "\tarray(\n");
		fwrite($stream, "\t\t'data' => \$this->data,\n");
		fwrite($stream, "\t\t'table_render' => \$tblRender,\n");
		fwrite($stream, "\t\t'elements' => array(\n");
		fwrite($stream, "\t\t\t'{$schema->fields[1]['field_name']}' => array(\n");
		fwrite($stream, "\t\t\t\t'callback' => 'get{$schema->fields[1]['func_name']}Link'\n");
		fwrite($stream, "\t\t\t),\n");
		fwrite($stream, "\t\t),\n");
		fwrite($stream, "\t\t'columns' => array(\n");
		foreach ($schema->listIndexColumns as $columnName) {
			fwrite($stream, "\t\t\t'{$columnName}',\n");
		}

		fwrite($stream, "\t\t\t'_operate_',\n");
		fwrite($stream, "\t\t),\n");
		fwrite($stream, "\t\t'checkedToggle' => '{$schema->pkColumn}',\n");
		fwrite($stream, "\t)\n");
		fwrite($stream, ");\n");
		fwrite($stream, "?>\n\n");
		fwrite($stream, "<?php \$this->display('{$schema->modName}/{$tmpFileName}_btns'); ?>\n\n");
		fwrite($stream, "<?php\n");
		fwrite($stream, "\$this->widget(\n");
		fwrite($stream, "\t'views\\bootstrap\\widgets\\PaginatorBuilder',\n");
		fwrite($stream, "\t\$this->paginator\n");
		fwrite($stream, ");\n");
		fwrite($stream, "?>");

		fclose($stream);
		Log::echoTrace('Generate App View ' . $tmpFileName . ' Successfully');
	}

	/**
	 * 创建 TrashIndex View
	 * @return void
	 */
	public function viwTrashIndex()
	{
		$fileManager = $this->fileManager;
		$schema = $this->schema;

		$tmpFileName = $schema->ctrlName . '_' . strtolower($schema->actTrashIndexName);
		$filePath = $fileManager->view . DS . $tmpFileName . '.php';
		$stream = $fileManager->fopen($filePath);

		fwrite($stream, "<?php\n");
		fwrite($stream, "use views\\bootstrap\\components\\ComponentsConstant;\n");
		fwrite($stream, "use views\\bootstrap\\components\\ComponentsBuilder;\n\n");
		fwrite($stream, "class TableRender extends views\\bootstrap\\components\\TableRender\n");
		fwrite($stream, "{\n");
		fwrite($stream, "\tpublic function getOperate(\$data)\n");
		fwrite($stream, "\t{\n");
		fwrite($stream, "\t\t\$params = array(\n");
		fwrite($stream, "\t\t\t'id' => \$data['{$schema->pkColumn}'],\n");
		if ($schema->fkColumn) {
			fwrite($stream, "\t\t\t'{$schema->fkColumn}' => \$data['{$schema->fkColumn}']\n");
		}

		fwrite($stream, "\t\t);\n\n");

		fwrite($stream, "\t\t\$restoreIcon = \$this->getRestoreIcon(\$params);\n");
		$output = '$restoreIcon';

		if (in_array('remove', $schema->indexRowBtns)) {
			fwrite($stream, "\t\t\$removeIcon = \$this->getRemoveIcon(\$params);\n");
			$output .= ' . $removeIcon';
		}

		fwrite($stream, "\n");
		fwrite($stream, "\t\t\$output = {$output};\n");
		fwrite($stream, "\t\treturn \$output;\n");
		fwrite($stream, "\t}\n");
		fwrite($stream, "}\n\n");
		fwrite($stream, "\$tblRender = new TableRender(\$this->elements);\n");
		fwrite($stream, "?>\n\n");

		fwrite($stream, "<?php \$this->display('{$schema->modName}/{$tmpFileName}_btns'); ?>\n\n");
		fwrite($stream, "<?php\n");

		fwrite($stream, "\$this->widget(\n");
		fwrite($stream, "\t'views\\bootstrap\\widgets\\TableBuilder',\n");
		fwrite($stream, "\tarray(\n");
		fwrite($stream, "\t\t'data' => \$this->data,\n");
		fwrite($stream, "\t\t'table_render' => \$tblRender,\n");
		fwrite($stream, "\t\t'elements' => array(\n");
		fwrite($stream, "\t\t),\n");
		fwrite($stream, "\t\t'columns' => array(\n");
		foreach ($schema->listIndexColumns as $columnName) {
			fwrite($stream, "\t\t\t'{$columnName}',\n");
		}

		fwrite($stream, "\t\t\t'_operate_',\n");
		fwrite($stream, "\t\t),\n");
		fwrite($stream, "\t\t'checkedToggle' => '{$schema->pkColumn}',\n");
		fwrite($stream, "\t)\n");
		fwrite($stream, ");\n");
		fwrite($stream, "?>\n\n");
		fwrite($stream, "<?php \$this->display('{$schema->modName}/{$tmpFileName}_btns'); ?>\n\n");
		fwrite($stream, "<?php\n");
		fwrite($stream, "\$this->widget(\n");
		fwrite($stream, "\t'views\\bootstrap\\widgets\\PaginatorBuilder',\n");
		fwrite($stream, "\t\$this->paginator\n");
		fwrite($stream, ");\n");
		fwrite($stream, "?>");

		fclose($stream);
		Log::echoTrace('Generate App View ' . $tmpFileName . ' Successfully');
	}

	/**
	 * 创建 View View
	 * @return void
	 */
	public function viwView()
	{
		$fileManager = $this->fileManager;
		$schema = $this->schema;

		$tmpFileName = $schema->ctrlName . '_' . $schema->actViewName;
		$filePath = $fileManager->view . DS . $tmpFileName . '.php';
		$stream = $fileManager->fopen($filePath);

		fwrite($stream, "<?php\n");
		fwrite($stream, "\$this->widget('views\\bootstrap\\widgets\\ViewBuilder',\n");
		fwrite($stream, "\tarray(\n");
		fwrite($stream, "\t\t'name' => 'view',\n");
		fwrite($stream, "\t\t'values' => \$this->data,\n");
		fwrite($stream, "\t\t'elements_object' => \$this->elements,\n");
		fwrite($stream, "\t\t'elements' => array(\n");
		fwrite($stream, "\t\t),\n");
		fwrite($stream, "\t\t'columns' => array(\n");
		foreach ($schema->formViewColumns as $columnName) {
			fwrite($stream, "\t\t\t'{$columnName}',\n");
		}

		fwrite($stream, "\t\t\t'_button_history_back_'\n");
		fwrite($stream, "\t\t)\n");
		fwrite($stream, "\t)\n");
		fwrite($stream, ");\n");
		fwrite($stream, "?>");

		fclose($stream);

		Log::echoTrace('Generate App View ' .$tmpFileName . ' Successfully');
	}

	/**
	 * 创建 Create View
	 * @return void
	 */
	public function viwCreate()
	{
		$fileManager = $this->fileManager;
		$schema = $this->schema;

		$tmpFileName = $schema->ctrlName . '_' . $schema->actCreateName;
		$filePath = $fileManager->view . DS . $tmpFileName . '.php';
		$stream = $fileManager->fopen($filePath);

		fwrite($stream, "<?php\n");
		fwrite($stream, "\$this->widget('views\\bootstrap\\widgets\\FormBuilder',\n");
		fwrite($stream, "\tarray(\n");
		fwrite($stream, "\t\t'name' => 'create',\n");
		fwrite($stream, "\t\t'action' => \$this->getUrlManager()->getUrl(\$this->action),\n");
		fwrite($stream, "\t\t'errors' => \$this->errors,\n");
		fwrite($stream, "\t\t'elements_object' => \$this->elements,\n");
		fwrite($stream, "\t\t'elements' => array(\n");
		if ($schema->fkColumn) {
			fwrite($stream, "\t\t\t'{$schema->fkColumn}' => array(\n");
			fwrite($stream, "\t\t\t\t'value' => \$this->{$schema->fkColumn}\n");
			fwrite($stream, "\t\t\t),\n");
		}

		fwrite($stream, "\t\t),\n");
		fwrite($stream, "\t\t'columns' => array(\n");
		foreach ($schema->formCreateColumns as $columnName) {
			fwrite($stream, "\t\t\t'{$columnName}',\n");
		}

		fwrite($stream, "\t\t\t'_button_save_',\n");
		fwrite($stream, "\t\t\t'_button_saveclose_',\n");
		fwrite($stream, "\t\t\t'_button_savenew_',\n");
		fwrite($stream, "\t\t\t'_button_cancel_'\n");
		fwrite($stream, "\t\t)\n");
		fwrite($stream, "\t)\n");
		fwrite($stream, ");\n");
		fwrite($stream, "?>");

		fclose($stream);

		Log::echoTrace('Generate App View ' .$tmpFileName . ' Successfully');
	}

	/**
	 * 创建 Modify View
	 * @return void
	 */
	public function viwModify()
	{
		$fileManager = $this->fileManager;
		$schema = $this->schema;

		$tmpFileName = $schema->ctrlName . '_' . $schema->actModifyName;
		$filePath = $fileManager->view . DS . $tmpFileName . '.php';
		$stream = $fileManager->fopen($filePath);

		fwrite($stream, "<?php\n");
		fwrite($stream, "\$this->widget('views\\bootstrap\\widgets\\FormBuilder',\n");
		fwrite($stream, "\tarray(\n");
		fwrite($stream, "\t\t'name' => 'modify',\n");
		fwrite($stream, "\t\t'action' => \$this->getUrlManager()->getUrl(\$this->action, '', '', array('id' => \$this->id)),\n");
		fwrite($stream, "\t\t'errors' => \$this->errors,\n");
		fwrite($stream, "\t\t'values' => \$this->data,\n");
		fwrite($stream, "\t\t'elements_object' => \$this->elements,\n");
		fwrite($stream, "\t\t'elements' => array(\n");
		fwrite($stream, "\t\t),\n");
		fwrite($stream, "\t\t'columns' => array(\n");
		foreach ($schema->formModifyColumns as $columnName) {
			fwrite($stream, "\t\t\t'{$columnName}',\n");
		}

		fwrite($stream, "\t\t\t'_button_save_',\n");
		fwrite($stream, "\t\t\t'_button_saveclose_',\n");
		fwrite($stream, "\t\t\t'_button_savenew_',\n");
		fwrite($stream, "\t\t\t'_button_cancel_'\n");
		fwrite($stream, "\t\t)\n");
		fwrite($stream, "\t)\n");
		fwrite($stream, ");\n");
		fwrite($stream, "?>");

		fclose($stream);
		Log::echoTrace('Generate App View ' .$tmpFileName . ' Successfully');
	}

	/**
	 * 创建 Sidebar View
	 * @return void
	 */
	public function viwSidebar()
	{
		$fileManager = $this->fileManager;
		$schema = $this->schema;

		$tmpFileName = $schema->ctrlName . '_sidebar';
		$filePath = $fileManager->view . DS . $tmpFileName . '.php';
		$stream = $fileManager->fopen($filePath);

		fwrite($stream, "<!-- SideBar -->\n");
		fwrite($stream, "<div class=\"col-xs-6 col-sm-2 sidebar-offcanvas\" id=\"sidebar\">\n");
		fwrite($stream, "<?php\n");
		fwrite($stream, "\$config = array(\n");
		fwrite($stream, ");\n");
		fwrite($stream, "\$this->widget('views\\bootstrap\\components\\bar\\SideBar', array('config' => \$config));\n");
		fwrite($stream, "?>\n");
		fwrite($stream, "</div><!-- /.col-xs-6 col-sm-2 -->\n");
		fwrite($stream, "<!-- /SideBar -->\n\n");

		fwrite($stream, "<?php echo \$this->getHtml()->jsFile(\$this->js_url . '/mods/{$schema->modName}.js?v=' . \$this->version); ?>\n");

		fclose($stream);
		Log::echoTrace('Generate App View ' .$tmpFileName . ' Successfully');
	}

	/**
	 * 创建 Index Btns View
	 * @return void
	 */
	public function viwIndexBtns()
	{
		$fileManager = $this->fileManager;
		$schema = $this->schema;

		$upCtrlName = strtoupper($schema->ctrlName);
		$upModName = strtoupper($schema->modName);

		$tmpFileName = $schema->ctrlName . '_' . $schema->actIndexName . '_btns';
		$filePath = $fileManager->view . DS . $tmpFileName . '.php';
		$stream = $fileManager->fopen($filePath);
		fwrite($stream, "<form class=\"form-inline\">\n");
		fwrite($stream, "<?php\n");
		fwrite($stream, "\$this->widget(\n");
		fwrite($stream, "\t'views\\bootstrap\\widgets\\ButtonBuilder',\n");
		fwrite($stream, "\tarray(\n");
		fwrite($stream, "\t\t'label' => \$this->MOD_{$upModName}_URLS_{$upCtrlName}_CREATE,\n");
		fwrite($stream, "\t\t'jsfunc' => \\views\\bootstrap\\components\\ComponentsConstant::JSFUNC_HREF,\n");
		if ($schema->fkColumn) {
			fwrite($stream, "\t\t'url' => \$this->getUrlManager()->getUrl('{$schema->actCreateName}', '', '', array('{$schema->fkColumn}' => \$this->{$schema->fkColumn})),\n");
		}
		else {
			fwrite($stream, "\t\t'url' => \$this->getUrlManager()->getUrl('{$schema->actCreateName}', '', ''),\n");
		}

		fwrite($stream, "\t\t'glyphicon' => \\views\\bootstrap\\components\\ComponentsConstant::GLYPHICON_CREATE,\n");
		fwrite($stream, "\t\t'primary' => true,\n");
		fwrite($stream, "\t)\n");
		fwrite($stream, ");\n");
		fwrite($stream, "?>\n");
		fwrite($stream, "</form>");
		fclose($stream);

		Log::echoTrace('Generate App View ' .$tmpFileName . ' Successfully');
	}

	/**
	 * 创建 TrashIndex Btns View
	 * @return void
	 */
	public function viwTrashIndexBtns()
	{
		$fileManager = $this->fileManager;
		$schema = $this->schema;

		$tmpFileName = $schema->ctrlName . '_' . strtolower($schema->actTrashIndexName) . '_btns';
		$filePath = $fileManager->view . DS . $tmpFileName . '.php';
		$stream = $fileManager->fopen($filePath);
		fclose($stream);

		Log::echoTrace('Generate App View ' .$tmpFileName . ' Successfully');
	}
}
