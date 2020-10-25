<?php
/*
 * YanPHP
 * User: weilongjiang(江炜隆)<william@jwlchina.cn>
 * Date: 2017/9/28
 * Time: 13:56
 */

namespace Yan\Core;


use Aura\Session\CsrfToken;
use Aura\Session\SessionFactory;

/**
 * @method static mixed get($key, $alt = null)
 * @method static mixed set($key, $val)
 * @method static null clear()
 * @method static mixed getFlash($key, $alt = null)
 * @method static null setFlash($key, $val)
 * @method static null clearFlash()
 * @method static mixed getFlashNext($key, $alt = null)
 * @method static null setFlashNow($key, $val)
 * @method static null clearFlashNow()
 * @method static null keepFlash()
 */
class Session
{
    /** @var \Aura\Session\SessionFactory */
    protected static $sessionFactory;
    /** @var  \Aura\Session\Session */
    protected static $session;
    /** @var  \Aura\Session\Segment */
    protected static $segment;

    public static function initialize()
    {
        static::$sessionFactory = new SessionFactory();
        static::$session = static::$sessionFactory->newInstance($_COOKIE);
        static::$session->setSavePath(Config::get('session_path'));
        static::$session->setName(Config::get('session_name'));

        static::$segment = static::$session->getSegment('YanPHP\Core\Session');

    }

    public static function start(): bool
    {
        return static::$session->start();
    }

    public static function commit()
    {
        return static::$session->commit();
    }

    public static function regenerateId(): bool
    {
        return static::$session->regenerateId();
    }

    /**
     *
     * Destroys the session entirely.
     *
     * @return bool
     *
     * @see http://php.net/manual/en/function.session-destroy.php
     *
     */
    public static function destroy(): bool
    {
        return static::$session->destroy();
    }

    public static function getCsrfToken(): CsrfToken
    {
        return static::$session->getCsrfToken();
    }


    public static function __callStatic($name, $arguments)
    {
        //立即刷数据到磁盘
        static::start();
        $ret = call_user_func_array([static::$segment, $name], $arguments);
        static::commit();
        return $ret;
    }
}