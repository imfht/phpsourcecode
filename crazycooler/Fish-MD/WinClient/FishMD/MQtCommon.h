/************************************************************************/
/*
Copyright 2017 wtulip Inc.
License GPL
Author:crazycooler
Description:和QT相关的一些常用功能
*/
/************************************************************************/

#pragma once
#include "MCommon.h"
#include <QInputDialog>
#include <QMessageBox>

class MQtCommon
{
public:
	MQtCommon();
	~MQtCommon();

public:
	/************************************************************************/
	/* 
	获取字符串对话框，现已废弃
	*/
	/************************************************************************/
	static QString getText(QWidget *parent, const QString &title, const QString &label,
		QLineEdit::EchoMode echo = QLineEdit::Normal,
		const QString &text = QString(), bool *ok = Q_NULLPTR,
		Qt::WindowFlags flags = Qt::WindowFlags(),
		Qt::InputMethodHints inputMethodHints = Qt::ImhNone);

	/************************************************************************/
	/*
	confirm对话框
	*/
	/************************************************************************/
	static QMessageBox::StandardButton question(QWidget *parent, 
		const QString &title, const QString &content);

	/************************************************************************/
	/*
	将字符串转json对象和json对象转字符串
	*/
	/************************************************************************/
	static bool StringToJsonArray(const QString &str, QJsonArray &arr,const QString &errMsg);
	static bool StringToJsonObject(const QString &str, QJsonObject &obj, const QString &errMsg);

	static QString JsonObjectToString(const QJsonObject &obj);
	
};

