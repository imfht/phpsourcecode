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
 * AbstractGc class file
 * 生成代码基类
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: AbstractGc.php 1 2014-01-18 14:19:29Z huan.song $
 * @package modules.builder.model.gc
 * @since 1.0
 */
abstract class AbstractGc
{
	public
		$schema = null,
		$fileManager = null;

	/**
	 * 构造方法：初始化数据寄存器和文件管理器
	 * @param modules\builder\model\gc\Schema $schema
	 * @param modules\builder\model\gc\FileManager $fileManager
	 */
	public function __construct($schema, $fileManager)
	{
		$this->schema = $schema;
		$this->fileManager = $fileManager;
	}

	/**
	 * 生成代码
	 * @return void
	 */
	public function run()
	{
		$className = strrev(substr(strstr(strrev(get_class($this)), '\\', true), 0, -2));
		Log::echoTrace('Generate ' . $className . ' Begin ...');

		$this->_exec();

		Log::echoTrace('Generate ' . $className . ' End');
	}

	/**
	 * 执行生成代码
	 * @return void
	 */
	protected abstract function _exec();
}
