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
namespace Tang\Exception;
use Tang\Log\LogService;
use Tang\Services\I18nService;
use Tang\Services\ConfigService;
use Tang\Services\RequestService;

/**
 * 系统异常
 * Class SystemException
 * @package Tang\Exception
 */
class SystemException extends \Exception
{
    /**
     * 日志等级 当日志等级为空的时，不记录日志
     * @var string
     */
    protected $logLevel = '';

    /**
     * @param string $message 语言包消息
     * @param null $args 语言参数
     * @param int $code 错误码
     * @param string $logLevel 日志等级
     * @param string $file 错误文件
     * @param int $line 行数
     */
    public function __construct($message,$args=null,$code = 0,$logLevel ='',$file='',$line=0)
	{
        if(!is_array($args))
        {
            $args = array($args);
        }
        $this->logLevel = $logLevel;
		parent::__construct(I18nService::getService()->get($message,$args),$code);
        if($file)
        {
            $this->file = $file;
        }
        if($line)
        {
            $this->line = $line;
        }
	}
	
	public function __toString()
	{
		$config = ConfigService::getService();
        if($this->logLevel)
        {
            try
			{
				LogService::getService()->write($this->getMessage().'@File:'.$this->getFile().'@Line:'.$this->getLine(),$this->logLevel);
			} catch(\Exception $e)
			{
			}
        }
		if($config->get('debug',false) === false)
		{
			$exception = array('message' =>'发生异常，需要在开发者模式下才能显示','debug' => false);
		} else
		{
			$line = $this->getLine();
			$file = $this->getFile();
			$exception = array(
					'message' => $this->getMessage(),
					'code' => $this->getCode(),
					'file' => $file,
					'line' => $line,
					'trace' => $this->getTraceAsString(),
					'exceptionName' => get_called_class(),
					'debug' => true
			);
			$codes = '';
			if($file != 'Unknown')
			{
				$line2 = $line > 10 ? $line - 10:1;
				$fp = fopen($file, 'r');
				for($i = 0;$i <$line2-1;$i++)
				{
					fgets($fp,1024);
				}
				$i = $line2; $line2+=20;
				$paddingZore = strlen($line2) ;
				for(;$i<$line2;$i++)
				{
					if(feof($fp))
					{
						break;
					}
					$content = htmlspecialchars(fgets($fp,1024));
					$lineStirng = sprintf('%0'.$paddingZore.'d',$i);
					if($i == $line)
					{
						$codes .= '<font color=red>行'.$lineStirng.' '.$content.'</font>';
					} else
					{
						$codes .= '行'.$lineStirng.' '.$content;
					}
				}
			}
			$charset = $config->get('charset');
			$otherMessage = $this->getOtherMessage();
		}
		if(PHP_SAPI == 'cli')
		{
            $response = RequestService::getService()->getResponse();
            $response->writeLine('-------------['.$exception['exceptionName'].']-------');
            $response->writeLine('Exception:'.$exception['message']);
			if($exception['debug'])
			{
                $response->writeLine('File:'.$exception['file']);
                $response->writeLine('Line:'.$exception['line']);
                $response->writeLine('Trace-------------------------------------------');
                $response->writeLine($exception['trace']);
                $response->writeLine('Trace-------------------------------------------');
			}
            $response->writeLine('----------------------------');
		} else 
		{
			include __DIR__.'/../Pages/Exception.php';
		}
		exit;
	}
	protected function getOtherMessage()
	{
		return '';
	}
}