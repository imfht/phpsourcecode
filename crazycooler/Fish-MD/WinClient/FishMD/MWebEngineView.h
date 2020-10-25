/************************************************************************/
/*
Copyright 2017 wtulip Inc.
License GPL
Author:crazycooler
Description:webview的派生，用于设置右键菜单
*/
/************************************************************************/
#pragma once

#include <QWebEngineView>

class MWebEngineView : public QWebEngineView
{
	Q_OBJECT

public:
	MWebEngineView(QWidget *parent);
	~MWebEngineView();

	void contextMenuEvent(QContextMenuEvent *event);

	QMenu *m_menu;
};
