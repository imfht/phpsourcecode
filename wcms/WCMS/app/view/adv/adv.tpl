<!doctype html>
<html lang="en"><head>
	<meta charset="utf-8">
	<title>WCMS</title>
	<meta content="IE=edge,chrome=1" http-equiv="X-UA-Compatible">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="description" content="">
	<meta name="author" content="">

	<link rel="stylesheet" type="text/css" href="/static/theme/bootstrap/css/bootstrap.css">
	<link rel="stylesheet" href="/static/theme/font-awesome/css/font-awesome.css">

	<script src="/static/theme/jquery-1.11.1.min.js" type="text/javascript"></script>
	<link rel="stylesheet" type="text/css" href="/static/theme/stylesheets/theme.css">
	<link rel="stylesheet" type="text/css" href="/static/theme/stylesheets/premium.css">
	<link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.15/css/jquery.dataTables.min.css">


</head>
<body class="theme-blue">


<div class="navbar navbar-default" role="navigation">
	<div class="navbar-header">
		<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target=".navbar-collapse">
			<span class="sr-only">Toggle navigation</span>
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
		</button>
		<a class="" href="index.html"><span class="navbar-brand"><span class="fa fa-paper-plane"></span> WCMS</span></a></div>

	<div class="navbar-collapse collapse" style="height: 1px;">
		<ul id="main-menu" class="nav navbar-nav navbar-right">
			<li class="dropdown hidden-xs">
				<a href="#" class="dropdown-toggle" data-toggle="dropdown">
					<span class="glyphicon glyphicon-user padding-right-small" style="position:relative;top: 3px;"></span> {$user.username}
					<i class="fa fa-caret-down"></i>
				</a>

				<ul class="dropdown-menu">
					<li><a tabindex="1" href="/index.html" target="_blank">首页</a></li>

					<li><a tabindex="-1" href="/index.php?anonymous/signout">退出</a></li>
				</ul>
			</li>
		</ul>

	</div>
</div>
</div>


<div class="sidebar-nav">
	<ul>
		<li><a href="#" data-target=".dashboard-menu" class="nav-header" data-toggle="collapse"><i class="fa fa-fw fa-dashboard"></i> Dashboard<i class="fa fa-collapse"></i></a></li>
		<li><ul class="dashboard-menu nav nav-list collapse in">
				<li ><a href="/index.php?articleadmin/getallcon"><span class="fa fa-caret-right"></span> 文章</a></li>
				<li ><a href="/index.php?memberadmin/getallmember"><span class="fa fa-caret-right"></span> 用户</a></li>
				<li ><a href="/index.php?commentadmin/getallcomment"><span class="fa fa-caret-right"></span> 评论</a></li>
				<li ><a href="/index.php?cateadmin/edit"><span class="fa fa-caret-right"></span> 分类</a></li>

				<li class="active"><a href="/index.php?advadmin/adv"><span class="fa fa-caret-right"></span> 广告</a></li>

			</ul></li>

		<li></li>


	</ul></li>


	</ul>
</div>

<div class="content">
	<div class="main-content">

		<div class="btn-toolbar list-toolbar">
			<a href="javascript:add()" class="btn btn-primary"><i class="fa fa-plus"></i> 添加广告</a>
			<div class="btn-group">
			</div>
		</div>
		<table class="table" id="myTable">
			<thead>
			<tr>
				<th>ID</th>
				<th>状态</th>
				<th>类型</th>
				<th>上传时间</th>
				<th>广告名</th>
				<th>图片</th>
				<th>URL</th>
				<th>操作</th>
			</tr>
			</thead>
			<tbody>

{foreach from=$adv item=l}
<tr>
<td>{$l.id}</td>
<td><a href="javascript:setStatus({$l.id})" id="status_{$l.id}">{$l.status}</a></td>
<td>{$l.type}</td>
<td>{$l.add_time|date_format:"%Y-%m-%d"}</td>
<td>{$l.title}</td>
<td><img src="{$l.image}" width="120px;" height="120px;"></td>
<td><a href="{$l.url}">{$l.url}</a></td>
<td><a href="javascript:edit({$l.id})">编辑</a>|<a href="javascript:del({$l.id})">X</a></td>
</tr>
<tr>
{/foreach}


</table>
</div>
</div>
</div>
</div>
 <script  type="text/javascript" src="./static/public/layer/layer.min.js" ></script>
  <script  type="text/javascript" src="./static/public/layer/extend/layer.ext.js" ></script>

<script src="./static/bootstrap2/js/bootstrap.min.js" language="javascript"></script>
 
 {literal}
 <script>
 function edit(id){
		$.layer({
		    type: 2,
		    shadeClose: true,
		    title: false,
		    closeBtn: [0, true],
		    shade: [0.3, '#000'],
		    border: [0],
		    offset: ['20px',''],
		    area: ['850px', '480px'],
		    iframe: {src: './index.php?advadmin/edit/?id='+id,
		        scrolling: 'yes'
		      }
		}) 

  }
 function add(sku){	
		$.layer({
		    type: 2,
		    shadeClose: true,
		    title: false,
		    closeBtn: [0, true],
		    shade: [0.3, '#000'],
		    border: [0],
		    offset: ['20px',''],
		    area: ['850px', '480px'],
		    iframe: {src: './index.php?advadmin/add/',
		        scrolling: 'yes'
		      }
		}) 

}
function del(id){


	if(!confirm("确认删除?")){
return;
	}
  $.get("./index.php?advadmin/remove/?id="+id,function(){
  location.reload();
  });
}
 
 function setStatus(id){
  $.post("./index.php?advadmin/setstatus",{id:id},function(data){
       if(data.status==false){
   alert(data.message);
   return;
        }

       $("#status_"+id).html(data.data);
	  },"json")
 }
 </script>
 {/literal}