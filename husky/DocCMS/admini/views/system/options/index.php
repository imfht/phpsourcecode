<style type="text/css">
.txt { width:400px; font-size:12px; }
#tabs5 { text-align:left; border:none; }
.menu5box { position:relative; overflow:hidden; height:30px; text-align:left; }
.menu5box .savetab { float:right; }
#menu5 { position:absolute; top:0; left:0; z-index:1; }
#menu5 li { float:left; display:block; cursor:pointer; width:82px; text-align:center; line-height:29px; height:30px; cursor:pointer; }
#menu5 li a { text-decoration:none; color:#0099FF; }
#menu5 li.hover { background:#F5F9FD; height:29px; width:80px; border-left:1px solid #ccc; border-top:1px solid #ccc; border-right:1px solid #ccc; }
.main5box { clear:both; margin-top:-1px; border:1px solid #ccc; background:#F5F9FD; }
#main5 ul { display: none; padding:18px 0; }
#main5 ul.block { display: block; }
.small { color:#999; font-size:12px; font-weight:normal; text-align:right; width:210px; padding-left:10px }
.mail tr { height:30px }
</style>
<link rel="STYLESHEET" type="text/css" href="<?php echo get_root_path()?>/inc/css/colorPicker/dhtmlxcolorpicker.css">
<script type="text/javascript">
<!--
	window.dhx_globalImgPath="<?php echo get_root_path()?>/inc/img/colorPicker/";
-->
</script>
<script type="text/javascript" src="<?php echo get_root_path()?>/inc/js/colorPicker/dhtmlxcolorpicker.js"></script>
<script type="text/javascript">
<!--
function setTab(m,n){
var tli=document.getElementById("menu"+m).getElementsByTagName("li");
var mli=document.getElementById("main"+m).getElementsByTagName("ul");
for(i=0;i<mli.length;i++){
   tli[i].className=i==n?"hover":"";
   mli[i].style.display=i==n?"block":"none";
}
}
</script>
<div class="location">当前位置: <a href="./index.php">首 页</a> → <a href="./index.php?m=system&s=managechannel">操作员后台</a> → <a href="./index.php?m=system&s=options">站点设置</a></div>
<div id="tabs5">
<div class="menu5box">
  <ul id="menu5">
    <li class="hover" onclick="setTab(5,0)">基本设置</li>
    <li onclick="setTab(5,1)">高级设置</li>
    <li onclick="setTab(5,2)">邮箱设置</li>
    <li onclick="setTab(5,3)">支付宝接口</li>
    <li onclick="setTab(5,4)">财付通接口</li>
  </ul>
  <div class="savetab">
    <input name="saveme" type="button" onclick="form1.submit()" class="savebt" value=" 保存设置 " />
  </div>
</div>
<div class="main5box">
  <div class="main" id="main5">
    <form name="form1" method="POST" action="./index.php?m=system&s=options&a=save" enctype="multipart/form-data">
      <ul class="block">
        <li>
          <table width="100%" border="0" cellpadding="0" cellspacing="0">
            <tr>
              <td>
			  <table width="96%" border="0" align="center" cellpadding="0" cellspacing="0">
                  <tr>
                    <td width="200">站点开启：</td>
                    <td colspan="2"><input name="webopen" type="checkbox"  id="weburl" <?php echo WEBOPEN?'checked="checked"':'';?>>
                      用来设置是否开启网站</td>
                  </tr>
                  <tr>
                    <td>站点地址：</td>
                    <td colspan="2"><input name="weburl" type="text" class="txt" id="weburl" value="<?php echo WEBURL ?>" size="41" /></td>
                  </tr>
                  <tr>
                    <td>站点标题：</td>
                    <td colspan="2"><input name="sitename" type="text" class="txt" id="sitename" value="<?php echo htmlspecialchars(stripslashes(SITENAME)) ?>" size="41" />
                      <a href="http://www.doccms.com/seo/#biaoti" target="_blank"><img src="./images/help.gif" alt="点击了解如何优化网站标题？" border="0" /></a></td>
                  </tr>
                  <tr>
                    <td>站点关键词：</td>
                    <td colspan="2"><textarea name="sitekeywords" id="sitekeywords" class="txt" cols="27" rows="3"><?php echo htmlspecialchars(stripslashes(SITEKEYWORDS)) ?></textarea>
                      <a href="http://www.doccms.com/seo/#guanjianzi" target="_blank"><img src="./images/help.gif" alt="如何写站点关键词？" border="0" /></a></td>
                  </tr>
                  <tr>
                    <td>站点摘要：</td>
                    <td colspan="2"><textarea name="sitesummary" id="sitesummary" class="txt" cols="27" rows="3"><?php echo htmlspecialchars(stripslashes(SITESUMMARY)) ?></textarea>
                      <a href="http://www.doccms.com/seo/#zhaiyao" target="_blank"><img src="./images/help.gif" alt="如何写站点摘要描述？" border="0" /></a></td>
                  </tr>
                  <tr>
                    <td>站点资源文件上传路径：</td>
                    <td colspan="2"><input name="uploadpath" type="text" class="txt" id="uploadpath" value="<?php echo UPLOADPATH ?>" size="41" />
                      <span class="small">设置上传文件的路径(一般不用更改)</span></td>
                  </tr>
                  <tr>
                    <td>站点根路径：</td>
                    <td colspan="2"><input name="rootpath" type="text" class="txt" id="rootpath" value="<?php echo ROOTPATH ?>" size="41" />
                      <span class="small">如果您的网站未安装在站点根目录下,请设置,如果在根目录下请留空.例如 /xmlol (注意后面不带 /)</span></td>
                  </tr>
                  <tr>
                    <td>服务器时区设置：</td>
                    <td colspan="2"><input name="timeZoneName" type="text" class="txt" id="timeZoneName" value="<?php echo TIMEZONENAME ?>" size="41" /></td>
                  </tr>
                  <tr>
                    <td>服务器空间站点大小：</td>
                    <td colspan="2"><input name="websize" type="text" class="txt" id="websize" value="<?php echo WEBSIZE ?>" style="width:100px;"/>
                      MB</td>
                  </tr>
                  <tr>
                    <td colspan="3"><hr></td>
                  </tr>
                  <tr>
                    <td>站点页面永久路径[伪静态路径]：</td>
                   
                    <td colspan="2"><input name="urlrewrite" <?php if (URLREWRITE)echo 'checked="checked"';?> type="checkbox" id="urlrewrite" />
                      用来对搜索引擎优化<span class="small">（*此功能需要把程序放在站点根目录并且需要服务器环境支持URL重写功能,否则此功能将无法使用）</span> <a href="http://www.doccms.com/seo/#jitaihua" target="_blank"><img src="./images/help.gif" alt="点击了解更多" border="0" /></a></td>

                  </tr>
                  <tr>
                    <td colspan="3"><hr></td>
                  </tr>
                  
                  <tr>
                    <td>站点页面HTML静态缓存目录设置：</td>
                    <td colspan="2"> &nbsp;&nbsp;&nbsp;&nbsp;<input name="htmlpath" type="text" class="txt" id="cachetime" value="<?php echo HTMLPATH?>" style="width:100px;"/>
                      <span class="small">(*填写此选项以选择生成的静态文件的所在目录,根目录则填写 / ) </span>
                  </tr>
                  <tr>
                    <td>站点页面HTML静态缓存更新设置：</td>
                    <td colspan="2">每 <input name="cachetime" type="text" class="txt" id="cachetime" value="<?php echo CACHETIME?CACHETIME/(3600*24):'0'; ?>" style="width:100px;"/>
                      天 自动更新<span class="small">(*填写此选项可以在用户访问前台页面后自动更新静态页面，填0 则默认不自动更新静态页面) </span>
                  </tr>
                  <tr>
                    <td colspan="3"><hr></td>
                  </tr>
                  <tr>
                    <td>是否使用图片水印：</td>
                    <td colspan="2"><input type="checkbox" name="iswater" id="iswater" <?php echo ISWATER?'checked="true"':''?>>
                      <span class="small">勾选此项为网站上传图片时自动添加水印功能</span></td>
                  </tr>
                  <tr>
                    <td>图片水印上传：</td>
                    <td colspan="2"><input type="file" name="waterimgs" id="waterimgs" >
                      &nbsp;<img src="<?php echo WATERIMGS?>"  align="absmiddle" width="140" height="50" <?php echo !WATERIMGS?'style="display:none;"':''?>/></td>
                  </tr>
                  <tr>
                    <td>上传图片缩略图背景颜色设置：</td>
                    <td colspan="2"><input type="text" name="paint_bgcolor" id="btn_out_color" onclick="init1('btn_out_color',280,390);" value="<?php echo paint_bgcolor?>">
                      &nbsp;<img src="<?php echo get_root_path()?>/inc/img/colorPicker//datePicker.gif" onclick="init1('btn_out_color',400,340);" align="absmiddle" height="22" width="16" /></td>
                    <script type="text/javascript">
						function init1(e,w,d){
						var z=new dhtmlXColorPicker();
						z.init();
						z.setPosition(w,d);
						z.setOnSelectHandler(function(color){
							document.getElementById(e).value=color;
						});	}
					</script> 
                  </tr>
                  <tr>
                    <td colspan="3"><hr></td>
                  </tr>
              </table>
			  </td>
            </tr>
          </table>
        </li>
      </ul>
      <ul>
        <li>
          <table width="100%" border="0" cellpadding="0" cellspacing="0">
            <tr>
              <td>		
			  <table width="96%" border="0" align="center" cellpadding="0" cellspacing="0">
               <tr>
				  <td width="200">开启百度Ping插件：</td>
				  <td colspan="2"><input name="docping" <?php echo DOCPING===true?'checked="checked"':''?> type="checkbox" id="guestbookauditing" onclick="return confirm('开启此选项前需要您拥有一个RSS订阅类型的栏目，如果还没有的话，请先至设置频道菜单中创建一个RSS类型的栏目后再开启。');"/>
					勾选此项开启在添加新闻、产品等数据的时候系统会自动把跟新的页面地址和RSS提交至百度，提醒百度您的网站有更新，加快您的网页收录速度<br /><span class="small">（*开启此选项前需要您拥有一个RSS订阅类型的栏目，如果还没有的话，<a href="./index.php?m=system&s=managechannel&a=create" style="color:#F00">点击此处</a>创建RSS订阅类型的频道栏目。）</span></td>	  
				</tr>
                <tr>
				  <td colspan="3"><hr></td>
				</tr>
				<tr>
				  <td width="200">留言审核发布：</td>
				  <?php if (GUESTBOOKAUDITING){?>
				  <td colspan="2"><input name="guestbookauditing" checked="checked" type="checkbox" id="guestbookauditing" />
					用户留言模块审核<span class="small">（*当为选中状态时表示用户在前台留言的内容需经管理员审核后才能出现，否则为及时留言及时出现）</span></td>
				  <?php }else{ ?>
				  <td colspan="2"><input name="guestbookauditing" type="checkbox" id="guestbookauditing" />
					用户留言模块审核<span class="small">（*当为选中状态时表示用户在前台留言的内容需经管理员审核后才能出现，否则为及时留言及时出现）</span></td>
				  <?php }?>
				</tr>
				<tr>
				  <td height="50">评论审核发布：</td>
				  <?php if (COMMENTAUDITING){?>
				  <td colspan="2"><input name="commentauditing" checked="checked" type="checkbox" id="commentauditing" />
					用户评论模块审核<span class="small">（*当为选中状态时表示用户在前台评论的内容需经管理员审核后才能出现，否则为即时评论即时出现）</span></td>
				  <?php }else{ ?>
				  <td colspan="2"><input name="commentauditing" type="checkbox" id="commentauditing" />
					用户评论模块审核<span class="small">（*当为选中状态时表示用户在前台评论的内容需经管理员审核后才能出现，否则为及时评论及时出现）</span></td>
				  <?php }?>
				</tr>
				<tr>
				  <td colspan="3"><hr></td>
				</tr>
				<tr>
				  <td>编辑器类型：</td>
				  <td colspan="2"><?php
			$editor_arr = array('kindeditor'=>'KindEditor','ueditor'=>'UEditor');
			select($editor_arr,'editorstyle',EDITORSTYLE);
			?></td>
				</tr>
				<tr>
				  <td colspan="3"><hr></td>
				</tr>
				<tr>
				  <td rowspan="4"><p><br />
					  首页调用图片尺寸设置：</p></td>
				  <td width="70" height="30"><strong>图文：</strong></td>
				  <td> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;宽&nbsp;&nbsp;
					<input name="articleWidth" type="text" size="4" maxlength="4" id="articleWidth" class="txt" style="width:100px;" value="<?php echo articleWidth ?>"/>
					&nbsp;像素&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;高&nbsp;&nbsp;
					<input name="articleHight" type="text" size="4" maxlength="4" id="articleHight" class="txt" style="width:100px;" value="<?php echo articleHight ?>" />
					像素 </td>
				</tr>
				<tr>
				  <td height="30"><strong>新闻：</strong></td>
				  <td> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;宽&nbsp;&nbsp;
					<input name="listWidth" type="text" size="4" maxlength="4" id="listWidth" class="txt" style="width:100px;" value="<?php echo listWidth ?>"/>
					&nbsp;像素&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;高&nbsp;&nbsp;
					<input name="listHight" type="text" size="4" maxlength="4" id="listHight" class="txt" style="width:100px;" value="<?php echo listHight ?>"/>
					像素 </td>
				</tr>
				<tr>
				  <td height="30"><strong>产品：</strong></td>
				  <td> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;宽&nbsp;&nbsp;
					<input name="productWidth" type="text" size="4" maxlength="4" id="productWidth" class="txt" style="width:100px;" value="<?php echo productWidth ?>"/>
					&nbsp;像素&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;高&nbsp;&nbsp;
					<input name="productHight" type="text" size="4" maxlength="4" id="productHight" class="txt" style="width:100px;" value="<?php echo productHight ?>"/>
					像素 </td>
				</tr>
				<tr>
				  <td height="30"><strong>图片：</strong></td>
				  <td> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;宽&nbsp;&nbsp;
					<input name="pictureWidth" type="text" size="4" maxlength="4" id="pictureWidth" class="txt" style="width:100px;" value="<?php echo pictureWidth ?>"/>
					&nbsp;像素&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;高&nbsp;&nbsp;
					<input name="pictureHight" type="text" size="4" maxlength="4" id="pictureHight" class="txt" style="width:100px;" value="<?php echo pictureHight ?>"/>
					像素 </td>
				</tr>
				<tr>
				  <td colspan="3"><hr></td>
				</tr>
				<tr>
				  <td rowspan="6"><p><br />
					  内容页模块列表图片调用尺寸设置：</p></td>
				  <td width="70" height="60" rowspan="2"><strong>产品：</strong></td>
				  <td> &nbsp;&nbsp;&nbsp;中图宽&nbsp;&nbsp;
				  <input name="productMiddlePicWidth" type="text" size="4" id="productMiddlePicWidth" class="txt" style="width:100px;" value="<?php echo productMiddlePicWidth ?>"/>
				  &nbsp;像素
				  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;高&nbsp;&nbsp;
				  <input name="productMiddlePicHight" type="text" size="4" id="productMiddlePicHight" class="txt" style="width:100px;" value="<?php echo productMiddlePicHight ?>" />
				  像素 </td>
				</tr>
				<tr>
				  <td> &nbsp;&nbsp;&nbsp;小图宽&nbsp;&nbsp;
				  <input name="productSmallPicWidth" type="text" size="4" id="productSmallPicWidth" class="txt" style="width:100px;" value="<?php echo productSmallPicWidth ?>"/>
				  &nbsp;像素
				  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;高&nbsp;&nbsp;
				  <input name="productSmallPicHight" type="text" size="4" id="productSmallPicHight" class="txt" style="width:100px;" value="<?php echo productSmallPicHight ?>" />
				  像素 </td>
				</tr>
				<tr>
				  <td width="70" height="60" rowspan="2"><strong>图片：</strong></td>
				  <td> &nbsp;&nbsp;&nbsp;中图宽&nbsp;&nbsp;
				  <input name="pictureMiddlePicWidth" type="text" size="4" id="pictureMiddlePicWidth" class="txt" style="width:100px;" value="<?php echo pictureMiddlePicWidth ?>"/>
				  &nbsp;像素
				  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;高&nbsp;&nbsp;
				  <input name="pictureMiddlePicHight" type="text" size="4" id="pictureMiddlePicHight" class="txt" style="width:100px;" value="<?php echo pictureMiddlePicHight ?>" />
				  像素 </td>
			    </tr>
			    <tr>
				  <td> &nbsp;&nbsp;&nbsp;小图宽&nbsp;&nbsp;
				  <input name="pictureSmallPicWidth" type="text" size="4" id="pictureSmallPicWidth" class="txt" style="width:100px;" value="<?php echo pictureSmallPicWidth ?>"/>
				  &nbsp;像素
				  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;高&nbsp;&nbsp;
				  <input name="pictureSmallPicHight" type="text" size="4" id="pictureSmallPicHight" class="txt" style="width:100px;" value="<?php echo pictureSmallPicHight ?>" />
				  像素 </td>
				</tr>
				<tr>
				  <td width="70" height="30"><strong>视频：</strong></td>
				  <td> &nbsp;&nbsp;&nbsp;封面宽&nbsp;&nbsp;
				  <input name="videoWidth" type="text" size="4" id="videoWidth" class="txt" style="width:100px;" value="<?php echo videoWidth ?>"/>
				  &nbsp;像素
				  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;高&nbsp;&nbsp;
				  <input name="videoHight" type="text" size="4" id="videoHight" class="txt" style="width:100px;" value="<?php echo videoHight ?>" />
				  像素 </td>
				</tr>
				<tr>
				  <td width="70" height="30"><strong>会员头像：</strong></td>
				  <td> &nbsp;&nbsp;&nbsp;头像宽&nbsp;&nbsp;
				  <input name="userWidth" type="text" size="4" id="userWidth" class="txt" style="width:100px;" value="<?php echo userWidth ?>"/>
				  &nbsp;像素
				  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;高&nbsp;&nbsp;
				  <input name="userHight" type="text" size="4" id="userHight" class="txt" style="width:100px;" value="<?php echo userHight ?>" />
				  像素 </td>
				</tr>
				<tr>
				  <td colspan="3"><hr></td>
				</tr>
				<tr>
				  <td rowspan="9"><p><br />
					  <br />
					  内容页模块列表单页显示条数设置：</p></td>
				  <td width="70" height="30"><strong>文章列表：</strong></td>
				  <td>单页显示&nbsp;&nbsp;
					<input name="listCount" type="text" size="4" maxlength="4" id="listCount" class="txt" style="width:100px;" value="<?php echo listCount ?>" />
					&nbsp;条</td>
				</tr>
				<tr>
				  <td width="70" height="30"><strong>图片列表：</strong></td>
				  <td>单页显示&nbsp;&nbsp;
					<input name="pictureCount" type="text" size="4" maxlength="4" id="pictureCount" class="txt" style="width:100px;" value="<?php echo pictureCount ?>" />
					&nbsp;条</td>
				</tr>
				<tr>
				  <td width="70" height="30"><strong>产品列表：</strong></td>
				  <td>单页显示&nbsp;&nbsp;
					<input name="productCount" type="text" size="4" maxlength="4" id="productCount" class="txt" style="width:100px;" value="<?php echo productCount ?>" />
					&nbsp;条</td>
				</tr>
				<tr>
				  <td width="70" height="30"><strong>视频列表：</strong></td>
				  <td>单页显示&nbsp;&nbsp;
					<input name="videoCount" type="text" size="4" maxlength="4" id="videoCount" class="txt" style="width:100px;" value="<?php echo videoCount ?>" />
					&nbsp;条</td>
				</tr>
				<tr>
				  <td width="70" height="30"><strong>留言列表：</strong></td>
				  <td>单页显示&nbsp;&nbsp;
					<input name="guestbookCount" type="text" size="4" maxlength="4" id="guestbookCount" class="txt" style="width:100px;" value="<?php echo guestbookCount ?>" />
					&nbsp;条</td>
				</tr>
				<tr>
				  <td width="70" height="30"><strong>评论列表：</strong></td>
				  <td>单页显示&nbsp;&nbsp;
					<input name="commentCount" type="text" size="4" maxlength="4" id="commentCount" class="txt" style="width:100px;" value="<?php echo commentCount ?>" />
					&nbsp;条</td>
				</tr>
				<tr>
				  <td width="70" height="30"><strong>招聘列表：</strong></td>
				  <td>单页显示&nbsp;&nbsp;
					<input name="jobsCount" type="text" size="4" maxlength="4" id="jobsCount" class="txt" style="width:100px;" value="<?php echo jobsCount ?>" />
					&nbsp;条</td>
				</tr>
				<tr>
				  <td width="70" height="30"><strong>列表调用：</strong></td>
				  <td>单页显示&nbsp;&nbsp;
					<input name="calllistCount" type="text" size="4" maxlength="4" id="calllistCount" class="txt" style="width:100px;" value="<?php echo calllistCount ?>" />
					&nbsp;条</td>
				</tr>
				<tr>
				  <td width="70" height="30"><strong>下载列表：</strong></td>
				  <td>单页显示&nbsp;&nbsp;
					<input name="downloadCount" type="text" size="4" maxlength="4" id="downloadCount" class="txt" style="width:100px;" value="<?php echo downloadCount ?>" />
					&nbsp;条</td>
				</tr>
				<tr>
				  <td colspan="3"><hr></td>
				</tr>
			  </table>
			  </td>
            </tr>
          </table>
        </li>
      </ul>
      <ul>
        <li>
          <table width="100%" border="0" cellpadding="0" cellspacing="0" class="mail">
            <tr>
              <td colspan="2"><table width="96%" border="0" align="center" cellpadding="0" cellspacing="0">
                  <tr>
                    <td width="200">后台登录IP绑定：</td>
                    <td colspan="2"><textarea name="loginip" id="loginip" class="txt" cols="27" rows="3" style="float:left"><?php echo LOGINIP ?></textarea></td>
                  </tr>
				  <tr>
				    <td width="200">&nbsp;</td>
					<td>
					<span class="small" style="display:block; width:99%; text-align:left; line-height:30px;">此项设置为绑定允许登录网站后台的IP地址，设置完成后，非此IP的用户将无法登录后台，多个IP之间可用输入法英文状态下的分号隔开;<br />如:192.168.0.1;192.168.0.2 (请慎重填写此项，如填错有可能会造成您的后台无法登录！)。</span>
					</td>
				  </tr>
                  <tr>
                    <td colspan="3"><hr></td>
                  </tr>
                  <tr>
                    <td width="200">产品购物车模块订单邮件提醒：</td>
                    <td colspan="2"><input type="checkbox" name="productISON" <?php echo productISON?'checked="checked"':''?>>
                      <span class="small">勾选此项可开启站内产品购物车模块订单生成的同时发送邮件提醒到邮箱的功能</span></td>
                  </tr>
                  <tr>
                    <td width="200">自定义表单模块订单邮件提醒：</td>
                    <td colspan="2"><input type="checkbox" name="orderISON" <?php echo orderISON?'checked="checked"':''?>>
                      <span class="small">勾选此项可开启站内自定义表单模块订单生成的的同时发送邮件提醒到邮箱的的功能</span></td>
                  </tr>
                  <tr>
                    <td width="200">留言模块留言邮件提醒：</td>
                    <td colspan="2"><input type="checkbox" name="guestbookISON" <?php echo guestbookISON?'checked="checked"':''?>>
                      <span class="small">勾选此项可开启站内留言模块访客留言的同时发送邮件提醒到邮箱的功能</span></td>
                  </tr>
                  <tr>
                    <td colspan="3"><hr></td>
                  </tr>
                  <tr>
                    <td width="200">端口：</td>
                    <td colspan="2"><input name="smtpPort" type="text" class="txt" id="smtpPort" value="<?php echo smtpPort ?>" size="41" />
                      <span class="small">smtp服务器的端口，一般是 25</span></td>
                  </tr>
                  <tr>
                    <td width="200">SMTP服务器：</td>
                    <td colspan="2"><input name="smtpServer" type="text" class="txt" id="smtpServer" value="<?php echo smtpServer ?>" size="41" />
                      <span class="small">您的smtp服务器的地址</span></td>
                  </tr>
                  <tr>
                    <td width="200">邮箱帐号：</td>
                    <td colspan="2"><input name="smtpId" type="text" class="txt" id="smtpId" value="<?php echo smtpId ?>" size="41" />
                      <span class="small">您发送邮件的邮箱帐号</span></td>
                  </tr>
                  <tr>
                    <td width="200">邮箱密码：</td>
                    <td colspan="2"><input name="smtpPwd" type="password" class="txt" id="smtpPwd" value="<?php echo smtpPwd ?>" size="41" />
                      <span class="small">您发送邮件的邮箱密码</span></td>
                  </tr>
                  <tr>
                    <td width="200">发件人：</td>
                    <td colspan="2"><input name="smtpSender" type="text" class="txt" id="smtpSender" value="<?php echo smtpSender ?>" size="41" />
                      <span class="small">一般要与您登录smtp服务器的用户名相同，否则可能会因为smtp服务器的设置导致发送失败</span></td>
                  </tr>
                  <tr>
                    <td width="200">收件人：</td>
                    <td colspan="2"><input name="smtpReceiver" type="text" class="txt" id="smtpReceiver" value="<?php echo smtpReceiver ?>" size="41" />
                      <span class="small">接收邮件的邮箱地址，可添加多个收件邮箱，邮箱之间用分号隔开,例如: a@qq.com;b@163.com</span></td>
                  </tr>
                </table></td>
            </tr>
          </table>
        </li>
      </ul>
      <ul>
        <li>
          <table width="96%" border="0" cellpadding="2" cellspacing="1" align="center">
            <tr height="50">
              <td width="200">是否使用支付宝签约接口</td>
              <td><input type="radio" name="is_pay" value="1" <?php echo PAY_ISPAY?'checked="checked"':''?>>
                是
                <input type="radio" name="is_pay" value="0" <?php echo !PAY_ISPAY?'checked="checked"':''?>>
                否 </td>
              <td><span class="small">如果您的支付宝帐号是签约用户，可以选择"是"让网站使用您自己的商家服务平台，如果您还未签约，<a href="https://www.alipay.com/himalayas/practicality.htm" target="_blank">请点击这里签约</a></span></td>
            </tr>
            <tr height="50">
              <td>签约支付宝账号或卖家支付宝帐户：</td>
              <td><input type="text" value="<?=PAY_SELLER?>" name="seller" class="txt" style="width:230px;" size="40"></td>
              <td><span class="small">支付宝签约用户可以在此处填写签约支付宝账号或卖家支付宝帐户</span></td>
            </tr>
            <tr height="50">
              <td>合作者身份(partnerID) ：</td>
              <td><input type="text" value="<?=PAY_PARTNER?>" name="partner" class="txt" style="width:230px;" size="40"></td>
              <td><span class="small">支付宝签约用户可以在此处填写支付宝分配给您的合作者身份，签约用户的手续费按照您与支付宝官方的签约协议为准</span></td>
            </tr>
            <tr height="50">
              <td>交易安全校验码(key)：</td>
              <td><input type="password" value="<?=PAY_KEY?>" name="key" class="txt" style="width:230px;" size="40"></td>
              <td><span class="small">支付宝签约用户可以在此处填写支付宝分配给您的交易安全校验码，此校验码您可以到支付宝的商家服务功能处查看</span></td>
            </tr>
            <tr height="50">
              <td>网站商品的展示地址：</td>
              <td><input type="text" value="<?=PAY_SHOW_URL?>" name="show_url" class="txt" style="width:230px;" size="40"></td>
              <td><span class="small">网站商品的展示地址，不允许加?p=123这类自定义参数（可在选择网站静态化之后填写）</span></td>
            </tr>
            <tr height="50">
              <td>收款方名称：</td>
              <td><input type="text" value="<?=PAY_MAINNAME?>" name="mainname" class="txt" style="width:230px;" size="40"></td>
              <td><span class="small">收款方名称，如：公司名称、网站名称、收款人姓名等</span></td>
            </tr>
            <tr height="50">
              <td>使用即时到账接口</td>
              <td><input type="radio" name="is_js" value="1" <?php echo PAY_ISJS?'checked="checked"':''?>>
                是
                <input type="radio" name="is_js" value="0" <?php echo !PAY_ISJS?'checked="checked"':''?>>
                否 </td>
              <td><span class="small">如果您的签约协议中包含即时到账接口可以选择此项，让积分充值、商品交易使用即时到账方式付款</span></td>
            </tr>
          </table>
        </li>
      </ul>
      <ul>
        <li>
          <table width="96%" border="0" cellpadding="4" cellspacing="1" align="center">
            <tr height="50">
              <td width="200">是否使用财付通签约接口</td>
              <td><input type="radio" name="is_pay_ten" value="1" <?php echo PAY_ISPAY_TEN?'checked="checked"':''?>>
                是
                <input type="radio" name="is_pay_ten" value="0" <?php echo !PAY_ISPAY_TEN?'checked="checked"':''?>>
                否 </td>
              <td><span class="small">如果您的财付通帐号是签约用户，可以选择"是"让网站使用您自己的商家服务平台，如果您还未签约，<a href="http://mch.tenpay.com/market/index.shtml" target="_blank">请点击这里签约</a></span></td>
            </tr>
            <tr height="50">
              <td>商户号(partnerID) ：</td>
              <td><input type="text" value="<?=PAY_PARTNER_TEN?>" name="partner_ten" class="txt" style="width:230px;" size="40"></td>
              <td><span class="small">财付通签约用户可以在此处填写财付通分配给您的商户号，签约用户的手续费按照您与财付通官方的签约协议为准</span></td>
            </tr>
            <tr height="50">
              <td>交易安全校验码(key)：</td>
              <td><input type="password" value="<?=PAY_KEY_TEN?>" name="key_ten" class="txt" style="width:230px;" size="40"></td>
              <td><span class="small">财付通签约用户可以在此处填写财付通分配给您的交易安全校验码，此校验码您可以到财付通的商家服务功能处查看</span></td>
            </tr>
            <tr height="50">
              <td>网站商品的展示地址：</td>
              <td><input type="text" value="<?=PAY_SHOW_URL_TEN?>" name="show_url_ten" class="txt" style="width:230px;" size="40"></td>
              <td><span class="small">网站商品的展示地址，不允许加?p=123这类自定义参数（可在选择网站静态化之后填写）</span></td>
            </tr>
            <tr height="50">
              <td>收款方名称：</td>
              <td><input type="text" value="<?=PAY_MAINNAME_TEN?>" name="mainname_ten" class="txt" style="width:230px;" size="40"></td>
              <td><span class="small">收款方名称，如：公司名称、网站名称、收款人姓名等</span></td>
            </tr>
            <tr height="50">
              <td>使用即时到账接口</td>
              <td><input type="radio" name="is_js_ten" value="1" <?php echo PAY_ISJS_TEN?'checked="checked"':''?>>
                是
                <input type="radio" name="is_js_ten" value="0" <?php echo !PAY_ISJS_TEN?'checked="checked"':''?>>
                否 </td>
              <td><span class="small">如果您的签约协议中包含即时到账接口可以选择此项，让积分充值、商品交易使用即时到账方式付款</span></td>
            </tr>
          </table>
        </li>
      </ul>
    </form>
  </div>
</div>
