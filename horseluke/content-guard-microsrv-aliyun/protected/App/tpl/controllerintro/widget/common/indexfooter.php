<?php 
use SCH60\Kernel\KernelHelper;
use SCH60\Kernel\StrHelper;
?>
<?php if(!isset($only_resource) || $only_resource != true): ?>

<footer class="admin-footer">
    <div class="container">
        <div class="row">
            <div class="col-md-7 col-xs-7">
                <p><?=StrHelper::O(KernelHelper::config('product_name'));?></p>
                <p><a href="<?=StrHelper::urlStatic('')?>">作品介绍</a></p>
                </div>
            <div class="col-md-5 col-xs-5 admin-footer-right">
                <p>执行时间：<?=substr(microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'], 0, 6)?> s</p>
                <p>Horse Luke 2015</p>
            </div>
        </div>
    </div>

</footer>

<?php endif; ?>

<script src="<?=StrHelper::urlStatic("static/jquery_1.11.1/jquery.min.js");?>"></script>
<script src="<?=StrHelper::urlStatic("static/bootstrap-3.3.5/js/bootstrap.min.js");?>"></script>


