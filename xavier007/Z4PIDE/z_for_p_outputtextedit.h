#ifndef Z_FOR_P_OUTPUTTEXTEDIT_H
#define Z_FOR_P_OUTPUTTEXTEDIT_H



#include <qtextedit.h>


/**
*	重实现QTextEdit 使之能够获取特定区域内的文本
*/
class Z_FOR_P_OutputTextEdit :public QTextEdit{

    Q_OBJECT

public:
    Z_FOR_P_OutputTextEdit(QWidget* = NULL);

protected:
    void keyPressEvent(QKeyEvent*);

public slots:
    void storeCurrentCursor(){
        m_prevCursor = textCursor();
    }

signals:
    void inputedString(const QString&);

private:
    QTextCursor m_prevCursor;
};

#endif // Z_FOR_P_OUTPUTTEXTEDIT_H
