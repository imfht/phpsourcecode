<?php
session_start();
header("content-type:text/html;charset=utf-8");
?>
<!doctype html>
<style type="text/css">
<!--
body{
    background:#f2f2f2;
}
#msg{
    width:600px;
    height:auto;
    margin:100px auto auto auto;
    border:1px solid #a1a1a1;
    overflow:hidden;
    padding:15px;
    background:#fff;
    border-radius:10px;
}
#title{
    width:auto;
    height:40px;
    line-height:40px;
    font-size:16px;
    padding-left:10px;
}
#content{
    width:100%;
    height:auto;
    border-top:1px solid #a1a1a1;
    border-bottom:1px solid #a1a1a1;
    

}
#content .tit{
    width:auto;
    height:30px;
    line-height:30px;
    color:#a1a1a1;
}
#content .json{
    width:100%;
    height:auto;
    padding-bottom:10px;
}	
#footer{
    width:auto;
    height:40px;
    line-height:40px;
    float:right;
}
-->
</style>
<?php

if ($_POST['t'] == 's') {//获取access_token
    $token=select($_POST['appid'], $_POST['appsecret']);
    if($token){
    $_SESSION['wxtoken'] =$token;
    //mysql_query("UPDATE wx_chaidang SET access_token='" . $name . "' where id=1 ");
    echo "<script>alert('获取成功,如果没自动跳转请重新获取一次');location.href='menu.php';</script>";
      }
      else{
        echo"access_token获取失败";
        exit();
      }
}elseif($_GET['t']=='js'){//创建菜单
    f12();
    $xjson=str_replace(",}","}",str_replace("},]","}]",json()));
    $url = "https://api.weixin.qq.com/cgi-bin/menu/create?access_token=".$_SESSION['wxtoken'];
    //$xjson=urldecode(json_encode($arr));
    $result = vpost($url, $xjson);
    $err=json_decode($result,true);
    if($err['errcode']==0){
        echo"<div id='msg'>";
        echo"<div id='title'>菜单创建成功</div>";
        echo"<div id='content'><div class='tit'>JSON</div>";
        echo"<div class='json'>".$xjson."</div>";
        echo"<div id='footer'><a href='menu.php'>返回修改</a></div>";
        echo"</div>";
    }
    else{
        echo"<div id='msg'>";
        echo"<div id='title'>菜单创失败！错误代码：".$err['errcode']."</div>";
        echo"<div id='content'><div class='tit'>JSON</div>";
        echo"<div class='json'>".$xjson."</div>";
        echo"<div id='footer'><a href='menu.php'>返回修改</a></div>";
        echo"</div>";
    }
    
}//----------------------------------------------------------
//获取access_token
function select($id, $key)
{
    $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$id&secret=$key";
    $ch = CURL_INIT();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $a = curl_exec($ch);
    $json = json_decode($a, true);
    $j = $json['access_token'];
    return $j;

}
//菜单创建
function vpost($url, $data)
{ // 模拟提交数据函数
    $curl = curl_init(); // 启动一个CURL会话
    curl_setopt($curl, CURLOPT_URL, $url); // 要访问的地址
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); // 对认证证书来源的检查
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false); // 从证书中检查SSL加密算法是否存在
    curl_setopt($curl, CURLOPT_USERAGENT,'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)'); // 模拟用户使用的浏览器
    // curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1); // 使用自动跳转
    // curl_setopt($curl, CURLOPT_AUTOREFERER, 1); // 自动设置Referer
    curl_setopt($curl, CURLOPT_POST, 1); // 发送一个常规的Post请求
    curl_setopt($curl, CURLOPT_POSTFIELDS, $data); // Post提交的数据包x
    curl_setopt($curl, CURLOPT_TIMEOUT, 30); // 设置超时限制防止死循环
    curl_setopt($curl, CURLOPT_HEADER, 0); // 显示返回的Header区域内容
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回
    $tmpInfo = curl_exec($curl); // 执行操作
    if (curl_errno($curl)) {
        echo 'Errno' . curl_error($curl); //捕抓异常
    }
    curl_close($curl); // 关闭CURL会话
    return $tmpInfo; // 返回数据
}

//接收并创建数组


function json(){//组装数据
$json="{\"button\":[".ff1(f1($_POST['type0'],$_POST['name0'],$_POST['key0'])).ff1(cc2(c1($_POST['type01'],$_POST['name01'],$_POST['key01']),c1($_POST['type02'],$_POST['name02'],$_POST['key02']),c1($_POST['type03'],$_POST['name03'],$_POST['key03']),c1($_POST['type04'],$_POST['name04'],$_POST['key04']),c1($_POST['type05'],$_POST['name05'],$_POST['key05']),$_POST['name0'])).ff1(f1($_POST['type1'],$_POST['name1'],$_POST['key1'])).ff1(cc2(c1($_POST['type11'],$_POST['name11'],$_POST['key11']),c1($_POST['type12'],$_POST['name12'],$_POST['key12']),c1($_POST['type13'],$_POST['name13'],$_POST['key13']),c1($_POST['type14'],$_POST['name14'],$_POST['key14']),c1($_POST['type15'],$_POST['name15'],$_POST['key15']),$_POST['name1'])).ff1(f1($_POST['type2'],$_POST['name2'],$_POST['key2'])).ff1(cc2(c1($_POST['type21'],$_POST['name21'],$_POST['key21']),c1($_POST['type22'],$_POST['name22'],$_POST['key22']),c1($_POST['type23'],$_POST['name23'],$_POST['key23']),c1($_POST['type24'],$_POST['name24'],$_POST['key24']),c1($_POST['type25'],$_POST['name25'],$_POST['key25']),$_POST['name2']))."]}";
return $json;
}

function c1($a,$b,$c){
    if($a!="" or $b!="" or $c!="")
    {
        if($a=="view")
        {
            $file2='{"type":"'.$a.'","name":"'.$b.'","url":"'.$c.'"},';
        }else
        {
           $file2='{"type":"'.$a.'","name":"'.$b.'","key":"'.$c.'"},'; 
        } 
    }
    else
    {
       null;
    }
    return $file2;
}//取二级菜单

function cc2($a1,$a2,$a3,$a4,$a5,$g1)
{
    
    if($a1!="")
    {
        $b1=$a1;
    }else
    {
        NULL;
    }//a1
    
    if($a2!="")
    {
        $b2=$a2;
    }else
    {
        NULL;
    }//a2
    
    if($a3!="")
    {
        $b3=$a3;
    }else
    {
        NULL;
    }//a3
    
    if($a4!="")
    {
        $b4=$a4;
    }else
    {
        NULL;
    }//a4
    
    if($a5!="")
    {
        $b5=$a5;
    }else
    {
        NULL;
    }//a5
    if($b1!="" or $b2!=""  or $b3!="" or $b4!="" or $b5!=""){
        $sub_tutton='"sub_button":['.$b1.$b2.$b3.$b4.$b5.']},';
       }elseif($g1!=""){
        $sub_tutton="},";
       }else{
        $sub_tutton="";
       }
    
    return $sub_tutton;
    
}//取sub_button
function f1($a,$b,$c){
    if($a!=""||$b!=""||$c!="")
    {
        
            if($a=="view")
            {
                $file2='{"type":"'.$a.'","name":"'.$b.'","url":"'.$c.'",';
            }elseif($a=="click")
            {
                $file2='{"type":"'.$a.'","name":"'.$b.'","key":"'.$c.'",'; 
            }
            else
            {
                $file2='{"name":"'.$b.'",'; 
            }
        
    }
    else
    {
        null;
    }
    return $file2;
}//取一级菜单
function ff1($key){
    if($key!=""){
        return $key; 
    }
    
}
function f12(){//判断一线跟二级菜单关系
 for($i=0;$i<2;$i++){//检查一级菜单键名
    if($_POST['name'.$i.'1']!="" || $_POST['name'.$i.'2']!="" || $_POST['name'.$i.'3']!="" || $_POST['name'.$i.'4']!="" || $_POST['name'.$i.'5']!=""){
        if($_POST['name'.$i]==""){
            $mi=$i+1;
            echo"<script>alert('第".$mi."个一级菜单为空');history.back();</script>";
            exit();
        }
    }

 }//for结束
 for($i=0;$i<5;$i++){//检查二级菜单键名
 for($ai=1;$ai<5;$ai++){
    if($_POST['key'.$i.$ai]!=""){
        if($_POST['name'.$i.$ai]==""){
            echo"<script>alert('有一个二级菜单没键名请检查');history.back();</script>";
            exit();
        }
    }
  }//for 二级结束
 }//for结束
  for($ai=1;$ai<5;$ai++){//检查二级菜单键值
  for($i=0;$i<5;$i++){
    if($_POST['name'.$i.$ai]!=""){
        
        if($_POST['key'.$i.$ai]=="" || $_POST['type'.$i.$ai]==""){
            echo"<script>alert('有一个二级菜单错误，值请检查键值或类型');history.back();</script>";
            exit();
        }
    }
  }//for 二级结束
 }//for结束
 if($_POST['name0']=="" &&$_POST['name1']=="" &&$_POST['name2']==""){
    echo"<script>alert('您至少要创建一个一级菜单');history.back();</script>";
    exit();
 }
 
}
?>