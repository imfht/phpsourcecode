<?php
require( "{$viewDir}/header.php" );
/* @var $ex Exception */
?>
<h1><?php echo $ex->getMessage(); ?></h1>

<div class="error">
<h2>错误原因：</h2>
您看到这个错误页面是因为应用程序抛出了没有捕获的异常。
</div>

<p></p>

<div class="track">
<?php __error_dump_trace($ex); ?>
</div>