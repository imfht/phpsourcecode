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
 * GcActs class file
 * 生成“项目层Action方法类”
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: GcActs.php 1 2014-01-18 14:19:29Z huan.song $
 * @package modules.builder.model.gc
 * @since 1.0
 */
class GcActs extends AbstractGc
{
	/**
	 * (non-PHPdoc)
	 * @see \modules\builder\model\gc\AbstractGc::_exec()
	 */
	protected function _exec()
	{
		$this->actIndex();
		$this->actView();
		$this->actCreate();
		$this->actModify();
		$this->actRemove();
		$this->actSingleModify();

		if ($this->schema->hasTrash) {
			$this->actTrashIndex();
			$this->actTrash();
		}
	}

	/**
	 * 创建 Index Action
	 * @return void
	 */
	public function actIndex()
	{
		$fileManager = $this->fileManager;
		$schema = $this->schema;

		$clsName = ucfirst($schema->actIndexName);
		$filePath = $fileManager->action . DS . $clsName . '.php';
		$stream = $fileManager->fopen($filePath);
		$fileManager->writeCopyrightComment($stream);
		fwrite($stream, "namespace modules\\{$schema->modName}\\action\\{$schema->ctrlName};\n\n");
		fwrite($stream, "use library\\actions;\n");
		fwrite($stream, "use tfc\\ap\\Ap;\n\n");

		$fileManager->writeClassComment($stream, $clsName, '查询数据列表', "modules.{$schema->modName}.action.{$schema->ctrlName}");
		fwrite($stream, "class {$clsName} extends actions\\Index\n");
		fwrite($stream, "{\n");
		fwrite($stream, "\t/**\n");
		fwrite($stream, "\t * (non-PHPdoc)\n");
		fwrite($stream, "\t * @see \\tfc\\mvc\\interfaces\\Action::run()\n");
		fwrite($stream, "\t */\n");
		fwrite($stream, "\tpublic function run()\n");
		fwrite($stream, "\t{\n");
		if ($schema->fkColumn) {
			fwrite($stream, "\t\t{$schema->fkVarName} = Ap::getRequest()->getInteger('{$schema->fkColumn}');\n");
			fwrite($stream, "\t\tif ({$schema->fkVarName} <= 0) {\n");
			fwrite($stream, "\t\t\t\$this->err404();\n");
			fwrite($stream, "\t\t}\n\n");
			fwrite($stream, "\t\t\$this->assign('{$schema->fkColumn}', {$schema->fkVarName});\n\n");
		}

		if ($schema->hasTrash) {
			fwrite($stream, "\t\tAp::getRequest()->setParam('trash', 'n');\n");
		}

		if ($schema->hasSort) {
			fwrite($stream, "\t\tAp::getRequest()->setParam('order', 'sort');\n");
		}

		fwrite($stream, "\t\t\$this->execute('{$schema->ucClsName}');\n");
		fwrite($stream, "\t}\n");
		fwrite($stream, "}\n");
		fclose($stream);

		Log::echoTrace('Generate App Act ' . $clsName . ' Successfully');
	}

	/**
	 * 创建 TrashIndex Action
	 * @return void
	 */
	public function actTrashIndex()
	{
		$fileManager = $this->fileManager;
		$schema = $this->schema;

		$clsName = ucfirst($schema->actTrashIndexName);
		$filePath = $fileManager->action . DS . $clsName . '.php';
		$stream = $fileManager->fopen($filePath);
		$fileManager->writeCopyrightComment($stream);
		fwrite($stream, "namespace modules\\{$schema->modName}\\action\\{$schema->ctrlName};\n\n");
		fwrite($stream, "use library\\actions;\n");
		fwrite($stream, "use tfc\\ap\\Ap;\n\n");

		$fileManager->writeClassComment($stream, $clsName, '查询回收站数据列表', "modules.{$schema->modName}.action.{$schema->ctrlName}");
		fwrite($stream, "class {$clsName} extends actions\\Index\n");
		fwrite($stream, "{\n");
		fwrite($stream, "\t/**\n");
		fwrite($stream, "\t * (non-PHPdoc)\n");
		fwrite($stream, "\t * @see \\tfc\\mvc\\interfaces\\Action::run()\n");
		fwrite($stream, "\t */\n");
		fwrite($stream, "\tpublic function run()\n");
		fwrite($stream, "\t{\n");
		if ($schema->fkColumn) {
			fwrite($stream, "\t\t{$schema->fkVarName} = Ap::getRequest()->getInteger('{$schema->fkColumn}');\n");
			fwrite($stream, "\t\tif ({$schema->fkVarName} <= 0) {\n");
			fwrite($stream, "\t\t\t\$this->err404();\n");
			fwrite($stream, "\t\t}\n\n");
			fwrite($stream, "\t\t\$this->assign('{$schema->fkColumn}', {$schema->fkVarName});\n\n");
		}

		fwrite($stream, "\t\tAp::getRequest()->setParam('trash', 'y');\n");
		if ($schema->hasSort) {
			fwrite($stream, "\t\tAp::getRequest()->setParam('order', 'sort');\n");
		}

		fwrite($stream, "\t\t\$this->execute('{$schema->ucClsName}');\n");
		fwrite($stream, "\t}\n");
		fwrite($stream, "}\n");
		fclose($stream);

		Log::echoTrace('Generate App Act ' . $clsName . ' Successfully');
	}

	/**
	 * 创建 View Action
	 * @return void
	 */
	public function actView()
	{
		$fileManager = $this->fileManager;
		$schema = $this->schema;

		$clsName = ucfirst($schema->actViewName);
		$filePath = $fileManager->action . DS . $clsName . '.php';
		$stream = $fileManager->fopen($filePath);
		$fileManager->writeCopyrightComment($stream);
		fwrite($stream, "namespace modules\\{$schema->modName}\\action\\{$schema->ctrlName};\n\n");
		fwrite($stream, "use library\\actions;\n");
		fwrite($stream, "use tfc\\ap\\Ap;\n");
		if ($schema->fkColumn) {
			fwrite($stream, "use libapp\\Model;\n");
		}

		fwrite($stream, "\n");

		$fileManager->writeClassComment($stream, $clsName, '查询数据详情', "modules.{$schema->modName}.action.{$schema->ctrlName}");
		fwrite($stream, "class {$clsName} extends actions\\View\n");
		fwrite($stream, "{\n");
		fwrite($stream, "\t/**\n");
		fwrite($stream, "\t * (non-PHPdoc)\n");
		fwrite($stream, "\t * @see \\tfc\\mvc\\interfaces\\Action::run()\n");
		fwrite($stream, "\t */\n");
		fwrite($stream, "\tpublic function run()\n");
		fwrite($stream, "\t{\n");
		if ($schema->fkColumn) {
			fwrite($stream, "\t\t\$mod = Model::getInstance('{$schema->ucClsName}');\n");
			fwrite($stream, "\t\t{$schema->fkVarName} = \$mod->{$schema->fkFuncName}();\n");
			fwrite($stream, "\t\tif ({$schema->fkVarName} <= 0) {\n");
			fwrite($stream, "\t\t\t\$this->err404();\n");
			fwrite($stream, "\t\t}\n\n");
			fwrite($stream, "\t\t\$this->assign('{$schema->fkColumn}', {$schema->fkVarName});\n");
		}

		fwrite($stream, "\t\t\$this->execute('{$schema->ucClsName}');\n");
		fwrite($stream, "\t}\n");
		fwrite($stream, "}\n");
		fclose($stream);

		Log::echoTrace('Generate App Act ' .$clsName . ' Successfully');
	}

	/**
	 * 创建 Create Action
	 * @return void
	 */
	public function actCreate()
	{
		$fileManager = $this->fileManager;
		$schema = $this->schema;

		$clsName = ucfirst($schema->actCreateName);
		$filePath = $fileManager->action . DS . $clsName . '.php';
		$stream = $fileManager->fopen($filePath);
		$fileManager->writeCopyrightComment($stream);
		fwrite($stream, "namespace modules\\{$schema->modName}\\action\\{$schema->ctrlName};\n\n");
		fwrite($stream, "use library\\actions;\n");
		fwrite($stream, "use tfc\\ap\\Ap;\n");
		if ($schema->fkColumn) {
			fwrite($stream, "use libapp\\Model;\n");
		}

		fwrite($stream, "\n");

		$fileManager->writeClassComment($stream, $clsName, '新增数据', "modules.{$schema->modName}.action.{$schema->ctrlName}");
		fwrite($stream, "class {$clsName} extends actions\\Create\n");
		fwrite($stream, "{\n");
		fwrite($stream, "\t/**\n");
		fwrite($stream, "\t * (non-PHPdoc)\n");
		fwrite($stream, "\t * @see \\tfc\\mvc\\interfaces\\Action::run()\n");
		fwrite($stream, "\t */\n");
		fwrite($stream, "\tpublic function run()\n");
		fwrite($stream, "\t{\n");
		if ($schema->fkColumn) {
			fwrite($stream, "\t\t\$mod = Model::getInstance('{$schema->ucClsName}');\n");
			fwrite($stream, "\t\t{$schema->fkVarName} = \$mod->{$schema->fkFuncName}();\n");
			fwrite($stream, "\t\tif ({$schema->fkVarName} <= 0) {\n");
			fwrite($stream, "\t\t\t\$this->err404();\n");
			fwrite($stream, "\t\t}\n\n");
			fwrite($stream, "\t\t\$this->assign('{$schema->fkColumn}', {$schema->fkVarName});\n");
		}

		fwrite($stream, "\t\t\$this->execute('{$schema->ucClsName}');\n");
		fwrite($stream, "\t}\n");
		fwrite($stream, "}\n");
		fclose($stream);

		Log::echoTrace('Generate App Act ' .$clsName . ' Successfully');
	}

	/**
	 * 创建 Modify Action
	 * @return void
	 */
	public function actModify()
	{
		$fileManager = $this->fileManager;
		$schema = $this->schema;

		$clsName = ucfirst($schema->actModifyName);
		$filePath = $fileManager->action . DS . $clsName . '.php';
		$stream = $fileManager->fopen($filePath);
		$fileManager->writeCopyrightComment($stream);
		fwrite($stream, "namespace modules\\{$schema->modName}\\action\\{$schema->ctrlName};\n\n");
		fwrite($stream, "use library\\actions;\n");
		fwrite($stream, "use tfc\\ap\\Ap;\n");
		if ($schema->fkColumn) {
			fwrite($stream, "use libapp\\Model;\n");
		}

		fwrite($stream, "\n");

		$fileManager->writeClassComment($stream, $clsName, '编辑数据', "modules.{$schema->modName}.action.{$schema->ctrlName}");
		fwrite($stream, "class {$clsName} extends actions\\Modify\n");
		fwrite($stream, "{\n");
		fwrite($stream, "\t/**\n");
		fwrite($stream, "\t * (non-PHPdoc)\n");
		fwrite($stream, "\t * @see \\tfc\\mvc\\interfaces\\Action::run()\n");
		fwrite($stream, "\t */\n");
		fwrite($stream, "\tpublic function run()\n");
		fwrite($stream, "\t{\n");
		if ($schema->fkColumn) {
			fwrite($stream, "\t\t\$mod = Model::getInstance('{$schema->ucClsName}');\n");
			fwrite($stream, "\t\t{$schema->fkVarName} = \$mod->{$schema->fkFuncName}();\n");
			fwrite($stream, "\t\tif ({$schema->fkVarName} <= 0) {\n");
			fwrite($stream, "\t\t\t\$this->err404();\n");
			fwrite($stream, "\t\t}\n\n");
			fwrite($stream, "\t\t\$this->assign('{$schema->fkColumn}', {$schema->fkVarName});\n");
		}

		fwrite($stream, "\t\t\$this->execute('{$schema->ucClsName}');\n");
		fwrite($stream, "\t}\n");
		fwrite($stream, "}\n");
		fclose($stream);

		Log::echoTrace('Generate App Act ' .$clsName . ' Successfully');
	}

	/**
	 * 创建 Remove Action
	 * @return void
	 */
	public function actRemove()
	{
		$fileManager = $this->fileManager;
		$schema = $this->schema;

		$clsName = ucfirst($schema->actRemoveName);
		$filePath = $fileManager->action . DS . $clsName . '.php';
		$stream = $fileManager->fopen($filePath);
		$fileManager->writeCopyrightComment($stream);
		fwrite($stream, "namespace modules\\{$schema->modName}\\action\\{$schema->ctrlName};\n\n");
		fwrite($stream, "use library\\actions;\n\n");

		$fileManager->writeClassComment($stream, $clsName, '删除数据', "modules.{$schema->modName}.action.{$schema->ctrlName}");
		fwrite($stream, "class {$clsName} extends actions\\Remove\n");
		fwrite($stream, "{\n");
		fwrite($stream, "\t/**\n");
		fwrite($stream, "\t * (non-PHPdoc)\n");
		fwrite($stream, "\t * @see \\tfc\\mvc\\interfaces\\Action::run()\n");
		fwrite($stream, "\t */\n");
		fwrite($stream, "\tpublic function run()\n");
		fwrite($stream, "\t{\n");
		fwrite($stream, "\t\t\$this->execute('{$schema->ucClsName}');\n");
		fwrite($stream, "\t}\n");
		fwrite($stream, "}\n");
		fclose($stream);

		Log::echoTrace('Generate App Act ' .$clsName . ' Successfully');
	}

	/**
	 * 创建 SingleModify Action
	 * @return void
	 */
	public function actSingleModify()
	{
		$fileManager = $this->fileManager;
		$schema = $this->schema;

		$clsName = 'Single' . $schema->actModifyName;
		$filePath = $fileManager->action . DS . $clsName . '.php';
		$stream = $fileManager->fopen($filePath);
		$fileManager->writeCopyrightComment($stream);
		fwrite($stream, "namespace modules\\{$schema->modName}\\action\\{$schema->ctrlName};\n\n");
		fwrite($stream, "use library\\actions;\n\n");

		$fileManager->writeClassComment($stream, $clsName, '编辑单个字段', "modules.{$schema->modName}.action.{$schema->ctrlName}");
		fwrite($stream, "class {$clsName} extends actions\\SingleModify\n");
		fwrite($stream, "{\n");
		fwrite($stream, "\t/**\n");
		fwrite($stream, "\t * (non-PHPdoc)\n");
		fwrite($stream, "\t * @see \\tfc\\mvc\\interfaces\\Action::run()\n");
		fwrite($stream, "\t */\n");
		fwrite($stream, "\tpublic function run()\n");
		fwrite($stream, "\t{\n");
		fwrite($stream, "\t\t\$this->execute('{$schema->ucClsName}');\n");
		fwrite($stream, "\t}\n");
		fwrite($stream, "}\n");
		fclose($stream);

		Log::echoTrace('Generate App Act ' .$clsName . ' Successfully');
	}

	/**
	 * 创建 Trash Action
	 * @return void
	 */
	public function actTrash()
	{
		$fileManager = $this->fileManager;
		$schema = $this->schema;

		$clsName = ucfirst($schema->actTrashName);
		$filePath = $fileManager->action . DS . $clsName . '.php';
		$stream = $fileManager->fopen($filePath);
		$fileManager->writeCopyrightComment($stream);
		fwrite($stream, "namespace modules\\{$schema->modName}\\action\\{$schema->ctrlName};\n\n");
		fwrite($stream, "use library\\actions;\n\n");

		$fileManager->writeClassComment($stream, $clsName, '移至回收站和从回收站还原', "modules.{$schema->modName}.action.{$schema->ctrlName}");
		fwrite($stream, "class {$clsName} extends actions\\Trash\n");
		fwrite($stream, "{\n");
		fwrite($stream, "\t/**\n");
		fwrite($stream, "\t * (non-PHPdoc)\n");
		fwrite($stream, "\t * @see \\tfc\\mvc\\interfaces\\Action::run()\n");
		fwrite($stream, "\t */\n");
		fwrite($stream, "\tpublic function run()\n");
		fwrite($stream, "\t{\n");
		fwrite($stream, "\t\t\$this->execute('{$schema->ucClsName}');\n");
		fwrite($stream, "\t}\n");
		fwrite($stream, "}\n");
		fclose($stream);

		Log::echoTrace('Generate App Act ' .$clsName . ' Successfully');
	}
}
