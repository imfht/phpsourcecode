/*
  +----------------------------------------------------------------------+
  |  php_snowflake  https://github.com/Sxdd/php_snowflake  |
  +----------------------------------------------------------------------+
  | Copyright (c) 1997-2016 The PHP Group                                |
  +----------------------------------------------------------------------+
  | This source file is subject to version 3.01 of the PHP license,      |
  | that is bundled with this package in the file LICENSE, and is        |
  | available through the world-wide-web at the following url:           |
  | http://www.php.net/license/3_01.txt                                  |
  | If you did not receive a copy of the PHP license and are unable to   |
  | obtain it through the world-wide-web, please send a note to          |
  | license@php.net so we can mail you a copy immediately.               |
  +----------------------------------------------------------------------+
  | Author:  Towers   (sxddwuwang@gmail.com)       |
  +----------------------------------------------------------------------+
*/

/* $Id$ */

#ifndef PHP_SNOWFLAKE_H
#define PHP_SNOWFLAKE_H

#ifdef ZTS
#include "TSRM.h"
#endif

extern zend_module_entry php_snowflake_module_entry;
#define phpext_php_snowflake_ptr &php_snowflake_module_entry

#define PHP_SNOWFLAKE_VERSION "1.0.1"

#ifdef PHP_WIN32
# define PHP_SNOWFLAKE_API __declspec(dllexport)
#elif defined(__GNUC__) && __GNUC__ >= 4
# define PHP_SNOWFLAKE_API __attribute__ ((visibility("default")))
#else
# define PHP_SNOWFLAKE_API
#endif

# ifdef ZTS
#  define PHP_SNOWFLAKE_G(v) TSRMG(php_snowflake_globals_id, zend_php_snowflake_globals *, v)
# else
#  define PHP_SNOWFLAKE_G(v) (php_snowflake_globals.v)
# endif

/* Always refer to the globals in your function as PHP_SNOWFLAKE_G(variable).
   You are encouraged to rename these macros something shorter, see
   examples in any other php module directory.
*/
#if PHP_API_VERSION < 20151012

# if defined(__x86_64__) || defined(__LP64__) || defined(_LP64) || defined(_WIN64)
typedef int64_t zend_long;
# else
typedef int32_t zend_long;
# endif

#else

# if defined(ZTS) && defined(COMPILE_DL_PHP_SNOWFLAKE)
ZEND_TSRMLS_CACHE_EXTERN()
# endif

#endif

// IdWorker Struct
ZEND_BEGIN_MODULE_GLOBALS(php_snowflake)
  zend_long worker_id;
  zend_long service_no;
  zend_long last_time_stamp;
  unsigned int sequence;
ZEND_END_MODULE_GLOBALS(php_snowflake)

ZEND_EXTERN_MODULE_GLOBALS(php_snowflake);

#endif  /* PHP_PHP_SNOWFLAKE_H */

/*
 * Local variables:
 * tab-width: 4
 * c-basic-offset: 4
 * End:
 * vim600: noet sw=4 ts=4 fdm=marker
 * vim<600: noet sw=4 ts=4
 */
