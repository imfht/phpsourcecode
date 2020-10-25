/************************************************************************/
/*
Copyright 2017 wtulip Inc.
License GPL
Author:crazycooler
Description:公用头文件
*/
/************************************************************************/
#pragma once
#pragma execution_character_set("utf-8")

#include "MGlobal.h"
extern MGlobal *g_global;

//刷新时间，单位ms
#define UPDATE_TIME_INTERVAL	10000

//版本信息
#define SINGLE_QMD_SERVER_NAME  "Fish-MD-1.1.0"


#define ITEM_TYPE (256+2)
#define ITEM_ID (256+3)

//item类型，在json结构中对应
#define ITEM_TYPE_FOLDER	1
#define ITEM_TYPE_DOC	2

//注册的URL
#define SIGN_UP_URL "http://mt.wtulip.com/qmd/sign-up.html"

#include <QJsonDocument>
#include <QJsonObject>
#include <QJsonArray>

//网络应答的错误码
#define RESPONSE_NOT_JSON 1000
#define RESPONSE_ERROR_FLAG 1001
#define RESPONSE_AUTH_ERROR 1002

//doc的当前模式
#define EDITOR_MODE_EDIT	1
#define EDITOR_MODE_PREVIEW 2

#ifndef GET_X_LPARAM
#define GET_X_LPARAM(lp)                        ((int)(short)LOWORD(lp))
#endif
#ifndef GET_Y_LPARAM
#define GET_Y_LPARAM(lp)                        ((int)(short)HIWORD(lp))
#endif

#define EXIT_CODE 0
#define SIGNOUT_CODE 1