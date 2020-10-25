/************************************************************************/
/*
Copyright 2017 wtulip Inc.
License GPL
Author:crazycooler
Description:简单的来说就是一个全局对象，也可以认为是全局容器
*/
/************************************************************************/
#include "MGlobal.h"
#include <QMessageBox>
#include <QDir>
#include <QApplication>
#include "utils/MIniFile.h"
#include "MStringTrans.h"
#include "MHttp.h"
#include "MSqldb.h"

MGlobal::MGlobal()
{
	m_ini = NULL;
	m_http = NULL;
	m_db = NULL;
	m_logFile = NULL;
}


MGlobal::~MGlobal()
{
	uninstance();
}

void MGlobal::uninstance()
{
	if (m_ini)
	{
		delete m_ini;
		m_ini = NULL;
	}

	if (m_http)
	{
		delete m_http;
		m_http = NULL;
	}


	if (m_db)
	{
		delete m_db;
		m_db = NULL;
	}

	if (m_logFile)
	{
		m_logFile->close();
		delete m_logFile;
		m_logFile = NULL;
	}
}


bool MGlobal::instance()
{
	m_workPath = checkWorkPath();
	if (m_workPath.isEmpty())
	{
		QMessageBox::critical(NULL, "程序启动", "缺少启动时的必要文件", QMessageBox::Ok);
		qCritical() << "[MGlobal::instance] can not find some necessary files in work path";
		return false;
	}

	if (!loadIni(m_workPath))
	{
		QMessageBox::critical(NULL, "程序启动", "无效的配置文件", QMessageBox::Ok);
		qCritical() << "[MGlobal::instance] invalid ini config file,which load failed";
		return false;
	}

	m_db = new MSqldb;
	if (!m_db->instance(qstr2str(m_workPath)))
	{
		QMessageBox::critical(NULL, "程序启动", "启动数据库失败", QMessageBox::Ok);
		qCritical() << "[MGlobal::instance] startup local database failed";
		return false;
	}

	return true;
}

bool MGlobal::loadIni(const QString &workPath)
{
	if (m_ini || workPath.isEmpty())
		return false;
	m_ini = new MIniFile;
	QString iniPath = workPath + INI_FILE_REL_PATH;
	if (!m_ini->load(qstr2str(iniPath)))
	{
		delete m_ini;
		m_ini = NULL;
		return false;
	}

	return true;
}

QString MGlobal::checkWorkPath()
{
	QString appPath = QCoreApplication::applicationDirPath();
	QString iniPath = appPath + INI_FILE_REL_PATH;
	QString webPath = appPath + WEB_FILE_REL_PATH;

	if (!QFile::exists(iniPath) || !QFile::exists(webPath))
	{
		QString currentPath = QDir::currentPath();
		iniPath = currentPath + INI_FILE_REL_PATH;
		webPath = currentPath + WEB_FILE_REL_PATH;

		if (!QFile::exists(iniPath) || !QFile::exists(webPath))
		{
			return "";
		}

		return currentPath;
	}

	return appPath;
}

const QString &MGlobal::getWorkPath()
{
	return m_workPath;
}

MIniFile *MGlobal::getIniFile()
{
	return m_ini;
}

MHttp *MGlobal::getHttp()
{
	if (!m_http)
		m_http = new MHttp;
	return m_http;
}


MKVdb *MGlobal::getKVDB()
{
	return m_db->getKVDB();
}

MDocdb *MGlobal::getDocDB()
{
	return m_db->getDocDB();
}

MDirDB *MGlobal::getDirDB()
{
	return m_db->getDirDB();
}


QString MGlobal::getUserName()
{
	return m_userName;
}

void MGlobal::setUserName(const QString &name)
{
	m_userName = name;
}

QFile *MGlobal::getLogFile()
{
	if (!m_logFile)
	{
		QString fileName = "log/" + QDateTime::currentDateTime().toString("yyyy-MM-dd") + "-log.txt";
		m_logFile = new QFile(fileName);
		m_logFile->open(QIODevice::WriteOnly | QIODevice::Append);
	}
	return m_logFile;
	
}