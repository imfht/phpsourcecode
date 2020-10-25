#ifndef Z_FOR_P_EDITOR_H
#define Z_FOR_P_EDITOR_H
#include "resourceFileName.h"
#include <QMainWindow>
#include<QMdiArea>
#include<QActionGroup>
#include"z_for_p_filesystemdockwidget.h"
#include"z_for_p_editorwidget.h"
#include "z_for_p_compilermodule.h"
#include<QSettings>
#include<QInputDialog>

namespace Ui {
class Z_FOR_P_editor;
}


class Z_FOR_P_editor : public QMainWindow
{
    Q_OBJECT

public:
    explicit Z_FOR_P_editor(QWidget *parent = 0);
    ~Z_FOR_P_editor();
#ifndef _CHAN_VERSION
#define _CHAN_EDITOR_VERSION 0.1
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

    void initCompile();

    void addZ4PEditerWidget(Z_FOR_P_EditorWidget*);
    Z_FOR_P_EditorWidget* activeZ4PEditerWidget();

    QMdiSubWindow* findSubWindowByFileName(const QString&);
protected:
    void closeEvent(QCloseEvent*);
private slots:
    void openFile(const QString&);
    void closeFile(const QString&);
    void renameFile(const QString&, const QString&);
    void selectFile(const QString&);
    void createFileSystemDockWidget();
    void openFileSystemDockWidget();
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
    void addToOutputDockWidget(QString);
    void compile_finish_process(int, QProcess::ExitStatus);

private:
    Ui::Z_FOR_P_editor *ui;

    QMdiArea* m_mdiArea;
    //file
    QAction* m_newProjectAction;
    QAction* m_openProjectAction;
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

    QMenu* m_windowMenu;

    Z_FOR_P_FileSystemDockWidget* m_fileSystemDockWidget;
    QString m_cd;
    Z_FOR_P_CompilerModule* z4p_cm;
    bool m_build;
    int m_mode;
    QString m_projectName;
};

#endif // Z_FOR_P_EDITOR_H
