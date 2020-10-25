dnl $Id$
dnl config.m4 for extension php_snowflake

dnl Comments in this file start with the string 'dnl'.
dnl Remove where necessary. This file will not work
dnl without editing.

dnl If your extension references something external, use with:

PHP_ARG_WITH(php_snowflake, for php_snowflake support,
dnl Make sure that the comment is aligned:
[  --with-php_snowflake             Include php_snowflake support])

dnl Otherwise use enable:

dnl PHP_ARG_ENABLE(php_snowflake, whether to enable php_snowflake support,
dnl Make sure that the comment is aligned:
dnl [  --enable-php_snowflake           Enable php_snowflake support])

if test "$PHP_PHP_SNOWFLAKE" != "no"; then
  dnl Write more examples of tests here...

  dnl # --with-php_snowflake -> check with-path
  dnl SEARCH_PATH="/usr/local /usr"     # you might want to change this
  dnl SEARCH_FOR="/include/php_snowflake.h"  # you most likely want to change this
  dnl if test -r $PHP_PHP_SNOWFLAKE/$SEARCH_FOR; then # path given as parameter
  dnl   PHP_SNOWFLAKE_DIR=$PHP_PHP_SNOWFLAKE
  dnl else # search default path list
  dnl   AC_MSG_CHECKING([for php_snowflake files in default path])
  dnl   for i in $SEARCH_PATH ; do
  dnl     if test -r $i/$SEARCH_FOR; then
  dnl       PHP_SNOWFLAKE_DIR=$i
  dnl       AC_MSG_RESULT(found in $i)
  dnl     fi
  dnl   done
  dnl fi
  dnl
  dnl if test -z "$PHP_SNOWFLAKE_DIR"; then
  dnl   AC_MSG_RESULT([not found])
  dnl   AC_MSG_ERROR([Please reinstall the php_snowflake distribution])
  dnl fi

  dnl # --with-php_snowflake -> add include path
  dnl PHP_ADD_INCLUDE($PHP_SNOWFLAKE_DIR/include)

  dnl # --with-php_snowflake -> check for lib and symbol presence
  dnl LIBNAME=php_snowflake # you may want to change this
  dnl LIBSYMBOL=php_snowflake # you most likely want to change this 

  dnl PHP_CHECK_LIBRARY($LIBNAME,$LIBSYMBOL,
  dnl [
  dnl   PHP_ADD_LIBRARY_WITH_PATH($LIBNAME, $PHP_SNOWFLAKE_DIR/$PHP_LIBDIR, PHP_SNOWFLAKE_SHARED_LIBADD)
  dnl   AC_DEFINE(HAVE_PHP_SNOWFLAKELIB,1,[ ])
  dnl ],[
  dnl   AC_MSG_ERROR([wrong php_snowflake lib version or lib not found])
  dnl ],[
  dnl   -L$PHP_SNOWFLAKE_DIR/$PHP_LIBDIR -lm
  dnl ])
  dnl
  dnl PHP_SUBST(PHP_SNOWFLAKE_SHARED_LIBADD)

  PHP_NEW_EXTENSION(php_snowflake, php_snowflake.c, $ext_shared,, -DZEND_ENABLE_STATIC_TSRMLS_CACHE=1)
fi
