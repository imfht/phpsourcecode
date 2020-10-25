/************************************************************************/
/*
Copyright 2017 wtulip Inc.
License GPL
Author:crazycooler
Description:界面左侧的树形目录控件的model，在这里做派生主要实现item的拖拽功能
*/
/************************************************************************/
#pragma once
#include "MCommon.h"
#include <QStandardItemModel>

class MDirTreeModel : public QStandardItemModel
{
	Q_OBJECT

public:
	MDirTreeModel(QObject *parent = nullptr);
	~MDirTreeModel();

protected:
	/************************************************************************/
	/* 
	用来设置item是否可以被拖拽
	*/
	/************************************************************************/
	virtual Qt::ItemFlags flags(const QModelIndex &index) const;

	/************************************************************************/
	/* 
	用于判断和发送，拖拽成功后的信号itemDragMoved
	*/
	/************************************************************************/
	virtual bool dropMimeData(const QMimeData *data, Qt::DropAction action, int row, int column, const QModelIndex &parent);

	/************************************************************************/
	/* 
	当前Item是否可以接收拖拽拖来的item
	例如：doc的item是不能接受child的
	*/
	/************************************************************************/
	virtual bool canDropMimeData(const QMimeData *data, Qt::DropAction action, int row, int column, const QModelIndex &parent) const;

signals:
	void itemDragMoved(const QMimeData *data, Qt::DropAction action, int row, int column, const QModelIndex &parent);
};
