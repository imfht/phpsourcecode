<?php 
use SCH60\Kernel\KernelHelper;
use SCH60\Kernel\StrHelper;
?>
<div class="container">

    <div class="row">
        <ol class="breadcrumb">
            <li>系统管理</li>
            <li>内容安全微服务</li>
            <li class="active">接入检测</li>
        </ol>
    </div>

	<div class="row">
		<div class="col-md-12">
		    <?php if(!$isOk): ?>
			<div class="alert alert-danger" role="alert">内容安全微服务接入失败。错误详情：<?=StrHelper::O($error['error']. ' ('. $error['errorDetail']. ')');?></div>
			<?php else: ?>
			<div class="alert alert-success" role="alert">恭喜，内容安全微服务已接入成功。</div>
			<?php endif; ?>
		</div>
	</div>
	
	<div class="row">
		<div class="col-md-12">
		    <h4><b>接入信息</b></h4>
		    <hr />
            <p>API网关：<?=StrHelper::O(KernelHelper::config('MICROSRV_GATEWAYURL'));?></p>
            <p>接入APPID：<?=StrHelper::O(KernelHelper::config('MICROSRV_APPID'));?></p>
		</div>
	</div>
	
	<div class="row">
	    <div class="col-md-12">&nbsp;</div>
		<div class="col-md-12">
		    <div class="alert alert-info" role="alert">此处模拟程序接入内容安全微服务后，进行微服务连通性检测。</div>
		</div>
		<div class="col-md-12">&nbsp;</div>
	</div>
	
</div>

