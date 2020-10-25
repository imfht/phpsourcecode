<?php 
session_start();
header("content-type:text/html;charset=utf-8"); 
if($_GET['id'] && $_GET['key']){
$id = $_GET['id'];
$key = $_GET['key'];
}else{
die("请把参数填写完整！");
}


$token = select($id,$key);
if($token){
    $_SESSION['wxtoken'] =$token;
}
?>
<style type="text/css">
<!--
*{
    border:0;
    margin:0;
    padding:0;
}
#f{
  width:760px;

}
.f1{
   width:auto;
   height:30px;
   margin:10px 10px auto 10px;
   border: 0; 

}
.number{
    width:30px;
    height:30px;
    border:1px solid #a1a1a1;
    line-height:30px;
    text-align:center;
    font-size:18px;
    float:left;
}
.f1text{
    width:210px;
    height:30px;
    float:right;
}
.f2{
    width:auto;
   height:30px;
   margin:10px 10px auto 30px;
   border: 0; 

}
.j{
    width:230px;
}
.key{
    width:400px;
    border-left:0;
    border-right:0;
}
.type{
    width:90px;
}
li{
    list-style:none;
    float:left;
    
    height:30px;
    width:250px;

}
.text{
    width:85%;
    height:100%;
    font-size: 14px;
    line-height:25px;
    border:1px solid #a1a1a1;
}
.sub{
    margin-top:20px;
    margin-bottom:30px;
    width:100%;
    float:right;
    text-align:center;

}
.submit{
    background:url('sss.jpg') #a1a1a1;
    border-top:1px solid #a1a1a1;
    width:600px;
    height:35px;
    color:#fff;
    
}

#box{
    width:250px;
    border:1px solid #a1a1a1;
    border-radius:5px;
   margin: 0px;
padding-top: 10px;
}
#box .textit{
    width:auto;
    height:35px;
    line-height:35px;
    padding-left:12px;
    font-weight:700;
    font:16px;
}
#box .boxcen{
    width:100%;
    border-top:1px solid #a1a1a1;
}
#box .boxcen img{
    width:230px;
    height:230px;
}
#left{
    width:252px;
    height:200px;
    float:left;
}
-->
</style>
<?php
if($_SESSION['wxtoken']==""){
   die("获取access_token失败！");
}

//获取access_token
function select($id, $key)
{
    $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$id."&secret=".$key;
    $ch = CURL_INIT();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $a = curl_exec($ch);
    $json = json_decode($a, true);
    $j = $json['access_token'];
    return $j;

}

function token()
{
    $url = "https://api.weixin.qq.com/cgi-bin/menu/get?access_token=".$_SESSION['wxtoken'];
    $ch = CURL_INIT();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $a = curl_exec($ch);
    return $a;

}
$arr=json_decode(token(),true);
?>
<div id='m'>

<div id='f'>
<div class="f1" style="border-bottom: 1px solid #a1a1a1;text-align:center;background-color: aliceblue;
margin: 0px;
padding-top: 10px; font-weight:bold;">
<li>菜单名</li>
<li>键值</li>
<li class="type" style="text-align: right;">类型</li>
</div>

<?php   
echo"<form name='form1' action='wx.php?t=js' method='post'>\n";
for($f1=0;$f1<3;$f1++){
echo"<div class='f1'>\n";
echo"<li><div class='number'>".$f1."</div><div class='f1text'><input type='text' class='text' style='width:100%;' name='name".$f1."' value='".$arr['menu']['button'][$f1]['name']."'></div></li><li class='key'>——<input type='text' class='text' name='key".$f1."' value='".$arr['menu']['button'][$f1]['key'].$arr['menu']['button'][$f1]['url']."'></li><li class='type'>\n";
echo"<select name='type".$f1."' class='text' id='type".$f1."'>\n";
    if($arr['menu']['button'][$f1]['type']=="view"){
        echo"<option value=''>请选择</option>";
        echo"<option value='view' selected>链接</option>\n";
        echo"<option value='click'>点击</optime>\n";
     }elseif($arr['menu']['button'][$f1]['type']=="click"){
        echo"<option value=''>请选择</option>\n";
        echo"<option value='click' selected>点击</option>\n";
        echo"<option value='view'>链接</option>\n";
     }
     else{
        echo"<option value='' selected>请选择</option>\n";
        echo"<option value='click'>点击</option>\n";
        echo"<option value='view'>链接</option>\n";
    }
echo"</select>\n</li>";
echo"</div>";
for($f2=0;$f2<5;$f2++){
$f3=$f2+1;
echo"<div class='f2'>\n";
echo"<li class='j'>|---<input type='text' class='text' name='name".$f1.$f3."' value='".$arr['menu']['button'][$f1]['sub_button'][$f2]['name']."'></li><li class='key'>——<input type='text' class='text' name='key".$f1.$f3."' value='".$arr['menu']['button'][$f1]['sub_button'][$f2]['key'].$arr['menu']['button'][$f1]['sub_button'][$f2]['url']."'></li><li class='type'>\n";
echo"<select name='type".$f1.$f3."' class='text'>\n";
    if($arr['menu']['button'][$f1]['sub_button'][$f2]['type']=="view"){
        echo"<option value=''>请选择</option>\n";
        echo"<option value='view' selected>链接</option>\n";
        echo"<option value='click'>点击</optime>\n";
     }elseif($arr['menu']['button'][$f1]['sub_button'][$f2]['type']=="click"){
        echo"<option value=''>请选择</option>\n";
        echo"<option value='click' selected>点击</option>\n";
        echo"<option value='view'>链接</option>\n";
     }
     else{
        echo"<option value='' selected>请选择</option>\n";
        echo"<option value='click'>点击</option>\n";
        echo"<option value='view'>链接</option>\n";
    }
echo"</select></li>\n";
echo"</div>\n";
}//二级菜单
}//一级菜单
echo "<br />";
//结束一个主菜单（菜单3echo"<br />";
echo"<div class='sub'><input type='submit' class='submit' name='submit1' value='创建菜单'></div>";
echo"</form>";
echo"</div></div>";
?>