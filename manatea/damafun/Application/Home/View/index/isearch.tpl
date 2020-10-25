<{include file="public/header.tpl"}>
  <div class="container">

    <div class="row">
      <div class="col-md-10 col-md-offset-1" style="padding:0px">
        <{foreach from=$data item="row"}>
        <div class="row jumbotron1">
          <div class="media col-md-10  col-md-offset-1 ">
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
        </div>
        <{foreachelse}>
        <h4 class="col-md-7  col-md-offset-1">没有找到相关视频</h4>
        <{/foreach}>
      </div>
    </div>

  
  </div>
<{include file="public/footer.tpl"}>