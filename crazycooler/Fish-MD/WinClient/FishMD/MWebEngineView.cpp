/************************************************************************/
/*
Copyright 2017 wtulip Inc.
License GPL
Author:crazycooler
Description:webview的派生，用于设置右键菜单
*/
/************************************************************************/
#include "MWebEngineView.h"
#include <QMenu>
#include <QContextMenuEvent>
#include <QWebEngineContextMenuData>

MWebEngineView::MWebEngineView(QWidget *parent)
	: QWebEngineView(parent)
{
	m_menu = NULL;
}

MWebEngineView::~MWebEngineView()
{
}

void MWebEngineView::contextMenuEvent(QContextMenuEvent *event)
{
	//QMenu *menu = page()->createStandardContextMenu();
	const QWebEngineContextMenuData &data = page()->contextMenuData();

	if (m_menu == NULL)
		m_menu = new QMenu(this);
	else
		m_menu->clear();

	bool showFlag = false;
	
	if (data.isContentEditable())
	{
		if (page()->hasSelection())
		{
			m_menu->addAction(page()->action(QWebEnginePage::Cut));
			m_menu->addAction(page()->action(QWebEnginePage::Copy));
			m_menu->addAction(page()->action(QWebEnginePage::Paste));
			m_menu->addSeparator();
			m_menu->addAction(page()->action(QWebEnginePage::Unselect));
		}
		else
		{
			m_menu->addAction(page()->action(QWebEnginePage::Paste));
			m_menu->addSeparator();
			m_menu->addAction(page()->action(QWebEnginePage::SelectAll));
		}
		showFlag = true;
	}
	else
	{
		if (page()->hasSelection())
		{
			m_menu->addAction(page()->action(QWebEnginePage::Copy));
			m_menu->addSeparator();
			m_menu->addAction(page()->action(QWebEnginePage::Unselect));
			showFlag = true;
		}
	}

	if(showFlag)
		m_menu->popup(event->globalPos());
}
