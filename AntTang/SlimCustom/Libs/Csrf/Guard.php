<?php
/**
 * @package     Guard.php
 * @author      Jing Tang <tangjing3321@gmail.com>
 * @link        http://www.slimphp.net/
 * @version     2.0
 * @copyright   Copyright (c) http://www.slimphp.net
 * @date        2017年8月3日
 */

namespace SlimCustom\Libs\Csrf;

/**
 * Csrf防御
 * 
 * @author Jing Tang <tangjing3321@gmail.com>
 */
class Guard extends \Slim\Csrf\Guard
{
    /**
     * 验证存贮
     * 
     * {@inheritDoc}
     * @see \Slim\Csrf\Guard::validateStorage()
     */
    public function validateStorage()
    {
        if (is_array($this->storage)) {
            return $this->storage;
        }
        if ($this->storage instanceof ArrayAccess) {
            return $this->storage;
        }
        $this->storage = cache()->get('csrf.' . $this->prefix, []);
        return $this->storage;
    }
    
    /**
     * 保存 csrfToken
     * 
     * {@inheritDoc}
     * @see \Slim\Csrf\Guard::__destruct()
     */
    public function __destruct()
    {
        cache()->put('csrf.' . $this->prefix, $this->storage, 3600);
    }
}
