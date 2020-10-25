# 利用group by获取最新的记录

## 场景一
获取每个用户最新发布的文章信息
``` sql
select * from (select * from articles order by id desc) tb group by userid;
```
