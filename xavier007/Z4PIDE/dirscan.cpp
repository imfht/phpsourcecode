#include "dirscan.h"


#include <QtCore>

int DirScan::k = 0 ;

DirScan::DirScan(QObject *parent) :
  QObject(parent)
{
}

void DirScan::AsncScan(const QString strPath)
{
  QtConcurrent::run(this, &DirScan::Scan, strPath);
}

void DirScan::Scan(const QString strPath)
{
  //! 第一次肯定是显示磁盘驱动器
  if(k == 0 && !QString::compare(strPath , QString(tr("我的电脑")))){
    QFileInfoList drivers = QDir::drives();
    int q = 0 ;
    do{
      QFileInfo d = drivers.at(q++);

      qDebug("-----sendBack:%s\n" , d.filePath().toLatin1().data());
      emit ItemScaned(strPath , d , k);
    }while(q < drivers.size());
    k++ ;

  //! 其它就是盘符下面的内容
  }else{
      QDir dir(strPath);
      if(dir.exists())
      {
        k ++ ;
        QFileInfoList fileList = dir.entryInfoList();
        int i = 0 ;
        do{
          QFileInfo file = fileList.at(i++);

          emit ItemScaned(strPath, file , k);


        }while(i < fileList.size());

      }
  }

  return ;
}

