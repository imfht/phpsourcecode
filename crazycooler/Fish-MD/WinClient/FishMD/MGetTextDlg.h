/************************************************************************/
/*
Copyright 2017 wtulip Inc.
License GPL
Author:crazycooler
Description:对话框，用于获取一个字符串
*/
/************************************************************************/
#pragma once
#include "MCommon.h"
#include <QDialog>
#include "ui_MGetTextDlg.h"

class MGetTextDlg : public QDialog
{
	Q_OBJECT

public:
	MGetTextDlg(QWidget *parent = Q_NULLPTR);
	~MGetTextDlg();

public:
	/************************************************************************/
	/* 
	设置提示信息
	*/
	/************************************************************************/
	void setLabelText(const QString &str);

	/************************************************************************/
	/*
	设置输入框中的内容
	*/
	/************************************************************************/
	void setTextValue(const QString &str);

	/************************************************************************/
	/* 
	获取输入框中的内容
	*/
	/************************************************************************/
	QString getTextValue();

private slots:
	void onAcceptClick();
	void onCancelClick();

private:
	Ui::MGetTextDlg ui;
	QString m_textValue;
};
