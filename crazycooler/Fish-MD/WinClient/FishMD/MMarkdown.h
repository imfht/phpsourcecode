/************************************************************************/
/*
Copyright 2017 wtulip Inc.
License GPL
Author:crazycooler
Description:主界面
*/
/************************************************************************/
#pragma once

#include <QWidget>
#include "ui_MMarkdown.h"
#include "MDocument.h"
#include "utils/MIniFile.h"
#include <QWebEngineView>
#include <QSystemTrayIcon>
#include <QPushButton>
#include <QMenu>
#include <QLabel>
#include "MSpinLabel.h"

#define TYPE_OF_SAVE 1
#define TYPE_OF_SAVE_AND_EXIT 2
#define TYPE_OF_SAVE_AND_NEW 3

class MSyncData;
class MDirTree;
class MWebEngineView;

class MMarkdown : public QWidget
{
	Q_OBJECT

public:
	MMarkdown(QWidget *parent = Q_NULLPTR);
	~MMarkdown();

public:
	/************************************************************************/
	/* 
	返回一个code，来表示 退出 还是 注销
	#define EXIT_CODE 0
	#define SIGNOUT_CODE 1
	*/
	/************************************************************************/
	int code() const { return m_exitCode; }

private slots:
	/************************************************************************/
	/* 
	右上角弹出式菜单的事件函数
	onActionAbout  关于我们
	onSignout	   注销
	*/
	/************************************************************************/
	void onActionAbout();
	void onSignout();

private slots:
	/************************************************************************/
	/* 
	托盘右键菜单的事件函数
	onShowMainWnd	显示主界面
	onCloseMainWnd	退出
	*/
	/************************************************************************/
	void onShowMainWnd();
	void onCloseMainWnd();

	/************************************************************************/
	/* 
	托盘图标的操作事件函数
	在这里主要处理了双击显示界面的功能
	*/
	/************************************************************************/
	void onSystemTrayIconEvent(QSystemTrayIcon::ActivationReason reason);

	/************************************************************************/
	/* 
	事件函数
	从服务器获取数据后被调用
	*/
	/************************************************************************/
	void onAfterGetDataFromServer();

	/************************************************************************/
	/* 
	事件函数
	和服务器数据同步之后调用
	*/
	/************************************************************************/
	void onAfterSetDataToServer();

	/************************************************************************/
	/* 
	事件函数
	开始和服务器同步数据的时候被调用
	*/
	/************************************************************************/
	void onStartSetDataToServer();

	/************************************************************************/
	/*
	事件函数
	doc在完成保存操作后的回调函数
	*/
	/************************************************************************/
	void onAfterSave();

	/************************************************************************/
	/* 
	事件函数
	在和服务器同步数据错误时调用
	*/
	/************************************************************************/
	void onNetworkError(bool b);

private slots:
	/************************************************************************/
	/* 
	主界面右上角的按钮事件函数
	*/
	/************************************************************************/
	void onMenuButtonClick();
	void onMinButtonClick();
	void onMaxButtonClick();
	void onMax2ButtonClick();
	void onCloseButtonClick();


protected:
	/************************************************************************/
	/* 
	处理windows的窗口事件，这样操作会导致QT无法跨平台。
	在这里我们主要完成无边框窗口的缩放，拖动等功能。
	*/
	/************************************************************************/
	virtual bool nativeEvent(const QByteArray &eventType, void *message, long *result);

	/************************************************************************/
	/* 
	监测窗口状态变化事件
	*/
	/************************************************************************/
	virtual void changeEvent(QEvent *event);

	/************************************************************************/
	/* 
	窗口关闭事件
	*/
	/************************************************************************/
	virtual void closeEvent(QCloseEvent *event);

	/************************************************************************/
	/* 
	窗口绘制事件
	*/
	/************************************************************************/
	virtual void paintEvent(QPaintEvent *event);
	
private:
	/************************************************************************/
	/* 
	加载初始化数据
	*/
	/************************************************************************/
	void loadInitData();

	/************************************************************************/
	/* 
	创建程序托盘功能
	*/
	/************************************************************************/
	void createTrayIcon();

	/************************************************************************/
	/* 
	初始化主界面的布局
	*/
	/************************************************************************/
	void initLayout();

private:
	Ui::MMarkdown ui;

	//当前显示的文档
	MDocument m_doc;

	//用来显示markdown的浏览器内核
	MWebEngineView *m_webview;

	//和服务器数据同步
	MSyncData *m_syncData;

	//主界面左侧的树形控件
	MDirTree *m_dirTree;

	//窗口是否要关闭了
	bool m_closeFlag;

	//窗口边框厚度
	int m_nBorder;

	//主界面右上角的按钮
	QPushButton *menuButton;
	QPushButton *minButton;
	QPushButton *maxButton;
	QPushButton *max2Button;
	QPushButton *closeButton;

	//主界面右上角弹出式菜单的menu
	QMenu *m_optionMenu;

	//退出码
	int m_exitCode;

	//数据同步时的动画
	MSpinLabel *m_spinImage;

	//网络异常提示的label
	QLabel *m_networkMsgLabel;
};
