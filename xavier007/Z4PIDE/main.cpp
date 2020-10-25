#include "z_for_p_editor.h"
#include <QApplication>
#include <qtextcodec.h>
int main(int argc, char *argv[])
{
    QApplication a(argc, argv);
    QTextCodec::setCodecForLocale(QTextCodec::codecForName("UTF-8"));
    Z_FOR_P_editor w;
    w.show();

    return a.exec();
}
