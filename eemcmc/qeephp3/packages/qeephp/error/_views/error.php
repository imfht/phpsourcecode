<?php
require( "{$viewDir}/header.php" );
?>

<h1><?php echo $title; ?></h1>

<div class="error">
<h2>错误原因：</h2>
<?php echo $error['message']; ?>
</div>


<div class="tip">
<h2>解决：</h2>
请检查文件 <strong><?php __error_filelink($errfile); ?></strong> 第 <strong><?php echo $error['line']; ?></strong> 行。
</div>

<p></p>

<div class="track">
<?php 
    echo "SOURCE CODE: <br />\n";
	echo __error_show_source($error['file'], $error['line']);
	echo "<br />\n";
?>

</div>
