系统要求：
PHP版本：5.3或以上版本。
PDO支持：否则无法操作MySQL数据库！

安装步骤：
1、直接用浏览器访问 “根目录/webroot/install.php” 文件进行安装。

Linux对目录权限要求严格，为了安装正确，先将下面几个目录权限设置为：可读可写可执行（chmod 777 目录名）
1、根目录/log          - 日志目录：存放系统打印的Warning日志、SQL语句等日志。
2、根目录/data/runtime - 临时文件：存放用户权限数据、表结构、生成的代码等。
3、根目录/data/u       - 上传目录：用户上传图片存放目录。
4、根目录/cfg/db       - 数据库配置：安装时填写的数据库配置存放在该目录。
5、根目录/cfg/key      - 密钥配置：安装时随机生成的加密密钥、签名密钥存放在该目录。

如果自动安装失败，手动安装过程：
1、请手动将数据库信息写入 “根目录/cfg/db/cluster-sample.php” 文件，并将 “cluster-sample.php” 文件重命名为 “cluster.php”。
2、请手动将 “根目录/data/install/db_tables.sql” 和 “根目录/data/install/db_data.sql” 中的#@__替换成表前缀，并依次将两个文件手动导入数据库。
3、请手动将密钥信息写入 “根目录/cfg/key/cluster-sample.php” 文件，并将 “cluster-sample.php” 文件重命名为 “cluster.php”。
4、再次执行安装操作，这时会跳过数据库配置，直接转到创建管理员操作，输入管理员 “用户名” 和 “密码” 后提交即可。

项目上线注意点：
1、请定期修改 “根目录/cfg/key/cluster.php” 文件里面的加密Key（crypt、sign值）。
2、这些Key用于加解密存于Cookie中的信息，例如用户登录凭证等。
3、线上的加密Key和测试机的不能一样。
