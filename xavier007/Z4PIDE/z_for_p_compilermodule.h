#ifndef Z_FOR_P_COMPILERMODULE_H
#define Z_FOR_P_COMPILERMODULE_H

#include <qstring.h>
#include <qprocess.h>
#include <qobject.h>
#include<QProcess>
class Z_FOR_P_CompilerModule:public QObject{
    Q_OBJECT
public:
    Z_FOR_P_CompilerModule();
    void CreateProject(QString path,QString projectname);
    void BuildToCode(QString path,QString projectname);
    void Compile_C_Code(QString path,QString projectname);
    void run_bat(QString path);
    void run_xcopy_to_releasert_dir();
private:
    QProcess* m_process;
private slots:
    void m_start_process();
    void m_start_read_output();
    void m_start_read_err_output();
    void m_finish_process(int, QProcess::ExitStatus);
signals:
    void start_process();
    //void start_read_output();
    //void start_read_err_output();
    void finish_process(int, QProcess::ExitStatus);
    void start_read_output(QString);
    void start_read_err_output(QString);
};

#endif // Z_FOR_P_COMPILERMODULE_H
