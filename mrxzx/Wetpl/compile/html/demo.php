<?php $vars = json_decode('{"title":"Wetpl Demo","conf":{"name":"\u5c0f\u5353","age":15,"sex":"\u7537","hobby":["\u7f16\u7a0b","\u6e38\u620f"],"about":"\u4eba\u751f\u82e6\u77ed\uff0c\u6211\u7528python"}}',true); ?>
<?php foreach($vars as $k => $v){ ?>
<?php $$k = $v; ?>
<?php } ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo $title; ?></title>
</head>
<body>


<?php //使用import可以导入其他的模板 ?>

<?php // 下面是一个简单的个人信息例子！ ?>

<?php goto conf; ?>

<p>我被跳过了！</p>

<?php conf: ?>

<p>我的名字:<?php echo $conf['name']; ?></p>

<p>我的年龄:<?php echo $conf['age']; ?></p>

<p>
    我的爱好:
    <?php foreach($conf['hobby'] as $v){ ?>

    <?php echo $v; ?>

    <?php } ?>
</p>

<p>一段介绍:<?php echo $conf['about']; ?></p>

<hr>

<?php // 下面介绍一下其它东西 ?>

<?php function demo1(){ echo '我是一个函数，我的名字叫做"demo1",我会自己说话'; } ?>

<?php function demo2(){ return '我是一个函数，我的名字叫做"demo2",我不会自己说话，只有你让我说话时我才能说话'; } ?>

<p><?php demo1(); ?></p>
<?php // 上面的在调用之后会自己输出 ?>

<p><?php echo demo2(); ?></p>
<?php // 如果想输出他的返回值的话，只需要在前面加上'&' ?>

<?php // 下面是关于赋值的操作 ?>
<?php $demo2_val =  demo2(); ?>
<?php $demo2_val_2 =  $demo2_val; ?>
<?php $n =  1; ?>
<?php //Ps:赋值的前面要有'%' ?>

<?php $a = 1; ?>
<?php $a += 1; ?>
<?php // 使用这种方法赋值也是可以的，更简单了 ?>

<?php #类似于 $n += 1 ('+'可以为 +、-、*、/、.); ?>
<?php $n +=  1; ?>


<hr>

<?php //最重要的判断语句 ?>

<?php $num =  1 + 1 * 1; ?>

<?php if($num == 1){ ?>
    if:1 + 1 * 1 = 1!
<?php }elseif($num == 3){ ?>
    elif了:1 + 1 * 1 = 3!
<?php }else{ ?>
    else: 1 + 1 * 1 = <?php echo $num; ?>!
<?php } ?>

<hr>

<?php //还有循环 ?>

<p>
    <?php for($i = 0;$i < 10;$i++){ ?>
    for:<?php echo $i + 1; ?>
    <?php } ?>
</p>

<p>
    <?php $j =  0; ?>
    <?php while($j < 10){ ?>
        while:<?php echo $j + 1; ?>
        <?php $j +=  1; ?>
    <?php } ?>
</p>

<p>
    <?php $arr =  [1,2,3,4,5]; ?>
    <?php foreach($arr as $key => $val){ ?>
    <?php echo $key; ?> => <?php echo $val; ?>
    <?php } ?>
</p>

<p>
    <?php foreach($arr as $value){ ?>
    <?php echo $value; ?>
    <?php } ?>
</p>

<p>
    <?php // 如果需要用到其他东西，可以使用php:直接运行php代码 ?>

    <?php  echo "通过直接运行php输出"; ?>
</p>

</body>
</html>
