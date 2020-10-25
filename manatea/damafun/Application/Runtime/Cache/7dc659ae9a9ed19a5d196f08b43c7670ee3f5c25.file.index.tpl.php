<?php /* Smarty version Smarty-3.1.6, created on 2015-10-30 14:13:27
         compiled from "./Application/Home/View\Index\index.tpl" */ ?>
<?php /*%%SmartyHeaderCode:179935633055f596938-19183286%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '7dc659ae9a9ed19a5d196f08b43c7670ee3f5c25' => 
    array (
      0 => './Application/Home/View\\Index\\index.tpl',
      1 => 1446185328,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '179935633055f596938-19183286',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.6',
  'unifunc' => 'content_5633055f73891',
  'variables' => 
  array (
    'video' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5633055f73891')) {function content_5633055f73891($_smarty_tpl) {?><?php echo $_smarty_tpl->getSubTemplate ("public/header.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

<script>
$(function(){
   $(".case_li").hover(function(){
      $(".case_li_txt",this).stop().animate({top:"80px"},{queue:false,duration:160});
	  $(".case_li_txt",this).css("background-color","#000000");
	  $(".case_li_txt .span_mr_txt",this).attr("class","span_font");
   },function(){
      $(".case_li_txt",this).stop().animate({top:"95px"},{queue:false,duration:160});
	  $(".case_li_txt",this).css("background-color","#eee");
	  $(".case_li_txt .span_font",this).attr("class","span_mr_txt");
   })
})
</script>
	<div class="container">

		<!-- 缩略图的形式显示下面的图片以及轮播效果 分为左中右三个部分 -->
		<div class="row">
			<div class="col-md-6">
				<div class="jumbotron" style="margin-bottom:0px;padding-top:30px;padding-bottom:30px;">
					<!-- 此处为轮播效果  用法见http://v3.bootcss.com/javascript/#carousel-examples-->
					<div id="carousel-example-generic" class="carousel slide"
						data-ride="carousel">
						<!-- Indicators -->
						<ol class="carousel-indicators">
							<li data-target="#carousel-example-generic" data-slide-to="0"
								class="active"></li>
							<li data-target="#carousel-example-generic" data-slide-to="1"></li>
							<li data-target="#carousel-example-generic" data-slide-to="2"></li>
						</ol>
						<!-- 轮播图片 -->
							<div id="myCarousel" class="carousel-inner">
								<div class="item active">
									<img src="<?php echo @APP_RES;?>
/images/Hydrangeas.jpg"></img>
								</div>
								<div class="item">
									<img src="<?php echo @APP_RES;?>
/images/Koala.jpg"></img>
								</div>
								<div class="item ">
									<img src="<?php echo @APP_RES;?>
/images/Tulips.jpg"></img>
								</div>
							</div>
							<a class="carousel-control left" href="#carousel-example-generic" data-slide="prev">&lsaquo;</a>
  							<a class="carousel-control right" href="#carousel-example-generic" data-slide="next">&rsaquo;</a>
					</div>
				</div>
				<?php if (isset($_smarty_tpl->tpl_vars['smarty']->value['section']['row'])) unset($_smarty_tpl->tpl_vars['smarty']->value['section']['row']);
$_smarty_tpl->tpl_vars['smarty']->value['section']['row']['name'] = 'row';
$_smarty_tpl->tpl_vars['smarty']->value['section']['row']['loop'] = is_array($_loop=$_smarty_tpl->tpl_vars['video']->value) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$_smarty_tpl->tpl_vars['smarty']->value['section']['row']['start'] = (int)0;
$_smarty_tpl->tpl_vars['smarty']->value['section']['row']['max'] = (int)2;
$_smarty_tpl->tpl_vars['smarty']->value['section']['row']['step'] = ((int)1) == 0 ? 1 : (int)1;
$_smarty_tpl->tpl_vars['smarty']->value['section']['row']['show'] = true;
if ($_smarty_tpl->tpl_vars['smarty']->value['section']['row']['max'] < 0)
    $_smarty_tpl->tpl_vars['smarty']->value['section']['row']['max'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['row']['loop'];
if ($_smarty_tpl->tpl_vars['smarty']->value['section']['row']['start'] < 0)
    $_smarty_tpl->tpl_vars['smarty']->value['section']['row']['start'] = max($_smarty_tpl->tpl_vars['smarty']->value['section']['row']['step'] > 0 ? 0 : -1, $_smarty_tpl->tpl_vars['smarty']->value['section']['row']['loop'] + $_smarty_tpl->tpl_vars['smarty']->value['section']['row']['start']);
else
    $_smarty_tpl->tpl_vars['smarty']->value['section']['row']['start'] = min($_smarty_tpl->tpl_vars['smarty']->value['section']['row']['start'], $_smarty_tpl->tpl_vars['smarty']->value['section']['row']['step'] > 0 ? $_smarty_tpl->tpl_vars['smarty']->value['section']['row']['loop'] : $_smarty_tpl->tpl_vars['smarty']->value['section']['row']['loop']-1);
if ($_smarty_tpl->tpl_vars['smarty']->value['section']['row']['show']) {
    $_smarty_tpl->tpl_vars['smarty']->value['section']['row']['total'] = min(ceil(($_smarty_tpl->tpl_vars['smarty']->value['section']['row']['step'] > 0 ? $_smarty_tpl->tpl_vars['smarty']->value['section']['row']['loop'] - $_smarty_tpl->tpl_vars['smarty']->value['section']['row']['start'] : $_smarty_tpl->tpl_vars['smarty']->value['section']['row']['start']+1)/abs($_smarty_tpl->tpl_vars['smarty']->value['section']['row']['step'])), $_smarty_tpl->tpl_vars['smarty']->value['section']['row']['max']);
    if ($_smarty_tpl->tpl_vars['smarty']->value['section']['row']['total'] == 0)
        $_smarty_tpl->tpl_vars['smarty']->value['section']['row']['show'] = false;
} else
    $_smarty_tpl->tpl_vars['smarty']->value['section']['row']['total'] = 0;
if ($_smarty_tpl->tpl_vars['smarty']->value['section']['row']['show']):

            for ($_smarty_tpl->tpl_vars['smarty']->value['section']['row']['index'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['row']['start'], $_smarty_tpl->tpl_vars['smarty']->value['section']['row']['iteration'] = 1;
                 $_smarty_tpl->tpl_vars['smarty']->value['section']['row']['iteration'] <= $_smarty_tpl->tpl_vars['smarty']->value['section']['row']['total'];
                 $_smarty_tpl->tpl_vars['smarty']->value['section']['row']['index'] += $_smarty_tpl->tpl_vars['smarty']->value['section']['row']['step'], $_smarty_tpl->tpl_vars['smarty']->value['section']['row']['iteration']++):
$_smarty_tpl->tpl_vars['smarty']->value['section']['row']['rownum'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['row']['iteration'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['row']['index_prev'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['row']['index'] - $_smarty_tpl->tpl_vars['smarty']->value['section']['row']['step'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['row']['index_next'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['row']['index'] + $_smarty_tpl->tpl_vars['smarty']->value['section']['row']['step'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['row']['first']      = ($_smarty_tpl->tpl_vars['smarty']->value['section']['row']['iteration'] == 1);
$_smarty_tpl->tpl_vars['smarty']->value['section']['row']['last']       = ($_smarty_tpl->tpl_vars['smarty']->value['section']['row']['iteration'] == $_smarty_tpl->tpl_vars['smarty']->value['section']['row']['total']);
?>
				<div class="col-md-5 col-md-offset-1 ">
					<div class="jumbotron case_li" style="padding:0px; ">
						<a href="<?php echo @__MODULE__;?>
/video/index/vid/<?php echo $_smarty_tpl->tpl_vars['video']->value[$_smarty_tpl->getVariable('smarty')->value['section']['row']['index']]['id'];?>
"><img src="<?php echo @APP_RES;?>
/uploads/images/<?php echo $_smarty_tpl->tpl_vars['video']->value[$_smarty_tpl->getVariable('smarty')->value['section']['row']['index']]['pic'];?>
" /></a>
						<!-- 视频标题求特技 -->
						<div class="case_li_txt">
					      <div class="span_mr_txt"><?php echo $_smarty_tpl->tpl_vars['video']->value[$_smarty_tpl->getVariable('smarty')->value['section']['row']['index']]['name'];?>
</div>
						  <div class="span_mr_txt">点击量：<?php echo $_smarty_tpl->tpl_vars['video']->value[$_smarty_tpl->getVariable('smarty')->value['section']['row']['index']]['hot'];?>
&nbsp;&nbsp;&nbsp;回复：<?php echo $_smarty_tpl->tpl_vars['video']->value[$_smarty_tpl->getVariable('smarty')->value['section']['row']['index']]['comnumber'];?>
</div>
						</div>
					</div>
				</div>

				<?php endfor; endif; ?>
<!-- 				<div class="col-md-6">
					<div class="jumbotron">
						<h3>Hello, world</h3>
					</div>
				</div> -->
			</div>
			<div class="col-md-3">
				<?php if (isset($_smarty_tpl->tpl_vars['smarty']->value['section']['row'])) unset($_smarty_tpl->tpl_vars['smarty']->value['section']['row']);
$_smarty_tpl->tpl_vars['smarty']->value['section']['row']['name'] = 'row';
$_smarty_tpl->tpl_vars['smarty']->value['section']['row']['loop'] = is_array($_loop=$_smarty_tpl->tpl_vars['video']->value) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$_smarty_tpl->tpl_vars['smarty']->value['section']['row']['start'] = (int)2;
$_smarty_tpl->tpl_vars['smarty']->value['section']['row']['max'] = (int)3;
$_smarty_tpl->tpl_vars['smarty']->value['section']['row']['step'] = ((int)1) == 0 ? 1 : (int)1;
$_smarty_tpl->tpl_vars['smarty']->value['section']['row']['show'] = true;
if ($_smarty_tpl->tpl_vars['smarty']->value['section']['row']['max'] < 0)
    $_smarty_tpl->tpl_vars['smarty']->value['section']['row']['max'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['row']['loop'];
if ($_smarty_tpl->tpl_vars['smarty']->value['section']['row']['start'] < 0)
    $_smarty_tpl->tpl_vars['smarty']->value['section']['row']['start'] = max($_smarty_tpl->tpl_vars['smarty']->value['section']['row']['step'] > 0 ? 0 : -1, $_smarty_tpl->tpl_vars['smarty']->value['section']['row']['loop'] + $_smarty_tpl->tpl_vars['smarty']->value['section']['row']['start']);
else
    $_smarty_tpl->tpl_vars['smarty']->value['section']['row']['start'] = min($_smarty_tpl->tpl_vars['smarty']->value['section']['row']['start'], $_smarty_tpl->tpl_vars['smarty']->value['section']['row']['step'] > 0 ? $_smarty_tpl->tpl_vars['smarty']->value['section']['row']['loop'] : $_smarty_tpl->tpl_vars['smarty']->value['section']['row']['loop']-1);
if ($_smarty_tpl->tpl_vars['smarty']->value['section']['row']['show']) {
    $_smarty_tpl->tpl_vars['smarty']->value['section']['row']['total'] = min(ceil(($_smarty_tpl->tpl_vars['smarty']->value['section']['row']['step'] > 0 ? $_smarty_tpl->tpl_vars['smarty']->value['section']['row']['loop'] - $_smarty_tpl->tpl_vars['smarty']->value['section']['row']['start'] : $_smarty_tpl->tpl_vars['smarty']->value['section']['row']['start']+1)/abs($_smarty_tpl->tpl_vars['smarty']->value['section']['row']['step'])), $_smarty_tpl->tpl_vars['smarty']->value['section']['row']['max']);
    if ($_smarty_tpl->tpl_vars['smarty']->value['section']['row']['total'] == 0)
        $_smarty_tpl->tpl_vars['smarty']->value['section']['row']['show'] = false;
} else
    $_smarty_tpl->tpl_vars['smarty']->value['section']['row']['total'] = 0;
if ($_smarty_tpl->tpl_vars['smarty']->value['section']['row']['show']):

            for ($_smarty_tpl->tpl_vars['smarty']->value['section']['row']['index'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['row']['start'], $_smarty_tpl->tpl_vars['smarty']->value['section']['row']['iteration'] = 1;
                 $_smarty_tpl->tpl_vars['smarty']->value['section']['row']['iteration'] <= $_smarty_tpl->tpl_vars['smarty']->value['section']['row']['total'];
                 $_smarty_tpl->tpl_vars['smarty']->value['section']['row']['index'] += $_smarty_tpl->tpl_vars['smarty']->value['section']['row']['step'], $_smarty_tpl->tpl_vars['smarty']->value['section']['row']['iteration']++):
$_smarty_tpl->tpl_vars['smarty']->value['section']['row']['rownum'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['row']['iteration'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['row']['index_prev'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['row']['index'] - $_smarty_tpl->tpl_vars['smarty']->value['section']['row']['step'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['row']['index_next'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['row']['index'] + $_smarty_tpl->tpl_vars['smarty']->value['section']['row']['step'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['row']['first']      = ($_smarty_tpl->tpl_vars['smarty']->value['section']['row']['iteration'] == 1);
$_smarty_tpl->tpl_vars['smarty']->value['section']['row']['last']       = ($_smarty_tpl->tpl_vars['smarty']->value['section']['row']['iteration'] == $_smarty_tpl->tpl_vars['smarty']->value['section']['row']['total']);
?>
					<div class="jumbotron case_li" style="padding:0px;" >
						<a href="<?php echo @__MODULE__;?>
/video/index/vid/<?php echo $_smarty_tpl->tpl_vars['video']->value[$_smarty_tpl->getVariable('smarty')->value['section']['row']['index']]['id'];?>
"><img src="<?php echo @APP_RES;?>
/uploads/images/<?php echo $_smarty_tpl->tpl_vars['video']->value[$_smarty_tpl->getVariable('smarty')->value['section']['row']['index']]['pic'];?>
" /></a>
						<!-- 视频标题求特技 -->
						 <div class="case_li_txt">
					      <div class="span_mr_txt"><?php echo $_smarty_tpl->tpl_vars['video']->value[$_smarty_tpl->getVariable('smarty')->value['section']['row']['index']]['name'];?>
</div>
						  <div class="span_mr_txt">点击量：<?php echo $_smarty_tpl->tpl_vars['video']->value[$_smarty_tpl->getVariable('smarty')->value['section']['row']['index']]['hot'];?>
&nbsp;&nbsp;&nbsp;&nbsp;回复数：<?php echo $_smarty_tpl->tpl_vars['video']->value[$_smarty_tpl->getVariable('smarty')->value['section']['row']['index']]['comnumber'];?>
</div>
						</div>
					</div>
				<?php endfor; endif; ?>
			</div>
			<div class="col-md-3">
				<?php if (isset($_smarty_tpl->tpl_vars['smarty']->value['section']['row'])) unset($_smarty_tpl->tpl_vars['smarty']->value['section']['row']);
$_smarty_tpl->tpl_vars['smarty']->value['section']['row']['name'] = 'row';
$_smarty_tpl->tpl_vars['smarty']->value['section']['row']['loop'] = is_array($_loop=$_smarty_tpl->tpl_vars['video']->value) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$_smarty_tpl->tpl_vars['smarty']->value['section']['row']['start'] = (int)5;
$_smarty_tpl->tpl_vars['smarty']->value['section']['row']['max'] = (int)2;
$_smarty_tpl->tpl_vars['smarty']->value['section']['row']['step'] = ((int)1) == 0 ? 1 : (int)1;
$_smarty_tpl->tpl_vars['smarty']->value['section']['row']['show'] = true;
if ($_smarty_tpl->tpl_vars['smarty']->value['section']['row']['max'] < 0)
    $_smarty_tpl->tpl_vars['smarty']->value['section']['row']['max'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['row']['loop'];
if ($_smarty_tpl->tpl_vars['smarty']->value['section']['row']['start'] < 0)
    $_smarty_tpl->tpl_vars['smarty']->value['section']['row']['start'] = max($_smarty_tpl->tpl_vars['smarty']->value['section']['row']['step'] > 0 ? 0 : -1, $_smarty_tpl->tpl_vars['smarty']->value['section']['row']['loop'] + $_smarty_tpl->tpl_vars['smarty']->value['section']['row']['start']);
else
    $_smarty_tpl->tpl_vars['smarty']->value['section']['row']['start'] = min($_smarty_tpl->tpl_vars['smarty']->value['section']['row']['start'], $_smarty_tpl->tpl_vars['smarty']->value['section']['row']['step'] > 0 ? $_smarty_tpl->tpl_vars['smarty']->value['section']['row']['loop'] : $_smarty_tpl->tpl_vars['smarty']->value['section']['row']['loop']-1);
if ($_smarty_tpl->tpl_vars['smarty']->value['section']['row']['show']) {
    $_smarty_tpl->tpl_vars['smarty']->value['section']['row']['total'] = min(ceil(($_smarty_tpl->tpl_vars['smarty']->value['section']['row']['step'] > 0 ? $_smarty_tpl->tpl_vars['smarty']->value['section']['row']['loop'] - $_smarty_tpl->tpl_vars['smarty']->value['section']['row']['start'] : $_smarty_tpl->tpl_vars['smarty']->value['section']['row']['start']+1)/abs($_smarty_tpl->tpl_vars['smarty']->value['section']['row']['step'])), $_smarty_tpl->tpl_vars['smarty']->value['section']['row']['max']);
    if ($_smarty_tpl->tpl_vars['smarty']->value['section']['row']['total'] == 0)
        $_smarty_tpl->tpl_vars['smarty']->value['section']['row']['show'] = false;
} else
    $_smarty_tpl->tpl_vars['smarty']->value['section']['row']['total'] = 0;
if ($_smarty_tpl->tpl_vars['smarty']->value['section']['row']['show']):

            for ($_smarty_tpl->tpl_vars['smarty']->value['section']['row']['index'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['row']['start'], $_smarty_tpl->tpl_vars['smarty']->value['section']['row']['iteration'] = 1;
                 $_smarty_tpl->tpl_vars['smarty']->value['section']['row']['iteration'] <= $_smarty_tpl->tpl_vars['smarty']->value['section']['row']['total'];
                 $_smarty_tpl->tpl_vars['smarty']->value['section']['row']['index'] += $_smarty_tpl->tpl_vars['smarty']->value['section']['row']['step'], $_smarty_tpl->tpl_vars['smarty']->value['section']['row']['iteration']++):
$_smarty_tpl->tpl_vars['smarty']->value['section']['row']['rownum'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['row']['iteration'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['row']['index_prev'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['row']['index'] - $_smarty_tpl->tpl_vars['smarty']->value['section']['row']['step'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['row']['index_next'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['row']['index'] + $_smarty_tpl->tpl_vars['smarty']->value['section']['row']['step'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['row']['first']      = ($_smarty_tpl->tpl_vars['smarty']->value['section']['row']['iteration'] == 1);
$_smarty_tpl->tpl_vars['smarty']->value['section']['row']['last']       = ($_smarty_tpl->tpl_vars['smarty']->value['section']['row']['iteration'] == $_smarty_tpl->tpl_vars['smarty']->value['section']['row']['total']);
?>
					<div class="jumbotron case_li" style="padding:0px;">
						<a href="<?php echo @__MODULE__;?>
/video/index/vid/<?php echo $_smarty_tpl->tpl_vars['video']->value[$_smarty_tpl->getVariable('smarty')->value['section']['row']['index']]['id'];?>
"><img src="<?php echo @APP_RES;?>
/uploads/images/<?php echo $_smarty_tpl->tpl_vars['video']->value[$_smarty_tpl->getVariable('smarty')->value['section']['row']['index']]['pic'];?>
" /></a>
						<!-- 视频标题求特技 -->
						 <div class="case_li_txt">
					      <div class="span_mr_txt"><?php echo $_smarty_tpl->tpl_vars['video']->value[$_smarty_tpl->getVariable('smarty')->value['section']['row']['index']]['name'];?>
</div>
						  <div class="span_mr_txt">点击量：<?php echo $_smarty_tpl->tpl_vars['video']->value[$_smarty_tpl->getVariable('smarty')->value['section']['row']['index']]['hot'];?>
&nbsp;&nbsp;&nbsp;&nbsp;回复数：<?php echo $_smarty_tpl->tpl_vars['video']->value[$_smarty_tpl->getVariable('smarty')->value['section']['row']['index']]['comnumber'];?>
</div>
						</div>
					</div>
				<?php endfor; endif; ?>
				<div class="jumbotron col-md-9">
					<h3>Hello, world!</h3>
					<p>...</p>
					<p>...</p>
					<p>...</p>
					<p>...</p>
				</div>
			</div>

		</div>
	</div>

</body>
<?php echo $_smarty_tpl->getSubTemplate ("public/footer.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>
<?php }} ?>