/************************************************************************/
/*
Copyright 2017 wtulip Inc.
License GPL
Author:crazycooler
Description:关于我们对话框
*/
/************************************************************************/

#include "MAboutDlg.h"
#include "MVersion.h"

MAboutDlg::MAboutDlg(QWidget *parent)
	: QDialog(parent)
{
	ui.setupUi(this);

	ui.label->setText(
		"<h3>关于我们</h3>\
	 	Fish-MD是一个markdown的文档云笔记，和常规的<br>\
 	markdown笔记相比，增加了图片的粘贴和数据云端<br>\
 	同步功能。目前只提供windows版本\
 	<h3>作者</h3>crazycooler\
 	<h3>联系方式</h3>crazycooler@qq.com\
 	<h3>版本</h3>" + QString(FISH_MD_VERSION) + "<br>"
	);
}

MAboutDlg::~MAboutDlg()
{
}
