<{include file="public/header.tpl"}>
<div class="container">

        <{foreach from=$data item="row"}>
        <div class="row catlistsheet" >
          <div class="col-md-10  col-md-offset-1 ">
          	<div class="jumbotron1">
          	<a href="<{$smarty.const.__CONTROLLER__}>/forward/cat/<{$row.id}>"><{$row.name}></a>
          	</div>
          	<{foreach from=$row.video item="key"}>
          	<div class="jumbotron1 col-md-offset-1">
	          <div class="media catlistmedia">
	            <div class="media-left ">
	              <a href="<{$smarty.const.__MODULE__}>/video/index/vid/<{$key.id}>">
	                <img class="media-object" src="<{$smarty.const.APP_RES}>/uploads/images/<{$key.pic}>" alt="未找到图片">
	              </a>
	            </div>
	            <div class="media-body">
	              <h4 class="media-heading"><{$key.name}> </h4>
	              <h5>发布时间：<{$key.ptime|date_format:"%Y-%m-%d %H:%M:%S"}> 点击量：<{$key.hot}> 评论数：<{$key.comnumber}></h5>
	              <h6>描述：<{$key.desn}></h6>
	            </div>
	          </div> 
	          </div>
	          <{foreachelse}>
      		  <h4>该分类尚未拥有视频</h4>
	          <{/foreach}>
          </div>
        </div>
        <{/foreach}>
    </div>
<{include file="public/footer.tpl"}>