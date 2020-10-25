<?php
include './library/inc.php';

if ($act == 'feedback_post') {
  $arr['f_name'] = !empty($_POST['name']) ? str_safe($_POST['name']) : '';
  $arr['f_content'] = !empty($_POST['message']) ? str_safe($_POST['message']) : '';
  foreach ($arr as $val) {
    if ($val == '') alert_back('请核对信息后重试!');
  }
  $arr['f_tel'] = !empty($_POST['tel']) ? str_safe($_POST['tel']) : '';
  $arr['f_title'] = !empty($_POST['subject']) ? str_safe($_POST['subject']) : '';
  $arr['f_email'] = !empty($_POST['email']) ? str_safe($_POST['email']) : '';

  if ($db->autoExecute("feedback", $arr, 'INSERT')) {
    alert_back('您的信息已提交, 谢谢您对我们的支持!');
  } else {
    alert_back('请核对信息后重试!');
  }
}

elseif ($act == 'feedback_post_mail') {
  $arr['f_name'] = !empty($_POST['name']) ? str_safe($_POST['name']) : '';
  $arr['f_content'] = !empty($_POST['message']) ? str_safe($_POST['message']) : '';
  foreach ($arr as $val) {
    if ($val == '') alert_back('请核对信息后重试!');
  }
  $arr['f_tel'] = !empty($_POST['tel']) ? str_safe($_POST['tel']) : '';
  $arr['f_title'] = !empty($_POST['subject']) ? str_safe($_POST['subject']) : '';
  $arr['f_email'] = !empty($_POST['email']) ? str_safe($_POST['email']) : '';

  $mail_subject = '[' . $cms['s_name'] . '] 留言邮件';
  $mail_body = '姓名:' . $arr['f_name'] . '<br>';
  $mail_body .= '电话:' . $arr['f_tel'] . '<br>';
  $mail_body .= '电邮:' . $arr['f_email'] . '<br>';
  $mail_body .= '主题:' . $arr['f_title'] . '<br>';
  $mail_body .= '内容:' . $arr['f_content'] . '<br>';
  if (smtp_mail(SMTP_RECIEVER, $mail_subject, $mail_body)) {
    alert_back('您的信息已提交,谢谢您对我们的支持!');
  } else {
    alert_back('发送失败请稍后重试!');
  }
}

// subscribe post
elseif ($act == 'subscribe') {
  $sub_mail = str_safe($_POST['sub_mail']);
  if (check($sub_mail, 'email')) {
    $arr['sub_name'] = str_safe($_POST['sub_name']);
    $arr['sub_sex'] = str_safe($_POST['sub_sex']);
    $arr['sub_mail'] = $sub_mail;
    $arr['sub_addr'] = str_safe($_POST['sub_addr']);
    $arr['sub_post'] = str_safe($_POST['sub_post']);
    $arr['sub_date'] = gmtime();
    if ($db->getRow("SELECT * FROM subscribe WHERE sub_mail = '$sub_mail'")) {
      alert_back($_lang['reg']['email_existing']);
    } else {
      $db->autoExecute("subscribe", $arr, 'INSERT');
      alert_back('您已加入邮件订阅!');
    }
  } else {
    alert_back($_lang['reg']['email_error']);
  }
}

elseif ($act == 'mail') {
  $size = 10000;
  $count = $db->getOne("SELECT COUNT(*) FROM subscribe");
  if ($count) {
    if (mk_dir(SUBSCRIBE_DIR) === false) {
      alert_back($lang['mkdir_error']);
    }
    for($i=0;$i<$count;$i+=$size){
      $res = $db->getAll("SELECT * FROM subscribe ORDER BY id ASC LIMIT $i,$size");
      foreach ($res as $key => $value) {
        if ($key==0) {
          $str = $value['sub_name'] . "," . $value['sub_sex'] . "," . $value['sub_mail'] . "," . $value['sub_addr'] . "," . $value['sub_post'] . "," . local_date('Y-m-d', $value['sub_date']);
        }else{
          $str .= PHP_EOL . $value['sub_name'] . "," . $value['sub_sex'] . "," . $value['sub_mail'] . "," . $value['sub_addr'] . "," . $value['sub_post'] . "," . local_date('Y-m-d', $value['sub_date']);
        }
      }
      $fp = fopen("subscribe_$i.txt", "w+");
      fwrite($fp, $str);
      fclose($fp);
    }
    alert_back($_lang['msg_success']);
  } else {
    alert_back($_lang['msg_failed']);
  }
}

elseif ($act == 'rewrite_apache') {
  $str = "RewriteEngine On" . PHP_EOL . "#主域名指向www二级域名" . PHP_EOL . "#RewriteCond %{HTTP_HOST} ^domain.com [NC]" . PHP_EOL . "#RewriteRule ^(.*)$ http://www.domain.com/$1 [L,R=301]" . PHP_EOL . "RewriteBase /" . PHP_EOL . "RewriteRule ^index\.html$ index.php" . PHP_EOL . "RewriteRule ^channel-([0-9]+)\.html$ index.php?m=channel&id=$1" . PHP_EOL . "RewriteRule ^channel-([0-9]+)-([0-9]+)\.html$ index.php?m=channel&id=$1&page=$2" . PHP_EOL . "RewriteRule ^detail-([0-9]+)\.html$ index.php?m=detail&id=$1" . PHP_EOL . "RewriteRule ^search\.html$ index.php?m=search" . PHP_EOL . "RewriteRule ^search-([0-9]+)\.html$ index.php?m=search&page=$1";
  $fp = fopen(".htaccess", "w+");
  fwrite($fp, $str);
  fclose($fp);
  alert_back($GLOBALS['lang']['msg_success']);
}

elseif ($act == 'rewrite_nginx') {
    $str = "location / {" . PHP_EOL . "rewrite /index\.html$ /index.php;" . PHP_EOL . "rewrite ^/channel-([0-9]+)\.html$ /index.php?m=channel&id=$1;" . PHP_EOL . "rewrite ^/channel-([0-9]+)-([0-9]+)\.html$ /index.php?m=channel&id=$1&page=$2;" . PHP_EOL . "rewrite ^/detail-([0-9]+)\.html$ /index.php?m=detail&id=$1;" . PHP_EOL . "rewrite ^/search\.html$ /index.php?m=search;" . PHP_EOL . "rewrite ^/search-([0-9]+)\.html$ /index.php?m=search&page=$1;}";
    $fp = fopen(".nginx", "w+");
    fwrite($fp, $str);
    fclose($fp);
    alert_back($GLOBALS['lang']['msg_success']);
}

elseif ($act == 'rewrite_isapi') {
  $str = "[ISAPI_Rewrite]" . PHP_EOL . "# 3600 = 1 hour" . PHP_EOL . "CacheClockRate 3600" . PHP_EOL . "RepeatLimit 32" . PHP_EOL . "# Protext httpd.ini and httpd.parse.errors files" . PHP_EOL . "# from accessing through HTTP" . PHP_EOL . "RewriteRule ^index\.html$ index.php" . PHP_EOL . "RewriteRule ^channel-([0-9]+)\.html$ index\.php\?m=channel&id=$1" . PHP_EOL . "RewriteRule ^channel-([0-9]+)-([0-9]+)\.html$ index\.php\?m=channel&id=$1&page=$2" . PHP_EOL . "RewriteRule ^detail-([0-9]+)\.html$ index\.php\?m=detail&id=$1" . PHP_EOL . "RewriteRule ^search\.html$ index\.php\?m=search" . PHP_EOL . "RewriteRule ^search-([0-9]+)\.html$ index\.php\?m=search&page=$1";
  $fp = fopen("httpd.ini", "w+");
  fwrite($fp, $str);
  fclose($fp);
  alert_back($GLOBALS['lang']['msg_success']);
}

elseif ($act == 'rewrite_dotnet') {
  $str = '<?xml version="1.0" encoding="UTF-8"?><configuration><system.webServer><rewrite><rules><rule name="index" stopProcessing="true"><match url="^index.html" /><action type="Rewrite" url="index.php" /></rule><rule name="channelp" stopProcessing="true"><match url="^channel-([0-9]+)\.html$" /><action type="Rewrite" url="index.php?m=channel&id={R:1}" /></rule><rule name="channelpp" stopProcessing="true"><match url="^channel-([0-9]+)-([0-9]+)\.html$" /><action type="Rewrite" url="index.php?m=channel&id={R:1}&page={R:2}" /></rule><rule name="detailp" stopProcessing="true"><match url="^detail-([0-9]+)\.html$" /><action type="Rewrite" url="index.php?m=detail&id={R:1}" /></rule><rule name="searchp" stopProcessing="true"><match url="^search\.html$" /><action type="Rewrite" url="index.php?m=search" /></rule><rule name="searchpp" stopProcessing="true"><match url="^search-([0-9]+)\.html$" /><action type="Rewrite" url="index.php?m=search&page={R:1}" /></rule></rules></rewrite></system.webServer></configuration>';
  $fp = fopen("web.config", "w+");
  fwrite($fp, $str);
  fclose($fp);
  alert_back($GLOBALS['lang']['msg_success']);
}

// cart
elseif ($act == 'cart') {
  if (isset($_POST['list']) && isset($_POST['num']) && isset($_POST['amount'])) {
    $arr = array();
    $arr['list'] = json_decode(str_replace("\\", "", $_POST['list']), true);
    $arr['num'] = $_POST['num'];
    $arr['amount'] = $_POST['amount'];
    $_SESSION['cart'] = $arr;

    $res['err'] = 'n';
    $res['url'] = 'cart.php';
  } else {
    $res["err"] = 'y';
    $res["url"] = '';
  }
  die(json_encode($res, JSON_NUMERIC_CHECK));
}

else {
  die($_lang['illegal']);
}
