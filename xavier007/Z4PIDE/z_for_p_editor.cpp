#include "z_for_p_editor.h"
#include "ui_z_for_p_editor.h"
#include<QToolBar>
#include<QMdiSubWindow>
#include <qmdisubwindow.h>
#include <qmessagebox.h>
#include <qevent.h>
#include<QMdiSubWindow>
#include <qfiledialog.h>
#include<QDebug>
Z_FOR_P_editor::Z_FOR_P_editor(QWidget *parent) :
    QMainWindow(parent),
    ui(new Ui::Z_FOR_P_editor)
{
    ui->setupUi(this);
    init();
}

Z_FOR_P_editor::~Z_FOR_P_editor()
{
    delete ui;
    delete z4p_cm;
}
void Z_FOR_P_editor::closeEvent(QCloseEvent* event){

    m_mdiArea->closeAllSubWindows();

    if (!m_mdiArea->subWindowList().isEmpty())
        event->ignore();
    else event->accept();
}
template <typename ParentPtr, typename ShortCutType>
void Z_FOR_P_editor::releaseAction(QAction*& pAction, ParentPtr pParent,
    const char* const pTitle, const char* const pIconFileName,
    ShortCutType shortCut, const char* const pStatusTip){

    //产生QAction
    pAction = new QAction(tr(pTitle), pParent);

    //如果有图标 则加图标
    if (pIconFileName != NULL)
        pAction->setIcon(QIcon(pIconFileName));

    //设置快捷键
    pAction->setShortcut(shortCut);

    //设置状态提示
    pAction->setStatusTip(tr(pStatusTip));
}


void Z_FOR_P_editor::init()
{
    //分配多用户界面
    m_mdiArea = new QMdiArea(this);

    //设置中央组件
    setCentralWidget(m_mdiArea);
    //如果窗口发生变化，那么就要更新action 比如 重做按钮，此时可能设置为无效
    connect(m_mdiArea, SIGNAL(subWindowActivated(QMdiSubWindow*)),
        this, SLOT(updateActions()));


    //设置窗口的图标
    setWindowIcon(QIcon(resourceFileName::pWindow));


    //创建所需要的所有组件
    createWidgets();
    //创建菜单栏
    createMenuBar();

    //创建工具栏
    createToolBar();
    //初始化编译器
    initCompile();
}
void Z_FOR_P_editor::initCompile()
{
    //初始化编译器
    z4p_cm=new Z_FOR_P_CompilerModule();
    connect(z4p_cm, SIGNAL(finish_process(int, QProcess::ExitStatus)),
        this, SLOT(compile_finish_process(int, QProcess::ExitStatus)));
    connect(z4p_cm, SIGNAL(start_read_output(QString)),
        this, SLOT(addToOutputDockWidget(QString)));
    connect(z4p_cm, SIGNAL(start_read_err_output(QString)),
        this, SLOT(addToOutputDockWidget(QString)));
    m_build=false;
    m_mode=0;//1为build 2 为run

}
//start slot
void Z_FOR_P_editor::compile_finish_process(int i , QProcess::ExitStatus error)
{
    qDebug()<<i;

    if (m_build)
    {
        m_fileSystemDockWidget = new Z_FOR_P_FileSystemDockWidget(m_cd, this);

        connect(m_fileSystemDockWidget, SIGNAL(removedFile(const QString&)),
            this, SLOT(closeFile(const QString&)));
        connect(m_fileSystemDockWidget, SIGNAL(importedFile(const QString&)),
            this, SLOT(openFile(const QString&)));
        connect(m_fileSystemDockWidget, SIGNAL(renamedFile(const QString&, const QString&)),
            this, SLOT(renameFile(const QString&, const QString&)));
        connect(m_fileSystemDockWidget, SIGNAL(selectedFile(const QString&)),
            this, SLOT(selectFile(const QString&)));
        connect(m_fileSystemDockWidget, SIGNAL(createdFile(const QString&)),
            this, SLOT(openFile(const QString&)));
        connect(m_fileSystemDockWidget, SIGNAL(itemDoubleClickFile(const QString&)),
            this, SLOT(selectFile(const QString&)));

        m_fileSystemDockWidget->setWindowTitle(tr("Solution Explorer"));
        addDockWidget(Qt::LeftDockWidgetArea, m_fileSystemDockWidget);
        m_fileSystemDockWidget->show();
        m_build=false;
    }else{
        switch (m_mode) {
        case 1:{

            break;
        }
        case 2:{
            z4p_cm->run_xcopy_to_releasert_dir();
            m_mode=0;
            break;
        }

        default:
            break;
        }


    }



}
void Z_FOR_P_editor::updateActions()
{
    /*
    Z_FOR_P_EditorWidget* widget = activeZ4PEditerWidget();

    bool hasEditer = (widget != NULL);

    bool hasSelection = (widget != NULL &&
        widget->hasSelectedText());

    m_saveAction->setEnabled(hasEditer);
    m_saveAsAction->setEnabled(hasEditer);

    m_redoAction->setEnabled((hasEditer && widget->isRedoAvailable()));
    m_undoAction->setEnabled((hasEditer && widget->isUndoAvailable()));

    m_cutAction->setEnabled(hasSelection);
    m_copyAction->setEnabled(hasSelection);
    m_pasteAction->setEnabled(hasEditer);

    m_closeAction->setEnabled(hasEditer);
    m_closeAllAction->setEnabled(hasEditer);
    m_tileAction->setEnabled(hasEditer);
    m_cascadeAction->setEnabled(hasEditer);
    m_nextAction->setEnabled(hasEditer);
    m_prevAction->setEnabled(hasEditer);

    if (widget != NULL)
        widget->windowMenuAction()->setChecked(true);*/

}
void Z_FOR_P_editor::openFile(const QString& fileName)
{
    Z_FOR_P_EditorWidget* widget = Z_FOR_P_EditorWidget::openFile(fileName, this);

    if (widget != NULL){
        addZ4PEditerWidget(widget);
    }
}
void Z_FOR_P_editor::closeFile(const QString& fileName)
{
    selectFile(fileName);
    m_mdiArea->closeActiveSubWindow();
}
void Z_FOR_P_editor::renameFile(const QString& oFileName, const QString& nFileName)
{
    Z_FOR_P_EditorWidget* win = qobject_cast<Z_FOR_P_EditorWidget*>(findSubWindowByFileName(oFileName)->widget());
    win->setFileName(nFileName);
}
void Z_FOR_P_editor::selectFile(const QString& fileName)
{
    m_mdiArea->setActiveSubWindow(findSubWindowByFileName(fileName));

}

void Z_FOR_P_editor::createFileSystemDockWidget()
{
    if (!m_cd.isEmpty()) return;

    m_cd = QFileDialog::getExistingDirectory(this, tr("create project"),
        ".",
        QFileDialog::ShowDirsOnly
        | QFileDialog::DontResolveSymlinks);

    if (m_cd.isEmpty())
        return;


    #ifdef Q_OS_WIN
    m_cd.replace(QString("/"),QString("\\\\"));
    #endif
    QString   projectName = QInputDialog::getText(this, "set project's name", "enter name:");
    m_projectName=projectName;
    if (projectName=="")return ;
    QSettings *configIniWrite = new QSettings(m_cd+QDir::separator()+"project.z4p", QSettings::IniFormat);
    configIniWrite->setValue("/PROJECT/name", m_projectName);
    z4p_cm->CreateProject(m_cd,m_projectName);
    m_build=true;
    m_mode=0;
/*
    m_fileSystemDockWidget = new Z_FOR_P_FileSystemDockWidget(m_cd, this);

    connect(m_fileSystemDockWidget, SIGNAL(removedFile(const QString&)),
        this, SLOT(closeFile(const QString&)));
    connect(m_fileSystemDockWidget, SIGNAL(importedFile(const QString&)),
        this, SLOT(openFile(const QString&)));
    connect(m_fileSystemDockWidget, SIGNAL(renamedFile(const QString&, const QString&)),
        this, SLOT(renameFile(const QString&, const QString&)));
    connect(m_fileSystemDockWidget, SIGNAL(selectedFile(const QString&)),
        this, SLOT(selectFile(const QString&)));
    connect(m_fileSystemDockWidget, SIGNAL(createdFile(const QString&)),
        this, SLOT(openFile(const QString&)));
    connect(m_fileSystemDockWidget, SIGNAL(itemDoubleClickFile(const QString&)),
        this, SLOT(openFile(const QString&)));

    m_fileSystemDockWidget->setWindowTitle(tr("Solution Explorer"));
    addDockWidget(Qt::LeftDockWidgetArea, m_fileSystemDockWidget);
    m_fileSystemDockWidget->show();


*/

}
void Z_FOR_P_editor::openFileSystemDockWidget()
{
    if (!m_cd.isEmpty()) return;

    m_cd = QFileDialog::getExistingDirectory(this, tr("create project"),
        ".",
        QFileDialog::ShowDirsOnly
        | QFileDialog::DontResolveSymlinks);

    if (m_cd.isEmpty())
        return;
    #ifdef Q_OS_WIN
    m_cd.replace(QString("/"),QString("\\\\"));
    #endif
    QSettings *configIniWrite = new QSettings(m_cd+QDir::separator()+"project.z4p", QSettings::IniFormat);

    m_projectName=configIniWrite->value("/PROJECT/name","").toString();
    if (m_projectName=="")
    {
        QMessageBox::critical(NULL, "Error", "It is not a project!", QMessageBox::Yes , QMessageBox::Yes);
        m_cd="";
        return ;
    }
    m_fileSystemDockWidget = new Z_FOR_P_FileSystemDockWidget(m_cd, this);

    connect(m_fileSystemDockWidget, SIGNAL(removedFile(const QString&)),
        this, SLOT(closeFile(const QString&)));
    connect(m_fileSystemDockWidget, SIGNAL(importedFile(const QString&)),
        this, SLOT(openFile(const QString&)));
    connect(m_fileSystemDockWidget, SIGNAL(renamedFile(const QString&, const QString&)),
        this, SLOT(renameFile(const QString&, const QString&)));
    connect(m_fileSystemDockWidget, SIGNAL(selectedFile(const QString&)),
        this, SLOT(selectFile(const QString&)));
    connect(m_fileSystemDockWidget, SIGNAL(createdFile(const QString&)),
        this, SLOT(openFile(const QString&)));
    connect(m_fileSystemDockWidget, SIGNAL(itemDoubleClickFile(const QString&)),
        this, SLOT(openFile(const QString&)));




    m_fileSystemDockWidget->setWindowTitle(tr("Solution Explorer"));

    addDockWidget(Qt::LeftDockWidgetArea, m_fileSystemDockWidget);
    m_fileSystemDockWidget->show();



    //addDockWidget(Qt::BottomDockWidgetArea, z4p_output);
    //z4p_output->show();

}
void Z_FOR_P_editor::newFile()
{
    Z_FOR_P_EditorWidget* widget = new Z_FOR_P_EditorWidget(this);

    widget->newFile();

    addZ4PEditerWidget(widget);
}
void Z_FOR_P_editor::open()
{
    Z_FOR_P_EditorWidget* widget = Z_FOR_P_EditorWidget::open(this);

    if (widget != NULL)
        addZ4PEditerWidget(widget);
}
void Z_FOR_P_editor::save()
{
    //获得当前活跃的窗口
    Z_FOR_P_EditorWidget* widget = activeZ4PEditerWidget();

    if (widget != NULL)
        widget->save();
}
void Z_FOR_P_editor::saveAs()
{
    Z_FOR_P_EditorWidget* widget = activeZ4PEditerWidget();

    if (widget != NULL)
        widget->saveAs();
}
void Z_FOR_P_editor::redo()
{

    Z_FOR_P_EditorWidget* widget = activeZ4PEditerWidget();

    if (widget != NULL)
        widget->redo();
}
void Z_FOR_P_editor::undo()
{
    Z_FOR_P_EditorWidget* widget = activeZ4PEditerWidget();

    if (widget != NULL)
        widget->undo();
}
void Z_FOR_P_editor::cut()
{
    Z_FOR_P_EditorWidget* widget = activeZ4PEditerWidget();

    if (widget != NULL)
        widget->cut();
}
void Z_FOR_P_editor::copy()
{
    Z_FOR_P_EditorWidget* widget = activeZ4PEditerWidget();

    if (widget != NULL)
        widget->copy();
}
void Z_FOR_P_editor::paste()
{
    Z_FOR_P_EditorWidget* widget = activeZ4PEditerWidget();

    if (widget != NULL)
        widget->paste();
}
void Z_FOR_P_editor::aboutMe()
{
    QMessageBox::about(this, tr("About Z4P Editor"),
        tr("<h2> Z4P Editer %1 </h2>"
        "<p> Copyright copy 2016 Software Inc."
        "<p>Z4P Editer is a small editer for zephir.QQqun:233847895\n").arg(CE_VER));
}

void Z_FOR_P_editor::setWhitespaceVisibility(bool isVisibility)
{

    //设置空白格可见
    QList<QMdiSubWindow*> wins = m_mdiArea->subWindowList();

    if (isVisibility){
        foreach(QMdiSubWindow *win, wins)
        {
            qobject_cast<Z_FOR_P_EditorWidget*>(win->widget())
                ->setWhitespaceVisibility(QsciScintilla::WsVisible);
        }

    }
    else{
        foreach(QMdiSubWindow *win, wins)
        {
            qobject_cast<Z_FOR_P_EditorWidget*>(win->widget())
                ->setWhitespaceVisibility(QsciScintilla::WsInvisible);
        }
    }
}
void Z_FOR_P_editor::build()
{
    if (m_cd.isEmpty())
        return;
    m_build=false;
    m_mode=1;
    z4p_cm->BuildToCode(m_cd+"/"+m_projectName,m_projectName);
}
void Z_FOR_P_editor::run()
{

    if (m_cd.isEmpty())
        return;
    m_mode=2;
    m_build=false;
    z4p_cm->Compile_C_Code(m_cd,m_projectName);
}
void Z_FOR_P_editor::shutDown()
{

}
void Z_FOR_P_editor::addToOutputDockWidget(QString text)
{
    ui->textEdit->append(text);
    ui->textEdit->moveCursor(QTextCursor::End);
}
//end slot
void Z_FOR_P_editor::createWidgets()
{
    createActionGroups();

    //生成一系列的actions
    createFileActions();
    createEditActions();
    createWindowActions();
    createProjectActions();
    createOptionActions();
    createHelpActions();
}
void Z_FOR_P_editor::createFileActions()
{

    releaseAction(m_newProjectAction, this, "&New Project", resourceFileName::pNew, QKeySequence::New,
        "create a new project");
    connect(m_newProjectAction, SIGNAL(triggered()), this, SLOT(createFileSystemDockWidget()));

    releaseAction(m_openProjectAction, this, "&Open Project", resourceFileName::pNew, QKeySequence::Open,
        "open a  project");
    connect(m_openProjectAction, SIGNAL(triggered()), this, SLOT(openFileSystemDockWidget()));



    releaseAction(m_saveAction, this, "&Save", resourceFileName::pSave, QKeySequence::Save,
        "save the file");
    connect(m_saveAction, SIGNAL(triggered()), this, SLOT(save()));

    releaseAction(m_saveAsAction, this, "Save &As", resourceFileName::pSaveAs, QKeySequence::SaveAs,
        "save as");
    connect(m_saveAsAction, SIGNAL(triggered()), this, SLOT(saveAs()));

    releaseAction(m_exitAction, this, "&Exit", resourceFileName::pExit, QKeySequence::Close,
        "close chan editer");
    connect(m_exitAction, SIGNAL(triggered()), this, SLOT(close()));

}
void Z_FOR_P_editor::createEditActions()
{
    releaseAction(m_undoAction, this, "&Undo", resourceFileName::pUndo, QKeySequence::Undo,
        "undo");
    connect(m_undoAction, SIGNAL(triggered()), this, SLOT(undo()));

    releaseAction(m_redoAction, this, "&Redo", resourceFileName::pRedo, QKeySequence::Redo,
        "redo");
    connect(m_redoAction, SIGNAL(triggered()), this, SLOT(redo()));

    releaseAction(m_cutAction, this, "&Cut", resourceFileName::pCut, QKeySequence::Cut,
        "cut");
    connect(m_cutAction, SIGNAL(triggered()), this, SLOT(cut()));

    releaseAction(m_copyAction, this, "C&opy", resourceFileName::pCopy, QKeySequence::Copy,
        "copy");
    connect(m_copyAction, SIGNAL(triggered()), this, SLOT(copy()));

    releaseAction(m_pasteAction, this, "&Paste", resourceFileName::pPaste, QKeySequence::Paste,
        "paste");
    connect(m_pasteAction, SIGNAL(triggered()), this, SLOT(paste()));


}
void Z_FOR_P_editor::createWindowActions()
{
    releaseAction(m_closeAction, this, "&Close", resourceFileName::pClose, tr(""),
        "close current window");
    connect(m_closeAction, SIGNAL(triggered()), m_mdiArea, SLOT(closeActiveSubWindow()));

    releaseAction(m_closeAllAction, this, "Close &All", resourceFileName::pCloseAll, tr(""),
        "close all the windows");
    connect(m_closeAllAction, SIGNAL(triggered()), m_mdiArea, SLOT(closeAllSubWindows()));

    releaseAction(m_tileAction, this, "&Tile", NULL, tr(""),
        "tile sub windows");
    connect(m_tileAction, SIGNAL(triggered()), m_mdiArea, SLOT(tileSubWindows()));

    releaseAction(m_cascadeAction, this, "Ca&scade", NULL, tr(""),
        "cascade sub windows");
    connect(m_cascadeAction, SIGNAL(triggered()), m_mdiArea, SLOT(cascadeSubWindows()));

    releaseAction(m_nextAction, this, "&Next", resourceFileName::pNext, tr(""),
        "activate next sub window");
    connect(m_nextAction, SIGNAL(triggered()), m_mdiArea, SLOT(activateNextSubWindow()));

    releaseAction(m_prevAction, this, "&Previous", resourceFileName::pPrev, tr(""),
        "ctivate previous sub window");
    connect(m_prevAction, SIGNAL(triggered()), m_mdiArea, SLOT(activatePreviousSubWindow()));

}
void Z_FOR_P_editor::createProjectActions()
{

    releaseAction(m_buildAction, this, "&Build", resourceFileName::pBuild, tr("F7"),
        "build");
    connect(m_buildAction, SIGNAL(triggered()), this, SLOT(build()));
    //connect(m_compilerModule, SIGNAL(finished(bool)), m_buildAction, SLOT(setEnabled(bool)));

    releaseAction(m_runAction, this, "&Run", resourceFileName::pRun, tr("F5"),
        "run");
    connect(m_runAction, SIGNAL(triggered()), this, SLOT(run()));
    //connect(m_runtimeEnvironment, SIGNAL(finished(bool)), m_runAction, SLOT(setEnabled(bool)));

    releaseAction(m_shutDownAction, this, "&Shut Down", resourceFileName::pShutDown, tr("F4"),
        "shut down");
    connect(m_shutDownAction, SIGNAL(triggered()), this, SLOT(shutDown()));
    m_shutDownAction->setEnabled(false);

}
void Z_FOR_P_editor::createOptionActions()
{
    releaseAction(m_wsvAction, this, "&Whitespace Visibility", NULL, tr(""),
        "set whitespace visibility");
    m_wsvAction->setCheckable(true);
    m_wsvAction->setChecked(true);
    connect(m_wsvAction, SIGNAL(toggled(bool)), this, SLOT(setWhitespaceVisibility(bool)));

}
void Z_FOR_P_editor::createHelpActions()
{
    releaseAction(m_aboutMeAction, this, "About &Me", resourceFileName::pAboutMe, tr(""),
        "about chan editer");
    connect(m_aboutMeAction, SIGNAL(triggered()), this, SLOT(aboutMe()));

    releaseAction(m_aboutQtAction, this, "About &Qt", resourceFileName::pAboutQt, tr(""),
        "about qt");
    connect(m_aboutQtAction, SIGNAL(triggered()), qApp, SLOT(aboutQt()));

}
void Z_FOR_P_editor::createActionGroups()
{
    //生成action groups
    m_actionGroup = new QActionGroup(this);
}
void Z_FOR_P_editor::createMenuBar()
{
    createFileMenu();
    createEditMenu();
    createWindowMenu();
    createProjectMenu();
    createOptionMenu();
    createHelpMenu();
}
void Z_FOR_P_editor::createFileMenu()
{
    QMenu* menu = menuBar()->addMenu(tr("&File"));

    menu->addAction(m_newProjectAction);
    menu->addAction(m_openProjectAction);
    menu->addAction(m_saveAction);
    menu->addAction(m_saveAsAction);
    menu->addAction(m_exitAction);
}

void Z_FOR_P_editor::createEditMenu()
{
    QMenu* menu = menuBar()->addMenu(tr("&Edit"));

    menu->addAction(m_undoAction);
    menu->addAction(m_redoAction);
    menu->addAction(m_cutAction);
    menu->addAction(m_copyAction);
    menu->addAction(m_pasteAction);
}
void Z_FOR_P_editor::createWindowMenu()
{
    m_windowMenu = menuBar()->addMenu(tr("&Window"));

    m_windowMenu->addAction(m_closeAction);
    m_windowMenu->addAction(m_closeAllAction);

    //添加分隔
    m_windowMenu->addSeparator();

    m_windowMenu->addAction(m_tileAction);
    m_windowMenu->addAction(m_cascadeAction);

    //添加分隔
    m_windowMenu->addSeparator();

    m_windowMenu->addAction(m_nextAction);
    m_windowMenu->addAction(m_prevAction);

    //添加分隔符
    m_windowMenu->addSeparator();
}
void Z_FOR_P_editor::createProjectMenu()
{

    QMenu* menu = menuBar()->addMenu(tr("&Project"));

    menu->addAction(m_buildAction);
    menu->addAction(m_runAction);
    menu->addAction(m_shutDownAction);
}
void Z_FOR_P_editor::createOptionMenu()
{
    QMenu* menu = menuBar()->addMenu(tr("&Option"));

    menu->addAction(m_wsvAction);
}
void Z_FOR_P_editor::createHelpMenu()
{
    QMenu* menu = menuBar()->addMenu(tr("&Help"));

    menu->addAction(m_aboutMeAction);
    menu->addAction(m_aboutQtAction);
}
void Z_FOR_P_editor::createToolBar()
{
    createFileToolBar();
    createEditToolBar();
    createProjectToolBar();
}
void Z_FOR_P_editor::createFileToolBar()
{
    QToolBar* toolBar = addToolBar(tr("&File"));

    toolBar->addAction(m_saveAction);
    toolBar->addAction(m_saveAsAction);
}
void Z_FOR_P_editor::createEditToolBar()
{
    QToolBar* toolBar = addToolBar(tr("&Edit"));

    toolBar->addAction(m_undoAction);
    toolBar->addAction(m_redoAction);
}
void Z_FOR_P_editor::createProjectToolBar()
{

    QToolBar* toolBar = addToolBar(tr("&Project"));

    toolBar->addAction(m_buildAction);
    toolBar->addAction(m_runAction);
    toolBar->addAction(m_shutDownAction);
}
void Z_FOR_P_editor::addZ4PEditerWidget(Z_FOR_P_EditorWidget *widget)
{
    connect(widget, SIGNAL(undoAvailable(bool)),
        m_undoAction, SLOT(setEnabled(bool)));
    connect(widget, SIGNAL(redoAvailable(bool)),
        m_redoAction, SLOT(setEnabled(bool)));
    connect(widget, SIGNAL(copyAvailable(bool)),
        m_copyAction, SLOT(setEnabled(bool)));
    connect(widget, SIGNAL(copyAvailable(bool)),
        m_cutAction, SLOT(setEnabled(bool)));

    QMdiSubWindow* win = m_mdiArea->addSubWindow(widget);

    m_windowMenu->addAction(widget->windowMenuAction());

    m_actionGroup->addAction(widget->windowMenuAction());

    if (!m_wsvAction->isChecked())
        widget->setWhitespaceVisibility(
        QsciScintilla::WhitespaceVisibility::WsInvisible);

    win->show();
}
Z_FOR_P_EditorWidget* Z_FOR_P_editor::activeZ4PEditerWidget()
{

    QMdiSubWindow* win = m_mdiArea->activeSubWindow();

    return (win != NULL) ?
        qobject_cast<Z_FOR_P_EditorWidget*>(win->widget()) : NULL;

}
QMdiSubWindow* Z_FOR_P_editor::findSubWindowByFileName(const QString& fileName){

    foreach(QMdiSubWindow *window, m_mdiArea->subWindowList())
    {
        Z_FOR_P_EditorWidget *my_mdi = qobject_cast<Z_FOR_P_EditorWidget *>(window->widget());//qobject_cast为进行强制类型转换
        if(my_mdi->fileName() == fileName)//如果已经存在该窗口，则返回。
            return window;
    }
    return NULL;

}
