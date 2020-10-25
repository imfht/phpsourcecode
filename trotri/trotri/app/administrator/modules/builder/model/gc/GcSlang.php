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
 * GcSlang class file
 * 生成“业务层语言包”
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: GcSlang.php 1 2014-01-18 14:19:29Z huan.song $
 * @package modules.builder.model.gc
 * @since 1.0
 */
class GcSlang extends AbstractGc
{
	/**
	 * (non-PHPdoc)
	 * @see \modules\builder\model\gc\AbstractGc::_exec()
	 */
	protected function _exec()
	{
		$this->enum();
		$this->filter();
	}

	/**
	 * 生成zh-CN.srv_enum.ini
	 * @return void
	 */
	public function enum()
	{
		$fileManager = $this->fileManager;
		$schema = $this->schema;

		$fileName = 'zh-CN.srv_enum.ini';
		$filePath = $fileManager->sCNLangs . DS . $fileName;
		$stream = $fileManager->fopen($filePath);
		$fileManager->writeLangComment($stream, $fileName, $schema->srvName);
		fwrite($stream, "; Global\n");
		fwrite($stream, "SRV_ENUM_GLOBAL_YES=\"是\"\n");
		fwrite($stream, "SRV_ENUM_GLOBAL_NO=\"否\"\n\n");

		fwrite($stream, "; {$schema->tblName} {$schema->builderName}\n");
		foreach ($schema->fields as $rows) {
			if (isset($rows['enums'])) {
				foreach ($rows['enums'] as $enums) {
					if ($enums['lang_key'] !== 'SRV_ENUM_GLOBAL_YES' && $enums['lang_key'] !== 'SRV_ENUM_GLOBAL_NO') {
						fwrite($stream, "{$enums['lang_key']}=\"{$enums['value']}\"\n");
					}
				}
			}
		}

		fclose($stream);
	}

	/**
	 * 生成zh-CN.srv_filter.ini
	 * @return void
	 */
	public function filter()
	{
		$fileManager = $this->fileManager;
		$schema = $this->schema;

		$fileName = 'zh-CN.srv_filter.ini';
		$filePath = $fileManager->sCNLangs . DS . $fileName;
		$stream = $fileManager->fopen($filePath);
		$fileManager->writeLangComment($stream, $fileName, $schema->srvName);
		fwrite($stream, "; {$schema->tblName} {$schema->builderName}\n");
		foreach ($schema->fields as $rows) {
			if (isset($rows['validators'])) {
				foreach ($rows['validators'] as $validators) {
					fwrite($stream, "{$validators['lang_key']}=\"{$validators['message']}\"\n");
				}
			}
		}

		fclose($stream);
	}
}
