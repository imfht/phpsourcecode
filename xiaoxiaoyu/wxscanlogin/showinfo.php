<!DOCTYPE html>
<html>
    <head>
        <title>微信登陆演示</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
         <script src="http://hstatic.cn/uajsapi/uajsapi.js" type="text/javascript"></script>
         <script type="text/javascript" src="http://cdn.qn.hstatic.cn/emoji2b5ae0.js"></script>
         <link rel="stylesheet" href="https://res.wx.qq.com/c/=/mpres/htmledition/style/widget/emoji218878.css,/mpres/htmledition/style/biz_web/widget/dropdown2f12f7.css,/mpres/htmledition/style/widget/rich_buddy2f2f7f.css,/mpres/htmledition/style/widget/pagination218878.css"/>
 
    </head>
  
    <body>
        <div>
<?php
require_once 'comm.php';
$b=$wxlogin->getUserInfo();
 //   var_dump($b);
    
 function getSex($int){
   $sexdesc=array("未知","男","女");
   return $sexdesc[$int];
 }
   // echo 
?>
            <p>用户openid:<?php echo $b->openid?></p>
            <p>用户昵称:<span id="nickname"><?php echo $b->nickname;?></span></p>
            <p>用户性别:<?php echo getSex($b->sex);?></p>
            <p>所在国家:<?php echo $b->country;?></p>
            <p>所在省:<?php echo $b->province;?></p>
            <p>所在市:<?php echo $b->city;?></p>
            <p>头像<img src="<?php echo $b->headimgurl?>"></p>
         </div>
<script>

document.getElementById("nickname").innerHTML=(document.getElementById("nickname").innerHTML.emoji());
</script>
        
    </body>
</html>
