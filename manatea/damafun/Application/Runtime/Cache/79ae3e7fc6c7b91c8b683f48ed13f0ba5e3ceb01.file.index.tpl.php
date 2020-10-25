<?php /* Smarty version Smarty-3.1.6, created on 2015-11-02 15:33:25
         compiled from "./Application/Home/View\Video\index.tpl" */ ?>
<?php /*%%SmartyHeaderCode:879856334e816c2bc3-58843953%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '79ae3e7fc6c7b91c8b683f48ed13f0ba5e3ceb01' => 
    array (
      0 => './Application/Home/View\\Video\\index.tpl',
      1 => 1446449485,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '879856334e816c2bc3-58843953',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.6',
  'unifunc' => 'content_56334e818ddd4',
  'variables' => 
  array (
    'video' => 0,
    'user' => 0,
    'comment' => 0,
    'row' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_56334e818ddd4')) {function content_56334e818ddd4($_smarty_tpl) {?><?php if (!is_callable('smarty_modifier_date_format')) include 'C:\\Lamp\\apache24\\htdocs\\damafun\\ThinkPHP\\Library\\Vendor\\Smarty\\plugins\\modifier.date_format.php';
?><?php echo $_smarty_tpl->getSubTemplate ("public/header.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

<body>

    <link rel="stylesheet" href="<?php echo @APP_RES;?>
/home/css/base.css?1" />
    <script src="<?php echo @APP_RES;?>
/home/js/CommentCoreLibrary.js"></script>
    <script src="<?php echo @APP_RES;?>
/home/js/ABPLibxml.js"></script>
    <script src="<?php echo @APP_RES;?>
/home/js/ABPlayer.js"></script>
    <script type="text/javascript">
      window.addEventListener("load",function(){
        var inst = ABP.bind(document.getElementById("ChouneyPlay"), false,"","<?php echo $_smarty_tpl->tpl_vars['video']->value['path'];?>
","<?php echo @__CONTROLLER__;?>
/sendDama");
        CommentLoader("<?php echo @APP_RES;?>
/uploads/video/info/<?php echo $_smarty_tpl->tpl_vars['video']->value['path'];?>
.xml", inst.cmManager); ///这里url最好采用绝对定位
        inst.txtText.focus();
        //时间标签的显示
    	$(".progress-bar").tooltip({
    		delay:{
    			show:0,
    			hide:0
    		},
    		animation:true,
    		placement:'top'
    	});
      });
      function sendComment(){
        if("<?php echo $_SESSION['user']['allow'];?>
"==""||"<?php echo $_SESSION['user']['allow'];?>
"==0){
          alert("您没有登录或没有权限无法发表评论");
          return ;
        }
        var comment = $("#CZ_comment");
        $.post("<?php echo @__CONTROLLER__;?>
/sendComment","cont=1&comment="+comment.val()+"&uid=<?php echo $_SESSION['user']['id'];?>
&vid=<?php echo $_smarty_tpl->tpl_vars['video']->value['id'];?>
",function(data){
          var obj = $.parseJSON(data);
          var str='<div class="row jumbotron1"><div class="col-md-12 col-md-offset-1" >#'+obj.cid+'<h4> <a href="#">'+obj.name+'</a>于'+obj.time+'说：'+obj.comment+'</h4></div></div>';
          $('.comment').append(str);
          comment.val("");
        });
      }
      </script>
<div class="container">


	<ol class="breadcrumb">
  <li><a href="#">主页></a></li>
  <li class="active">动画短片</li>
</ol>
<div class="page-header">
  <h3><?php echo $_smarty_tpl->tpl_vars['video']->value['name'];?>
 <small>上传者：<?php echo $_smarty_tpl->tpl_vars['user']->value['name'];?>
</small></h3>
  <ul class="nav nav-pills" role="tablist">
  <li role="presentation"><a href="#">点击数 <span class="badge"><?php echo $_smarty_tpl->tpl_vars['video']->value['hot'];?>
</span></a></li>
  <li role="presentation"><a href="#">评论数 <span class="badge"><?php echo $_smarty_tpl->tpl_vars['video']->value['comnumber'];?>
</span></a></li>
</ul>
</div>
<!-- 视频内容和视频简介放在左右两边 -->
<div class="row">
  <div class="col-md-12" style="height:700;">
  <div id="ChouneyPlay" class="ABP-Unit" style="width:1120px;height:630px;" tabindex="1">
      <div class="ABP-Video">
        <div class="ABP-Container"></div>
        <video id="abp-video" autobuffer="true" data-setup="{}" poster="<?php echo @APP_RES;?>
/uploads/images/<?php echo $_smarty_tpl->tpl_vars['video']->value['pic'];?>
">
          <source src="<?php echo @APP_RES;?>
/uploads/video/<?php echo $_smarty_tpl->tpl_vars['video']->value['path'];?>
" type="video/mp4">
          <!-- // END VIDEO 1-->
          <!-- START VIDEO 2
          <source src="http://media.w3.org/2010/05/sintel/trailer.mp4" type="video/mp4">
          // END VIDEO 2-->
          <!-- START VIDEO 3
          <source src="http://content.bitsontherun.com/videos/bkaovAYt-52qL9xLP.mp4" type="video/mp4">
          <source src="http://content.bitsontherun.com/videos/bkaovAYt-27m5HpIu.webm" type="video/webm">
          // END VIDEO 3-->
          <p>Your browser does not support html5 video!</p>
        </video>
        
      </div>
      <div class="ABP-Text">
      <label class="ABP-label">发送弹幕:</label>
        <input type="text">
      </div>
      <div class="ABP-Control">
        <div class="button ABP-Play" title="播放"></div>
        <label class="ABP-Time">00:00/00:00</label>
        <div class="progress-bar" data-toggle="tooltip" data-original-title="00:00">
          <div class="bar dark"></div>
          <div class="bar"></div>
        </div>
        <div class="ABP-Sound">
        	<div class="button glyphicon glyphicon-volume-up"></div>
	        <div class="progress">
			  <div class="progress-bar " role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: 60%;">
			    60%
			  </div>
			</div>
		</div>
        <div class="button ABP-CommentShow" title="关闭弹幕"></div>
        <div class="button ABP-FullScreen" title="全屏"></div>
      </div>
    </div>
  </div>

</div>
<div class="row">
   <div class="col-md-12" >
    <div class="page-header">
      <h3>视频简介  <small><?php echo $_smarty_tpl->tpl_vars['video']->value['desn'];?>
</small></h3>
    </div>
  </div>
</div>
<div class="row comment col-md-12">
      <h3>视频评论</h3>
      <?php  $_smarty_tpl->tpl_vars["row"] = new Smarty_Variable; $_smarty_tpl->tpl_vars["row"]->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['comment']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars["row"]->key => $_smarty_tpl->tpl_vars["row"]->value){
$_smarty_tpl->tpl_vars["row"]->_loop = true;
?>
      <div class="row jumbotron1">
        <div class="col-md-12 col-md-offset-1" >
        #<?php echo $_smarty_tpl->tpl_vars['row']->value['id'];?>

        <h4> <a href="#"><?php echo $_smarty_tpl->tpl_vars['row']->value['name'];?>
</a>于<?php echo smarty_modifier_date_format($_smarty_tpl->tpl_vars['row']->value['time'],"%Y-%m-%d %H-%M-%S");?>
说：
          <?php echo $_smarty_tpl->tpl_vars['row']->value['comment'];?>

          </h4>
        </div>
      </div>
      <?php } ?>
</div>
<div class="row">
  <div class="col-md-12">
<textarea  rows="4" cols="40" id="CZ_comment" style="resize:none;width:100%;">
发表评论
</textarea>
<button type="button" onclick="sendComment();" class="btn btn-info btn-sm" >提交</button>
  </div>
</div>
</div>
</body>
<?php echo $_smarty_tpl->getSubTemplate ("public/footer.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>
<?php }} ?>