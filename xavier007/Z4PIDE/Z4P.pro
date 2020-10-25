#-------------------------------------------------
#
# Project created by QtCreator 2016-02-15T11:43:15
#
#-------------------------------------------------

QT       += core gui

greaterThan(QT_MAJOR_VERSION, 4): QT += widgets
CONFIG      += release qscintilla2
TARGET = Z4P
TEMPLATE = app

DEFINES +=  QSCINTILLA_DLL
SOURCES += main.cpp\
        mainwindow.cpp \
    z_for_p_editorwidget.cpp \
    z_for_p_filesystemdockwidget.cpp \
    z_for_p_editor.cpp \
    z_for_p_compilermodule.cpp \
    dirscan.cpp \
    z_for_p_outputtextedit.cpp

HEADERS  += mainwindow.h \
    z_for_p_editorwidget.h \
    z_for_p_filesystemdockwidget.h \
    resourcefilename.h \
    z_for_p_editor.h \
    z_for_p_compilermodule.h \
    dirscan.h \
    z_for_p_outputtextedit.h

FORMS    += mainwindow.ui \
    z_for_p_editor.ui
