<script type="text/javascript" src="../inc/js/pinyin.js"></script>
<script type="text/javascript">
$(document).ready(function(){
	$('#title').bind('keyup',function(){
		$('#menuName').val(CC2PY($('#title').val()));
	});
	if(cookie==0)
	{
		$("#menuName").focusin(function () {
		$("#showtips1").toggle("slow");
		}).focusout(function () {
		$("#showtips1").toggle("slow");
		});//英文名得到焦点和失去焦点的特效
		
		$("li").each(function(){//遍历所有Li为选中状态进行标记处理
		  $(this).click(function(){
			  var pid  = $(this).parent().attr("id");//提取变量为了减少查询次数
			  var cval = $(this).children().attr("value");
			  //下两行代码作用为增加label的选中属性
			  $("#"+pid+" label").removeClass();
			  $(this).children().attr("class","slected")
			  //kqpl的这个判断不能放在dkfs那个if语句下边，否则会有逻辑错误，错误就在kqpl的else执行有错
			  if(pid == 'kqpl'){
				  $("#isComment").val(cval);			  
			  }
			  if(pid == 'dkfs'){
				  $("#isTarget").val(cval);
			  }else if(pid == 'lmsz'){
				  $("#isHidden").val(cval);
			  }else if(pid == 'lmnx'){
				 if($(this).attr('class')!='hover')
				 {
					$('.one').toggle("slow");
					$('.hidd').toggle("slow");
					$('#showtips2').toggle("slow");
				 }
				 $("#"+pid+" li").removeClass();
				 $(this).attr('class','hover');
				 ($(this).html()=='系统内链')?$("#isExternalLinks").val(0):$("#isExternalLinks").val(1);				
			  }
		   });
		});
		//栏目类型选择和弹窗图片提示
		$("#lmlx label").each(function(){
			$(this).click(function(){
				$("#showtips4").hide('slow');
				$("#type").val($(this).attr("value"));
				$("#lmlx label").removeClass("slected");
				$(this).addClass("slected");			
			}).mouseover(function(){
				var winth = $(window).width(),labwidth = $("#lmlx label").width();
				var imgsrc = $(this).attr("value");
				var labpos = $(this).position(),labtop = labpos.top+28 , lableft = labpos.left+labwidth-497;		
				if((labpos.left+550)<winth){
					$(".lantype").css({left:labpos.left+"px",top:labtop+"px"})
				}else{
					$(".lantype").css({left:lableft+"px",top:labtop+"px"})
				}
				$(".lantype img").attr("src","images/thumb/"+imgsrc+".png");
				$(".lantype").show('slow');
			}).mouseout(function(){
				$(".lantype").hide();
			});
		});
		//关闭弹窗提示
		$(".lantype span").click(function () {
			$(".lantype").fadeOut();
		})
		$("#title").blur(function (){
			if($(this).val()) $("#showtips5").hide('slow');
		})
		$("#menuName").blur(function (){
			if($(this).val()) $("#showtips6").hide('slow');
		})
	}
	else
	{
		$("li").each(function(){//遍历所有Li为选中状态进行标记处理
		  $(this).click(function(){
			  var pid  = $(this).parent().attr("id");//提取变量为了减少查询次数
			  var cval = $(this).children().attr("value");
			  //下两行代码作用为增加label的选中属性
			  $("#"+pid+" label").removeClass();
			  $(this).children().attr("class","slected")
			  //kqpl的这个判断不能放在dkfs那个if语句下边，否则会有逻辑错误，错误就在kqpl的else执行有错
			  if(pid == 'kqpl'){
				  $("#isComment").val(cval);			  
			  }
			  if(pid == 'dkfs'){
				  $("#isTarget").val(cval);
			  }else if(pid == 'lmsz'){
				  $("#isHidden").val(cval);
			  }else if(pid == 'lmnx'){
				 if($(this).attr('class')!='hover')
				 {
					$('.one').toggle("slow");
					$('.hidd').toggle("slow"); 
				 }
				 $("#"+pid+" li").removeClass();
				 $(this).attr('class','hover');
				 ($(this).html()=='系统内链')?$("#isExternalLinks").val(0):$("#isExternalLinks").val(1);				
			  }
		   });
		});
		//栏目类型选择和弹窗图片提示
		$("#lmlx label").each(function(){
			$(this).click(function(){
				$("#type").val($(this).attr("value"));
				$("#lmlx label").removeClass("slected");
				$(this).addClass("slected");			
			})
		});
	}
	
});
function changesel(m,n,obj)
{
	$('#sel'+m).html(obj.innerHTML);
	if(m==1)
	{
		$("#related_common").val(n);
  	}
	else
	{
		$("#level").val(n);
	}
}
function showSelect(hld,id){
    var ele = document.getElementById(id);
    ele.style.display = 'block';
    var timer = null;
    ele.onmouseover = function(){
        if(timer){
            clearTimeout(timer);
        }
        ele.style.display = 'block';
    }
    ele.onmouseout = function(){
        timer = setTimeout(function(){ele.style.display = 'none'},500);
    }
    
    hld.onmouseover = function(){
        if(timer){
            clearTimeout(timer);
        }
    }
    hld.onmouseout = function(){
        timer = setTimeout(function(){ele.style.display = 'none'},500);
    }
}
function showtips(i){
	if(document.getElementById(i).style.display="none"){
		document.getElementById(i).style.display="";
	}
}
function closetips(o){
	document.getElementById(o).style.display="none";	
}
function letsok()
{
	if($('#type').val()=="" && $('#isExternalLinks').val()=='0')
	{
		$("#showtips4").show('slow');
		return;
	}
	if($('#title').val()=="")
	{
		$("#showtips5").show('slow');
		return;
	}
	if($('#menuName').val()=="" && $('#isExternalLinks').val()=='0')
	{
		$("#showtips6").show('slow');
		return;
	}
	document.getElementById('form1').submit();
}
</script>
<style type="text/css">
* { margin:0; padding:0; }
.lmnxb { position:relative; overflow:hidden; width:330px; height:37px; }
#lmnx { position:absolute; top:0; left:90px; z-index:1; }
.lmnxb h4, #menu_main1 h4 { width:90px; text-align:right; float:left; font-weight:normal; font-size:12px; padding-top:6px; display:block; }
#menu_main1 h5 { width:60px; text-align:right; float:left; font-size:12px; padding-top:6px; display:block; font-weight:normal; }
#lmnx, #lmnx li, .externallinks, .externallinks li, .subclass li { float:left; }
.externallinks { margin-bottom:6px; width:85%; }
.externallinks label { padding:5px 10px; border:1px solid #ccc; display:inline-block; margin:0 15px 6px 0; font-size:12px; cursor:pointer; }
#lmnx li, .externallinks li label { padding:4px 10px; border:1px solid #ccc; display:block; margin:0 15px 0 0; font-size:12px; cursor:pointer; }
#lmnx li:hover, .externallinks label:hover { border:1px solid #fc3; }
#lmnx .hover, .externallinks .slected { border:1px solid #fc3; background:url(images/chosebg.gif) right bottom no-repeat; }
#menu_main1 .dex { display:none; width:96%; overflow:hidden; padding-top:5px; font-size:12px; }
#menu_main1 .block { display:block; }
#menu_main1 .dex .relist { width:100%; float:left; z-index:1; }
#menu_main1 .dex .one { <?php echo $menu_item->isExternalLinks==0?'display:none;':'display:block;';
?>
}
#menu_main1 .dex .hidd { <?php echo $menu_item->isExternalLinks==0?'display:block;':'display:none;';
?>
}
#menu_main1 .dex .textinput { height:14px; padding:5px 0 5px 10px; -moz-border-bottom-colors: none; -moz-border-image: none; -moz-border-left-colors: none; -moz-border-right-colors: none; -moz-border-top-colors: none; background-color: white; border-color: #CCCCCC #E2E2E2 #E2E2E2 #CCCCCC; border-style: solid; border-width: 1px; box-shadow: 1px 2px 3px #F0F0F0 inset; color: #666; overflow: hidden; vertical-align: middle; margin-bottom:8px; float:left; }
.tip-bubble { margin:5px 0 10px 100px; float:left; padding:20px; color: #666; position: relative; background-color: #F0F0F0; border-radius: 10px; border: 1px solid #F0F0F0; box-shadow: 1px 1px 2px #F0F0F0; -moz-box-shadow: 1px 1px 2px #F0F0F0; -webkit-border-shadow: 1px 1px 2px #F0F0F0; text-shadow: 0px 0px 15px #fff; }
.tip-bubble:after { content: ''; position: absolute; width: 0; height: 0; border: 15px solid; }
.tip-bubble-left:after { border-right-color: #F0F0F0; top: 50%; right: 100%; margin-top: -15px; color:#fff; }
#menu_main1 .dex span { line-height:20px; }
.select, .select2 { position:relative; padding:0; width:200px; height:35px; float:left; }
.select span, .select2 span { display:block; height:20px; width:190px; background:url(images/select_bg.jpg) no-repeat; z-index:1; padding:5px 0 0 10px; }
#sub_list, #sub_list2 { position:absolute; padding:0 2px 2px 0; top:28px; left:0; width:198px; z-index:5; }
#sub_list ul, #sub_list2 ul { display:block; padding:5px; border:1px solid #E1E1E1; background:#FFF }
#sub_list ul li, #sub_list2 ul li { line-height:20px; }
#sub_list ul li a, #sub_list2 ul li a { display:block; padding:3px 0 2px 3px; text-decoration:none }
#sub_list ul li a:hover, #sub_list2 ul li a:hover { background:#F4F4F4 }
.hidden { display:none }
.flt { float:left; padding:5px 0 0 10px; }
.lantype { display:none; width:500px; height:280px; padding:10px; position:absolute; z-index:999; background:url(images/thumb/lantypebg.png) no-repeat; }
.lantype span { display:block; width:80px; height:30px; cursor:pointer; position:absolute; right:10px; top:10px; }
</style>
<div class="location">当前位置: <a href="./index.php">首 页</a> → <a href="./index.php?m=system&s=managechannel">操作员后台</a> → <a href="./index.php?m=system&s=managechannel">导航及栏目设置</a></div>
<form name="form1" id="form1" method="post"  enctype="multipart/form-data" action="?m=system&s=managechannel&a=edit&pid=<?php echo $request['pid'] ?>&cid=<?php echo $request['cid'] ?>&deep=<?php echo $request['deep'] ?>">
  <input type="hidden" name="isExternalLinks" id="isExternalLinks" value="<?php echo $menu_item->isExternalLinks ?>">
  <input type="hidden" name="related_common" id="related_common" value="<?php echo $menu_item->related_common ?>">
  <input type="hidden" name="level" id="level" value="<?php echo $menu_item->level ?>">
  <input type="hidden" name="isTarget" id="isTarget" value="<?php echo $menu_item->isTarget ?>">
  <input type="hidden" name="isHidden" id="isHidden" value="<?php echo $menu_item->isHidden ?>">
  <input type="hidden" name="isComment" id="isComment" value="<?php echo $menu_item->isComment ?>">
  <input type="hidden" name="type" id="type" value="<?php echo $menu_item->type ?>">
  <table width="100%" border="0" cellpadding="4" cellspacing="1" class="admintb">
    <tr class="adtbtitle">
      <td width="872"><h3>修改菜单属性</h3>
        <a href="javascript:history.back(1)" class="creatbt">返回</a></td>
      <td width="72"><div align="right">
          <input type="button" class="savebt" value=" 保存 " name="button" onclick="letsok()">
        </div></td>
    </tr>
  </table>
  <div class="menu_main1box">
    <div class="menu_main" id="menu_main1">
      <div class="dex block">
        <div class="hidd relist">
          <h4>栏目类型：</h4>
          <div class="externallinks" id="lmlx">
            <?php model_radio_group('type',$menu_item->type) ?>
          </div>
          <div class="admin_help">
            <div class="lantype"><span></span><img ></div>
          </div>
          <div id="showtips4" style="display:none;" class="tip-bubble tip-bubble-left">[温馨提示:<span style="color:#FF0000;">请选择您要创建的栏目类型</span>]</div>
        </div>
        <div class="relist">
          <h4>栏目标题：</h4>
          <input type="text" class="textinput" name="title"  value="<?php echo $menu_item->title ?>" id="title" style="width:89%" >
          <div id="showtips5" style="display:none;" class="tip-bubble tip-bubble-left">[温馨提示:<span style="color:#FF0000;">请输入您的栏目标题</span>]</div>
        </div>
        <div class="hidd relist">
          <h4>英文名：</h4>
          <input type="text" class="textinput hdbt" name="menuName" onkeyup="value=value.replace(/[\W]/g,'') " onbeforepaste="clipboardData.setData('text',clipboardData.getData('text').replace(/[^a-zA-Z0-9]/g,''))" value="<?php echo $menu_item->menuName ?>" id="menuName" style="width:89%">
          <div class="admin_help">
            <div id="showtips1" class="tip-bubble tip-bubble-left hidden"> <span style="width:96%;color:#FF0000;">*</span>[如果您打开了支持搜索引擎优化的页面永久路径和静态化,那么<span style="color:#FF0000;">栏目</span>英文名就一定要填写。]<br />
              <span style="width:96%;color:#FF0000;">*</span>[而且还要注意<span style="color:#FF0000;">不要与其它频道和栏目</span>有重复的英文名，且输入的英文名只能是<span style="color:#FF0000;">英文字符</span>或<span style="color:#FF0000;">数字字符</span>或<span style="color:#FF0000;">英文和数字的字符组合</span>。] </div>
          </div>
          <div id="showtips6" style="display:none;" class="tip-bubble tip-bubble-left">[温馨提示:<span style="color:#FF0000;">请输入您的栏目英文名称</span>]</div>
        </div>
        <div class="one relist">
          <h4>外链URL地址：</h4>
          <input type="text" class="textinput hdbt" name="redirectUrl" id="redirectUrl" value="<?php echo $menu_item->redirectUrl ?>" style="width:89%">
          <div id="showtips2" style=" display:none;" class="tip-bubble tip-bubble-left"><span style="width:96%;color:#FF0000;">*</span>[这里填写您要外链到系统以外或已确定的固定URL地址，即http://地址，例如：<span style="width:96%;color:#FF0000;">http://www.doooc.com/</span>]</div>
        </div>
        <div class="hidd relist">
          <h4>栏目样式：</h4>
          <div class="select"> <span onclick="showSelect(this,'sub_list')" id="sel1"><?php echo $menu_item->related_common!='common.php'?$menu_item->related_common:'默认样式'?></span>
            <div id="sub_list" class="hidden" onclick="this.style.display = 'none'">
              <ul>
                <?php $temp=new menu();$temp->menu_power_list_select('related_common',$menu_item->related_common,1); ?>
              </ul>
            </div>
          </div>
        </div>
        <div class="hidd relist">
          <h4>栏目关键词：</h4>
          <input type="text" class="textinput" name="keywords" id="keywords" value="<?php echo $menu_item->keywords ?>" style="width:89%">
        </div>
        <div class="hidd relist">
          <h4>栏目摘要：</h4>
          <input type="text" class="textinput" name="description" id="description" value="<?php echo $menu_item->description ?>" style="width:89%">
        </div>
        <div class="hidd relist">
          <h4>权限：</h4>
          <?php $temp_arr = array( '0'=>'匿名',
						   '1'=>'普通会员',
						   '2'=>'vip1级用户',
						   '3'=>'vip2级用户',
						   '4'=>'vip3级用户',
						   '5'=>'vip4级用户',
						);?>
          <div class="select"> <span onclick="showSelect(this,'sub_list2')" id="sel2"><?php echo $temp_arr[$menu_item->level]?></span>
            <div id="sub_list2" class="hidden" onclick="this.style.display = 'none'">
              <ul>
                <?php user::user_power_list_select('level',$menu_item->level,true,1); ?>
              </ul>
            </div>
          </div>
        </div>
        <div class="lmnxb">
          <h4>栏目属性：</h4>
          <ul id="lmnx">
            <li <?php echo $menu_item->isExternalLinks==0?'class="hover"':'';?>>系统内链</li>
            <li <?php echo $menu_item->isExternalLinks==1?'class="hover"':'';?>>系统外链</li>
          </ul>
        </div>
        <div class="relist">
          <h4>打开方式：</h4>
          <ul class="externallinks" id="dkfs">
            <li>
              <label value='0' <?php echo $menu_item->isTarget==0?'class="slected"':'';?> >当前窗口</label>
            </li>
            <li>
              <label value='1' <?php echo $menu_item->isTarget==1?'class="slected"':'';?> >新窗口</label>
            </li>
          </ul>
        </div>
        <div class="relist">
          <h4>栏目设置：</h4>
          <ul class="externallinks" id="lmsz">
            <li>
              <label value='0' <?php echo $menu_item->isHidden==0?'class="slected"':'';?> >显示</label>
            </li>
            <li>
              <label value='1' <?php echo $menu_item->isHidden==1?'class="slected"':'';?> >隐藏</label>
            </li>
          </ul>
        </div>
        <?php
		global $noComment;
        if(!in_array($menu_item->type,$noComment))
		{
		?>
        <div class="hidd relist">
          <h4>开启评论：</h4>
          <ul class="externallinks" id="kqpl">
            <li>
              <label value='1' <?php echo $menu_item->isComment==1?'class="slected"':'';?> >开启</label>
            </li>
            <li>
              <label value='0' <?php echo $menu_item->isComment==0?'class="slected"':'';?> >关闭</label>
            </li>
          </ul>
        </div>
        <?php }?>
        <div class="relist">
          <h4>排序：</h4>
          <input type="text" class="textinput" name="ordering" id="ordering" value="<?php echo $menu_item->ordering ?>" style="width:24px">
          <span class="flt">(正序)</span> </div>
        <div class="relist">
          <h4>添加缩略图：</h4>
          <input type="text" class="textinput" name="originalPic" id="originalPic" value="<?php echo $menu_item->originalPic ?>" style="width:30%">
          <input disabled class="textinput" name="uploadfile" type="file" style="display: none;width:30%; height:28px;">
          <input type="button" name="bt2" value="本地上传" class="creatbt" style="height:26px; margin-left:15px; padding:0.2em 1em" onclick="originalPic.disabled=true;uploadfile.disabled=false;uploadfile.style.display='';originalPic.style.display='none';this.style.display='none'">
          <h5>宽：</h5>
          <input name="width" class="textinput" type="text" size="5" maxlength="4" id="width"   value="<?php echo $menu_item->width ?>"/>
          <span class="flt">px</span>
          <h5>高：</h5>
          <input name="hight" class="textinput" type="text" size="5" maxlength="4" id="hight" value="<?php echo $menu_item->hight ?>" />
          <span class="flt">px</span> </div>
      </div>
    </div>
  </div>
</form>