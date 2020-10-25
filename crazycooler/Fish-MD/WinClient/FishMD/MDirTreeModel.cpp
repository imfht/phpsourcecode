/************************************************************************/
/*
Copyright 2017 wtulip Inc.
License GPL
Author:crazycooler
Description:界面左侧的树形目录控件的model，在这里做派生主要实现item的拖拽功能
*/
/************************************************************************/
#include "MDirTreeModel.h"

MDirTreeModel::MDirTreeModel(QObject *parent)
	: QStandardItemModel(parent)
{
}

MDirTreeModel::~MDirTreeModel()
{
}


//用来设置item是否可以被拖拽
Qt::ItemFlags MDirTreeModel::flags(const QModelIndex &index) const
{
	if (!index.isValid())
		return 0;

	int type = data(index,ITEM_TYPE).value<int>();

	return Qt::ItemIsDragEnabled | Qt::ItemIsDropEnabled | QAbstractItemModel::flags(index);
}


bool MDirTreeModel::dropMimeData(const QMimeData *data, Qt::DropAction action, int row, int column, const QModelIndex &parent) 
{

	if (!canDropMimeData(data, action, row, column, parent))
		return false;

	if (action == Qt::IgnoreAction)
		return true;

	emit itemDragMoved(data,action,row,column,parent);

	return QStandardItemModel::dropMimeData(data, action, row, column, parent);
}


//当前Item是否可以接收拖拽拖来的item
bool MDirTreeModel::canDropMimeData(const QMimeData *data, Qt::DropAction action, int row, int column, const QModelIndex &parent) const
{
	int type = parent.data(ITEM_TYPE).value<int>();
	if (type == ITEM_TYPE_DOC)
		return false;

	return QStandardItemModel::canDropMimeData(data, action, row, column, parent);
}