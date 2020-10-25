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
extern HashTable *iup_callback;

void event_del_callback(zend_string * event_key)
{

    zval * event_val_old;
    zend_fcall_info * callable_old;

    event_val_old = zend_hash_find(iup_events,event_key);

    if(event_val_old != NULL){
        callable_old = zend_fetch_resource_ex(event_val_old,"iup-event",le_iup_event);
        if(callable_old != NULL){
            free(callable_old);
        }
    }

}

int event_set_callback(Ihandle *ih , char * event_name)
{
    Icallback cb;

    char full_name[80];

    char *class_name = IupGetClassName(ih);

    sprintf(full_name,"%s_%s",class_name,event_name);

    cb = zend_hash_str_find_ptr(iup_callback,full_name,strlen(full_name));

    if(cb == NULL){
        sprintf(full_name,"%s: no callback function registered.",full_name);
        php_error(E_WARNING, full_name);
        return 0;
    }

    IupSetCallback(ih, event_name, cb);

    return 1;
}

void event_get_callinfo(char * event_name, Ihandle *ih , zend_fcall_info **callable)
{
    intptr_t ih_p_int;

    char event_key_str[120];

    zend_string * event_key;

    zval * event_val;

    ih_p_int = (intptr_t)ih;

    sprintf(event_key_str,"EVENT_%s_%"SCNiPTR,event_name,ih_p_int);

    event_key = zend_string_init(event_key_str, strlen(event_key_str), 0);

    // 判断事件数组中是否存有相同事件id
    event_val = zend_hash_find(iup_events,event_key);

    if(event_val == NULL){
        // 没有相应的事件，直接返回
        *callable = NULL;
    }

    *callable = zend_fetch_resource_ex(event_val,"iup-event",le_iup_event);
}

int event_call_function(zend_fcall_info *callable)
{

    int call_result;

    zval func_result;

    zend_long func_result_value;

    callable->retval = &func_result;

    call_result = zend_call_function(callable, NULL);

    func_result_value = Z_LVAL(func_result);

    if(
        func_result_value == IUP_IGNORE || 
        func_result_value == IUP_DEFAULT || 
        func_result_value == IUP_CLOSE || 
        func_result_value == IUP_CONTINUE
        )
    {

        return (int)func_result_value;
    }

    return IUP_DEFAULT;
}

int event_base(char * event_name, Ihandle *ih , int param_count, zval *call_params)
{
    char warning[80];

    zend_fcall_info *callable;

    event_get_callinfo(event_name,ih,&callable);

    if(callable == NULL){
        sprintf(warning,"%s: no user function set.",event_name);
        php_error(E_WARNING, warning);
        return IUP_DEFAULT;
    }

    ZVAL_RES(&call_params[0],zend_register_resource(ih, le_iup_ihandle));

    callable->param_count = param_count;

    callable->params = call_params;

    return event_call_function(callable);
}


char* event_call_function_str(zend_fcall_info *callable)
{

    int call_result;

    zval func_result;

    char *func_result_value;

    callable->retval = &func_result;

    call_result = zend_call_function(callable, NULL);

    func_result_value = Z_STRVAL(func_result);

    if(func_result_value != NULL){
        return func_result_value;
    }

    return NULL;
}

char* event_base_str(char * event_name, Ihandle *ih , int param_count, zval *call_params)
{
    char warning[80];

    zend_fcall_info *callable;

    event_get_callinfo(event_name,ih,&callable);

    if(callable == NULL){
        sprintf(warning,"%s: no user function set.",event_name);
        php_error(E_WARNING, warning);
        // return IUP_DEFAULT;
        return "";
    }

    ZVAL_RES(&call_params[0],zend_register_resource(ih, le_iup_ihandle));

    callable->param_count = param_count;

    callable->params = call_params;

    return event_call_function_str(callable);
}

// =============================================================================

int event_common(char * event_name, Ihandle *ih)
{
    zval call_params[1];
    return event_base(event_name,ih,1,&call_params);
}

int event_common_i(char * event_name, Ihandle *ih , int i)
{
    zval call_params[2];

    ZVAL_LONG(&call_params[1],i);

    return event_base(event_name,ih,2,&call_params);
}

int event_common_d(char * event_name, Ihandle *ih , double d)
{
    zval call_params[2];

    ZVAL_DOUBLE(&call_params[1],d);

    return event_base(event_name,ih,2,&call_params);
}

int event_common_s(char * event_name, Ihandle *ih , char *str)
{
    zval call_params[2];

    zend_string * zstring;

    zstring = zend_string_init(str, strlen(str), 0);

    ZVAL_STR(&call_params[1],zstring);

    return event_base(event_name,ih,2,&call_params);
}

int event_common_n(char * event_name, Ihandle *ih , Ihandle *ih2)
{
    zval call_params[2];

    ZVAL_RES(&call_params[1],zend_register_resource(ih2, le_iup_ihandle));

    return event_base(event_name,ih,2,&call_params);
}

int event_common_C(char * event_name, Ihandle *ih ,  cdCanvas* cnv)
{
    zval call_params[2];

    ZVAL_RES(&call_params[1],zend_register_resource(cnv, le_iup_ihandle));

    return event_base(event_name,ih,2,&call_params);
}

int event_common_ii(char * event_name, Ihandle *ih , int i1, int i2)
{
    zval call_params[3];

    ZVAL_LONG(&call_params[1],i1);
    ZVAL_LONG(&call_params[2],i2);

    return event_base(event_name,ih,3,&call_params);
}

char* event_common_ii_s(char * event_name, Ihandle *ih , int i1, int i2)
{
    zval call_params[3];

    ZVAL_LONG(&call_params[1],i1);
    ZVAL_LONG(&call_params[2],i2);

    return event_base_str(event_name,ih,3,&call_params);
}

char* event_common_iis_s(char * event_name, Ihandle *ih , int i1, int i2, char *str)
{
    zval call_params[4];

    zend_string * zstring;

    zstring = zend_string_init(str, strlen(str), 0);

    
    ZVAL_LONG(&call_params[1],i1);
    ZVAL_LONG(&call_params[2],i2);
    ZVAL_STR(&call_params[3],zstring);

    return event_base_str(event_name,ih,4,&call_params);
}

int event_common_Ii(char * event_name, Ihandle *ih , int *pi1, int i1)
{
    zval call_params[3];

    zval zlong1;

    HashTable *arr1;

    ALLOC_HASHTABLE(arr1);
    zend_hash_init(arr1,i1,NULL,NULL,0);

    for (int i = 0; i < i1; ++i)
    {

        ZVAL_LONG(&zlong1,pi1[i]);

        zend_hash_index_add(arr1,i,&zlong1);
    }

    ZVAL_ARR(&call_params[1],arr1);
    ZVAL_LONG(&call_params[2],i1);

    return event_base(event_name,ih,3,&call_params);
}


int event_common_is(char * event_name, Ihandle *ih , int i, char *str)
{
    zval call_params[3];

    zend_string * zstring;
    zstring = zend_string_init(str, strlen(str), 0);

    ZVAL_LONG(&call_params[1],i);
    ZVAL_STR(&call_params[2],zstring);

    return event_base(event_name,ih,3,&call_params);
}

int event_common_si(char * event_name, Ihandle *ih , char *str, int i)
{
    zval call_params[3];

    zend_string * zstring;
    zstring = zend_string_init(str, strlen(str), 0);

    ZVAL_STR(&call_params[1],zstring);
    ZVAL_LONG(&call_params[2],i);

    return event_base(event_name,ih,3,&call_params);
}

int event_common_nn(char * event_name, Ihandle *ih , Ihandle *ih2, Ihandle *ih3)
{
    zval call_params[3];

    ZVAL_RES(&call_params[1],zend_register_resource(ih2, le_iup_ihandle));
    ZVAL_RES(&call_params[2],zend_register_resource(ih3, le_iup_ihandle));

    return event_base(event_name,ih,3,&call_params);
}


int event_common_iii(char * event_name, Ihandle *ih , int i1, int i2, int i3)
{
    zval call_params[4];

    ZVAL_LONG(&call_params[1],i1);
    ZVAL_LONG(&call_params[2],i2);
    ZVAL_LONG(&call_params[3],i3);

    return event_base(event_name,ih,4,&call_params);
}

int event_common_ccc(char * event_name, Ihandle *ih , unsigned char r, unsigned char g, unsigned char b)
{
    zval call_params[4];

    ZVAL_LONG(&call_params[1],(int)r);
    ZVAL_LONG(&call_params[2],(int)g);
    ZVAL_LONG(&call_params[3],(int)b);

    return event_base(event_name,ih,4,&call_params);
}

int event_common_iid(char * event_name, Ihandle *ih , int i1, int i2, double d1)
{
    zval call_params[4];

    ZVAL_LONG(&call_params[1],i1);
    ZVAL_LONG(&call_params[2],i2);
    ZVAL_DOUBLE(&call_params[3],d1);

    return event_base(event_name,ih,4,&call_params);
}

int event_common_iis(char * event_name, Ihandle *ih , int i1, int i2, char * str)
{
    zval call_params[4];

    zend_string * zstring;
    zstring = zend_string_init(str, strlen(str), 0);

    ZVAL_LONG(&call_params[1],i1);
    ZVAL_LONG(&call_params[2],i2);

    ZVAL_STR(&call_params[3],zstring);

    return event_base(event_name,ih,4,&call_params);
}

int event_common_dds(char * event_name, Ihandle *ih , double d1, double d2, char * str)
{
    zval call_params[4];

    zend_string * zstring;
    zstring = zend_string_init(str, strlen(str), 0);

    ZVAL_DOUBLE(&call_params[1],d1);
    ZVAL_DOUBLE(&call_params[2],d2);

    ZVAL_STR(&call_params[3],zstring);

    return event_base(event_name,ih,4,&call_params);
}

int event_common_iff(char * event_name, Ihandle *ih , int i1, float f1, float f2)
{
    zval call_params[4];

    ZVAL_LONG(&call_params[1],i1);

    ZVAL_DOUBLE(&call_params[2],f1);
    ZVAL_DOUBLE(&call_params[3],f2);

    return event_base(event_name,ih,4,&call_params);
}

int event_common_nii(char * event_name, Ihandle *ih , Ihandle *ih2, int i1, int i2)
{
    zval call_params[4];

    ZVAL_RES(&call_params[1],zend_register_resource(ih2, le_iup_ihandle));
    ZVAL_LONG(&call_params[2],i1);
    ZVAL_LONG(&call_params[3],i2);

    return event_base(event_name,ih,4,&call_params);
}

int event_common_sCi(char * event_name, Ihandle *ih , char *str, void* data, int i)
{
    zval call_params[4];

    char *data_str;

    data_str = (char *)malloc(i);

    data_str = (char *)data;

    zend_string *zstring1,*zstring2;
    zstring1 = zend_string_init(str, strlen(str), 0);
    zstring2 = zend_string_init(data_str, strlen(data_str), 0);

    ZVAL_STR(&call_params[1],zstring1);
    ZVAL_STR(&call_params[2],zstring2);
    ZVAL_LONG(&call_params[3],i);

    return event_base(event_name,ih,4,&call_params);
}

int event_common_iidd(char * event_name, Ihandle *ih , int i1, int i2, double d1, double d2)
{
    zval call_params[5];

    ZVAL_LONG(&call_params[1],i1);
    ZVAL_LONG(&call_params[2],i2);
    ZVAL_DOUBLE(&call_params[3],d1);
    ZVAL_DOUBLE(&call_params[4],d2);

    return event_base(event_name,ih,5,&call_params);
}

int event_common_siii(char * event_name, Ihandle *ih , char *str, int i1, int i2, int i3)
{
    zval call_params[5];

    zend_string * zstring;
    zstring = zend_string_init(str, strlen(str), 0);

    ZVAL_STR(&call_params[1],zstring);

    ZVAL_LONG(&call_params[2],i1);
    ZVAL_LONG(&call_params[3],i2);
    ZVAL_LONG(&call_params[4],i3);

    return event_base(event_name,ih,5,&call_params);
}

int event_common_fiis(char * event_name, Ihandle *ih , float f1, int i1, int i2, char *str)
{
    zval call_params[5];

    zend_string * zstring;
    zstring = zend_string_init(str, strlen(str), 0);

    ZVAL_DOUBLE(&call_params[1],f1);

    ZVAL_LONG(&call_params[2],i1);
    ZVAL_LONG(&call_params[3],i2);

    ZVAL_STR(&call_params[4],zstring);

    return event_base(event_name,ih,5,&call_params);
}

int event_common_iiii(char * event_name, Ihandle *ih , int i1, int i2, int i3, int i4)
{
    zval call_params[5];

    ZVAL_LONG(&call_params[1],i1);
    ZVAL_LONG(&call_params[2],i2);
    ZVAL_LONG(&call_params[3],i3);
    ZVAL_LONG(&call_params[4],i4);

    return event_base(event_name,ih,5,&call_params);
}

int event_common_iiis(char * event_name, Ihandle *ih , int i1, int i2, int i3, char * str)
{
    zval call_params[5];

    zend_string * zstring;
    zstring = zend_string_init(str, strlen(str), 0);

    ZVAL_LONG(&call_params[1],i1);
    ZVAL_LONG(&call_params[2],i2);
    ZVAL_LONG(&call_params[3],i3);

    ZVAL_STR(&call_params[4],zstring);

    return event_base(event_name,ih,5,&call_params);
}

int event_common_sids(char * event_name, Ihandle *ih , char *str, int i, double d , void* p)
{
    zval call_params[4];

    char *p_str;

    p_str = (char *)malloc(i);

    p_str = (char *)p;

    zend_string *zstring1,*zstring2;
    zstring1 = zend_string_init(str, strlen(str), 0);
    zstring2 = zend_string_init(p_str, strlen(p_str), 0);

    ZVAL_STR(&call_params[1],zstring1);
    ZVAL_LONG(&call_params[2],i);
    ZVAL_DOUBLE(&call_params[3],d);
    ZVAL_STR(&call_params[4],zstring2);

    return event_base(event_name,ih,5,&call_params);
}

int event_common_iiIII(char * event_name, Ihandle *ih , int i1, int i2,int *r, int *g, int *b)
{
    int re;
    zval call_params[6];

    ZVAL_LONG(&call_params[1],i1);
    ZVAL_LONG(&call_params[2],i2);
    ZVAL_LONG(&call_params[3],0);
    ZVAL_LONG(&call_params[4],0);
    ZVAL_LONG(&call_params[5],0);

    re = event_base(event_name,ih,6,&call_params);

    if(re != IUP_IGNORE){
        *r = Z_LVAL(call_params[3]);
        *g = Z_LVAL(call_params[4]);
        *b = Z_LVAL(call_params[5]);
    }

    return re;
}

int event_common_iIIII(char * event_name, Ihandle *ih , int i1, int *pi1, int *pi2, int *pi3, int *pi4)
{
    zval call_params[6];

    zval zlong1,zlong2,zlong3,zlong4;

    HashTable *arr1, *arr2, *arr3, *arr4;

    ALLOC_HASHTABLE(arr1);
    zend_hash_init(arr1,i1,NULL,NULL,0);

    ALLOC_HASHTABLE(arr2);
    zend_hash_init(arr2,i1,NULL,NULL,0);

    ALLOC_HASHTABLE(arr3);
    zend_hash_init(arr3,i1,NULL,NULL,0);

    ALLOC_HASHTABLE(arr4);
    zend_hash_init(arr4,i1,NULL,NULL,0);

    for (int i = 0; i < i1; ++i)
    {

        ZVAL_LONG(&zlong1,pi1[i]);
        zend_hash_index_add(arr1,i,&zlong1);

        ZVAL_LONG(&zlong2,pi2[i]);
        zend_hash_index_add(arr2,i,&zlong2);

        ZVAL_LONG(&zlong3,pi3[i]);
        zend_hash_index_add(arr3,i,&zlong3);

        ZVAL_LONG(&zlong4,pi4[i]);
        zend_hash_index_add(arr4,i,&zlong4);
    }

    ZVAL_LONG(&call_params[1],i1);

    ZVAL_ARR(&call_params[2],arr1);
    ZVAL_ARR(&call_params[3],arr2);
    ZVAL_ARR(&call_params[4],arr3);
    ZVAL_ARR(&call_params[5],arr4);

    return event_base(event_name,ih,6,&call_params);
}

int event_common_iiiis(char * event_name, Ihandle *ih , int i1, int i2, int i3, int i4, char * str)
{
    zval call_params[6];

    zend_string * zstring;
    zstring = zend_string_init(str, strlen(str), 0);

    ZVAL_LONG(&call_params[1],i1);
    ZVAL_LONG(&call_params[2],i2);
    ZVAL_LONG(&call_params[3],i3);
    ZVAL_LONG(&call_params[4],i4);

    ZVAL_STR(&call_params[5],zstring);

    return event_base(event_name,ih,6,&call_params);
}

int event_common_iiddi(char * event_name, Ihandle *ih , int i1, int i2, double d1, double d2, int i3)
{
    zval call_params[6];

    ZVAL_LONG(&call_params[1],i1);
    ZVAL_LONG(&call_params[2],i2);
    ZVAL_DOUBLE(&call_params[3],d1);
    ZVAL_DOUBLE(&call_params[4],d2);
    ZVAL_LONG(&call_params[5],i3);

    return event_base(event_name,ih,6,&call_params);
}

int event_common_iidds(char * event_name, Ihandle *ih , int i1, int i2, double d1, double d2, char *str)
{
    zval call_params[6];

    zend_string * zstring;
    zstring = zend_string_init(str, strlen(str), 0);

    ZVAL_LONG(&call_params[1],i1);
    ZVAL_LONG(&call_params[2],i2);
    ZVAL_DOUBLE(&call_params[3],d1);
    ZVAL_DOUBLE(&call_params[4],d2);

    ZVAL_STR(&call_params[5],zstring);

    return event_base(event_name,ih,6,&call_params);
}

int event_common_iiiiiis(char * event_name, Ihandle *ih , int i1, int i2, int i3, int i4, int i5, int i6, char * str)
{
    zval call_params[8];

    zend_string * zstring;
    zstring = zend_string_init(str, strlen(str), 0);

    ZVAL_LONG(&call_params[1],i1);
    ZVAL_LONG(&call_params[2],i2);
    ZVAL_LONG(&call_params[3],i3);
    ZVAL_LONG(&call_params[4],i4);
    ZVAL_LONG(&call_params[5],i5);
    ZVAL_LONG(&call_params[6],i6);

    ZVAL_STR(&call_params[7],zstring);

    return event_base(event_name,ih,8,&call_params);
}

int event_common_iiiiiiC(char * event_name, Ihandle *ih , int i1, int i2, int i3, int i4, int i5, int i6, cdCanvas* canvas)
{
    zval call_params[8];

    zend_resource * zresource;
    zresource = zend_register_resource(canvas, le_iup_ihandle);

    ZVAL_LONG(&call_params[1],i1);
    ZVAL_LONG(&call_params[2],i2);
    ZVAL_LONG(&call_params[3],i3);
    ZVAL_LONG(&call_params[4],i4);
    ZVAL_LONG(&call_params[5],i5);
    ZVAL_LONG(&call_params[6],i6);

    ZVAL_RES(&call_params[7],zresource);

    return event_base(event_name,ih,8,&call_params);
}


int event_common_iinsii(char * event_name, Ihandle *ih, int i1, int i2 , Ihandle *ih2, char* str, int i3, int i4)
{
    zval call_params[7];

    zend_string * zstring;
    zstring = zend_string_init(str, strlen(str), 0);

    ZVAL_LONG(&call_params[1],i1);
    ZVAL_LONG(&call_params[2],i2);
    ZVAL_RES(&call_params[3],zend_register_resource(ih2, le_iup_ihandle));
    ZVAL_STR(&call_params[4],zstring);
    ZVAL_LONG(&call_params[5],i1);
    ZVAL_LONG(&call_params[6],i2);

    return event_base(event_name,ih,7,&call_params);
}

int event_common_iiddiddi(char * event_name, Ihandle *ih , int i1, int i2, double d1, double d2, int i3, double d3, double d4, int i4)
{
    zval call_params[9];

    ZVAL_LONG(&call_params[1],i1);
    ZVAL_LONG(&call_params[2],i2);
    ZVAL_DOUBLE(&call_params[3],d1);
    ZVAL_DOUBLE(&call_params[4],d2);
    ZVAL_LONG(&call_params[5],i3);
    ZVAL_DOUBLE(&call_params[6],d3);
    ZVAL_DOUBLE(&call_params[7],d4);
    ZVAL_LONG(&call_params[8],i4);

    return event_base(event_name,ih,9,&call_params);
}

// =============================================================================

int event_common_action( Ihandle *ih ){
    return event_common("ACTION",ih);
}

// common events
int event_common_destroy_cb( Ihandle *ih ){
    return event_common("DESTROY_CB",ih);
}

int event_common_ldestroy_cb( Ihandle *ih ){
    return event_common("LDESTROY_CB",ih);
}

int event_common_map_cb( Ihandle *ih ){
    return event_common("MAP_CB",ih);
}

int event_common_unmap_cb( Ihandle *ih ){
    return event_common("UNMAP_CB",ih);
}

int event_common_getfocus_cb( Ihandle *ih ){
    return event_common("GETFOCUS_CB",ih);
}

int event_common_killfocus_cb( Ihandle *ih ){
    return event_common("KILLFOCUS_CB",ih);
}

int event_common_enterwindow_cb( Ihandle *ih ){
    return event_common("ENTERWINDOW_CB",ih);
}

int event_common_leavewindow_cb( Ihandle *ih ){
    return event_common("LEAVEWINDOW_CB",ih);
}

int event_common_help_cb( Ihandle *ih ){
    return event_common("HELP_CB",ih);
}

int event_common_k_any( Ihandle *ih ,int i){
    return event_common_i("K_ANY",ih,i);
}

int event_common_postmessage_cb( Ihandle* ih, const char* s, int i, double d, void* p ){
    return event_common_sids("POSTMESSAGE_CB",ih,s,i,d,p);
}

// =============================================================================

int event_flat_action( Ihandle *ih ){
    return event_common("FLAT_ACTION",ih);
}

// common events
int event_flat_destroy_cb( Ihandle *ih ){
    return event_common("FLAT_DESTROY_CB",ih);
}

int event_flat_ldestroy_cb( Ihandle *ih ){
    return event_common("FLAT_LDESTROY_CB",ih);
}

int event_flat_map_cb( Ihandle *ih ){
    return event_common("FLAT_MAP_CB",ih);
}

int event_flat_unmap_cb( Ihandle *ih ){
    return event_common("FLAT_UNMAP_CB",ih);
}

int event_flat_getfocus_cb( Ihandle *ih ){
    return event_common("FLAT_GETFOCUS_CB",ih);
}

int event_flat_killfocus_cb( Ihandle *ih ){
    return event_common("FLAT_KILLFOCUS_CB",ih);
}

int event_flat_enterwindow_cb( Ihandle *ih ){
    return event_common("FLAT_ENTERWINDOW_CB",ih);
}

int event_flat_leavewindow_cb( Ihandle *ih ){
    return event_common("FLAT_LEAVEWINDOW_CB",ih);
}

int event_flat_help_cb( Ihandle *ih ){
    return event_common("FLAT_HELP_CB",ih);
}

int event_flat_k_any( Ihandle *ih ,int i){
    return event_common_i("FLAT_K_ANY",ih,i);
}

int event_flat_button_cb( Ihandle *ih, int button, int pressed, int x, int y, char* status){
    return event_common_iiiis("FLAT_BUTTON_CB",ih,button,pressed,x,y,status);
}

int event_flat_postmessage_cb( Ihandle* ih, const char* s, int i, double d, void* p ){
    return event_common_sids("FLAT_POSTMESSAGE_CB",ih,s,i,d,p);
}

// =============================================================================

int event_dialog_close_cb( Ihandle *ih){
    return event_common("CLOSE_CB",ih);
}

int event_dialog_copydata_cb( Ihandle *ih, char* cmdLine, int size){
    return event_common_si("COPYDATA_CB",ih,cmdLine,size);
}

int event_dialog_customframe_cb( Ihandle *ih){
    return event_common("CUSTOMFRAME_CB",ih);
}

int event_dialog_customframeactivate_cb( Ihandle *ih, int active){
    return event_common_i("CUSTOMFRAMEACTIVATE_CB",ih,active);
}

int event_dialog_mdiactivate_cb( Ihandle *ih){
    return event_common("MDIACTIVATE_CB",ih);
}

int event_dialog_show_cb( Ihandle *ih, int state){
    return event_common_i("SHOW_CB",ih,state);
}

// 各个控件的事件

int event_elements_action_cb( Ihandle *ih ){
    return event_common("ACTION_CB",ih);
}

int event_elements_valuechanged_cb( Ihandle *ih ){
    return event_common("VALUECHANGED_CB",ih);
}

int event_elements_valuechanging_cb( Ihandle *ih, int start ){
    return event_common_i("VALUECHANGING_CB",ih,start);
}

int event_elements_layoutupdate_cb( Ihandle *ih ){
    return event_common("LAYOUTUPDATE_CB",ih);
}

int event_elements_layoutchanged_cb( Ihandle *ih, char *name){
    return event_common_s("ATTRIBCHANGED_CB",ih,name);
}

int event_elements_attribchanged_cb( Ihandle *ih, Ihandle* elem){
    return event_common_n("LAYOUTCHANGED_CB",ih,elem);
}

int event_elements_openclose_cb( Ihandle *ih, int state){
    return event_common_i("OPENCLOSE_CB",ih,state);
}

int event_elements_extrabutton_cb( Ihandle *ih, int button, int pressed){
    return event_common_ii("EXTRABUTTON_CB",ih,button,pressed);
}

int event_elements_detached_cb( Ihandle *ih, Ihandle *new_parent, int x, int y){
    return event_common_nii("DETACHED_CB",ih,new_parent,x,y);
}

int event_elements_restored_cb( Ihandle *ih, Ihandle *old_parent, int x, int y){
    return event_common_nii("RESTORED_CB",ih,old_parent,x,y);
}

int event_elements_focus_cb( Ihandle *ih, int focus){
    return event_common_i("FOCUS_CB",ih,focus);
}

int event_flat_focus_cb( Ihandle *ih, int focus){
    return event_common_i("FLAT_FOCUS_CB",ih,focus);
}

int event_elements_highlight_cb( Ihandle *ih ){
    return event_common("HIGHLIGHT_CB",ih);
}

int event_elements_open_cb( Ihandle *ih ){
    return event_common("OPEN_CB",ih);
}

int event_elements_menuclose_cb( Ihandle *ih ){
    return event_common("MENUCLOSE_CB",ih);
}

int event_elements_button_cb( Ihandle *ih, int button, int pressed, int x, int y, char* status){
    return event_common_iiiis("BUTTON_CB",ih,button,pressed,x,y,status);
}

int event_elements_dropdown_cb( Ihandle *ih, int state){
    return event_common_i("DROPDOWN_CB",ih,state);
}

int event_elements_dropshow_cb( Ihandle *ih, int state){
    return event_common_i("DROPSHOW_CB",ih,state);
}

int event_elements_motion_cb( Ihandle *ih, int x, int y, char *status){
    return event_common_iis("MOTION_CB",ih,x,y,status);
}

int event_elements_keypress_cb( Ihandle *ih, int c, int press){
    return event_common_ii("KEYPRESS_CB",ih,c,press);
}

int event_elements_resize_cb( Ihandle *ih, int width, int height){
    return event_common_ii("RESIZE_CB",ih,width,height);
}

int event_elements_scroll_cb( Ihandle *ih, int op, float posx, float posy){
    return event_common_iff("SCROLL_CB",ih,op,posx,posy);
}

int event_elements_touch_cb( Ihandle *ih, int id, int x, int y, char* state){
    return event_common_iiis("TOUCH_CB",ih,id,x,y,state);
}

int event_elements_multitouch_cb( Ihandle *ih, int count, int* pid, int* px, int* py, int* pstate){
    return event_common_iIIII("MULTITOUCH_CB",ih,count,pid,px,py,pstate);
}


int event_elements_wheel_cb( Ihandle *ih, float delta, int x, int y, char *status){
    return event_common_fiis("WHEEL_CB",ih,delta,x,y,status);
}

int event_elements_wom_cb( Ihandle *ih, int state){
    return event_common_i("WOM_CB",ih,state);
}

int event_elements_dropfiles_cb( Ihandle *ih, char* filename, int num, int x, int y){
    return event_common_siii("DROPFILES_CB",ih,filename,num,x,y);
}

int event_elements_dragbegin_cb( Ihandle *ih, int x, int y){
    return event_common_ii("DRAGBEGIN_CB",ih,x,y);
}

int event_elements_dragdatasize_cb( Ihandle *ih, char* type){
    return event_common_s("DRAGDATASIZE_CB",ih,type);
}

int event_elements_dragdata_cb( Ihandle *ih, char* type, void* data, int size){
    return event_common_sCi("DRAGDATA_CB",ih,type,data,size);
}

int event_elements_dragend_cb( Ihandle *ih, int action){
    return event_common_i("DRAGEND_CB",ih,action);
}


int event_elements_dropdata_cb( Ihandle *ih, int action){
    return event_common_i("DROPDATA_CB",ih,action);
}

int event_elements_dropmotion_cb( Ihandle *ih, int action){
    return event_common_i("DROPMOTION_CB",ih,action);
}

int event_elements_move_cb( Ihandle *ih, int x, int y){
    return event_common_ii("MOVE_CB",ih,x,y);
}


int event_elements_trayclick_cb( Ihandle *ih, int but, int pressed, int dclick){
    return event_common_iii("TRAYCLICK_CB",ih,but,pressed,dclick);
}

int event_elements_caret_cb( Ihandle *ih, int lin, int col, int pos){
    return event_common_iii("CARET_CB",ih,lin,col,pos);
}

int event_elements_dblclick_cb( Ihandle *ih, int item, char *text){
    return event_common_is("DBLCLICK_CB",ih,item,text);
}

int event_elements_dragdrop_cb( Ihandle *ih, int drag_id, int drop_id, int isshift, int iscontrol){
    return event_common_iiii("DRAGDROP_CB",ih,drag_id,drop_id,isshift,iscontrol);
}

int event_elements_edit_cb( Ihandle *ih, int c, char *new_value){
    return event_common_is("EDIT_CB",ih,c,new_value);
}

int event_elements_multiselect_cb( Ihandle *ih, char *value){
    return event_common_s("MULTISELECT_CB",ih,value);
}

int event_elements_spin_cb( Ihandle *ih, int pos){
    return event_common_i("SPIN_CB",ih,pos);
}

int event_elements_tabchange_cb( Ihandle *ih, Ihandle* new_tab, Ihandle* old_tab){
    return event_common_nn("TABCHANGE_CB",ih,new_tab,old_tab);
}

int event_elements_tabchangepos_cb( Ihandle *ih, int new_pos, int old_pos){
    return event_common_ii("TABCHANGEPOS_CB",ih,new_pos,old_pos);
}

int event_elements_tabclose_cb( Ihandle *ih, int pos){
    return event_common_i("TABCLOSE_CB",ih,pos);
}

int event_elements_rightclick_cb( Ihandle *ih, int pos){
    return event_common_i("RIGHTCLICK_CB",ih,pos);
}

int event_elements_selection_cb( Ihandle *ih, int id, int status){
    return event_common_ii("SELECTION_CB",ih,id,status);
}

int event_elements_multiselection_cb( Ihandle *ih, int* ids, int n){
    return event_common_Ii("MULTISELECTION_CB",ih,ids,n);
}


int event_elements_multiunselection_cb( Ihandle *ih, int* ids, int n){
    return event_common_Ii("MULTIUNSELECTION_CB",ih,ids,n);
}

int event_elements_branchopen_cb( Ihandle *ih, int id){
    return event_common_i("BRANCHOPEN_CB",ih,id);
}

int event_elements_branchclose_cb( Ihandle *ih, int id){
    return event_common_i("BRANCHCLOSE_CB",ih,id);
}

int event_elements_executeleaf_cb( Ihandle *ih, int id){
    return event_common_i("EXECUTELEAF_CB",ih,id);
}

int event_elements_showrename_cb( Ihandle *ih, int id){
    return event_common_i("SHOWRENAME_CB",ih,id);
}

int event_elements_rename_cb( Ihandle *ih, int id, char *title){
    return event_common_is("RENAME_CB",ih,id,title);
}

int event_elements_noderemoved_cb( Ihandle *ih, void* userdata){
    return event_common_i("NODEREMOVED_CB",ih,(int)userdata);
}

int event_elements_togglevalue_cb( Ihandle *ih, int id, int state){
    return event_common_ii("TOGGLEVALUE_CB",ih,id,state);
}

int event_elements_cell_cb( Ihandle *ih, int cell){
    return event_common_i("CELL_CB",ih,cell);
}

int event_elements_extended_cb( Ihandle *ih, int cell){
    return event_common_i("EXTENDED_CB",ih,cell);
}

int event_elements_select_cb( Ihandle *ih, int cell, int type){
    return event_common_ii("SELECT_CB",ih,cell,type);
}

int event_elements_switch_cb( Ihandle *ih, int prim_cell, int sec_cell){
    return event_common_ii("SWITCH_CB",ih,prim_cell,sec_cell);
}

int event_elements_button_press_cb( Ihandle *ih, double angle){
    return event_common_d("BUTTON_PRESS_CB",ih,angle);
}

int event_elements_button_release_cb( Ihandle *ih, double angle){
    return event_common_d("BUTTON_RELEASE_CB",ih,angle);
}

int event_elements_mousemove_cb( Ihandle *ih, double angle){
    return event_common_d("MOUSEMOVE_CB",ih,angle);
}

int event_elements_change_cb( Ihandle *ih, unsigned char r, unsigned char g, unsigned char b){
    return event_common_ccc("CHANGE_CB",ih,r,g,b);
}

int event_elements_drag_cb( Ihandle *ih, unsigned char r, unsigned char g, unsigned char b){
    return event_common_ccc("DRAG_CB",ih,r,g,b);
}

int event_scintilla_action( Ihandle *ih, int insert, int pos, int length, char* text ){
    return event_common_iiis("ACTION",ih,insert,pos,length,text);
}

int event_scintilla_autocselection_cb( Ihandle *ih, int pos, char* text ){
    return event_common_is("AUTOCSELECTION_CB",ih,pos,text);
}

int event_scintilla_autoccancelled_cb( Ihandle *ih ){
    return event_common("AUTOCCANCELLED_CB",ih);
}

int event_scintilla_autocchardeleted_cb( Ihandle *ih ){
    return event_common("AUTOCCHARDELETED_CB",ih);
}

int event_scintilla_dwell_cb( Ihandle *ih, int state, int pos, int x, int y){
    return event_common_iiii("DWELL_CB",ih,state,pos,x,y);
}

int event_scintilla_hotspotclick_cb( Ihandle *ih, int pos, int lin, int col, char* status){
    return event_common_iiis("HOTSPOTCLICK_CB",ih,pos,lin,col,status);
}

int event_scintilla_lineschanged_cb( Ihandle *ih, int lin, int num){
    return event_common_ii("LINESCHANGED_CB",ih,lin,num);
}

int event_scintilla_marginclick_cb( Ihandle *ih, int margin, int lin, char* status){
    return event_common_iis("MARGINCLICK_CB",ih,margin,lin,status);
}

int event_scintilla_savepoint_cb( Ihandle *ih, int status){
    return event_common_i("SAVEPOINT_CB",ih,status);
}

int event_scintilla_updatecontent_cb( Ihandle *ih ){
    return event_common("UPDATECONTENT_CB",ih);
}

int event_scintilla_updateselection_cb( Ihandle *ih ){
    return event_common("UPDATESELECTION_CB",ih);
}

int event_scintilla_updatehscroll_cb( Ihandle *ih ){
    return event_common("UPDATEHSCROLL_CB",ih);
}

int event_scintilla_updatevscroll_cb( Ihandle *ih ){
    return event_common("UPDATEVSCROLL_CB",ih);
}

int event_scintilla_zoom_cb( Ihandle *ih, int status){
    return event_common_i("ZOOM_CB",ih,status);
}

int event_webbrowser_completed_cb( Ihandle *ih, char* url){
    return event_common_s("COMPLETED_CB",ih,url);
}

int event_webbrowser_error_cb( Ihandle *ih, char* url){
    return event_common_s("ERROR_CB",ih,url);
}

int event_webbrowser_navigate_cb( Ihandle *ih, char* url){
    return event_common_s("NAVIGATE_CB",ih,url);
}

int event_webbrowser_newwindow_cb( Ihandle *ih, char* url){
    return event_common_s("NEWWINDOW_CB",ih,url);
}

int event_cells_draw_cb( Ihandle* ih, int line, int column, int xmin, int xmax, int ymin, int ymax, cdCanvas* canvas){
    return event_common_iiiiiiC("DRAW_CB",ih,line, column, xmin, xmax, ymin, ymax, canvas);
}

int event_cells_height_cb( Ihandle *ih, int line){
    return event_common_i("HEIGHT_CB",ih,line);
}

int event_cells_hspan_cb( Ihandle *ih, int line, int column){
    return event_common_ii("HSPAN_CB",ih,line,column);
}

int event_cells_mouseclick_cb( Ihandle *ih, int button, int pressed, int line, int column, int x, int y, char* status){
    return event_common_iiiiiis("MOUSECLICK_CB",ih,button,pressed,line,column,x,y,status);
}

int event_cells_mousemotion_cb( Ihandle *ih, int line, int column, int x, int y, char *r){
    return event_common_iiiis("MOUSEMOTION_CB",ih,line,column,x,y,r);
}

int event_cells_ncols_cb( Ihandle *ih ){
    return event_common("NCOLS_CB",ih);
}

int event_cells_nlines_cb( Ihandle *ih ){
    return event_common("NLINES_CB",ih);
}

int event_cells_scrolling_cb( Ihandle *ih, int line, int column){
    return event_common_ii("SCROLLING_CB",ih,line,column);
}

int event_cells_vspan_cb( Ihandle *ih, int line, int column){
    return event_common_ii("VSPAN_CB",ih,line,column);
}

int event_cells_width_cb( Ihandle *ih, int line){
    return event_common_i("WIDTH_CB",ih,line);
}

int event_matrix_action_cb( Ihandle *ih, int key, int lin, int col, int edition, char* value){
    return event_common_iiiis("ACTION_CB",ih,key, lin, col, edition,value);
}

int event_matrix_click_cb( Ihandle *ih, int lin, int col, char *status){
    return event_common_iis("CLICK_CB",ih,lin,col,status);
}

int event_matrix_colresize_cb( Ihandle *ih, int col){
    return event_common_i("COLRESIZE_CB",ih,col);
}

int event_matrix_release_cb( Ihandle *ih, int lin, int col, char *status){
    return event_common_iis("RELEASE_CB",ih,lin,col,status);
}

int event_matrix_resizematrix_cb( Ihandle *ih, int width, int height){
    return event_common_ii("RESIZEMATRIX_CB",ih,width,height);
}

int event_matrix_mousemove_cb( Ihandle *ih, int lin, int col){
    return event_common_ii("MOUSEMOVE_CB",ih,lin,col);
}

int event_matrix_enteritem_cb( Ihandle *ih, int lin, int col){
    return event_common_ii("ENTERITEM_CB",ih,lin,col);
}

int event_matrix_leaveitem_cb( Ihandle *ih, int lin, int col){
    return event_common_ii("LEAVEITEM_CB",ih,lin,col);
}

int event_matrix_scrolltop_cb( Ihandle *ih, int lin, int col){
    return event_common_ii("SCROLLTOP_CB",ih,lin,col);
}

int event_matrix_bgcolor_cb( Ihandle *ih, int lin, int col, int *red, int *green, int *blue){
    return event_common_iiIII("BGCOLOR_CB",ih,lin,col,red,green,blue);
}

int event_matrix_fgcolor_cb( Ihandle *ih, int lin, int col, int *red, int *green, int *blue){
    return event_common_iiIII("FGCOLOR_CB",ih,lin,col,red,green,blue);
}

char* event_matrix_font_cb( Ihandle *ih, int lin, int col){
    return event_common_ii_s("FONT_CB",ih,lin,col);
}

char* event_matrix_type_cb( Ihandle *ih, int lin, int col){
    return event_common_ii_s("TYPE_CB",ih,lin,col);
}

int event_matrix_draw_cb( Ihandle* ih, int lin, int col, int x1, int x2, int y1, int y2, cdCanvas* cnv){
    return event_common_iiiiiiC("DRAW_CB",ih,lin, col, x1, x2, y1, y2, cnv);
}

int event_matrix_dropcheck_cb( Ihandle *ih, int lin, int col){
    return event_common_ii("DROPCHECK_CB",ih,lin,col);
}

char* event_matrix_translatevalue_cb( Ihandle *ih, int lin, int col, char* value){
    return event_common_iis_s("TRANSLATEVALUE_CB",ih,lin,col,value);
}

int event_matrix_togglevalue_cb( Ihandle *ih, int lin, int col, int status){
    return event_common_iii("TOGGLEVALUE_CB",ih,lin,col,status);
}

int event_matrix_valuechanged_cb( Ihandle *ih ){
    return event_common("VALUECHANGED_CB",ih);
}

int event_matrix_drop_cb( Ihandle *ih, Ihandle *drop, int lin, int col){
    return event_common_nii("DROP_CB",ih,drop,lin,col);
}

int event_matrix_menudrop_cb( Ihandle *ih, Ihandle *drop, int lin, int col){
    return event_common_nii("MENUDROP_CB",ih,drop,lin,col);
}

int event_matrix_dropselect_cb( Ihandle *ih, int lin, int col, Ihandle *drop, char *t, int i, int v){
    return event_common_iinsii("DROPSELECT_CB",ih,lin,col,drop,t,i,v);
}

int event_matrix_edition_cb( Ihandle *ih, int lin, int col, int mode, int update){
    if(mode == 0){
        return event_common_iii("EDITION_CB",ih,lin,col,mode);
    }else{
        return event_common_iiii("EDITION_CB",ih,lin,col,mode,update);
    }
}

char* event_matrix_value_cb( Ihandle *ih, int lin, int col){
    return event_common_ii_s("VALUE_CB",ih,lin,col);
}

int event_matrix_value_edit_cb( Ihandle *ih, int lin, int col, char *newval){
    return event_common_iis("VALUE_EDIT_CB",ih,lin,col,newval);
}

int event_matrix_mark_cb( Ihandle *ih, int lin, int col){
    return event_common_ii("MARK_CB",ih,lin,col);
}

int event_matrix_markedit_cb( Ihandle *ih, int lin, int col, int marked){
    return event_common_iii("MARKEDIT_CB",ih,lin,col,marked);
}

int event_matrixex_busy_cb( Ihandle *ih, int status, int count, char* name){
    return event_common_iis("BUSY_CB",ih,status,count,name);
}

int event_matrixex_numericgetvalue_cb( Ihandle *ih, int lin, int col){
    return event_common_ii("NUMERICGETVALUE_CB",ih,lin,col);
}

int event_matrixex_numericsetvalue_cb( Ihandle *ih, int lin, int col, double value){
    return event_common_iid("NUMERICSETVALUE_CB",ih,lin,col,value);
}

int event_matrixex_menucontext_cb( Ihandle *ih, Ihandle* menu, int lin, int col){
    return event_common_nii("MENUCONTEXT_CB",ih,menu,lin,col);
}

int event_matrixex_pastesize_cb( Ihandle *ih, int lin, int col){
    return event_common_ii("PASTESIZE_CB",ih,lin,col);
}

int event_matrixex_sortcolumncompare_cb( Ihandle *ih, int col, int lin1, int lin2){
    return event_common_iii("SORTCOLUMNCOMPARE_CB",ih,col,lin1,lin2);
}

int event_matrixlist_imagevaluechanged_cb( Ihandle *ih, int lin, int imagevalue){
    return event_common_ii("IMAGEVALUECHANGED_CB",ih,lin,imagevalue);
}

int event_matrixlist_listaction_cb( Ihandle *ih,  int item, int state){
    return event_common_ii("LISTACTION_CB",ih,item,state);
}

int event_matrixlist_listclick_cb( Ihandle *ih, int lin, int col, char *status){
    return event_common_iis("LISTCLICK_CB",ih,lin,col,status);
}

int event_matrixlist_listdraw_cb( Ihandle* ih, int lin, int col, int x1, int x2, int y1, int y2, cdCanvas* cnv){
    return event_common_iiiiiiC("LISTDRAW_CB",ih,lin, col, x1, x2, y1, y2, cnv);
}

int event_matrixlist_listedition_cb( Ihandle *ih, int lin, int col, int mode, int update){
    if(mode == 0){
        return event_common_iii("LISTEDITION_CB",ih,lin,col,mode);
    }else{
        return event_common_iiii("LISTEDITION_CB",ih,lin,col,mode,update);
    }
}

int event_matrixlist_listinsert_cb( Ihandle *ih, int lin){
    return event_common_i("LISTINSERT_CB",ih,lin);
}

int event_matrixlist_listrelease_cb( Ihandle *ih, int lin, int col, char *status){
    return event_common_iis("LISTRELEASE_CB",ih,lin,col,status);
}

int event_matrixlist_listremove_cb( Ihandle *ih, int lin){
    return event_common_i("LISTREMOVE_CB",ih,lin);
}

int event_plot_clicksample_cb( Ihandle *ih, int ds_index, int sample_index, double x, double y, int button){
    return event_common_iiddi("CLICKSAMPLE_CB",ih,ds_index,sample_index,x,y,button);
}

int event_plot_clicksegment_cb( Ihandle *ih, int ds_index, int sample_index1, double x1, double y1, int sample_index2, double x2, double y2, int button){
    return event_common_iiddiddi("CLICKSEGMENT_CB",ih,ds_index,sample_index1,x1,y1,sample_index2,x2,y2,button);
}

int event_plot_editsample_cb( Ihandle *ih, int ds_index, int sample_index, double x, double y){
    return event_common_iidd("EDITSAMPLE_CB",ih,ds_index,sample_index,x,y);
}

int event_plot_delete_cb( Ihandle *ih, int ds_index, int sample_index, double x, double y){
    return event_common_iidd("DELETE_CB",ih,ds_index,sample_index,x,y);
}

int event_plot_deletebegin_cb( Ihandle *ih ){
    return event_common("DELETEBEGIN_CB",ih);
}

int event_plot_deleteend_cb( Ihandle *ih ){
    return event_common("DELETEEND_CB",ih);
}

int event_plot_drawsample_cb( Ihandle *ih, int ds_index, int sample_index, double x, double y, int selected){
    return event_common_iiddi("DRAWSAMPLE_CB",ih,ds_index,sample_index,x,y,selected);
}

int event_plot_menucontext_cb( Ihandle *ih, Ihandle* menu, int cnv_x, int cnv_y){
    return event_common_nii("MENUCONTEXT_CB",ih,menu,cnv_x,cnv_y);
}

int event_plot_menucontextclose_cb( Ihandle *ih, Ihandle* menu, int cnv_x, int cnv_y){
    return event_common_nii("MENUCONTEXTCLOSE_CB",ih,menu,cnv_x,cnv_y);
}

int event_plot_dspropertieschanged_cb( Ihandle *ih, int ds_index){
    return event_common_i("DSPROPERTIESCHANGED_CB",ih,ds_index);
}

int event_plot_propertieschanged_cb( Ihandle *ih ){
    return event_common("PROPERTIESCHANGED_CB",ih);
}

int event_plot_select_cb( Ihandle *ih, int ds_index, int sample_index, double x, double y, int selected){
    return event_common_iiddi("SELECT_CB",ih,ds_index,sample_index,x,y,selected);
}

int event_plot_selectbegin_cb( Ihandle *ih ){
    return event_common("SELECTBEGIN_CB",ih);
}

int event_plot_selectend_cb( Ihandle *ih ){
    return event_common("SELECTEND_CB",ih);
}

int event_plot_plotbutton_cb( Ihandle *ih, int button, int pressed, double x, double y, char* status){
    return event_common_iidds("PLOTBUTTON_CB",ih,button,pressed,x,y,status);
}

int event_plot_plotmotion_cb( Ihandle *ih, double x, double y, char* status){
    return event_common_dds("PLOTMOTION_CB",ih,x,y,status);
}

int event_plot_predraw_cb( Ihandle *ih, cdCanvas* cnv){
    return event_common_C("PREDRAW_CB",ih,cnv);
}

int event_plot_postdraw_cb( Ihandle *ih, cdCanvas* cnv){
    return event_common_C("POSTDRAW_CB",ih,cnv);
}

int event_thread_thread_cb( Ihandle *ih ){
    return event_common("THREAD_CB",ih);
}

// =============================================================================
void event_register_callback()
{
    zval event_callback;

    // ======================================== common事件 ========================================
    ZVAL_PTR(&event_callback,(Icallback) event_common_action);
    zend_hash_str_add_new(iup_callback,"expander_ACTION",strlen("expander_ACTION"),&event_callback);
    zend_hash_str_add_new(iup_callback,"item_ACTION",strlen("item_ACTION"),&event_callback);
    zend_hash_str_add_new(iup_callback,"button_ACTION",strlen("button_ACTION"),&event_callback);
    zend_hash_str_add_new(iup_callback,"canvas_ACTION",strlen("canvas_ACTION"),&event_callback);
    zend_hash_str_add_new(iup_callback,"dialog_ACTION",strlen("dialog_ACTION"),&event_callback);
    zend_hash_str_add_new(iup_callback,"layoutdialog_ACTION",strlen("layoutdialog_ACTION"),&event_callback);
    zend_hash_str_add_new(iup_callback,"list_ACTION",strlen("list_ACTION"),&event_callback);
    zend_hash_str_add_new(iup_callback,"text_ACTION",strlen("text_ACTION"),&event_callback);
    zend_hash_str_add_new(iup_callback,"toggle_ACTION",strlen("toggle_ACTION"),&event_callback);
    zend_hash_str_add_new(iup_callback,"link_ACTION",strlen("link_ACTION"),&event_callback);
    zend_hash_str_add_new(iup_callback,"olecontrol_ACTION",strlen("olecontrol_ACTION"),&event_callback);
    zend_hash_str_add_new(iup_callback,"matrix_ACTION",strlen("matrix_ACTION"),&event_callback);
    zend_hash_str_add_new(iup_callback,"matrixex_ACTION",strlen("matrixex_ACTION"),&event_callback);

    ZVAL_PTR(&event_callback,(Icallback) event_flat_action);
    zend_hash_str_add_new(iup_callback,"flatbutton_FLAT_ACTION",strlen("flatbutton_FLAT_ACTION"),&event_callback);
    zend_hash_str_add_new(iup_callback,"flattoggle_FLAT_ACTION",strlen("flattoggle_FLAT_ACTION"),&event_callback);
    zend_hash_str_add_new(iup_callback,"dropbutton_FLAT_ACTION",strlen("dropbutton_FLAT_ACTION"),&event_callback);
    zend_hash_str_add_new(iup_callback,"flattabs_FLAT_ACTION",strlen("flattabs_FLAT_ACTION"),&event_callback);
    zend_hash_str_add_new(iup_callback,"flatseparator_FLAT_ACTION",strlen("flatseparator_ACTION"),&event_callback);

    // DESTROY_CB
    ZVAL_PTR(&event_callback,(Icallback) event_common_destroy_cb);
    zend_hash_str_add_new(iup_callback,"frame_DESTROY_CB",strlen("frame_DESTROY_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"item_DESTROY_CB",strlen("item_DESTROY_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"submenu_DESTROY_CB",strlen("submenu_DESTROY_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"menu_DESTROY_CB",strlen("menu_DESTROY_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"button_DESTROY_CB",strlen("button_DESTROY_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"canvas_DESTROY_CB",strlen("canvas_DESTROY_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"flatseparator_DESTROY_CB",strlen("flatseparator_DESTROY_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"dialog_DESTROY_CB",strlen("dialog_DESTROY_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"layoutdialog_DESTROY_CB",strlen("layoutdialog_DESTROY_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"list_DESTROY_CB",strlen("list_DESTROY_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"text_DESTROY_CB",strlen("text_DESTROY_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"val_DESTROY_CB",strlen("val_DESTROY_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"tabs_DESTROY_CB",strlen("tabs_DESTROY_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"toggle_DESTROY_CB",strlen("toggle_DESTROY_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"tree_DESTROY_CB",strlen("tree_DESTROY_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"datepick_DESTROY_CB",strlen("datepick_DESTROY_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"calendar_DESTROY_CB",strlen("calendar_DESTROY_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"colorbar_DESTROY_CB",strlen("colorbar_DESTROY_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"dial_DESTROY_CB",strlen("dial_DESTROY_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"colorbrowser_DESTROY_CB",strlen("colorbrowser_DESTROY_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"gauge_DESTROY_CB",strlen("gauge_DESTROY_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"label_DESTROY_CB",strlen("label_DESTROY_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"animatedlabel_DESTROY_CB",strlen("animatedlabel_DESTROY_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"progressbar_DESTROY_CB",strlen("progressbar_DESTROY_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"scintilla_DESTROY_CB",strlen("scintilla_DESTROY_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"webbrowser_DESTROY_CB",strlen("webbrowser_DESTROY_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"olecontrol_DESTROY_CB",strlen("olecontrol_DESTROY_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"cells_DESTROY_CB",strlen("cells_DESTROY_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"matrix_DESTROY_CB",strlen("matrix_DESTROY_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"matrixex_DESTROY_CB",strlen("matrixex_DESTROY_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"plot_DESTROY_CB",strlen("plot_DESTROY_CB"),&event_callback);

    ZVAL_PTR(&event_callback,(Icallback) event_flat_destroy_cb);
    zend_hash_str_add_new(iup_callback,"flatbutton_FLAT_DESTROY_CB",strlen("flatbutton_FLAT_DESTROY_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"flattoggle_FLAT_DESTROY_CB",strlen("flattoggle_FLAT_DESTROY_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"dropbutton_FLAT_DESTROY_CB",strlen("dropbutton_FLAT_DESTROY_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"flattabs_FLAT_DESTROY_CB",strlen("flattabs_FLAT_DESTROY_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"flatval_FLAT_DESTROY_CB",strlen("flatval_FLAT_DESTROY_CB"),&event_callback);
    
    // LDESTROY_CB
    ZVAL_PTR(&event_callback,(Icallback) event_common_ldestroy_cb);

    // MAP_CB
    ZVAL_PTR(&event_callback,(Icallback) event_common_map_cb);
    zend_hash_str_add_new(iup_callback,"frame_MAP_CB",strlen("frame_MAP_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"item_MAP_CB",strlen("item_MAP_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"submenu_MAP_CB",strlen("submenu_MAP_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"menu_MAP_CB",strlen("menu_MAP_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"button_MAP_CB",strlen("button_MAP_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"canvas_MAP_CB",strlen("canvas_MAP_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"flatseparator_MAP_CB",strlen("flatseparator_MAP_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"dialog_MAP_CB",strlen("dialog_MAP_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"layoutdialog_MAP_CB",strlen("layoutdialog_MAP_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"list_MAP_CB",strlen("list_MAP_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"text_MAP_CB",strlen("text_MAP_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"val_MAP_CB",strlen("val_MAP_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"tabs_MAP_CB",strlen("tabs_MAP_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"toggle_MAP_CB",strlen("toggle_MAP_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"tree_MAP_CB",strlen("tree_MAP_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"datepick_MAP_CB",strlen("datepick_MAP_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"calendar_MAP_CB",strlen("calendar_MAP_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"colorbar_MAP_CB",strlen("colorbar_MAP_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"dial_MAP_CB",strlen("dial_MAP_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"colorbrowser_MAP_CB",strlen("colorbrowser_MAP_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"gauge_MAP_CB",strlen("gauge_MAP_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"label_MAP_CB",strlen("label_MAP_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"animatedlabel_MAP_CB",strlen("animatedlabel_MAP_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"progressbar_MAP_CB",strlen("progressbar_MAP_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"scintilla_MAP_CB",strlen("scintilla_MAP_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"webbrowser_MAP_CB",strlen("webbrowser_MAP_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"olecontrol_MAP_CB",strlen("olecontrol_MAP_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"cells_MAP_CB",strlen("cells_MAP_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"matrix_MAP_CB",strlen("matrix_MAP_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"matrixex_MAP_CB",strlen("matrixex_MAP_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"plot_MAP_CB",strlen("plot_MAP_CB"),&event_callback);

    ZVAL_PTR(&event_callback,(Icallback) event_flat_map_cb);
    zend_hash_str_add_new(iup_callback,"flatbutton_FLAT_MAP_CB",strlen("flatbutton_FLAT_MAP_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"flattoggle_FLAT_MAP_CB",strlen("flattoggle_FLAT_MAP_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"dropbutton_FLAT_MAP_CB",strlen("dropbutton_FLAT_MAP_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"flattabs_FLAT_MAP_CB",strlen("flattabs_FLAT_MAP_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"flatval_FLAT_MAP_CB",strlen("flatval_FLAT_MAP_CB"),&event_callback);
    
    // UNMAP_CB
    ZVAL_PTR(&event_callback,(Icallback) event_common_unmap_cb);
    zend_hash_str_add_new(iup_callback,"frame_UNMAP_CB",strlen("frame_UNMAP_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"item_UNMAP_CB",strlen("item_UNMAP_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"submenu_UNMAP_CB",strlen("submenu_UNMAP_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"menu_UNMAP_CB",strlen("menu_UNMAP_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"button_UNMAP_CB",strlen("button_UNMAP_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"canvas_UNMAP_CB",strlen("canvas_UNMAP_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"flatseparator_UNMAP_CB",strlen("flatseparator_UNMAP_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"dialog_UNMAP_CB",strlen("dialog_UNMAP_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"layoutdialog_UNMAP_CB",strlen("layoutdialog_UNMAP_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"list_UNMAP_CB",strlen("list_UNMAP_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"text_UNMAP_CB",strlen("text_UNMAP_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"val_UNMAP_CB",strlen("val_UNMAP_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"tabs_UNMAP_CB",strlen("tabs_UNMAP_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"toggle_UNMAP_CB",strlen("toggle_UNMAP_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"tree_UNMAP_CB",strlen("tree_UNMAP_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"datepick_UNMAP_CB",strlen("datepick_UNMAP_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"calendar_UNMAP_CB",strlen("calendar_UNMAP_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"colorbar_UNMAP_CB",strlen("colorbar_UNMAP_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"dial_UNMAP_CB",strlen("dial_UNMAP_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"colorbrowser_UNMAP_CB",strlen("colorbrowser_UNMAP_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"gauge_UNMAP_CB",strlen("gauge_UNMAP_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"label_UNMAP_CB",strlen("label_UNMAP_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"animatedlabel_UNMAP_CB",strlen("animatedlabel_UNMAP_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"progressbar_UNMAP_CB",strlen("progressbar_UNMAP_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"scintilla_UNMAP_CB",strlen("scintilla_UNMAP_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"webbrowser_UNMAP_CB",strlen("webbrowser_UNMAP_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"olecontrol_UNMAP_CB",strlen("olecontrol_UNMAP_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"cells_UNMAP_CB",strlen("cells_UNMAP_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"matrix_UNMAP_CB",strlen("matrix_UNMAP_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"matrixex_UNMAP_CB",strlen("matrixex_UNMAP_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"plot_UNMAP_CB",strlen("plot_UNMAP_CB"),&event_callback);

    ZVAL_PTR(&event_callback,(Icallback) event_flat_unmap_cb);
    zend_hash_str_add_new(iup_callback,"flatbutton_FLAT_UNMAP_CB",strlen("flatbutton_FLAT_UNMAP_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"flattoggle_FLAT_UNMAP_CB",strlen("flattoggle_FLAT_UNMAP_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"dropbutton_FLAT_UNMAP_CB",strlen("dropbutton_FLAT_UNMAP_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"flattabs_FLAT_UNMAP_CB",strlen("flattabs_FLAT_UNMAP_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"flatval_FLAT_UNMAP_CB",strlen("flatval_FLAT_UNMAP_CB"),&event_callback);

    // GETFOCUS_CB
    ZVAL_PTR(&event_callback,(Icallback) event_common_getfocus_cb);
    zend_hash_str_add_new(iup_callback,"button_GETFOCUS_CB",strlen("button_GETFOCUS_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"canvas_GETFOCUS_CB",strlen("canvas_GETFOCUS_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"flatseparator_GETFOCUS_CB",strlen("flatseparator_GETFOCUS_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"dialog_GETFOCUS_CB",strlen("dialog_GETFOCUS_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"layoutdialog_GETFOCUS_CB",strlen("layoutdialog_GETFOCUS_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"list_GETFOCUS_CB",strlen("list_GETFOCUS_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"text_GETFOCUS_CB",strlen("text_GETFOCUS_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"val_GETFOCUS_CB",strlen("val_GETFOCUS_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"tabs_GETFOCUS_CB",strlen("tabs_GETFOCUS_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"toggle_GETFOCUS_CB",strlen("toggle_GETFOCUS_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"tree_GETFOCUS_CB",strlen("tree_GETFOCUS_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"datepick_GETFOCUS_CB",strlen("datepick_GETFOCUS_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"calendar_GETFOCUS_CB",strlen("calendar_GETFOCUS_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"colorbar_GETFOCUS_CB",strlen("colorbar_GETFOCUS_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"dial_GETFOCUS_CB",strlen("dial_GETFOCUS_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"colorbrowser_GETFOCUS_CB",strlen("colorbrowser_GETFOCUS_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"scintilla_GETFOCUS_CB",strlen("scintilla_GETFOCUS_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"olecontrol_GETFOCUS_CB",strlen("olecontrol_GETFOCUS_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"cells_GETFOCUS_CB",strlen("cells_GETFOCUS_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"matrix_GETFOCUS_CB",strlen("matrix_GETFOCUS_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"matrixex_GETFOCUS_CB",strlen("matrixex_GETFOCUS_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"plot_GETFOCUS_CB",strlen("plot_GETFOCUS_CB"),&event_callback);

    ZVAL_PTR(&event_callback,(Icallback) event_flat_getfocus_cb);
    zend_hash_str_add_new(iup_callback,"flatbutton_FLAT_GETFOCUS_CB",strlen("flatbutton_FLAT_GETFOCUS_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"flattoggle_FLAT_GETFOCUS_CB",strlen("flattoggle_FLAT_GETFOCUS_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"dropbutton_FLAT_GETFOCUS_CB",strlen("dropbutton_FLAT_GETFOCUS_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"flattabs_FLAT_GETFOCUS_CB",strlen("flattabs_FLAT_GETFOCUS_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"flatval_FLAT_GETFOCUS_CB",strlen("flatval_FLAT_GETFOCUS_CB"),&event_callback);

    // KILLFOCUS_CB
    ZVAL_PTR(&event_callback,(Icallback) event_common_killfocus_cb);
    zend_hash_str_add_new(iup_callback,"button_KILLFOCUS_CB",strlen("button_KILLFOCUS_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"canvas_KILLFOCUS_CB",strlen("canvas_KILLFOCUS_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"flatseparator_KILLFOCUS_CB",strlen("flatseparator_KILLFOCUS_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"dialog_KILLFOCUS_CB",strlen("dialog_KILLFOCUS_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"layoutdialog_KILLFOCUS_CB",strlen("layoutdialog_KILLFOCUS_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"list_KILLFOCUS_CB",strlen("list_KILLFOCUS_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"text_KILLFOCUS_CB",strlen("text_KILLFOCUS_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"val_KILLFOCUS_CB",strlen("val_KILLFOCUS_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"tabs_KILLFOCUS_CB",strlen("tabs_KILLFOCUS_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"toggle_KILLFOCUS_CB",strlen("toggle_KILLFOCUS_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"tree_KILLFOCUS_CB",strlen("tree_KILLFOCUS_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"datepick_KILLFOCUS_CB",strlen("datepick_KILLFOCUS_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"calendar_KILLFOCUS_CB",strlen("calendar_KILLFOCUS_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"colorbar_KILLFOCUS_CB",strlen("colorbar_KILLFOCUS_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"dial_KILLFOCUS_CB",strlen("dial_KILLFOCUS_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"colorbrowser_KILLFOCUS_CB",strlen("colorbrowser_KILLFOCUS_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"scintilla_KILLFOCUS_CB",strlen("scintilla_KILLFOCUS_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"olecontrol_KILLFOCUS_CB",strlen("olecontrol_KILLFOCUS_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"cells_KILLFOCUS_CB",strlen("cells_KILLFOCUS_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"matrix_KILLFOCUS_CB",strlen("matrix_KILLFOCUS_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"matrixex_KILLFOCUS_CB",strlen("matrixex_KILLFOCUS_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"plot_KILLFOCUS_CB",strlen("plot_KILLFOCUS_CB"),&event_callback);

    ZVAL_PTR(&event_callback,(Icallback) event_flat_killfocus_cb);
    zend_hash_str_add_new(iup_callback,"flatbutton_FLAT_KILLFOCUS_CB",strlen("flatbutton_FLAT_KILLFOCUS_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"flattoggle_FLAT_KILLFOCUS_CB",strlen("flattoggle_FLAT_KILLFOCUS_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"dropbutton_FLAT_KILLFOCUS_CB",strlen("dropbutton_FLAT_KILLFOCUS_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"flattabs_FLAT_KILLFOCUS_CB",strlen("flattabs_FLAT_KILLFOCUS_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"flatval_FLAT_KILLFOCUS_CB",strlen("flatval_FLAT_KILLFOCUS_CB"),&event_callback);

    // ENTERWINDOW_CB
    ZVAL_PTR(&event_callback,(Icallback) event_common_enterwindow_cb);
    zend_hash_str_add_new(iup_callback,"button_ENTERWINDOW_CB",strlen("button_ENTERWINDOW_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"canvas_ENTERWINDOW_CB",strlen("canvas_ENTERWINDOW_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"flatseparator_ENTERWINDOW_CB",strlen("flatseparator_ENTERWINDOW_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"dialog_ENTERWINDOW_CB",strlen("dialog_ENTERWINDOW_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"layoutdialog_ENTERWINDOW_CB",strlen("layoutdialog_ENTERWINDOW_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"list_ENTERWINDOW_CB",strlen("list_ENTERWINDOW_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"text_ENTERWINDOW_CB",strlen("text_ENTERWINDOW_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"val_ENTERWINDOW_CB",strlen("val_ENTERWINDOW_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"tabs_ENTERWINDOW_CB",strlen("tabs_ENTERWINDOW_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"toggle_ENTERWINDOW_CB",strlen("toggle_ENTERWINDOW_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"tree_ENTERWINDOW_CB",strlen("tree_ENTERWINDOW_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"datepick_ENTERWINDOW_CB",strlen("datepick_ENTERWINDOW_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"calendar_ENTERWINDOW_CB",strlen("calendar_ENTERWINDOW_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"colorbar_ENTERWINDOW_CB",strlen("colorbar_ENTERWINDOW_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"dial_ENTERWINDOW_CB",strlen("dial_ENTERWINDOW_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"colorbrowser_ENTERWINDOW_CB",strlen("colorbrowser_ENTERWINDOW_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"label_ENTERWINDOW_CB",strlen("label_ENTERWINDOW_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"animatedlabel_ENTERWINDOW_CB",strlen("animatedlabel_ENTERWINDOW_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"scintilla_ENTERWINDOW_CB",strlen("scintilla_ENTERWINDOW_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"olecontrol_ENTERWINDOW_CB",strlen("olecontrol_ENTERWINDOW_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"cells_ENTERWINDOW_CB",strlen("cells_ENTERWINDOW_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"matrix_ENTERWINDOW_CB",strlen("matrix_ENTERWINDOW_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"matrixex_ENTERWINDOW_CB",strlen("matrixex_ENTERWINDOW_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"plot_ENTERWINDOW_CB",strlen("plot_ENTERWINDOW_CB"),&event_callback);

    ZVAL_PTR(&event_callback,(Icallback) event_flat_enterwindow_cb);
    zend_hash_str_add_new(iup_callback,"flatbutton_FLAT_ENTERWINDOW_CB",strlen("flatbutton_FLAT_ENTERWINDOW_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"flattoggle_FLAT_ENTERWINDOW_CB",strlen("flattoggle_FLAT_ENTERWINDOW_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"dropbutton_FLAT_ENTERWINDOW_CB",strlen("dropbutton_FLAT_ENTERWINDOW_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"flattabs_FLAT_ENTERWINDOW_CB",strlen("flattabs_FLAT_ENTERWINDOW_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"flatval_FLAT_ENTERWINDOW_CB",strlen("flatval_FLAT_ENTERWINDOW_CB"),&event_callback);

    // LEAVEWINDOW_CB
    ZVAL_PTR(&event_callback,(Icallback) event_common_leavewindow_cb);
    zend_hash_str_add_new(iup_callback,"button_LEAVEWINDOW_CB",strlen("button_LEAVEWINDOW_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"canvas_LEAVEWINDOW_CB",strlen("canvas_LEAVEWINDOW_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"flatseparator_LEAVEWINDOW_CB",strlen("flatseparator_LEAVEWINDOW_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"dialog_LEAVEWINDOW_CB",strlen("dialog_LEAVEWINDOW_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"layoutdialog_LEAVEWINDOW_CB",strlen("layoutdialog_LEAVEWINDOW_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"list_LEAVEWINDOW_CB",strlen("list_LEAVEWINDOW_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"text_LEAVEWINDOW_CB",strlen("text_LEAVEWINDOW_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"val_LEAVEWINDOW_CB",strlen("val_LEAVEWINDOW_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"tabs_LEAVEWINDOW_CB",strlen("tabs_LEAVEWINDOW_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"toggle_LEAVEWINDOW_CB",strlen("toggle_LEAVEWINDOW_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"tree_LEAVEWINDOW_CB",strlen("tree_LEAVEWINDOW_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"datepick_LEAVEWINDOW_CB",strlen("datepick_LEAVEWINDOW_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"calendar_LEAVEWINDOW_CB",strlen("calendar_LEAVEWINDOW_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"colorbar_LEAVEWINDOW_CB",strlen("colorbar_LEAVEWINDOW_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"dial_LEAVEWINDOW_CB",strlen("dial_LEAVEWINDOW_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"colorbrowser_LEAVEWINDOW_CB",strlen("colorbrowser_LEAVEWINDOW_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"label_LEAVEWINDOW_CB",strlen("label_LEAVEWINDOW_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"animatedlabel_LEAVEWINDOW_CB",strlen("animatedlabel_LEAVEWINDOW_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"scintilla_LEAVEWINDOW_CB",strlen("scintilla_LEAVEWINDOW_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"olecontrol_LEAVEWINDOW_CB",strlen("olecontrol_LEAVEWINDOW_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"cells_LEAVEWINDOW_CB",strlen("cells_LEAVEWINDOW_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"matrix_LEAVEWINDOW_CB",strlen("matrix_LEAVEWINDOW_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"matrixex_LEAVEWINDOW_CB",strlen("matrixex_LEAVEWINDOW_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"plot_LEAVEWINDOW_CB",strlen("plot_LEAVEWINDOW_CB"),&event_callback);

    ZVAL_PTR(&event_callback,(Icallback) event_flat_leavewindow_cb);
    zend_hash_str_add_new(iup_callback,"flatbutton_FLAT_LEAVEWINDOW_CB",strlen("flatbutton_FLAT_LEAVEWINDOW_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"flattoggle_FLAT_LEAVEWINDOW_CB",strlen("flattoggle_FLAT_LEAVEWINDOW_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"dropbutton_FLAT_LEAVEWINDOW_CB",strlen("dropbutton_FLAT_LEAVEWINDOW_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"flattabs_FLAT_LEAVEWINDOW_CB",strlen("flattabs_FLAT_LEAVEWINDOW_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"flatval_FLAT_LEAVEWINDOW_CB",strlen("flatval_FLAT_LEAVEWINDOW_CB"),&event_callback);

    // HELP_CB
    ZVAL_PTR(&event_callback,(Icallback) event_common_help_cb);
    zend_hash_str_add_new(iup_callback,"item_HELP_CB",strlen("item_HELP_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"submenu_HELP_CB",strlen("submenu_HELP_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"menu_HELP_CB",strlen("menu_HELP_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"button_HELP_CB",strlen("button_HELP_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"canvas_HELP_CB",strlen("canvas_HELP_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"flatseparator_HELP_CB",strlen("flatseparator_HELP_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"dialog_HELP_CB",strlen("dialog_HELP_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"layoutdialog_HELP_CB",strlen("layoutdialog_HELP_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"list_HELP_CB",strlen("list_HELP_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"text_HELP_CB",strlen("text_HELP_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"val_HELP_CB",strlen("val_HELP_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"tabs_HELP_CB",strlen("tabs_HELP_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"toggle_HELP_CB",strlen("toggle_HELP_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"tree_HELP_CB",strlen("tree_HELP_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"datepick_HELP_CB",strlen("datepick_HELP_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"calendar_HELP_CB",strlen("calendar_HELP_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"colorbar_HELP_CB",strlen("colorbar_HELP_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"dial_HELP_CB",strlen("dial_HELP_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"colorbrowser_HELP_CB",strlen("colorbrowser_HELP_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"scintilla_HELP_CB",strlen("scintilla_HELP_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"cells_HELP_CB",strlen("cells_HELP_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"matrix_HELP_CB",strlen("matrix_HELP_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"matrixex_HELP_CB",strlen("matrixex_HELP_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"plot_HELP_CB",strlen("plot_HELP_CB"),&event_callback);

    ZVAL_PTR(&event_callback,(Icallback) event_flat_help_cb);
    zend_hash_str_add_new(iup_callback,"flatbutton_FLAT_HELP_CB",strlen("flatbutton_FLAT_HELP_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"flattoggle_FLAT_HELP_CB",strlen("flattoggle_FLAT_HELP_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"dropbutton_FLAT_HELP_CB",strlen("dropbutton_FLAT_HELP_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"flattabs_FLAT_HELP_CB",strlen("flattabs_FLAT_HELP_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"flatval_FLAT_HELP_CB",strlen("flatval_FLAT_HELP_CB"),&event_callback);

    // K_ANY
    ZVAL_PTR(&event_callback,(Icallback) event_common_k_any);
    zend_hash_str_add_new(iup_callback,"button_K_ANY",strlen("button_K_ANY"),&event_callback);
    zend_hash_str_add_new(iup_callback,"canvas_K_ANY",strlen("canvas_K_ANY"),&event_callback);
    zend_hash_str_add_new(iup_callback,"flatseparator_K_ANY",strlen("flatseparator_K_ANY"),&event_callback);
    zend_hash_str_add_new(iup_callback,"dialog_K_ANY",strlen("dialog_K_ANY"),&event_callback);
    zend_hash_str_add_new(iup_callback,"layoutdialog_K_ANY",strlen("layoutdialog_K_ANY"),&event_callback);
    zend_hash_str_add_new(iup_callback,"list_K_ANY",strlen("list_K_ANY"),&event_callback);
    zend_hash_str_add_new(iup_callback,"text_K_ANY",strlen("text_K_ANY"),&event_callback);
    zend_hash_str_add_new(iup_callback,"val_K_ANY",strlen("val_K_ANY"),&event_callback);
    zend_hash_str_add_new(iup_callback,"tabs_K_ANY",strlen("tabs_K_ANY"),&event_callback);
    zend_hash_str_add_new(iup_callback,"toggle_K_ANY",strlen("toggle_K_ANY"),&event_callback);
    zend_hash_str_add_new(iup_callback,"tree_K_ANY",strlen("tree_K_ANY"),&event_callback);
    zend_hash_str_add_new(iup_callback,"datepick_K_ANY",strlen("datepick_K_ANY"),&event_callback);
    zend_hash_str_add_new(iup_callback,"calendar_K_ANY",strlen("calendar_K_ANY"),&event_callback);
    zend_hash_str_add_new(iup_callback,"colorbar_K_ANY",strlen("colorbar_K_ANY"),&event_callback);
    zend_hash_str_add_new(iup_callback,"dial_K_ANY",strlen("dial_K_ANY"),&event_callback);
    zend_hash_str_add_new(iup_callback,"colorbrowser_K_ANY",strlen("colorbrowser_K_ANY"),&event_callback);
    zend_hash_str_add_new(iup_callback,"scintilla_K_ANY",strlen("scintilla_K_ANY"),&event_callback);
    zend_hash_str_add_new(iup_callback,"cells_K_ANY",strlen("cells_K_ANY"),&event_callback);
    zend_hash_str_add_new(iup_callback,"matrix_K_ANY",strlen("matrix_K_ANY"),&event_callback);
    zend_hash_str_add_new(iup_callback,"matrixex_K_ANY",strlen("matrixex_K_ANY"),&event_callback);
    zend_hash_str_add_new(iup_callback,"plot_K_ANY",strlen("plot_K_ANY"),&event_callback);

    ZVAL_PTR(&event_callback,(Icallback) event_flat_k_any);
    zend_hash_str_add_new(iup_callback,"flatbutton_FLAT_K_ANY",strlen("flatbutton_FLAT_K_ANY"),&event_callback);
    zend_hash_str_add_new(iup_callback,"flattoggle_FLAT_K_ANY",strlen("flattoggle_FLAT_K_ANY"),&event_callback);
    zend_hash_str_add_new(iup_callback,"dropbutton_FLAT_K_ANY",strlen("dropbutton_FLAT_K_ANY"),&event_callback);
    zend_hash_str_add_new(iup_callback,"flattabs_FLAT_K_ANY",strlen("flattabs_FLAT_K_ANY"),&event_callback);
    zend_hash_str_add_new(iup_callback,"flatval_FLAT_K_ANY",strlen("flatval_FLAT_K_ANY"),&event_callback);


    // POSTMESSAGE_CB
    ZVAL_PTR(&event_callback,(Icallback) event_common_postmessage_cb);
    zend_hash_str_add_new(iup_callback,"frame_POSTMESSAGE_CB",strlen("frame_POSTMESSAGE_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"item_POSTMESSAGE_CB",strlen("item_POSTMESSAGE_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"submenu_POSTMESSAGE_CB",strlen("submenu_POSTMESSAGE_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"menu_POSTMESSAGE_CB",strlen("menu_POSTMESSAGE_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"button_POSTMESSAGE_CB",strlen("button_POSTMESSAGE_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"canvas_POSTMESSAGE_CB",strlen("canvas_POSTMESSAGE_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"flatseparator_POSTMESSAGE_CB",strlen("flatseparator_POSTMESSAGE_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"dialog_POSTMESSAGE_CB",strlen("dialog_POSTMESSAGE_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"layoutdialog_POSTMESSAGE_CB",strlen("layoutdialog_POSTMESSAGE_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"list_POSTMESSAGE_CB",strlen("list_POSTMESSAGE_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"text_POSTMESSAGE_CB",strlen("text_POSTMESSAGE_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"val_POSTMESSAGE_CB",strlen("val_POSTMESSAGE_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"tabs_POSTMESSAGE_CB",strlen("tabs_POSTMESSAGE_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"toggle_POSTMESSAGE_CB",strlen("toggle_POSTMESSAGE_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"tree_POSTMESSAGE_CB",strlen("tree_POSTMESSAGE_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"datepick_POSTMESSAGE_CB",strlen("datepick_POSTMESSAGE_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"calendar_POSTMESSAGE_CB",strlen("calendar_POSTMESSAGE_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"colorbar_POSTMESSAGE_CB",strlen("colorbar_POSTMESSAGE_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"dial_POSTMESSAGE_CB",strlen("dial_POSTMESSAGE_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"colorbrowser_POSTMESSAGE_CB",strlen("colorbrowser_POSTMESSAGE_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"gauge_POSTMESSAGE_CB",strlen("gauge_POSTMESSAGE_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"label_POSTMESSAGE_CB",strlen("label_POSTMESSAGE_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"animatedlabel_POSTMESSAGE_CB",strlen("animatedlabel_POSTMESSAGE_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"progressbar_POSTMESSAGE_CB",strlen("progressbar_POSTMESSAGE_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"scintilla_POSTMESSAGE_CB",strlen("scintilla_POSTMESSAGE_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"webbrowser_POSTMESSAGE_CB",strlen("webbrowser_POSTMESSAGE_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"olecontrol_POSTMESSAGE_CB",strlen("olecontrol_POSTMESSAGE_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"cells_POSTMESSAGE_CB",strlen("cells_POSTMESSAGE_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"matrix_POSTMESSAGE_CB",strlen("matrix_POSTMESSAGE_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"matrixex_POSTMESSAGE_CB",strlen("matrixex_POSTMESSAGE_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"plot_POSTMESSAGE_CB",strlen("plot_POSTMESSAGE_CB"),&event_callback);

    ZVAL_PTR(&event_callback,(Icallback) event_flat_postmessage_cb);
    zend_hash_str_add_new(iup_callback,"flatbutton_FLAT_POSTMESSAGE_CB",strlen("flatbutton_FLAT_POSTMESSAGE_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"flattoggle_FLAT_POSTMESSAGE_CB",strlen("flattoggle_FLAT_POSTMESSAGE_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"dropbutton_FLAT_POSTMESSAGE_CB",strlen("dropbutton_FLAT_POSTMESSAGE_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"flattabs_FLAT_POSTMESSAGE_CB",strlen("flattabs_FLAT_POSTMESSAGE_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"flatval_FLAT_POSTMESSAGE_CB",strlen("flatval_FLAT_POSTMESSAGE_CB"),&event_callback);
    

    // ======================================== 其他可共用事件 ========================================

    ZVAL_PTR(&event_callback,(Icallback) event_elements_valuechanged_cb);
    
    zend_hash_str_add_new(iup_callback,"split_VALUECHANGED_CB",strlen("split_VALUECHANGED_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"flatbutton_VALUECHANGED_CB",strlen("flatbutton_VALUECHANGED_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"flattoggle_VALUECHANGED_CB",strlen("flattoggle_VALUECHANGED_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"dropbutton_VALUECHANGED_CB",strlen("dropbutton_VALUECHANGED_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"list_VALUECHANGED_CB",strlen("list_VALUECHANGED_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"text_VALUECHANGED_CB",strlen("text_VALUECHANGED_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"toggle_VALUECHANGED_CB",strlen("toggle_VALUECHANGED_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"val_VALUECHANGED_CB",strlen("val_VALUECHANGED_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"flatval_VALUECHANGED_CB",strlen("flatval_VALUECHANGED_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"datepick_VALUECHANGED_CB",strlen("datepick_VALUECHANGED_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"calendar_VALUECHANGED_CB",strlen("calendar_VALUECHANGED_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"dial_VALUECHANGED_CB",strlen("dial_VALUECHANGED_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"colorbrowser_VALUECHANGED_CB",strlen("colorbrowser_VALUECHANGED_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"scintilla_VALUECHANGED_CB",strlen("scintilla_VALUECHANGED_CB"),&event_callback);

    // VALUECHANGING_CB
    ZVAL_PTR(&event_callback,(Icallback) event_elements_valuechanging_cb);
    zend_hash_str_add_new(iup_callback,"flatval_VALUECHANGING_CB",strlen("flatval_VALUECHANGING_CB"),&event_callback);

    // LAYOUTUPDATE_CB
    ZVAL_PTR(&event_callback,(Icallback) event_elements_layoutupdate_cb);
    zend_hash_str_add_new(iup_callback,"scrollbox_LAYOUTUPDATE_CB",strlen("scrollbox_LAYOUTUPDATE_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"flatscrollbox_LAYOUTUPDATE_CB",strlen("flatscrollbox_LAYOUTUPDATE_CB"),&event_callback);

    // LAYOUTCHANGED_CB
    ZVAL_PTR(&event_callback,(Icallback) event_elements_layoutchanged_cb);
    zend_hash_str_add_new(iup_callback,"layoutdialog_LAYOUTCHANGED_CB",strlen("layoutdialog_LAYOUTCHANGED_CB"),&event_callback);

    // ATTRIBCHANGED_CB
    ZVAL_PTR(&event_callback,(Icallback) event_elements_attribchanged_cb);
    zend_hash_str_add_new(iup_callback,"layoutdialog_ATTRIBCHANGED_CB",strlen("layoutdialog_ATTRIBCHANGED_CB"),&event_callback);
    


    // FOCUS_CB
    ZVAL_PTR(&event_callback,(Icallback) event_elements_focus_cb);

    zend_hash_str_add_new(iup_callback,"frame_FOCUS_CB",strlen("frame_FOCUS_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"flatframe_FOCUS_CB",strlen("flatframe_FOCUS_CB"),&event_callback);

    zend_hash_str_add_new(iup_callback,"button_FOCUS_CB",strlen("button_FOCUS_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"canvas_FOCUS_CB",strlen("canvas_FOCUS_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"dialog_FOCUS_CB",strlen("dialog_FOCUS_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"layoutdialog_FOCUS_CB",strlen("layoutdialog_FOCUS_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"flatseparator_FOCUS_CB",strlen("flatseparator_FOCUS_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"tabs_FOCUS_CB",strlen("tabs_FOCUS_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"matrix_FOCUS_CB",strlen("matrix_FOCUS_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"matrixex_FOCUS_CB",strlen("matrixex_FOCUS_CB"),&event_callback);


    ZVAL_PTR(&event_callback,(Icallback) event_flat_focus_cb);
    zend_hash_str_add_new(iup_callback,"flatbutton_FLAT_FOCUS_CB",strlen("flatbutton_FLAT_FOCUS_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"flattoggle_FLAT_FOCUS_CB",strlen("flattoggle_FLAT_FOCUS_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"dropbutton_FLAT_FOCUS_CB",strlen("dropbutton_FLAT_FOCUS_CB"),&event_callback);

    // HIGHLIGHT_CB
    ZVAL_PTR(&event_callback,(Icallback) event_elements_highlight_cb);

    zend_hash_str_add_new(iup_callback,"item_HIGHLIGHT_CB",strlen("item_HIGHLIGHT_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"submenu_HIGHLIGHT_CB",strlen("submenu_HIGHLIGHT_CB"),&event_callback);

    // BUTTON_CB
    ZVAL_PTR(&event_callback,(Icallback) event_elements_button_cb);
    zend_hash_str_add_new(iup_callback,"button_BUTTON_CB",strlen("button_BUTTON_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"canvas_BUTTON_CB",strlen("canvas_BUTTON_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"flatseparator_BUTTON_CB",strlen("flatseparator_BUTTON_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"label_BUTTON_CB",strlen("label_BUTTON_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"animatedlabel_BUTTON_CB",strlen("animatedlabel_BUTTON_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"list_BUTTON_CB",strlen("list_BUTTON_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"text_BUTTON_CB",strlen("text_BUTTON_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"tree_BUTTON_CB",strlen("tree_BUTTON_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"scintilla_BUTTON_CB",strlen("scintilla_BUTTON_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"matrix_BUTTON_CB",strlen("matrix_BUTTON_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"matrixex_BUTTON_CB",strlen("matrixex_BUTTON_CB"),&event_callback);

    ZVAL_PTR(&event_callback,(Icallback) event_flat_button_cb);
    zend_hash_str_add_new(iup_callback,"flatbutton_FLAT_BUTTON_CB",strlen("flatbutton_FLAT_BUTTON_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"flattoggle_FLAT_BUTTON_CB",strlen("flattoggle_FLAT_BUTTON_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"dropbutton_FLAT_BUTTON_CB",strlen("dropbutton_FLAT_BUTTON_CB"),&event_callback);

    
    // DROPDOWN_CB
    ZVAL_PTR(&event_callback,(Icallback) event_elements_dropdown_cb);
    zend_hash_str_add_new(iup_callback,"dropbutton_DROPDOWN_CB",strlen("dropbutton_DROPDOWN_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"list_DROPDOWN_CB",strlen("list_DROPDOWN_CB"),&event_callback);

    // DROPSHOW_CB
    ZVAL_PTR(&event_callback,(Icallback) event_elements_dropshow_cb);
    zend_hash_str_add_new(iup_callback,"dropbutton_DROPSHOW_CB",strlen("dropbutton_DROPSHOW_CB"),&event_callback);

    // DRAGDROP_CB
    ZVAL_PTR(&event_callback,(Icallback) event_elements_dragdrop_cb);
    zend_hash_str_add_new(iup_callback,"list_DRAGDROP_CB",strlen("list_DRAGDROP_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"tree_DRAGDROP_CB",strlen("tree_DRAGDROP_CB"),&event_callback);

    // MOTION_CB
    ZVAL_PTR(&event_callback,(Icallback) event_elements_motion_cb);
    zend_hash_str_add_new(iup_callback,"canvas_MOTION_CB",strlen("canvas_MOTION_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"flatseparator_MOTION_CB",strlen("flatseparator_MOTION_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"label_MOTION_CB",strlen("label_MOTION_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"animatedlabel_MOTION_CB",strlen("animatedlabel_MOTION_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"list_MOTION_CB",strlen("list_MOTION_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"text_MOTION_CB",strlen("text_MOTION_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"tree_MOTION_CB",strlen("tree_MOTION_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"scintilla_MOTION_CB",strlen("scintilla_MOTION_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"matrix_MOTION_CB",strlen("matrix_MOTION_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"matrixex_MOTION_CB",strlen("matrixex_MOTION_CB"),&event_callback);

    // KEYPRESS_CB
    ZVAL_PTR(&event_callback,(Icallback) event_elements_keypress_cb);
    zend_hash_str_add_new(iup_callback,"canvas_KEYPRESS_CB",strlen("canvas_KEYPRESS_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"flatseparator_KEYPRESS_CB",strlen("flatseparator_KEYPRESS_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"matrix_KEYPRESS_CB",strlen("matrix_KEYPRESS_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"matrixex_KEYPRESS_CB",strlen("matrixex_KEYPRESS_CB"),&event_callback);

    // RESIZE_CB
    ZVAL_PTR(&event_callback,(Icallback) event_elements_resize_cb);
    zend_hash_str_add_new(iup_callback,"dialog_RESIZE_CB",strlen("dialog_RESIZE_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"layoutdialog_RESIZE_CB",strlen("layoutdialog_RESIZE_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"canvas_RESIZE_CB",strlen("canvas_RESIZE_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"flatseparator_RESIZE_CB",strlen("flatseparator_RESIZE_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"olecontrol_RESIZE_CB",strlen("olecontrol_RESIZE_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"matrix_RESIZE_CB",strlen("matrix_RESIZE_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"matrixex_RESIZE_CB",strlen("matrixex_RESIZE_CB"),&event_callback);

    // SCROLL_CB
    ZVAL_PTR(&event_callback,(Icallback) event_elements_scroll_cb);
    zend_hash_str_add_new(iup_callback,"canvas_SCROLL_CB",strlen("canvas_SCROLL_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"flatseparator_SCROLL_CB",strlen("flatseparator_SCROLL_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"matrix_SCROLL_CB",strlen("matrix_SCROLL_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"matrixex_SCROLL_CB",strlen("matrixex_SCROLL_CB"),&event_callback);

    // TOUCH_CB
    ZVAL_PTR(&event_callback,(Icallback) event_elements_touch_cb);
    zend_hash_str_add_new(iup_callback,"canvas_TOUCH_CB",strlen("canvas_TOUCH_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"flatseparator_TOUCH_CB",strlen("flatseparator_TOUCH_CB"),&event_callback);

    // MULTITOUCH_CB
    ZVAL_PTR(&event_callback,(Icallback) event_elements_multitouch_cb);
    zend_hash_str_add_new(iup_callback,"canvas_MULTITOUCH_CB",strlen("canvas_MULTITOUCH_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"flatseparator_MULTITOUCH_CB",strlen("flatseparator_MULTITOUCH_CB"),&event_callback);

    // WHEEL_CB
    ZVAL_PTR(&event_callback,(Icallback) event_elements_wheel_cb);
    zend_hash_str_add_new(iup_callback,"canvas_WHEEL_CB",strlen("canvas_WHEEL_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"flatseparator_WHEEL_CB",strlen("flatseparator_WHEEL_CB"),&event_callback);

    // WOM_CB
    ZVAL_PTR(&event_callback,(Icallback) event_elements_wom_cb);
    zend_hash_str_add_new(iup_callback,"canvas_WOM_CB",strlen("canvas_WOM_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"flatseparator_WOM_CB",strlen("flatseparator_WOM_CB"),&event_callback);

    // DROPFILES_CB
    ZVAL_PTR(&event_callback,(Icallback) event_elements_dropfiles_cb);
    zend_hash_str_add_new(iup_callback,"dialog_DROPFILES_CB",strlen("dialog_DROPFILES_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"layoutdialog_DROPFILES_CB",strlen("layoutdialog_DROPFILES_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"canvas_DROPFILES_CB",strlen("canvas_DROPFILES_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"flatseparator_DROPFILES_CB",strlen("flatseparator_DROPFILES_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"glcanvas_DROPFILES_CB",strlen("glcanvas_DROPFILES_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"text_DROPFILES_CB",strlen("text_DROPFILES_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"list_DROPFILES_CB",strlen("list_DROPFILES_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"label_DROPFILES_CB",strlen("label_DROPFILES_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"animatedlabel_DROPFILES_CB",strlen("animatedlabel_DROPFILES_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"tree_DROPFILES_CB",strlen("tree_DROPFILES_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"scintilla_DROPFILES_CB",strlen("scintilla_DROPFILES_CB"),&event_callback);

    // DRAGBEGIN_CB
    ZVAL_PTR(&event_callback,(Icallback) event_elements_dragbegin_cb);
    zend_hash_str_add_new(iup_callback,"dialog_DRAGBEGIN_CB",strlen("dialog_DRAGBEGIN_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"layoutdialog_DRAGBEGIN_CB",strlen("layoutdialog_DRAGBEGIN_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"canvas_DRAGBEGIN_CB",strlen("canvas_DRAGBEGIN_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"flatseparator_DRAGBEGIN_CB",strlen("flatseparator_DRAGBEGIN_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"label_DRAGBEGIN_CB",strlen("label_DRAGBEGIN_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"animatedlabel_DRAGBEGIN_CB",strlen("animatedlabel_DRAGBEGIN_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"text_DRAGBEGIN_CB",strlen("text_DRAGBEGIN_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"list_DRAGBEGIN_CB",strlen("list_DRAGBEGIN_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"tree_DRAGBEGIN_CB",strlen("tree_DRAGBEGIN_CB"),&event_callback);

    // DRAGDATASIZE_CB
    ZVAL_PTR(&event_callback,(Icallback) event_elements_dragdatasize_cb);
    zend_hash_str_add_new(iup_callback,"dialog_DRAGDATASIZE_CB",strlen("dialog_DRAGDATASIZE_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"layoutdialog_DRAGDATASIZE_CB",strlen("layoutdialog_DRAGDATASIZE_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"canvas_DRAGDATASIZE_CB",strlen("canvas_DRAGDATASIZE_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"flatseparator_DRAGDATASIZE_CB",strlen("flatseparator_DRAGDATASIZE_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"label_DRAGDATASIZE_CB",strlen("label_DRAGDATASIZE_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"animatedlabel_DRAGDATASIZE_CB",strlen("animatedlabel_DRAGDATASIZE_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"text_DRAGDATASIZE_CB",strlen("text_DRAGDATASIZE_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"list_DRAGDATASIZE_CB",strlen("list_DRAGDATASIZE_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"tree_DRAGDATASIZE_CB",strlen("tree_DRAGDATASIZE_CB"),&event_callback);

    // DRAGDATA_CB
    ZVAL_PTR(&event_callback,(Icallback) event_elements_dragdata_cb);
    zend_hash_str_add_new(iup_callback,"dialog_DRAGDATA_CB",strlen("dialog_DRAGDATA_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"layoutdialog_DRAGDATA_CB",strlen("layoutdialog_DRAGDATA_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"canvas_DRAGDATA_CB",strlen("canvas_DRAGDATA_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"flatseparator_DRAGDATA_CB",strlen("flatseparator_DRAGDATA_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"label_DRAGDATA_CB",strlen("label_DRAGDATA_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"animatedlabel_DRAGDATA_CB",strlen("animatedlabel_DRAGDATA_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"text_DRAGDATA_CB",strlen("text_DRAGDATA_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"list_DRAGDATA_CB",strlen("list_DRAGDATA_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"tree_DRAGDATA_CB",strlen("tree_DRAGDATA_CB"),&event_callback);

    // DRAGEND_CB
    ZVAL_PTR(&event_callback,(Icallback) event_elements_dragend_cb);
    zend_hash_str_add_new(iup_callback,"dialog_DRAGEND_CB",strlen("dialog_DRAGEND_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"layoutdialog_DRAGEND_CB",strlen("layoutdialog_DRAGEND_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"canvas_DRAGEND_CB",strlen("canvas_DRAGEND_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"flatseparator_DRAGEND_CB",strlen("flatseparator_DRAGEND_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"label_DRAGEND_CB",strlen("label_DRAGEND_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"animatedlabel_DRAGEND_CB",strlen("animatedlabel_DRAGEND_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"text_DRAGEND_CB",strlen("text_DRAGEND_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"list_DRAGEND_CB",strlen("list_DRAGEND_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"tree_DRAGEND_CB",strlen("tree_DRAGEND_CB"),&event_callback);

    // DROPDATA_CB
    ZVAL_PTR(&event_callback,(Icallback) event_elements_dropdata_cb);
    zend_hash_str_add_new(iup_callback,"dialog_DROPDATA_CB",strlen("dialog_DROPDATA_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"layoutdialog_DROPDATA_CB",strlen("layoutdialog_DROPDATA_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"canvas_DROPDATA_CB",strlen("canvas_DROPDATA_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"flatseparator_DROPDATA_CB",strlen("flatseparator_DROPDATA_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"label_DROPDATA_CB",strlen("label_DROPDATA_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"animatedlabel_DROPDATA_CB",strlen("animatedlabel_DROPDATA_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"text_DROPDATA_CB",strlen("text_DROPDATA_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"list_DROPDATA_CB",strlen("list_DROPDATA_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"tree_DROPDATA_CB",strlen("tree_DROPDATA_CB"),&event_callback);

    // DROPMOTION_CB
    ZVAL_PTR(&event_callback,(Icallback) event_elements_dropmotion_cb);
    zend_hash_str_add_new(iup_callback,"dialog_DROPMOTION_CB",strlen("dialog_DROPMOTION_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"layoutdialog_DROPMOTION_CB",strlen("layoutdialog_DROPMOTION_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"canvas_DROPMOTION_CB",strlen("canvas_DROPMOTION_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"flatseparator_DROPMOTION_CB",strlen("flatseparator_DROPMOTION_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"label_DROPMOTION_CB",strlen("label_DROPMOTION_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"animatedlabel_DROPMOTION_CB",strlen("animatedlabel_DROPMOTION_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"text_DROPMOTION_CB",strlen("text_DROPMOTION_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"list_DROPMOTION_CB",strlen("list_DROPMOTION_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"tree_DROPMOTION_CB",strlen("tree_DROPMOTION_CB"),&event_callback);

    // MOVE_CB
    ZVAL_PTR(&event_callback,(Icallback) event_elements_move_cb);
    zend_hash_str_add_new(iup_callback,"dialog_MOVE_CB",strlen("dialog_MOVE_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"layoutdialog_MOVE_CB",strlen("layoutdialog_MOVE_CB"),&event_callback);


    // TRAYCLICK_CB
    ZVAL_PTR(&event_callback,(Icallback) event_elements_trayclick_cb);
    zend_hash_str_add_new(iup_callback,"dialog_TRAYCLICK_CB",strlen("dialog_TRAYCLICK_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"layoutdialog_TRAYCLICK_CB",strlen("layoutdialog_TRAYCLICK_CB"),&event_callback);

    // CARET_CB
    ZVAL_PTR(&event_callback,(Icallback) event_elements_caret_cb);
    zend_hash_str_add_new(iup_callback,"list_CARET_CB",strlen("list_CARET_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"text_CARET_CB",strlen("text_CARET_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"scintilla_CARET_CB",strlen("scintilla_CARET_CB"),&event_callback);

    // SPIN_CB
    ZVAL_PTR(&event_callback,(Icallback) event_elements_spin_cb);
    zend_hash_str_add_new(iup_callback,"text_SPIN_CB",strlen("text_SPIN_CB"),&event_callback);

    // TABCHANGE_CB
    ZVAL_PTR(&event_callback,(Icallback) event_elements_tabchange_cb);
    zend_hash_str_add_new(iup_callback,"tabs_TABCHANGE_CB",strlen("tabs_TABCHANGE_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"flattabs_TABCHANGE_CB",strlen("flattabs_TABCHANGE_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"colorbrowser_TABCHANGE_CB",strlen("colorbrowser_TABCHANGE_CB"),&event_callback);

    // TABCHANGEPOS_CB
    ZVAL_PTR(&event_callback,(Icallback) event_elements_tabchangepos_cb);
    zend_hash_str_add_new(iup_callback,"tabs_TABCHANGEPOS_CB",strlen("tabs_TABCHANGEPOS_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"flattabs_TABCHANGEPOS_CB",strlen("flattabs_TABCHANGEPOS_CB"),&event_callback);

    // TABCLOSE_CB
    ZVAL_PTR(&event_callback,(Icallback) event_elements_tabclose_cb);
    zend_hash_str_add_new(iup_callback,"tabs_TABCLOSE_CB",strlen("tabs_TABCLOSE_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"flattabs_TABCLOSE_CB",strlen("flattabs_TABCLOSE_CB"),&event_callback);

    // RIGHTCLICK_CB
    ZVAL_PTR(&event_callback,(Icallback) event_elements_rightclick_cb);
    zend_hash_str_add_new(iup_callback,"tabs_RIGHTCLICK_CB",strlen("tabs_RIGHTCLICK_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"flattabs_RIGHTCLICK_CB",strlen("flattabs_RIGHTCLICK_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"tree_RIGHTCLICK_CB",strlen("tree_RIGHTCLICK_CB"),&event_callback);

    ZVAL_PTR(&event_callback,(Icallback) event_elements_extrabutton_cb);
    zend_hash_str_add_new(iup_callback,"expander_EXTRABUTTON_CB",strlen("expander_EXTRABUTTON_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"flattabs_EXTRABUTTON_CB",strlen("flattabs_EXTRABUTTON_CB"),&event_callback);

    // ======================================== 独立事件 ========================================

    // Ihandle*  IupExpander   (Ihandle* child);
    ZVAL_PTR(&event_callback,(Icallback) event_elements_openclose_cb);
    zend_hash_str_add_new(iup_callback,"expander_OPENCLOSE_CB",strlen("expander_OPENCLOSE_CB"),&event_callback);

    // Ihandle*  IupDetachBox  (Ihandle* child);
    ZVAL_PTR(&event_callback,(Icallback) event_elements_detached_cb);
    zend_hash_str_add_new(iup_callback,"expander_DETACHED_CB",strlen("expander_DETACHED_CB"),&event_callback);

    ZVAL_PTR(&event_callback,(Icallback) event_elements_restored_cb);
    zend_hash_str_add_new(iup_callback,"expander_RESTORED_CB",strlen("expander_RESTORED_CB"),&event_callback);


    // Ihandle*  IupMenu       (Ihandle* child, ...);
    ZVAL_PTR(&event_callback,(Icallback) event_elements_open_cb);
    zend_hash_str_add_new(iup_callback,"menu_OPEN_CB",strlen("menu_OPEN_CB"),&event_callback);

    ZVAL_PTR(&event_callback,(Icallback) event_elements_menuclose_cb);
    zend_hash_str_add_new(iup_callback,"menu_MENUCLOSE_CB",strlen("menu_MENUCLOSE_CB"),&event_callback);

    // Ihandle*  IupDialog     (Ihandle* child);
    ZVAL_PTR(&event_callback,(Icallback) event_dialog_close_cb);
    zend_hash_str_add_new(iup_callback,"dialog_CLOSE_CB",strlen("dialog_CLOSE_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"layoutdialog_CLOSE_CB",strlen("layoutdialog_CLOSE_CB"),&event_callback);
    
    ZVAL_PTR(&event_callback,(Icallback) event_dialog_copydata_cb);
    zend_hash_str_add_new(iup_callback,"dialog_COPYDATA_CB",strlen("dialog_COPYDATA_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"layoutdialog_COPYDATA_CB",strlen("layoutdialog_COPYDATA_CB"),&event_callback);
    
    ZVAL_PTR(&event_callback,(Icallback) event_dialog_customframe_cb);
    zend_hash_str_add_new(iup_callback,"dialog_CUSTOMFRAME_CB",strlen("dialog_CUSTOMFRAME_CB"),&event_callback);    
    zend_hash_str_add_new(iup_callback,"layoutdialog_CUSTOMFRAME_CB",strlen("layoutdialog_CUSTOMFRAME_CB"),&event_callback);    

    ZVAL_PTR(&event_callback,(Icallback) event_dialog_customframeactivate_cb);
    zend_hash_str_add_new(iup_callback,"dialog_CUSTOMFRAMEACTIVATE_CB",strlen("dialog_CUSTOMFRAMEACTIVATE_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"layoutdialog_CUSTOMFRAMEACTIVATE_CB",strlen("layoutdialog_CUSTOMFRAMEACTIVATE_CB"),&event_callback);

    ZVAL_PTR(&event_callback,(Icallback) event_dialog_mdiactivate_cb);
    zend_hash_str_add_new(iup_callback,"dialog_MDIACTIVATE_CB",strlen("dialog_MDIACTIVATE_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"layoutdialog_MDIACTIVATE_CB",strlen("layoutdialog_MDIACTIVATE_CB"),&event_callback);

    ZVAL_PTR(&event_callback,(Icallback) event_dialog_show_cb);
    zend_hash_str_add_new(iup_callback,"dialog_SHOW_CB",strlen("dialog_SHOW_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"layoutdialog_SHOW_CB",strlen("layoutdialog_SHOW_CB"),&event_callback);


    // Ihandle*  IupList       (const char* action);
    ZVAL_PTR(&event_callback,(Icallback) event_elements_dblclick_cb);
    zend_hash_str_add_new(iup_callback,"list_DBLCLICK_CB",strlen("list_DBLCLICK_CB"),&event_callback);

    ZVAL_PTR(&event_callback,(Icallback) event_elements_edit_cb);
    zend_hash_str_add_new(iup_callback,"list_EDIT_CB",strlen("list_EDIT_CB"),&event_callback);

    ZVAL_PTR(&event_callback,(Icallback) event_elements_multiselect_cb);
    zend_hash_str_add_new(iup_callback,"list_MULTISELECT_CB",strlen("list_MULTISELECT_CB"),&event_callback);

    // Ihandle*  IupTimer      (void);
    ZVAL_PTR(&event_callback,(Icallback) event_elements_action_cb);
    zend_hash_str_add_new(iup_callback,"timer_ACTION_CB",strlen("timer_ACTION_CB"),&event_callback);

    // Ihandle*  IupTree       (void);
    ZVAL_PTR(&event_callback,(Icallback) event_elements_selection_cb);
    zend_hash_str_add_new(iup_callback,"tree_SELECTION_CB",strlen("tree_SELECTION_CB"),&event_callback);

    ZVAL_PTR(&event_callback,(Icallback) event_elements_multiselection_cb);
    zend_hash_str_add_new(iup_callback,"tree_MULTISELECTION_CB",strlen("tree_MULTISELECTION_CB"),&event_callback);

    ZVAL_PTR(&event_callback,(Icallback) event_elements_multiunselection_cb);
    zend_hash_str_add_new(iup_callback,"tree_MULTIUNSELECTION_CB",strlen("tree_MULTIUNSELECTION_CB"),&event_callback);

    ZVAL_PTR(&event_callback,(Icallback) event_elements_branchopen_cb);
    zend_hash_str_add_new(iup_callback,"tree_BRANCHOPEN_CB",strlen("tree_BRANCHOPEN_CB"),&event_callback);

    ZVAL_PTR(&event_callback,(Icallback) event_elements_branchclose_cb);
    zend_hash_str_add_new(iup_callback,"tree_BRANCHCLOSE_CB",strlen("tree_BRANCHCLOSE_CB"),&event_callback);

    ZVAL_PTR(&event_callback,(Icallback) event_elements_executeleaf_cb);
    zend_hash_str_add_new(iup_callback,"tree_EXECUTELEAF_CB",strlen("tree_EXECUTELEAF_CB"),&event_callback);

    ZVAL_PTR(&event_callback,(Icallback) event_elements_showrename_cb);
    zend_hash_str_add_new(iup_callback,"tree_SHOWRENAME_CB",strlen("tree_SHOWRENAME_CB"),&event_callback);

    ZVAL_PTR(&event_callback,(Icallback) event_elements_rename_cb);
    zend_hash_str_add_new(iup_callback,"tree_RENAME_CB",strlen("tree_RENAME_CB"),&event_callback);

    ZVAL_PTR(&event_callback,(Icallback) event_elements_noderemoved_cb);
    zend_hash_str_add_new(iup_callback,"tree_NODEREMOVED_CB",strlen("tree_NODEREMOVED_CB"),&event_callback);

    ZVAL_PTR(&event_callback,(Icallback) event_elements_togglevalue_cb);
    zend_hash_str_add_new(iup_callback,"tree_TOGGLEVALUE_CB",strlen("tree_TOGGLEVALUE_CB"),&event_callback);

    // Ihandle*  IupColorbar   (void);
    ZVAL_PTR(&event_callback,(Icallback) event_elements_togglevalue_cb);
    zend_hash_str_add_new(iup_callback,"colorbar_CELL_CB",strlen("colorbar_CELL_CB"),&event_callback);

    ZVAL_PTR(&event_callback,(Icallback) event_elements_togglevalue_cb);
    zend_hash_str_add_new(iup_callback,"colorbar_EXTENDED_CB",strlen("colorbar_EXTENDED_CB"),&event_callback);

    ZVAL_PTR(&event_callback,(Icallback) event_elements_select_cb);
    zend_hash_str_add_new(iup_callback,"colorbar_SELECT_CB",strlen("colorbar_SELECT_CB"),&event_callback);

    ZVAL_PTR(&event_callback,(Icallback) event_elements_switch_cb);
    zend_hash_str_add_new(iup_callback,"colorbar_SWITCH_CB",strlen("colorbar_SWITCH_CB"),&event_callback);

    // Ihandle*  IupDial       (const char* type);
    ZVAL_PTR(&event_callback,(Icallback) event_elements_button_press_cb);
    zend_hash_str_add_new(iup_callback,"dial_BUTTON_PRESS_CB",strlen("dial_BUTTON_PRESS_CB"),&event_callback);

    ZVAL_PTR(&event_callback,(Icallback) event_elements_button_release_cb);
    zend_hash_str_add_new(iup_callback,"dial_BUTTON_RELEASE_CB",strlen("dial_BUTTON_RELEASE_CB"),&event_callback);

    ZVAL_PTR(&event_callback,(Icallback) event_elements_mousemove_cb);
    zend_hash_str_add_new(iup_callback,"dial_MOUSEMOVE_CB",strlen("dial_MOUSEMOVE_CB"),&event_callback);


    // Ihandle*  IupColorBrowser(void);
    ZVAL_PTR(&event_callback,(Icallback) event_elements_change_cb);
    zend_hash_str_add_new(iup_callback,"colorbrowser_CHANGE_CB",strlen("colorbrowser_CHANGE_CB"),&event_callback);

    ZVAL_PTR(&event_callback,(Icallback) event_elements_drag_cb);
    zend_hash_str_add_new(iup_callback,"colorbrowser_DRAG_CB",strlen("colorbrowser_DRAG_CB"),&event_callback);

    // scintilla
    ZVAL_PTR(&event_callback,(Icallback) event_scintilla_action);
    zend_hash_str_add_new(iup_callback,"scintilla_ACTION",strlen("scintilla_ACTION"),&event_callback);

    ZVAL_PTR(&event_callback,(Icallback) event_scintilla_autocselection_cb);
    zend_hash_str_add_new(iup_callback,"scintilla_AUTOCSELECTION_CB",strlen("scintilla_AUTOCSELECTION_CB"),&event_callback);

    ZVAL_PTR(&event_callback,(Icallback) event_scintilla_autoccancelled_cb);
    zend_hash_str_add_new(iup_callback,"scintilla_AUTOCCANCELLED_CB",strlen("scintilla_AUTOCCANCELLED_CB"),&event_callback);

    ZVAL_PTR(&event_callback,(Icallback) event_scintilla_autocchardeleted_cb);
    zend_hash_str_add_new(iup_callback,"scintilla_AUTOCCHARDELETED_CB",strlen("scintilla_AUTOCCHARDELETED_CB"),&event_callback);

    ZVAL_PTR(&event_callback,(Icallback) event_scintilla_dwell_cb);
    zend_hash_str_add_new(iup_callback,"scintilla_DWELL_CB",strlen("scintilla_DWELL_CB"),&event_callback);

    ZVAL_PTR(&event_callback,(Icallback) event_scintilla_hotspotclick_cb);
    zend_hash_str_add_new(iup_callback,"scintilla_HOTSPOTCLICK_CB",strlen("scintilla_HOTSPOTCLICK_CB"),&event_callback);

    ZVAL_PTR(&event_callback,(Icallback) event_scintilla_lineschanged_cb);
    zend_hash_str_add_new(iup_callback,"scintilla_LINESCHANGED_CB",strlen("scintilla_LINESCHANGED_CB"),&event_callback);

    ZVAL_PTR(&event_callback,(Icallback) event_scintilla_marginclick_cb);
    zend_hash_str_add_new(iup_callback,"scintilla_MARGINCLICK_CB",strlen("scintilla_MARGINCLICK_CB"),&event_callback);

    ZVAL_PTR(&event_callback,(Icallback) event_scintilla_savepoint_cb);
    zend_hash_str_add_new(iup_callback,"scintilla_SAVEPOINT_CB",strlen("scintilla_SAVEPOINT_CB"),&event_callback);

    ZVAL_PTR(&event_callback,(Icallback) event_scintilla_updatecontent_cb);
    zend_hash_str_add_new(iup_callback,"scintilla_UPDATECONTENT_CB",strlen("scintilla_UPDATECONTENT_CB"),&event_callback);

    ZVAL_PTR(&event_callback,(Icallback) event_scintilla_updateselection_cb);
    zend_hash_str_add_new(iup_callback,"scintilla_UPDATESELECTION_CB",strlen("scintilla_UPDATESELECTION_CB"),&event_callback);

    ZVAL_PTR(&event_callback,(Icallback) event_scintilla_updatehscroll_cb);
    zend_hash_str_add_new(iup_callback,"scintilla_UPDATEHSCROLL_CB",strlen("scintilla_UPDATEHSCROLL_CB"),&event_callback);

    ZVAL_PTR(&event_callback,(Icallback) event_scintilla_updatevscroll_cb);
    zend_hash_str_add_new(iup_callback,"scintilla_UPDATEVSCROLL_CB",strlen("scintilla_UPDATEVSCROLL_CB"),&event_callback);

    ZVAL_PTR(&event_callback,(Icallback) event_scintilla_zoom_cb);
    zend_hash_str_add_new(iup_callback,"scintilla_ZOOM_CB",strlen("scintilla_ZOOM_CB"),&event_callback);

    // webbrowser
    ZVAL_PTR(&event_callback,(Icallback) event_webbrowser_completed_cb);
    zend_hash_str_add_new(iup_callback,"webbrowser_COMPLETED_CB",strlen("webbrowser_COMPLETED_CB"),&event_callback);

    ZVAL_PTR(&event_callback,(Icallback) event_webbrowser_error_cb);
    zend_hash_str_add_new(iup_callback,"webbrowser_ERROR_CB",strlen("webbrowser_ERROR_CB"),&event_callback);

    ZVAL_PTR(&event_callback,(Icallback) event_webbrowser_navigate_cb);
    zend_hash_str_add_new(iup_callback,"webbrowser_NAVIGATE_CB",strlen("webbrowser_NAVIGATE_CB"),&event_callback);

    ZVAL_PTR(&event_callback,(Icallback) event_webbrowser_newwindow_cb);
    zend_hash_str_add_new(iup_callback,"webbrowser_NEWWINDOW_CB",strlen("webbrowser_NEWWINDOW_CB"),&event_callback);

    // cells
    ZVAL_PTR(&event_callback,(Icallback) event_cells_draw_cb);
    zend_hash_str_add_new(iup_callback,"cells_DRAW_CB",strlen("cells_DRAW_CB"),&event_callback);

    ZVAL_PTR(&event_callback,(Icallback) event_cells_height_cb);
    zend_hash_str_add_new(iup_callback,"cells_HEIGHT_CB",strlen("cells_HEIGHT_CB"),&event_callback);

    ZVAL_PTR(&event_callback,(Icallback) event_cells_hspan_cb);
    zend_hash_str_add_new(iup_callback,"cells_HSPAN_CB",strlen("cells_HSPAN_CB"),&event_callback);

    ZVAL_PTR(&event_callback,(Icallback) event_cells_mouseclick_cb);
    zend_hash_str_add_new(iup_callback,"cells_MOUSECLICK_CB",strlen("cells_MOUSECLICK_CB"),&event_callback);

    ZVAL_PTR(&event_callback,(Icallback) event_cells_mousemotion_cb);
    zend_hash_str_add_new(iup_callback,"cells_MOUSEMOTION_CB",strlen("cells_MOUSEMOTION_CB"),&event_callback);

    ZVAL_PTR(&event_callback,(Icallback) event_cells_ncols_cb);
    zend_hash_str_add_new(iup_callback,"cells_NCOLS_CB",strlen("cells_NCOLS_CB"),&event_callback);

    ZVAL_PTR(&event_callback,(Icallback) event_cells_nlines_cb);
    zend_hash_str_add_new(iup_callback,"cells_NLINES_CB",strlen("cells_NLINES_CB"),&event_callback);

    ZVAL_PTR(&event_callback,(Icallback) event_cells_scrolling_cb);
    zend_hash_str_add_new(iup_callback,"cells_SCROLLING_CB",strlen("cells_SCROLLING_CB"),&event_callback);

    ZVAL_PTR(&event_callback,(Icallback) event_cells_vspan_cb);
    zend_hash_str_add_new(iup_callback,"cells_VSPAN_CB",strlen("cells_VSPAN_CB"),&event_callback);

    ZVAL_PTR(&event_callback,(Icallback) event_cells_width_cb);
    zend_hash_str_add_new(iup_callback,"cells_WIDTH_CB",strlen("cells_WIDTH_CB"),&event_callback);


    // matrix
    ZVAL_PTR(&event_callback,(Icallback) event_matrix_action_cb);
    zend_hash_str_add_new(iup_callback,"matrix_ACTION_CB",strlen("matrix_ACTION_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"matrixex_ACTION_CB",strlen("matrixex_ACTION_CB"),&event_callback);

    ZVAL_PTR(&event_callback,(Icallback) event_matrix_click_cb);
    zend_hash_str_add_new(iup_callback,"matrix_CLICK_CB",strlen("matrix_CLICK_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"matrixex_CLICK_CB",strlen("matrixex_CLICK_CB"),&event_callback);

    ZVAL_PTR(&event_callback,(Icallback) event_matrix_colresize_cb);
    zend_hash_str_add_new(iup_callback,"matrix_COLRESIZE_CB",strlen("matrix_COLRESIZE_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"matrixex_COLRESIZE_CB",strlen("matrixex_COLRESIZE_CB"),&event_callback);

    ZVAL_PTR(&event_callback,(Icallback) event_matrix_release_cb);
    zend_hash_str_add_new(iup_callback,"matrix_RELEASE_CB",strlen("matrix_RELEASE_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"matrixex_RELEASE_CB",strlen("matrixex_RELEASE_CB"),&event_callback);

    ZVAL_PTR(&event_callback,(Icallback) event_matrix_resizematrix_cb);
    zend_hash_str_add_new(iup_callback,"matrix_RESIZEMATRIX_CB",strlen("matrix_RESIZEMATRIX_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"matrixex_RESIZEMATRIX_CB",strlen("matrixex_RESIZEMATRIX_CB"),&event_callback);

    ZVAL_PTR(&event_callback,(Icallback) event_matrix_mousemove_cb);
    zend_hash_str_add_new(iup_callback,"matrix_MOUSEMOVE_CB",strlen("matrix_MOUSEMOVE_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"matrixex_MOUSEMOVE_CB",strlen("matrixex_MOUSEMOVE_CB"),&event_callback);

    ZVAL_PTR(&event_callback,(Icallback) event_matrix_enteritem_cb);
    zend_hash_str_add_new(iup_callback,"matrix_ENTERITEM_CB",strlen("matrix_ENTERITEM_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"matrixex_ENTERITEM_CB",strlen("matrixex_ENTERITEM_CB"),&event_callback);

    ZVAL_PTR(&event_callback,(Icallback) event_matrix_leaveitem_cb);
    zend_hash_str_add_new(iup_callback,"matrix_LEAVEITEM_CB",strlen("matrix_LEAVEITEM_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"matrixex_LEAVEITEM_CB",strlen("matrixex_LEAVEITEM_CB"),&event_callback);

    ZVAL_PTR(&event_callback,(Icallback) event_matrix_scrolltop_cb);
    zend_hash_str_add_new(iup_callback,"matrix_SCROLLTOP_CB",strlen("matrix_SCROLLTOP_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"matrixex_SCROLLTOP_CB",strlen("matrixex_SCROLLTOP_CB"),&event_callback);

    ZVAL_PTR(&event_callback,(Icallback) event_matrix_bgcolor_cb);
    zend_hash_str_add_new(iup_callback,"matrix_BGCOLOR_CB",strlen("matrix_BGCOLOR_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"matrixex_BGCOLOR_CB",strlen("matrixex_BGCOLOR_CB"),&event_callback);

    ZVAL_PTR(&event_callback,(Icallback) event_matrix_fgcolor_cb);
    zend_hash_str_add_new(iup_callback,"matrix_FGCOLOR_CB",strlen("matrix_FGCOLOR_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"matrixex_FGCOLOR_CB",strlen("matrixex_FGCOLOR_CB"),&event_callback);

    ZVAL_PTR(&event_callback,(Icallback) event_matrix_font_cb);
    zend_hash_str_add_new(iup_callback,"matrix_FONT_CB",strlen("matrix_FONT_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"matrixex_FONT_CB",strlen("matrixex_FONT_CB"),&event_callback);

    ZVAL_PTR(&event_callback,(Icallback) event_matrix_type_cb);
    zend_hash_str_add_new(iup_callback,"matrix_TYPE_CB",strlen("matrix_TYPE_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"matrixex_TYPE_CB",strlen("matrixex_TYPE_CB"),&event_callback);

    ZVAL_PTR(&event_callback,(Icallback) event_matrix_draw_cb);
    zend_hash_str_add_new(iup_callback,"matrix_DRAW_CB",strlen("matrix_DRAW_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"matrixex_DRAW_CB",strlen("matrixex_DRAW_CB"),&event_callback);

    ZVAL_PTR(&event_callback,(Icallback) event_matrix_dropcheck_cb);
    zend_hash_str_add_new(iup_callback,"matrix_DROPCHECK_CB",strlen("matrix_DROPCHECK_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"matrixex_DROPCHECK_CB",strlen("matrixex_DROPCHECK_CB"),&event_callback);

    ZVAL_PTR(&event_callback,(Icallback) event_matrix_translatevalue_cb);
    zend_hash_str_add_new(iup_callback,"matrix_TRANSLATEVALUE_CB",strlen("matrix_TRANSLATEVALUE_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"matrixex_TRANSLATEVALUE_CB",strlen("matrixex_TRANSLATEVALUE_CB"),&event_callback);

    ZVAL_PTR(&event_callback,(Icallback) event_matrix_togglevalue_cb);
    zend_hash_str_add_new(iup_callback,"matrix_TOGGLEVALUE_CB",strlen("matrix_TOGGLEVALUE_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"matrixex_TOGGLEVALUE_CB",strlen("matrixex_TOGGLEVALUE_CB"),&event_callback);

    ZVAL_PTR(&event_callback,(Icallback) event_matrix_valuechanged_cb);
    zend_hash_str_add_new(iup_callback,"matrix_VALUECHANGED_CB",strlen("matrix_VALUECHANGED_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"matrixex_VALUECHANGED_CB",strlen("matrixex_VALUECHANGED_CB"),&event_callback);

    ZVAL_PTR(&event_callback,(Icallback) event_matrix_drop_cb);
    zend_hash_str_add_new(iup_callback,"matrix_DROP_CB",strlen("matrix_DROP_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"matrixex_DROP_CB",strlen("matrixex_DROP_CB"),&event_callback);

    ZVAL_PTR(&event_callback,(Icallback) event_matrix_menudrop_cb);
    zend_hash_str_add_new(iup_callback,"matrix_MENUDROP_CB",strlen("matrix_MENUDROP_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"matrixex_MENUDROP_CB",strlen("matrixex_MENUDROP_CB"),&event_callback);

    ZVAL_PTR(&event_callback,(Icallback) event_matrix_dropselect_cb);
    zend_hash_str_add_new(iup_callback,"matrix_DROPSELECT_CB",strlen("matrix_DROPSELECT_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"matrixex_DROPSELECT_CB",strlen("matrixex_DROPSELECT_CB"),&event_callback);

    ZVAL_PTR(&event_callback,(Icallback) event_matrix_edition_cb);
    zend_hash_str_add_new(iup_callback,"matrix_EDITION_CB",strlen("matrix_EDITION_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"matrixex_EDITION_CB",strlen("matrixex_EDITION_CB"),&event_callback);

    ZVAL_PTR(&event_callback,(Icallback) event_matrix_value_cb);
    zend_hash_str_add_new(iup_callback,"matrix_VALUE_CB",strlen("matrix_VALUE_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"matrixex_VALUE_CB",strlen("matrixex_VALUE_CB"),&event_callback);

    ZVAL_PTR(&event_callback,(Icallback) event_matrix_value_edit_cb);
    zend_hash_str_add_new(iup_callback,"matrix_VALUE_EDIT_CB",strlen("matrix_VALUE_EDIT_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"matrixex_VALUE_EDIT_CB",strlen("matrixex_VALUE_EDIT_CB"),&event_callback);

    ZVAL_PTR(&event_callback,(Icallback) event_matrix_mark_cb);
    zend_hash_str_add_new(iup_callback,"matrix_MARK_CB",strlen("matrix_MARK_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"matrixex_MARK_CB",strlen("matrixex_MARK_CB"),&event_callback);

    ZVAL_PTR(&event_callback,(Icallback) event_matrix_markedit_cb);
    zend_hash_str_add_new(iup_callback,"matrix_MARKEDIT_CB",strlen("matrix_MARKEDIT_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"matrixex_MARKEDIT_CB",strlen("matrixex_MARKEDIT_CB"),&event_callback);

    // matrixex
    ZVAL_PTR(&event_callback,(Icallback) event_matrixex_busy_cb);
    zend_hash_str_add_new(iup_callback,"matrixex_BUSY_CB",strlen("matrixex_BUSY_CB"),&event_callback);

    ZVAL_PTR(&event_callback,(Icallback) event_matrixex_numericgetvalue_cb);
    zend_hash_str_add_new(iup_callback,"matrixex_NUMERICGETVALUE_CB",strlen("matrixex_NUMERICGETVALUE_CB"),&event_callback);

    ZVAL_PTR(&event_callback,(Icallback) event_matrixex_numericsetvalue_cb);
    zend_hash_str_add_new(iup_callback,"matrixex_NUMERICSETVALUE_CB",strlen("matrixex_NUMERICSETVALUE_CB"),&event_callback);

    ZVAL_PTR(&event_callback,(Icallback) event_matrixex_menucontext_cb);
    zend_hash_str_add_new(iup_callback,"matrixex_MENUCONTEXT_CB",strlen("matrixex_MENUCONTEXT_CB"),&event_callback);
    zend_hash_str_add_new(iup_callback,"matrixex_MENUCONTEXTCLOSE_CB",strlen("matrixex_MENUCONTEXTCLOSE_CB"),&event_callback);

    ZVAL_PTR(&event_callback,(Icallback) event_matrixex_pastesize_cb);
    zend_hash_str_add_new(iup_callback,"matrixex_PASTESIZE_CB",strlen("matrixex_PASTESIZE_CB"),&event_callback);

    ZVAL_PTR(&event_callback,(Icallback) event_matrixex_sortcolumncompare_cb);
    zend_hash_str_add_new(iup_callback,"matrixex_SORTCOLUMNCOMPARE_CB",strlen("matrixex_SORTCOLUMNCOMPARE_CB"),&event_callback);


    // matrixlist

    ZVAL_PTR(&event_callback,(Icallback) event_matrixlist_imagevaluechanged_cb);
    zend_hash_str_add_new(iup_callback,"matrixlist_IMAGEVALUECHANGED_CB",strlen("matrixlist_IMAGEVALUECHANGED_CB"),&event_callback);

    ZVAL_PTR(&event_callback,(Icallback) event_matrixlist_listaction_cb);
    zend_hash_str_add_new(iup_callback,"matrixlist_LISTACTION_CB",strlen("matrixlist_LISTACTION_CB"),&event_callback);

    ZVAL_PTR(&event_callback,(Icallback) event_matrixlist_listclick_cb);
    zend_hash_str_add_new(iup_callback,"matrixlist_LISTCLICK_CB",strlen("matrixlist_LISTCLICK_CB"),&event_callback);

    ZVAL_PTR(&event_callback,(Icallback) event_matrixlist_listdraw_cb);
    zend_hash_str_add_new(iup_callback,"matrixlist_LISTDRAW_CB",strlen("matrixlist_LISTDRAW_CB"),&event_callback);

    ZVAL_PTR(&event_callback,(Icallback) event_matrixlist_listedition_cb);
    zend_hash_str_add_new(iup_callback,"matrixlist_LISTEDITION_CB",strlen("matrixlist_LISTEDITION_CB"),&event_callback);

    ZVAL_PTR(&event_callback,(Icallback) event_matrixlist_listinsert_cb);
    zend_hash_str_add_new(iup_callback,"matrixlist_LISTINSERT_CB",strlen("matrixlist_LISTINSERT_CB"),&event_callback);

    ZVAL_PTR(&event_callback,(Icallback) event_matrixlist_listrelease_cb);
    zend_hash_str_add_new(iup_callback,"matrixlist_LISTRELEASE_CB",strlen("matrixlist_LISTRELEASE_CB"),&event_callback);

    ZVAL_PTR(&event_callback,(Icallback) event_matrixlist_listremove_cb);
    zend_hash_str_add_new(iup_callback,"matrixlist_LISTREMOVE_CB",strlen("matrixlist_LISTREMOVE_CB"),&event_callback);


    // plot
    ZVAL_PTR(&event_callback,(Icallback) event_plot_clicksample_cb);
    zend_hash_str_add_new(iup_callback,"plot_CLICKSAMPLE_CB",strlen("plot_CLICKSAMPLE_CB"),&event_callback);

    ZVAL_PTR(&event_callback,(Icallback) event_plot_clicksegment_cb);
    zend_hash_str_add_new(iup_callback,"plot_CLICKSEGMENT_CB",strlen("plot_CLICKSEGMENT_CB"),&event_callback);

    ZVAL_PTR(&event_callback,(Icallback) event_plot_editsample_cb);
    zend_hash_str_add_new(iup_callback,"plot_EDITSAMPLE_CB",strlen("plot_EDITSAMPLE_CB"),&event_callback);

    ZVAL_PTR(&event_callback,(Icallback) event_plot_delete_cb);
    zend_hash_str_add_new(iup_callback,"plot_DELETE_CB",strlen("plot_DELETE_CB"),&event_callback);

    ZVAL_PTR(&event_callback,(Icallback) event_plot_deletebegin_cb);
    zend_hash_str_add_new(iup_callback,"plot_DELETEBEGIN_CB",strlen("plot_DELETEBEGIN_CB"),&event_callback);

    ZVAL_PTR(&event_callback,(Icallback) event_plot_deleteend_cb);
    zend_hash_str_add_new(iup_callback,"plot_DELETEEND_CB",strlen("plot_DELETEEND_CB"),&event_callback);

    ZVAL_PTR(&event_callback,(Icallback) event_plot_drawsample_cb);
    zend_hash_str_add_new(iup_callback,"plot_DRAWSAMPLE_CB",strlen("plot_DRAWSAMPLE_CB"),&event_callback);

    ZVAL_PTR(&event_callback,(Icallback) event_plot_menucontext_cb);
    zend_hash_str_add_new(iup_callback,"plot_MENUCONTEXT_CB",strlen("plot_MENUCONTEXT_CB"),&event_callback);

    ZVAL_PTR(&event_callback,(Icallback) event_plot_menucontextclose_cb);
    zend_hash_str_add_new(iup_callback,"plot_MENUCONTEXTCLOSE_CB",strlen("plot_MENUCONTEXTCLOSE_CB"),&event_callback);

    ZVAL_PTR(&event_callback,(Icallback) event_plot_dspropertieschanged_cb);
    zend_hash_str_add_new(iup_callback,"plot_DSPROPERTIESCHANGED_CB",strlen("plot_DSPROPERTIESCHANGED_CB"),&event_callback);

    ZVAL_PTR(&event_callback,(Icallback) event_plot_propertieschanged_cb);
    zend_hash_str_add_new(iup_callback,"plot_PROPERTIESCHANGED_CB",strlen("plot_PROPERTIESCHANGED_CB"),&event_callback);

    ZVAL_PTR(&event_callback,(Icallback) event_plot_select_cb);
    zend_hash_str_add_new(iup_callback,"plot_SELECT_CB",strlen("plot_SELECT_CB"),&event_callback);

    ZVAL_PTR(&event_callback,(Icallback) event_plot_selectbegin_cb);
    zend_hash_str_add_new(iup_callback,"plot_SELECTBEGIN_CB",strlen("plot_SELECTBEGIN_CB"),&event_callback);

    ZVAL_PTR(&event_callback,(Icallback) event_plot_selectend_cb);
    zend_hash_str_add_new(iup_callback,"plot_SELECTEND_CB",strlen("plot_SELECTEND_CB"),&event_callback);

    ZVAL_PTR(&event_callback,(Icallback) event_plot_plotbutton_cb);
    zend_hash_str_add_new(iup_callback,"plot_PLOTBUTTON_CB",strlen("plot_PLOTBUTTON_CB"),&event_callback);

    ZVAL_PTR(&event_callback,(Icallback) event_plot_plotmotion_cb);
    zend_hash_str_add_new(iup_callback,"plot_PLOTMOTION_CB",strlen("plot_PLOTMOTION_CB"),&event_callback);

    ZVAL_PTR(&event_callback,(Icallback) event_plot_predraw_cb);
    zend_hash_str_add_new(iup_callback,"plot_PREDRAW_CB",strlen("plot_PREDRAW_CB"),&event_callback);

    ZVAL_PTR(&event_callback,(Icallback) event_plot_postdraw_cb);
    zend_hash_str_add_new(iup_callback,"plot_POSTDRAW_CB",strlen("plot_POSTDRAW_CB"),&event_callback);

    ZVAL_PTR(&event_callback,(Icallback) event_thread_thread_cb);
    zend_hash_str_add_new(iup_callback,"thread_THREAD_CB",strlen("thread_THREAD_CB"),&event_callback);

}