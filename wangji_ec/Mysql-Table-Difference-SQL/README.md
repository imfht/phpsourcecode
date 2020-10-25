#Mysql Table Difference SQL 
### 关于
我们在升级某个PHP项目时，往往会对原有的数据库增加表或者字段。本工具可以自动帮助程序员生成新数据库和旧数据库表及表字段差异，生成更新数据库的语句。

### 注意
1. 本工具只生成create table的语句，不生成删除表的语句
2. 本工具只生成alter table 的add column的语句,不生成删除列和修改列的语句
3. 本工具基于ci开发[http://codeigniter.org.cn/]

### 使用
1. 下载ci框架
2. 将本工具的文件放入相应的目录中
3. 运行index.php/tabletool
4. 填入相应信息
![使用](http://git.oschina.net/uploads/images/2015/0615/170307_eeb9c9d5_427916.png "输入数据库信息")
5. 提交
6. 页面右侧将生成相应的语句