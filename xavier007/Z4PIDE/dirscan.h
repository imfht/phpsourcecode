#ifndef DIRSCAN_H
#define DIRSCAN_H


#include <QObject>
#include <QFileInfo>

class DirScan : public QObject
{
  Q_OBJECT
public:
  explicit DirScan(QObject *parent = 0);

  void AsncScan(const QString strPath);

signals:
  void ItemScaned(const QString &strRootPath, const QFileInfo &ItemInfo , const int i);

protected slots:
  void Scan(const QString strPath);

private:
  static int k ;

};
#endif // DIRSCAN_H
