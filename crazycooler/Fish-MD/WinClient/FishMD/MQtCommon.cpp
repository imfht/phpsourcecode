/************************************************************************/
/*
Copyright 2017 wtulip Inc.
License GPL
Author:crazycooler
Description:和QT相关的一些常用功能
*/
/************************************************************************/
#include "MQtCommon.h"
#include <QPushButton>
#include <QDebug>
#include "MGetTextDlg.h"

MQtCommon::MQtCommon()
{
}


MQtCommon::~MQtCommon()
{
}


QString MQtCommon::getText(QWidget *parent, const QString &title, const QString &label,
	QLineEdit::EchoMode mode, const QString &text, bool *ok,
	Qt::WindowFlags flags, Qt::InputMethodHints inputMethodHints)
{
#if 0
	std::auto_ptr<QInputDialog> dialog(new QInputDialog(parent, flags));
	dialog->setWindowTitle(title);
	dialog->setLabelText(label);
	dialog->setTextValue(text);
	dialog->setTextEchoMode(mode);
	dialog->setInputMethodHints(inputMethodHints);

	dialog->setOkButtonText("确定");
	dialog->setCancelButtonText("取消");

	const int ret = dialog->exec();
	if (ok)
		*ok = !!ret;
	if (ret) {
		return dialog->textValue();
	}
	else {
		return QString();
	}
#endif
	std::auto_ptr<MGetTextDlg> dialog(new MGetTextDlg(parent));
	dialog->setWindowTitle(title);
	dialog->setLabelText(label);
	dialog->setTextValue(text);

	const int ret = dialog->exec();
	if (ok)
		*ok = !!ret;
	if (ret) {
		return dialog->getTextValue();
	}
	else {
		return QString();
	}
}

QMessageBox::StandardButton MQtCommon::question(QWidget *parent, const QString &title, const QString &content)
{
	std::auto_ptr<QMessageBox> msgBox(new QMessageBox(parent));
	msgBox->setWindowTitle(title);
	msgBox->setText(content);
	QPushButton *yesBtn = msgBox->addButton("是", QMessageBox::YesRole);
	QPushButton *noBtn = msgBox->addButton("否", QMessageBox::NoRole);
	QPushButton *cancelBtn = msgBox->addButton(QMessageBox::Cancel);
	cancelBtn->setVisible(false);
	msgBox->setEscapeButton(QMessageBox::Cancel);
	msgBox->exec();
	auto r = msgBox->clickedButton();
	if (r == yesBtn)
		return QMessageBox::Yes;
	else if (r == noBtn)
		return QMessageBox::No;
	else
		return QMessageBox::Cancel;
}

bool MQtCommon::StringToJsonArray(const QString &str, QJsonArray &arr, const QString &errMsg)
{
	QJsonParseError errJson;
	QJsonDocument d = QJsonDocument::fromJson(str.toUtf8(), &errJson);
	if (errJson.error != QJsonParseError::NoError)
	{
		qCritical() << errMsg << " ( " << errJson.errorString() << " ) ==> " << str;
		return false;
	}

	if (!d.isArray())
		return false;

	arr = d.array();
	return true;
}

bool MQtCommon::StringToJsonObject(const QString &str, QJsonObject &obj, const QString &errMsg)
{
	QJsonParseError errJson;
	QJsonDocument d = QJsonDocument::fromJson(str.toUtf8(), &errJson);
	if (errJson.error != QJsonParseError::NoError)
	{
		qCritical() << errMsg << " ( " << errJson.errorString() << " ) ==> " << str;
		return false;
	}

	if (!d.isObject())
		return false;

	obj = d.object();
	return true;
}

QString MQtCommon::JsonObjectToString(const QJsonObject &obj)
{
	QJsonDocument d;
	d.setObject(obj);
	return d.toJson(QJsonDocument::Compact);
}