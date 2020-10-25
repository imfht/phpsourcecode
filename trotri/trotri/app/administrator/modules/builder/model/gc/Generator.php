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
 * Generator class file
 * 代码生成器
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: Generator.php 1 2014-01-18 14:19:29Z huan.song $
 * @package modules.builder.model.gc
 * @since 1.0
 */
class Generator
{
	public
		$schema = null,
		$fileManager = null;

	/**
	 * 构造方法：初始化Builders、Types、Groups、Fields、Validators数据寄存器和文件管理器
	 * @param integer $builderId
	 */
	public function __construct($builderId)
	{
		// 初始化工作开始
		Log::echoTrace('Initialization Begin ...');

		$this->schema = new Schema($builderId);
		$this->fileManager = new FileManager(
			$this->schema->authorName,
			$this->schema->authorMail,
			$this->schema->srvName,
			$this->schema->appName,
			$this->schema->modName,
			$this->schema->ctrlName
		);

		// 初始化工作结束
		Log::echoTrace('Initialization End');
	}

	/**
	 * 生成代码
	 * @return void
	 */
	public function run()
	{
		Log::echoTrace('Generate Begin, Table Name "' . $this->schema->tblName . '"');

		$GcDb = new GcDb($this->schema, $this->fileManager);
		$GcDb->run();

		$GcData = new GcData($this->schema, $this->fileManager);
		$GcData->run();

		$GcFormProcessor = new GcFormProcessor($this->schema, $this->fileManager);
		$GcFormProcessor->run();

		$GcService = new GcService($this->schema, $this->fileManager);
		$GcService->run();

		$GcSlang = new GcSlang($this->schema, $this->fileManager);
		$GcSlang->run();

		$GcLang = new GcLang($this->schema, $this->fileManager);
		$GcLang->run();

		$GcModel = new GcModel($this->schema, $this->fileManager);
		$GcModel->run();

		$GcController = new GcController($this->schema, $this->fileManager);
		$GcController->run();

		$GcActs = new GcActs($this->schema, $this->fileManager);
		$GcActs->run();

		$GcViews = new GcViews($this->schema, $this->fileManager);
		$GcViews->run();

		Log::echoTrace('Generate End, Table Name "' . $this->schema->tblName . '"');
	}
}
