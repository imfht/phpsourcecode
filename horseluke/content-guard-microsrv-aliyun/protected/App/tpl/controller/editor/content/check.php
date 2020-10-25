<?php
use SCH60\Kernel\StrHelper;
?>
<div class="container">
    <div class="row">
        <ol class="breadcrumb">
            <li>编辑工作站</li>
            <li class="active">发表文章</li>
        </ol>
    </div>
    
	<div class="row">
		<div class="col-md-12">
			<form action="<?=StrHelper::url('editor/content/submit')?>" method="post">
				<div class="form-group">
					<textarea class="form-control" rows="20" placeholder="文章内容"
						name="content"></textarea>
				</div>
				<button type="submit" class="btn btn-default">发表</button>
			</form>
		</div>
	</div>
	
	<div class="row">
	    <div class="col-md-12">&nbsp;</div>
	    <div class="col-md-12">
		    <div class="alert alert-info" role="alert">此处模拟一个CMS系统接入云安全API后，编辑发表文章时会调用相关接口进行内容检测。</div>
	    </div>
	</div>
	
</div>


