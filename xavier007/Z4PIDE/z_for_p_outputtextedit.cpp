#include "z_for_p_outputtextedit.h"

#include <qevent.h>
#include <exception>
#include <qmessagebox.h>//***********************

Z_FOR_P_OutputTextEdit::Z_FOR_P_OutputTextEdit(QWidget* parent)
:QTextEdit(parent)
{
    m_prevCursor = textCursor();
}


void Z_FOR_P_OutputTextEdit::keyPressEvent(QKeyEvent* event){

    if ((event->key() == Qt::Key_Return) ||
        (event->key() == Qt::Key_Enter)){

        QTextCursor cursor = textCursor();

        cursor.setPosition(m_prevCursor.position());

        if (!cursor.movePosition(QTextCursor::MoveOperation::NoMove,
            QTextCursor::MoveMode::KeepAnchor,
            cursor.position() - m_prevCursor.position()
            )){

            QTextEdit::keyPressEvent(event);
            return;
        }

        cursor.select(QTextCursor::SelectionType::WordUnderCursor);

#if defined _DEBUG
        QString str = cursor.selectedText();
#endif

        emit inputedString(cursor.selectedText());

        storeCurrentCursor();
    }

    QTextEdit::keyPressEvent(event);
}



