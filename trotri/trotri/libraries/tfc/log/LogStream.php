<?php
/**
 * Trotri Foundation Classes
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright (c) 2011-2013 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace tfc\log;

use tfc\ap\ErrorException;

/**
 * LogStream class file
 * 文本文件方式处理日志
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: LogStream.php 1 2013-03-29 16:48:06Z huan.song $
 * @package tfc.log
 * @since 1.0
 */
class LogStream extends Log
{
    /**
     * @var resource|null 文件句柄
     */
    protected $_stream = null;

    /**
     * 构造方法：打开写入文件的句柄，追加方式写入文件
     * @param string $path
     * @throws ErrorException 如果打开文件失败，抛出异常
     */
    public function __construct($path)
    {
        if (!($this->_stream = @fopen($path, 'a', false))) {
            throw new ErrorException(sprintf(
                'LogStream Path "%s" cannot be opened with mode "a"', $path
            ));
        }
    }

    /**
     * (non-PHPdoc)
     * @see \tfc\log\Log::shutdown()
     */
    public function shutdown()
    {
        if (is_resource($this->_stream)) {
            fclose($this->_stream);
        }
    }

    /**
     * (non-PHPdoc)
     * @see \tfc\log\Log::_write()
     */
    protected function _write(array $logs)
    {
        $log = $logs['priority'] . ': ' . $this->_leftDelimiter . $logs['dt_create'] . $this->_rightDelimiter . ' ' . $logs['event'] . "\n";
        if (@fwrite($this->_stream, $log) === false) {
            throw new ErrorException('LogStream unable to write to stream');
        }
    }
}
