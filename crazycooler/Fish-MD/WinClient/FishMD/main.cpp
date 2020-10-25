/************************************************************************/
/*
Copyright 2017 wtulip Inc.
License GPL
Author:crazycooler
Description:main
*/
/************************************************************************/

#include "MCommon.h"
#include <QtWidgets/QApplication>
#include <QFile>
#include <QDebug>
#include <QTranslator>
#include "MHttp.h"
#include "MSingleQMD.h"
#include <QMessageBox>
#include "MSignIn.h"
#include "MMarkdown.h"
#include <windows.h>
#include <QMutex>
#include "MVersion.h"

#pragma comment(lib,"sqlite.lib")

MGlobal *g_global = NULL;
static QFile *g_logFile = NULL;


void outputMessage(QtMsgType type, const QMessageLogContext &context, const QString &msg)
{
	static QMutex mutex;
	mutex.lock();

	QString text;
	switch (type)
	{
	case QtDebugMsg:
#ifdef QT_DEBUG
		text = QString("Debug:");
#else
		mutex.unlock();
		return;
#endif
		break;

	case QtInfoMsg:
		text = QString("Info:");
		break;

	case QtWarningMsg:
		text = QString("Warning:");
		break;

	case QtCriticalMsg:
		text = QString("Critical:");
		break;

	case QtFatalMsg:
		text = QString("Fatal:");
		abort();
	}
	QString message = QString("[%1] %2 %3").arg(QDateTime::currentDateTime().toString("yyyy-MM-dd hh:mm:ss")).arg(text).arg(msg);

	
	QTextStream text_stream(g_global->getLogFile());
	text_stream.setCodec("utf8");
	text_stream << message << endl;
	g_global->getLogFile()->flush();
	
	mutex.unlock();
}

int appLoop(int argc, char *argv[])
{
#ifdef QT_DEBUG
	qputenv("QTWEBENGINE_REMOTE_DEBUGGING", "12345");
#endif
	QApplication a(argc, argv);

	g_global = new MGlobal;
	qInstallMessageHandler(outputMessage);

	qInfo()<<"Fish-MD start";
	qInfo() << "version v" << FISH_MD_VERSION;

	//设置界面样式
	QFile file("res/main.css");
	if (file.open(QFile::ReadOnly))
	{
		QTextStream filetext(&file);
		QString stylesheet = filetext.readAll();
		a.setStyleSheet(stylesheet);
		file.close();
	}
	else
	{
		qCritical() << "can not open res/main.css,the stylesheet of the program";
		return EXIT_CODE;
	}
	

	MSingleQMD sqmd;
	if (!sqmd.init(SINGLE_QMD_SERVER_NAME))
	{
		qInfo() << "the Fish-MD has been opened";
		return EXIT_CODE;
	}

	
	if (!g_global->instance())
	{
		qCritical() << "some global object instance failed";
		return EXIT_CODE;
	}


	MSignIn signIn;
	sqmd.setCurMainWnd(&signIn);
	if (signIn.exec() != QDialog::Accepted)
	{
		return EXIT_CODE;
	}
	

	try
	{
		MMarkdown w;
		sqmd.setCurMainWnd(&w);
		w.show();
		a.exec();
 
		return w.code();
	}
	catch (std::exception& e)
	{
		QMessageBox::critical(NULL, "崩溃", e.what(), QMessageBox::Ok);
		qCritical() << "the program get a crash,with message (" << e.what() << " )";
		return EXIT_CODE;
	}
}

int main(int argc, char *argv[])
{
	int code = appLoop(argc, argv);
	if (g_logFile)
	{
		g_logFile->flush();
		g_logFile->close();
		delete g_logFile;
		g_logFile = NULL;
	}

	if (code == EXIT_CODE)
	{
		qInfo() << "fish-MD close";
		return 0;
	}
	else
	{
		qInfo() << "sign out by current user,the program will restart";
		WinExec(argv[0], SW_HIDE);
	}
		

}


