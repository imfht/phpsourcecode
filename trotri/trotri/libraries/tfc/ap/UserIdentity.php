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
 * UserIdentity class file
 * 用户身份管理类
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: UserIdentity.php 1 2013-04-05 01:08:06Z huan.song $
 * @package tfc.ap
 * @since 1.0
 */
class UserIdentity
{
    /**
     * @var integer 用户ID
     */
    protected static $_id = 0;

    /**
     * @var string 用户名
     */
    protected static $_name = '';

    /**
     * @var string 用户昵称
     */
    protected static $_nick = '';

    /**
     * 判断用户是否已登录
     * @return boolean
     */
    public static function isLogin()
    {
        return self::getId() > 0;
    }

    /**
     * 获取用户ID
     * @return integer
     */
    public static function getId()
    {
        return self::$_id;
    }

    /**
     * 设置用户ID
     * @param integer $id
     * @return void
     */
    public static function setId($id)
    {
        self::$_id = (int) $id;
    }

    /**
     * 获取用户名
     * @return string
     */
    public static function getName()
    {
        return self::$_name;
    }

    /**
     * 设置用户名
     * @param string $name
     * @return void
     */
    public static function setName($name)
    {
        self::$_name = trim($name);
    }

    /**
     * 获取用户昵称
     * @return string
     */
    public static function getNick()
    {
        return self::$_nick;
    }

    /**
     * 设置用户昵称
     * @param string $nick
     * @return void
     */
    public static function setNick($nick)
    {
        self::$_nick = trim($nick);
    }
}
