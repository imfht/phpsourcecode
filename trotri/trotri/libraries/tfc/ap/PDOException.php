<?php
/**
 * Trotri Foundation Classes
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright (c) 2011-2013 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace tfc\ap;

/**
 * PDOException class file
 * PDO操作数据库时发生的异常
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: PDOException.php 1 2013-03-29 16:48:06Z huan.song $
 * @package tfc.ap
 * @since 1.0
 */
class PDOException extends \PDOException
{
    /**
     * 构造方法：重新定义PDOException的code和message值
     * @param \PDOException $e
     */
    public function __construct(\PDOException $e)
    {
        if (strstr($e->getMessage(), 'SQLSTATE[')) {
            preg_match('/SQLSTATE\[(\w+)\] \[(\w+)\] (.*)/', $e->getMessage(), $matches);
            $this->code = ($matches[1] == 'HT000' ? $matches[2] : $matches[1]);
            $this->message = $matches[3];
        }
    }
}
