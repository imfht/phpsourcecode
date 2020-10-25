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
 * GcController class file
 * 生成“项目层控制器类”
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: GcController.php 1 2014-01-18 14:19:29Z huan.song $
 * @package modules.builder.model.gc
 * @since 1.0
 */
class GcController extends AbstractGc
{
	/**
	 * (non-PHPdoc)
	 * @see \modules\builder\model\gc\AbstractGc::_exec()
	 */
	protected function _exec()
	{
		$fileManager = $this->fileManager;
		$schema = $this->schema;

		$clsName = $schema->ucCtrlName . 'Controller';

		$filePath = $fileManager->controller . DS . $clsName . '.php';
		$stream = $fileManager->fopen($filePath);
		$fileManager->writeCopyrightComment($stream);

		fwrite($stream, "namespace modules\\{$schema->modName}\\controller;\n\n");
		fwrite($stream, "use libapp\\BaseController;\n\n");

		$fileManager->writeClassComment($stream, $clsName, $schema->builderName, "modules.{$schema->modName}.controller");
		fwrite($stream, "class {$clsName} extends BaseController\n");
		fwrite($stream, "{\n");
		fwrite($stream, "}\n");
		fclose($stream);
	}
}
