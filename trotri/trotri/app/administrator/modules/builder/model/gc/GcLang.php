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
 * GcLang class file
 * 生成“项目层语言包”
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: GcLang.php 1 2014-01-18 14:19:29Z huan.song $
 * @package modules.builder.model.gc
 * @since 1.0
 */
class GcLang extends AbstractGc
{
	/**
	 * (non-PHPdoc)
	 * @see \modules\builder\model\gc\AbstractGc::_exec()
	 */
	protected function _exec()
	{
		$fileManager = $this->fileManager;
		$schema = $this->schema;

		$upCtrlName = strtoupper($schema->ctrlName);
		$upModName = strtoupper($schema->modName);

		$fileName = 'zh-CN.mod_' . $schema->modName . '.ini';
		$filePath = $fileManager->CNLangs . DS . $fileName;
		$stream = $fileManager->fopen($filePath);
		$fileManager->writeLangComment($stream, $fileName, $schema->appName);

		fwrite($stream, "; SideBar Url\n");
		fwrite($stream, "MOD_{$upModName}_URLS_{$upCtrlName}_INDEX=\"{$schema->builderName}管理\"\n");
		fwrite($stream, "MOD_{$upModName}_URLS_{$upCtrlName}_CREATE=\"新增{$schema->builderName}\"\n\n");

		fwrite($stream, "; {$schema->tblName} {$schema->builderName}\n");
		foreach ($schema->groups as $rows) {
			if ($rows['group_name'] != 'main') {
				fwrite($stream, "{$rows['lang_key']}=\"{$rows['prompt']}\"\n");
			}
		}

		foreach ($schema->fields as $rows) {
			fwrite($stream, "{$rows['lang_label']}=\"{$rows['html_label']}\"\n");
			fwrite($stream, "{$rows['lang_hint']}=\"{$rows['form_prompt']}\"\n");
		}

		fclose($stream);
	}
}
