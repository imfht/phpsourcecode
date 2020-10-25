#ifndef RESOURCEFILENAME_H
#define RESOURCEFILENAME_H


#if defined _MSC_VER && _MSC_VER > 1000
#pragma once
#endif

#if !defined __CHANEDITERRESOURCE__
#define __CHANEDITERRESOURCE__

#define _RESOURCE_BEGIN namespace resourceFileName {
#define _RESOURCE_END	}

#define RESOURCE_BEGIN _RESOURCE_BEGIN
#define RESOURCE_END _RESOURCE_END

#endif


RESOURCE_BEGIN

char* const pOpen = "src/open.png";
char* const pNew = "src/new.png";
char* const pSave = "src/save.png";
char* const pSaveAs = "src/saveAs.png";
char* const pExit = "src/exit.png";
char* const pCut = "src/cut.png";
char* const pPaste = "src/paste.png";
char* const pCopy = "src/copy.png";
char* const pWindow = "src/window.png";
char* const pRedo = "src/redo.png";
char* const pUndo = "src/undo.png";
char* const pAboutMe = "src/aboutMe.png";
char* const pAboutQt = "src/aboutQt.png";
char* const pSplash = "src/background.png";
char* const pClose = "src/close.png";
char* const pCloseAll = "src/closeAll.png";
char* const pNext = "src/next.png";
char* const pPrev = "src/prev.png";
char* const pFolder = "src/folder.png";
char* const pC = "src/c.png";
char* const pCpp = "src/cpp.png";
char* const pH = "src/h.png";
char* const pRun = "src/run.png";
char* const pShutDown = "src/shutDown.png";
char* const pBuild = "src/build.png";

RESOURCE_END
#include <QTextCodec>

inline QString GBK2UTF8(const QString &inStr)
{
    QTextCodec *gbk = QTextCodec::codecForName("GB18030");
    QTextCodec *utf8 = QTextCodec::codecForName("UTF-8");

    QString g2u = gbk->toUnicode(gbk->fromUnicode(inStr));              // gbk  convert utf8
    return g2u;
}

inline QString UTF82GBK(const QString &inStr)
{
    QTextCodec *gbk = QTextCodec::codecForName("GB18030");
    QTextCodec *utf8 = QTextCodec::codecForName("UTF-8");

    QString utf2gbk = gbk->toUnicode(inStr.toLocal8Bit());
    return utf2gbk;
}

inline std::string gbk2utf8(const QString &inStr)
{
    return GBK2UTF8(inStr).toStdString();
}

inline QString utf82gbk(const std::string &inStr)
{
    QString str = QString::fromStdString(inStr);

    return UTF82GBK(str);
}

#endif // RESOURCEFILENAME_H
