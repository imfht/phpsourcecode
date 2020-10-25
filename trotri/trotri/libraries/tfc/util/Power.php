<?php
/**
 * Trotri Foundation Classes
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright (c) 2011-2013 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace tfc\util;

use tfc\ap\ErrorException;

/**
 * Power class file
 * 权限验证类
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: Power.php 1 2013-03-29 16:48:06Z huan.song $
 * @package tfc.util
 * @since 1.0
 */
class Power
{
    /**
     * @var integer 权限：全部拒绝
     */
    const MODE_DENY_ALL = 0x00; // 00000000 :0

    /**
     * @var integer 权限：SELECT
     */
    const MODE_S    = 0x01; // 00000001 :1

    /**
     * @var integer 权限：INSERT
     */
    const MODE_I    = 0x02; // 00000010 :2

    /**
     * @var integer 权限：UPDATE
     */
    const MODE_U    = 0x04; // 00000100 :4

    /**
     * @var integer 权限：DELETE
     */
    const MODE_D    = 0x08; // 00001000 :8

    /**
     * @var integer 权限：SELECT | INSERT
     */
    const MODE_SI   = 0x03; // 00000001 | 00000010 = 00000011 :3

    /**
     * @var integer 权限：SELECT | UPDATE
     */
    const MODE_SU   = 0x05; // 00000001 | 00000100 = 00000101 :5

    /**
     * @var integer 权限：SELECT | DELETE
     */
    const MODE_SD   = 0x09; // 00000001 | 00001000 = 00001001 :9

    /**
     * @var integer 权限：INSERT | UPDATE
     */
    const MODE_IU   = 0x06; // 00000010 | 00000100 = 00000110 :6

    /**
     * @var integer 权限：INSERT | DELETE
     */
    const MODE_ID   = 0x0a; // 00000010 | 00001000 = 00001010 :10

    /**
     * @var integer 权限：UPDATE | DELETE
     */
    const MODE_UD   = 0x0c; // 00000100 | 00001000 = 00001100 :12

    /**
     * @var integer 权限：SELECT | INSERT | UPDATE
     */
    const MODE_SIU  = 0x07; // 00000011 | 00000100 = 00000111 :7

    /**
     * @var integer 权限：SELECT | INSERT | DELETE
     */
    const MODE_SID  = 0x0b; // 00000011 | 00001000 = 00001011 :11

    /**
     * @var integer 权限：SELECT | UPDATE | DELETE
     */
    const MODE_SUD  = 0x0d; // 00000101 | 00001000 = 00001101 :13

    /**
     * @var integer 权限：INSERT | UPDATE | DELETE
     */
    const MODE_IUD  = 0x0e; // 00000110 | 00001000 = 00001110 :14

    /**
     * @var integer 权限：SELECT | INSERT | UPDATE | DELETE
     */
    const MODE_SIUD = 0x0f; // 00000111 | 00001000 = 00001111 :15

    /**
     * 验证权限：是否允许“查询”数据
     * @param integer $mode
     * @return boolean
     */
    public static function allowSelect($mode)
    {
        return self::isAllow($mode, self::MODE_S);
    }

    /**
     * 验证权限：是否允许“新增”数据
     * @param integer $mode
     * @return boolean
     */
    public static function allowInsert($mode)
    {
        return self::isAllow($mode, self::MODE_I);
    }

    /**
     * 验证权限：是否允许“编辑”数据
     * @param integer $mode
     * @return boolean
     */
    public static function allowUpdate($mode)
    {
        return self::isAllow($mode, self::MODE_U);
    }

    /**
     * 验证权限：是否允许“删除”数据
     * @param integer $mode
     * @return boolean
     */
    public static function allowDelete($mode)
    {
        return self::isAllow($mode, self::MODE_D);
    }

    /**
     * 验证权限：是否拒绝“查询”数据
     * @param integer $mode
     * @return boolean
     */
    public static function denySelect($mode)
    {
        return self::isDeny($mode, self::MODE_S);
    }

    /**
     * 验证权限：是否拒绝“新增”数据
     * @param integer $mode
     * @return boolean
     */
    public static function denyInsert($mode)
    {
        return self::isDeny($mode, self::MODE_I);
    }

    /**
     * 验证权限：是否拒绝“编辑”数据
     * @param integer $mode
     * @return boolean
     */
    public static function denyUpdate($mode)
    {
        return self::isDeny($mode, self::MODE_U);
    }

    /**
     * 验证权限：是否拒绝“删除”数据
     * @param integer $mode
     * @return boolean
     */
    public static function denyDelete($mode)
    {
        return self::isDeny($mode, self::MODE_D);
    }

    /**
     * 验证权限：是否允许
     * @param integer $userMode
     * @param integer $powerMode
     * @return boolean
     * @throws ErrorException 如果参数不是有效的权限码，抛出异常
     */
    public static function isAllow($userMode, $powerMode)
    {
        $powerMode = (int) $powerMode;
        if ($powerMode < self::MODE_S || $powerMode > self::MODE_SIUD) {
            throw new ErrorException(sprintf(
                'Power power mode "%d" invalid.', $powerMode
            ));
        }

        $userMode = (int) $userMode;
        if ($userMode < self::MODE_S || $userMode > self::MODE_SIUD) {
            throw new ErrorException(sprintf(
                'Power user mode "%d" invalid.', $userMode
            ));
        }

        return (boolean) ($userMode & $powerMode);
    }

    /**
     * 验证权限：是否拒绝
     * @param integer $userMode
     * @param integer $powerMode
     * @return boolean
     * @throws ErrorException 如果参数不是有效的权限码，抛出异常
     */
    public static function isDeny($userMode, $powerMode)
    {
        return !self::isAllow($userMode, $powerMode);
    }
}
