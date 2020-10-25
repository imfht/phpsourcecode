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

use tfc\util\FileManager as UtilFileManager;
use tfc\saf\Log;

/**
 * FileManager class file
 * 生成代码文件管理器
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: FileManager.php 1 2014-01-18 14:19:29Z huan.song $
 * @package modules.builder.model.gc
 * @since 1.0
 */
class FileManager
{
	const
		SKIN_NAME = 'bootstrap',
		DIR_NAME_GC_DATA = 'code_generator';

	public
		$authorName    = '',
		$authorMail    = '',
		$srvName       = '',
		$appName       = '',
		$moduleName    = '',
		$ctrlName      = '';

	public
		$srv = '',
		$app = '',
		$slangs = '',
		$sGBLangs = '',
		$sCNLangs = '',
		$langs = '',
		$GBLangs = '',
		$CNLangs = '',
		$db = '',
		$services = '',
		$modules = '',
		$model = '',
		$controller = '',
		$action = '',
		$view = '';

	/**
	 * 构造方法：初始化所有的目录
	 * @param string $authorName
	 * @param string $authorMail
	 * @param string $srvName
	 * @param string $appName
	 * @param string $moduleName
	 * @param string $ctrlName
	 */
	public function __construct($authorName, $authorMail, $srvName, $appName, $moduleName, $ctrlName)
	{
		// 初始化工作开始
		Log::echoTrace('Initialization FileManager Begin ...');

		$this->authorName = $authorName;
		$this->authorMail = $authorMail;
		$this->srvName    = $srvName;
		$this->appName    = $appName;
		$this->moduleName = $moduleName;
		$this->ctrlName   = $ctrlName;

		$this->_initDirs();
		Log::echoTrace('Code Generator SRV Directory "' . $this->srv . '"');
		Log::echoTrace('Code Generator APP Directory "' . $this->app . '"');

		// 初始化工作结束
		Log::echoTrace('Initialization FileManager End');
	}

	/**
	 * 打开文件
	 * @param string $filePath
	 * @return resource
	 */
	public function fopen($filePath)
	{
		if (!($stream = @fopen($filePath, 'w', false))) {
			Log::errExit(__LINE__, sprintf(
				'File "%s" cannot be opened with mode "w"', $filePath
			));
		}

		return $stream;
	}

	/**
	 * 写类注释
	 * @param resource $stream
	 * @param string $className
	 * @param string $description
	 * @param string $package
	 * @return void
	 */
	public function writeClassComment($stream, $className, $description, $package)
	{
		fwrite($stream, "/**\n");
		fwrite($stream, " * {$className} class file\n");
		fwrite($stream, " * {$description}\n");
		fwrite($stream, " * @author {$this->authorName} <{$this->authorMail}>\n");
		fwrite($stream, " * @version \$Id: {$className}.php 1 " . date('Y-m-d H:i:s') . "Z Code Generator \$\n");
		fwrite($stream, " * @package {$package}\n");
		fwrite($stream, " * @since 1.0\n");
		fwrite($stream, " */\n");
	}

	/**
	 * 写语言包注释
	 * @param resource $stream
	 * @param string $fileName
	 * @param string $package
	 * @return void
	 */
	public function writeLangComment($stream, $fileName, $package)
	{
		fwrite($stream, "; \$Id: {$fileName} 1 " . date('Y-m-d H:i:s') . "Z Create By Code Generator \$\n");
		fwrite($stream, ";\n");
		fwrite($stream, "; @package     {$package}\n");
		fwrite($stream, "; @description [Description] [Name of language]([Country code])\n");
		fwrite($stream, "; @version     1.0\n");
		fwrite($stream, "; @date        " . date('Y-m-d') . "\n");
		fwrite($stream, "; @author      {$this->authorName} <{$this->authorMail}>\n");
		fwrite($stream, "; @copyright   Copyright &copy; 2011-" . date('Y') . " http://www.trotri.com/ All rights reserved.\n");
		fwrite($stream, "; @license     http://www.apache.org/licenses/LICENSE-2.0\n");
		fwrite($stream, "; @note        Client Site\n");
		fwrite($stream, "; @note        All ini files need to be saved as UTF-8 - No BOM\n\n");
	}

	/**
	 * 写版权注释
	 * @param resource $stream
	 * @return void
	 */
	public function writeCopyrightComment($stream)
	{
		fwrite($stream, "<?php\n");
		fwrite($stream, "/**\n");
		fwrite($stream, " * Trotri\n");
		fwrite($stream, " *\n");
		fwrite($stream, " * @author    Huan Song <trotri@yeah.net>\n");
		fwrite($stream, " * @link      http://github.com/trotri/trotri for the canonical source repository\n");
		fwrite($stream, " * @copyright Copyright &copy; 2011-" . date('Y') . " http://www.trotri.com/ All rights reserved.\n");
		fwrite($stream, " * @license   http://www.apache.org/licenses/LICENSE-2.0\n");
		fwrite($stream, " */\n\n");
	}

	/**
	 * 初始化目录
	 * @return void
	 */
	protected function _initDirs()
	{
		$this->srv = DIR_DATA_RUNTIME . DS . self::DIR_NAME_GC_DATA . DS . 'srv' . DS . $this->srvName;
		$this->app = DIR_DATA_RUNTIME . DS . self::DIR_NAME_GC_DATA . DS . 'app' . DS . $this->appName;

		$this->_mkDir($this->srv);
		$this->_mkDir($this->app);

		$this->slangs     = $this->srv     . DS . 'languages';       $this->_mkDir($this->slangs);
		$this->sGBLangs   = $this->slangs  . DS . 'en-GB';           $this->_mkDir($this->sGBLangs);
		$this->sCNLangs   = $this->slangs  . DS . 'zh-CN';           $this->_mkDir($this->sCNLangs);
		$this->langs      = $this->app     . DS . 'languages';       $this->_mkDir($this->langs);
		$this->GBLangs    = $this->langs   . DS . 'en-GB';           $this->_mkDir($this->GBLangs);
		$this->CNLangs    = $this->langs   . DS . 'zh-CN';           $this->_mkDir($this->CNLangs);
		$this->db         = $this->srv     . DS . 'db';              $this->_mkDir($this->db);
		$this->services   = $this->srv     . DS . 'services';        $this->_mkDir($this->services);
		$this->modules    = $this->app     . DS . 'modules';         $this->_mkDir($this->modules);
		$this->modules   .=                  DS . $this->moduleName; $this->_mkDir($this->modules);
		$this->model      = $this->modules . DS . 'model';           $this->_mkDir($this->model);
		$this->controller = $this->modules . DS . 'controller';      $this->_mkDir($this->controller);
		$this->action     = $this->modules . DS . 'action';          $this->_mkDir($this->action);
		$this->action    .=                  DS . $this->ctrlName;   $this->_mkDir($this->action);
		$this->view       = $this->app     . DS . 'views';           $this->_mkDir($this->view);
		$this->view      .=                  DS . self::SKIN_NAME;   $this->_mkDir($this->view);
		$this->view      .=                  DS . $this->moduleName; $this->_mkDir($this->view);
	}

	/**
	 * 新建目录，如果目录存在，则改变目录权限
	 * @param string $directory
	 * @param integer $mode 文件权限，8进制
	 * @return void
	 */
	protected function _mkDir($directory, $mode = 0664)
	{
		static $fileManager = null;

		if ($fileManager === null) {
			$fileManager = new UtilFileManager();
		}

		if (!$fileManager->mkDir($directory, $mode, true)) {
			Log::errExit(__LINE__, sprintf(
				'Dir "%s" cannot be create with mode "%04o"', $directory, $mode
			));
		}

		$dest = $directory . DS . 'index.html';
		if (!$fileManager->isFile($dest)) {
			$source = DIR_DATA_RUNTIME . DS . 'index.html';
			$fileManager->copy($source, $dest);
		}
	}
}
