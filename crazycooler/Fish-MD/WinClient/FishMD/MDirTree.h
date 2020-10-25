/************************************************************************/
/* 
Copyright 2017 wtulip Inc.
License GPL
Author:crazycooler
Description:界面左侧的树形目录控件
*/
/************************************************************************/

#pragma once
#include "MCommon.h"
#include <QWidget>
#include "ui_MDirTree.h"
#include <QTreeView>
#include <QMenu>
#include <QStandardItemModel>
#include <QMouseEvent>
#include "MDirTreeModel.h"

class MResponseData;
class MDocument;

class MDirTree : public QWidget
{
	Q_OBJECT

public:
	MDirTree(QWidget *parent,MDocument *doc);
	~MDirTree();
	/************************************************************************/
	/* 
	设置dir数据到控件中
	@param [QString] strData 为json格式的数据
	 例如：
		[
		 {"id":"KBYFWikA","name":"md-name1","type":2},
		 {"name":"dir-name1","type":1,"children":[{"id":"Oy3gWSkA","name":"md-name2","type":2}]}
		]
	 type:1 表示为目录，2 表示为文档
	@return 是否设置成功
	*/
	/************************************************************************/
	bool setData(const QString& strData);

	/************************************************************************/
	/* 
	树形目录转换为json，也就是setData的逆向操作
	*/
	/************************************************************************/
	QString dirTreeToJson();

	/************************************************************************/
	/* 
	保存修改的dir数据（目前只在程序退出时调用）
	*/
	/************************************************************************/
	void save();

private slots:
	/************************************************************************/
	/* 
	显示菜单，按照doc和folder两种不同方式进行显示
	对应的signal QTreeView::customContextMenuRequested
	*/
	/************************************************************************/
	void treeViewMenu(const QPoint& pos);


	/************************************************************************/
	/*
	以下为菜单所绑定的一些基本操作
	创建doc
	创建folder
	重命名
	删除
	浏览doc
	编辑doc
	*/
	/************************************************************************/
	void newDoc();
	void newDir();
	void rename();
	void del();

	void viewDoc();
	void editDoc();

	/************************************************************************/
	/* 
	双击控件中节点时触发
	*/
	/************************************************************************/
	void treeDoubleClicked(const QModelIndex &index);

	/************************************************************************/
	/* 
	folder或者doc被拖动完成后触发，用来标记当前的目录数据是否有改变
	*/
	/************************************************************************/
	void itemDragMoved(const QMimeData *data, Qt::DropAction action, int row, int column, const QModelIndex &parent);


protected:
	/************************************************************************/
	/* 
	创建树形控件中的Menu，包括doc和folder两种
	*/
	/************************************************************************/
	void createTreeMenu();

	/************************************************************************/
	/* 
	生成文档ID
	*/
	/************************************************************************/
	QString makeDirId();

	/************************************************************************/
	/* 
	获得folder下的所有doc的id
	*/
	/************************************************************************/
	void getChildDocIds(QModelIndex parent, std::vector<std::string> &data);

	/************************************************************************/
	/* 
	将json中item用递归的方式添加到QTreeView中
	setData中被调用
	*/
	/************************************************************************/
	void addNode(QJsonArray &arr, QStandardItem *parent);

	/************************************************************************/
	/* 
	将QTreeView中item用递归的方式转换为json
	dirTreeToJson中被调用
	*/
	/************************************************************************/
	void childToJson(QJsonArray &json, QModelIndex &index);

protected:
	/************************************************************************/
	/* 
	定时器，用来做定时缓存
	*/
	/************************************************************************/
	virtual void timerEvent(QTimerEvent *event);

	

private:
	Ui::QtDirTree ui;

	//树形控件
	MDirTreeModel *m_model;
	QTreeView *m_docsTree;

	//doc和folder的menu
	QMenu *m_floderMenu;
	QMenu *m_docMenu;

	//被选中的item
	QModelIndex m_menuSelItem;

	//当前正在显示的doc
	MDocument *m_doc;
	
	//时间ID
	int m_nTimerId;

	//标志位，用来记录目录数据是否被修改
	bool m_changeFlag;
};
