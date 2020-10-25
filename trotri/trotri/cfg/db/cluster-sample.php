<?php
/**
 * Trotri
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright &copy; 2011-2013 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

return array (
  'trotri' => array (
    'dsn' => 'mysql:host=数据库主机-通常是localhost;dbname=数据库名',
    'username' => '数据库用户名',
    'password' => '数据库密码',
    'charset' => 'utf8',
    'retry' => 3,
    'tblprefix' => '数据表前缀'
  ),
);

/**
 * 示例：
 * return array (
 *   'trotri' => array (
 *     'dsn' => 'mysql:host=localhost;dbname=trotri',
 *     'username' => 'root',
 *     'password' => '123456',
 *     'charset' => 'utf8',
 *     'retry' => 3,
 *     'tblprefix' => 'tr_'
 *   ),
 * );
 */
