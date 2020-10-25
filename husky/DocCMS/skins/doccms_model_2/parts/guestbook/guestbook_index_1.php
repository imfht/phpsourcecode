<?php
    // 为方便并保证您以后的快速升级 请使用SHL提供的如下全局数组
	
	// 数组定义/config/doc-global.php
	
	// 如有需要， 请去掉注释，输出数据。
	/*
	echo '<pre>';
		print_r($tag);
	echo '</pre>';
	*/
?>
<style type="text/css">
*{ padding:0; margin:0;}
img{ border:none;}
a{ text-decoration:none;}
.guestbk,.guestbk p,#guestsmt{ width:100%; float:left;}
.ask-box{background:#EEE; margin-bottom:30px; float:left;}
.ask-box .lwrap{background:#EEE;/* 修正IE6 */_position:relative;_z-index:10;} /* arrow-effect */
.lwrap span{ float:right; font-size:14px; color:#999;}
.ask-left{ border-left:20px solid #FFF; border-top:20px solid #EEE; margin-top:20px;}
.answer-right{ border-right:20px solid #FFF; border-top:20px solid #4FBCD8;}
.ask-left .lwrap,.answer-right .rwraps{ padding:12px 10px 12px 10px; margin-top:-40px; line-height:25px; width:auto!important; max-width:500px;  /* sets min-width & max-width for ie */ _width: expression(document.body.clientwidth > 500 ? "500px" : "auto");}
.answer-box{background:#4FBCD8;margin-bottom:30px; float:right;}
.answer-box .rwraps{background:#4FBCD8;/* 修正IE6 */_position:relative;_z-index:10; color:#fff;} /* arrow-effect */
.useript{ background-color: white; border-color: #CCCCCC #E2E2E2 #E2E2E2 #CCCCCC; border-style: solid; border-width: 1px;  box-shadow: 1px 2px 3px #F0F0F0 inset; overflow: hidden; padding: 10px 0 8px 8px; vertical-align: middle;}
#guestsmt{ padding:30px 0 30px 10px;}
.guestinfo{ width:96%; height:80px; margin-bottom:15px; float:left;}
#guestsmt p{ width:97%;}
#guestsmt span{ font-family:"微软雅黑"; font-size:14px;}
.usertel{ width:100px; margin-right:20px;}
.userbtn{ padding:0.2em 0.8em; font-family:"微软雅黑"; font-size:20px; border:none; float:left; cursor:pointer;}
.usersbmt{ background:url(<?php echo $tag['path.skin']; ?>res/images/logbg.jpg) no-repeat; color:#fff; float:left; margin-top:15px;}
#articeBottom { font-size: 14px; margin: 6px 0 10px; padding-top: 10px; text-align: right; width: 97%;}
</style>
<script language="javascript">
<!--
function validator()
{
 	if(document.getElementById('name').value=="")
	{alert("请输入您的姓名!"); document.getElementById('name').focus(); return false;}
	if(document.getElementById('telephone').value=="")
	{alert("请输入您的联系电话!");document.getElementById('telephone').focus();return false;}
	if(document.all['content'].value=="")
	{alert("请输入您的留言内容!");document.all['content'].focus();return false;}
}
-->
</script>
<div class="guestbk">
<form id="form1" name="form1" method="post" action="<?php echo sys_href($params['id'],'form_action');?>" onsubmit="return validator()">
	<div id="guestsmt">
        	<textarea name="content" id="" cols="" rows="" class="useript guestinfo"></textarea>
            <p>
            	<span>姓名：</span><input name="name" type="text" id="name" class="useript usertel"  value=""/>
                <?php echo sys_push('','<span>{name}：</span><input name="custom[]" type="text" class="useript usertel"  value=""/>')?>
                <span>联系方式：</span><input name="contact" type="text" id="contact" class="useript usertel"  value=""/>
                <span>验证码：</span><input name="checkcode" type="text" id="checkcode" class="useript usertel"  value=""/><img src="<?php echo URLREWRITE? '/':'./'; ?>inc/verifycode.php" />
            </p>
            <p><input name="" type="submit" value="提 交" class="userbtn usersbmt" /></p>
        </div>
 </form>
</div>
<?php
if(!empty($tag['data.results']))
{
	foreach($tag['data.results'] as $k=>$data)
	{
		?>
        <p>
            <div class="ask-box ask-left">
                <div class="lwrap"><strong><?php echo $data['name'];?>问：</strong><a href="<?php echo sys_href($data['channelId'],'guestbook',$data['id'])?>"><?php echo $data['content'];?></a><br /><span><?php echo $data['dtTime'];?></span></div>
            </div>
        </p>
        <p>
            <div class="answer-box answer-right">
                <div class="rwraps">答：<?php echo $data['content1'];?></div>
            </div>
        </p>
  		<?php
	}
	?>
	<div id="articeBottom">
		<div id="apartPage"><?php if(!empty($tag['pager.cn']))echo $tag['pager.cn'];?> </div>
	</div>
	<?php

}
?>