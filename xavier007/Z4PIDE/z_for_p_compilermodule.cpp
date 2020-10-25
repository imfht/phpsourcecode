/*****
 * 2016-2-17
 *这里用到了C++和批处理的混编，可读性非常差，下一步将BAT从代码中分离出去
 *
 *
 *
 *
 *
 *
 *
 *
 *
******/
#include "z_for_p_compilermodule.h"
#include<QDir>
#include<QDebug>
#include<QFile>
#include<QMessageBox>
#include<QTextStream>
#include<QApplication>
Z_FOR_P_CompilerModule::Z_FOR_P_CompilerModule()
{
    m_process = new QProcess(this);
    connect(m_process, SIGNAL(started()), this, SLOT(m_start_process()));
    connect(m_process, SIGNAL(readyReadStandardOutput()), this, SLOT(m_start_read_output()));
    connect(m_process, SIGNAL(readyReadStandardError()), this, SLOT(m_start_read_err_output()));
    connect(m_process, SIGNAL(finished(int,QProcess::ExitStatus)), this, SLOT(m_finish_process(int, QProcess::ExitStatus)));
}
void Z_FOR_P_CompilerModule::CreateProject(QString path,QString projectname)
{

    qDebug()<<"------------------------------------------------------------------------------------";

    QDir dir;
    QString currentPath=dir.currentPath();
    #ifdef Q_OS_WIN
    currentPath.replace(QString("/"),QString("\\\\"));
    #endif
    currentPath.replace(QString("/"),QString("\\\\"));
    QString PHP=currentPath+QDir::separator()+"php"+QDir::separator()+"php.exe";
    QString compiler=currentPath+QDir::separator()+"zephircompiler"+QDir::separator()+"compiler.php";



    QString sFilePath = currentPath+QDir::separator()+"compiler.bat";

    QFile file(sFilePath);
    if (file.exists()){
        QFile::remove(currentPath+QDir::separator()+"compiler.bat");
    }
    //方式：Append为追加，WriteOnly，ReadOnly
    if (!file.open(QIODevice::WriteOnly|QIODevice::Text)) {
        QMessageBox::critical(NULL, "提示", "无法创建文件,创建工程失败");
        //return -1;
    }
    QTextStream out(&file);
    out<<"cd "+path<<endl;
    out<<PHP+" "+compiler+" init "+projectname<<endl;
    out.flush();
    file.close();


    //QProcess::execute(currentPath+"/compiler.bat");

    m_process->start(currentPath+QDir::separator()+"compiler.bat");

    //m_process->waitForStarted();
    //m_process->waitForReadyRead();
    //m_process->waitForFinished();
    /*
    while(!m_process->waitForFinished())
    {
        QString result=QString::fromLocal8Bit(m_process->readAll());
        qDebug()<<"result:"<<result;
    }*/
    //QFile::remove(currentPath+"/compiler.bat");
}
void Z_FOR_P_CompilerModule::BuildToCode(QString path,QString projectname)
{
    qDebug()<<"------------------------------------------------------------------------------------";

    QDir dir;
    QString currentPath=dir.currentPath();
    #ifdef Q_OS_WIN
    currentPath.replace(QString("/"),QString("\\\\"));
    #endif
    currentPath.replace(QString("/"),QString("\\\\"));
    QString PHP=currentPath+QDir::separator()+"php"+QDir::separator()+"php.exe";
    QString compiler=currentPath+QDir::separator()+"zephircompiler"+QDir::separator()+"compiler.php";



    QString sFilePath = currentPath+QDir::separator()+"build.bat";

    QFile file(sFilePath);
    if (file.exists()){
        QFile::remove(currentPath+QDir::separator()+"build.bat");
    }
    //方式：Append为追加，WriteOnly，ReadOnly
    if (!file.open(QIODevice::WriteOnly|QIODevice::Text)) {
        QMessageBox::critical(NULL, "提示", "无法创建文件,创建工程失败");
        //return -1;
    }
    QTextStream out(&file);
    out<<"cd "+path<<endl;
    out<<PHP+" "+compiler+" generate "+projectname<<endl;
    out.flush();
    file.close();
    m_process->start(currentPath+QDir::separator()+"build.bat");
    //QProcess::execute(currentPath+"/build.bat");
    //QFile::remove(currentPath+"/build.bat");
}
void Z_FOR_P_CompilerModule::Compile_C_Code(QString path,QString projectname)
{
    qDebug()<<"------------------------------------------------------------------------------------";

    QDir dir;
    QString currentPath=dir.currentPath();
    #ifdef Q_OS_WIN
    currentPath.replace(QString("/"),QString("\\\\"));
    #endif

    QString PHP=currentPath+QDir::separator()+"php"+QDir::separator()+"php.exe";
    QString compiler=currentPath+QDir::separator()+"zephircompiler"+QDir::separator()+"compiler.php";



    QString sFilePath = currentPath+QDir::separator()+"compile_c.bat";

    QFile file(sFilePath);
    if (file.exists()){
        QFile::remove(currentPath+QDir::separator()+"compile_c.bat");
    }
    //方式：Append为追加，WriteOnly，ReadOnly
    if (!file.open(QIODevice::WriteOnly|QIODevice::Text)) {
        QMessageBox::critical(NULL, "提示", "无法创建文件,创建工程失败");
        //return -1;
    }
    QTextStream out(&file);
    QString vc9_path=currentPath+QDir::separator()+"tool"+QDir::separator()+"vc9"+QDir::separator();
    QString vc9_bin=vc9_path+"bin";
    QString vc9_include=vc9_path+"include";
    QString vc9_lib=vc9_path+"lib";
    QString PHPSDK_BIN=currentPath+QDir::separator()+"tool"+QDir::separator()+"php-sdk-binary-tools-20110915"+QDir::separator()+"bin";
    QString PHP_SRC=currentPath+QDir::separator()+"tool"+QDir::separator()+"php-5.4.45-src";
    QString WINSDK=currentPath+QDir::separator()+"tool"+QDir::separator()+"winsdkv7.1";
    QString WINDSDK_INCLUDE=WINSDK+QDir::separator()+"include";
    QString WINDSDK_LIB=WINSDK+QDir::separator()+"lib";
    QString WINDSDK_BIN=WINSDK+QDir::separator()+"bin";


    out<<"cd "+PHP_SRC+QDir::separator()+"ext"<<endl;
    out<<"if not exist "+projectname+ " (md "+projectname+")"<<endl;
    out<<"xcopy \""+path+QDir::separator()+projectname+QDir::separator()+"ext"+QDir::separator()+"*\"   \""+PHP_SRC+QDir::separator()+"ext"+QDir::separator()+projectname+QDir::separator()+"\"  /E /F /R /Y "<<endl;


    out<<"set PATH="+vc9_bin+";"+PHPSDK_BIN+";"+WINDSDK_BIN+";%PATH%"<<endl;
    out<<"set INCLUDE="+vc9_include+";"+WINDSDK_INCLUDE+";"<<endl;
    out<<"set LIB="+vc9_lib+";"+WINDSDK_LIB+";"<<endl;
    out<<"cd  "+PHP_SRC<<endl;
    out<<"call buildconf"<<endl;
    out<<"call configure --disable-all --enable-cli --enable-"+projectname+"=shared"<<endl;
    out<<"cd  \""+PHP_SRC+"\""<<endl;
    out<<"nmake"<<endl;
    out.flush();
    file.close();
    m_process->start(currentPath+"/compile_c.bat");
}
void Z_FOR_P_CompilerModule::m_start_process()
{
    qDebug()<<"start process";
    emit start_process();
}
void Z_FOR_P_CompilerModule::m_start_read_output()
{

    emit start_read_output(m_process->readAll());
    //emit start_read_output();
}
void Z_FOR_P_CompilerModule::m_start_read_err_output()
{

    emit start_read_err_output(m_process->errorString());
    //emit start_read_err_output();
}
void Z_FOR_P_CompilerModule::m_finish_process(int i, QProcess::ExitStatus exitStatus)
{
    qDebug()<<"m_finish_process";
    emit finish_process(i, exitStatus);
}
void Z_FOR_P_CompilerModule::run_bat(QString path)
{
    QFile file(path);
    if (!file.exists())
        return ;
    m_process->start(path);
}
void Z_FOR_P_CompilerModule::run_xcopy_to_releasert_dir()
{
    QDir dir;
    QString currentPath=dir.currentPath();
    #ifdef Q_OS_WIN
    currentPath.replace(QString("/"),QString("\\\\"));
    m_process->start(currentPath+QDir::separator()+"copy.bat");
    #endif

}
