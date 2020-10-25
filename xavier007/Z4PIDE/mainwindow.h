#ifndef MAINWINDOW_H
#define MAINWINDOW_H


#include <QMainWindow>
#include "resourceFileName.h"
#include<QActionGroup>
#include<QtCore>
#include"ui_mainwindow.h"
class QMdiArea;
class Z_FOR_P_EditorWidget;
class QMenu;
class Z_FOR_P_FileSystemDockWidget;
class QMdiSubWindow;
class QProcess;

//class chanCompilerModule;
//class chanRuntimeEnvironment;

class MainWindow : public QMainWindow
{
    Q_OBJECT

public:
    MainWindow(QWidget *parent = 0);

#ifndef _CHAN_VERSION
#define _CHAN_EDITOR_VERSION 1.2
#define CE_VER _CHAN_EDITOR_VERSION
#endif

    template <typename ParentPtr, typename ShortCutType>
    static void releaseAction(QAction*&, ParentPtr,
        const char* const, const char* const,
        ShortCutType, const char* const);

private:
    void init();
    void createWidgets();

    void createFileActions();
    void createEditActions();
    void createWindowActions();
    void createProjectActions();
    void createOptionActions();
    void createHelpActions();
    void createActionGroups();

    void createMenuBar();
    void createFileMenu();
    void createEditMenu();
    void createWindowMenu();
    void createProjectMenu();
    void createOptionMenu();
    void createHelpMenu();

    void createToolBar();
    void createFileToolBar();
    void createEditToolBar();
    void createProjectToolBar();

    void addChanEditerWidget(Z_FOR_P_EditorWidget*);
    void setOutputDockWidget();
    Z_FOR_P_EditorWidget* activeChanEditerWidget();
    QMdiSubWindow* findSubWindowByFileName(const QString&);

    const QString binaryFileName();
    const QStringList cppOrCFileNames();

private slots:
    void openFile(const QString&);
    void closeFile(const QString&);
    void renameFile(const QString&, const QString&);
    void selectFile(const QString&);
    void createFileSystemDockWidget();
    void updateActions();
    void newFile();
    void open();
    void save();
    void saveAs();
    void redo();
    void undo();
    void cut();
    void copy();
    void paste();
    void aboutMe();
    void setWhitespaceVisibility(bool);
    void build();
    void run();
    void shutDown();
    void addToOutputDockWidget(const QString&);
protected:
    void closeEvent(QCloseEvent*);

private:

    //file
    QAction* m_newProjectAction;
    QAction* m_saveAction;
    QAction* m_saveAsAction;
    QAction* m_exitAction;

    //edit
    QAction* m_redoAction;
    QAction* m_undoAction;
    QAction* m_cutAction;
    QAction* m_copyAction;
    QAction* m_pasteAction;

    //window
    QAction* m_closeAction;
    QAction* m_closeAllAction;
    QAction* m_tileAction;
    QAction* m_cascadeAction;
    QAction* m_nextAction;
    QAction* m_prevAction;

    //option
    //Whitespace Visibility
    QAction* m_wsvAction;

    //project
    QAction* m_buildAction;
    QAction* m_runAction;
    QAction* m_shutDownAction;

    //help
    QAction* m_aboutMeAction;
    QAction* m_aboutQtAction;

    QActionGroup* m_actionGroup;

    QMdiArea* m_mdiArea;
    QMenu* m_windowMenu;

    Z_FOR_P_FileSystemDockWidget* m_fileSystemDockWidget;
    //chanCompilerModule* m_compilerModule;
    //chanRuntimeEnvironment* m_runtimeEnvironment;
    Ui_MainWindow *ui;
    QString m_cd;
};


#endif // MAINWINDOW_H
