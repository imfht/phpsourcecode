<?php
/**
 * Trotri
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright &copy; 2011-2013 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace libapp;

/**
 * ErrorNo class file
 * 常用错误码类
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: ErrorNo.php 1 2013-05-18 14:58:59Z huan.song $
 * @package libapp
 * @since 1.0
 */
class ErrorNo
{
    /**
     * @var integer OK
     */
    const SUCCESS_NUM                  = 0;

    /**
     * @var integer 参数错误
     */
    const ERROR_REQUEST                = 400;

    /**
     * @var integer 用户没有访问权限
     */
    const ERROR_NO_POWER               = 403;

    /**
     * @var integer 用户未登录，禁止访问
     */
    const ERROR_NO_LOGIN               = 404;

    /**
     * @var integer 系统运行异常
     */
    const ERROR_SYSTEM_RUN_ERR         = 500;

    /**
     * @var integer 脚本运行失败
     */
    const ERROR_SCRIPT_RUN_ERR         = 501;

    /**
     * @var integer 未知错误
     */
    const ERROR_UNKNOWN                = 2008;

    /**
     * @var integer 验证数据结果：数据正确
     */
    const SUCCESS_VALIDATE             = 10001;

    /**
     * @var integer 验证数据结果：数据错误
     */
    const ERROR_VALIDATE               = 10002;

    /**
     * @var integer 查询成功
     */
    const SUCCESS_SELECT               = 100010;

    /**
     * @var integer 添加成功
     */
    const SUCCESS_INSERT               = 100011;

    /**
     * @var integer 更新成功
     */
    const SUCCESS_UPDATE               = 100012;

    /**
     * @var integer 删除成功
     */
    const SUCCESS_DELETE               = 100013;

    /**
     * @var integer 恢复成功
     */
    const SUCCESS_RESTORE              = 100014;

    /**
     * @var integer 保存成功
     */
    const SUCCESS_REPLACE              = 100015;

    /**
     * @var integer 查询失败，提交内容有误
     */
    const ERROR_ARGS_SELECT            = 100020;

    /**
     * @var integer 添加失败，提交内容有误
     */
    const ERROR_ARGS_INSERT            = 100021;

    /**
     * @var integer 更新失败，提交内容有误
     */
    const ERROR_ARGS_UPDATE            = 100022;

    /**
     * @var integer 删除失败，提交内容有误
     */
    const ERROR_ARGS_DELETE            = 100023;

    /**
     * @var integer 恢复失败，提交内容有误
     */
    const ERROR_ARGS_RESTORE           = 100024;

    /**
     * @var integer 保存失败，提交内容有误
     */
    const ERROR_ARGS_REPLACE           = 100025;

    /**
     * @var integer 查询失败，数据库操作失败
     */
    const ERROR_DB_SELECT              = 100030;

    /**
     * @var integer 添加失败，数据库操作失败
     */
    const ERROR_DB_INSERT              = 100031;

    /**
     * @var integer 更新失败，数据库操作失败
     */
    const ERROR_DB_UPDATE              = 100032;

    /**
     * @var integer 删除失败，数据库操作失败
     */
    const ERROR_DB_DELETE              = 100033;

    /**
     * @var integer 恢复失败，数据库操作失败
     */
    const ERROR_DB_RESTORE             = 100034;

    /**
     * @var integer 保存失败，数据库操作失败
     */
    const ERROR_DB_REPLACE             = 100035;

    /**
     * @var integer 查询数据库成功，但是查询结果为空
     */
    const ERROR_DB_SELECT_EMPTY        = 100036;

    /**
     * @var integer 更新数据库成功，但是影响行数为零
     */
    const ERROR_DB_AFFECTS_ZERO        = 100037;

    /**
     * @var integer 查询失败，文件操作失败
     */
    const ERROR_FILE_SELECT            = 100040;

    /**
     * @var integer 添加失败，文件操作失败
     */
    const ERROR_FILE_INSERT            = 100041;

    /**
     * @var integer 更新失败，文件操作失败
     */
    const ERROR_FILE_UPDATE            = 100042;

    /**
     * @var integer 删除失败，文件操作失败
     */
    const ERROR_FILE_DELETE            = 100043;

    /**
     * @var integer 保存失败，文件操作失败
     */
    const ERROR_FILE_REPLACE           = 100044;

    /**
     * @var integer 查询文件成功，但是查询结果为空
     */
    const ERROR_FILE_SELECT_EMPTY      = 100045;

    /**
     * @var integer 查询失败，缓存操作失败
     */
    const ERROR_CACHE_SELECT           = 100050;

    /**
     * @var integer 添加失败，缓存操作失败
     */
    const ERROR_CACHE_INSERT           = 100051;

    /**
     * @var integer 更新失败，缓存操作失败
     */
    const ERROR_CACHE_UPDATE           = 100052;

    /**
     * @var integer 删除失败，缓存操作失败
     */
    const ERROR_CACHE_DELETE           = 100053;

    /**
     * @var integer 保存失败，缓存操作失败
     */
    const ERROR_CACHE_REPLACE          = 100054;

    /**
     * @var integer 查询缓存成功，但是查询结果为空
     */
    const ERROR_CACHE_SELECT_EMPTY     = 100055;

}
