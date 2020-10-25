注：该版本的安装引导只匹配thinkphp5.0程序，如果你有兴趣可以修改该引导程序，以匹配更多的程序。

vip-admin install-thinkphp5.0-v1.0.0		更新时间：2017-03-18

目录结构：
css             css文件夹
frame           前端框架文件夹
xxx*n.html      安装过程引导页面
index.php       主要处理程序文件
vip_admin.sql   主要构件sql文件，这里放置你的基础sql

程序手册：
1.将该引导程序【install】文件夹完全复制到【thinkphp5.0/public】文件夹下，和static文件夹同级。
2.在thinkphp5.0的入口文件(index.php)中添加以下代码,添加位置： [// 定义应用目录] 之前即可

// 检测是否是新安装
if(file_exists("install") && !file_exists("install/install.lock")){
    // 组装安装url
    $url = $_SERVER['HTTP_HOST'].trim($_SERVER['SCRIPT_NAME'],'index.php').'install/index.php';
    // 使用http://域名方式访问；避免./public/install 路径方式的兼容性和其他出错问题
    header("Location:http://$url");die;
}

3.安装前请先根据index.php文件中的config配置配来置你自己的相关信息
// 配置页面
$config = [
    'version'       => 'thinkphp5.0-v1.0.5',    // 版本

    'indexPage'     => 'a_welcome',             // 首页
    'checkPage'     => 'b_check',               // 检查页
    'createPage'    => 'c_create',              // 数据库数据页
    'endPage'       => 'd_success',             // 安装页
    'errorPage'     => 'error',                 // 错误页

    'sqlName'       => 'vip_admin',             // 安装的sql文件名，同该文件同级的sql文件名相同
    'tableName'     => 'vip_',                  // 安转的sql文件中的表名前缀，该项是被用户输入替换的
    'databaseUrl'   => '../../application/database.php',    // database.php安装地址

    'account'       => 'root',                  // 安装成功后的 登录账号
    'password'      => '123456'                 // 安装成功后的 登录密码
];
4.该安装引导程序使用layui进行样式布局，如果你的程序已经引用了layui前端框架，你大可把本引导程序的layui删除，从而引入你的layui样式，以减少程序容量。

配置完成过后即可访问你的 http://localhost/你的项目名/public 进行程序安装引导了。



附：如果想重新进入该安装导流程，删除【install】文件夹内【install.lock】文件即可。

