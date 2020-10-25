<?php
/**
 * Trotri Foundation Classes
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright (c) 2011-2013 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace tfc\ap\interfaces;

/**
 * SessionSaveHandler interface file
 * 用户自定义会话处理接口
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: SessionSaveHandler.php 1 2013-03-29 16:48:06Z huan.song $
 * @package tfc.ap.interfaces
 * @since 1.0
 */
interface SessionSaveHandler
{
    /**
     * 打开会话任务
     * @param string $path
     * @param string $name
     * @return boolean
     */
    public function open($path, $name);

    /**
     * 关闭会话任务
     * @return boolean
     */
    public function close();

    /**
     * 通过会话ID获取会话数据
     * @param string $sessId
     * @return string|boolean
     */
    public function read($sessId);

    /**
     * 通过会话ID更新会话数据
     * @param string $sessId
     * @param string $data
     * @return boolean
     */
    public function write($sessId, $data);

    /**
     * 通过会话ID删除会话数据
     * @param string $sessId
     * @return boolean
     */
    public function destroy($sessId);

    /**
     * 清除所有过期的会话数据
     * @param integer $maxLifeTime
     * @return boolean
     */
    public function gc($maxLifeTime);
}
