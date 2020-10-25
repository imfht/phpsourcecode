<?php
require_once 'lib.include.php';

/**
 * 用于显示当前的命令信息，并进行可触发执行
 */

//获取配置信息
$conf_arr=  require_once 'conf/setting.inc.php';

echo '<h1>Order list </h1>';

//循环要执行的命令
foreach ($conf_arr as $index=>$conf_val){
    $order_md5=  md5($index + $conf_val['__title']);
    $order_str= $conf_val['__order'];
    if($order_md5 == $_GET['exec']){
        if('' != $_GET['p'] && $conf_val['__manual_execute_pwd'] == $_GET['p']){
            $order_result=array();
            exec($order_str,$order_result);
            echo "Exectue : [{$order_str}] [<a href='order-list.php'>Clean order</a>]<br/><pre>".  print_r($order_result,true).'</pre><hr/>';
        }
        echo "Exectue failed. password not valid.";
    }
}

//循环显示的命令
foreach ($conf_arr as $index=>$conf_val){
    $order_md5=  md5($index + $conf_val['__title']);
    echo "<p>Order：{$conf_val['__title']} <span style='color:red;'>[{$conf_val['__order']}]</span> PWD:[<input id='id_{$order_md5}' type='password' />] [<a href='order-list.php?exec={$order_md5}' onclick=\"var _password=document.getElementById('id_{$order_md5}').value; this.href=this.href + '&p='+_password ; \">Execute</a>]</p>";
}

