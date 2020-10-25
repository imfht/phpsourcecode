<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<meta http-equiv="content-type" content="text/html;charset=utf-8">

</head>
<body>
<?php
$kv = new SaeKV();
$ret = $kv->init();
echo "KVDB初始化：".($ret?"成功":"失败(errno:".$kv->errno()." errmsg:".$kv->errmsg().")");
echo "<br/>==============================<br/>";
?>


<form action="#" method="post" >


<p>操作：<select name="m" ><option>list</option><option>add</option><option>set</option><option>get</option><option>replace</option><option>delete</option>  </select> key:<input type="text" name="k"/>value:<input type="text" name="v"/><input type="submit"/></p>

<?
if(isset($_POST['m'])){
    $m=$_POST['m'];
    // 增加key-value
    $_key=$_POST['k'];
    $_val=isset($_POST['v'])?$_POST['v']:'';
    if($m=='add'){
        $ret = $kv->add($_key, $_val);
    }
    // 更新key-value
    if($m=='set'){
        $ret = $kv->set($_key, $_val);
    }
    // 替换key-value
    if($m=='replace'){
        $ret = $kv->replace($_key, $_val);
    }
    // 获得key-value
    if($m=='get'){
        $ret = $kv->get($_key);
        echo "<b>$_key:<br/>==============<br/><pre>";
        var_dump($ret);
        echo "</pre><br/>==============<br/></b>";
    }
    // 删除key-value
    if($m=='delete'){
        $ret = $kv->delete($_key);
    }

   $er=$ret?"成功(".$_key.":".$_val.")":"失败(errno:".$kv->errno()." errmsg:".$kv->errmsg().")";
   echo "<br/>$m 操作返回：$er <br/> <br/>";
}


$pk = isset($_POST['pk'])?$_POST['pk']:'';
$sk = isset($_POST['sk'])?$_POST['sk']:'';

$r=array();
$ret = $kv->pkrget($pk, 100,$sk);
end($ret);
$sk = key($ret);
//$all = isset($_POST['all'])?$_POST['all']:false;
// 循环获取所有key-values


/*
if ($all)
{
    while (true) {
        $r=array_merge($r,$ret);
        end($ret);
        $start_key = key($ret);
        $i = count($ret);
        if ($i < 100) break;
        $ret = $kv->pkrget($pk, 100, $start_key);
    }
}
$r=array_merge($r,$ret);
*/



?>


    <p>前缀:<input type="text" name="pk" value="<?=$pk?>"/>start_key:<input type="text" name="sk" value="<?=$sk?>"/><input type="submit" value="下一页"/></p>

<pre>
<?// 前缀搜索

var_dump($ret);
?>
</pre>
<p><input type="submit" value="下一页"/></p>
</form>
</body>
</html>