/************************************************************************/
/*
Copyright 2017 wtulip Inc.
License GPL
Author:crazycooler
Description:和服务器数据同步
*/
/************************************************************************/
#include "MSyncData.h"
#include "MSqldb.h"
#include "MHttp.h"
#include <QMessageBox>
#include <boost/lexical_cast.hpp>
#include <QTimerEvent>
using namespace std;

MSyncData::MSyncData(QObject *parent)
	: QObject(parent)
{
	m_name = g_global->getUserName();
	m_nTimerId = startTimer(UPDATE_TIME_INTERVAL);
	m_delaySyncCount = 1;
}

MSyncData::~MSyncData()
{
	if (m_nTimerId != 0)
	{
		killTimer(m_nTimerId);
	}
}

void MSyncData::getDataCallback(int status, const MResponseData &data)
{
	if (data.status == 0)
	{
		const QJsonObject &root = data.root;
		QJsonArray docs = root["docs"].toArray();
		QJsonObject dir = root["dir"].toObject();

		int lastVersion = 0;

		MDocdb *docDB = g_global->getDocDB();
		for (int i = 0; i < docs.size(); i++)
		{
			QJsonObject doc = docs[i].toObject();
			string docId = doc["id"].toString().toUtf8().data();
			string content = doc["content"].toString().toUtf8().data();
			int version = doc["version"].toInt();

			MDocData docData;
			docData.id = docId;
			docData.src_data = content;
			docData.cur_data = content;
			docData.version = version;
			docData.change_flag = 0;
			docData.create_flag = 0;
			//需要再做拷贝一份的处理


			docDB->setDoc(docData);

			lastVersion = max(version, lastVersion);
		}

		if (dir["version"].isDouble())
		{
			string content = dir["content"].toString().toUtf8().data();
			int version = dir["version"].toInt();

			MDirDB *dirDB = g_global->getDirDB();
			MDirData dirData;
			
			dirData.id = m_name.toUtf8().data();
			dirData.src_data = content;
			dirData.cur_data = content;
			dirData.version = version;
			dirData.change_flag = 0;
			//在此需要一个路径合并功能

			dirDB->setDir(dirData);
		}

		MKVdb *kvDB = g_global->getKVDB();
		stringstream ss;
		ss << lastVersion;
		kvDB->set("lastVersion",ss.str());
	}

	emit afterGetDataFromServer();
}

void MSyncData::getDataFromServer()
{
	MKVdb *kvDB = g_global->getKVDB();
	int lastVersion = boost::lexical_cast<int>(kvDB->getWithDefault("lastVersion","0"));

	MDirDB *dirDB = g_global->getDirDB();
	MDirData dir;
	memset(&dir, 0, sizeof(MDirData));
	dirDB->getDir(m_name.toUtf8().data(), dir);

	QJsonObject param;
	param["lastVersion"] = lastVersion;
	param["dirVersion"] = dir.version;

	MHttp *http = g_global->getHttp();
	http->post(
		"/get-data",
		param,
		std::bind(&MSyncData::getDataCallback, this, std::placeholders::_1, std::placeholders::_2),
		true);
}

int MSyncData::__getChangeDocsToJson(QJsonArray &docs)
{
	MDocdb *docDB = g_global->getDocDB();
	vector<MDocData> docsData;

	docDB->getChangeDocs(docsData);

	for (int i = 0; i < docsData.size(); i++)
	{
		if (docsData[i].change_flag == 1 && docsData[i].src_data == docsData[i].cur_data)
		{
			docDB->resetChangeFlag(docsData[i].id);
		}
		else
		{
			QJsonObject doc;
			doc["id"] = QString::fromUtf8(docsData[i].id.c_str());
			doc["content"] = QString::fromUtf8(docsData[i].cur_data.c_str());
			doc["version"] = docsData[i].version;
			doc["create_flag"] = docsData[i].create_flag;
			docs.append(doc);
		}
	}

	return docs.size();
}

bool MSyncData::__getChangeDirToJson(QJsonObject &dir)
{
	MDirDB *dirDB = g_global->getDirDB();
	if (!dirDB->isChange(m_name.toUtf8().data()))
		return false;

	MDirData dirData;
	dirDB->getDir(m_name.toUtf8().data(), dirData);

	if (dirData.src_data == dirData.cur_data)
	{
		dirDB->resetChangeFlag(m_name.toUtf8().data());
		return false;
	}
	dir["content"] = QString::fromUtf8(dirData.cur_data.c_str());
	dir["version"] = dirData.version;
	return true;
}

bool MSyncData::setDataToServer()
{
	QJsonArray docs;
	QJsonObject dir;
	int changeDocNum = __getChangeDocsToJson(docs);
	bool isDirChange = __getChangeDirToJson(dir);

	if (changeDocNum > 0 || isDirChange)
	{
		emit startSetDataToServer();

		QJsonObject param;
		if(changeDocNum > 0)
			param["docs"] = docs;
		if (isDirChange)
			param["dir"] = dir;

		MHttp *http = g_global->getHttp();
		http->post(
			"/set-data",
			param,
			std::bind(&MSyncData::setDataCallback, this, std::placeholders::_1, std::placeholders::_2),
			true);
		return true;
	}
	else
	{
		return false;
	}
}

void MSyncData::setDataCallback(int status, const MResponseData &data)
{
	if (data.status == 0)
	{
		QJsonParseError error;
		QJsonDocument d = QJsonDocument::fromJson(data.request->data, &error);
		if (error.error != QJsonParseError::NoError)
			return;
		QJsonObject reqJson = d.object();

		int version = data.root["version"].toInt();

		//在此会出现一个问题，就是 发送服务器----服务器应答 在这个时间间隔中，修改了一个已经提交的文档
		//下面就会把已经提交的文档回退到 发送服务器 这个时间点。
		if (reqJson["docs"].isArray())
		{
			QJsonArray docs = reqJson["docs"].toArray();
			MDocdb *docDB = g_global->getDocDB();
			for (auto it = docs.begin(); it != docs.end(); ++it)
			{
				QJsonObject doc = it->toObject();
				QString docId = doc["id"].toString();
				QString content = doc["content"].toString();
				int create_flag = doc["create_flag"].toInt();
				
				docDB->updateDocAfterUpload(docId.toUtf8().data(), content.toUtf8().data(), version);
			}

			docDB->refreshChangeFlag();
		}

		if (reqJson["dir"].isObject())
		{
			QJsonObject dir = reqJson["dir"].toObject();
			MDirDB *dirDB = g_global->getDirDB();

			QString content = dir["content"].toString();
			dirDB->updateDocAfterUpload(m_name.toUtf8().data(), content.toUtf8().data(), version);
			dirDB->refreshChangeFlag(m_name.toUtf8().data());
		}
		
		m_delaySyncCount = 1;
		emit networkError(false);
	}
	else
	{
		if (m_delaySyncCount < 64)
		{
			m_delaySyncCount *= 2;
		}
		qCritical() << "[MSyncData::setDataCallback] sync data failed";
		//QMessageBox::warning(NULL, "错误", "数据同步失败", QMessageBox::Ok);
		emit networkError(true);
	}

	emit afterSetDataToServer();
}

void MSyncData::timerEvent(QTimerEvent *event)
{
	if (m_nTimerId == event->timerId())
	{
		static int count = 1;
		if (count < m_delaySyncCount)
		{
			count++;
		}
		else
		{
			count = 1;
			setDataToServer();
		}
		
	}
}