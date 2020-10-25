/************************************************************************/
/*
Copyright 2017 wtulip Inc.
License GPL
Author:crazycooler
Description:用于和Markdown对应web的交互功能，这个是c++和javascript两种语言交互的桥梁
*/
/************************************************************************/
#include "MDocument.h"
#include "MHttp.h"
#include "MStringTrans.h"
#include <QTimerEvent>
#include <QClipboard>
#include <QMimeData>
#include <QApplication>
#include <QPixmap>
#include <QDir>
#include <ctime>
#include "utils/md5.h"
#include <QMessageBox>
#include "MSqldb.h"
#include <vector>
#include <QDesktopServices>
#include "MQtCommon.h"

using namespace std;

MDocument::MDocument(QObject *parent)
	: QObject(parent)
{
	m_nTimerId = startTimer(UPDATE_TIME_INTERVAL);
	m_editorMode = EDITOR_MODE_PREVIEW;
}

MDocument::~MDocument()
{
	if (m_nTimerId != 0)
	{
		killTimer(m_nTimerId);
	}
}

void MDocument::getValue(const QString &mback)
{
	emit doActionEmit("getvalue","" ,mback);
}

void MDocument::setValue(const QString &value)
{
	emit doActionEmit("setvalue",value);
}

void MDocument::undo()
{
	emit doActionEmit("undo","");
}

void MDocument::redo()
{
	emit doActionEmit("redo", "");
}

void MDocument::preview()
{
	emit doActionEmit("preview", "");
}

void MDocument::showToolbar(bool isShow)
{
	if (isShow)
		emit doActionEmit("showToolbar", "");
	else
		emit doActionEmit("hideToolbar", "");
}

void MDocument::changeMode(int mode)
{
	if (mode == EDITOR_MODE_EDIT)
		emit doActionEmit("changeMode", "edit");
	else
		emit doActionEmit("changeMode", "preview");

}


void MDocument::onGetValue(const QString &value, const QString &mback)
{
	if (mback.isEmpty())
		return;

	QJsonObject root;

	if (!MQtCommon::StringToJsonObject(mback, root, "[MDocument::onGetValue] mback json parse fail "))
		return;

	if (root["for"].toString() == "update" &&  value != m_srcDocData)
	{
		QString docId = root["docId"].toString();
		__updateDocToLocal(docId, value);
	}

	//获得value之后的操作
	QString then = root["then"].toString();

	if (then == "loadDoc")
	{
		bool isEditMode = root["isEditMode"].toBool();
		QString newDocId = root["newDocId"].toString();
		__loadDoc(newDocId, isEditMode);
	}
	else if (then == "loadNewDoc")
	{
		QString newDocId = root["newDocId"].toString();
		QString content = root["content"].toString();
		__loadNewDoc(newDocId, content);
	}
}

void MDocument::__updateDocToLocal(const QString &id, const QString &content)
{
	MDocdb *docDB = g_global->getDocDB();
	docDB->updateDocLocal(id.toUtf8().data(), content.toUtf8().data());
	m_srcDocData = content;
}

void MDocument::onChangeMode(const QString &mode)
{
	m_editorMode = mode == "edit" ? EDITOR_MODE_EDIT : EDITOR_MODE_PREVIEW;
}

void MDocument::onEventNotify(const QString &action, const QString &value, const QString &mback)
{
	if (action == "getvalue")
	{
		onGetValue(value,mback);
		emit afterSave();
	}
	else if (action == "getmode")
	{

	}
	else if (action == "onload")
	{

	}
	else if (action == "pasteImage")
	{
		pasteImage();
	}
	else if (action == "finish")
	{
		if (value != m_srcDocData)
		{
			__updateDocToLocal(m_curDocId,value);
		}
	}
	else if (action == "changeMode")
	{
		onChangeMode(value);
	}
	else if (action == "openWeb")
	{
		QDesktopServices::openUrl(QUrl(value.toLatin1()));
	}
}

//加载文档
void MDocument::loadDoc(const QString& id,bool isEditMode)
{
	m_wantEditorMode = isEditMode ? EDITOR_MODE_EDIT : EDITOR_MODE_PREVIEW;
	if (m_curDocId == id)
	{
		if(m_wantEditorMode != m_editorMode)
			changeMode(m_wantEditorMode);
		return;
	}
		
	if (!m_curDocId.isEmpty())
	{
		QJsonObject mback;
		mback["docId"] = m_curDocId;
		mback["for"] = "update";

		mback["then"] = "loadDoc";
		mback["isEditMode"] = isEditMode;
		mback["newDocId"] = id;

		QJsonDocument d;
		d.setObject(mback);
		
		getValue(d.toJson(QJsonDocument::Compact));
	}
	else
	{
		__loadDoc(id, isEditMode);
	}
}

void MDocument::__loadDoc(const QString& id, bool isEditMode)
{
	MDocdb *docDB = g_global->getDocDB();
	MDocData doc;
	if (docDB->getDoc(id.toUtf8().data(), doc))
	{
		m_curDocId = id;
		m_srcDocData = QString::fromUtf8(doc.cur_data.c_str());
		QString content = QString::fromUtf8(doc.cur_data.c_str());
		if(m_wantEditorMode != m_editorMode)
			changeMode(m_wantEditorMode);
		setValue(content);
	}
}

void MDocument::loadNewDoc(const QString& id, const QString &content)
{
	if (!m_curDocId.isEmpty())
	{
		QJsonObject mback;
		mback["docId"] = m_curDocId;
		mback["for"] = "update";

		mback["then"] = "loadNewDoc";
		mback["newDocId"] = id;
		mback["content"] = content;
		QJsonDocument d;
		d.setObject(mback);

		getValue(d.toJson(QJsonDocument::Compact));
	}
	else
	{
		__loadNewDoc(id,content);
	}
}


//加载新创建的doc
void MDocument::__loadNewDoc(const QString &id, const QString &content)
{
	m_wantEditorMode = EDITOR_MODE_EDIT;
	m_curDocId = id;
	m_srcDocData = content;
	if(m_wantEditorMode != m_editorMode)
		changeMode(m_wantEditorMode);
	setValue(content);
}

void MDocument::timerEvent(QTimerEvent *event)
{
	if (event->timerId() == m_nTimerId)
	{
		save();
	}
}


void MDocument::pasteImageCallback(int status, const MResponseData &data)
{
	if(data.status == 0)
	{
		const QJsonObject &root = data.root;
		QString content = root["imageUrl"].toString();
		emit doActionEmit("addImage", content);
	}
	else
	{
		qCritical() << "[MDocument::pasteImageCallback] paste Image fail ( " << data.data << " )";
	}
}

void MDocument::pasteImage()
{
	const QClipboard *clipboard = QApplication::clipboard();
	const QMimeData *mimeData = clipboard->mimeData();

	if (mimeData->hasImage())
	{
		QPixmap img = qvariant_cast<QPixmap>(mimeData->imageData());
		QString tmpPath = QDir::tempPath();
		int t = time(0);
		QString tmpImagePath = tmpPath+"/qmd_image_" + QString::number(t) + ".jpg";
		img.save(tmpImagePath);

		QFile  file(tmpImagePath);
		if (!file.open(QIODevice::ReadOnly))
			return;

		QByteArray buffer = file.readAll();

		file.close();
		file.remove();

		MHttp *http = g_global->getHttp();
		http->postImage(
			"/upload-image",
			buffer,
			std::bind(&MDocument::pasteImageCallback, this, std::placeholders::_1, std::placeholders::_2),
			false);
	}
}

void MDocument::save()
{
	if (m_curDocId.size())
	{
		QJsonObject mback;
		mback["docId"] = m_curDocId;
		mback["for"] = "update";
		QJsonDocument d;
		d.setObject(mback);
		getValue(d.toJson(QJsonDocument::Compact));
	}
	else
	{
		emit afterSave();
	}
}