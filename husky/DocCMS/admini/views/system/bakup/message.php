<?php require(ABSPATH.'/admini/views/system/bakup/header.php') ?>
<table cellpadding="2" cellspacing="1" class="tableborder">
  <tr>
    <th>提 示 信 息</th>
  </tr>
<tr>
<td align="center" style="line-height:200%;" class="tablerow">
<?php echo $msg?>
<?php if($url_forward=='goback' || $url_forward=='') { ?>
<br/><a href="javascript:history.back();" >[点这里返回上一页]</a>
<?php } elseif($url_forward=="close") { ?>
<br/><input type="button" name="close" value=" 关闭 " onClick="window.close();">
<?php } elseif($url_forward) { ?>
<br/><a href="<?php echo $url_forward?>">如果您的浏览器没有自动跳转，请点击这里</a>
<script type="text/javascript">
function redirect(url){
window.location.href=url;
}
setTimeout("redirect('<?php echo $url_forward?>');",<?php echo $ms?>);
</script>
<?php } ?>
<br/>
</td>
</tr>
</table>