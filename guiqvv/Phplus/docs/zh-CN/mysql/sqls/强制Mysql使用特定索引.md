# 强制Mysql使用特定索引

## 场景一
强制Mysql使用特定索引
``` sql
select * from users force index (created) where type=1 and created >= '2016-12-30';
```
