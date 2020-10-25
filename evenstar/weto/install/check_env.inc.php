<?php $succeed = 1;?>
<style type="text/css">
#body div.ok, #body div.warn, #body div.error{border: 0px; background: none; margin: 0px; padding: 0px;}
.list .table td {padding:0px; padding-left: 4px;}
</style>
<div style="width: 700px; margin: auto;">
	<h1>2. Xiuno BBS 安装环境检测</h1>
	<div class="list">
		<table align="center" class="table">
			<tr class="header">
				<td>检测环境</td>
				<td>要求</td>
				<td>当前</td>
				<td style="text-align: center;">检测结果</td>
			</tr>
			
			<?php foreach($env as $v) { ?>
			<tr>
				<td><?php echo $v['name'];?></td>
				<td><?php echo $v['need'];?></td>
				<td><b><?php echo $v['current'];?></b></td>
				<td>
					<?php if($v['status'] == 1) { ?><div class="ok">✔通过</div>
					<?php } elseif($v['status'] == 2) { ?><span class="warn">！通过</span>
					<?php } elseif($v['status'] == 0) { $succeed = 0; ?><span class="error">✘未通过</span><?php }?>
				</td>
			</tr>
			<?php } ?>
		<?php if(!IN_SAE) {?>
			<tr class="header">
				<td colspan="4">检测目录文件可写</td>
			</tr>
			<?php foreach($write as $k=>$v) { ?>
			<tr>
				<td><?php echo $k;?></td>
				<td></td>
				<td></td>
				<td>
					<?php if($v) { ?><div class="ok">✔可写</div><?php }?>
					<?php if(!$v) { $succeed = 0;?><div class="error">✘不可写</div><?php }?>
				</td>
			</tr>
			<?php } ?>
		<?php } else {?>
			<tr class="header">
				<td colspan="4">SAE 检测目录文件可写</td>
			</tr>
		
			<tr>
				<td>saekv://tmp/</td>
				<td>可读写</td>
				<td><b><?php echo $saewrite['tmp_path'] ? '可写' : '不可写';?></b></td>
				<td>
					<?php if($saewrite['tmp_path']) { ?><div class="ok">✔通过</div>
					<?php } else { $succeed = 0; ?><div class="error">✘未通过</div><?php }?>
				</td>
			</tr>
			<tr>
				<td>saestor://upload/</td>
				<td>可读写</td>
				<td><b><?php echo $saewrite['upload_path'] ? '可写' : '不可写';?></b></td>
				<td width="33%">
					<?php if($saewrite['upload_path']) { ?><div class="ok">✔通过</div>
					<?php } else { $succeed = 0; ?><div class="error">✘未通过</div>（请在SAE创建 upload 域，<a href="http://www.xiuno.com/thread-index-fid-3-tid-806.htm" target="_blank">查看帮助</a>）<?php }?>
				</td>
			</tr>
		<?php } ?>
		</table>
	</div>
	<?php if (!$succeed) { ?>
		<div class="error" style="text-align: center;">✘ 未通过检测！Xiuno BBS 依赖以上所有环境，请检测红色字体项目。</div>
		<p style="text-align: center;">
			<input type="button" value=" 上一步" onClick="history.back();"/>
		</p>
	<?php } else { ?>
		<div class="ok" style="text-align: center;">✔ 通过检测，Xiuno BBS 可以正常运行在该环境下。 </div>
		<p style="text-align: center;">
			<input type="button" value=" 上一步" onClick="history.back();"/>
			<input type="button" value=" 下一步" onClick="window.location='index.php?step=checkdb';"/>
		</p>
	<?php } ?>
</div>