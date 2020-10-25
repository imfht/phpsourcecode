<script type="text/javascript">
$(document).ready(function(){
	$("#tree li").mouseover(function (){
		$(this).css({background:"#FFFFCC"});//#efefef
	});
	$("#tree li").mouseout(function (){
		$(this).css({background:"none"});
	});
	$("input[name='submit']").click(function(){
  		$("ul").find("li").find("input[type='checkbox']").attr("disabled",false);
	}); 
	$("#checkAll").click(function(){//全选
		$("input[type='checkbox']").attr("checked",true);
	});
	$("#checkNo").click(function(){//全不选
		$("input[type='checkbox']").attr("checked",false);
	});
	$("#check").click(function(){//反选  不合理取消
  		var checkboxObj=$("input[type='checkbox']").get();
  		for(x in checkboxObj){
  	  		if(checkboxObj[x].checked){
  	  			checkboxObj[x].checked=false;
  	  		}else{
  	  			checkboxObj[x].checked=true;
  	  		}	
  		}
	});
	$("input[type='checkbox']").click(function(){//追溯节点
		var opts={
	  			"operids":{"clentAction":"checkToRoot(html)"},
	  			"ajaxopts":{
		  			"type":"POST",
		  			"url":"?",
		  			"data":"id="+$(this).val(),
		  			"timeout":"4000",
		  			"dataType":"text"// json string 
	  			}
	  		};
		if($(this).attr("checked")){
			opts.operids.clentAction="checkToRoot(html)";
			opts.ajaxopts.url="./index.php?m=system&s=userinfo&a=get_parent_nodes";
		}else{
			opts.operids.clentAction="checkToSub(html)";
			opts.ajaxopts.url="./index.php?m=system&s=userinfo&a=get_sub_nodes";
		}
		getDatas(opts);
	}); 
});

function checkToRoot(html){
	if(html.length>0){
		var arr=html.split(',');
		for(x in arr){
			$("#check_"+arr[x]).attr('checked',true);
		}
	}else{
		alert('出现异常');
	}
}
function checkToSub(html){
	if(html.length>0){
		var arr=html.split(',');
		for(x in arr){
			$("#check_"+arr[x]).attr('checked',false);
		}
	}else{
		
	}
}
function getDatas(opts){
	 $.ajax({
			type:opts.ajaxopts.type,
			url:opts.ajaxopts.url,
			data:opts.ajaxopts.data,
			timeout:opts.ajaxopts.timeout,
			dataType:opts.ajaxopts.dataType,                                      
			success: function(html){
				 eval(opts.operids.clentAction);
			},
			error:function(){
				alert("超时,请重试");
			}
		});
}

</script>
<style>
<!--
#table{width:100%; border:1px solid #ccc;}
ul#tree{width:100%; }
ul#tree li{width:95%; clear:both; height:24px; line-height:24px;   border-bottom:1px dashed #ccc;}
ul#tree li span{ display:block;float:left;}
ul#tree li span a{ }
ul#tree li ul{ display:none;}
ul#tree li ul li{ }
.tree{ width:50%;  }
.tree .prefix{ }
.tree .title{ background:url(../inc/img/tree/tree_file.gif) no-repeat 0 50%; padding-left:16px;}
.menuname{ width:10%; }
.type{ width:15%; }
.mod{ width:20%;}
.check{ width:20%;}
.check input{ }
#submit{width:50px;}
-->
</style>
<div class="location">当前位置: <a href="./index.php">首 页</a> → <a href="./index.php?m=system&s=managechannel">操作员后台</a> → <a href="./index.php?m=system&s=userinfo">用户管理</a> → 用户权限分配</div>
<table width="100%" border="0" cellpadding="2" cellspacing="1" bgcolor="#FFFFFF"  id="table">
  <tr class="adtbtitle">
    <td><h3>用户权限分配</h3> <a class="creatbt" href="javascript:history.back(1)">返回</a> </td>
  </tr>
  <tr>
    <td bgcolor="#efefef">
	  <table width="100%" height="60" border="0" align="center" cellpadding="0" cellspacing="0">
	  	 <tr bgcolor="#fff0f5">
          <td width="70">用户名</td>
          <td width="70">昵称</td>
          <td width="70">权限级别</td>
          <td width="70">姓名</td>
          <td width="70">性别</td>
          <td width="70">年龄</td>
          <td width="70">手机</td>
          <td width="70">地址</td>
           <td width="70">Email</td>
        </tr>
        <tr>       
          <td><?php echo $tmp['user']->username?></td>     
          <td><?php echo $tmp['user']->nickname?></td>
          <td>
          <?php $userRights=new userRights();echo $userRights->return_level_name(intval($tmp['user']->role));?>
          </td>
          <td><?php echo $tmp['user']->name?></td>
          <td><?php echo isSex($tmp['user']->sex); ?></td>
          <td><?php echo $tmp['user']->age?></td>
          <td><?php echo $tmp['user']->mtel?></td>
          <td><?php echo $tmp['user']->address?></td>
           <td><?php echo $tmp['user']->email?></td>
        </tr>		
      </table>	
	  </td>
  </tr>
  <tr bgcolor="#FFFFFF">
    <td >&nbsp; </td>
  </tr>
  <tr>
    <td >
	<form id="form1" name="form1" method="post" action="./index.php?m=system&s=userinfo&a=assigningPermissions&id=<?php echo $tmp['user']->id?>">
   <ul id="tree">
	<li>
		<span class="tree">中文标题</span>
		<span class="menuname">英文名</span>
		<span class="type">模块类型</span>
		<span class="mod">
			<input id="checkAll" name="checkAll" type="button" value="全选"/>
			<input id="checkNo" name="checkNo" type="button" value="全不选"/>
			<input id="submit" name="submit" type="submit" value="保存"/>
		</span>
		
	</li>
	<?php echo $tmp['menuinfo'];unset($tmp);?>
	</ul>
  </form>
	</td>
  </tr>
</table>