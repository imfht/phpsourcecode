<?php 
use SCH60\Kernel\App;
use SCH60\Kernel\KernelHelper;
use SCH60\Kernel\StrHelper;

use Common\AppCustomHelper;

$router = App::$app->getRouter();

?>

<?php if(!isset($only_resource) || $only_resource != true): ?>

<footer class="admin-footer">
    <div class="container">
        <div class="row">
            <div class="col-md-7 col-xs-7">
                <p><?=StrHelper::O(KernelHelper::config('product_name'));?></p>
                <p><a href="<?=StrHelper::urlStatic("")?>">作品介绍</a></p>
                </div>
            <div class="col-md-5 col-xs-5 admin-footer-right">
                <p>执行时间：<?=substr(microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'], 0, 6)?> s</p>
                <p>Horse Luke 2015</p>
            </div>
        </div>
    </div>

</footer>

<?php endif; ?>

<!-- Include all compiled plugins (below), or include individual files as needed -->
<script src="<?=StrHelper::urlStatic("static/require.js/2.1.15/require.min.js");?>"></script>
<script src="<?=StrHelper::urlStatic("static/adminapp/js/init.js");?>"></script>

    <?php 
    $basicCfg = array(
        'baseUrl' => StrHelper::urlStatic(""),
        'url' => StrHelper::url(),
    );
    ?>
    <script>
    require(['webjstool'], function(webjstool){
    	webjstool.cfg.set(<?=json_encode($basicCfg)?>);
    });
    
    require(['jquery'], function($){
    	$(document).ready(function(){
            var actDefault =  "action_<?= strtolower($router['subapp']. '/'. $router['controller']. '/'. $router['action']);?>";
    	    require([actDefault], function(action){
    		    action.run();
    		}, function(err){
        		if(err.message.match(/^script error for/i)){
            		return ;
        		}
        		console.log(err);
    		});
    	});
    });
    </script>
    
    <?php if(!isset($disable_ping) && AppCustomHelper::isLogin()): ?>
    <script>
    require(['jquery'], function($){
        var pingAction = function(){
            $.ajax({
    			url: '<?=StrHelper::url('index/ajax/ping')?>',
    			method: 'GET',
    			success: function(rst){
    				setTimeout(pingAction, 600 * 1000);
    			},
    			error: function(result){
    				alert("长时间没有操作，已被强制退出。");
    			}
            });
        };
    	$(document).ready(function(){
    		setTimeout(pingAction, 600 * 1000);
    	});
    });
    </script>
    <?php endif; ?>