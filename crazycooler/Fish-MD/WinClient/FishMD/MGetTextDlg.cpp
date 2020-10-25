/************************************************************************/
/*
Copyright 2017 wtulip Inc.
License GPL
Author:crazycooler
Description:对话框，用于获取一个字符串
*/
/************************************************************************/
#include "MGetTextDlg.h"

MGetTextDlg::MGetTextDlg(QWidget *parent)
	: QDialog(parent)
{
	ui.setupUi(this);

	connect(ui.AcceptButton,&QPushButton::clicked,this, &MGetTextDlg::onAcceptClick);
	connect(ui.CancelButton, &QPushButton::clicked, this, &MGetTextDlg::onCancelClick);

	setFixedSize(this->width(), this->height());
}

MGetTextDlg::~MGetTextDlg()
{
}


void MGetTextDlg::onAcceptClick()
{
	m_textValue = ui.TextEdit->text();
	accept();
}

void MGetTextDlg::onCancelClick()
{
	reject();
}

void MGetTextDlg::setLabelText(const QString &str)
{
	ui.TipLabel->setText(str);
}

void MGetTextDlg::setTextValue(const QString &str)
{
	ui.TextEdit->setText(str);
}

QString MGetTextDlg::getTextValue()
{
	return m_textValue;
}