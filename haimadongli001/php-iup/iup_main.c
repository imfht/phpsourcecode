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
extern int is_iup_open;

extern HashTable *iup_events;
extern HashTable *iup_callback;

/* {{{ proto void IupDebug(resource ih)
   ;
 */
PHP_FUNCTION(IupDebug)
{
    int argc = ZEND_NUM_ARGS();

    char *msg = NULL;
    size_t msg_len;

    zval *ihandle_res = NULL;

    Ihandle *ih;

    intptr_t ih_p_int;
    char event_key_str[120];

    if (zend_parse_parameters(argc TSRMLS_DC,"sr",&msg,&msg_len,&ihandle_res) == FAILURE) {
        return;
    }

    ih = zend_fetch_resource_ex(ihandle_res,"iup-handle",le_iup_ihandle);

    ih_p_int = (intptr_t)ih;
    
    sprintf(event_key_str,"%"SCNiPTR,ih_p_int);

    php_printf("%s_%s\n",msg,event_key_str);

    RETURN_BOOL(1);
}
/* }}} */

/* {{{ proto void IupOpen()
    */
PHP_FUNCTION(IupOpen)
{
    if (zend_parse_parameters_none() == FAILURE) {
        return;
    }

    if(is_iup_open == 0){

        is_iup_open = 1;

        IupOpen(NULL, NULL);

        // init array
        ALLOC_HASHTABLE(iup_events);
        zend_hash_init(iup_events,512,NULL,NULL,0);

        // 注册回调函数
        ALLOC_HASHTABLE(iup_callback);
        zend_hash_init(iup_callback,640,NULL,NULL,0);
        event_register_callback();

        RETURN_BOOL(1);
    }

    RETURN_BOOL(0);
}
/* }}} */

/* {{{ proto void IupClose()
    */
PHP_FUNCTION(IupClose)
{
    if (zend_parse_parameters_none() == FAILURE) {
        return;
    }

    if(is_iup_open == 1)
    {
        is_iup_open = 0;
        
        zend_hash_destroy(iup_events);
        zend_hash_destroy(iup_callback);

        IupClose();

        RETURN_BOOL(1);
    }


    RETURN_BOOL(0);
}
/* }}} */

/* {{{ proto void IupIsOpened()
    */
PHP_FUNCTION(IupIsOpened)
{
    int i;
    if (zend_parse_parameters_none() == FAILURE) {
        return;
    }

    i = IupIsOpened();

    RETURN_LONG(i);
}
/* }}} */

/* {{{ proto void IupMainLoop()
    */
PHP_FUNCTION(IupMainLoop)
{
    int i;
    if (zend_parse_parameters_none() == FAILURE) {
        return;
    }

    i = IupMainLoop();

    RETURN_LONG(i);
}
/* }}} */

/* {{{ proto void IupLoopStep()
    */
PHP_FUNCTION(IupLoopStep)
{
    int i;
    if (zend_parse_parameters_none() == FAILURE) {
        return;
    }

    i = IupLoopStep();

    RETURN_LONG(i);
}
/* }}} */

/* {{{ proto void IupLoopStepWait()
    */
PHP_FUNCTION(IupLoopStepWait)
{
    int i;
    if (zend_parse_parameters_none() == FAILURE) {
        return;
    }

    i = IupLoopStepWait();

    RETURN_LONG(i);
}
/* }}} */

/* {{{ proto void IupMainLoopLevel()
    */
PHP_FUNCTION(IupMainLoopLevel)
{
    int i;
    if (zend_parse_parameters_none() == FAILURE) {
        return;
    }

    i = IupMainLoopLevel();

    RETURN_LONG(i);
}
/* }}} */

/* {{{ proto void IupImageLibOpen()
    */
PHP_FUNCTION(IupImageLibOpen)
{
    if (zend_parse_parameters_none() == FAILURE) {
        return;
    }

    IupImageLibOpen();

    RETURN_BOOL(1);
}
/* }}} */

/* {{{ proto void IupFlush()
    */
PHP_FUNCTION(IupFlush)
{
    if (zend_parse_parameters_none() == FAILURE) {
        return;
    }

    IupFlush();

    RETURN_BOOL(1);
}
/* }}} */

/* {{{ proto void IupExitLoop()
    */
PHP_FUNCTION(IupExitLoop)
{
    if (zend_parse_parameters_none() == FAILURE) {
        return;
    }

    IupExitLoop();

    RETURN_BOOL(1);
}
/* }}} */

/* {{{ proto resource IupPostMessage(resource ih, string s, int i, double d, string p)
   ;
 */
PHP_FUNCTION(IupPostMessage)
{
    int argc = ZEND_NUM_ARGS();

    zval *ihandle_res = NULL;

    Ihandle *ih;

    char *s = NULL;
    size_t s_len;

    zend_long i;
    double d;

    char *p = NULL;
    size_t p_len;

    if (zend_parse_parameters(argc TSRMLS_DC,"r!slds!",&ihandle_res,&s,&s_len,&i,&d,&p,&p_len) == FAILURE) {
        return;
    }

    if(ihandle_res != NULL){
        ih = zend_fetch_resource_ex(ihandle_res,"iup-handle",le_iup_ihandle);
    }else{
        ih = NULL;
    }

    IupPostMessage(ih,s,i,d,p);

    RETURN_BOOL(1);
}
/* }}} */

/* {{{ proto string IupRecordInput(string filename, int mode)
   ;
 */
PHP_FUNCTION(IupRecordInput)
{
    int argc = ZEND_NUM_ARGS();

    char *filename = NULL;
    size_t filename_len;

    zend_long mode;

    int i;

    if (zend_parse_parameters(argc, "sl", &filename, &filename_len,&mode) == FAILURE) {
        return;
    }

    i = IupRecordInput(filename,mode);

    RETURN_LONG(i);
}
/* }}} */

/* {{{ proto string IupPlayInput(string filename)
   ;
 */
PHP_FUNCTION(IupPlayInput)
{
    int argc = ZEND_NUM_ARGS();

    char *filename = NULL;
    size_t filename_len;

    int i;

    if (zend_parse_parameters(argc, "s", &filename, &filename_len) == FAILURE) {
        return;
    }

    i = IupPlayInput(filename);

    RETURN_LONG(i);
}
/* }}} */

/* {{{ proto void IupUpdate(resource ih)
   ;
 */
PHP_FUNCTION(IupUpdate)
{
    int argc = ZEND_NUM_ARGS();

    zval *ihandle_res = NULL;

    Ihandle *ih;

    if (zend_parse_parameters(argc TSRMLS_DC,"r",&ihandle_res) == FAILURE) {
        return;
    }

    ih = zend_fetch_resource_ex(ihandle_res,"iup-handle",le_iup_ihandle);

    IupUpdate(ih);

    RETURN_BOOL(1);
    
}
/* }}} */

/* {{{ proto void IupUpdateChildren(resource ih)
   ;
 */
PHP_FUNCTION(IupUpdateChildren)
{
    int argc = ZEND_NUM_ARGS();

    zval *ihandle_res = NULL;

    Ihandle *ih;

    if (zend_parse_parameters(argc TSRMLS_DC,"r",&ihandle_res) == FAILURE) {
        return;
    }

    ih = zend_fetch_resource_ex(ihandle_res,"iup-handle",le_iup_ihandle);

    IupUpdateChildren(ih);

    RETURN_BOOL(1);
    
}
/* }}} */

/* {{{ proto void IupRedraw(resource ih, int children)
   ;
 */
PHP_FUNCTION(IupRedraw)
{
    int argc = ZEND_NUM_ARGS();

    zval *ihandle_res = NULL;

    Ihandle *ih;

    zend_long children;

    if (zend_parse_parameters(argc TSRMLS_DC,"rl",&ihandle_res,&children) == FAILURE) {
        return;
    }

    ih = zend_fetch_resource_ex(ihandle_res,"iup-handle",le_iup_ihandle);

    IupRedraw(ih,children);

    RETURN_BOOL(1);
    
}
/* }}} */

/* {{{ proto void IupRefresh(resource ih)
   ;
 */
PHP_FUNCTION(IupRefresh)
{
    int argc = ZEND_NUM_ARGS();

    zval *ihandle_res = NULL;

    Ihandle *ih;

    if (zend_parse_parameters(argc TSRMLS_DC,"r",&ihandle_res) == FAILURE) {
        return;
    }

    ih = zend_fetch_resource_ex(ihandle_res,"iup-handle",le_iup_ihandle);

    IupRefresh(ih);

    RETURN_BOOL(1);
    
}
/* }}} */

/* {{{ proto void IupRefreshChildren(resource ih)
   ;
 */
PHP_FUNCTION(IupRefreshChildren)
{
    int argc = ZEND_NUM_ARGS();

    zval *ihandle_res = NULL;

    Ihandle *ih;

    if (zend_parse_parameters(argc TSRMLS_DC,"r",&ihandle_res) == FAILURE) {
        return;
    }

    ih = zend_fetch_resource_ex(ihandle_res,"iup-handle",le_iup_ihandle);

    IupRefreshChildren(ih);

    RETURN_BOOL(1);
    
}
/* }}} */


/* {{{ proto string IupExecute(string filename, string parameters)
   ;
 */
PHP_FUNCTION(IupExecute)
{
    int argc = ZEND_NUM_ARGS();

    char *filename = NULL;
    size_t filename_len;

    char *parameters = NULL;
    size_t parameters_len;

    int i;

    if (zend_parse_parameters(argc, "ss!", &filename, &filename_len, &parameters, &parameters_len) == FAILURE){
        return;
    }

    i = IupExecute(filename,parameters);

    RETURN_LONG(i);
}
/* }}} */

/* {{{ proto string IupExecuteWait(string filename, string parameters)
   ;
 */
PHP_FUNCTION(IupExecuteWait)
{
    int argc = ZEND_NUM_ARGS();
    
    char *filename = NULL;
    size_t filename_len;

    char *parameters = NULL;
    size_t parameters_len;

    int i;

    if (zend_parse_parameters(argc, "ss!", &filename, &filename_len, &parameters, &parameters_len) == FAILURE){
        return;
    }

    i = IupExecuteWait(filename,parameters);

    RETURN_LONG(i);
}
/* }}} */

/* {{{ proto string IupHelp(string url)
   ;
 */
PHP_FUNCTION(IupHelp)
{
    int argc = ZEND_NUM_ARGS();
    
    char *url = NULL;
    size_t url_len;

    int i;

    if (zend_parse_parameters(argc, "s", &url, &url_len) == FAILURE){
        return;
    }

    i = IupHelp(url);

    RETURN_LONG(i);
}
/* }}} */


// void IupLog(const char* type, const char* format, ...);
// 待完善
/* {{{ proto string IupLog(string type, string format)
   ;
 */
PHP_FUNCTION(IupLog)
{
    int argc = ZEND_NUM_ARGS();
    
    char *type = NULL;
    size_t type_len;

    char *format = NULL;
    size_t format_len;

    if (zend_parse_parameters(argc, "ss", &type, &type_len, &format, &format_len) == FAILURE){
        return;
    }

    IupLog(type,format,NULL);

    RETURN_BOOL(1);
}
/* }}} */

/* {{{ proto string IupLoad(string filename)
   ;
 */
PHP_FUNCTION(IupLoad)
{
    int argc = ZEND_NUM_ARGS();
    char *filename = NULL;
    size_t filename_len;

    char * str;

    if (zend_parse_parameters(argc, "s", &filename, &filename_len) == FAILURE){
        return;
    }

    str = IupLoad(filename);

    RETURN_STRING(str);

}
/* }}} */

/* {{{ proto string IupLoadBuffer(string buffer)
   ;
 */
PHP_FUNCTION(IupLoadBuffer)
{
    int argc = ZEND_NUM_ARGS();
    char *buffer = NULL;
    size_t buffer_len;

    char * str;

    if (zend_parse_parameters(argc, "s", &buffer, &buffer_len) == FAILURE){
        return;
    }

    str = IupLoadBuffer(buffer);

    RETURN_STRING(str);

}
/* }}} */

/* {{{ proto string IupVersion()
   ;
 */
PHP_FUNCTION(IupVersion)
{
    char * str;

    if (zend_parse_parameters_none() == FAILURE) {
        return;
    }

    str = IupVersion();

    RETURN_STRING(str);

}
/* }}} */

/* {{{ proto string IupVersionDate()
   ;
 */
PHP_FUNCTION(IupVersionDate)
{
    char * str;

    if (zend_parse_parameters_none() == FAILURE) {
        return;
    }

    str = IupVersionDate();

    RETURN_STRING(str);

}
/* }}} */

/* {{{ proto int IupVersionNumber()
   ;
 */
PHP_FUNCTION(IupVersionNumber)
{
    long l;

    if (zend_parse_parameters_none() == FAILURE) {
        return;
    }

    l = IupVersionNumber();

    RETURN_LONG(l);

}
/* }}} */

/* {{{ proto string IupVersionShow()
   ;
 */
PHP_FUNCTION(IupVersionShow)
{

    if (zend_parse_parameters_none() == FAILURE) {
        return;
    }
    
    IupVersionShow();

    RETURN_BOOL(1);
}
/* }}} */


/* {{{ proto string IupSetLanguage(string lng)
   ;
 */
PHP_FUNCTION(IupSetLanguage)
{
    int argc = ZEND_NUM_ARGS();
    char *lng = NULL;
    size_t lng_len;

    if (zend_parse_parameters(argc, "s", &lng, &lng_len) == FAILURE) {
        return;
    }
    
    IupSetLanguage(lng);

    RETURN_BOOL(1);
}
/* }}} */

/* {{{ proto string IupGetLanguage()
   ;
 */
PHP_FUNCTION(IupGetLanguage)
{
    char * str;
    if (zend_parse_parameters_none() == FAILURE) {
        return;
    }

    str = IupGetLanguage();
    
    RETURN_STRING(str);
}
/* }}} */

/* {{{ proto string IupSetLanguageString(string name, string str)
   ;
 */
PHP_FUNCTION(IupSetLanguageString)
{
    int argc = ZEND_NUM_ARGS();
    char *name = NULL;
    size_t name_len;

    char *str = NULL;
    size_t str_len;

    if (zend_parse_parameters(argc, "ss", &name, &name_len, &str, &str_len) == FAILURE) {
        return;
    }
    
    IupSetLanguageString(name,str);

    RETURN_BOOL(1);
}
/* }}} */

/* {{{ proto string IupStoreLanguageString(string name, string str)
   ;
 */
PHP_FUNCTION(IupStoreLanguageString)
{
    int argc = ZEND_NUM_ARGS();
    char *name = NULL;
    size_t name_len;

    char *str = NULL;
    size_t str_len;

    if (zend_parse_parameters(argc, "ss", &name, &name_len, &str, &str_len) == FAILURE) {
        return;
    }
    
    IupStoreLanguageString(name,str);

    RETURN_BOOL(1);
}
/* }}} */

/* {{{ proto string IupGetLanguageString(string name)
   ;
 */
PHP_FUNCTION(IupGetLanguageString)
{
    int argc = ZEND_NUM_ARGS();
    char *name = NULL;
    size_t name_len;

    char *str;

    if (zend_parse_parameters(argc, "s", &name, &name_len) == FAILURE) {
        return;
    }

    str = IupGetLanguageString(name);
    
    RETURN_STRING(str);
}
/* }}} */

/* {{{ proto string IupSetLanguagePack(resource ih)
   ;
 */
PHP_FUNCTION(IupSetLanguagePack)
{
    int argc = ZEND_NUM_ARGS();

    zval *ihandle_res = NULL;

    Ihandle *ih;

    if (zend_parse_parameters(argc TSRMLS_DC,"r",&ihandle_res) == FAILURE) {
        return;
    }

    ih = zend_fetch_resource_ex(ihandle_res,"iup-handle",le_iup_ihandle);

    IupSetLanguagePack(ih);

    RETURN_BOOL(1);
    
}
/* }}} */

/* {{{ proto string IupDestroy(resource ih)
   ;
 */
PHP_FUNCTION(IupDestroy)
{
    int argc = ZEND_NUM_ARGS();

    zval *ihandle_res = NULL;

    Ihandle *ih;

    if (zend_parse_parameters(argc TSRMLS_DC,"r",&ihandle_res) == FAILURE) {
        return;
    }

    ih = zend_fetch_resource_ex(ihandle_res,"iup-handle",le_iup_ihandle);

    IupDestroy(ih);

    RETURN_BOOL(1);
    
}
/* }}} */

/* {{{ proto string IupDetach(resource child)
   ;
 */
PHP_FUNCTION(IupDetach)
{
    int argc = ZEND_NUM_ARGS();

    zval *ihandle_res = NULL;

    Ihandle *child;

    if (zend_parse_parameters(argc TSRMLS_DC,"r",&ihandle_res) == FAILURE) {
        return;
    }

    child = zend_fetch_resource_ex(ihandle_res,"iup-handle",le_iup_ihandle);

    IupDetach(child);

    RETURN_BOOL(1);
    
}
/* }}} */

/* {{{ proto resource IupAppend(resource ih, resource child)
   ;
 */
PHP_FUNCTION(IupAppend)
{
    int argc = ZEND_NUM_ARGS();

    zval *ihandle_res_ih = NULL;
    zval *ihandle_res_child = NULL;

    Ihandle *ih, *child, *re;

    if (zend_parse_parameters(argc TSRMLS_DC,"rr",&ihandle_res_ih,&ihandle_res_child) == FAILURE) {
        return;
    }

    ih = zend_fetch_resource_ex(ihandle_res_ih,"iup-handle",le_iup_ihandle);
    child = zend_fetch_resource_ex(ihandle_res_child,"iup-handle",le_iup_ihandle);

    re = IupAppend(ih,child);

    RETURN_RES(zend_register_resource(re, le_iup_ihandle));
}
/* }}} */

/* {{{ proto resource IupInsert(resource ih, resource ref_child, resource child)
   ;
 */
PHP_FUNCTION(IupInsert)
{
    int argc = ZEND_NUM_ARGS();

    zval *ihandle_res_ih = NULL;
    zval *ihandle_res_ref_child = NULL;
    zval *ihandle_res_child = NULL;

    Ihandle *ih, *ref_child, *child, *re;

    if (zend_parse_parameters(argc TSRMLS_DC,"rrr",&ihandle_res_ih,&ihandle_res_ref_child,&ihandle_res_child) == FAILURE) {
        return;
    }

    ih = zend_fetch_resource_ex(ihandle_res_ih,"iup-handle",le_iup_ihandle);
    ref_child = zend_fetch_resource_ex(ihandle_res_ref_child,"iup-handle",le_iup_ihandle);
    child = zend_fetch_resource_ex(ihandle_res_child,"iup-handle",le_iup_ihandle);

    re = IupInsert(ih,ref_child,child);

    RETURN_RES(zend_register_resource(re, le_iup_ihandle));
}
/* }}} */

/* {{{ proto resource IupGetChild(resource ih, int pos)
   ;
 */
PHP_FUNCTION(IupGetChild)
{
    int argc = ZEND_NUM_ARGS();

    zval *ihandle_res = NULL;

    Ihandle *ih, *re;

    zend_long pos;

    if (zend_parse_parameters(argc TSRMLS_DC,"rl",&ihandle_res,&pos) == FAILURE) {
        return;
    }

    ih = zend_fetch_resource_ex(ihandle_res,"iup-handle",le_iup_ihandle);

    re = IupGetChild(ih,pos);

    RETURN_RES(zend_register_resource(re, le_iup_ihandle));
    
}
/* }}} */

/* {{{ proto int IupGetChildPos(resource ih, resource child)
   ;
 */
PHP_FUNCTION(IupGetChildPos)
{
    int argc = ZEND_NUM_ARGS();

    zval *ihandle_res_ih = NULL;
    zval *ihandle_res_child = NULL;

    Ihandle *ih, *child;

    int i;

    if (zend_parse_parameters(argc TSRMLS_DC,"rr",&ihandle_res_ih,&ihandle_res_child) == FAILURE) {
        return;
    }

    ih = zend_fetch_resource_ex(ihandle_res_ih,"iup-handle",le_iup_ihandle);
    child = zend_fetch_resource_ex(ihandle_res_child,"iup-handle",le_iup_ihandle);

    i = IupGetChildPos(ih,child);

    RETURN_LONG(i);
}
/* }}} */

/* {{{ proto int IupGetChildCount(resource ih)
   ;
 */
PHP_FUNCTION(IupGetChildCount)
{
    int argc = ZEND_NUM_ARGS();

    zval *ihandle_res = NULL;

    Ihandle *ih;

    int i;

    if (zend_parse_parameters(argc TSRMLS_DC,"r",&ihandle_res) == FAILURE) {
        return;
    }

    ih = zend_fetch_resource_ex(ihandle_res,"iup-handle",le_iup_ihandle);

    i = IupGetChildCount(ih);

    RETURN_LONG(i);
}
/* }}} */

/* {{{ proto resource IupGetNextChild(resource ih, resource child)
   ;
 */
PHP_FUNCTION(IupGetNextChild)
{
    int argc = ZEND_NUM_ARGS();

    zval *ihandle_res_ih = NULL;
    zval *ihandle_res_child = NULL;

    Ihandle *ih, *child, *re;

    if (zend_parse_parameters(argc TSRMLS_DC,"rr",&ihandle_res_ih,&ihandle_res_child) == FAILURE) {
        return;
    }

    ih = zend_fetch_resource_ex(ihandle_res_ih,"iup-handle",le_iup_ihandle);
    child = zend_fetch_resource_ex(ihandle_res_child,"iup-handle",le_iup_ihandle);

    re = IupGetNextChild(ih,child);

    RETURN_RES(zend_register_resource(re, le_iup_ihandle));
}
/* }}} */

/* {{{ proto resource IupGetBrother(resource ih)
   ;
 */
PHP_FUNCTION(IupGetBrother)
{
    int argc = ZEND_NUM_ARGS();

    zval *ihandle_res = NULL;

    Ihandle *ih, *re;

    if (zend_parse_parameters(argc TSRMLS_DC,"r",&ihandle_res) == FAILURE) {
        return;
    }

    ih = zend_fetch_resource_ex(ihandle_res,"iup-handle",le_iup_ihandle);

    re = IupGetBrother(ih);

    RETURN_RES(zend_register_resource(re, le_iup_ihandle));
    
}
/* }}} */

/* {{{ proto resource IupGetParent(resource ih)
   ;
 */
PHP_FUNCTION(IupGetParent)
{
    int argc = ZEND_NUM_ARGS();

    zval *ihandle_res = NULL;

    Ihandle *ih, *re;

    if (zend_parse_parameters(argc TSRMLS_DC,"r",&ihandle_res) == FAILURE) {
        return;
    }

    ih = zend_fetch_resource_ex(ihandle_res,"iup-handle",le_iup_ihandle);

    re = IupGetParent(ih);

    RETURN_RES(zend_register_resource(re, le_iup_ihandle));
    
}
/* }}} */

/* {{{ proto resource IupGetDialog(resource ih)
   ;
 */
PHP_FUNCTION(IupGetDialog)
{
    int argc = ZEND_NUM_ARGS();

    zval *ihandle_res = NULL;

    Ihandle *ih, *re;

    if (zend_parse_parameters(argc TSRMLS_DC,"r",&ihandle_res) == FAILURE) {
        return;
    }

    ih = zend_fetch_resource_ex(ihandle_res,"iup-handle",le_iup_ihandle);

    re = IupGetDialog(ih);

    RETURN_RES(zend_register_resource(re, le_iup_ihandle));
    
}
/* }}} */

/* {{{ proto resource IupGetDialogChild(resource ih, string name)
   ;
 */
PHP_FUNCTION(IupGetDialogChild)
{
    int argc = ZEND_NUM_ARGS();

    zval *ihandle_res = NULL;

    Ihandle *ih, *re;

    char *name = NULL;
    size_t name_len;

    if (zend_parse_parameters(argc TSRMLS_DC,"rs",&ihandle_res,&name,&name_len) == FAILURE) {
        return;
    }

    ih = zend_fetch_resource_ex(ihandle_res,"iup-handle",le_iup_ihandle);

    re = IupGetDialogChild(ih,name);

    RETURN_RES(zend_register_resource(re, le_iup_ihandle));
    
}
/* }}} */

/* {{{ proto resource IupReparent(resource ih, resource new_parent, resource ref_child)
   ;
 */
PHP_FUNCTION(IupReparent)
{
    int argc = ZEND_NUM_ARGS();

    zval *ihandle_res_ih = NULL;
    zval *ihandle_res_new_parent = NULL;
    zval *ihandle_res_ref_child = NULL;

    Ihandle *ih, *new_parent, *ref_child;

    int i;

    if (zend_parse_parameters(argc TSRMLS_DC,"rrr",&ihandle_res_ih,&ihandle_res_new_parent,&ihandle_res_ref_child) == FAILURE) {
        return;
    }

    ih = zend_fetch_resource_ex(ihandle_res_ih,"iup-handle",le_iup_ihandle);
    new_parent = zend_fetch_resource_ex(ihandle_res_new_parent,"iup-handle",le_iup_ihandle);
    ref_child = zend_fetch_resource_ex(ihandle_res_ref_child,"iup-handle",le_iup_ihandle);

    i = IupReparent(ih,new_parent,ref_child);

    RETURN_LONG(i);
}
/* }}} */

/* {{{ proto string IupPopup(resource ih, int x, int y)
   ;
 */
PHP_FUNCTION(IupPopup)
{
    int argc = ZEND_NUM_ARGS();

    zval *ihandle_res = NULL;

    Ihandle *ih;

    zend_long x,y;

    int i;

    if (zend_parse_parameters(argc TSRMLS_DC,"rll",&ihandle_res,&x,&y) == FAILURE) {
        return;
    }

    ih = zend_fetch_resource_ex(ihandle_res,"iup-handle",le_iup_ihandle);

    i = IupPopup(ih,x,y);

    RETURN_LONG(i);
}
/* }}} */

/* {{{ proto int IupShow(resource ih)
   ;
 */
PHP_FUNCTION(IupShow)
{
    int argc = ZEND_NUM_ARGS();

    zval *ihandle_res = NULL;

    Ihandle *ih;

    int i;

    if (zend_parse_parameters(argc TSRMLS_DC,"r",&ihandle_res) == FAILURE) {
        return;
    }

    ih = zend_fetch_resource_ex(ihandle_res,"iup-handle",le_iup_ihandle);

    i = IupShow(ih);

    RETURN_LONG(i);
}
/* }}} */

/* {{{ proto string IupShowXY(resource ih, int x, int y)
   ;
 */
PHP_FUNCTION(IupShowXY)
{
    int argc = ZEND_NUM_ARGS();

    zval *ihandle_res = NULL;

    Ihandle *ih;

    zend_long x,y;

    int i;

    if (zend_parse_parameters(argc TSRMLS_DC,"rll",&ihandle_res,&x,&y) == FAILURE) {
        return;
    }

    ih = zend_fetch_resource_ex(ihandle_res,"iup-handle",le_iup_ihandle);

    i = IupShowXY(ih,x,y);

    RETURN_LONG(i);
}
/* }}} */

/* {{{ proto int IupHide(resource ih)
   ;
 */
PHP_FUNCTION(IupHide)
{
    int argc = ZEND_NUM_ARGS();

    zval *ihandle_res = NULL;

    Ihandle *ih;

    int i;

    if (zend_parse_parameters(argc TSRMLS_DC,"r",&ihandle_res) == FAILURE) {
        return;
    }

    ih = zend_fetch_resource_ex(ihandle_res,"iup-handle",le_iup_ihandle);

    i = IupHide(ih);

    RETURN_LONG(i);
}
/* }}} */

/* {{{ proto int IupMap(resource ih)
   ;
 */
PHP_FUNCTION(IupMap)
{
    int argc = ZEND_NUM_ARGS();

    zval *ihandle_res = NULL;

    Ihandle *ih;

    int i;

    if (zend_parse_parameters(argc TSRMLS_DC,"r",&ihandle_res) == FAILURE) {
        return;
    }

    ih = zend_fetch_resource_ex(ihandle_res,"iup-handle",le_iup_ihandle);

    i = IupMap(ih);

    RETURN_LONG(i);
}
/* }}} */

/* {{{ proto int IupUnmap(resource ih)
   ;
 */
PHP_FUNCTION(IupUnmap)
{
    int argc = ZEND_NUM_ARGS();

    zval *ihandle_res = NULL;

    Ihandle *ih;

    if (zend_parse_parameters(argc TSRMLS_DC,"r",&ihandle_res) == FAILURE) {
        return;
    }

    ih = zend_fetch_resource_ex(ihandle_res,"iup-handle",le_iup_ihandle);

    IupUnmap(ih);

    RETURN_BOOL(1);
}
/* }}} */


/* {{{ proto resource IupResetAttribute(resource ih, string name)
   ;
 */
PHP_FUNCTION(IupResetAttribute)
{
    int argc = ZEND_NUM_ARGS();

    zval *ihandle_res = NULL;

    Ihandle *ih;

    char *name = NULL;
    size_t name_len;

    if (zend_parse_parameters(argc TSRMLS_DC,"rs",&ihandle_res,&name,&name_len) == FAILURE) {
        return;
    }

    ih = zend_fetch_resource_ex(ihandle_res,"iup-handle",le_iup_ihandle);

    IupResetAttribute(ih,name);

    RETURN_BOOL(1);
    
}
/* }}} */

/* {{{ proto resource IupGetAllAttributes(resource ih, string name, int n)
   ;
 */
PHP_FUNCTION(IupGetAllAttributes)
{
    int argc = ZEND_NUM_ARGS();

    zval *ihandle_res = NULL;

    Ihandle *ih;

    // 用以遍历arr_list数组
    zval *names_val;

    const char **names;

    zend_long n;

    int max_num,re,i;

    if (zend_parse_parameters(argc TSRMLS_DC,"rzl",&ihandle_res,&names_val,&n) == FAILURE) {
        return;
    }

    ih = zend_fetch_resource_ex(ihandle_res,"iup-handle",le_iup_ihandle);

    max_num = IupGetAllAttributes(ih,NULL,0);

    // 当未提供接收数据的数组时，直接返回最大属性数量
    if(names_val == NULL || n == 0 || n == -1){
        RETURN_LONG(max_num);
    }

    // 当设定的行数大于最大数量时，设定为最大值
    if(n > max_num){
        n = max_num;
    }

    names = (char **)malloc(sizeof(char *)* n);

    for (i = 0; i < n; i ++ )
    {
        names[i] = (char *)malloc(sizeof(char) * 1024);
    }

    re = IupGetAllAttributes(ih,names,n);

    if(re != -1){

        zval names_re;

        zend_string * zstring;

        HashTable *name_arr;

        ALLOC_HASHTABLE(name_arr);
        zend_hash_init(name_arr,re,NULL,NULL,0);

        // PHP 7.2 的特殊要求
        // 参考swoole的解决方案 c7109880427f9773b9925b046629e4e8344bdc34
        #ifdef HT_ALLOW_COW_VIOLATION
            HT_ALLOW_COW_VIOLATION(Z_ARRVAL_P(names_val));
        #endif

        // 修改引用数组的值
        for (i = 0; i < re; i ++ )
        {
            // php_error(E_WARNING, names[i]);

            zstring = zend_string_init(names[i], strlen(names[i]), 0);

            ZVAL_STR(&names_re,zstring);
            zend_hash_index_update(name_arr,i,&names_re);
        }

        zval *real_arr_val = Z_REFVAL_P(names_val);
        ZVAL_ARR(real_arr_val,name_arr);
    }

    free(names);

    RETURN_LONG(re);
}
/* }}} */

/* {{{ proto resource IupSetAtt(string handle_name, resource ih, string name, string value)
   ;
 */
PHP_FUNCTION(IupSetAtt)
{

    php_error(E_WARNING, "IupSetAtt: not yet implemented");

}
/* }}} */

/* {{{ proto resource IupSetAttributes(resource ih, string str)
   ;
 */
PHP_FUNCTION(IupSetAttributes)
{
    int argc = ZEND_NUM_ARGS();

    zval *ihandle_res = NULL;

    Ihandle *ih, *re;

    char *str = NULL;
    size_t str_len;

    if (zend_parse_parameters(argc TSRMLS_DC,"r!s",&ihandle_res,&str,&str_len) == FAILURE) {
        return;
    }

    if(ihandle_res != NULL){
        ih = zend_fetch_resource_ex(ihandle_res,"iup-handle",le_iup_ihandle);
    }else{
        ih = NULL;
    }

    re = IupSetAttributes(ih,str);

    if(re != NULL){
        RETURN_RES(zend_register_resource(re, le_iup_ihandle));
    }

    RETURN_NULL();
}
/* }}} */

/* {{{ proto char*  IupGetAttributes (Ihandle* ih);
   ;
 */
PHP_FUNCTION(IupGetAttributes)
{
    php_error(E_WARNING, "IupGetAttributes: This function should be avoided. Use IupGetAllAttributes instead.");
}
/* }}} */


/* {{{ proto resource IupSetAttribute(resource ih, string name, string value)
   ;
 */
PHP_FUNCTION(IupSetAttribute)
{
    int argc = ZEND_NUM_ARGS();

    zval *ihandle_res = NULL;

    Ihandle *ih;

    char *name = NULL;
    size_t name_len;

    const char *value = NULL;
    size_t value_len;

    if (zend_parse_parameters(argc TSRMLS_DC,"r!ss!",&ihandle_res,&name,&name_len,&value,&value_len) == FAILURE) {
        return;
    }

    if(ihandle_res != NULL){
        ih = zend_fetch_resource_ex(ihandle_res,"iup-handle",le_iup_ihandle);
    }else{
        ih = NULL;
    }

    IupSetAttribute(ih,name,value);

    RETURN_BOOL(1);
}
/* }}} */

/* {{{ proto resource IupSetStrAttribute(resource ih, string name, string value)
   ;
 */
PHP_FUNCTION(IupSetStrAttribute)
{
    int argc = ZEND_NUM_ARGS();

    zval *ihandle_res = NULL;

    Ihandle *ih;

    char *name = NULL;
    size_t name_len;

    char *value = NULL;
    size_t value_len;

    if (zend_parse_parameters(argc TSRMLS_DC,"rss",&ihandle_res,&name,&name_len,&value,&value_len) == FAILURE) {
        return;
    }

    ih = zend_fetch_resource_ex(ihandle_res,"iup-handle",le_iup_ihandle);

    IupSetStrAttribute(ih,name,value);

    RETURN_BOOL(1);
}
/* }}} */

/* {{{ proto resource IupSetStrf(resource ih, string name, string format)
   ;
 */
PHP_FUNCTION(IupSetStrf)
{

    php_error(E_WARNING, "IupSetStrf: not yet implemented");

}
/* }}} */

/* {{{ proto resource IupSetInt(resource ih, string name, int value)
   ;
 */
PHP_FUNCTION(IupSetInt)
{
    int argc = ZEND_NUM_ARGS();

    zval *ihandle_res = NULL;

    Ihandle *ih;

    char *name = NULL;
    size_t name_len;

    zend_long value;

    if (zend_parse_parameters(argc TSRMLS_DC,"rsl",&ihandle_res,&name,&name_len,&value) == FAILURE) {
        return;
    }

    ih = zend_fetch_resource_ex(ihandle_res,"iup-handle",le_iup_ihandle);

    IupSetInt(ih,name,value);

    RETURN_BOOL(1);
}
/* }}} */

/* {{{ proto resource IupSetFloat(resource ih, string name, float value)
   ;
 */
PHP_FUNCTION(IupSetFloat)
{
    int argc = ZEND_NUM_ARGS();

    zval *ihandle_res = NULL;

    Ihandle *ih;

    char *name = NULL;
    size_t name_len;

    double value;

    if (zend_parse_parameters(argc TSRMLS_DC,"rsd",&ihandle_res,&name,&name_len,&value) == FAILURE) {
        return;
    }

    ih = zend_fetch_resource_ex(ihandle_res,"iup-handle",le_iup_ihandle);

    IupSetFloat(ih,name,(float)value);

    RETURN_BOOL(1);
}
/* }}} */

/* {{{ proto resource IupSetDouble(resource ih, string name, double value)
   ;
 */
PHP_FUNCTION(IupSetDouble)
{
    int argc = ZEND_NUM_ARGS();

    zval *ihandle_res = NULL;

    Ihandle *ih;

    char *name = NULL;
    size_t name_len;

    double value;

    if (zend_parse_parameters(argc TSRMLS_DC,"rsd",&ihandle_res,&name,&name_len,&value) == FAILURE) {
        return;
    }

    ih = zend_fetch_resource_ex(ihandle_res,"iup-handle",le_iup_ihandle);

    IupSetDouble(ih,name,value);

    RETURN_BOOL(1);
}
/* }}} */


/* {{{ proto resource IupSetRGB(resource ih, string name, int r, int g, int b)
   ;
 */
PHP_FUNCTION(IupSetRGB)
{
    int argc = ZEND_NUM_ARGS();

    zval *ihandle_res = NULL;

    Ihandle *ih;

    char *name = NULL;
    size_t name_len;

    zend_long r,g,b;

    unsigned char rr,gg,bb;

    if (zend_parse_parameters(argc TSRMLS_DC,"rslll",&ihandle_res,&name,&name_len,&r,&g,&b) == FAILURE) {
        return;
    }

    ih = zend_fetch_resource_ex(ihandle_res,"iup-handle",le_iup_ihandle);

    rr = (unsigned char)r;
    gg = (unsigned char)g;
    bb = (unsigned char)b;

    IupSetRGB(ih,name,rr,gg,bb);

    RETURN_BOOL(1);

    // php_error(E_ERROR, "IupSetRGB: this function requested is not supported");
}
/* }}} */

/* {{{ proto resource IupGetAttribute(resource ih, string name)
   ;
 */
PHP_FUNCTION(IupGetAttribute)
{
    int argc = ZEND_NUM_ARGS();

    zval *ihandle_res = NULL;

    Ihandle *ih;

    char *name = NULL;
    size_t name_len;

    char * str;

    if (zend_parse_parameters(argc TSRMLS_DC,"r!s",&ihandle_res,&name,&name_len) == FAILURE) {
        return;
    }

    if(ihandle_res != NULL){
        ih = zend_fetch_resource_ex(ihandle_res,"iup-handle",le_iup_ihandle);
    }else{
        ih = NULL;
    }

    str = IupGetAttribute(ih,name);

    if(str != NULL){
        RETURN_STRING(str);
    }

    RETURN_NULL();
}
/* }}} */

/* {{{ proto resource IupGetInt(resource ih, string name)
   ;
 */
PHP_FUNCTION(IupGetInt)
{
    int argc = ZEND_NUM_ARGS();

    zval *ihandle_res = NULL;

    Ihandle *ih;

    char *name = NULL;
    size_t name_len;

    int i;

    if (zend_parse_parameters(argc TSRMLS_DC,"rs",&ihandle_res,&name,&name_len) == FAILURE) {
        return;
    }

    ih = zend_fetch_resource_ex(ihandle_res,"iup-handle",le_iup_ihandle);

    i = IupGetInt(ih,name);

    RETURN_LONG(i);
}
/* }}} */

/* {{{ proto resource IupGetInt2(resource ih, string name)
   ;
 */
PHP_FUNCTION(IupGetInt2)
{
    int argc = ZEND_NUM_ARGS();

    zval *ihandle_res = NULL;

    Ihandle *ih;

    char *name = NULL;
    size_t name_len;

    int i;

    if (zend_parse_parameters(argc TSRMLS_DC,"rs",&ihandle_res,&name,&name_len) == FAILURE) {
        return;
    }

    ih = zend_fetch_resource_ex(ihandle_res,"iup-handle",le_iup_ihandle);

    i = IupGetInt2(ih,name);

    RETURN_LONG(i);
}
/* }}} */

/* {{{ proto string IupGetIntInt(resource ih, string name, int i1, int i2)
   ;
 */
PHP_FUNCTION(IupGetIntInt)
{
    int argc = ZEND_NUM_ARGS();

    zval *ihandle_res = NULL;

    Ihandle *ih;

    char *name = NULL;
    size_t name_len;

    zend_long i1,i2;

    int i,p1,p2;

    if (zend_parse_parameters(argc TSRMLS_DC,"rsll",&ihandle_res,&name,&name_len,&i1,&i2) == FAILURE) {
        return;
    }

    ih = zend_fetch_resource_ex(ihandle_res,"iup-handle",le_iup_ihandle);

    p1 = (int)i1;
    p2 = (int)i2;

    i = IupGetIntInt(ih,name,&p1,&p2);

    RETURN_LONG(i);
}
/* }}} */

/* {{{ proto resource IupGetFloat(resource ih, string name)
   ;
 */
PHP_FUNCTION(IupGetFloat)
{
    int argc = ZEND_NUM_ARGS();

    zval *ihandle_res = NULL;

    Ihandle *ih;

    char *name = NULL;
    size_t name_len;

    double d;

    if (zend_parse_parameters(argc TSRMLS_DC,"rs",&ihandle_res,&name,&name_len) == FAILURE) {
        return;
    }

    ih = zend_fetch_resource_ex(ihandle_res,"iup-handle",le_iup_ihandle);

    d = IupGetFloat(ih,name);

    RETURN_DOUBLE(d);
}
/* }}} */

/* {{{ proto resource IupGetDouble(resource ih, string name)
   ;
 */
PHP_FUNCTION(IupGetDouble)
{
    int argc = ZEND_NUM_ARGS();

    zval *ihandle_res = NULL;

    Ihandle *ih;

    char *name = NULL;
    size_t name_len;

    double d;

    if (zend_parse_parameters(argc TSRMLS_DC,"rs",&ihandle_res,&name,&name_len) == FAILURE) {
        return;
    }

    ih = zend_fetch_resource_ex(ihandle_res,"iup-handle",le_iup_ihandle);

    d = IupGetDouble(ih,name);

    RETURN_DOUBLE(d);
}
/* }}} */

/* {{{ proto resource IupGetRGB(resource ih, string name, ref r, ref g, ref g)
   ;
 */
PHP_FUNCTION(IupGetRGB)
{
    int argc = ZEND_NUM_ARGS();

    zval *ihandle_res = NULL;

    Ihandle *ih;

    char *name = NULL;
    size_t name_len;

    unsigned char r, g, b;
    zval *rr,*gg,*bb;

    if (zend_parse_parameters(argc TSRMLS_DC,"rszzz",&ihandle_res,&name,&name_len,&rr,&gg,&bb) == FAILURE) {
        return;
    }

    ih = zend_fetch_resource_ex(ihandle_res,"iup-handle",le_iup_ihandle);

    IupGetRGB(ih,name,&r,&g,&b);

    zval *real_rr_val = Z_REFVAL_P(rr);
    ZVAL_LONG(real_rr_val,(int)r);

    zval *real_gg_val = Z_REFVAL_P(gg);
    ZVAL_LONG(real_gg_val,(int)g);

    zval *real_bb_val = Z_REFVAL_P(bb);
    ZVAL_LONG(real_bb_val,(int)b);

    RETURN_NULL();
}
/* }}} */

/* {{{ proto resource IupSetAttributeId(resource ih, string name, int id, string value)
   ;
 */
PHP_FUNCTION(IupSetAttributeId)
{
    int argc = ZEND_NUM_ARGS();

    zval *ihandle_res = NULL;

    Ihandle *ih;

    char *name = NULL;
    size_t name_len;

    zend_long id;

    char *value = NULL;
    size_t value_len;

    if (zend_parse_parameters(argc TSRMLS_DC,"r!sls!",&ihandle_res,&name,&name_len,&id,&value,&value_len) == FAILURE) {
        return;
    }

    if(ihandle_res != NULL){
        ih = zend_fetch_resource_ex(ihandle_res,"iup-handle",le_iup_ihandle);
    }else{
        ih = NULL;
    }

    IupSetAttributeId(ih,name,id,value);

    RETURN_BOOL(1);
}
/* }}} */

/* {{{ proto resource IupSetStrAttributeId(resource ih, string name, int id, string value)
   ;
 */
PHP_FUNCTION(IupSetStrAttributeId)
{
    int argc = ZEND_NUM_ARGS();

    zval *ihandle_res = NULL;

    Ihandle *ih;

    char *name = NULL;
    size_t name_len;

    zend_long id;

    char *value = NULL;
    size_t value_len;

    if (zend_parse_parameters(argc TSRMLS_DC,"rsls",&ihandle_res,&name,&name_len,&id,&value,&value_len) == FAILURE) {
        return;
    }

    ih = zend_fetch_resource_ex(ihandle_res,"iup-handle",le_iup_ihandle);

    IupSetStrAttributeId(ih,name,id,value);

    RETURN_BOOL(1);
}
/* }}} */

/* {{{ proto resource IupSetStrfId(resource ih, string name, int id, string format)
   ;
 */
PHP_FUNCTION(IupSetStrfId)
{

    php_error(E_WARNING, "IupSetStrfId: not yet implemented");

}
/* }}} */

/* {{{ proto resource IupSetIntId(resource ih, string name, int id, int value)
   ;
 */
PHP_FUNCTION(IupSetIntId)
{
    int argc = ZEND_NUM_ARGS();

    zval *ihandle_res = NULL;

    Ihandle *ih;

    char *name = NULL;
    size_t name_len;

    zend_long id;

    zend_long value;

    if (zend_parse_parameters(argc TSRMLS_DC,"rsll",&ihandle_res,&name,&name_len,&id,&value) == FAILURE) {
        return;
    }

    ih = zend_fetch_resource_ex(ihandle_res,"iup-handle",le_iup_ihandle);

    IupSetIntId(ih,name,id,value);

    RETURN_BOOL(1);
}
/* }}} */

/* {{{ proto resource IupSetFloatId(resource ih, string name, int id, float value)
   ;
 */
PHP_FUNCTION(IupSetFloatId)
{
    int argc = ZEND_NUM_ARGS();

    zval *ihandle_res = NULL;

    Ihandle *ih;

    char *name = NULL;
    size_t name_len;

    zend_long id;

    double value;

    if (zend_parse_parameters(argc TSRMLS_DC,"rsld",&ihandle_res,&name,&name_len,&id,&value) == FAILURE) {
        return;
    }

    ih = zend_fetch_resource_ex(ihandle_res,"iup-handle",le_iup_ihandle);

    IupSetFloatId(ih,name,id,(float)value);

    RETURN_BOOL(1);
}
/* }}} */

/* {{{ proto resource IupSetDoubleId(resource ih, string name, int id, double value)
   ;
 */
PHP_FUNCTION(IupSetDoubleId)
{
    int argc = ZEND_NUM_ARGS();

    zval *ihandle_res = NULL;

    Ihandle *ih;

    char *name = NULL;
    size_t name_len;

    zend_long id;

    double value;

    if (zend_parse_parameters(argc TSRMLS_DC,"rsld",&ihandle_res,&name,&name_len,&id,&value) == FAILURE) {
        return;
    }

    ih = zend_fetch_resource_ex(ihandle_res,"iup-handle",le_iup_ihandle);

    IupSetDoubleId(ih,name,id,value);

    RETURN_BOOL(1);
}
/* }}} */

/* {{{ proto resource IupSetRGBId(resource ih, string name, int id, int r, int g, int b)
   ;
 */
PHP_FUNCTION(IupSetRGBId)
{
    int argc = ZEND_NUM_ARGS();

    zval *ihandle_res = NULL;

    Ihandle *ih;

    char *name = NULL;
    size_t name_len;

    zend_long id;

    zend_long r;
    zend_long g;
    zend_long b;

    unsigned char rr;
    unsigned char gg;
    unsigned char bb;

    if (zend_parse_parameters(argc TSRMLS_DC,"rsllll",&ihandle_res,&name,&name_len,&id,&r,&g,&b) == FAILURE) {
        return;
    }

    ih = zend_fetch_resource_ex(ihandle_res,"iup-handle",le_iup_ihandle);

    rr = (unsigned char)r;
    gg = (unsigned char)g;
    bb = (unsigned char)b;

    IupSetRGBId(ih,name,id,rr,gg,bb);

    RETURN_BOOL(1);

    // php_error(E_ERROR, "IupSetRGB: this function requested is not supported");
}
/* }}} */

/* {{{ proto resource IupGetAttributeId(resource ih, string name, int id)
   ;
 */
PHP_FUNCTION(IupGetAttributeId)
{
    int argc = ZEND_NUM_ARGS();

    zval *ihandle_res = NULL;

    Ihandle *ih;

    char *name = NULL;
    size_t name_len;

    zend_long id;

    char * str;

    if (zend_parse_parameters(argc TSRMLS_DC,"r!sl",&ihandle_res,&name,&name_len,&id) == FAILURE) {
        return;
    }

    if(ihandle_res != NULL){
        ih = zend_fetch_resource_ex(ihandle_res,"iup-handle",le_iup_ihandle);
    }else{
        ih = NULL;
    }

    str = IupGetAttributeId(ih,name,id);

    if(str != NULL){
        RETURN_STRING(str);
    }

    RETURN_NULL();
}
/* }}} */

/* {{{ proto resource IupGetIntId(resource ih, string name, int id)
   ;
 */
PHP_FUNCTION(IupGetIntId)
{
    int argc = ZEND_NUM_ARGS();

    zval *ihandle_res = NULL;

    Ihandle *ih;

    char *name = NULL;
    size_t name_len;

    zend_long id;

    int i;

    if (zend_parse_parameters(argc TSRMLS_DC,"rsl",&ihandle_res,&name,&name_len,&id) == FAILURE) {
        return;
    }

    ih = zend_fetch_resource_ex(ihandle_res,"iup-handle",le_iup_ihandle);

    i = IupGetIntId(ih,name,id);

    RETURN_LONG(i);
}
/* }}} */

/* {{{ proto resource IupGetFloatId(resource ih, string name, int id)
   ;
 */
PHP_FUNCTION(IupGetFloatId)
{
    int argc = ZEND_NUM_ARGS();

    zval *ihandle_res = NULL;

    Ihandle *ih;

    char *name = NULL;
    size_t name_len;

    zend_long id;

    double d;

    if (zend_parse_parameters(argc TSRMLS_DC,"rsl",&ihandle_res,&name,&name_len,&id) == FAILURE) {
        return;
    }

    ih = zend_fetch_resource_ex(ihandle_res,"iup-handle",le_iup_ihandle);

    d = IupGetFloatId(ih,name,id);

    RETURN_DOUBLE(d);
}
/* }}} */

/* {{{ proto resource IupGetDoubleId(resource ih, string name, int id)
   ;
 */
PHP_FUNCTION(IupGetDoubleId)
{
    int argc = ZEND_NUM_ARGS();

    zval *ihandle_res = NULL;

    Ihandle *ih;

    char *name = NULL;
    size_t name_len;

    zend_long id;

    double d;

    if (zend_parse_parameters(argc TSRMLS_DC,"rsl",&ihandle_res,&name,&name_len,&id) == FAILURE) {
        return;
    }

    ih = zend_fetch_resource_ex(ihandle_res,"iup-handle",le_iup_ihandle);

    d = IupGetDoubleId(ih,name,id);

    RETURN_DOUBLE(d);
}
/* }}} */

/* {{{ proto resource IupGetRGBId(resource ih, string name,int id, ref r, ref g, ref g)
   ;
 */
PHP_FUNCTION(IupGetRGBId)
{
    int argc = ZEND_NUM_ARGS();

    zval *ihandle_res = NULL;

    Ihandle *ih;

    char *name = NULL;
    size_t name_len;

    zend_long id;

    unsigned char r, g, b;
    zval *rr,*gg,*bb;

    if (zend_parse_parameters(argc TSRMLS_DC,"rslzzz",&ihandle_res,&name,&name_len,&id,&rr,&gg,&bb) == FAILURE) {
        return;
    }

    ih = zend_fetch_resource_ex(ihandle_res,"iup-handle",le_iup_ihandle);

    IupGetRGBId(ih,name,id,&r,&g,&b);

    zval *real_rr_val = Z_REFVAL_P(rr);
    ZVAL_LONG(real_rr_val,(int)r);

    zval *real_gg_val = Z_REFVAL_P(gg);
    ZVAL_LONG(real_gg_val,(int)g);

    zval *real_bb_val = Z_REFVAL_P(bb);
    ZVAL_LONG(real_bb_val,(int)b);

    RETURN_NULL();
}

/* }}} */

/* {{{ proto resource IupSetAttributeId2(resource ih, string name, int lin, int col, string value)
   ;
 */
PHP_FUNCTION(IupSetAttributeId2)
{
    int argc = ZEND_NUM_ARGS();

    zval *ihandle_res = NULL;

    Ihandle *ih;

    char *name = NULL;
    size_t name_len;

    zend_long lin,col;

    char *value = NULL;
    size_t value_len;

    if (zend_parse_parameters(argc TSRMLS_DC,"r!slls!",&ihandle_res,&name,&name_len,&lin,&col,&value,&value_len) == FAILURE) {
        return;
    }

    if(ihandle_res != NULL){
        ih = zend_fetch_resource_ex(ihandle_res,"iup-handle",le_iup_ihandle);
    }else{
        ih = NULL;
    }

    IupSetAttributeId2(ih,name,lin,col,value);

    RETURN_BOOL(1);
}
/* }}} */

/* {{{ proto resource IupSetStrAttributeId2(resource ih, string name, int lin, int col, string value)
   ;
 */
PHP_FUNCTION(IupSetStrAttributeId2)
{
    int argc = ZEND_NUM_ARGS();

    zval *ihandle_res = NULL;

    Ihandle *ih;

    char *name = NULL;
    size_t name_len;

    zend_long lin,col;

    char *value = NULL;
    size_t value_len;

    if (zend_parse_parameters(argc TSRMLS_DC,"rslls",&ihandle_res,&name,&name_len,&lin,&col,&value,&value_len) == FAILURE) {
        return;
    }

    ih = zend_fetch_resource_ex(ihandle_res,"iup-handle",le_iup_ihandle);

    IupSetStrAttributeId2(ih,name,lin,col,value);

    RETURN_BOOL(1);
}
/* }}} */

/* {{{ proto resource IupSetStrfId2(resource ih, string name, int lin, int col, string format)
   ;
 */
PHP_FUNCTION(IupSetStrfId2)
{
    php_error(E_WARNING, "IupSetStrfId2: not yet implemented");
}
/* }}} */

/* {{{ proto resource IupSetIntId2(resource ih, string name, int lin, int col, int value)
   ;
 */
PHP_FUNCTION(IupSetIntId2)
{
    int argc = ZEND_NUM_ARGS();

    zval *ihandle_res = NULL;

    Ihandle *ih;

    char *name = NULL;
    size_t name_len;

    zend_long lin,col;

    zend_long value;

    if (zend_parse_parameters(argc TSRMLS_DC,"rslll",&ihandle_res,&name,&name_len,&lin,&col,&value) == FAILURE) {
        return;
    }

    ih = zend_fetch_resource_ex(ihandle_res,"iup-handle",le_iup_ihandle);

    IupSetIntId2(ih,name,lin,col,value);

    RETURN_BOOL(1);
}
/* }}} */

/* {{{ proto resource IupSetFloatId2(resource ih, string name, int lin, int col, float value)
   ;
 */
PHP_FUNCTION(IupSetFloatId2)
{
    int argc = ZEND_NUM_ARGS();

    zval *ihandle_res = NULL;

    Ihandle *ih;

    char *name = NULL;
    size_t name_len;

    zend_long lin,col;

    double value;

    if (zend_parse_parameters(argc TSRMLS_DC,"rslld",&ihandle_res,&name,&name_len,&lin,&col,&value) == FAILURE) {
        return;
    }

    ih = zend_fetch_resource_ex(ihandle_res,"iup-handle",le_iup_ihandle);

    IupSetFloatId2(ih,name,lin,col,(float)value);

    RETURN_BOOL(1);
}
/* }}} */

/* {{{ proto resource IupSetDoubleId2(resource ih, string name, int lin, int col, double value)
   ;
 */
PHP_FUNCTION(IupSetDoubleId2)
{
    int argc = ZEND_NUM_ARGS();

    zval *ihandle_res = NULL;

    Ihandle *ih;

    char *name = NULL;
    size_t name_len;

    zend_long lin,col;

    double value;

    if (zend_parse_parameters(argc TSRMLS_DC,"rslld",&ihandle_res,&name,&name_len,&lin,&col,&value) == FAILURE) {
        return;
    }

    ih = zend_fetch_resource_ex(ihandle_res,"iup-handle",le_iup_ihandle);

    IupSetDoubleId2(ih,name,lin,col,value);

    RETURN_BOOL(1);
}
/* }}} */


/* {{{ proto resource IupSetRGBId2(resource ih, string name, int lin, int col, int r, int g, int b)
   ;
 */
PHP_FUNCTION(IupSetRGBId2)
{
    int argc = ZEND_NUM_ARGS();

    zval *ihandle_res = NULL;

    Ihandle *ih;

    char *name = NULL;
    size_t name_len;

    zend_long lin,col;

    zend_long r;
    zend_long g;
    zend_long b;

    unsigned char rr;
    unsigned char gg;
    unsigned char bb;

    if (zend_parse_parameters(argc TSRMLS_DC,"rslllll",&ihandle_res,&name,&name_len,&lin,&col,&r,&g,&b) == FAILURE) {
        return;
    }

    ih = zend_fetch_resource_ex(ihandle_res,"iup-handle",le_iup_ihandle);

    rr = (unsigned char)r;
    gg = (unsigned char)g;
    bb = (unsigned char)b;

    IupSetRGBId2(ih,name,lin,col,rr,gg,bb);

    RETURN_BOOL(1);
}
/* }}} */

/* {{{ proto resource IupGetAttributeId2(resource ih, string name, int lin, int col)
   ;
 */
PHP_FUNCTION(IupGetAttributeId2)
{
    int argc = ZEND_NUM_ARGS();

    zval *ihandle_res = NULL;

    Ihandle *ih;

    char *name = NULL;
    size_t name_len;

    zend_long lin, col;

    char * str;

    if (zend_parse_parameters(argc TSRMLS_DC,"r!sll",&ihandle_res,&name,&name_len,&lin,&col) == FAILURE) {
        return;
    }

    if(ihandle_res != NULL){
        ih = zend_fetch_resource_ex(ihandle_res,"iup-handle",le_iup_ihandle);
    }else{
        ih = NULL;
    }

    str = IupGetAttributeId2(ih,name,lin,col);

    if(str != NULL){
        RETURN_STRING(str);
    }

    RETURN_NULL();
}
/* }}} */

/* {{{ proto resource IupGetIntId2(resource ih, string name, int lin, int col)
   ;
 */
PHP_FUNCTION(IupGetIntId2)
{
    int argc = ZEND_NUM_ARGS();

    zval *ihandle_res = NULL;

    Ihandle *ih;

    char *name = NULL;
    size_t name_len;

    zend_long lin, col;

    int i;

    if (zend_parse_parameters(argc TSRMLS_DC,"rsll",&ihandle_res,&name,&name_len,&lin,&col) == FAILURE) {
        return;
    }

    ih = zend_fetch_resource_ex(ihandle_res,"iup-handle",le_iup_ihandle);

    i = IupGetIntId2(ih,name,lin,col);

    RETURN_LONG(i);
}
/* }}} */

/* {{{ proto resource IupGetFloatId2(resource ih, string name, int lin, int col)
   ;
 */
PHP_FUNCTION(IupGetFloatId2)
{
    int argc = ZEND_NUM_ARGS();

    zval *ihandle_res = NULL;

    Ihandle *ih;

    char *name = NULL;
    size_t name_len;

    zend_long lin, col;

    double d;

    if (zend_parse_parameters(argc TSRMLS_DC,"rsll",&ihandle_res,&name,&name_len,&lin,&col) == FAILURE) {
        return;
    }

    ih = zend_fetch_resource_ex(ihandle_res,"iup-handle",le_iup_ihandle);

    d = IupGetFloatId2(ih,name,lin,col);

    RETURN_DOUBLE(d);
}
/* }}} */

/* {{{ proto resource IupGetDoubleId2(resource ih, string name, int lin, int col)
   ;
 */
PHP_FUNCTION(IupGetDoubleId2)
{
    int argc = ZEND_NUM_ARGS();

    zval *ihandle_res = NULL;

    Ihandle *ih;

    char *name = NULL;
    size_t name_len;

    zend_long lin, col;

    double d;

    if (zend_parse_parameters(argc TSRMLS_DC,"rsll",&ihandle_res,&name,&name_len,&lin,&col) == FAILURE) {
        return;
    }

    ih = zend_fetch_resource_ex(ihandle_res,"iup-handle",le_iup_ihandle);

    d = IupGetDoubleId2(ih,name,lin,col);

    RETURN_DOUBLE(d);
}
/* }}} */

/* {{{ proto resource IupGetRGBId2(resource ih, string name, int lin, int col, ref r, ref g, ref g)
   ;
 */

PHP_FUNCTION(IupGetRGBId2)
{
    int argc = ZEND_NUM_ARGS();

    zval *ihandle_res = NULL;

    Ihandle *ih;

    char *name = NULL;
    size_t name_len;

    zend_long lin, col;

    unsigned char r, g, b;
    zval *rr,*gg,*bb;

    if (zend_parse_parameters(argc TSRMLS_DC,"rsllzzz",&ihandle_res,&name,&name_len,&lin,&col,&rr,&gg,&bb) == FAILURE) {
        return;
    }

    ih = zend_fetch_resource_ex(ihandle_res,"iup-handle",le_iup_ihandle);

    IupGetRGBId2(ih,name,lin,col,&r,&g,&b);

    zval *real_rr_val = Z_REFVAL_P(rr);
    ZVAL_LONG(real_rr_val,(int)r);

    zval *real_gg_val = Z_REFVAL_P(gg);
    ZVAL_LONG(real_gg_val,(int)g);

    zval *real_bb_val = Z_REFVAL_P(bb);
    ZVAL_LONG(real_bb_val,(int)b);

    RETURN_NULL();
}
/* }}} */

/* {{{ proto void IupSetGlobal(string name, string value)
   ;
 */
PHP_FUNCTION(IupSetGlobal)
{
    int argc = ZEND_NUM_ARGS();
    char *name = NULL;
    size_t name_len;

    char *value = NULL;
    size_t value_len;

    if (zend_parse_parameters(argc, "ss", &name, &name_len, &value, &value_len) == FAILURE) {
        return;
    }

    IupSetGlobal(name,value);

    RETURN_BOOL(1);
}
/* }}} */

/* {{{ proto void IupSetStrGlobal(string name, string value)
   ;
 */
PHP_FUNCTION(IupSetStrGlobal)
{
    int argc = ZEND_NUM_ARGS();
    char *name = NULL;
    size_t name_len;

    char *value = NULL;
    size_t value_len;

    if (zend_parse_parameters(argc, "ss", &name, &name_len, &value, &value_len) == FAILURE) {
        return;
    }

    IupSetStrGlobal(name,value);

    RETURN_BOOL(1);
}
/* }}} */

/* {{{ proto string IupGetGlobal(string name)
   ;
 */
PHP_FUNCTION(IupGetGlobal)
{
    int argc = ZEND_NUM_ARGS();
    char *name = NULL;
    size_t name_len;

    char *str;

    if (zend_parse_parameters(argc, "s", &name, &name_len) == FAILURE) {
        return;
    }

    str = IupGetGlobal(name);

    RETURN_STRING(str);
}
/* }}} */

/* {{{ proto resource IupSetFocus(resource ih)
   ;
 */
PHP_FUNCTION(IupSetFocus)
{
    int argc = ZEND_NUM_ARGS();

    zval *ihandle_res = NULL;

    Ihandle *ih,*re;

    if (zend_parse_parameters(argc TSRMLS_DC,"r",&ihandle_res) == FAILURE) {
        return;
    }

    ih = zend_fetch_resource_ex(ihandle_res,"iup-handle",le_iup_ihandle);

    re = IupSetFocus(ih);

    RETURN_RES(zend_register_resource(re, le_iup_ihandle));
}
/* }}} */

/* {{{ proto resource IupGetFocus()
   ;
 */
PHP_FUNCTION(IupGetFocus)
{
    int argc = ZEND_NUM_ARGS();

    if (zend_parse_parameters_none() == FAILURE) {
        return;
    }

    Ihandle *re;

    re = IupGetFocus();

    RETURN_RES(zend_register_resource(re, le_iup_ihandle));
}
/* }}} */

/* {{{ proto resource IupPreviousField(resource ih)
   ;
 */
PHP_FUNCTION(IupPreviousField)
{
    int argc = ZEND_NUM_ARGS();

    zval *ihandle_res = NULL;

    Ihandle *ih,*re;

    if (zend_parse_parameters(argc TSRMLS_DC,"r",&ihandle_res) == FAILURE) {
        return;
    }

    ih = zend_fetch_resource_ex(ihandle_res,"iup-handle",le_iup_ihandle);

    re = IupPreviousField(ih);

    RETURN_RES(zend_register_resource(re, le_iup_ihandle));
}
/* }}} */

/* {{{ proto resource IupNextField(resource ih)
   ;
 */
PHP_FUNCTION(IupNextField)
{
    int argc = ZEND_NUM_ARGS();

    zval *ihandle_res = NULL;

    Ihandle *ih,*re;

    if (zend_parse_parameters(argc TSRMLS_DC,"r",&ihandle_res) == FAILURE) {
        return;
    }

    ih = zend_fetch_resource_ex(ihandle_res,"iup-handle",le_iup_ihandle);

    re = IupNextField(ih);

    RETURN_RES(zend_register_resource(re, le_iup_ihandle));
}
/* }}} */

/* {{{ proto string IupSetCallback(resource ih, string name, string fun_name)
   ;
 */
PHP_FUNCTION(IupSetCallback)
{
    int argc = ZEND_NUM_ARGS();

    zval *ihandle_res = NULL;

    Ihandle *ih;

    char *event_name = NULL;
    size_t event_name_len;

    zend_fcall_info callable;
    zend_fcall_info_cache call_cache;

    zend_fcall_info * call_p;

    intptr_t ih_p_int;

    char event_key_str[120];

    zend_string * event_key;

    zval event_val;

    if (zend_parse_parameters(argc TSRMLS_DC,"rsf!",&ihandle_res, &event_name, &event_name_len, &callable, &call_cache) == FAILURE) {
        return;
    }

    ih = zend_fetch_resource_ex(ihandle_res,"iup-handle",le_iup_ihandle);

    ih_p_int = (intptr_t)ih;

    sprintf(event_key_str,"EVENT_%s_%"SCNiPTR,event_name,ih_p_int);

    event_key = zend_string_init(event_key_str, strlen(event_key_str), 0);

    if(callable.size == 0){

        if(zend_hash_exists(iup_events,event_key)){
            // 释放旧事件方法占用的内存
            event_del_callback(event_key);

            // 然后再删除事件
            zend_hash_del(iup_events,event_key);            
        }

        IupSetCallback(ih,event_name,NULL);

        RETURN_BOOL(1);
    }

    // 判断事件数组中是否存有相同事件id
    if(zend_hash_exists(iup_events,event_key)){
        // 释放旧事件方法占用的内容
        event_del_callback(event_key);
    }else{
        // 绑定事件
        event_set_callback(ih, event_name);
    }

    callable.object = Z_OBJ(EX(This));

    call_p = (zend_fcall_info *)malloc(sizeof(zend_fcall_info));
    *call_p = callable;

    ZVAL_RES(&event_val,zend_register_resource(call_p, le_iup_event));
    zend_hash_update(iup_events, event_key, &event_val);

    RETURN_BOOL(1);
    
}
/* }}} */

/* {{{ proto int IupGetCallback()
   ;
 */
PHP_FUNCTION(IupGetCallback)
{

    php_error(E_WARNING, "IupGetCallback: not yet implemented");

}
/* }}} */

/* {{{ proto int IupSetCallbacks()
   ;
 */
PHP_FUNCTION(IupSetCallbacks)
{

    php_error(E_WARNING, "IupSetCallbacks: not yet implemented");

}
/* }}} */

/* {{{ proto int IupGetFunction()
   ;
 */
PHP_FUNCTION(IupGetFunction)
{

    php_error(E_WARNING, "IupGetFunction: not yet implemented");

}
/* }}} */

/* {{{ proto int IupSetFunction()
   ;
 */
PHP_FUNCTION(IupSetFunction)
{

    php_error(E_WARNING, "IupSetFunction: not yet implemented");

}
/* }}} */

/* {{{ proto resource IupGetHandle(string name)
   ;
 */
PHP_FUNCTION(IupGetHandle)
{
    int argc = ZEND_NUM_ARGS();

    char *name = NULL;
    size_t name_len;

    Ihandle *re;

    if (zend_parse_parameters(argc, "s", &name, &name_len) == FAILURE) {
        return;
    }

    re = IupGetHandle(name);

    RETURN_RES(zend_register_resource(re, le_iup_ihandle));

}
/* }}} */

/* {{{ proto resource IupSetHandle(string name, resource ih)
   ;
 */
PHP_FUNCTION(IupSetHandle)
{
    int argc = ZEND_NUM_ARGS();

    char *name = NULL;
    size_t name_len;

    zval *ihandle_res = NULL;
    
    Ihandle *ih, *re;

    if (zend_parse_parameters(argc, "sr!", &name, &name_len,&ihandle_res) == FAILURE) {
        return;
    }

    if(ihandle_res == NULL){
        ih = NULL;
    }else{
        ih = zend_fetch_resource_ex(ihandle_res,"iup-handle",le_iup_ihandle);
    }

    re = IupSetHandle(name,ih);

    RETURN_RES(zend_register_resource(re, le_iup_ihandle));

}
/* }}} */

/* {{{ proto resource IupGetAllNames(ref names, int n)
   ;
 */
PHP_FUNCTION(IupGetAllNames)
{
    int argc = ZEND_NUM_ARGS();

    // 用以遍历arr_list数组
    zval *names_val;

    const char **names;

    zend_long n;

    int max_num,re,i;

    if (zend_parse_parameters(argc,"zl",&names_val,&n) == FAILURE) {
        return;
    }

    max_num = IupGetAllNames(NULL,0);

    // 当未提供接收数据的数组时，直接返回最大属性数量
    if(names_val == NULL || n == 0 || n == -1){
        RETURN_LONG(max_num);
    }

    // 当设定的行数大于最大数量时，设定为最大值
    if(n > max_num){
        n = max_num;
    }

    names = (char **)malloc(sizeof(char *)* n);

    for (i = 0; i < n; i ++ )
    {
        names[i] = (char *)malloc(sizeof(char) * 1024);
    }

    re = IupGetAllNames(names,n);
    
    zval names_re;
    zend_string * zstring;

    HashTable *name_arr;
    ALLOC_HASHTABLE(name_arr);
    zend_hash_init(name_arr,n,NULL,NULL,0);

    // PHP 7.2 的特殊要求
    // 参考swoole的解决方案 c7109880427f9773b9925b046629e4e8344bdc34
    #ifdef HT_ALLOW_COW_VIOLATION
        HT_ALLOW_COW_VIOLATION(Z_ARRVAL_P(names_val));
    #endif
    // 修改引用数组的值
    for (i = 0; i < n; i ++ )
    {
        // php_error(E_WARNING, names[i]);
        zstring = zend_string_init(names[i], strlen(names[i]), 0);
        ZVAL_STR(&names_re,zstring);
        zend_hash_index_update(name_arr,i,&names_re);
    }

    zval *real_arr_val = Z_REFVAL_P(names_val);
    ZVAL_ARR(real_arr_val,name_arr);

    free(names);

    RETURN_LONG(re);
}
/* }}} */

/* {{{ proto resource IupGetAllDialogs(ref names, int n)
   ;
 */
PHP_FUNCTION(IupGetAllDialogs)
{
    int argc = ZEND_NUM_ARGS();

    // 用以遍历arr_list数组
    zval *names_val;

    const char **names;

    zend_long n;

    int max_num,re,i;

    if (zend_parse_parameters(argc,"zl",&names_val,&n) == FAILURE) {
        return;
    }

    max_num = IupGetAllDialogs(NULL,0);

    // 当未提供接收数据的数组时，直接返回最大属性数量
    if(names_val == NULL || n == 0 || n == -1){
        RETURN_LONG(max_num);
    }

    // 当设定的行数大于最大数量时，设定为最大值
    if(n > max_num){
        n = max_num;
    }

    names = (char **)malloc(sizeof(char *)* n);

    for (i = 0; i < n; i ++ )
    {
        names[i] = (char *)malloc(sizeof(char) * 1024);
    }

    re = IupGetAllDialogs(names,n);
    
    zval names_re;
    zend_string * zstring;

    HashTable *name_arr;
    ALLOC_HASHTABLE(name_arr);
    zend_hash_init(name_arr,n,NULL,NULL,0);

    // PHP 7.2 的特殊要求
    // 参考swoole的解决方案 c7109880427f9773b9925b046629e4e8344bdc34
    #ifdef HT_ALLOW_COW_VIOLATION
        HT_ALLOW_COW_VIOLATION(Z_ARRVAL_P(names_val));
    #endif
    // 修改引用数组的值
    for (i = 0; i < n; i ++ )
    {
        // php_error(E_WARNING, names[i]);
        zstring = zend_string_init(names[i], strlen(names[i]), 0);
        ZVAL_STR(&names_re,zstring);
        zend_hash_index_update(name_arr,i,&names_re);
    }

    zval *real_arr_val = Z_REFVAL_P(names_val);
    ZVAL_ARR(real_arr_val,name_arr);

    free(names);

    RETURN_LONG(re);
}
/* }}} */

/* {{{ proto string IupGetName(resource ih)
   ;
 */
PHP_FUNCTION(IupGetName)
{
    int argc = ZEND_NUM_ARGS();

    zval *ihandle_res = NULL;

    Ihandle *ih;

    char * str;

    if (zend_parse_parameters(argc TSRMLS_DC,"r",&ihandle_res) == FAILURE) {
        return;
    }

    ih = zend_fetch_resource_ex(ihandle_res,"iup-handle",le_iup_ihandle);

    str = IupGetName(ih);

    RETURN_STRING(str);
}
/* }}} */

/* {{{ proto resource IupSetAttributeHandle(resource ih, string name, resource ih_named)
   ;
 */
PHP_FUNCTION(IupSetAttributeHandle)
{
    int argc = ZEND_NUM_ARGS();

    zval *ihandle_res = NULL;
    zval *ihandle_res_named = NULL;

    char *name = NULL;
    size_t name_len;

    Ihandle *ih,*ih_named;

    if (zend_parse_parameters(argc TSRMLS_DC,"r!sr!",&ihandle_res,&name,&name_len,&ihandle_res_named) == FAILURE) {
        return;
    }

    if(ihandle_res != NULL){
        ih = zend_fetch_resource_ex(ihandle_res,"iup-handle",le_iup_ihandle);
    }else{
        ih = NULL;
    }

    if(ihandle_res_named != NULL){
        ih_named = zend_fetch_resource_ex(ihandle_res_named,"iup-handle",le_iup_ihandle);
    }else{
        ih_named = NULL;
    }

    IupSetAttributeHandle(ih,name,ih_named);

    RETURN_BOOL(1);
}
/* }}} */

/* {{{ proto resource IupGetAttributeHandle(resource ih, string name)
   ;
 */
PHP_FUNCTION(IupGetAttributeHandle)
{
    int argc = ZEND_NUM_ARGS();

    zval *ihandle_res = NULL;

    char *name = NULL;
    size_t name_len;

    Ihandle *ih,*re;

    if (zend_parse_parameters(argc TSRMLS_DC,"r!s",&ihandle_res,&name,&name_len) == FAILURE) {
        return;
    }

    if(ihandle_res != NULL){
        ih = zend_fetch_resource_ex(ihandle_res,"iup-handle",le_iup_ihandle);
    }else{
        ih = NULL;
    }

    re = IupGetAttributeHandle(ih,name);

    if(re != NULL){
        RETURN_RES(zend_register_resource(re, le_iup_ihandle));
    }

    RETURN_NULL();
}
/* }}} */

/* {{{ proto resource IupSetAttributeHandleId(resource ih, string name, int id, resource ih_named)
   ;
 */
PHP_FUNCTION(IupSetAttributeHandleId)
{
    int argc = ZEND_NUM_ARGS();

    zval *ihandle_res = NULL;
    zval *ihandle_res_named = NULL;

    char *name = NULL;
    size_t name_len;

    zend_long id;

    Ihandle *ih,*ih_named;

    if (zend_parse_parameters(argc TSRMLS_DC,"r!slr!",&ihandle_res,&name,&name_len,&id,&ihandle_res_named) == FAILURE) {
        return;
    }

    if(ihandle_res != NULL){
        ih = zend_fetch_resource_ex(ihandle_res,"iup-handle",le_iup_ihandle);
    }else{
        ih = NULL;
    }

    if(ihandle_res_named != NULL){
        ih_named = zend_fetch_resource_ex(ihandle_res_named,"iup-handle",le_iup_ihandle);
    }else{
        ih_named = NULL;
    }

    IupSetAttributeHandleId(ih,name,id,ih_named);

    RETURN_BOOL(1);
}
/* }}} */

/* {{{ proto resource IupGetAttributeHandleId(resource ih, string name, int id)
   ;
 */
PHP_FUNCTION(IupGetAttributeHandleId)
{
    int argc = ZEND_NUM_ARGS();

    zval *ihandle_res = NULL;

    char *name = NULL;
    size_t name_len;

    zend_long id;

    Ihandle *ih,*re;

    if (zend_parse_parameters(argc TSRMLS_DC,"r!sl",&ihandle_res,&name,&name_len,&id) == FAILURE) {
        return;
    }

    if(ihandle_res != NULL){
        ih = zend_fetch_resource_ex(ihandle_res,"iup-handle",le_iup_ihandle);
    }else{
        ih = NULL;
    }

    re = IupGetAttributeHandleId(ih,name,id);

    if(re != NULL){
        RETURN_RES(zend_register_resource(re, le_iup_ihandle));
    }

    RETURN_NULL();

}
/* }}} */

/* {{{ proto resource IupSetAttributeHandleId2(resource ih, string name, int lin, int col, resource ih_named)
   ;
 */
PHP_FUNCTION(IupSetAttributeHandleId2)
{
    int argc = ZEND_NUM_ARGS();

    zval *ihandle_res = NULL;
    zval *ihandle_res_named = NULL;

    char *name = NULL;
    size_t name_len;

    zend_long lin,col;

    Ihandle *ih,*ih_named;

    if (zend_parse_parameters(argc TSRMLS_DC,"r!sllr!",&ihandle_res,&name,&name_len,&lin,&col,&ihandle_res_named) == FAILURE) {
        return;
    }

    if(ihandle_res != NULL){
        ih = zend_fetch_resource_ex(ihandle_res,"iup-handle",le_iup_ihandle);
    }else{
        ih = NULL;
    }

    if(ihandle_res_named != NULL){
        ih_named = zend_fetch_resource_ex(ihandle_res_named,"iup-handle",le_iup_ihandle);
    }else{
        ih_named = NULL;
    }

    IupSetAttributeHandleId2(ih,name,lin,col,ih_named);

    RETURN_BOOL(1);
}
/* }}} */

/* {{{ proto resource IupGetAttributeHandleId2(resource ih, string name, int lin, int col)
   ;
 */
PHP_FUNCTION(IupGetAttributeHandleId2)
{
    int argc = ZEND_NUM_ARGS();

    zval *ihandle_res = NULL;

    char *name = NULL;
    size_t name_len;

    zend_long lin,col;

    Ihandle *ih,*re;

    if (zend_parse_parameters(argc TSRMLS_DC,"r!sll",&ihandle_res,&name,&name_len,&lin,&col) == FAILURE) {
        return;
    }

    if(ihandle_res != NULL){
        ih = zend_fetch_resource_ex(ihandle_res,"iup-handle",le_iup_ihandle);
    }else{
        ih = NULL;
    }

    re = IupGetAttributeHandleId2(ih,name,lin,col);

    if(re != NULL){
        RETURN_RES(zend_register_resource(re, le_iup_ihandle));
    }

    RETURN_NULL();
}
/* }}} */

/* {{{ proto string IupGetClassName(resource ih)
   ;
 */
PHP_FUNCTION(IupGetClassName)
{
    int argc = ZEND_NUM_ARGS();

    zval *ihandle_res = NULL;

    Ihandle *ih;

    char * str;

    if (zend_parse_parameters(argc TSRMLS_DC,"r",&ihandle_res) == FAILURE) {
        return;
    }

    ih = zend_fetch_resource_ex(ihandle_res,"iup-handle",le_iup_ihandle);

    str = IupGetClassName(ih);

    RETURN_STRING(str);
}
/* }}} */

/* {{{ proto string IupGetClassType(resource ih)
   ;
 */
PHP_FUNCTION(IupGetClassType)
{
    int argc = ZEND_NUM_ARGS();

    zval *ihandle_res = NULL;

    Ihandle *ih;

    char * str;

    if (zend_parse_parameters(argc TSRMLS_DC,"r",&ihandle_res) == FAILURE) {
        return;
    }

    ih = zend_fetch_resource_ex(ihandle_res,"iup-handle",le_iup_ihandle);

    str = IupGetClassType(ih);

    RETURN_STRING(str);
}
/* }}} */

/* {{{ proto resource IupGetAllClasses(ref names, int n)
   ;
 */
PHP_FUNCTION(IupGetAllClasses)
{
    int argc = ZEND_NUM_ARGS();

    // 用以遍历arr_list数组
    zval *names_val;

    const char **names;

    zend_long n;

    int max_num,re,i;

    if (zend_parse_parameters(argc,"zl",&names_val,&n) == FAILURE) {
        return;
    }

    max_num = IupGetAllClasses(NULL,0);

    // 当未提供接收数据的数组时，直接返回最大属性数量
    if(names_val == NULL || n == 0 || n == -1){
        RETURN_LONG(max_num);
    }

    // 当设定的行数大于最大数量时，设定为最大值
    if(n > max_num){
        n = max_num;
    }

    names = (char **)malloc(sizeof(char *)* n);

    for (i = 0; i < n; i ++ )
    {
        names[i] = (char *)malloc(sizeof(char) * 1024);
    }

    re = IupGetAllClasses(names,n);
    
    zval names_re;
    zend_string * zstring;

    HashTable *name_arr;
    ALLOC_HASHTABLE(name_arr);
    zend_hash_init(name_arr,n,NULL,NULL,0);

    // PHP 7.2 的特殊要求
    // 参考swoole的解决方案 c7109880427f9773b9925b046629e4e8344bdc34
    #ifdef HT_ALLOW_COW_VIOLATION
        HT_ALLOW_COW_VIOLATION(Z_ARRVAL_P(names_val));
    #endif
    // 修改引用数组的值
    for (i = 0; i < n; i ++ )
    {
        // php_error(E_WARNING, names[i]);
        zstring = zend_string_init(names[i], strlen(names[i]), 0);
        ZVAL_STR(&names_re,zstring);
        zend_hash_index_update(name_arr,i,&names_re);
    }

    zval *real_arr_val = Z_REFVAL_P(names_val);
    ZVAL_ARR(real_arr_val,name_arr);

    free(names);

    RETURN_LONG(re);
}
/* }}} */

/* {{{ proto resource IupGetClassAttributes(string name, ref names, int n)
   ;
 */
PHP_FUNCTION(IupGetClassAttributes)
{
    int argc = ZEND_NUM_ARGS();

    char *name = NULL;
    size_t name_len;

    // 用以遍历arr_list数组
    zval *names_val;

    const char **names;

    zend_long n;

    int max_num,re,i;

    if (zend_parse_parameters(argc TSRMLS_DC,"szl",&name,&name_len,&names_val,&n) == FAILURE) {
        return;
    }

    max_num = IupGetClassAttributes(name,NULL,0);

    // 当未提供接收数据的数组时，直接返回最大属性数量
    if(names_val == NULL || n == 0 || n == -1){
        RETURN_LONG(max_num);
    }

    // 当设定的行数大于最大数量时，设定为最大值
    if(n > max_num){
        n = max_num;
    }

    names = (char **)malloc(sizeof(char *)* n);

    for (i = 0; i < n; i ++ )
    {
        names[i] = (char *)malloc(sizeof(char) * 1024);
    }

    re = IupGetClassAttributes(name,names,n);

    if(re != -1){

        zval names_re;

        zend_string * zstring;

        HashTable *name_arr;
        ALLOC_HASHTABLE(name_arr);
        zend_hash_init(name_arr,re,NULL,NULL,0);

        // PHP 7.2 的特殊要求
        // 参考swoole的解决方案 c7109880427f9773b9925b046629e4e8344bdc34
        #ifdef HT_ALLOW_COW_VIOLATION
            HT_ALLOW_COW_VIOLATION(Z_ARRVAL_P(names_val));
        #endif

        // 修改引用数组的值
        for (i = 0; i < n; i ++ )
        {
            // php_error(E_WARNING, names[i]);

            zstring = zend_string_init(names[i], strlen(names[i]), 0);

            ZVAL_STR(&names_re,zstring);

            // zend_hash_index_update(Z_ARRVAL_P(names_val),i,&names_re);

            zend_hash_index_update(name_arr,i,&names_re);
        }

        zval *real_arr_val = Z_REFVAL_P(names_val);
        ZVAL_ARR(real_arr_val,name_arr);
    }

    free(names);

    RETURN_LONG(re);
}
/* }}} */

/* {{{ proto resource IupGetClassCallbacks(string name, ref names, int n)
   ;
 */
PHP_FUNCTION(IupGetClassCallbacks)
{
    int argc = ZEND_NUM_ARGS();

    char *name = NULL;
    size_t name_len;

    // 用以遍历arr_list数组
    zval *names_val;

    const char **names;

    zend_long n;

    int max_num,re,i;

    if (zend_parse_parameters(argc TSRMLS_DC,"szl",&name,&name_len,&names_val,&n) == FAILURE) {
        return;
    }

    max_num = IupGetClassCallbacks(name,NULL,0);

    // 当未提供接收数据的数组时，直接返回最大属性数量
    if(names_val == NULL || n == 0 || n == -1){
        RETURN_LONG(max_num);
    }

    // 当设定的行数大于最大数量时，设定为最大值
    if(n > max_num){
        n = max_num;
    }

    names = (char **)malloc(sizeof(char *)* n);

    for (i = 0; i < n; i ++ )
    {
        names[i] = (char *)malloc(sizeof(char) * 1024);
    }

    re = IupGetClassCallbacks(name,names,n);

    if(re != -1){

        zval names_re;

        zend_string * zstring;

        HashTable *name_arr;
        ALLOC_HASHTABLE(name_arr);
        zend_hash_init(name_arr,n,NULL,NULL,0);

        // PHP 7.2 的特殊要求
        // 参考swoole的解决方案 c7109880427f9773b9925b046629e4e8344bdc34
        #ifdef HT_ALLOW_COW_VIOLATION
            HT_ALLOW_COW_VIOLATION(Z_ARRVAL_P(names_val));
        #endif

        // 修改引用数组的值
        for (i = 0; i < n; i ++ )
        {
            // php_error(E_WARNING, names[i]);

            zstring = zend_string_init(names[i], strlen(names[i]), 0);

            ZVAL_STR(&names_re,zstring);

            zend_hash_index_update(name_arr,i,&names_re);
        }
        zval *real_arr_val = Z_REFVAL_P(names_val);
        ZVAL_ARR(real_arr_val,name_arr);
    }

    free(names);

    RETURN_LONG(re);
}
/* }}} */

/* {{{ proto string IupSaveClassAttributes(resource ih)
   ;
 */
PHP_FUNCTION(IupSaveClassAttributes)
{
    int argc = ZEND_NUM_ARGS();

    zval *ihandle_res = NULL;

    Ihandle *ih;

    if (zend_parse_parameters(argc TSRMLS_DC,"r",&ihandle_res) == FAILURE) {
        return;
    }

    ih = zend_fetch_resource_ex(ihandle_res,"iup-handle",le_iup_ihandle);

    IupSaveClassAttributes(ih);

    RETURN_BOOL(1);
}
/* }}} */

/* {{{ proto string IupCopyClassAttributes(resource ih, resource det_ih)
   ;
 */
PHP_FUNCTION(IupCopyClassAttributes)
{
    int argc = ZEND_NUM_ARGS();

    zval *ihandle_res = NULL;
    zval *ihandle_res_det = NULL;

    Ihandle *ih,*dst_ih;

    if (zend_parse_parameters(argc TSRMLS_DC,"rr",&ihandle_res,&ihandle_res_det) == FAILURE) {
        return;
    }

    ih = zend_fetch_resource_ex(ihandle_res,"iup-handle",le_iup_ihandle);
    dst_ih = zend_fetch_resource_ex(ihandle_res,"iup-handle",le_iup_ihandle);

    IupCopyClassAttributes(ih,dst_ih);

    RETURN_BOOL(1);
}
/* }}} */

/* {{{ proto string IupSetClassDefaultAttribute(string classname, string name, string value)
   ;
 */
PHP_FUNCTION(IupSetClassDefaultAttribute)
{
    int argc = ZEND_NUM_ARGS();
    char *classname = NULL;
    size_t classname_len;

    char *name = NULL;
    size_t name_len;

    char *value = NULL;
    size_t value_len;
    if (zend_parse_parameters(argc, "sss", &classname, &classname_len,&name, &name_len,&value, &value_len) == FAILURE) {
        return;
    }

    IupSetClassDefaultAttribute(classname,name,value);

    RETURN_BOOL(1);
}
/* }}} */

/* {{{ proto int IupClassMatch(resource ih, string classname)
   ;
 */
PHP_FUNCTION(IupClassMatch)
{
    int argc = ZEND_NUM_ARGS();

    zval *ihandle_res = NULL;

    char *classname = NULL;
    size_t classname_len;

    Ihandle *ih;

    int i;

    if (zend_parse_parameters(argc TSRMLS_DC,"rs",&ihandle_res,&classname,&classname_len) == FAILURE) {
        return;
    }

    ih = zend_fetch_resource_ex(ihandle_res,"iup-handle",le_iup_ihandle);

    i = IupClassMatch(ih,classname);

    RETURN_LONG(i);
}
/* }}} */

/* {{{ proto int IupCreate()
   ;
 */
PHP_FUNCTION(IupCreatek)
{

    php_error(E_WARNING, "IupCreate: not yet implemented");

}
/* }}} */

/* {{{ proto int IupCreatev()
   ;
 */
PHP_FUNCTION(IupCreatev)
{

    php_error(E_WARNING, "IupCreatev: not yet implemented");

}
/* }}} */

/* {{{ proto int IupCreatep()
   ;
 */
PHP_FUNCTION(IupCreatep)
{

    php_error(E_WARNING, "IupCreatep: not yet implemented");

}
/* }}} */

/* {{{ proto resource IupFill()
   ;
 */
PHP_FUNCTION(IupFill)
{
    Ihandle *re;

    if (zend_parse_parameters_none() == FAILURE) {
        return;
    }

    re = IupFill();

    RETURN_RES(zend_register_resource(re, le_iup_ihandle));
}
/* }}} */

/* {{{ proto resource IupSpace()
   ;
 */
PHP_FUNCTION(IupSpace)
{
    Ihandle *re;

    if (zend_parse_parameters_none() == FAILURE) {
        return;
    }

    re = IupSpace();

    RETURN_RES(zend_register_resource(re, le_iup_ihandle));
}
/* }}} */

/* {{{ proto resource IupRadio(resource child)
   ;
 */
PHP_FUNCTION(IupRadio)
{
    int argc = ZEND_NUM_ARGS();

    zval *ihandle_res = NULL;

    Ihandle *child,*re;

    if (zend_parse_parameters(argc TSRMLS_DC,"r!",&ihandle_res) == FAILURE) {
        return;
    }

    if(ihandle_res == NULL){
        re = IupRadio(NULL);
    }else{
        child = zend_fetch_resource_ex(ihandle_res,"iup-handle",le_iup_ihandle);

        re = IupRadio(child);
    }

    RETURN_RES(zend_register_resource(re, le_iup_ihandle));
}
/* }}} */

/* {{{ proto resource IupVbox(resource child)
   ;
 */
PHP_FUNCTION(IupVbox)
{

    zval *args;
    int argc;
    int i;

    Ihandle *child,*re;

    ZEND_PARSE_PARAMETERS_START(0, -1)
        Z_PARAM_VARIADIC('+', args, argc)
    ZEND_PARSE_PARAMETERS_END();

    if(argc < 1){
        re = IupVbox(NULL);
    }else{
        for (i = 0; i < argc; i++) {

            child = zend_fetch_resource_ex(&args[i],"iup-handle",le_iup_ihandle);

            if(i == 0){
                re = IupVbox(child,NULL);
            }else{
                IupAppend(re,child);
            }
        }        
    }

    RETURN_RES(zend_register_resource(re, le_iup_ihandle));
}
/* }}} */

/* {{{ proto resource IupVboxv(resource children)
   ;
 */
PHP_FUNCTION(IupVboxv)
{
    int argc = ZEND_NUM_ARGS();

    zval *ihandle_res = NULL;

    Ihandle *children,*re;

    if (zend_parse_parameters(argc TSRMLS_DC,"r!",&ihandle_res) == FAILURE) {
        return;
    }

    if(ihandle_res == NULL){
        re = IupVboxv(NULL);
    }else{
        children = zend_fetch_resource_ex(ihandle_res,"iup-handle",le_iup_ihandle);

        re = IupVboxv(&children);
    }

    RETURN_RES(zend_register_resource(re, le_iup_ihandle));
}
/* }}} */

/* {{{ proto resource IupZbox(resource child)
   ;
 */
PHP_FUNCTION(IupZbox)
{

    zval *args;
    int argc;
    int i;

    Ihandle *child,*re;

    ZEND_PARSE_PARAMETERS_START(0, -1)
        Z_PARAM_VARIADIC('+', args, argc)
    ZEND_PARSE_PARAMETERS_END();

    if(argc < 1){
        re = IupZbox(NULL);
    }else{
        for (i = 0; i < argc; i++) {

            child = zend_fetch_resource_ex(&args[i],"iup-handle",le_iup_ihandle);

            if(i == 0){
                re = IupZbox(child,NULL);
            }else{
                IupAppend(re,child);
            }
        }        
    }

    RETURN_RES(zend_register_resource(re, le_iup_ihandle));
}
/* }}} */

/* {{{ proto resource IupZboxv(resource children)
   ;
 */
PHP_FUNCTION(IupZboxv)
{
    int argc = ZEND_NUM_ARGS();

    zval *ihandle_res = NULL;

    Ihandle *children,*re;

    if (zend_parse_parameters(argc TSRMLS_DC,"r!",&ihandle_res) == FAILURE) {
        return;
    }

    if(ihandle_res == NULL){
        re = IupZboxv(NULL);
    }else{
        children = zend_fetch_resource_ex(ihandle_res,"iup-handle",le_iup_ihandle);
        re = IupZboxv(&children);
    }

    RETURN_RES(zend_register_resource(re, le_iup_ihandle));
}
/* }}} */

/* {{{ proto resource IupHbox(resource child)
   ;
 */
PHP_FUNCTION(IupHbox)
{

    zval *args;
    int argc;
    int i;

    Ihandle *child,*re;

    ZEND_PARSE_PARAMETERS_START(0, -1)
        Z_PARAM_VARIADIC('+', args, argc)
    ZEND_PARSE_PARAMETERS_END();

    if(argc < 1){
        re = IupHbox(NULL);
    }else{
        for (i = 0; i < argc; i++) {

            child = zend_fetch_resource_ex(&args[i],"iup-handle",le_iup_ihandle);

            if(i == 0){
                re = IupHbox(child,NULL);
            }else{
                IupAppend(re,child);
            }
        }        
    }

    RETURN_RES(zend_register_resource(re, le_iup_ihandle));
}
/* }}} */

/* {{{ proto resource IupHboxv(resource children)
   ;
 */
PHP_FUNCTION(IupHboxv)
{
    int argc = ZEND_NUM_ARGS();

    zval *ihandle_res = NULL;

    Ihandle *children,*re;

    if (zend_parse_parameters(argc TSRMLS_DC,"r!",&ihandle_res) == FAILURE) {
        return;
    }

    if(ihandle_res == NULL){
        re = IupHboxv(NULL);
    }else{
        children = zend_fetch_resource_ex(ihandle_res,"iup-handle",le_iup_ihandle);
        re = IupHboxv(&children);
    }

    RETURN_RES(zend_register_resource(re, le_iup_ihandle));
}
/* }}} */

/* {{{ proto resource IupNormalizer(resource child)
   ;
 */
PHP_FUNCTION(IupNormalizer)
{

    zval *args;
    int argc;
    int i;

    Ihandle *child,*re;

    ZEND_PARSE_PARAMETERS_START(0, -1)
        Z_PARAM_VARIADIC('+', args, argc)
    ZEND_PARSE_PARAMETERS_END();

    if(argc < 1){
        re = IupNormalizer(NULL);
    }else{
        for (i = 0; i < argc; i++) {

            child = zend_fetch_resource_ex(&args[i],"iup-handle",le_iup_ihandle);

            if(i == 0){
                re = IupNormalizer(child,NULL);
            }else{
                IupAppend(re,child);
            }
        }        
    }

    RETURN_RES(zend_register_resource(re, le_iup_ihandle));
}
/* }}} */

/* {{{ proto resource IupNormalizerv(resource ih_list)
   ;
 */
PHP_FUNCTION(IupNormalizerv)
{
    int argc = ZEND_NUM_ARGS();

    zval *ihandle_res = NULL;

    Ihandle *ih_list,*re;

    if (zend_parse_parameters(argc TSRMLS_DC,"r!",&ihandle_res) == FAILURE) {
        return;
    }

    if(ihandle_res == NULL){
        re = IupNormalizerv(NULL);
    }else{
        ih_list = zend_fetch_resource_ex(ihandle_res,"iup-handle",le_iup_ihandle);
        re = IupNormalizerv(&ih_list);
    }

    RETURN_RES(zend_register_resource(re, le_iup_ihandle));
}
/* }}} */

/* {{{ proto resource IupCbox(resource child)
   ;
 */
PHP_FUNCTION(IupCbox)
{

    zval *args;
    int argc;
    int i;

    Ihandle *child,*re;

    ZEND_PARSE_PARAMETERS_START(0, -1)
        Z_PARAM_VARIADIC('+', args, argc)
    ZEND_PARSE_PARAMETERS_END();

    if(argc < 1){
        re = IupCbox(NULL);
    }else{
        for (i = 0; i < argc; i++) {

            child = zend_fetch_resource_ex(&args[i],"iup-handle",le_iup_ihandle);

            if(i == 0){
                re = IupCbox(child,NULL);
            }else{
                IupAppend(re,child);
            }
        }        
    }

    RETURN_RES(zend_register_resource(re, le_iup_ihandle));
}
/* }}} */

/* {{{ proto resource IupCboxv(resource children)
   ;
 */
PHP_FUNCTION(IupCboxv)
{
    int argc = ZEND_NUM_ARGS();

    zval *ihandle_res = NULL;

    Ihandle *children,*re;

    if (zend_parse_parameters(argc TSRMLS_DC,"r!",&ihandle_res) == FAILURE) {
        return;
    }

    if(ihandle_res == NULL){
        re = IupCboxv(NULL);
    }else{
        children = zend_fetch_resource_ex(ihandle_res,"iup-handle",le_iup_ihandle);
        re = IupCboxv(&children);
    }

    RETURN_RES(zend_register_resource(re, le_iup_ihandle));
}
/* }}} */

/* {{{ proto resource IupSbox(resource child)
   ;
 */
PHP_FUNCTION(IupSbox)
{
    int argc = ZEND_NUM_ARGS();

    zval *ihandle_res = NULL;

    Ihandle *child,*re;

    if (zend_parse_parameters(argc TSRMLS_DC,"r!",&ihandle_res) == FAILURE) {
        return;
    }

    if(ihandle_res == NULL){
        re = IupSbox(NULL);
    }else{
        child = zend_fetch_resource_ex(ihandle_res,"iup-handle",le_iup_ihandle);
        re = IupSbox(child);
    }

    RETURN_RES(zend_register_resource(re, le_iup_ihandle));
}
/* }}} */

/* {{{ proto resource IupSplit(resource child1, resource child2)
   ;
 */
PHP_FUNCTION(IupSplit)
{
    int argc = ZEND_NUM_ARGS();

    zval *ihandle_res_child1 = NULL;
    zval *ihandle_res_child2 = NULL;

    Ihandle *child1,*child2,*re;

    if (zend_parse_parameters(argc TSRMLS_DC,"r!r!",&ihandle_res_child1,&ihandle_res_child2) == FAILURE) {
        return;
    }

    if(ihandle_res_child1 == NULL){
        child1 = NULL;
    }else{
        child1 = zend_fetch_resource_ex(ihandle_res_child1,"iup-handle",le_iup_ihandle);
    }

    if(ihandle_res_child2 == NULL){
        child2 = NULL;
    }else{
        child2 = zend_fetch_resource_ex(ihandle_res_child2,"iup-handle",le_iup_ihandle);
    }

    re = IupSplit(child1,child2);

    RETURN_RES(zend_register_resource(re, le_iup_ihandle));
}
/* }}} */

/* {{{ proto resource IupScrollBox(resource child)
   ;
 */
PHP_FUNCTION(IupScrollBox)
{
    int argc = ZEND_NUM_ARGS();

    zval *ihandle_res = NULL;

    Ihandle *child,*re;

    if (zend_parse_parameters(argc TSRMLS_DC,"r!",&ihandle_res) == FAILURE) {
        return;
    }

    if(ihandle_res == NULL){
        re = IupScrollBox(NULL);
    }else{
        child = zend_fetch_resource_ex(ihandle_res,"iup-handle",le_iup_ihandle);
        re = IupScrollBox(child);
    }

    RETURN_RES(zend_register_resource(re, le_iup_ihandle));
}
/* }}} */

/* {{{ proto resource IupFlatScrollBox(resource child)
   ;
 */
PHP_FUNCTION(IupFlatScrollBox)
{
    int argc = ZEND_NUM_ARGS();

    zval *ihandle_res = NULL;

    Ihandle *child,*re;

    if (zend_parse_parameters(argc TSRMLS_DC,"r!",&ihandle_res) == FAILURE) {
        return;
    }

    if(ihandle_res == NULL){
        re = IupFlatScrollBox(NULL);
    }else{
        child = zend_fetch_resource_ex(ihandle_res,"iup-handle",le_iup_ihandle);
        re = IupFlatScrollBox(child);
    }

    RETURN_RES(zend_register_resource(re, le_iup_ihandle));
}
/* }}} */

/* {{{ proto resource IupGridBox(resource child)
   ;
 */
PHP_FUNCTION(IupGridBox)
{

    zval *args;
    int argc;
    int i;

    Ihandle *child,*re;

    ZEND_PARSE_PARAMETERS_START(0, -1)
        Z_PARAM_VARIADIC('+', args, argc)
    ZEND_PARSE_PARAMETERS_END();

    if(argc < 1){
        re = IupGridBox(NULL);
    }else{
        for (i = 0; i < argc; i++) {

            child = zend_fetch_resource_ex(&args[i],"iup-handle",le_iup_ihandle);

            if(i == 0){
                re = IupGridBox(child,NULL);
            }else{
                IupAppend(re,child);
            }
        }        
    }

    RETURN_RES(zend_register_resource(re, le_iup_ihandle));
}
/* }}} */

/* {{{ proto resource IupGridBoxv(resource children)
   ;
 */
PHP_FUNCTION(IupGridBoxv)
{
    int argc = ZEND_NUM_ARGS();

    zval *ihandle_res = NULL;

    Ihandle *children,*re;

    if (zend_parse_parameters(argc TSRMLS_DC,"r!",&ihandle_res) == FAILURE) {
        return;
    }

    if(ihandle_res == NULL){
        re = IupGridBoxv(NULL);
    }else{
        children = zend_fetch_resource_ex(ihandle_res,"iup-handle",le_iup_ihandle);
        re = IupGridBoxv(&children);
    }

    RETURN_RES(zend_register_resource(re, le_iup_ihandle));
}
/* }}} */

/* {{{ proto resource IupExpander(resource child)
   ;
 */
PHP_FUNCTION(IupExpander)
{
    int argc = ZEND_NUM_ARGS();

    zval *ihandle_res = NULL;

    Ihandle *child,*re;

    if (zend_parse_parameters(argc TSRMLS_DC,"r!",&ihandle_res) == FAILURE) {
        return;
    }

    if(ihandle_res == NULL){
        re = IupExpander(NULL);
    }else{
        child = zend_fetch_resource_ex(ihandle_res,"iup-handle",le_iup_ihandle);
        re = IupExpander(child);
    }

    RETURN_RES(zend_register_resource(re, le_iup_ihandle));
}
/* }}} */

/* {{{ proto resource IupDetachBox(resource child)
   ;
 */
PHP_FUNCTION(IupDetachBox)
{
    int argc = ZEND_NUM_ARGS();

    zval *ihandle_res = NULL;

    Ihandle *child,*re;

    if (zend_parse_parameters(argc TSRMLS_DC,"r!",&ihandle_res) == FAILURE) {
        return;
    }

    if(ihandle_res == NULL){
        re = IupDetachBox(NULL);
    }else{
        child = zend_fetch_resource_ex(ihandle_res,"iup-handle",le_iup_ihandle);
        re = IupDetachBox(child);
    }

    RETURN_RES(zend_register_resource(re, le_iup_ihandle));
}
/* }}} */

/* {{{ proto resource IupBackgroundBox(resource child)
   ;
 */
PHP_FUNCTION(IupBackgroundBox)
{
    int argc = ZEND_NUM_ARGS();

    zval *ihandle_res = NULL;

    Ihandle *child,*re;

    if (zend_parse_parameters(argc TSRMLS_DC,"r!",&ihandle_res) == FAILURE) {
        return;
    }

    if(ihandle_res == NULL){
        re = IupBackgroundBox(NULL);    
    }else{
        child = zend_fetch_resource_ex(ihandle_res,"iup-handle",le_iup_ihandle);
        re = IupBackgroundBox(child);    
    }
    
    RETURN_RES(zend_register_resource(re, le_iup_ihandle));
}
/* }}} */

/* {{{ proto resource IupFrame(resource child)
   ;
 */
PHP_FUNCTION(IupFrame)
{
    int argc = ZEND_NUM_ARGS();

    zval *ihandle_res = NULL;

    Ihandle *child,*re;

    if (zend_parse_parameters(argc TSRMLS_DC,"r!",&ihandle_res) == FAILURE) {
        return;
    }

    if(ihandle_res == NULL){
        re = IupFrame(NULL);        
    }else{
        child = zend_fetch_resource_ex(ihandle_res,"iup-handle",le_iup_ihandle);
        re = IupFrame(child);        
    }

    RETURN_RES(zend_register_resource(re, le_iup_ihandle));
}
/* }}} */

/* {{{ proto resource IupFlatFrame(resource child)
   ;
 */
PHP_FUNCTION(IupFlatFrame)
{
    int argc = ZEND_NUM_ARGS();

    zval *ihandle_res = NULL;

    Ihandle *child,*re;

    if (zend_parse_parameters(argc TSRMLS_DC,"r!",&ihandle_res) == FAILURE) {
        return;
    }
    if(ihandle_res == NULL){
        re = IupFlatFrame(NULL);
    }else{
        child = zend_fetch_resource_ex(ihandle_res,"iup-handle",le_iup_ihandle);
        re = IupFlatFrame(child);
    }

    RETURN_RES(zend_register_resource(re, le_iup_ihandle));
}
/* }}} */

/* {{{ proto int IupImage()
   ;
 */
PHP_FUNCTION(IupImage)
{

    php_error(E_WARNING, "IupImage: not yet implemented");

}
/* }}} */

/* {{{ proto int IupImageRGB()
   ;
 */
PHP_FUNCTION(IupImageRGB)
{

    php_error(E_WARNING, "IupImageRGB: not yet implemented");

}
/* }}} */

/* {{{ proto int IupImageRGBA()
   ;
 */
PHP_FUNCTION(IupImageRGBA)
{

    php_error(E_WARNING, "IupImageRGBA: not yet implemented");

}
/* }}} */

/* {{{ proto string IupItem(string name, string action)
   ;
 */
PHP_FUNCTION(IupItem)
{
    int argc = ZEND_NUM_ARGS();

    char *name = NULL;
    size_t name_len;

    char *action = NULL;
    size_t action_len;

    Ihandle *re;

    if (zend_parse_parameters(argc, "s!s!", &name, &name_len, &action, &action_len) == FAILURE) {
        return;
    }

    re = IupItem(name,action);

    RETURN_RES(zend_register_resource(re, le_iup_ihandle));
}
/* }}} */

/* {{{ proto string IupSubmenu(string name, string child)
   ;
 */
PHP_FUNCTION(IupSubmenu)
{
    int argc = ZEND_NUM_ARGS();

    char *name = NULL;
    size_t name_len;

    zval *ihandle_res = NULL;

    Ihandle *child,*re;

    if (zend_parse_parameters(argc TSRMLS_DC,"s!r!", &name, &name_len, &ihandle_res) == FAILURE) {
        return;
    }

    if(ihandle_res == NULL){
        child = NULL;
    }else{
        child = zend_fetch_resource_ex(ihandle_res,"iup-handle",le_iup_ihandle);
    }


    re = IupSubmenu(name,child);

    RETURN_RES(zend_register_resource(re, le_iup_ihandle));
}
/* }}} */

/* {{{ proto string IupSeparator()
   ;
*/
PHP_FUNCTION(IupSeparator)
{
    Ihandle *re;
    if (zend_parse_parameters_none() == FAILURE) {
        return;
    }

    re = IupSeparator();

    RETURN_RES(zend_register_resource(re, le_iup_ihandle));
}
/* }}} */

/* {{{ proto resource IupMenu(resource child)
   ;
 */
PHP_FUNCTION(IupMenu)
{

    zval *args;
    int argc;
    int i;

    Ihandle *child,*re;

    ZEND_PARSE_PARAMETERS_START(0, -1)
        Z_PARAM_VARIADIC('+', args, argc)
    ZEND_PARSE_PARAMETERS_END();

    if(argc < 1){
        re = IupMenu(NULL);
    }else{
        for (i = 0; i < argc; i++) {

            child = zend_fetch_resource_ex(&args[i],"iup-handle",le_iup_ihandle);

            if(i == 0){
                re = IupMenu(child,NULL);
            }else{
                IupAppend(re,child);
            }
        }        
    }

    RETURN_RES(zend_register_resource(re, le_iup_ihandle));
}
/* }}} */

/* {{{ proto resource IupMenuv(resource children)
   ;
 */
PHP_FUNCTION(IupMenuv)
{
    int argc = ZEND_NUM_ARGS();

    zval *ihandle_res = NULL;

    Ihandle *children,*re;

    if (zend_parse_parameters(argc TSRMLS_DC,"r",&ihandle_res) == FAILURE) {
        return;
    }

    children = zend_fetch_resource_ex(ihandle_res,"iup-handle",le_iup_ihandle);

    re = IupMenuv(&children);

    RETURN_RES(zend_register_resource(re, le_iup_ihandle));
}
/* }}} */

/* {{{ proto string IupButton(string title, string action)
   ;
 */
PHP_FUNCTION(IupButton)
{
    int argc = ZEND_NUM_ARGS();

    char *title = NULL;
    size_t title_len;

    char *action = NULL;
    size_t action_len;

    Ihandle *re;

    if (zend_parse_parameters(argc, "s!s!", &title, &title_len, &action, &action_len) == FAILURE) {
        return;
    }

    re = IupButton(title,action);

    RETURN_RES(zend_register_resource(re, le_iup_ihandle));
}
/* }}} */

/* {{{ proto string IupFlatButton(string title)
   ;
 */
PHP_FUNCTION(IupFlatButton)
{
    int argc = ZEND_NUM_ARGS();

    char *title = NULL;
    size_t title_len;

    Ihandle *re;

    if (zend_parse_parameters(argc, "s!", &title, &title_len) == FAILURE) {
        return;
    }

    re = IupFlatButton(title);

    RETURN_RES(zend_register_resource(re, le_iup_ihandle));
}
/* }}} */

/* {{{ proto string IupFlatToggle(string title)
   ;
 */
PHP_FUNCTION(IupFlatToggle)
{
    int argc = ZEND_NUM_ARGS();

    char *title = NULL;
    size_t title_len;

    Ihandle *re;

    if (zend_parse_parameters(argc, "s!", &title, &title_len) == FAILURE) {
        return;
    }

    re = IupFlatToggle(title);

    RETURN_RES(zend_register_resource(re, le_iup_ihandle));
}
/* }}} */

/* {{{ proto resource IupDropButton(resource dropchild)
   ;
 */
PHP_FUNCTION(IupDropButton)
{
    int argc = ZEND_NUM_ARGS();

    zval *ihandle_res = NULL;

    Ihandle *dropchild,*re;

    if (zend_parse_parameters(argc TSRMLS_DC,"r!",&ihandle_res) == FAILURE) {
        return;
    }

    if(ihandle_res == NULL){
        re = IupDropButton(NULL);
    }else{
        dropchild = zend_fetch_resource_ex(ihandle_res,"iup-handle",le_iup_ihandle);
        re = IupDropButton(dropchild);        
    }

    RETURN_RES(zend_register_resource(re, le_iup_ihandle));
}
/* }}} */

/* {{{ proto string IupFlatLabel(string title)
   ;
 */
PHP_FUNCTION(IupFlatLabel)
{
    int argc = ZEND_NUM_ARGS();

    char *title = NULL;
    size_t title_len;

    Ihandle *re;

    if (zend_parse_parameters(argc, "s!", &title, &title_len) == FAILURE) {
        return;
    }

    re = IupFlatLabel(title);

    RETURN_RES(zend_register_resource(re, le_iup_ihandle));
}
/* }}} */

/* {{{ proto string IupFlatSeparator()
   ;
*/
PHP_FUNCTION(IupFlatSeparator)
{
    Ihandle *re;
    if (zend_parse_parameters_none() == FAILURE) {
        return;
    }

    re = IupFlatSeparator();

    RETURN_RES(zend_register_resource(re, le_iup_ihandle));
}
/* }}} */

/* {{{ proto string IupCanvas(string action)
   ;
 */
PHP_FUNCTION(IupCanvas)
{
    int argc = ZEND_NUM_ARGS();

    char *action = NULL;
    size_t action_len;

    Ihandle *re;

    if (zend_parse_parameters(argc, "s!", &action, &action_len) == FAILURE) {
        return;
    }

    re = IupCanvas(action);

    RETURN_RES(zend_register_resource(re, le_iup_ihandle));
}
/* }}} */

/* {{{ proto resource IupDialog(resource child)
   ;
 */
PHP_FUNCTION(IupDialog)
{
    int argc = ZEND_NUM_ARGS();

    zval *ihandle_res = NULL;

    Ihandle *child,*re;

    if (zend_parse_parameters(argc TSRMLS_DC,"r!",&ihandle_res) == FAILURE) {
        return;
    }

    if(ihandle_res == NULL){
        re = IupDialog(NULL);
    }else{
        child = zend_fetch_resource_ex(ihandle_res,"iup-handle",le_iup_ihandle);

        re = IupDialog(child);
    }


    RETURN_RES(zend_register_resource(re, le_iup_ihandle));
}
/* }}} */

/* {{{ proto string IupUser()
   ;
*/
PHP_FUNCTION(IupUser)
{
    Ihandle *re;
    if (zend_parse_parameters_none() == FAILURE) {
        return;
    }

    re = IupUser();

    RETURN_RES(zend_register_resource(re, le_iup_ihandle));
}
/* }}} */

/* {{{ proto string IupThread()
   ;
*/
PHP_FUNCTION(IupThread)
{
    Ihandle *re;
    if (zend_parse_parameters_none() == FAILURE) {
        return;
    }

    re = IupThread();

    RETURN_RES(zend_register_resource(re, le_iup_ihandle));
}
/* }}} */


/* {{{ proto string IupLabel(string title)
   ;
 */
PHP_FUNCTION(IupLabel)
{
    int argc = ZEND_NUM_ARGS();

    char *title = NULL;
    size_t title_len;

    Ihandle *re;

    if (zend_parse_parameters(argc, "s!", &title, &title_len) == FAILURE) {
        return;
    }

    re = IupLabel(title);

    RETURN_RES(zend_register_resource(re, le_iup_ihandle));
}
/* }}} */

/* {{{ proto string IupList(string action)
   ;
 */
PHP_FUNCTION(IupList)
{
    int argc = ZEND_NUM_ARGS();

    char *action = NULL;
    size_t action_len;

    Ihandle *re;

    if (zend_parse_parameters(argc, "s!", &action, &action_len) == FAILURE) {
        return;
    }

    re = IupList(action);

    RETURN_RES(zend_register_resource(re, le_iup_ihandle));
}
/* }}} */

/* {{{ proto string IupFlatList()
   ;
*/
PHP_FUNCTION(IupFlatList)
{
    Ihandle *re;
    if (zend_parse_parameters_none() == FAILURE) {
        return;
    }

    re = IupFlatList();

    RETURN_RES(zend_register_resource(re, le_iup_ihandle));
}
/* }}} */

/* {{{ proto string IupText(string action)
   ;
 */
PHP_FUNCTION(IupText)
{
    int argc = ZEND_NUM_ARGS();

    char *action = NULL;
    size_t action_len;

    Ihandle *re;

    if (zend_parse_parameters(argc, "s!", &action, &action_len) == FAILURE) {
        return;
    }

    re = IupText(action);

    RETURN_RES(zend_register_resource(re, le_iup_ihandle));
}
/* }}} */

/* {{{ proto string IupMultiLine(string action)
   ;
 */
PHP_FUNCTION(IupMultiLine)
{
    int argc = ZEND_NUM_ARGS();

    char *action = NULL;
    size_t action_len;

    Ihandle *re;

    if (zend_parse_parameters(argc, "s", &action, &action_len) == FAILURE) {
        return;
    }

    re = IupMultiLine(action);

    RETURN_RES(zend_register_resource(re, le_iup_ihandle));
}
/* }}} */

/* {{{ proto string IupToggle(string title, string action)
   ;
 */
PHP_FUNCTION(IupToggle)
{
    int argc = ZEND_NUM_ARGS();

    char *title = NULL;
    size_t title_len;

    char *action = NULL;
    size_t action_len;

    Ihandle *re;

    if (zend_parse_parameters(argc, "s!s!", &title, &title_len, &action, &action_len) == FAILURE) {
        return;
    }

    re = IupToggle(title,action);

    RETURN_RES(zend_register_resource(re, le_iup_ihandle));
}
/* }}} */

/* {{{ proto string IupTimer()
   ;
*/
PHP_FUNCTION(IupTimer)
{
    Ihandle *re;
    if (zend_parse_parameters_none() == FAILURE) {
        return;
    }

    re = IupTimer();

    RETURN_RES(zend_register_resource(re, le_iup_ihandle));
}
/* }}} */

/* {{{ proto string IupClipboard()
   ;
*/
PHP_FUNCTION(IupClipboard)
{
    Ihandle *re;
    if (zend_parse_parameters_none() == FAILURE) {
        return;
    }

    re = IupClipboard();

    RETURN_RES(zend_register_resource(re, le_iup_ihandle));
}
/* }}} */

/* {{{ proto string IupProgressBar()
   ;
*/
PHP_FUNCTION(IupProgressBar)
{
    Ihandle *re;
    if (zend_parse_parameters_none() == FAILURE) {
        return;
    }

    re = IupProgressBar();

    RETURN_RES(zend_register_resource(re, le_iup_ihandle));
}
/* }}} */

/* {{{ proto string IupVal(string type)
   ;
 */
PHP_FUNCTION(IupVal)
{
    int argc = ZEND_NUM_ARGS();

    char *type = NULL;
    size_t type_len;

    Ihandle *re;

    if (zend_parse_parameters(argc, "s!", &type, &type_len) == FAILURE) {
        return;
    }

    re = IupVal(type);

    RETURN_RES(zend_register_resource(re, le_iup_ihandle));
}
/* }}} */

/* {{{ proto string IupFlatVal(string type)
   ;
 */
PHP_FUNCTION(IupFlatVal)
{
    int argc = ZEND_NUM_ARGS();

    char *type = NULL;
    size_t type_len;

    Ihandle *re;

    if (zend_parse_parameters(argc, "s!", &type, &type_len) == FAILURE) {
        return;
    }

    re = IupFlatVal(type);

    RETURN_RES(zend_register_resource(re, le_iup_ihandle));
}
/* }}} */


/* {{{ proto resource IupTabs(resource child)
   ;
 */
PHP_FUNCTION(IupTabs)
{

    zval *args;
    int argc;
    int i;

    Ihandle *child,*re;

    ZEND_PARSE_PARAMETERS_START(0, -1)
        Z_PARAM_VARIADIC('+', args, argc)
    ZEND_PARSE_PARAMETERS_END();

    if(argc < 1){
        re = IupTabs(NULL);
    }else{
        for (i = 0; i < argc; i++) {

            child = zend_fetch_resource_ex(&args[i],"iup-handle",le_iup_ihandle);

            if(i == 0){
                re = IupTabs(child,NULL);
            }else{
                IupAppend(re,child);
            }
        }        
    }

    RETURN_RES(zend_register_resource(re, le_iup_ihandle));
}
/* }}} */

/* {{{ proto resource IupTabsv(resource children)
   ;
 */
PHP_FUNCTION(IupTabsv)
{
    int argc = ZEND_NUM_ARGS();

    zval *ihandle_res = NULL;

    Ihandle *children,*re;

    if (zend_parse_parameters(argc TSRMLS_DC,"r!",&ihandle_res) == FAILURE) {
        return;
    }

    if(ihandle_res == NULL){
        re = IupTabsv(NULL);
    }else{
        children = zend_fetch_resource_ex(ihandle_res,"iup-handle",le_iup_ihandle);
        re = IupTabsv(&children);        
    }

    RETURN_RES(zend_register_resource(re, le_iup_ihandle));
}
/* }}} */

/* {{{ proto resource IupFlatTabs(resource child)
   ;
 */
PHP_FUNCTION(IupFlatTabs)
{

    zval *args;
    int argc;
    int i;

    Ihandle *child,*re;

    ZEND_PARSE_PARAMETERS_START(0, -1)
        Z_PARAM_VARIADIC('+', args, argc)
    ZEND_PARSE_PARAMETERS_END();

    if(argc < 1){
        re = IupFlatTabs(NULL);
    }else{
        for (i = 0; i < argc; i++) {

            child = zend_fetch_resource_ex(&args[i],"iup-handle",le_iup_ihandle);

            if(i == 0){
                re = IupFlatTabs(child,NULL);
            }else{
                IupAppend(re,child);
            }
        }        
    }

    RETURN_RES(zend_register_resource(re, le_iup_ihandle));
}
/* }}} */

/* {{{ proto resource IupFlatTabsv(resource children)
   ;
 */
PHP_FUNCTION(IupFlatTabsv)
{
    int argc = ZEND_NUM_ARGS();

    zval *ihandle_res = NULL;

    Ihandle *children,*re;

    if (zend_parse_parameters(argc TSRMLS_DC,"r!",&ihandle_res) == FAILURE) {
        return;
    }

    if(ihandle_res == NULL){
        re = IupFlatTabsv(NULL);
    }else{
        children = zend_fetch_resource_ex(ihandle_res,"iup-handle",le_iup_ihandle);
        re = IupFlatTabsv(&children);
    }



    RETURN_RES(zend_register_resource(re, le_iup_ihandle));
}
/* }}} */

/* {{{ proto string IupTree()
   ;
*/
PHP_FUNCTION(IupTree)
{
    Ihandle *re;
    if (zend_parse_parameters_none() == FAILURE) {
        return;
    }

    re = IupTree();

    RETURN_RES(zend_register_resource(re, le_iup_ihandle));
}
/* }}} */

/* {{{ proto string IupLink(string url, string title)
   ;
 */
PHP_FUNCTION(IupLink)
{
    int argc = ZEND_NUM_ARGS();

    char *url = NULL;
    size_t url_len;

    char *title = NULL;
    size_t title_len;

    Ihandle *re;

    if (zend_parse_parameters(argc, "s!s!", &url, &url_len, &title, &title_len) == FAILURE) {
        return;
    }

    re = IupLink(url,title);

    RETURN_RES(zend_register_resource(re, le_iup_ihandle));
}
/* }}} */

/* {{{ proto resource IupAnimatedLabel(resource animation)
   ;
 */
PHP_FUNCTION(IupAnimatedLabel)
{
    int argc = ZEND_NUM_ARGS();

    zval *ihandle_res = NULL;

    Ihandle *animation,*re;

    if (zend_parse_parameters(argc TSRMLS_DC,"r!",&ihandle_res) == FAILURE) {
        return;
    }

    if(ihandle_res == NULL){
        re = IupAnimatedLabel(NULL);
    }else{
        animation = zend_fetch_resource_ex(ihandle_res,"iup-handle",le_iup_ihandle);
        re = IupAnimatedLabel(animation);
    }

    RETURN_RES(zend_register_resource(re, le_iup_ihandle));
}
/* }}} */

/* {{{ proto string IupDatePick()
   ;
*/
PHP_FUNCTION(IupDatePick)
{
    Ihandle *re;
    if (zend_parse_parameters_none() == FAILURE) {
        return;
    }

    re = IupDatePick();

    RETURN_RES(zend_register_resource(re, le_iup_ihandle));
}
/* }}} */


/* {{{ proto string IupCalendar()
   ;
*/
PHP_FUNCTION(IupCalendar)
{
    Ihandle *re;
    if (zend_parse_parameters_none() == FAILURE) {
        return;
    }

    re = IupCalendar();

    RETURN_RES(zend_register_resource(re, le_iup_ihandle));
}
/* }}} */

/* {{{ proto string IupColorbar()
   ;
*/
PHP_FUNCTION(IupColorbar)
{
    Ihandle *re;
    if (zend_parse_parameters_none() == FAILURE) {
        return;
    }

    re = IupColorbar();

    RETURN_RES(zend_register_resource(re, le_iup_ihandle));
}
/* }}} */

/* {{{ proto string IupGauge()
   ;
*/
PHP_FUNCTION(IupGauge)
{
    Ihandle *re;
    if (zend_parse_parameters_none() == FAILURE) {
        return;
    }

    re = IupGauge();

    RETURN_RES(zend_register_resource(re, le_iup_ihandle));
}
/* }}} */

/* {{{ proto string IupDial(string type)
   ;
 */
PHP_FUNCTION(IupDial)
{
    int argc = ZEND_NUM_ARGS();

    char *type = NULL;
    size_t type_len;

    Ihandle *re;

    if (zend_parse_parameters(argc, "s!", &type, &type_len) == FAILURE) {
        return;
    }

    re = IupDial(type);

    RETURN_RES(zend_register_resource(re, le_iup_ihandle));
}
/* }}} */

/* {{{ proto string IupColorBrowser()
   ;
*/
PHP_FUNCTION(IupColorBrowser)
{
    Ihandle *re;
    if (zend_parse_parameters_none() == FAILURE) {
        return;
    }

    re = IupColorBrowser();

    RETURN_RES(zend_register_resource(re, le_iup_ihandle));
}
/* }}} */

/* {{{ proto string IupSpin()
   ;
*/
PHP_FUNCTION(IupSpin)
{
    Ihandle *re;
    if (zend_parse_parameters_none() == FAILURE) {
        return;
    }

    re = IupSpin();

    RETURN_RES(zend_register_resource(re, le_iup_ihandle));
}
/* }}} */

/* {{{ proto resource IupSpinbox(resource child)
   ;
 */
PHP_FUNCTION(IupSpinbox)
{
    int argc = ZEND_NUM_ARGS();

    zval *ihandle_res = NULL;

    Ihandle *child,*re;

    if (zend_parse_parameters(argc TSRMLS_DC,"r",&ihandle_res) == FAILURE) {
        return;
    }

    child = zend_fetch_resource_ex(ihandle_res,"iup-handle",le_iup_ihandle);

    re = IupSpinbox(child);

    RETURN_RES(zend_register_resource(re, le_iup_ihandle));
}
/* }}} */


/* {{{ proto string IupStringCompare(string str1, string str2, int casesensitive, int lexicographic)
   ;
 */
PHP_FUNCTION(IupStringCompare)
{
    int argc = ZEND_NUM_ARGS();

    char *str1 = NULL;
    size_t str1_len;

    char *str2 = NULL;
    size_t str2_len;

    zend_long casesensitive,lexicographic;

    int i;

    if (zend_parse_parameters(argc, "ssll", &str1, &str1_len, &str2, &str2_len, &casesensitive, &lexicographic) == FAILURE) {
        return;
    }

    i = IupStringCompare(str1,str2,casesensitive,lexicographic);

    RETURN_LONG(i);
}
/* }}} */

/* {{{ proto resource IupSaveImageAsText(resource ih, string file_name, string format, string name)
   ;
 */
PHP_FUNCTION(IupSaveImageAsText)
{
    int argc = ZEND_NUM_ARGS();

    zval *ihandle_res = NULL;

    Ihandle *ih;

    char *file_name = NULL;
    size_t file_name_len;

    char *format = NULL;
    size_t format_len;

    char *name = NULL;
    size_t name_len;

    int re;

    if (zend_parse_parameters(argc TSRMLS_DC,"rsss",&ihandle_res,&file_name, &file_name_len, &format, &format_len, &name, &name_len) == FAILURE) {
        return;
    }

    ih = zend_fetch_resource_ex(ihandle_res,"iup-handle",le_iup_ihandle);

    re = IupSaveImageAsText(ih,file_name,format,name);

    RETURN_LONG(re);
}
/* }}} */

/* {{{ proto string IupImageGetHandle(string name)
   ;
 */
PHP_FUNCTION(IupImageGetHandle)
{
    int argc = ZEND_NUM_ARGS();

    char *name = NULL;
    size_t name_len;

    Ihandle *re;

    if (zend_parse_parameters(argc, "s!", &name, &name_len) == FAILURE) {
        return;
    }

    re = IupImageGetHandle(name);

    RETURN_RES(zend_register_resource(re, le_iup_ihandle));
}
/* }}} */


/* {{{ proto resource IupTextConvertLinColToPos(resource ih, int lin, int col, ref pos)
   ;
 */
PHP_FUNCTION(IupTextConvertLinColToPos)
{
    int argc = ZEND_NUM_ARGS();

    zval *ihandle_res = NULL;

    Ihandle *ih;

    zend_long lin,col;

    zval *pos_val;

    int pos;

    if (zend_parse_parameters(argc TSRMLS_DC,"rllz",&ihandle_res,&lin,&col,&pos_val) == FAILURE) {
        return;
    }

    ih = zend_fetch_resource_ex(ihandle_res,"iup-handle",le_iup_ihandle);

    IupTextConvertLinColToPos(ih,lin,col,&pos);

    zval *real_pos_val = Z_REFVAL_P(pos_val);

    ZVAL_LONG(real_pos_val,pos);

    RETURN_NULL();

}
/* }}} */

/* {{{ proto resource IupTextConvertPosToLinCol(resource ih, int pos, ref lin, ref col)
   ;
 */
PHP_FUNCTION(IupTextConvertPosToLinCol)
{
    int argc = ZEND_NUM_ARGS();

    zval *ihandle_res = NULL;

    Ihandle *ih;

    zend_long pos;

    zval *lin_val, *col_val;

    int lin,col;

    if (zend_parse_parameters(argc TSRMLS_DC,"rlzz",&ihandle_res,&pos,&lin_val,&col_val) == FAILURE) {
        return;
    }

    ih = zend_fetch_resource_ex(ihandle_res,"iup-handle",le_iup_ihandle);

    IupTextConvertPosToLinCol(ih,pos,&lin,&col);

    zval *real_lin_val = Z_REFVAL_P(lin_val);
    ZVAL_LONG(real_lin_val,lin);

    zval *real_col_val = Z_REFVAL_P(col_val);
    ZVAL_LONG(real_col_val,col);
}
/* }}} */

/* {{{ proto string IupConvertXYToPos(resource ih, int x, int y)
   ;
 */
PHP_FUNCTION(IupConvertXYToPos)
{
    int argc = ZEND_NUM_ARGS();

    zval *ihandle_res = NULL;

    Ihandle *ih;

    zend_long x,y;

    int i;

    if (zend_parse_parameters(argc TSRMLS_DC,"rll",&ihandle_res,&x,&y) == FAILURE) {
        return;
    }

    ih = zend_fetch_resource_ex(ihandle_res,"iup-handle",le_iup_ihandle);

    i = IupConvertXYToPos(ih,x,y);

    RETURN_LONG(i);
}
/* }}} */

/* {{{ proto int IupStoreGlobal()
   ;
 */
PHP_FUNCTION(IupStoreGlobal)
{

    php_error(E_WARNING, "IupStoreGlobal: OLD names, kept for backward compatibility, will never be implemented.");

}
/* }}} */

/* {{{ proto int IupStoreAttribute()
   ;
 */
PHP_FUNCTION(IupStoreAttribute)
{

    php_error(E_WARNING, "IupStoreAttribute: OLD names, kept for backward compatibility, will never be implemented.");

}
/* }}} */

/* {{{ proto int IupSetfAttribute()
   ;
 */
PHP_FUNCTION(IupSetfAttribute)
{

    php_error(E_WARNING, "IupSetfAttribute: OLD names, kept for backward compatibility, will never be implemented.");

}
/* }}} */

/* {{{ proto int IupStoreAttributeId()
   ;
 */
PHP_FUNCTION(IupStoreAttributeId)
{

    php_error(E_WARNING, "IupStoreAttributeId: OLD names, kept for backward compatibility, will never be implemented.");

}
/* }}} */

/* {{{ proto int IupSetfAttributeId()
   ;
 */
PHP_FUNCTION(IupSetfAttributeId)
{

    php_error(E_WARNING, "IupSetfAttributeId: OLD names, kept for backward compatibility, will never be implemented.");

}
/* }}} */

/* {{{ proto int IupStoreAttributeId2()
   ;
 */
PHP_FUNCTION(IupStoreAttributeId2)
{

    php_error(E_WARNING, "IupStoreAttributeId2: OLD names, kept for backward compatibility, will never be implemented.");

}
/* }}} */

/* {{{ proto int IupSetfAttributeId2()
   ;
 */
PHP_FUNCTION(IupSetfAttributeId2)
{

    php_error(E_WARNING, "IupSetfAttributeId2: OLD names, kept for backward compatibility, will never be implemented.");

}
/* }}} */

/* {{{ proto string IupTreeSetUserId(resource ih, int id, int userid)
   ;
 */
PHP_FUNCTION(IupTreeSetUserId)
{
    int argc = ZEND_NUM_ARGS();

    zval *ihandle_res = NULL;

    Ihandle *ih;

    zend_long id,userid;

    int *uid;

    int i;

    if (zend_parse_parameters(argc TSRMLS_DC,"rll!",&ihandle_res,&id,&userid) == FAILURE) {
        return;
    }

    ih = zend_fetch_resource_ex(ihandle_res,"iup-handle",le_iup_ihandle);

    uid = (int *)malloc(sizeof(int));

    *uid = userid;

    i = IupTreeSetUserId(ih,id,uid);

    RETURN_LONG(i);
}
/* }}} */

/* {{{ proto string IupTreeGetUserId(resource ih, int id)
   ;
 */
PHP_FUNCTION(IupTreeGetUserId)
{
    int argc = ZEND_NUM_ARGS();

    zval *ihandle_res = NULL;

    Ihandle *ih;

    zend_long id;

    int i;

    if (zend_parse_parameters(argc TSRMLS_DC,"rl",&ihandle_res,&id) == FAILURE) {
        return;
    }

    ih = zend_fetch_resource_ex(ihandle_res,"iup-handle",le_iup_ihandle);

    i = IupTreeGetUserId(ih,id);

    RETURN_LONG(i);
}
/* }}} */

/* {{{ proto string IupTreeGetId(resource ih, int userid)
   ;
 */
PHP_FUNCTION(IupTreeGetId)
{
    int argc = ZEND_NUM_ARGS();

    zval *ihandle_res = NULL;

    Ihandle *ih;

    zend_long userid;

    int *uid;

    int i;

    if (zend_parse_parameters(argc TSRMLS_DC,"rl",&ihandle_res,&userid) == FAILURE) {
        return;
    }

    ih = zend_fetch_resource_ex(ihandle_res,"iup-handle",le_iup_ihandle);

    uid = (int *)malloc(sizeof(int));

    *uid = userid;

    i = IupTreeGetId(ih,uid);

    RETURN_LONG(i);
}
/* }}} */

/* {{{ proto int IupTreeSetAttributeHandle()
   ;
 */
PHP_FUNCTION(IupTreeSetAttributeHandle)
{

    php_error(E_WARNING, "IupTreeSetAttributeHandle: deprecated, use IupSetAttributeHandleId.");

}
/* }}} */
/* {{{ proto string IupFileDlg()
   ;
*/
PHP_FUNCTION(IupFileDlg)
{
    Ihandle *re;
    if (zend_parse_parameters_none() == FAILURE) {
        return;
    }

    re = IupFileDlg();

    RETURN_RES(zend_register_resource(re, le_iup_ihandle));
}
/* }}} */

/* {{{ proto string IupMessageDlg()
   ;
*/
PHP_FUNCTION(IupMessageDlg)
{
    Ihandle *re;
    if (zend_parse_parameters_none() == FAILURE) {
        return;
    }

    re = IupMessageDlg();

    RETURN_RES(zend_register_resource(re, le_iup_ihandle));
}
/* }}} */

/* {{{ proto string IupColorDlg()
   ;
*/
PHP_FUNCTION(IupColorDlg)
{
    Ihandle *re;
    if (zend_parse_parameters_none() == FAILURE) {
        return;
    }

    re = IupColorDlg();

    RETURN_RES(zend_register_resource(re, le_iup_ihandle));
}
/* }}} */

/* {{{ proto string IupFontDlg()
   ;
*/
PHP_FUNCTION(IupFontDlg)
{
    Ihandle *re;
    if (zend_parse_parameters_none() == FAILURE) {
        return;
    }

    re = IupFontDlg();

    RETURN_RES(zend_register_resource(re, le_iup_ihandle));
}
/* }}} */

/* {{{ proto string IupProgressDlg()
   ;
*/
PHP_FUNCTION(IupProgressDlg)
{
    Ihandle *re;
    if (zend_parse_parameters_none() == FAILURE) {
        return;
    }

    re = IupProgressDlg();

    RETURN_RES(zend_register_resource(re, le_iup_ihandle));
}
/* }}} */

/* {{{ proto int IupGetFile(string arq)
   ;
 */
PHP_FUNCTION(IupGetFile)
{
    int argc = ZEND_NUM_ARGS();

    char *arq = NULL;
    size_t arq_len;

    int i;

    if (zend_parse_parameters(argc, "s", &arq, &arq_len) == FAILURE) {
        return;
    }

    i = IupGetFile(arq);

    RETURN_LONG(i);
}
/* }}} */

/* {{{ proto string IupMessage(string title, string msg)
   ;
 */
PHP_FUNCTION(IupMessage)
{
    int argc = ZEND_NUM_ARGS();

    char *title = NULL;
    size_t title_len;

    char *msg = NULL;
    size_t msg_len;

    if (zend_parse_parameters(argc, "ss", &title, &title_len, &msg, &msg_len) == FAILURE) {
        return;
    }

    IupMessage(title,msg);

    RETURN_BOOL(1);
}
/* }}} */

/* {{{ proto string IupMessagef(string title, string format, string msg)
   ;
 */
PHP_FUNCTION(IupMessagef)
{

    php_error(E_WARNING, "IupMessagef: not implemented, use IupMessage instead");
    
    // int argc = ZEND_NUM_ARGS();

    // char *title = NULL;
    // size_t title_len;

    // char *format = NULL;
    // size_t format_len;

    // char *msg = NULL;
    // size_t msg_len;

    // if (zend_parse_parameters(argc, "sss", &title, &title_len, &format, &format_len, &msg, &msg_len) == FAILURE) {
    //     return;
    // }

    // IupMessagef(title,format,msg);

    // RETURN_BOOL(1);
}
/* }}} */

/* {{{ proto string IupMessageError(string parent, string message)
   ;
 */
PHP_FUNCTION(IupMessageError)
{
    int argc = ZEND_NUM_ARGS();

    zval *ihandle_res = NULL;

    Ihandle *parent;

    char *message = NULL;
    size_t message_len;

    if (zend_parse_parameters(argc TSRMLS_DC,"rs",&ihandle_res, &message, &message_len) == FAILURE) {
        return;
    }

    parent = zend_fetch_resource_ex(ihandle_res,"iup-handle",le_iup_ihandle);

    IupMessageError(parent,message);

    RETURN_BOOL(1);
}
/* }}} */

/* {{{ proto string IupMessageAlarm(string parent, string title, string message, string buttons)
   ;
 */
PHP_FUNCTION(IupMessageAlarm)
{
    int argc = ZEND_NUM_ARGS();

    zval *ihandle_res = NULL;

    Ihandle *parent;

    char *title = NULL;
    size_t title_len;

    char *message = NULL;
    size_t message_len;

    char *buttons = NULL;
    size_t buttons_len;

    int i;

    if (zend_parse_parameters(argc TSRMLS_DC,"rsss",&ihandle_res, &title, &title_len, &message, &message_len, &buttons, &buttons_len) == FAILURE) {
        return;
    }

    parent = zend_fetch_resource_ex(ihandle_res,"iup-handle",le_iup_ihandle);

    i = IupMessageAlarm(parent,title,message,buttons);

    RETURN_LONG(i);
}
/* }}} */

/* {{{ proto string IupAlarm(string title, string msg, string b1, string b2, string b3)
   ;
 */
PHP_FUNCTION(IupAlarm)
{
    int argc = ZEND_NUM_ARGS();

    char *title = NULL;
    size_t title_len;

    char *msg = NULL;
    size_t msg_len;

    char *b1 = NULL;
    size_t b1_len;

    char *b2 = NULL;
    size_t b2_len;

    char *b3 = NULL;
    size_t b3_len;

    int i;
    

    if (zend_parse_parameters(argc, "sssss", &title, &title_len, &msg, &msg_len, &b1, &b1_len, &b2, &b2_len, &b3, &b3_len) == FAILURE) {
        return;
    }

    i = IupAlarm(title,msg,b1,b2,b3);

    RETURN_LONG(i);
}
/* }}} */

/* {{{ proto int IupScanf()
   ;
 */
PHP_FUNCTION(IupScanf)
{

    php_error(E_WARNING, "IupScanf: not yet implemented");

}
/* }}} */

/* {{{ proto int IupListDialog(int type, const char *title, int size, const char** list, int op, int max_col, int max_lin, int* marks)
   ;
 */
PHP_FUNCTION(IupListDialog)
{

    int argc = ZEND_NUM_ARGS();

    zend_long type,size,op,max_col,max_lin;

    char *title = NULL;
    size_t title_len;

    HashTable *arr_list,*arr_marks;

    char **list;

    int *marks;

    // 用以遍历arr_list数组
    long num_key;
    zval *val,*marks_val;
    zend_string *key;

    // 执行结果
    int error,i;

    if (zend_parse_parameters(argc, "lslhlllz!", &type,&title, &title_len,&size,&arr_list,&op,&max_col,&max_lin,&marks_val) == FAILURE) {
        return;
    }

    // 先根据数组的数量，申请内存
    list = (char **)malloc(sizeof(char *) * size);

    i = 0;

    // 将php的字符串数组转换为c的字符串数组
    ZEND_HASH_FOREACH_KEY_VAL(arr_list, num_key, key, val) {

        if(Z_TYPE_P(val) == IS_STRING && i < size) {

            list[i] = (char *)malloc(sizeof(char) * Z_STRLEN_P(val));

            list[i] = Z_STRVAL_P(val);

            i ++;
        }
    } ZEND_HASH_FOREACH_END();

    // 初始化marks，默认全部不选中
    marks = (int *)malloc(sizeof(int) * size);

    for (i = 0; i < size; i ++ )
    {
        marks[i] = 0;
    }

    if(type == 2)
    {
        if(Z_TYPE_P(marks_val) == IS_ARRAY)
        {
            arr_marks = Z_ARRVAL_P(marks_val);
            // 遍历数组
            i = 0;
            // 将php的字符串数组转换为c的字符串数组
            ZEND_HASH_FOREACH_KEY_VAL(arr_marks, num_key, key, val) {
                if(Z_TYPE_P(val) == IS_LONG && i < size) {
                    if(Z_LVAL_P(val) > 0){
                        marks[i] = 1;
                    }else{
                        marks[i] = 0;
                    }
                    i ++;
                }
            } ZEND_HASH_FOREACH_END();
        }else{
            php_error(E_ERROR, "IupListDialog: when 'type' is 2, 'marks' must be array.");
            RETURN_BOOL(0);
        }
    }

    error = IupListDialog(type,title,size,list,op,max_col,max_lin,marks);

    // 判断返回结果，如果是类型2，并且用户有所选中
    if(type == 2 && error == 1 && marks_val != NULL){

        zval marks_re;

        // PHP 7.2 的特殊要求
        // 参考swoole的解决方案 c7109880427f9773b9925b046629e4e8344bdc34
        #ifdef HT_ALLOW_COW_VIOLATION
            HT_ALLOW_COW_VIOLATION(Z_ARRVAL_P(marks_val));
        #endif

        // 修改引用数组的值
        for (i = 0; i < size; i ++ )
        {
            ZVAL_LONG(&marks_re,marks[i]);
            zend_hash_index_update(Z_ARRVAL_P(marks_val),i,&marks_re);
        }
    }

    // 释放内存
    // for (i = 0; i < size ; i++)
    // {
    //     if(list[i] != NULL){
    //        free(list[i]);
    //     }
    // }

    free(list);

    free(marks);

    RETURN_LONG(error);

}
/* }}} */

/* {{{ proto string IupGetText(string title, string text, int maxsize)
   ;
 */
PHP_FUNCTION(IupGetText)
{
    int argc = ZEND_NUM_ARGS();

    char *title = NULL;
    size_t title_len;

    char *text = NULL;
    size_t text_len;

    zend_long maxsize;

    int i;

    if (zend_parse_parameters(argc, "ssl", &title, &title_len, &text, &text_len,&maxsize) == FAILURE) {
        return;
    }

    i = IupGetText(title,text,maxsize);

    RETURN_LONG(i);
}
/* }}} */

/* {{{ proto resource IupGetColor(int x, int y, ref r, ref g, ref g)
   ;
 */
PHP_FUNCTION(IupGetColor)
{
    int argc = ZEND_NUM_ARGS();

    zend_long x,y;

    unsigned char r, g, b;
    zval *rr,*gg,*bb;

    HashTable *arr = NULL;

    if (zend_parse_parameters(argc,"llzzz",&x,&y,&rr,&gg,&bb) == FAILURE) {
        return;
    }

    IupGetColor(x,y,&r,&g,&b);

    zval *real_rr_val = Z_REFVAL_P(rr);
    ZVAL_LONG(real_rr_val,(int)r);

    zval *real_gg_val = Z_REFVAL_P(gg);
    ZVAL_LONG(real_gg_val,(int)g);

    zval *real_bb_val = Z_REFVAL_P(bb);
    ZVAL_LONG(real_bb_val,(int)b);

    RETURN_NULL();
}
/* }}} */

/* {{{ proto int IupGetParam()
   ;
 */
PHP_FUNCTION(IupGetParam)
{

    php_error(E_WARNING, "IupGetParam: not yet implemented");

}
/* }}} */

/* {{{ proto int IupGetParamv()
   ;
 */
PHP_FUNCTION(IupGetParamv)
{

    php_error(E_WARNING, "IupGetParamv: not yet implemented");

}
/* }}} */

/* {{{ proto int IupParam()
   ;
 */
PHP_FUNCTION(IupParam)
{

    php_error(E_WARNING, "IupParam: not yet implemented");

}
/* }}} */

/* {{{ proto int IupParamBox()
   ;
 */
PHP_FUNCTION(IupParamBox)
{

    php_error(E_WARNING, "IupParamBox: not yet implemented");

}
/* }}} */

/* {{{ proto int IupParamBoxv()
   ;
 */
PHP_FUNCTION(IupParamBoxv)
{

    php_error(E_WARNING, "IupParamBoxv: not yet implemented");

}
/* }}} */

/* {{{ proto resource IupLayoutDialog(resource dialog)
   ;
 */
PHP_FUNCTION(IupLayoutDialog)
{
    int argc = ZEND_NUM_ARGS();

    zval *ihandle_res = NULL;

    Ihandle *dialog,*re;

    if (zend_parse_parameters(argc TSRMLS_DC,"r!",&ihandle_res) == FAILURE) {
        return;
    }

    if(ihandle_res == NULL){
        re = IupLayoutDialog(NULL);
    }else{
        dialog = zend_fetch_resource_ex(ihandle_res,"iup-handle",le_iup_ihandle);
        re = IupLayoutDialog(dialog);
    }

    RETURN_RES(zend_register_resource(re, le_iup_ihandle));
}
/* }}} */

/* {{{ proto resource IupElementPropertiesDialog(resource parent, resource elem)
   ;
 */
PHP_FUNCTION(IupElementPropertiesDialog)
{
    int argc = ZEND_NUM_ARGS();

    zval *ihandle_res1 = NULL;
    zval *ihandle_res2 = NULL;

    Ihandle *parent,*elem,*re;

    if (zend_parse_parameters(argc TSRMLS_DC,"rr",&ihandle_res1,&ihandle_res2) == FAILURE) {
        return;
    }

    if(ihandle_res1 == NULL){
        parent = NULL;
    }else{
        parent = zend_fetch_resource_ex(ihandle_res1,"iup-handle",le_iup_ihandle);
    }

    elem = zend_fetch_resource_ex(ihandle_res2,"iup-handle",le_iup_ihandle);

    re = IupElementPropertiesDialog(parent,elem);

    RETURN_RES(zend_register_resource(re, le_iup_ihandle));
}
/* }}} */

/* {{{ proto string IupGlobalsDialog()
   ;
*/
PHP_FUNCTION(IupGlobalsDialog)
{
    Ihandle *re;
    if (zend_parse_parameters_none() == FAILURE) {
        return;
    }

    re = IupGlobalsDialog();

    RETURN_RES(zend_register_resource(re, le_iup_ihandle));
}
/* }}} */

/* {{{ proto resource IupClassInfoDialog(resource dialog)
   ;
 */
PHP_FUNCTION(IupClassInfoDialog)
{
    int argc = ZEND_NUM_ARGS();

    zval *ihandle_res = NULL;

    Ihandle *dialog,*re;

    if (zend_parse_parameters(argc TSRMLS_DC,"r!",&ihandle_res) == FAILURE) {
        return;
    }

    if(ihandle_res == NULL){
        re = IupClassInfoDialog(NULL);
    }else{
        dialog = zend_fetch_resource_ex(ihandle_res,"iup-handle",le_iup_ihandle);
        re = IupClassInfoDialog(dialog);
    }

    RETURN_RES(zend_register_resource(re, le_iup_ihandle));
}
/* }}} */
