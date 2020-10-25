<{include file="public/header.tpl"}>

	<div class="container">

		<div class="row jumbotron1">
			<div class="col-md-7 col-md-offset-1" style="padding:0px">
				<div  style="padding:20px">
					<span><b>当前分类：</b><{$nowcat.name}></span>
					&nbsp;
					<b>子分类：</b>
					<{foreach from=$scat item="row"}>
					&nbsp;<span><a href="<{$smarty.const.__CONTROLLER__}>/forward/cat/<{$row.id}>"><{$row.name}></a>
					<{foreachelse}>没有子分类
					</span>
					
					<{/foreach}>
				</div>
			</div>
			<div class="col-md-3" style="padding:0px">
				<div  style="padding:20px">
					<span><b>UP主</b></span>
				</div>
			</div>
		</div>
		<{foreach from=$video item="row"}>
		<div class="row jumbotron1">
			<div class="media col-md-7  col-md-offset-1 ">
			  <div class="media-left ">
			    <a href="<{$smarty.const.__MODULE__}>/video/index/vid/<{$row.id}>">
			      <img class="media-object" src="<{$smarty.const.APP_RES}>/uploads/images/<{$row.pic}>" alt="未找到图片">
			    </a>
			  </div>
			  <div class="media-body">
			    <h4 class="media-heading"><{$row.name}> </h4>
			    <h5>发布时间：<{$row.ptime|date_format:"%Y-%m-%d %H:%M:%S"}> 点击量：<{$row.hot}> 评论数：<{$row.comnumber}></h5>
			    <h6>描述：<{$row.desn}></h6>
			  </div>
			</div>
			<div class="col-md-3 " >
				<div  style="padding:20px">
					<span><b><{$row.uname}></b></span>
				</div>
			</div>
		</div>
		<{foreachelse}>
		<h4 class="col-md-7  col-md-offset-1">没有找到相关视频</h4>
		<{/foreach}>
	
	</div>


<{include file="public/footer.tpl"}>