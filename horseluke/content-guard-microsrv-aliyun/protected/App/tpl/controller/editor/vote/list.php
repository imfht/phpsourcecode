<?php 
use SCH60\Kernel\StrHelper;
?>
<div class="container">

    <div class="row">
        <ol class="breadcrumb">
            <li>编辑工作站</li>
            <li class="active">投票记录</li>
        </ol>
    </div>
    
	<div class="row">
		<div class="col-md-12">
		
		    <table class="table table-hover">
		        <thead>
		            <tr>
		                <th>记录id</th>
		                <th>投票选手</th>
		                <th>创建时间</th>
		                <th>ip</th>
		            </tr>
		        </thead>
		        <tbody>
		            <?php foreach($datalist as $row): ?>
		            <tr>
		                <td><?=StrHelper::O($row['logid'])?></td>
		                <td><?=StrHelper::O($row['optionname'])?></td>
		                <td><?=StrHelper::O(date('Y-m-d H:i:s',$row['create_time']))?></td>
		                <td><a href="<?=StrHelper::url('microsrv/ipquery/attackhistory', array('ip' => $row['ip']))?>" title="查看<?=StrHelper::O($row['ip'])?>的历史攻击记录"><?=StrHelper::O($row['ip'])?></a></td>
		            </tr>
		            <?php endforeach; ?>
		        </tbody>
		    </table>
		
		</div>
	</div>
	
	<div class="row">
	    <div class="col-md-12">&nbsp;</div>
	    <div class="col-md-12">
		    <div class="alert alert-info" role="alert">此处模拟一个CMS系统接入云安全API后，对投票进行一个全景式统计检测。</div>
	    </div>
	</div>
	
</div>
