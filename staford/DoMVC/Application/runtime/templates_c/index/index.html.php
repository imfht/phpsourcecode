<?php defined('ROOT_DIR') or die('Access denied!');?>
<?php include $this->_include('common/header.html');?>
<body style="background-color: #eee;margin-bottom: 1.5em;">

<?php $i=0; $limit=10; for($i;$i<$limit;$i++):?>
<?php echo $i;?>&nbsp;
<?php endfor;?>

&nbsp;
<?php $i=0; $limit=10; $step=3; for($i;$i<$limit;$i+=$step):?>
<?php echo $i;?>&nbsp;
<?php endfor;?>


&nbsp;

<?php $i=10; $limit=0; for($i;$i>$limit;$i--):?>
<?php echo $i;?>&nbsp;
<?php endfor;?>


&nbsp;
<?php $i=10; $limit=0; $step=3; for($i;$i>$limit;$i-=$step):?>
<?php echo $i;?>&nbsp;
<?php endfor;?>
&nbsp;
/Application/static
&nbsp;

<?php Cache::block('array',20);?>
<?php foreach(array('fuck','ciao','nima') as $key=>$val):?>
<?php echo $val;?><br />
<?php endforeach;?>

<?php foreach(array('fuck','ciao','nima') as $val):?>
<?php echo $val;?><br />
<?php endforeach;?>

<?php foreach(array('fuck','ciao','nima') as $key=>$val):?>
<?php echo $key;?>.<?php echo $val;?><br />
<?php endforeach;?>

<?php Cache::catchBlock('array');?>




<?php Cache::block('text',20);?>
<?php dump($cfg);?>
<?php Cache::catchBlock('text');?>





<?php echo $cfg['idx']['key'];?>

<?php echo $cfg[1];?>

<?php echo $cfg[2][0];?>

<?php switch(1+2):?>
<?php case '2':?>
        fuck
<?php break;?>
<?php case '3':?>
        haha
<?php break;?>
<?php default:?>
        fuck hahaha
<?php endswitch;?>

<?php echo dateFormat(time()-3600);?>

<?php echo Cache::getBlock('array');?>

<?php echo Cache::getBlock('text');?>
<?php echo strCut('卧槽、、、何等卧槽',1,3);?>
<div style="width: 320px;height: 320px;background-color: #FF9900;color: black;position: absolute;top: 50%;left: 50%;margin-top: -160px;margin-left: -160px;"><p style="text-align: center;vertical-align: middle;font-size: 64px;margin-top: 108px;">o(≧v≦)o</p><p><?php echo $content;?></p></div>
<?php include $this->_include('common/footer.html');?>
</body>
</html>