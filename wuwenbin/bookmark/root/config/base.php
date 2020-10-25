<?php
/**
 * 全局配置
 */
$config = array();
// 数据库配置(部署到SAE平台,请修改为SAE系统常量)
$config["db"]["default"] = array(
    // 服务器地址
    "host" => "127.0.0.1",
    // 服务器端口
    "port" => 3306,
    // 数据库用户名
    "user" => "root",
    // 数据库密码
    "pass" => "123456",
    // 数据库名称
    "name" => "bookmark",
);
// 网站地址重写
switch (App::getName()) {
    case "public":
        $config["rewriteRules"] = array();
        break;
    case "mobile":
        $config["rewriteRules"] = array();
        break;
}
// 网站描述
$config["description"] = "也许某天在这里会找到那些尘封的记忆 —— 网址书签";
// 网站关键词
$config["keywords"] = "网址书签,bookmark,网址收藏夹,网址导航,网址大全";
// 每小时添加分类最大数
$config["categoryCountPerHour"] = 200;
// 每小时添加网址最大数
$config["linkCountPerHour"] = 200;
// 随机查看用户最少网址数
$config["randUserMinLinkCount"] = 1;
// 首页底部链接
$config["footLinks"] = array(
    // 链接,名称,是否新窗口打开
    array("mailto:admin@wuwenbin.info", "联系方式", false),
    array("http://f.wuwenbin.info", "官方主页", true),
);

// 模板编译文件存储路径(部署到SAE平台,请取消如下注释)
// $config["templateCompilePath"] = SAE_TMP_PATH;
return $config;
