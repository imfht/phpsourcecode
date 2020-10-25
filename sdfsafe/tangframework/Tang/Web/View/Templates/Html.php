<?php
// +-----------------------------------------------------------------------------------
// | TangFrameWork 致力于WEB快速解决方案
// +-----------------------------------------------------------------------------------
// | Copyright (c) 2012-2014 http://www.tangframework.com All rights reserved.
// +-----------------------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +-----------------------------------------------------------------------------------
// | HomePage ( http://www.tangframework.com/ )
// +-----------------------------------------------------------------------------------
// | Author: wujibing<283109896@qq.com>
// +-----------------------------------------------------------------------------------
// | Version: 1.0
// +-----------------------------------------------------------------------------------
namespace Tang\Web\View\Templates;
use Tang\Exception\SystemException;
use Tang\Services\ConfigService;
use Tang\Services\FileService;
use Tang\Util\Format;
use Tang\Web\Parameters;
use Tang\Web\View\IView;

class Html implements ITemplate
{
	/**
	 * @var Parameters
	 */
	protected $parameters;
	/**
	 * @var string
	 */
	protected $applicationPath = '';
	/**
	 * @var IView
	 */
	protected $view;
	public function display($data,&$content,$templateFile='')
	{
		if($templateFile)
		{
			!is_file($templateFile) && $templateFile = $this->parseTemplateFile($templateFile);
		} else
		{
			$templateFile = $this->parameters->module.DIRECTORY_SEPARATOR.$this->parameters->controller.DIRECTORY_SEPARATOR.ucfirst($this->parameters->action);
		}
		$compileFile = $this->getCompileFile($templateFile);
		extract($data,EXTR_OVERWRITE);
		ob_start();
		include $compileFile;
		$content = ob_get_contents();
		ob_end_clean();
	}
    public function callback($callback,&$content)
    {
        Format::addslashes($content);
        $content = str_replace("\n","\"+\n\"",$content);
        $content = str_replace("\r","",$content);
        $content = $callback.'("'.$content.'");';
    }
	public function setApplicationPath($path)
	{
		$this->applicationPath = $path;
	}
	public function setView(IView $view)
	{
		$this->view = $view;
	}
	public function setParameters(Parameters $parameters)
	{
		$this->parameters = $parameters;
	}
	protected function getCompileFile($templateFile)
	{
		$config = $this->view->getConfig();
		$theme = $this->view->getTheme();
		$defaultTheme = $config['defaultTheme'];
		$directory = $config['directory'];
        $configInstance = ConfigService::getService();
        $dataDirectory = $configInstance->get('dataDirctory');
        $isFileExists = false;
        if(is_file($templateFile) && file_exists($templateFile))
        {
            $isFileExists = true;
            $filePath = $templateFile;
            $compileFile =$dataDirectory.DIRECTORY_SEPARATOR.$directory.DIRECTORY_SEPARATOR.'Pages'.DIRECTORY_SEPARATOR.md5($templateFile).'tpl.html';
        } else
        {
            $filePath = $directory.DIRECTORY_SEPARATOR.$theme.DIRECTORY_SEPARATOR.$templateFile.'.html';
            $compileFile = $dataDirectory.DIRECTORY_SEPARATOR.$filePath.'.tpl.php';
        }

		if(!$configInstance->get('debug') && file_exists($compileFile))
		{
			return $compileFile;
		}
        if(!$isFileExists)
        {
            $libPath = $this->applicationPath.'Lib'.DIRECTORY_SEPARATOR;
            $filePath = $libPath.$filePath;
            if(!file_exists($filePath))
            {
                $throw = function($file)
                {
                    throw new SystemException('Template File not Found!',array($file));
                };
                if($theme != $defaultTheme)
                {
                    $filePath = $libPath.$directory.DIRECTORY_SEPARATOR.$defaultTheme.DIRECTORY_SEPARATOR.$templateFile.'.html';
                    if(!file_exists($filePath))
                    {
                        $throw($filePath);
                    }
                } else
                {
                    $throw($filePath);
                }
            }
        }
		$this->compile($filePath,$compileFile);
		return $compileFile;
	}
	/**
	 * 生成模板页面
	 * @param string $templateFile 模板文件
	 * @param string $compileFile 转换为PHP的模板文件
	 */
	protected function compile($templateFile,$compileFile)
	{
		$content = '';
		$fileInstance = FileService::getService();
		$fileInstance->read($templateFile,$content);
		$content = preg_replace('$([\n\r]+)\t+$s', '\\1', $content);
        $content = preg_replace('$\<\?php$', '&lt;?php', $content);
		$content = preg_replace('$\<\?.+?\?\>$s', '', $content);
		$content = preg_replace('$\<\!\-\-\{(.+?)\}\-\-\>$s', '{\\1}', $content);

		$content = preg_replace('$\{StorageUrl\((.+?)\)\}$i', '<?php echo \Tang\Storage\StorageService::getUrl(\\1);?>', $content);
		$content = preg_replace('$\{Form-\>(.+?)\((.+?)\)\}$i', '<?php echo \Tang\Util\Form::createByFile(\\1,\\2);?>', $content);
		$content = preg_replace('$\{Form\.(.+?)\((.+?)\)\}$i', '<?php echo \Tang\Util\Form::\\1(\\2);?>', $content);
        $content = preg_replace('$\</form>$i','<?php echo \Tang\Services\RequestService::getService()->CSRF()->getInput();?></form>',$content);
		$content = preg_replace('$\{TOKEN\((.+?)\)}$', '<?php echo \Tang\Token\TokenService::getService()->getInput(\\1);?>', $content);
		$content = preg_replace('$\{I18n-\>(.+?)}$i', '<?php echo \Tang\Services\I18nService::get(\'\\1\');?>', $content);
		$content = preg_replace('$\{I18n-\>(.+?)-\>\((.+?)\)}$i', '<?php echo \Tang\Services\I18nService::get(\'\\1\',\\2)?>', $content);
        $content = preg_replace('$\{Url\((.+?)\)}$i', '<?php echo \Tang\Services\RouterService::getService()->driver(\'web\')->createUrl(\\1);?>', $content);

        $content = preg_replace_callback('${include\s+(\S+)}$is', array($this, 'includeTemplate'),$content);
		$content = preg_replace_callback('${controller\s+(\S+)}$is', array($this, 'loadController'),$content);
		$content = preg_replace('$\{Config-\>(.+?)}$i', '<?php echo \Tang\Services\ConfigService::get(\'\\1\');?>', $content);
		$content = preg_replace('$\{Config\.(.+?)}$i', '<?php echo \Tang\Services\ConfigService::get(\\1);?>', $content);
        $content = preg_replace('$\{Model\((.+?)\)-\>(.+?)}$i', '<?php echo \Tang\Database\Sql\Model::loadModel(\\1)->\\2;?>', $content);
		$content = preg_replace('$\{phpcode}(.+?)\{/phpcode\}$s', '<?php \\1?>',$content);
		$content = preg_replace('$\{\+\+(.+?)\}$','<?php ++\\1; ?>',$content);
		$content = preg_replace('$\{\-\-(.+?)\}$','<?php ++\\1; ?>',$content);
		$content = preg_replace('$\{(.+?)\+\+\}$','<?php \\1++; ?>',$content);
		$content = preg_replace('$\{(.+?)\-\-\}$','<?php \\1--; ?>',$content);
		$content = preg_replace('$\{if\s+(.+?)\}$i', '<?php if(\\1) { ?>', $content);
		$content = preg_replace('$\{elseif\s+(.+?)\}$i', '<?php } elseif (\\1) { ?>', $content);
		$content = preg_replace('$\{else\}$i', '<?php } else { ?>', $content);
		$content = preg_replace('$\{for\s+(.+?)\}$i', '<?php for(\\1) { ?>', $content);
		$content = preg_replace('$\{foreach\s+(\S+)\s+(\S+)\}$i', '<?php $tmpArray = \\1;if(is_array($tmpArray) || $tmpArray instanceof \ArrayIterator) foreach($tmpArray as \\2) { ?>', $content);
		$content = preg_replace('$\{foreach\s+(\S+)\s+(\S+)\s+(\S+)\}$i', '<?php $tmpArray = \\1;if(is_array($tmpArray) || $tmpArray instanceof \ArrayIterator) foreach($tmpArray as \\2 => \\3) {?>', $content);
		$content = preg_replace('$\{\/(for|if|foreach|end)\}$i', '<?php } ?>', $content);
        $content = preg_replace('$\{(\\$[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)\->([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)\}$', '<?php echo \\1->\\2;?>', $content);

		$content = preg_replace('$\{([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff:]*\(([^{}]*)\))\}$s', '<?php echo \\1;?>', $content);

		$content = preg_replace('$\{\\$([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff:]*\(([^{}]*)\))\}$', '<?php echo \\1;?>',$content);
		$content = preg_replace('$\{(\\$[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)\}$', '<?php echo \\1;?>',$content);
        $content = preg_replace('$\{(\\$[a-zA-Z0-9->_\[\]\'\"\$\x7f-\xff]+)\}$es','$this->addQuote(\'<?php echo \\1;?>\')',$content);
		$content = preg_replace('$\{([A-Z_\x7f-\xff][A-Z0-9_\x7f-\xff]*)\}$', '<?php echo \\1;?>', $content);

		$this->diyCompile($content);

		$fileInstance->write($compileFile,$content);
	}

    protected function includeTemplate($match)
    {
        $file = $this->parseTemplateFile(trim($match[1]));
        $file = $this->getCompileFile($file);
        $content = '';
        FileService::getService()->read($file,$content);
        return "<!--Insert[{$match[1]}]start!-->\n{$content}\n<!--Insert[{$match[1]}]end!-->\n";
    }
	protected function loadController($match)
	{
		$string = $this->parseTemplateFile(trim($match[1]));
		$string = str_replace(DIRECTORY_SEPARATOR,'\',\'',$string);
		$string = '\''.$string.'\'';
		return '<?php \Tang\Web\Controllers\Controller::loadRun('.$string.',\''.$this->parameters->type.'\');?>';
	}
	protected function parseTemplateFile($templateFile)
	{
        $templateFile = trim($templateFile,'.');
		$temp = explode('.',$templateFile,3);
        $temp = array_map('ucfirst',$temp);
        $count = count($temp);
        if($count ==2)
        {
            array_unshift($temp,$this->parameters->module);
        } else if($count == 1)
        {
            array_unshift($temp,$this->parameters->module,$this->parameters->controller);
        }
        return implode(DIRECTORY_SEPARATOR,$temp);
	}
	protected function diyCompile(&$content)
	{
	}
	protected function addQuote($value)
	{
		return str_replace("\\\"", "\"", preg_replace('%\[([a-zA-Z0-9_\-\.\x7f-\xff]+)\]%s', "['\\1']",$value));
	}
}
