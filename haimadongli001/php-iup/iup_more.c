/*
  +----------------------------------------------------------------------+
  | PHP Version 7                                                        |
  +----------------------------------------------------------------------+
  | Copyright (c) 1997-2018 The PHP Group                                |
  +----------------------------------------------------------------------+
  | This source file is subject to version 3.01 of the PHP license,      |
  | that is bundled with this package in the file LICENSE, and is        |
  | available through the world-wide-web at the following url:           |
  | http://www.php.net/license/3_01.txt                                  |
  | If you did not receive a copy of the PHP license and are unable to   |
  | obtain it through the world-wide-web, please send a note to          |
  | license@php.net so we can mail you a copy immediately.               |
  +----------------------------------------------------------------------+
  | Author:                                                              |
  +----------------------------------------------------------------------+
*/

/* $Id$ */

#ifdef HAVE_CONFIG_H
#include "config.h"
#endif

#include "php.h"
#include "php_ini.h"
#include "ext/standard/info.h"
#include "php_iup.h"

extern int le_iup_ihandle;
extern int le_iup_event;

extern HashTable *iup_events;

void config_register_callback(Ihandle* ih, zend_fcall_info * call_p)
{
    zend_string * event_key;

    zval event_val;

    intptr_t ih_p_int;

    char event_key_str[100];

    ih_p_int = (intptr_t)ih;

    sprintf(event_key_str,"IUP_%s_%"SCNiPTR,"RECENT_CB",ih_p_int);

    event_key = zend_string_init(event_key_str, strlen(event_key_str), 0);

    ZVAL_RES(&event_val,zend_register_resource(call_p, le_iup_event));

    zend_hash_update(iup_events, event_key, &event_val);
}

int config_recent_cb(Ihandle* ih)
{
  return event_common("RECENT_CB",ih);
}

/* {{{ proto void IupConfig()
    */
PHP_FUNCTION(IupConfig)
{
    Ihandle *re;

    if (zend_parse_parameters_none() == FAILURE) {
        return;
    }

    re = IupConfig();

    RETURN_RES(zend_register_resource(re, le_iup_ihandle));
}
/* }}} */

/* {{{ proto int IupConfigLoad(resource ih)
   ;
 */
PHP_FUNCTION(IupConfigLoad)
{
    int argc = ZEND_NUM_ARGS();

    zval *ihandle_res = NULL;

    Ihandle *ih;

    int re;

    if (zend_parse_parameters(argc TSRMLS_DC,"r",&ihandle_res) == FAILURE) {
        return;
    }

    ih = zend_fetch_resource_ex(ihandle_res,"iup-handle",le_iup_ihandle);

    re = IupConfigLoad(ih);

    RETURN_LONG(re);
}
/* }}} */

/* {{{ proto int IupConfigSave(resource ih)
   ;
 */
PHP_FUNCTION(IupConfigSave)
{
    int argc = ZEND_NUM_ARGS();

    zval *ihandle_res = NULL;

    Ihandle *ih;

    int re;

    if (zend_parse_parameters(argc TSRMLS_DC,"r",&ihandle_res) == FAILURE) {
        return;
    }

    ih = zend_fetch_resource_ex(ihandle_res,"iup-handle",le_iup_ihandle);

    re = IupConfigSave(ih);

    RETURN_LONG(re);
}
/* }}} */


/* {{{ proto resource IupConfigSetVariableStr(resource ih, string group, string key, string value)
   ;
 */
PHP_FUNCTION(IupConfigSetVariableStr)
{
    int argc = ZEND_NUM_ARGS();

    zval *ihandle_res = NULL;

    Ihandle *ih;

    char *group = NULL;
    size_t group_len;

    char *key = NULL;
    size_t key_len;

    char *value = NULL;
    size_t value_len;

    if (zend_parse_parameters(argc TSRMLS_DC,"r!sss!",&ihandle_res,&group,&group_len,&key,&key_len,&value,&value_len) == FAILURE) {
        return;
    }

    if(ihandle_res != NULL){
        ih = zend_fetch_resource_ex(ihandle_res,"iup-handle",le_iup_ihandle);
    }else{
        ih = NULL;
    }

    IupConfigSetVariableStr(ih,group,key,value);

    RETURN_BOOL(1);
}
/* }}} */

/* {{{ proto resource IupConfigSetVariableStrId(resource ih, string group, string key, int id, string value)
   ;
 */
PHP_FUNCTION(IupConfigSetVariableStrId)
{
    int argc = ZEND_NUM_ARGS();

    zval *ihandle_res = NULL;

    Ihandle *ih;

    char *group = NULL;
    size_t group_len;

    char *key = NULL;
    size_t key_len;

    zend_long id;

    char *value = NULL;
    size_t value_len;

    if (zend_parse_parameters(argc TSRMLS_DC,"r!ssls!",&ihandle_res,&group,&group_len,&key,&key_len,&id,&value,&value_len) == FAILURE) {
        return;
    }

    if(ihandle_res != NULL){
        ih = zend_fetch_resource_ex(ihandle_res,"iup-handle",le_iup_ihandle);
    }else{
        ih = NULL;
    }

    IupConfigSetVariableStrId(ih,group,key,id,value);

    RETURN_BOOL(1);
}
/* }}} */

/* {{{ proto resource IupConfigSetVariableInt(resource ih, string group, string key, int value)
   ;
 */
PHP_FUNCTION(IupConfigSetVariableInt)
{
    int argc = ZEND_NUM_ARGS();

    zval *ihandle_res = NULL;

    Ihandle *ih;

    char *group = NULL;
    size_t group_len;

    char *key = NULL;
    size_t key_len;

    zend_long value;

    if (zend_parse_parameters(argc TSRMLS_DC,"r!ssl!",&ihandle_res,&group,&group_len,&key,&key_len,&value) == FAILURE) {
        return;
    }

    if(ihandle_res != NULL){
        ih = zend_fetch_resource_ex(ihandle_res,"iup-handle",le_iup_ihandle);
    }else{
        ih = NULL;
    }

    IupConfigSetVariableInt(ih,group,key,value);

    RETURN_BOOL(1);
}
/* }}} */

/* {{{ proto resource IupConfigSetVariableIntId(resource ih, string group, string key, int id, int value)
   ;
 */
PHP_FUNCTION(IupConfigSetVariableIntId)
{
    int argc = ZEND_NUM_ARGS();

    zval *ihandle_res = NULL;

    Ihandle *ih;

    char *group = NULL;
    size_t group_len;

    char *key = NULL;
    size_t key_len;

    zend_long id,value;

    if (zend_parse_parameters(argc TSRMLS_DC,"r!ssll!",&ihandle_res,&group,&group_len,&key,&key_len,&id,&value) == FAILURE) {
        return;
    }

    if(ihandle_res != NULL){
        ih = zend_fetch_resource_ex(ihandle_res,"iup-handle",le_iup_ihandle);
    }else{
        ih = NULL;
    }

    IupConfigSetVariableIntId(ih,group,key,id,value);

    RETURN_BOOL(1);
}
/* }}} */

/* {{{ proto resource IupConfigSetVariableDouble(resource ih, string group, string key, double value)
   ;
 */
PHP_FUNCTION(IupConfigSetVariableDouble)
{
    int argc = ZEND_NUM_ARGS();

    zval *ihandle_res = NULL;

    Ihandle *ih;

    char *group = NULL;
    size_t group_len;

    char *key = NULL;
    size_t key_len;

    double value;

    if (zend_parse_parameters(argc TSRMLS_DC,"r!ssd!",&ihandle_res,&group,&group_len,&key,&key_len,&value) == FAILURE) {
        return;
    }

    if(ihandle_res != NULL){
        ih = zend_fetch_resource_ex(ihandle_res,"iup-handle",le_iup_ihandle);
    }else{
        ih = NULL;
    }

    IupConfigSetVariableDouble(ih,group,key,value);

    RETURN_BOOL(1);
}
/* }}} */

/* {{{ proto resource IupConfigSetVariableDoubleId(resource ih, string group, string key, int id, double value)
   ;
 */
PHP_FUNCTION(IupConfigSetVariableDoubleId)
{
    int argc = ZEND_NUM_ARGS();

    zval *ihandle_res = NULL;

    Ihandle *ih;

    char *group = NULL;
    size_t group_len;

    char *key = NULL;
    size_t key_len;

    zend_long id;
    double value;

    if (zend_parse_parameters(argc TSRMLS_DC,"r!ssld!",&ihandle_res,&group,&group_len,&key,&key_len,&id,&value) == FAILURE) {
        return;
    }

    if(ihandle_res != NULL){
        ih = zend_fetch_resource_ex(ihandle_res,"iup-handle",le_iup_ihandle);
    }else{
        ih = NULL;
    }

    IupConfigSetVariableDoubleId(ih,group,key,id,value);

    RETURN_BOOL(1);
}
/* }}} */


/* {{{ proto resource IupConfigGetVariableStr(resource ih, string group, string key)
   ;
 */
PHP_FUNCTION(IupConfigGetVariableStr)
{
    int argc = ZEND_NUM_ARGS();

    zval *ihandle_res = NULL;

    Ihandle *ih;

    char *group = NULL;
    size_t group_len;

    char *key = NULL;
    size_t key_len;

    char * str;

    if (zend_parse_parameters(argc TSRMLS_DC,"r!ss",&ihandle_res,&group,&group_len,&key,&key_len) == FAILURE) {
        return;
    }

    if(ihandle_res != NULL){
        ih = zend_fetch_resource_ex(ihandle_res,"iup-handle",le_iup_ihandle);
    }else{
        ih = NULL;
    }

    str = IupConfigGetVariableStr(ih,group,key);

    if(str != NULL){
        RETURN_STRING(str);
    }

    RETURN_NULL();
}
/* }}} */

/* {{{ proto resource IupConfigGetVariableStrId(resource ih, string group, string key, int id)
   ;
 */
PHP_FUNCTION(IupConfigGetVariableStrId)
{
    int argc = ZEND_NUM_ARGS();

    zval *ihandle_res = NULL;

    Ihandle *ih;

    char *group = NULL;
    size_t group_len;

    char *key = NULL;
    size_t key_len;

    zend_long id;

    char * str;

    if (zend_parse_parameters(argc TSRMLS_DC,"r!ssl",&ihandle_res,&group,&group_len,&key,&key_len,&id) == FAILURE) {
        return;
    }

    if(ihandle_res != NULL){
        ih = zend_fetch_resource_ex(ihandle_res,"iup-handle",le_iup_ihandle);
    }else{
        ih = NULL;
    }

    str = IupConfigGetVariableStrId(ih,group,key,id);

    if(str != NULL){
        RETURN_STRING(str);
    }

    RETURN_NULL();
}
/* }}} */

/* {{{ proto resource IupConfigGetVariableInt(resource ih, string group, string key)
   ;
 */
PHP_FUNCTION(IupConfigGetVariableInt)
{
    int argc = ZEND_NUM_ARGS();

    zval *ihandle_res = NULL;

    Ihandle *ih;

    char *group = NULL;
    size_t group_len;

    char *key = NULL;
    size_t key_len;

    int re;

    if (zend_parse_parameters(argc TSRMLS_DC,"r!ss",&ihandle_res,&group,&group_len,&key,&key_len) == FAILURE) {
        return;
    }

    if(ihandle_res != NULL){
        ih = zend_fetch_resource_ex(ihandle_res,"iup-handle",le_iup_ihandle);
    }else{
        ih = NULL;
    }

    re = IupConfigGetVariableInt(ih,group,key);

    RETURN_LONG(re);
}
/* }}} */

/* {{{ proto resource IupConfigGetVariableIntId(resource ih, string group, string key, int id)
   ;
 */
PHP_FUNCTION(IupConfigGetVariableIntId)
{
    int argc = ZEND_NUM_ARGS();

    zval *ihandle_res = NULL;

    Ihandle *ih;

    char *group = NULL;
    size_t group_len;

    char *key = NULL;
    size_t key_len;

    zend_long id;

    int re;

    if (zend_parse_parameters(argc TSRMLS_DC,"r!ssl",&ihandle_res,&group,&group_len,&key,&key_len,&id) == FAILURE) {
        return;
    }

    if(ihandle_res != NULL){
        ih = zend_fetch_resource_ex(ihandle_res,"iup-handle",le_iup_ihandle);
    }else{
        ih = NULL;
    }

    re = IupConfigGetVariableIntId(ih,group,key,id);

    RETURN_LONG(re);
}
/* }}} */

/* {{{ proto resource IupConfigGetVariableDouble(resource ih, string group, string key)
   ;
 */
PHP_FUNCTION(IupConfigGetVariableDouble)
{
    int argc = ZEND_NUM_ARGS();

    zval *ihandle_res = NULL;

    Ihandle *ih;

    char *group = NULL;
    size_t group_len;

    char *key = NULL;
    size_t key_len;

    double d;

    if (zend_parse_parameters(argc TSRMLS_DC,"r!ss",&ihandle_res,&group,&group_len,&key,&key_len) == FAILURE) {
        return;
    }

    if(ihandle_res != NULL){
        ih = zend_fetch_resource_ex(ihandle_res,"iup-handle",le_iup_ihandle);
    }else{
        ih = NULL;
    }

    d = IupConfigGetVariableDouble(ih,group,key);

    RETURN_DOUBLE(d);
}
/* }}} */

/* {{{ proto resource IupConfigGetVariableDoubleId(resource ih, string group, string key, int id)
   ;
 */
PHP_FUNCTION(IupConfigGetVariableDoubleId)
{
    int argc = ZEND_NUM_ARGS();

    zval *ihandle_res = NULL;

    Ihandle *ih;

    char *group = NULL;
    size_t group_len;

    char *key = NULL;
    size_t key_len;

    zend_long id;

    double d;

    if (zend_parse_parameters(argc TSRMLS_DC,"r!ssl",&ihandle_res,&group,&group_len,&key,&key_len,&id) == FAILURE) {
        return;
    }

    if(ihandle_res != NULL){
        ih = zend_fetch_resource_ex(ihandle_res,"iup-handle",le_iup_ihandle);
    }else{
        ih = NULL;
    }

    d = IupConfigGetVariableDoubleId(ih,group,key,id);

    RETURN_DOUBLE(d);
}
/* }}} */



/* {{{ proto resource IupConfigGetVariableStrDef(resource ih, string group, string key, string def)
   ;
 */
PHP_FUNCTION(IupConfigGetVariableStrDef)
{
    int argc = ZEND_NUM_ARGS();

    zval *ihandle_res = NULL;

    Ihandle *ih;

    char *group = NULL;
    size_t group_len;

    char *key = NULL;
    size_t key_len;
    char *def = NULL;
    size_t def_len;

    char * str;

    if (zend_parse_parameters(argc TSRMLS_DC,"r!ssd",&ihandle_res,&group,&group_len,&key,&key_len,&def,&def_len) == FAILURE) {
        return;
    }

    if(ihandle_res != NULL){
        ih = zend_fetch_resource_ex(ihandle_res,"iup-handle",le_iup_ihandle);
    }else{
        ih = NULL;
    }

    str = IupConfigGetVariableStrDef(ih,group,key,def);

    if(str != NULL){
        RETURN_STRING(str);
    }

    RETURN_NULL();
}
/* }}} */

/* {{{ proto resource IupConfigGetVariableStrIdDef(resource ih, string group, string key, int id, string def)
   ;
 */
PHP_FUNCTION(IupConfigGetVariableStrIdDef)
{
    int argc = ZEND_NUM_ARGS();

    zval *ihandle_res = NULL;

    Ihandle *ih;

    char *group = NULL;
    size_t group_len;

    char *key = NULL;
    size_t key_len;

    char *def = NULL;
    size_t def_len;

    zend_long id;

    char * str;

    if (zend_parse_parameters(argc TSRMLS_DC,"r!ssld",&ihandle_res,&group,&group_len,&key,&key_len,&id,&def,&def_len) == FAILURE) {
        return;
    }

    if(ihandle_res != NULL){
        ih = zend_fetch_resource_ex(ihandle_res,"iup-handle",le_iup_ihandle);
    }else{
        ih = NULL;
    }

    str = IupConfigGetVariableStrIdDef(ih,group,key,id,def);

    if(str != NULL){
        RETURN_STRING(str);
    }

    RETURN_NULL();
}
/* }}} */

/* {{{ proto resource IupConfigGetVariableIntDef(resource ih, string group, string key, int def)
   ;
 */
PHP_FUNCTION(IupConfigGetVariableIntDef)
{
    int argc = ZEND_NUM_ARGS();

    zval *ihandle_res = NULL;

    Ihandle *ih;

    char *group = NULL;
    size_t group_len;

    char *key = NULL;
    size_t key_len;

    zend_long def;

    int re;

    if (zend_parse_parameters(argc TSRMLS_DC,"r!ssd",&ihandle_res,&group,&group_len,&key,&key_len,&def) == FAILURE) {
        return;
    }

    if(ihandle_res != NULL){
        ih = zend_fetch_resource_ex(ihandle_res,"iup-handle",le_iup_ihandle);
    }else{
        ih = NULL;
    }

    re = IupConfigGetVariableIntDef(ih,group,key,def);

    RETURN_LONG(re);
}
/* }}} */

/* {{{ proto resource IupConfigGetVariableIntIdDef(resource ih, string group, string key, int id)
   ;
 */
PHP_FUNCTION(IupConfigGetVariableIntIdDef)
{
    int argc = ZEND_NUM_ARGS();

    zval *ihandle_res = NULL;

    Ihandle *ih;

    char *group = NULL;
    size_t group_len;

    char *key = NULL;
    size_t key_len;

    zend_long id,def;

    int re;

    if (zend_parse_parameters(argc TSRMLS_DC,"r!ssld",&ihandle_res,&group,&group_len,&key,&key_len,&id,&def) == FAILURE) {
        return;
    }

    if(ihandle_res != NULL){
        ih = zend_fetch_resource_ex(ihandle_res,"iup-handle",le_iup_ihandle);
    }else{
        ih = NULL;
    }

    re = IupConfigGetVariableIntIdDef(ih,group,key,id,def);

    RETURN_LONG(re);
}
/* }}} */

/* {{{ proto resource IupConfigGetVariableDoubleDef(resource ih, string group, string key, double def)
   ;
 */
PHP_FUNCTION(IupConfigGetVariableDoubleDef)
{
    int argc = ZEND_NUM_ARGS();

    zval *ihandle_res = NULL;

    Ihandle *ih;

    char *group = NULL;
    size_t group_len;

    char *key = NULL;
    size_t key_len;

    double def,d;

    if (zend_parse_parameters(argc TSRMLS_DC,"r!ssd",&ihandle_res,&group,&group_len,&key,&key_len,&def) == FAILURE) {
        return;
    }

    if(ihandle_res != NULL){
        ih = zend_fetch_resource_ex(ihandle_res,"iup-handle",le_iup_ihandle);
    }else{
        ih = NULL;
    }

    d = IupConfigGetVariableDoubleDef(ih,group,key,def);

    RETURN_DOUBLE(d);
}
/* }}} */

/* {{{ proto resource IupConfigGetVariableDoubleIdDef(resource ih, string group, string key, int id, double def)
   ;
 */
PHP_FUNCTION(IupConfigGetVariableDoubleIdDef)
{
    int argc = ZEND_NUM_ARGS();

    zval *ihandle_res = NULL;

    Ihandle *ih;

    char *group = NULL;
    size_t group_len;

    char *key = NULL;
    size_t key_len;

    zend_long id;

    double def,d;

    if (zend_parse_parameters(argc TSRMLS_DC,"r!ssld",&ihandle_res,&group,&group_len,&key,&key_len,&id,&def) == FAILURE) {
        return;
    }

    if(ihandle_res != NULL){
        ih = zend_fetch_resource_ex(ihandle_res,"iup-handle",le_iup_ihandle);
    }else{
        ih = NULL;
    }

    d = IupConfigGetVariableDoubleIdDef(ih,group,key,id,def);

    RETURN_DOUBLE(d);
}
/* }}} */

/* {{{ proto resource IupConfigCopy(resource ih1, resource ih2, string exclude_prefix)
   ;
 */
PHP_FUNCTION(IupConfigCopy)
{
    int argc = ZEND_NUM_ARGS();

    zval *ihandle_res_ih1 = NULL;
    zval *ihandle_res_ih2 = NULL;

    Ihandle *ih1,*ih2;

    char *exclude_prefix = NULL;
    size_t exclude_prefix_len;

    if (zend_parse_parameters(argc TSRMLS_DC,"r!r!s",&ihandle_res_ih1,&ihandle_res_ih2,&exclude_prefix,&exclude_prefix_len) == FAILURE) {
        return;
    }

    ih1 = zend_fetch_resource_ex(ihandle_res_ih1,"iup-handle",le_iup_ihandle);
    ih2 = zend_fetch_resource_ex(ihandle_res_ih2,"iup-handle",le_iup_ihandle);

    IupConfigCopy(ih1,ih2,exclude_prefix);

    RETURN_BOOL(1);
}
/* }}} */


/* {{{ proto resource IupConfigSetListVariable(resource ih, string group, string key, string value, int add)
   ;
 */
PHP_FUNCTION(IupConfigSetListVariable)
{
    int argc = ZEND_NUM_ARGS();

    zval *ihandle_res = NULL;

    Ihandle *ih;

    char *group = NULL;
    size_t group_len;

    char *key = NULL;
    size_t key_len;

    char *value = NULL;
    size_t value_len;

    zend_long add;

    if (zend_parse_parameters(argc TSRMLS_DC,"r!sss!l",&ihandle_res,&group,&group_len,&key,&key_len,&value,&value_len,&add) == FAILURE) {
        return;
    }

    if(ihandle_res != NULL){
        ih = zend_fetch_resource_ex(ihandle_res,"iup-handle",le_iup_ihandle);
    }else{
        ih = NULL;
    }

    IupConfigSetListVariable(ih,group,key,value,add);

    RETURN_BOOL(1);
}
/* }}} */

/* {{{ proto resource IupConfigRecentInit(resource ih1, resource ih2, string exclude_prefix)
   ;
 */
PHP_FUNCTION(IupConfigRecentInit)
{
    int argc = ZEND_NUM_ARGS();

    zval *ihandle_res_ih1 = NULL;
    zval *ihandle_res_ih2 = NULL;

    Ihandle *ih1,*ih2;

    zend_fcall_info callable;
    zend_fcall_info_cache call_cache;

    zend_fcall_info * call_p;

    call_p = (zend_fcall_info *)malloc(sizeof(zend_fcall_info));

    zend_long max_recent;

    if (zend_parse_parameters(argc TSRMLS_DC,"rrfl",&ihandle_res_ih1,&ihandle_res_ih2, &callable, &call_cache,&max_recent) == FAILURE) {
        return;
    }

    *call_p = callable;

    ih1 = zend_fetch_resource_ex(ihandle_res_ih1,"iup-handle",le_iup_ihandle);
    ih2 = zend_fetch_resource_ex(ihandle_res_ih2,"iup-handle",le_iup_ihandle);

    // 先注册
    config_register_callback(ih1,call_p);

    IupConfigRecentInit(ih1,ih2,config_recent_cb,max_recent);

    RETURN_BOOL(1);
}
/* }}} */


/* {{{ proto resource IupConfigRecentUpdate(resource ih, string filename)
   ;
 */
PHP_FUNCTION(IupConfigRecentUpdate)
{
    int argc = ZEND_NUM_ARGS();

    zval *ihandle_res = NULL;

    char *filename = NULL;
    size_t filename_len;

    Ihandle *ih;

    if (zend_parse_parameters(argc TSRMLS_DC,"r!s",&ihandle_res,&filename,&filename_len) == FAILURE) {
        return;
    }

    if(ihandle_res != NULL){
        ih = zend_fetch_resource_ex(ihandle_res,"iup-handle",le_iup_ihandle);
    }else{
        ih = NULL;
    }

    IupConfigRecentUpdate(ih,filename);

    RETURN_BOOL(1);
}
/* }}} */


/* {{{ proto resource IupConfigDialogShow(resource ih1, resource ih2, string  name)
   ;
 */
PHP_FUNCTION(IupConfigDialogShow)
{
    int argc = ZEND_NUM_ARGS();

    zval *ihandle_res_ih1 = NULL;
    zval *ihandle_res_ih2 = NULL;

    Ihandle *ih1,*ih2;

    char *name = NULL;
    size_t name_len;

    if (zend_parse_parameters(argc TSRMLS_DC,"r!r!s",&ihandle_res_ih1,&ihandle_res_ih2,&name,&name_len) == FAILURE) {
        return;
    }

    ih1 = zend_fetch_resource_ex(ihandle_res_ih1,"iup-handle",le_iup_ihandle);
    ih2 = zend_fetch_resource_ex(ihandle_res_ih2,"iup-handle",le_iup_ihandle);

    IupConfigDialogShow(ih1,ih2,name);

    RETURN_BOOL(1);
}
/* }}} */

/* {{{ proto resource IupConfigDialogClosed(resource ih1, resource ih2, string  name)
   ;
 */
PHP_FUNCTION(IupConfigDialogClosed)
{
    int argc = ZEND_NUM_ARGS();

    zval *ihandle_res_ih1 = NULL;
    zval *ihandle_res_ih2 = NULL;

    Ihandle *ih1,*ih2;

    char *name = NULL;
    size_t name_len;

    if (zend_parse_parameters(argc TSRMLS_DC,"r!r!s",&ihandle_res_ih1,&ihandle_res_ih2,&name,&name_len) == FAILURE) {
        return;
    }

    ih1 = zend_fetch_resource_ex(ihandle_res_ih1,"iup-handle",le_iup_ihandle);
    ih2 = zend_fetch_resource_ex(ihandle_res_ih2,"iup-handle",le_iup_ihandle);

    IupConfigDialogClosed(ih1,ih2,name);

    RETURN_BOOL(1);
}
/* }}} */
#ifdef PHP_IUP_SCINTILLA
/* {{{ proto void IupScintillaOpen()
    */
PHP_FUNCTION(IupScintillaOpen)
{
    if (zend_parse_parameters_none() == FAILURE) {
        return;
    }

    IupScintillaOpen();

    RETURN_BOOL(1);
}
/* }}} */

/* {{{ proto void IupScintilla()
    */
PHP_FUNCTION(IupScintilla)
{
    Ihandle *re;

    if (zend_parse_parameters_none() == FAILURE) {
        return;
    }

    re = IupScintilla();

    RETURN_RES(zend_register_resource(re, le_iup_ihandle));
}
/* }}} */

/* {{{ proto void IupScintillaDlg()
    */
PHP_FUNCTION(IupScintillaDlg)
{
    Ihandle *re;
    if (zend_parse_parameters_none() == FAILURE) {
        return;
    }

    re = IupScintillaDlg();

    RETURN_RES(zend_register_resource(re, le_iup_ihandle));
}
/* }}} */
#endif
/* {{{ proto void IupWebBrowserOpen()
    */
PHP_FUNCTION(IupWebBrowserOpen)
{

    int re;
    if (zend_parse_parameters_none() == FAILURE) {
        return;
    }

    re = IupWebBrowserOpen();

    RETURN_LONG(re);
}
/* }}} */

/* {{{ proto void IupWebBrowser()
    */
PHP_FUNCTION(IupWebBrowser)
{

    Ihandle *re;

    if (zend_parse_parameters_none() == FAILURE) {
        return;
    }

    re = IupWebBrowser();

    RETURN_RES(zend_register_resource(re, le_iup_ihandle));
}
/* }}} */

/* {{{ proto void IupTuioOpen()
    */
/*PHP_FUNCTION(IupTuioOpen)
{

    int re;
    if (zend_parse_parameters_none() == FAILURE) {
        return;
    }

    re = IupTuioOpen();

    RETURN_LONG(re);
}*/
/* }}} */

/* {{{ proto void IupTuioClient()
    */
/*PHP_FUNCTION(IupTuioClient)
{

    int argc = ZEND_NUM_ARGS();
    zend_long port;

    Ihandle *re;

    if (zend_parse_parameters(argc, "l", &port) == FAILURE) 
        return;

    re = IupTuioClient(port);

    RETURN_RES(zend_register_resource(re, le_iup_ihandle));
}*/
/* }}} */

/* {{{ proto void IupOleControlOpen()
    */
PHP_FUNCTION(IupOleControlOpen)
{

    int re;
    if (zend_parse_parameters_none() == FAILURE) {
        return;
    }

    re = IupOleControlOpen();

    RETURN_LONG(re);
}
/* }}} */

/* {{{ proto void IupOleControl()
    */
PHP_FUNCTION(IupOleControl)
{

    int argc = ZEND_NUM_ARGS();
    char *progid = NULL;
    size_t progid_len;

    Ihandle *re;

    if (zend_parse_parameters(argc, "s", &progid, &progid_len) == FAILURE) {
        return;
    }

    re = IupOleControl(progid);

    RETURN_RES(zend_register_resource(re, le_iup_ihandle));
}
/* }}} */

/* {{{ proto string IupLoadImage(string file_name)
   ;
 */
PHP_FUNCTION(IupLoadImage)
{
    int argc = ZEND_NUM_ARGS();

    char *file_name = NULL;
    size_t file_name_len;

    Ihandle *re;

    if (zend_parse_parameters(argc, "s", &file_name, &file_name_len) == FAILURE) {
        return;
    }

    re = IupLoadImage(file_name);

    RETURN_RES(zend_register_resource(re, le_iup_ihandle));
}
/* }}} */

/* {{{ proto resource IupSaveImage(resource ih, string file_name, string format)
   ;
 */
PHP_FUNCTION(IupSaveImage)
{
    int argc = ZEND_NUM_ARGS();

    zval *ihandle_res = NULL;

    Ihandle *ih;

    char *file_name = NULL;
    size_t file_name_len;

    char *format = NULL;
    size_t format_len;

    int re;

    if (zend_parse_parameters(argc TSRMLS_DC,"rss",&ihandle_res,&file_name, &file_name_len, &format, &format_len) == FAILURE) {
        return;
    }

    ih = zend_fetch_resource_ex(ihandle_res,"iup-handle",le_iup_ihandle);

    re = IupSaveImage(ih,file_name,format);

    RETURN_LONG(re);
}
/* }}} */

/* {{{ proto string IupLoadAnimation(string file_name)
   ;
 */
PHP_FUNCTION(IupLoadAnimation)
{
    int argc = ZEND_NUM_ARGS();

    char *file_name = NULL;
    size_t file_name_len;

    Ihandle *re;

    if (zend_parse_parameters(argc, "s", &file_name, &file_name_len) == FAILURE) {
        return;
    }

    re = IupLoadAnimation(file_name);

    RETURN_RES(zend_register_resource(re, le_iup_ihandle));
}
/* }}} */


/* {{{ proto resource IupLoadAnimationFrames(ref file_name_list, int file_count)
   ;
 */
PHP_FUNCTION(IupLoadAnimationFrames)
{
    int argc = ZEND_NUM_ARGS();

    // 用以遍历arr_list数组
    long num_key;
    zval *val;
    zend_string *key;

    HashTable *file_name_list_val;

    const char **file_name_list;

    zend_long file_count;

    int i;

    Ihandle *re;

    if (zend_parse_parameters(argc,"hl",&file_name_list_val,&file_count) == FAILURE) {
        return;
    }

    file_name_list = (char **)malloc(sizeof(char *)* file_count);

    i = 0;

    // 将php的字符串数组转换为c的字符串数组
    ZEND_HASH_FOREACH_KEY_VAL(file_name_list_val, num_key, key, val) {

        if(Z_TYPE_P(val) == IS_STRING && i < file_count) {

            file_name_list[i] = (char *)malloc(sizeof(char) * Z_STRLEN_P(val));

            file_name_list[i] = Z_STRVAL_P(val);

            i ++;
        }
    } ZEND_HASH_FOREACH_END();

    re = IupLoadAnimationFrames(file_name_list,file_count);

    free(file_name_list);

    RETURN_RES(zend_register_resource(re, le_iup_ihandle));
}
/* }}} */

/* {{{ proto int IupGetNativeHandleImage(resource ih)
   ;
 */
PHP_FUNCTION(IupGetNativeHandleImage)
{
    int argc = ZEND_NUM_ARGS();

    zval *ihandle_res = NULL;

    Ihandle *ih;
    imImage *re;

    if (zend_parse_parameters(argc TSRMLS_DC,"r",&ihandle_res) == FAILURE) {
        return;
    }

    ih = zend_fetch_resource_ex(ihandle_res,"iup-handle",le_iup_ihandle);

    re = IupGetNativeHandleImage(ih);

    RETURN_RES(zend_register_resource(re, le_iup_ihandle));
}
/* }}} */

/* {{{ proto int IupGetImageNativeHandle(resource ih)
   ;
 */
PHP_FUNCTION(IupGetImageNativeHandle)
{
    int argc = ZEND_NUM_ARGS();

    zval *ihandle_res = NULL;

    imImage *ih;
    void *re;

    if (zend_parse_parameters(argc TSRMLS_DC,"r",&ihandle_res) == FAILURE) {
        return;
    }

    ih = zend_fetch_resource_ex(ihandle_res,"iup-handle",le_iup_ihandle);

    re = IupGetImageNativeHandle(ih);

    RETURN_RES(zend_register_resource(re, le_iup_ihandle));
}
/* }}} */

/* {{{ proto int IupImageFromImImage(resource ih)
   ;
 */
PHP_FUNCTION(IupImageFromImImage)
{
    int argc = ZEND_NUM_ARGS();

    zval *ihandle_res = NULL;

    imImage *ih;
    Ihandle *re;

    if (zend_parse_parameters(argc TSRMLS_DC,"r",&ihandle_res) == FAILURE) {
        return;
    }

    ih = zend_fetch_resource_ex(ihandle_res,"iup-handle",le_iup_ihandle);

    re = IupImageFromImImage(ih);

    RETURN_RES(zend_register_resource(re, le_iup_ihandle));
}
/* }}} */

/* {{{ proto int IupImageToImImage(resource ih)
   ;
 */
PHP_FUNCTION(IupImageToImImage)
{
    int argc = ZEND_NUM_ARGS();

    zval *ihandle_res = NULL;

    Ihandle *ih;
    imImage *re;

    if (zend_parse_parameters(argc TSRMLS_DC,"r",&ihandle_res) == FAILURE) {
        return;
    }

    ih = zend_fetch_resource_ex(ihandle_res,"iup-handle",le_iup_ihandle);

    re = IupImageToImImage(ih);

    RETURN_RES(zend_register_resource(re, le_iup_ihandle));
}
/* }}} */


/* {{{ proto string IupDrawBegin(resource ih)
   ;
 */
PHP_FUNCTION(IupDrawBegin)
{
    int argc = ZEND_NUM_ARGS();

    zval *ihandle_res = NULL;

    Ihandle *ih;

    if (zend_parse_parameters(argc TSRMLS_DC,"r",&ihandle_res) == FAILURE) {
        return;
    }

    ih = zend_fetch_resource_ex(ihandle_res,"iup-handle",le_iup_ihandle);

    IupDrawBegin(ih);

    RETURN_NULL();
    
}
/* }}} */


/* {{{ proto string IupDrawEnd(resource ih)
   ;
 */
PHP_FUNCTION(IupDrawEnd)
{
    int argc = ZEND_NUM_ARGS();

    zval *ihandle_res = NULL;

    Ihandle *ih;

    if (zend_parse_parameters(argc TSRMLS_DC,"r",&ihandle_res) == FAILURE) {
        return;
    }

    ih = zend_fetch_resource_ex(ihandle_res,"iup-handle",le_iup_ihandle);

    IupDrawEnd(ih);

    RETURN_NULL();
    
}
/* }}} */


/* {{{ proto resource IupDrawSetClipRect(resource ih, int x1, int y1, int x2, int y2)
   ;
 */
PHP_FUNCTION(IupDrawSetClipRect)
{
    int argc = ZEND_NUM_ARGS();

    zval *ihandle_res = NULL;

    Ihandle *ih;

    zend_long x1,y1,x2,y2;

    if (zend_parse_parameters(argc TSRMLS_DC,"rllll",&ihandle_res,&x1,&y1,&x2,&y2) == FAILURE) {
        return;
    }

    ih = zend_fetch_resource_ex(ihandle_res,"iup-handle",le_iup_ihandle);

    IupDrawSetClipRect(ih,x1,y1,x2,y2);

    RETURN_NULL();
}
/* }}} */


/* {{{ proto resource IupDrawGetClipRect(resource ih, ref x1, ref y1, ref x2, ref y2)
   ;
 */
PHP_FUNCTION(IupDrawGetClipRect)
{
    int argc = ZEND_NUM_ARGS();

    zval *ihandle_res = NULL;

    Ihandle *ih;

    int x1, y1, x2, y2;
    zval *xx1,*yy1,*xx2,*yy2;

    if (zend_parse_parameters(argc TSRMLS_DC,"rzzzz",&ihandle_res,&xx1,&yy1,&xx2,&yy2) == FAILURE) {
        return;
    }

    ih = zend_fetch_resource_ex(ihandle_res,"iup-handle",le_iup_ihandle);

    IupDrawGetClipRect(ih,&x1,&y1,&x2,&y2);

    zval *real_xx1_val = Z_REFVAL_P(xx1);
    ZVAL_LONG(real_xx1_val,x1);

    zval *real_yy1_val = Z_REFVAL_P(yy1);
    ZVAL_LONG(real_yy1_val,y1);

    zval *real_xx2_val = Z_REFVAL_P(xx2);
    ZVAL_LONG(real_xx2_val,x2);

    zval *real_yy2_val = Z_REFVAL_P(yy2);
    ZVAL_LONG(real_yy2_val,y2);

    RETURN_NULL();
}
/* }}} */


/* {{{ proto string IupDrawResetClip(resource ih)
   ;
 */
PHP_FUNCTION(IupDrawResetClip)
{
    int argc = ZEND_NUM_ARGS();

    zval *ihandle_res = NULL;

    Ihandle *ih;

    if (zend_parse_parameters(argc TSRMLS_DC,"r",&ihandle_res) == FAILURE) {
        return;
    }

    ih = zend_fetch_resource_ex(ihandle_res,"iup-handle",le_iup_ihandle);

    IupDrawResetClip(ih);

    RETURN_NULL();
    
}
/* }}} */


/* {{{ proto string IupDrawParentBackground(resource ih)
   ;
 */
PHP_FUNCTION(IupDrawParentBackground)
{
    int argc = ZEND_NUM_ARGS();

    zval *ihandle_res = NULL;

    Ihandle *ih;

    if (zend_parse_parameters(argc TSRMLS_DC,"r",&ihandle_res) == FAILURE) {
        return;
    }

    ih = zend_fetch_resource_ex(ihandle_res,"iup-handle",le_iup_ihandle);

    IupDrawParentBackground(ih);

    RETURN_NULL();
    
}
/* }}} */


/* {{{ proto resource IupDrawLine(resource ih, int x1, int y1, int x2, int y2)
   ;
 */
PHP_FUNCTION(IupDrawLine)
{
    int argc = ZEND_NUM_ARGS();

    zval *ihandle_res = NULL;

    Ihandle *ih;

    zend_long x1,y1,x2,y2;

    if (zend_parse_parameters(argc TSRMLS_DC,"rllll",&ihandle_res,&x1,&y1,&x2,&y2) == FAILURE) {
        return;
    }

    ih = zend_fetch_resource_ex(ihandle_res,"iup-handle",le_iup_ihandle);

    IupDrawLine(ih,x1,y1,x2,y2);

    RETURN_NULL();
}
/* }}} */

/* {{{ proto resource IupDrawRectangle(resource ih, int x1, int y1, int x2, int y2)
   ;
 */
PHP_FUNCTION(IupDrawRectangle)
{
    int argc = ZEND_NUM_ARGS();

    zval *ihandle_res = NULL;

    Ihandle *ih;

    zend_long x1,y1,x2,y2;

    if (zend_parse_parameters(argc TSRMLS_DC,"rllll",&ihandle_res,&x1,&y1,&x2,&y2) == FAILURE) {
        return;
    }

    ih = zend_fetch_resource_ex(ihandle_res,"iup-handle",le_iup_ihandle);

    IupDrawRectangle(ih,x1,y1,x2,y2);

    RETURN_NULL();
}
/* }}} */

/* {{{ proto resource IupDrawArc(resource ih, int x1, int y1, int x2, int y2, double a1, double a2)
   ;
 */
PHP_FUNCTION(IupDrawArc)
{
    int argc = ZEND_NUM_ARGS();

    zval *ihandle_res = NULL;

    Ihandle *ih;

    zend_long x1,y1,x2,y2;
    double a1,a2;

    if (zend_parse_parameters(argc TSRMLS_DC,"rlllldd",&ihandle_res,&x1,&y1,&x2,&y2,&a1,&a2) == FAILURE) {
        return;
    }

    ih = zend_fetch_resource_ex(ihandle_res,"iup-handle",le_iup_ihandle);

    IupDrawArc(ih,x1,y1,x2,y2,a1,a2);

    RETURN_NULL();
}
/* }}} */

/* {{{ proto string IupDrawPolygon(resource ih, int* points, int count)
   ;
 */
PHP_FUNCTION(IupDrawPolygon)
{
    int argc = ZEND_NUM_ARGS();

    zval *ihandle_res = NULL;

    Ihandle *ih;

    // 用以遍历arr_list数组
    long num_key;
    zend_string *key;
    zval *val;

    HashTable *arr_points;

    zend_long count;

    int *points;

    int i;

    if (zend_parse_parameters(argc TSRMLS_DC,"rhl",&ihandle_res,&arr_points,&count) == FAILURE) {
        return;
    }

    ih = zend_fetch_resource_ex(ihandle_res,"iup-handle",le_iup_ihandle);

    points = (int *)malloc(sizeof(int) * count);

    i = 0;
    // 将php的数组转换为c的数组
    ZEND_HASH_FOREACH_KEY_VAL(arr_points, num_key, key, val) {
        if(Z_TYPE_P(val) == IS_LONG && i < count) {
            points[i] = Z_LVAL_P(val);
            i ++;
        }
    } ZEND_HASH_FOREACH_END();

    IupDrawPolygon(ih,&points,count);

    free(points);

    RETURN_NULL();
    
}
/* }}} */

/* {{{ proto resource IupDrawText(resource ih, char* text, int len, int x, int y, int w, int h)
   ;
 */
PHP_FUNCTION(IupDrawText)
{
    int argc = ZEND_NUM_ARGS();

    zval *ihandle_res = NULL;

    Ihandle *ih;

    char *text = NULL;
    size_t text_len;

    zend_long len,x,y,w,h;

    if (zend_parse_parameters(argc TSRMLS_DC,"rslllll",&ihandle_res,&text,&text_len,&len,&x,&y,&w,&h) == FAILURE) {
        return;
    }

    ih = zend_fetch_resource_ex(ihandle_res,"iup-handle",le_iup_ihandle);

    IupDrawText(ih,text,len,x,y,w,h);

    RETURN_NULL();
}
/* }}} */


/* {{{ proto resource IupDrawImage(resource ih, char* name, int x, int y, int w, int h)
   ;
 */
PHP_FUNCTION(IupDrawImage)
{
    int argc = ZEND_NUM_ARGS();

    zval *ihandle_res = NULL;

    Ihandle *ih;

    char *name = NULL;
    size_t name_len;

    zend_long x,y,w,h;

    if (zend_parse_parameters(argc TSRMLS_DC,"rsllll",&ihandle_res,&name,&name_len,&x,&y,&w,&h) == FAILURE) {
        return;
    }

    ih = zend_fetch_resource_ex(ihandle_res,"iup-handle",le_iup_ihandle);

    IupDrawImage(ih,name,x,y,w,h);

    RETURN_NULL();
}
/* }}} */

/* {{{ proto resource IupDrawSelectRect(resource ih, int x1, int y1, int x2, int y2)
   ;
 */
PHP_FUNCTION(IupDrawSelectRect)
{
    int argc = ZEND_NUM_ARGS();

    zval *ihandle_res = NULL;

    Ihandle *ih;

    zend_long x1,y1,x2,y2;

    if (zend_parse_parameters(argc TSRMLS_DC,"rllll",&ihandle_res,&x1,&y1,&x2,&y2) == FAILURE) {
        return;
    }

    ih = zend_fetch_resource_ex(ihandle_res,"iup-handle",le_iup_ihandle);

    IupDrawSelectRect(ih,x1,y1,x2,y2);

    RETURN_NULL();
}
/* }}} */

/* {{{ proto resource IupDrawFocusRect(resource ih, int x1, int y1, int x2, int y2)
   ;
 */
PHP_FUNCTION(IupDrawFocusRect)
{
    int argc = ZEND_NUM_ARGS();

    zval *ihandle_res = NULL;

    Ihandle *ih;

    zend_long x1,y1,x2,y2;

    if (zend_parse_parameters(argc TSRMLS_DC,"rllll",&ihandle_res,&x1,&y1,&x2,&y2) == FAILURE) {
        return;
    }

    ih = zend_fetch_resource_ex(ihandle_res,"iup-handle",le_iup_ihandle);

    IupDrawFocusRect(ih,x1,y1,x2,y2);

    RETURN_NULL();
}
/* }}} */


/* {{{ proto resource IupDrawGetSize(resource ih, ref w, ref h)
   ;
 */
PHP_FUNCTION(IupDrawGetSize)
{
    int argc = ZEND_NUM_ARGS();

    zval *ihandle_res = NULL;

    Ihandle *ih;

    int w, h;
    zval *ww,*hh;

    if (zend_parse_parameters(argc TSRMLS_DC,"rzz",&ihandle_res,&ww,&hh) == FAILURE) {
        return;
    }

    ih = zend_fetch_resource_ex(ihandle_res,"iup-handle",le_iup_ihandle);

    IupDrawGetSize(ih,&w,&h);

    zval *real_ww_val = Z_REFVAL_P(ww);
    ZVAL_LONG(real_ww_val,w);

    zval *real_hh_val = Z_REFVAL_P(hh);
    ZVAL_LONG(real_hh_val,h);

    RETURN_NULL();
}
/* }}} */


/* {{{ proto resource IupDrawGetTextSize(resource ih, char* text, int len, ref w, ref h)
   ;
 */
PHP_FUNCTION(IupDrawGetTextSize)
{
    int argc = ZEND_NUM_ARGS();

    zval *ihandle_res = NULL;

    char *text = NULL;
    size_t text_len;

    zend_long len;

    Ihandle *ih;

    int w, h;
    zval *ww,*hh;

    if (zend_parse_parameters(argc TSRMLS_DC,"rslzz",&ihandle_res,&text,&text_len,&len,&ww,&hh) == FAILURE) {
        return;
    }

    ih = zend_fetch_resource_ex(ihandle_res,"iup-handle",le_iup_ihandle);

    IupDrawGetTextSize(ih,text,len,&w,&h);

    zval *real_ww_val = Z_REFVAL_P(ww);
    ZVAL_LONG(real_ww_val,w);

    zval *real_hh_val = Z_REFVAL_P(hh);
    ZVAL_LONG(real_hh_val,h);

    RETURN_NULL();
}
/* }}} */

/* {{{ proto resource IupDrawGetImageInfo(char* name, ref w, ref h, ref bpp)
   ;
 */
PHP_FUNCTION(IupDrawGetImageInfo)
{
    int argc = ZEND_NUM_ARGS();

    char *name = NULL;
    size_t name_len;

    int w, h, bpp;
    zval *ww,*hh,*zbpp;

    if (zend_parse_parameters(argc TSRMLS_DC,"slzz",&name,&name_len,&ww,&hh,&zbpp) == FAILURE) {
        return;
    }

    IupDrawGetImageInfo(name,&w,&h,&bpp);

    zval *real_ww_val = Z_REFVAL_P(ww);
    ZVAL_LONG(real_ww_val,w);

    zval *real_hh_val = Z_REFVAL_P(hh);
    ZVAL_LONG(real_hh_val,h);

    zval *real_zbpp_val = Z_REFVAL_P(zbpp);
    ZVAL_LONG(real_zbpp_val,bpp);

    RETURN_NULL();
}
/* }}} */


/* {{{ proto void IupControlsOpen()
    */
PHP_FUNCTION(IupControlsOpen)
{

    int re;
    if (zend_parse_parameters_none() == FAILURE) {
        return;
    }

    re = IupControlsOpen();

    RETURN_LONG(re);
}
/* }}} */

/* {{{ proto string IupCells()
   ;
 */
PHP_FUNCTION(IupCells)
{
    Ihandle *re;

    if (zend_parse_parameters_none() == FAILURE) {
        return;
    }

    re = IupCells();

    RETURN_RES(zend_register_resource(re, le_iup_ihandle));
    
}
/* }}} */

/* {{{ proto string IupMatrix(string action)
   ;
 */
PHP_FUNCTION(IupMatrix)
{
    int argc = ZEND_NUM_ARGS();

    char *action = NULL;
    size_t action_len;

    Ihandle *re;

    if (zend_parse_parameters(argc, "s!", &action, &action_len) == FAILURE) {
        return;
    }

    re = IupMatrix(action);

    RETURN_RES(zend_register_resource(re, le_iup_ihandle));
}
/* }}} */

/* {{{ proto string IupMatrixList()
   ;
 */
PHP_FUNCTION(IupMatrixList)
{
    Ihandle *re;

    if (zend_parse_parameters_none() == FAILURE) {
        return;
    }

    re = IupMatrixList();

    RETURN_RES(zend_register_resource(re, le_iup_ihandle));
    
}
/* }}} */

/* {{{ proto string IupMatrixEx()
   ;
 */
PHP_FUNCTION(IupMatrixEx)
{
    Ihandle *re;

    if (zend_parse_parameters_none() == FAILURE) {
        return;
    }

    re = IupMatrixEx();

    RETURN_RES(zend_register_resource(re, le_iup_ihandle));
    
}
/* }}} */


/* {{{ proto void IupMatrixSetFormula(Ihandle* ih, int col, const char* formula, const char* init);
   ;
 */
PHP_FUNCTION(IupMatrixSetFormula)
{
    php_error(E_WARNING, "IupMatrixSetFormula: not yet implemented");
}
/* }}} */

/* {{{ proto void IupMatrixSetDynamic(Ihandle* ih, const char* init);
   ;
 */
PHP_FUNCTION(IupMatrixSetDynamic)
{
    php_error(E_WARNING, "IupMatrixSetDynamic: not yet implemented");
}
/* }}} */


/* {{{ proto void IupPlotOpen()
    */
PHP_FUNCTION(IupPlotOpen)
{
    if (zend_parse_parameters_none() == FAILURE) {
        return;
    }

    IupPlotOpen();

    RETURN_NULL();
}
/* }}} */

/* {{{ proto string IupPlot()
   ;
 */
PHP_FUNCTION(IupPlot)
{
    Ihandle *re;

    if (zend_parse_parameters_none() == FAILURE) {
        return;
    }

    re = IupPlot();

    RETURN_RES(zend_register_resource(re, le_iup_ihandle));
    
}
/* }}} */

/* {{{ proto string IupPlotBegin(resource ih, int strXdata)
   ;
 */
PHP_FUNCTION(IupPlotBegin)
{
    int argc = ZEND_NUM_ARGS();

    zval *ihandle_res = NULL;

    Ihandle *ih;

    zend_long strXdata;

    if (zend_parse_parameters(argc TSRMLS_DC,"rl",&ihandle_res,&strXdata) == FAILURE) {
        return;
    }

    ih = zend_fetch_resource_ex(ihandle_res,"iup-handle",le_iup_ihandle);

    IupPlotBegin(ih,strXdata);

    RETURN_NULL();
}
/* }}} */


/* {{{ proto resource IupPlotAdd(resource ih, double x, double y)
   ;
 */
PHP_FUNCTION(IupPlotAdd)
{
    int argc = ZEND_NUM_ARGS();

    zval *ihandle_res = NULL;

    Ihandle *ih;

    double x,y;

    if (zend_parse_parameters(argc TSRMLS_DC,"rdd",&ihandle_res,&x,&y) == FAILURE) {
        return;
    }

    ih = zend_fetch_resource_ex(ihandle_res,"iup-handle",le_iup_ihandle);

    IupPlotAdd(ih,x,y);

    RETURN_NULL();
}
/* }}} */

/* {{{ proto resource IupPlotAddStr(resource ih, string x, double y)
   ;
 */
PHP_FUNCTION(IupPlotAddStr)
{
    int argc = ZEND_NUM_ARGS();

    zval *ihandle_res = NULL;

    Ihandle *ih;

    char *x = NULL;
    size_t x_len;

    double y;

    if (zend_parse_parameters(argc TSRMLS_DC,"rsd",&ihandle_res,&x,&x_len,&y) == FAILURE) {
        return;
    }

    ih = zend_fetch_resource_ex(ihandle_res,"iup-handle",le_iup_ihandle);

    IupPlotAddStr(ih,x,y);

    RETURN_NULL();
}
/* }}} */

/* {{{ proto resource IupPlotAddSegment(resource ih, double x, double y)
   ;
 */
PHP_FUNCTION(IupPlotAddSegment)
{
    int argc = ZEND_NUM_ARGS();

    zval *ihandle_res = NULL;

    Ihandle *ih;

    double x,y;

    if (zend_parse_parameters(argc TSRMLS_DC,"rdd",&ihandle_res,&x,&y) == FAILURE) {
        return;
    }

    ih = zend_fetch_resource_ex(ihandle_res,"iup-handle",le_iup_ihandle);

    IupPlotAddSegment(ih,x,y);

    RETURN_NULL();
}
/* }}} */

/* {{{ proto int IupPlotEnd(resource ih)
   ;
 */
PHP_FUNCTION(IupPlotEnd)
{
    int argc = ZEND_NUM_ARGS();

    zval *ihandle_res = NULL;

    Ihandle *ih;

    int re;

    if (zend_parse_parameters(argc TSRMLS_DC,"r",&ihandle_res) == FAILURE) {
        return;
    }

    ih = zend_fetch_resource_ex(ihandle_res,"iup-handle",le_iup_ihandle);

    re = IupPlotEnd(ih);

    RETURN_LONG(re);
}
/* }}} */


/* {{{ proto resource IupPlotLoadData(resource ih, string filename, int strXdata)
   ;
 */
PHP_FUNCTION(IupPlotLoadData)
{
    int argc = ZEND_NUM_ARGS();

    zval *ihandle_res = NULL;

    Ihandle *ih;

    char *filename = NULL;
    size_t filename_len;

    zend_long strXdata;

    int re;

    if (zend_parse_parameters(argc TSRMLS_DC,"rsl",&ihandle_res,&filename,&filename_len,&strXdata) == FAILURE) {
        return;
    }

    ih = zend_fetch_resource_ex(ihandle_res,"iup-handle",le_iup_ihandle);

    re = IupPlotLoadData(ih,filename,strXdata);

    RETURN_LONG(re);
}
/* }}} */

/* {{{ proto resource IupPlotSetFormula(Ihandle* ih, int sample_count, const char* formula, const char* init)
   ;
 */
PHP_FUNCTION(IupPlotSetFormula)
{
    php_error(E_WARNING, "IupPlotSetFormula: not yet implemented");
}
/* }}} */

/* {{{ proto void IupPlotInsert(Ihandle *ih, int ds_index, int sample_index, double x, double y);
   ;
 */
PHP_FUNCTION(IupPlotInsert)
{
    int argc = ZEND_NUM_ARGS();

    zval *ihandle_res = NULL;

    Ihandle *ih;

    zend_long ds_index,sample_index;
    double x,y;

    if (zend_parse_parameters(argc TSRMLS_DC,"rlldd",&ihandle_res,&ds_index,&sample_index,&x,&y) == FAILURE) {
        return;
    }

    ih = zend_fetch_resource_ex(ihandle_res,"iup-handle",le_iup_ihandle);

    IupPlotInsert(ih,ds_index,sample_index,x,y);

    RETURN_NULL();
}
/* }}} */

/* {{{ proto void IupPlotInsertStr(Ihandle *ih, int ds_index, int sample_index, const char* x, double y);
   ;
 */
PHP_FUNCTION(IupPlotInsertStr)
{
    int argc = ZEND_NUM_ARGS();

    zval *ihandle_res = NULL;

    Ihandle *ih;

    char *x = NULL;
    size_t x_len;

    zend_long ds_index,sample_index;

    double y;

    if (zend_parse_parameters(argc TSRMLS_DC,"rllsd",&ihandle_res,&ds_index,&sample_index,&x,&x_len,&y) == FAILURE) {
        return;
    }

    ih = zend_fetch_resource_ex(ihandle_res,"iup-handle",le_iup_ihandle);

    IupPlotInsertStr(ih,ds_index,sample_index,x,y);

    RETURN_NULL();
}
/* }}} */

/* {{{ proto void IupPlotInsertSegment(Ihandle *ih, int ds_index, int sample_index, double x, double y);
   ;
 */
PHP_FUNCTION(IupPlotInsertSegment)
{
    int argc = ZEND_NUM_ARGS();

    zval *ihandle_res = NULL;

    Ihandle *ih;

    zend_long ds_index,sample_index;
    double x,y;

    if (zend_parse_parameters(argc TSRMLS_DC,"rlldd",&ihandle_res,&ds_index,&sample_index,&x,&y) == FAILURE) {
        return;
    }

    ih = zend_fetch_resource_ex(ihandle_res,"iup-handle",le_iup_ihandle);

    IupPlotInsertSegment(ih,ds_index,sample_index,x,y);

    RETURN_NULL();
}
/* }}} */

/* {{{ proto void IupPlotInsertStrSamples(Ihandle* ih, int ds_index, int sample_index, const char** x, double* y, int count);
   ;
 */
PHP_FUNCTION(IupPlotInsertStrSamples)
{
    int argc = ZEND_NUM_ARGS();

    zval *ihandle_res = NULL;

    // 用以遍历arr_list数组
    long num_key;
    zval *val;
    zend_string *key;

    HashTable *x_val,*y_val;

    const char **x;

    zend_long ds_index,sample_index,count;

    double* y;

    int i;

    Ihandle *ih;

    if (zend_parse_parameters(argc TSRMLS_DC,"rllhhl",&ihandle_res,&ds_index,&sample_index,&x_val,&y_val,&count) == FAILURE) {
        return;
    }

    ih = zend_fetch_resource_ex(ihandle_res,"iup-handle",le_iup_ihandle);

    x = (char **)malloc(sizeof(char *)* count);

    i = 0;

    // 将php的字符串数组转换为c的字符串数组
    ZEND_HASH_FOREACH_KEY_VAL(x_val, num_key, key, val) {

        if(Z_TYPE_P(val) == IS_STRING && i < count) {

            x[i] = (char *)malloc(sizeof(char) * Z_STRLEN_P(val));

            x[i] = Z_STRVAL_P(val);

            i ++;
        }
    } ZEND_HASH_FOREACH_END();

    y = (double *)malloc(sizeof(double)* count);

    i = 0;

    // 将php的字符串数组转换为c的字符串数组
    ZEND_HASH_FOREACH_KEY_VAL(y_val, num_key, key, val) {

        if(i < count){

            if(Z_TYPE_P(val) == IS_DOUBLE) {

                y[i] = Z_DVAL_P(val);

                i ++;
            }

            if(Z_TYPE_P(val) == IS_LONG){
                y[i] = Z_LVAL_P(val);

                i ++;
            }
        }

    } ZEND_HASH_FOREACH_END();

    IupPlotInsertStrSamples(ih,ds_index,sample_index,x,y,count);

    free(x);
    free(y);

    RETURN_NULL();
}
/* }}} */

/* {{{ proto void IupPlotInsertSamples(Ihandle* ih, int ds_index, int sample_index, double *x, double *y, int count);
   ;
 */
PHP_FUNCTION(IupPlotInsertSamples)
{
    int argc = ZEND_NUM_ARGS();

    zval *ihandle_res = NULL;

    // 用以遍历arr_list数组
    long num_key;
    zval *val;
    zend_string *key;

    HashTable *x_val,*y_val;

    zend_long ds_index,sample_index,count;

    double *x,*y;

    int i;

    Ihandle *ih;

    if (zend_parse_parameters(argc TSRMLS_DC,"rllhhl",&ihandle_res,&ds_index,&sample_index,&x_val,&y_val,&count) == FAILURE) {
        return;
    }

    ih = zend_fetch_resource_ex(ihandle_res,"iup-handle",le_iup_ihandle);

    x = (double *)malloc(sizeof(double)* count);

    i = 0;

    // 将php的字符串数组转换为c的字符串数组
    ZEND_HASH_FOREACH_KEY_VAL(x_val, num_key, key, val) {

        if(i < count){

            if(Z_TYPE_P(val) == IS_DOUBLE) {

                x[i] = Z_DVAL_P(val);

                i ++;
            }

            if(Z_TYPE_P(val) == IS_LONG){
                x[i] = Z_LVAL_P(val);

                i ++;
            }
        }
    } ZEND_HASH_FOREACH_END();

    y = (double *)malloc(sizeof(double)* count);

    i = 0;

    // 将php的字符串数组转换为c的字符串数组
    ZEND_HASH_FOREACH_KEY_VAL(y_val, num_key, key, val) {

        if(i < count){

            if(Z_TYPE_P(val) == IS_DOUBLE) {

                y[i] = Z_DVAL_P(val);

                i ++;
            }

            if(Z_TYPE_P(val) == IS_LONG){
                y[i] = Z_LVAL_P(val);

                i ++;
            }
        }

    } ZEND_HASH_FOREACH_END();

    IupPlotInsertSamples(ih,ds_index,sample_index,x,y,count);

    free(x);
    free(y);

    RETURN_NULL();
}
/* }}} */

/* {{{ proto void IupPlotAddSamples(Ihandle* ih, int ds_index, double *x, double *y, int count);
   ;
 */
PHP_FUNCTION(IupPlotAddSamples)
{
    int argc = ZEND_NUM_ARGS();

    zval *ihandle_res = NULL;

    // 用以遍历arr_list数组
    long num_key;
    zval *val;
    zend_string *key;

    HashTable *x_val,*y_val;

    zend_long ds_index,count;

    double *x,*y;

    int i;

    Ihandle *ih;

    if (zend_parse_parameters(argc TSRMLS_DC,"rlhhl",&ihandle_res,&ds_index,&x_val,&y_val,&count) == FAILURE) {
        return;
    }

    ih = zend_fetch_resource_ex(ihandle_res,"iup-handle",le_iup_ihandle);

    x = (double *)malloc(sizeof(double)* count);

    i = 0;

    // 将php的字符串数组转换为c的字符串数组
    ZEND_HASH_FOREACH_KEY_VAL(x_val, num_key, key, val) {

        if(i < count){

            if(Z_TYPE_P(val) == IS_DOUBLE) {

                x[i] = Z_DVAL_P(val);

                i ++;
            }

            if(Z_TYPE_P(val) == IS_LONG){
                x[i] = Z_LVAL_P(val);

                i ++;
            }
        }
    } ZEND_HASH_FOREACH_END();

    y = (double *)malloc(sizeof(double)* count);

    i = 0;

    // 将php的字符串数组转换为c的字符串数组
    ZEND_HASH_FOREACH_KEY_VAL(y_val, num_key, key, val) {

        if(i < count){

            if(Z_TYPE_P(val) == IS_DOUBLE) {

                y[i] = Z_DVAL_P(val);

                i ++;
            }

            if(Z_TYPE_P(val) == IS_LONG){
                y[i] = Z_LVAL_P(val);

                i ++;
            }
        }

    } ZEND_HASH_FOREACH_END();

    IupPlotAddSamples(ih,ds_index,x,y,count);

    free(x);
    free(y);

    RETURN_NULL();
}
/* }}} */

/* {{{ proto void IupPlotAddStrSamples(Ihandle* ih, int ds_index, const char** x, double* y, int count);
   ;
 */
PHP_FUNCTION(IupPlotAddStrSamples)
{
    int argc = ZEND_NUM_ARGS();

    zval *ihandle_res = NULL;

    // 用以遍历arr_list数组
    long num_key;
    zval *val;
    zend_string *key;

    HashTable *x_val,*y_val;

    const char **x;

    zend_long ds_index,count;

    double* y;

    int i;

    Ihandle *ih;

    if (zend_parse_parameters(argc TSRMLS_DC,"rlhhl",&ihandle_res,&ds_index,&x_val,&y_val,&count) == FAILURE) {
        return;
    }

    ih = zend_fetch_resource_ex(ihandle_res,"iup-handle",le_iup_ihandle);

    x = (char **)malloc(sizeof(char *)* count);

    i = 0;

    // 将php的字符串数组转换为c的字符串数组
    ZEND_HASH_FOREACH_KEY_VAL(x_val, num_key, key, val) {

        if(Z_TYPE_P(val) == IS_STRING && i < count) {

            x[i] = (char *)malloc(sizeof(char) * Z_STRLEN_P(val));

            x[i] = Z_STRVAL_P(val);

            i ++;
        }
    } ZEND_HASH_FOREACH_END();

    y = (double *)malloc(sizeof(double)* count);

    i = 0;

    // 将php的字符串数组转换为c的字符串数组
    ZEND_HASH_FOREACH_KEY_VAL(y_val, num_key, key, val) {

        if(i < count){

            if(Z_TYPE_P(val) == IS_DOUBLE) {

                y[i] = Z_DVAL_P(val);

                i ++;
            }

            if(Z_TYPE_P(val) == IS_LONG){
                y[i] = Z_LVAL_P(val);

                i ++;
            }
        }

    } ZEND_HASH_FOREACH_END();

    IupPlotAddStrSamples(ih,ds_index,x,y,count);

    free(x);
    free(y);

    RETURN_NULL();
}
/* }}} */
/* {{{ proto void IupPlotGetSample(Ihandle* ih, int ds_index, int sample_index, double *x, double *y);
   ;
 */
PHP_FUNCTION(IupPlotGetSample)
{
    int argc = ZEND_NUM_ARGS();

    zval *ihandle_res = NULL;

    Ihandle *ih;

    zend_long ds_index,sample_index;

    double x, y;
    zval *xx,*yy;

    if (zend_parse_parameters(argc TSRMLS_DC,"rllzz",&ihandle_res,&ds_index,&sample_index,&xx,&yy) == FAILURE) {
        return;
    }

    ih = zend_fetch_resource_ex(ihandle_res,"iup-handle",le_iup_ihandle);

    IupPlotGetSample(ih,ds_index,sample_index,&x,&y);

    zval *real_xx_val = Z_REFVAL_P(xx);
    ZVAL_DOUBLE(real_xx_val,x);

    zval *real_yy_val = Z_REFVAL_P(yy);
    ZVAL_DOUBLE(real_yy_val,y);

    RETURN_NULL();
}
/* }}} */

/* {{{ proto void IupPlotGetSampleStr(Ihandle* ih, int ds_index, int sample_index, const char* *x, double *y);
   ;
 */
PHP_FUNCTION(IupPlotGetSampleStr)
{
    int argc = ZEND_NUM_ARGS();

    zval *ihandle_res = NULL;

    Ihandle *ih;

    zend_long ds_index,sample_index;

    char* x;

    double y;
    zval *xx,*yy;

    if (zend_parse_parameters(argc TSRMLS_DC,"rllzd",&ihandle_res,&ds_index,&sample_index,&xx,&yy) == FAILURE) {
        return;
    }

    ih = zend_fetch_resource_ex(ihandle_res,"iup-handle",le_iup_ihandle);

    IupPlotGetSampleStr(ih,ds_index,sample_index,&x,&y);

    zval *real_xx_val = Z_REFVAL_P(xx);
    ZVAL_STR(real_xx_val,x);

    zval *real_yy_val = Z_REFVAL_P(yy);
    ZVAL_DOUBLE(real_yy_val,y);

    RETURN_NULL();
}
/* }}} */


/* {{{ proto int IupPlotGetSampleSelection(Ihandle* ih, int ds_index, int sample_index);
   ;
 */
PHP_FUNCTION(IupPlotGetSampleSelection)
{
    int argc = ZEND_NUM_ARGS();

    zval *ihandle_res = NULL;

    Ihandle *ih;

    zend_long ds_index,sample_index;

    int re;

    if (zend_parse_parameters(argc TSRMLS_DC,"rll",&ihandle_res,&ds_index,&sample_index) == FAILURE) {
        return;
    }

    ih = zend_fetch_resource_ex(ihandle_res,"iup-handle",le_iup_ihandle);

    re = IupPlotGetSampleSelection(ih,ds_index,sample_index);

    RETURN_LONG(re);
}
/* }}} */

/* {{{ proto double IupPlotGetSampleExtra(Ihandle* ih, int ds_index, int sample_index);
   ;
 */
PHP_FUNCTION(IupPlotGetSampleExtra)
{
    int argc = ZEND_NUM_ARGS();

    zval *ihandle_res = NULL;

    Ihandle *ih;

    zend_long ds_index,sample_index;

    double re;

    if (zend_parse_parameters(argc TSRMLS_DC,"rll",&ihandle_res,&ds_index,&sample_index) == FAILURE) {
        return;
    }

    ih = zend_fetch_resource_ex(ihandle_res,"iup-handle",le_iup_ihandle);

    re = IupPlotGetSampleExtra(ih,ds_index,sample_index);

    RETURN_DOUBLE(re);
}
/* }}} */


/* {{{ proto void IupPlotSetSample(Ihandle* ih, int ds_index, int sample_index, double x, double y);
   ;
 */
PHP_FUNCTION(IupPlotSetSample)
{
    int argc = ZEND_NUM_ARGS();

    zval *ihandle_res = NULL;

    Ihandle *ih;

    zend_long ds_index,sample_index;
    double x,y;

    if (zend_parse_parameters(argc TSRMLS_DC,"rlldd",&ihandle_res,&ds_index,&sample_index,&x,&y) == FAILURE) {
        return;
    }

    ih = zend_fetch_resource_ex(ihandle_res,"iup-handle",le_iup_ihandle);

    IupPlotSetSample(ih,ds_index,sample_index,x,y);

    RETURN_NULL();
}
/* }}} */


/* {{{ proto void IupPlotSetSampleStr(Ihandle *ih, int ds_index, int sample_index, const char* x, double y);
   ;
 */
PHP_FUNCTION(IupPlotSetSampleStr)
{
    int argc = ZEND_NUM_ARGS();

    zval *ihandle_res = NULL;

    Ihandle *ih;

    char *x = NULL;
    size_t x_len;

    zend_long ds_index,sample_index;

    double y;

    if (zend_parse_parameters(argc TSRMLS_DC,"rllsd",&ihandle_res,&ds_index,&sample_index,&x,&x_len,&y) == FAILURE) {
        return;
    }

    ih = zend_fetch_resource_ex(ihandle_res,"iup-handle",le_iup_ihandle);

    IupPlotSetSampleStr(ih,ds_index,sample_index,x,y);

    RETURN_NULL();
}
/* }}} */


/* {{{ proto void IupPlotSetSampleSelection(Ihandle* ih, int ds_index, int sample_index, int selected);
   ;
 */
PHP_FUNCTION(IupPlotSetSampleSelection)
{
    int argc = ZEND_NUM_ARGS();

    zval *ihandle_res = NULL;

    Ihandle *ih;

    zend_long ds_index,sample_index,selected;

    if (zend_parse_parameters(argc TSRMLS_DC,"rlldd",&ihandle_res,&ds_index,&sample_index,&selected) == FAILURE) {
        return;
    }

    ih = zend_fetch_resource_ex(ihandle_res,"iup-handle",le_iup_ihandle);

    IupPlotSetSampleSelection(ih,ds_index,sample_index,selected);

    RETURN_NULL();
}
/* }}} */

/* {{{ proto void IupPlotSetSampleExtra(Ihandle* ih, int ds_index, int sample_index, double extra);
   ;
 */
PHP_FUNCTION(IupPlotSetSampleExtra)
{
    int argc = ZEND_NUM_ARGS();

    zval *ihandle_res = NULL;

    Ihandle *ih;

    zend_long ds_index,sample_index;
    double extra;

    if (zend_parse_parameters(argc TSRMLS_DC,"rlldd",&ihandle_res,&ds_index,&sample_index,&extra) == FAILURE) {
        return;
    }

    ih = zend_fetch_resource_ex(ihandle_res,"iup-handle",le_iup_ihandle);

    IupPlotSetSampleExtra(ih,ds_index,sample_index,extra);

    RETURN_NULL();
}
/* }}} */

/* {{{ proto void IupPlotTransform(Ihandle* ih, double x, double y, double *cnv_x, double *cnv_y);
   ;
 */
PHP_FUNCTION(IupPlotTransform)
{
    int argc = ZEND_NUM_ARGS();

    zval *ihandle_res = NULL;

    Ihandle *ih;

    double x,y;

    double cnv_x, cnv_y;
    zval *cnv_xx,*cnv_yy;

    if (zend_parse_parameters(argc TSRMLS_DC,"rddzz",&ihandle_res,&x,&y,&cnv_xx,&cnv_yy) == FAILURE) {
        return;
    }

    ih = zend_fetch_resource_ex(ihandle_res,"iup-handle",le_iup_ihandle);

    IupPlotTransform(ih,x,y,&cnv_x,&cnv_y);

    zval *real_cnv_xx_val = Z_REFVAL_P(cnv_xx);
    ZVAL_DOUBLE(real_cnv_xx_val,cnv_x);

    zval *real_cnv_yy_val = Z_REFVAL_P(cnv_yy);
    ZVAL_DOUBLE(real_cnv_yy_val,cnv_y);

    RETURN_NULL();
}
/* }}} */

/* {{{ proto void IupPlotTransformTo(Ihandle* ih, double cnv_x, double cnv_y, double *x, double *y);
   ;
 */
PHP_FUNCTION(IupPlotTransformTo)
{
    int argc = ZEND_NUM_ARGS();

    zval *ihandle_res = NULL;

    Ihandle *ih;

    double cnv_x, cnv_y;

    double x,y;
    zval *xx,*yy;

    if (zend_parse_parameters(argc TSRMLS_DC,"rddzz",&ihandle_res,&cnv_x,&cnv_y,&xx,&yy) == FAILURE) {
        return;
    }

    ih = zend_fetch_resource_ex(ihandle_res,"iup-handle",le_iup_ihandle);

    IupPlotTransformTo(ih,cnv_x,cnv_y,&x,&y);

    zval *real_xx_val = Z_REFVAL_P(xx);
    ZVAL_DOUBLE(real_xx_val,x);

    zval *real_yy_val = Z_REFVAL_P(yy);
    ZVAL_DOUBLE(real_yy_val,y);

    RETURN_NULL();
}
/* }}} */


/* {{{ proto int  IupPlotFindSample(Ihandle* ih, double cnv_x, double cnv_y, int *ds_index, int *sample_index);
   ;
 */
PHP_FUNCTION(IupPlotFindSample)
{
    int argc = ZEND_NUM_ARGS();

    zval *ihandle_res = NULL;

    Ihandle *ih;

    double cnv_x, cnv_y;

    int ds_index,sample_index;
    zval *ds_index_val,*sample_index_val;

    if (zend_parse_parameters(argc TSRMLS_DC,"rddzz",&ihandle_res,&cnv_x,&cnv_y,&ds_index_val,&sample_index_val) == FAILURE) {
        return;
    }

    ih = zend_fetch_resource_ex(ihandle_res,"iup-handle",le_iup_ihandle);

    IupPlotFindSample(ih,cnv_x,cnv_y,&ds_index,&sample_index);

    zval *real_ds_index_val = Z_REFVAL_P(ds_index_val);
    ZVAL_LONG(real_ds_index_val,ds_index);

    zval *real_sample_index_val = Z_REFVAL_P(sample_index_val);
    ZVAL_LONG(real_sample_index_val,sample_index);

    RETURN_NULL();
}
/* }}} */

/* {{{ proto int  IupPlotFindSegment(Ihandle* ih, double cnv_x, double cnv_y, int *ds_index, int *sample_index1, int *sample_index2);
   ;
 */
PHP_FUNCTION(IupPlotFindSegment)
{
    int argc = ZEND_NUM_ARGS();

    zval *ihandle_res = NULL;

    Ihandle *ih;

    double cnv_x, cnv_y;

    double ds_index,sample_index1,sample_index2;
    zval *ds_index_val,*sample_index1_val,*sample_index2_val;

    if (zend_parse_parameters(argc TSRMLS_DC,"rddzzz",&ihandle_res,&cnv_x,&cnv_y,&ds_index_val,&sample_index1_val,&sample_index2_val) == FAILURE) {
        return;
    }

    ih = zend_fetch_resource_ex(ihandle_res,"iup-handle",le_iup_ihandle);

    IupPlotFindSegment(ih,cnv_x,cnv_y,&ds_index,&sample_index1,&sample_index2);

    zval *real_ds_index_val = Z_REFVAL_P(ds_index_val);
    ZVAL_DOUBLE(real_ds_index_val,ds_index);

    zval *real_sample_index1_val = Z_REFVAL_P(sample_index1_val);
    ZVAL_DOUBLE(real_sample_index1_val,sample_index1);

    zval *real_sample_index2_val = Z_REFVAL_P(sample_index2_val);
    ZVAL_DOUBLE(real_sample_index2_val,sample_index2);

    RETURN_NULL();
}
/* }}} */

/* {{{ proto void IupPlotPaintTo(Ihandle *ih, struct _cdCanvas* cnv);
   ;
 */
PHP_FUNCTION(IupPlotPaintTo)
{

    php_error(E_WARNING, "IupPlotPaintTo: not yet implemented");

}
/* }}} */