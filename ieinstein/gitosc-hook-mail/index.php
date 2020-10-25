<?php
/**
 * Created by PhpStorm.
 * User: cc
 * Date: 2015-03-14
 * Time: 13:48
 */

use Nette\Mail\Message;
use Nette\Mail\SmtpMailer;

// Load Nette Framework
if (@!include __DIR__ . '/mail-2.3.0/vendor/autoload.php') {
    die('Install Nette using `composer update`');
}
/*
 * 写日志
 * */
function writeFile($file,$str,$mode=FILE_APPEND){
    file_put_contents($file, date("[Y-m-d H:i:s]").$str, $mode);
}
/*
 * 获取post过来的数据
 * */
$oscdata = $_POST["hook"];
/*
 * {
    "password": "123",
    "push_data": {
         "before": "d187b143fa1e162df07615a0267a4d3cb664cf54",
         "after": "fb0e4c6f9c60260f9305f70e63a10599590e6791",
         "ref": "refs/heads/master",
         "user_id": 12983,
         "user_name": "莫粒",
         "repository": {
         "name": "JavaTest",
         "url": "git@git.oschina.net:moli/JavaTest.git",
         "description": "",
         "homepage": "http://git.oschina.net/moli/JavaTest"
    },
    "commits": [{
        "id": "fb0e4c6f9c60260f9305f70e63a10599590e6791",
        "message": "12",
        "timestamp": "2014-05-28T11:48:27+08:00",
        "url": "http://git.oschina.net/moli/JavaTest/commit/fb0e4c6f9c60260f9305f70e63a10599590e6791",
        "author": {
            "name": "moli",
            "email": "moli**@qq.com"
        }
    }],
        "total_commits_count": 1
    }
}
 * */
/*
 * 解析为数组
 **/
$dataArr = json_decode($oscdata, true);
$log = "";
if (!$dataArr) {
    writeFile("log.txt", "未解析出OSC发送过来的json数据！");
    exit;
}else {
    ob_start(); var_dump($dataArr); $log=ob_get_clean();
    writeFile("log.txt", "解析出OSC发送的json数据：\n".$log);
}
/*
 * 加载配置文件
 * */
$config = include "config.php";
ob_start(); var_dump($config); $log=ob_get_clean();
writeFile("log.txt", "读取config.php完毕：\n".$log);
/*
 * 验证password
 * */
if ($dataArr["password"] != $config["authpassword"]) {
    writeFile("log.txt", "与OSC设置的password验证失败！");
    exit;
}else {
    writeFile("log.txt", "与OSC设置的password验证成功！");
}
/*
 * 编写邮件体，必须进行异常处理
 **/
try{
    $mail = new Message;
    /*
     * 设置邮件发送者
     * */
    $mail->setFrom($config["account"], $config["name"]);
    /*
     * 邮件接收者，可多人
     * */
    foreach($config["to"] as $recver){
        if (is_string($recver))   $mail->addTo($recver);
        else if(is_array($recver))  $mail->addTo($recver[0], $recver[1]);
    }
    /*
     * 抄送，可多人
     * */
    foreach($config["cc"] as $cc){
        if (is_string($cc))   $mail->addCc($cc);
        else if(is_array($cc))  $mail->addCc($cc[0], $cc[1]);
    }
    /*
     * 邮件主题，默认的构造格式为：
     * [n]commits@[repository.name]
     * */
    $subject = $dataArr["push_data"]["total_commits_count"] . " NEW COMMITS @ " . $dataArr["push_data"]["repository"]["name"];
    $mail->setSubject($subject);
    /*
     * 邮件内容
     * 默认为commits的内容，适当的格式化
     * */
    $body = "";
    foreach($dataArr["push_data"]["commits"] as $item) {
        $body .= "<p>";
        $body .= "<b>Author:</b>".$item["author"]["name"]."<br>";
        $body .= "<b>Author Email:</b>".$item["author"]["email"]."<br>";
        $body .= "<b>Commits:</b>".$item["message"]."<br>";
        $body .= "<b>Timestamp:</b>".$item["timestamp"];
        $body .= "</p>";
        for ($i=0;$i<7;$i++) {
            $body.='<span style="color:#009900;">+</span><span style="color:#E53333;">-</span>';
        }
    }
    $body.="<a href=\"{$dataArr["push_data"]["repository"]["homepage"]}\">{$dataArr["push_data"]["repository"]["homepage"]}</a>";
    $mail->setHtmlBody($body);
    /*
     * 创建Mailer对象
     * */
    $mailer = new SmtpMailer(
        array(
            'host' => $config["host"],
            'username' => $config["account"],
            'password' => $config["accpwd"],
            'secure' => $config["secure"]
        )
    );
    /*
     * 发送
     * */
    $mailer->send($mail);
}catch (Exception $e){
    writeFile("log.txt", $e->getMessage());
    exit;
}

/*
 * 写日志
 */
writeFile("log.txt", "-----邮件发送完毕！------\n");

/*--End--*/

/*
 * 测试多次提交1
 * 测试多次提交2
 * */
