<{include file="public/header.tpl"}>
<body>

   
    <script src="<{$smarty.const.APP_RES}>/home/js/CommentCoreLibrary.js"></script>
    <script src="<{$smarty.const.APP_RES}>/home/js/ABPLibxml.js"></script>
    <script src="<{$smarty.const.APP_RES}>/home/js/ABPlayer.js"></script>
    <script src="<{$smarty.const.APP_RES}>/home/js/star-rating.min.js"></script>
    <link rel="stylesheet" href="<{$smarty.const.APP_RES}>/home/css/star-rating.min.css" />
    <link rel="stylesheet" href="<{$smarty.const.APP_RES}>/home/css/base.css?1" />
    <script type="text/javascript">
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
      window.addEventListener("load",function(){
        var inst = ABP.bind(document.getElementById("ChouneyPlay"), false,"","<{$video.path}>","<{$smarty.const.__CONTROLLER__}>/sendDama");
        CommentLoader("<{$smarty.const.APP_RES}>/uploads/video/info/<{$video.path}>.xml", inst.cmManager); ///这里url最好采用绝对定位
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
      var recomstr="未评论";
      var recomed=false;
      if('<{$recomed}>'){
        recomstr="已评论";
        recomed=true;
        $('#ratebutton').remove();
      }
        $('#input-21e').rating({clearCaption:recomstr,size:'xs',readonly:recomed,showClear:false,starCaptions:{
            0.5: '0.5',
            1: '1',
            1.5: '1.5',
            2: '2',
            2.5: '2.5',
            3: '3',
            3.5: '3.5',
            4: '4',
            4.5: '4.5',
            5: '5'
        }});
      });
      function sendComment(){
        if("<{$smarty.session.user.allow}>"==""||"<{$smarty.session.user.allow}>"==0){
          alert("您没有登录或没有权限无法发表评论");
          return ;
        }
        var comment = $("#CZ_comment");
        $.post("<{$smarty.const.__CONTROLLER__}>/sendComment","cont=1&comment="+comment.val()+"&uid=<{$smarty.session.user.id}>&vid=<{$video.id}>",function(data){
          var obj = $.parseJSON(data);
          var str='<div class="row jumbotron1"><div class="col-md-12 col-md-offset-1" >#'+obj.cid+'<h4> <a href="#">'+obj.name+'</a>于'+obj.time+'说：'+obj.comment+'</h4></div></div>';
          $('.comment').append(str);
          comment.val("");
        });
      }
      function rating(){
        if("<{$smarty.session.user.allow}>"==""||"<{$smarty.session.user.allow}>"==0){
          alert("您没有登录或没有权限进行评价");
          return ;
        }
       // var rate = $(".caption").children("span").text()
        var rate = $('.caption').children().text();
        if(rate=="未评论") return ;
        $.post("<{$smarty.const.__CONTROLLER__}>/setRating","cont=1&username=<{$smarty.session.user.id}>&rating="+rate+"&vid=<{$video.id}>",function(data){
            $('#input-21e').rating('refresh',{readonly:true,clearValue:'0'});
            alert(data);
            $('#ratebutton').remove();
        });
      }
      </script>
<div class="container">


	<ol class="breadcrumb">
  <li><a href="#">主页></a></li>
  <li class="active">动画短片</li>
</ol>
<div class="page-header">
  <h3><{$video.name}> <small>上传者：<{$user.name}></small></h3>
  <ul class="nav nav-pills" role="tablist">
  <li role="presentation"><a href="#">点击数 <span class="badge"><{$video.hot}></span></a></li>
  <li role="presentation"><a href="#">评论数 <span class="badge"><{$video.comnumber}></span></a></li>
  <li role="presentation"><input id="input-21e"><!-- <input id="input-21e" value="3" type="number" class="rating" min=0 max=5 step=0.5 data-size="xs"  data-default-caption="{rating}"  data-star-captions="{}"> -->
  </li>
  <li role="presentation">
  <button type="submit" class="btn btn-info btn-sm" id='ratebutton' onclick="rating()">评分</button>
  </li>
</ul>
</div>
<!-- 视频内容和视频简介放在左右两边 -->
<div class="row">
  <div class="col-md-12" style="height:700;">
  <div id="ChouneyPlay" class="ABP-Unit" style="width:1120px;height:630px;" tabindex="1">
      <div class="ABP-Video">
        <div class="ABP-Container"></div>
        <video id="abp-video" autobuffer="true" data-setup="{}" poster="<{$smarty.const.APP_RES}>/uploads/images/<{$video.pic}>">
          <source src="<{$smarty.const.APP_RES}>/uploads/video/<{$video.path}>" type="video/mp4">
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
      <h3>视频简介  <small><{$video.desn}></small></h3>
    </div>
  </div>
</div>
<div class="row comment col-md-12">
      <h3>视频评论</h3>
      <{foreach from=$comment item="row"}>
      <div class="row jumbotron1">
        <div class="col-md-12 col-md-offset-1" >
        #<{$row.id}>
        <h4> <a href="#"><{$row.name}></a>于<{$row.time|date_format:"%Y-%m-%d %H-%M-%S"}>说：
          <{$row.comment}>
          </h4>
        </div>
      </div>
      <{/foreach}>
</div>
<div class="row">
  <div class="col-md-12">
<textarea  rows="4" cols="40" id="CZ_comment" style="resize:none;width:100%;">
发表评论
</textarea>
<button type="button" onclick="sendComment();" class="btn btn-info btn-sm" >提交</button>
  </div>
</div>
<div class="row">   
<div class="col-md-12">
<h3><b>与该视频相关的还有</b></h3>
   <{foreach $recom as $row}>
        <div class="case_li col-md-3" style="padding:0px;">
            <a href="<{$smarty.const.__MODULE__}>/video/index/vid/<{$row.id}>"><img src="<{$smarty.const.APP_RES}>/uploads/images/<{$row.pic}>" /></a>
            <!-- 视频标题求特技 -->
             <div class="case_li_txt">
                <div class="span_mr_txt"><{$row.name}></div>
              <div class="span_mr_txt">点击量：<{$row.hot}>&nbsp;&nbsp;&nbsp;&nbsp;回复数：<{$row.comnumber}></div>
            </div>
          </div>
    <{/foreach}>
    </div>
</div>
</div>
</body>
<{include file="public/footer.tpl"}>