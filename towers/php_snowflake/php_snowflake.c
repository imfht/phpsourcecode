/*
  +----------------------------------------------------------------------+
  | php_snowflake  https://github.com/Sxdd/php_snowflake   |
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

#ifdef HAVE_CONFIG_H
#include "config.h"
#endif

#define MAX_SEQUENCE 8191

#include <unistd.h>
#include <string.h>
#include <sys/time.h>
#include <stdio.h>
#include <sys/syscall.h>
#include "php.h"
#include "php_ini.h"
#include "ext/standard/info.h"
#include "php_snowflake.h"

/* If you declare any globals in php_snowflake.h uncomment this:*/
ZEND_DECLARE_MODULE_GLOBALS(php_snowflake)

zend_class_entry *php_snowflake_ce;

static zend_long time_re_gen(zend_long last) {
	zend_long new_time;
	struct timeval tv;
	do {
		gettimeofday(&tv, 0);
		new_time = (zend_long)tv.tv_sec * 1000 + (zend_long)tv.tv_usec / 1000;
	} while (new_time <= last);

	return new_time;
}

static zend_long get_workid() {
	return syscall(SYS_gettid); 
}

static zend_long time_gen() {
	struct timeval tv;
	gettimeofday(&tv, 0);
	return (zend_long)tv.tv_sec * 1000 + (zend_long)tv.tv_usec / 1000;
}

static void next_id(char *id) {
#if PHP_API_VERSION < 20151012
	TSRMLS_FETCH();
#endif
	zend_long ts;

	ts = time_gen();

	if (ts == PHP_SNOWFLAKE_G(last_time_stamp)) {
		PHP_SNOWFLAKE_G(sequence) = (PHP_SNOWFLAKE_G(sequence)+1) & MAX_SEQUENCE;
		if (PHP_SNOWFLAKE_G(sequence) == 0) {
			ts = time_re_gen(ts);
		}
	} else {
		PHP_SNOWFLAKE_G(last_time_stamp) = ts;
		PHP_SNOWFLAKE_G(sequence) = 1;
	}

	if (ts < PHP_SNOWFLAKE_G(last_time_stamp)) {
		strcpy(id, NULL);
	} else {
		sprintf(id, "00%ld%05ld%08ld%04d", ts, PHP_SNOWFLAKE_G(service_no), PHP_SNOWFLAKE_G(worker_id), PHP_SNOWFLAKE_G(sequence));
	}
}

PHP_METHOD(PhpSnowFlake, nextId) {
	char id[33];

	if (zend_parse_parameters(ZEND_NUM_ARGS() 
#if PHP_API_VERSION < 20151012
		TSRMLS_CC
#endif
		, "l", &PHP_SNOWFLAKE_G(service_no)) == FAILURE) {
		RETURN_FALSE;
	}

	if (PHP_SNOWFLAKE_G(service_no)>99999 | PHP_SNOWFLAKE_G(service_no)<0) {
		zend_error(E_ERROR, "service_no in the range of 0,99999");
	}

	next_id(id);

	if (id == NULL) {
		RETURN_FALSE;
	} else {
#if PHP_API_VERSION < 20151012
		RETURN_STRINGL(id,strlen(id), 1);
#else
		RETURN_STRINGL(id,strlen(id));
#endif
	}
}

/* }}} */
/* The previous line is meant for vim and emacs, so it can correctly fold and
   unfold functions in source code. See the corresponding marks just before
   function definition, where the functions purpose is also documented. Please
   follow this convention for the convenience of others editing your code.
*/

/* {{{ ARG_INFO
 */
ZEND_BEGIN_ARG_INFO_EX(php_snowflake_next_id, 0, 0, 1)
	ZEND_ARG_INFO(0, service_no)
ZEND_END_ARG_INFO()
/* }}} */

/* {{{ php_php_snowflake_init_globals
 */
/* Uncomment this function if you have INI entries
static void php_php_snowflake_init_globals(zend_php_snowflake_globals *php_snowflake_globals)
{
	php_snowflake_globals->global_value = 0;
	php_snowflake_globals->global_string = NULL;
}
*/
/* }}} */


/* {{{ php_snowflake_functions[]
 *
 * Every user visible function must have an entry in php_snowflake_methods[].
 */
const zend_function_entry php_snowflake_methods[] = {
	PHP_ME(PhpSnowFlake, nextId, php_snowflake_next_id, ZEND_ACC_PUBLIC | ZEND_ACC_STATIC)
	PHP_FE_END
};

#if PHP_API_VERSION < 20151012
zend_function_entry php_snowflake_functions[] = {
	PHP_FE_END
};
#else
#define php_snowflake_functions NULL
#endif
/* }}} */


static void php_snowflake_ctor(
#if PHP_API_VERSION < 20151012
	zend_php_snowflake_globals *iw TSRMLS_DC
#else
	zend_php_snowflake_globals *iw
#endif
)
{
	iw->sequence = 0;
#ifdef ZTS
	iw->worker_id = get_workid();
#endif
}

/* {{{ PHP_MINIT_FUNCTION
 */
PHP_MINIT_FUNCTION(php_snowflake)
{

#ifdef ZTS
	ts_allocate_id(&php_snowflake_globals_id, sizeof(zend_php_snowflake_globals), (ts_allocate_ctor)php_snowflake_ctor, NULL);
#else	
# if PHP_API_VERSION < 20151012
	php_snowflake_ctor(&php_snowflake_globals TSRMLS_CC);
# else
	php_snowflake_ctor(&php_snowflake_globals);
# endif
#endif

	zend_class_entry ce;
	INIT_CLASS_ENTRY(ce, "PhpSnowFlake", php_snowflake_methods);
#if PHP_API_VERSION < 20151012
	php_snowflake_ce = zend_register_internal_class(&ce TSRMLS_CC);
#else
	php_snowflake_ce = zend_register_internal_class_ex(&ce, NULL);
#endif
	/* If you have INI entries, uncomment these lines
	REGISTER_INI_ENTRIES();
	*/
	return SUCCESS;
}
/* }}} */



/* {{{ PHP_MSHUTDOWN_FUNCTION
 */
PHP_MSHUTDOWN_FUNCTION(php_snowflake)
{

	/* uncomment this line if you have INI entries
	UNREGISTER_INI_ENTRIES();
	*/
	return SUCCESS;
}
/* }}} */

/* Remove if there's nothing to do at request start */
/* {{{ PHP_RINIT_FUNCTION
 */
PHP_RINIT_FUNCTION(php_snowflake)
{
#ifndef ZTS
	if (!PHP_SNOWFLAKE_G(worker_id)) {
		PHP_SNOWFLAKE_G(worker_id) = get_workid();
	}
#endif	
#if defined(COMPILE_DL_PHP_SNOWFLAKE) && defined(ZTS) && PHP_API_VERSION >= 20151012
	ZEND_TSRMLS_CACHE_UPDATE();
#endif
	return SUCCESS;
}
/* }}} */

/* Remove if there's nothing to do at request end */
/* {{{ PHP_RSHUTDOWN_FUNCTION
 */
PHP_RSHUTDOWN_FUNCTION(php_snowflake)
{
	return SUCCESS;
}
/* }}} */

/* {{{ PHP_MINFO_FUNCTION
 */
PHP_MINFO_FUNCTION(php_snowflake)
{
	php_info_print_table_start();
	php_info_print_table_header(2, "php_snowflake", "enabled");
	php_info_print_table_row(2, "version", PHP_SNOWFLAKE_VERSION); 
	php_info_print_table_end();

	/* Remove comments if you have entries in php.ini
	DISPLAY_INI_ENTRIES();
	*/
}
/* }}} */

/* {{{ php_snowflake_module_entry
 */
zend_module_entry php_snowflake_module_entry = {
	STANDARD_MODULE_HEADER,
	"php_snowflake",
	php_snowflake_functions,
	PHP_MINIT(php_snowflake),
	PHP_MSHUTDOWN(php_snowflake),
	PHP_RINIT(php_snowflake),		/* Replace with NULL if there's nothing to do at request start */
	PHP_RSHUTDOWN(php_snowflake),	/* Replace with NULL if there's nothing to do at request end */
	PHP_MINFO(php_snowflake),
	PHP_SNOWFLAKE_VERSION,
	STANDARD_MODULE_PROPERTIES
};
/* }}} */

#ifdef COMPILE_DL_PHP_SNOWFLAKE
#if defined(ZTS) && PHP_API_VERSION >= 20151012
ZEND_TSRMLS_CACHE_DEFINE()
#endif
ZEND_GET_MODULE(php_snowflake)
#endif

/*
 * Local variables:
 * tab-width: 4
 * c-basic-offset: 4
 * End:
 * vim600: noet sw=4 ts=4 fdm=marker
 * vim<600: noet sw=4 ts=4
 */
