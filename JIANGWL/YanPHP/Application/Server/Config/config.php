<?php
defined('BASE_PATH') OR exit('No direct script access allowed');
/**
 * YanPHP
 * User: weilongjiang(江炜隆)<willliam@jwlchina.cn>
 */

/**
 *  -------------------------------------------------------------
 * |System config                                                |
 *  -------------------------------------------------------------
 */
$config['namespace'] = 'App\\Server';


/**
 *  -------------------------------------------------------------
 * |Log config                                                   |
 *  -------------------------------------------------------------
 *
 * Logger::DEBUG => 'DEBUG',
 * Logger::INFO => 'INFO',
 * Logger::NOTICE => 'NOTICE',
 * Logger::WARNING => 'WARNING',
 * Logger::ERROR => 'ERROR',
 * Logger::CRITICAL => 'CRITICAL',
 * Logger::ALERT => 'ALERT',
 * Logger::EMERGENCY => 'EMERGENCY',
 */
$config['log_level'] = 'DEBUG';
/**
 * The log path
 */
$config['log_path'] = BASE_PATH . '/logs/server.log';
/**
 *  Max file num, default 0(not limited)
 */
$config['log_max_file'] = 0;
/**
 * Configure log format
 * "[%datetime%] %channel%.%level_name%: %message% %context%\n";
 */
$config['log_format'] = "[%datetime%]-%extra.process_id% %channel%.%level_name%: %message% %context%\n";

/**
 *  -------------------------------------------------------------
 * |Session config                                               |
 *  -------------------------------------------------------------
 *
 */
/**
 * session_path leave blank to use default
 */
$config['session_path'] = BASE_PATH.'/Cache/session';
$config['session_name'] = 'YAN_SESSION';