<?php 
use SCH60\Kernel\StrHelper;
?>
<div class="container">

    <div class="row">
        <ol class="breadcrumb">
            <li>编辑工作站</li>
            <li class="active">发表结果</li>
        </ol>
    </div>

	<div class="row">
		<div class="col-md-12">
		    <?php if(!$isOk): ?>
			<div class="alert alert-danger" role="alert">对不起，文章发表失败！<?=StrHelper::O($error);?></div>
			<?php else: ?>
			<div class="alert alert-success" role="alert">恭喜，文章发表成功！</div>
			<?php endif; ?>
		</div>
	</div>
	
	<div class="row">
	    <h5>刚才提交的文章内容：</h5>
		<div class="col-md-12">
			<?=StrHelper::O($content)?>
		</div>
	</div>
	
</div>


