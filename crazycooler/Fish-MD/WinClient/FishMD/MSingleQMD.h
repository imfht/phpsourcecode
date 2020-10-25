/************************************************************************/
/*
Copyright 2017 wtulip Inc.
License GPL
Author:crazycooler
Description:只允许一个Fish-MD进程，如果已经有Fish-MD进程，则会显示已打开的Fish-MD，用QLocalServer的方式来实现
*/
/************************************************************************/
#pragma once

#include <QObject>
#include <QLocalServer>
#include <QLocalSocket>


class MSingleQMD : public QObject
{
	Q_OBJECT

public:
	MSingleQMD(QObject *parent = nullptr);
	~MSingleQMD();

	/************************************************************************/
	/* 
	初始化QLocalServer，如果已经有QLocalServer启动了，则会返回false，否则返回true
	*/
	/************************************************************************/
	bool init(const QString &serverName);

	/************************************************************************/
	/* 
	设置当前的主界面
	*/
	/************************************************************************/
	void setCurMainWnd(QWidget *curMainWnd) { m_curMainWnd = curMainWnd; }

private slots:
	/************************************************************************/
	/*
	时间函数
	如果有新的QLocalServer客户端来连接的时候被调用，用于显示出主界面
	*/
	/************************************************************************/
	void newConnection();

private:
	/************************************************************************/
	/* 
	判断QLocalServer是否已经被打开
	*/
	/************************************************************************/
	bool isServerRun(const QString &serverName);

private:
	QLocalServer *m_server;
	QWidget *m_curMainWnd;
};
