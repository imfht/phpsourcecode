<!--{template header}-->
  <!--{eval $adlist = $this->fromcache("adlist");}-->
<link rel="stylesheet" media="all" href="{SITE_URL}static/css/widescreen/css/greendetail.css?v1.1" />
<link rel="stylesheet" media="all" href="{SITE_URL}static/css/widescreen/css/widescreendetail.css?v1.1" />
   <script type="text/javascript" src="{SITE_URL}static/js/jquery.qrcode.min.js"></script>
        <link rel="stylesheet" type="text/css" href="{SITE_URL}static/js/poster/poster.css">
  <script type="text/javascript" src="{SITE_URL}static/js/poster/haibao.js"></script>

<div class="scrollshow position-inf" style="display: none;">
   <div class="fix-hnav posd" style="display: block;">
    <p class="title" title="{$topicone['title']}">        {$topicone['title']}  </p>
    <div class="btns">
                        {if $isfollowarticle}
                    <input type="button"  class="btn-default-secondary details-collection-btn collection js-not-fav js-project-fav" value="已收藏" data-on="1" title="已收藏">
                        
                   {else}
                   {if $user['uid']}
                    <input type="button" onclick="window.location.href='{url favorite/topicadd/$topicone['id']}'" z-st="favorite" class="btn-default-secondary details-collection-btn collection js-not-fav js-project-fav" value="收藏文章" data-on="1" title="收藏文章">
                      
                      {else}
                       <input type="button" onclick="login()" z-st="favorite" class="btn-default-secondary details-collection-btn collection js-not-fav js-project-fav" value="收藏" data-on="1" title="收藏文章">
                     
                      {/if}  
                    {/if}
                        
                         <button type="button" class="btneditanswer" {if $user['uid']==0}  onclick="login()" {else} onclick="showeditor()" {/if} ><i class="fa fa-pencil"></i>写评论</button>
                      
            </div>
  </div>
  </div>
        <div class="content-wrap">
        <div class="work-details-wrap border-bottom">
                <div class="work-details-box-wrap container">
                    <div class="left-details-head col-md-16">
                                              <div class="works-tag-wrap">
                       
                      
                
                 <!--{if $taglist}-->
                                       <!--{loop $taglist $tag}-->

  <a href="{url tags/view/$tag['tagalias']}" title="{$tag['tagname']}" target="_blank" class="project-tag-14">{$tag['tagname']}</a>
                           
               
                <!--{/loop}--><!--{else}--><!--{/if}-->
                
                           
                        </div>
                        <div class="details-contitle-box">
                            <!--标题-->
                            <h2>{$topicone['title']}</h2>
                           
                            <!--发布时间-->
                            <p  class="title-time">
                                <span>{$topicone['viewtime']}</span>发布
                                     <span class="share-group">
        <a class="share-circle share-weixin" data-action="weixin-share" data-toggle="tooltip" data-original-title="分享到微信">
          <i class="fa fa-wechat"></i>
        </a>
        <a class="share-circle" data-toggle="tooltip" href="javascript:void((function(s,d,e,r,l,p,t,z,c){var%20f='http://v.t.sina.com.cn/share/share.php?appkey=1515056452',u=z||d.location,p=['&amp;url=',e(u),'&amp;title=',e(t||d.title),'&amp;source=',e(r),'&amp;sourceUrl=',e(l),'&amp;content=',c||'gb2312','&amp;pic=',e(p||'')].join('');function%20a(){if(!window.open([f,p].join(''),'mb',['toolbar=0,status=0,resizable=1,width=440,height=430,left=',(s.width-440)/2,',top=',(s.height-430)/2].join('')))u.href=[f,p].join('');};if(/Firefox/.test(navigator.userAgent))setTimeout(a,0);else%20a();})(screen,document,encodeURIComponent,'','','{$topicone['image']}', '推荐 {$topicone['author']} 的文章《{$topicone['title']}》','{url topic/getone/$topicone['id']}','页面编码gb2312|utf-8默认gb2312'));" data-original-title="分享到微博">
          <i class="fa fa-weibo"></i>
        </a>

         <a  class="share-circle" data-toggle="tooltip"  target="_self" data-original-title="分享到qq" href="javascript:shareqq()"   title="分享到QQ"><i class="fa fa-qq"></i></a>

 <a  class="" data-toggle="tooltip"  target="_self" data-original-title="生成海报" href="javascript:showposter('{SITE_URL}',$topicone['id'],'article')"   title="生成海报" style="margin-left: 10px;font-size:12px;"><span class=""><i style="margin-right: 5px;font-size:18px;position:relative;top:3px;" class="fa fa-share-alt"></i></span>生成海报</a>

  <script type="text/javascript">

//  document.write(['<a class="share-circle" data-toggle="tooltip"  target="_self" data-original-title="分享到qq空间" href="http://sns.qzone.qq.com/cgi-bin/qzshare/cgi_qzshare_onekey?url=',encodeURIComponent(location.href),'&title=',encodeURIComponent(document.title),'" target="_self"   title="分享到QQ空间"> <i class="fa fa-qq"></i><\/a>'].join(''));

  function shareqq()
  {
      var p = {
          url: location.href,/*获取URL，可加上来自分享到QQ标识，方便统计*/
          desc: "{eval echo clearhtml(htmlspecialchars_decode( replacewords($topicone['describtion'])),50);}", 
          title : document.title,/*分享标题(可选)*/
          summary : document.title,/*分享描述(可选)*/
          pics : "{$topicone['image']}",/*分享图片(可选)*/
          flash : '', /*视频地址(可选)*/
          //commonClient : true, /*客户端嵌入标志*/
          site: "{$setting['site_name']}"/*分享来源 (可选) ，如：QQ分享*/
      };


      var s = [];
      for (var i in p) {
          s.push(i + '=' + encodeURIComponent(p[i] || ''));
      }
      var target_url = "http://connect.qq.com/widget/shareqq/iframe_index.html?" + s.join('&') ;
      window.open(target_url, 'qq',
              'height=520, width=720');
  }
  
  </script>

      </span>
                            </p>
                            <!--分类,浏览数,评论数,推荐数-->
                            <div class="work-head-box">
                                <div class="head-left">
                                            <span class="head-index">
                                                <span><a href="{url topic/default}" target="_blank">站内文章</a></span>
                                                <i>/</i>
                                                <span><a href="{url topic/catlist/$cat_model['id']}" target="_blank">{$cat_model['name']}</a></span>
                                               
                                            </span>
                              
                                </div>
                                <div class="head-right">
                                            <span class="head-data-show">
                                            <a href="javascript:;" title="共{$topicone['views']}人气" class="see vertical-line">
                                                <i></i>{$topicone['views']}
                                            </a>
                                            <a href="javascript:;" title="共{$topicone['articles']}评论" class="news vertical-line">
                                                <i></i>{$topicone['articles']}
                                            </a>
                                            <a href="javascript:;" title="共{$topicone['likes']}收藏" class="recommend-show">
                                                <i></i>{$topicone['likes']}
                                            </a>
                                            </span>
                                              <!--{if $user['grouptype']==1||$user['uid']==$member['uid']}-->
                <!-- 如果是当前作者，加入编辑按钮 -->
                <a href="javascript:void(0)"  data-toggle="dropdown" class="edit dropdown-toggle">操作 <i class="fa fa-angle-down mar-lr-05"></i> </a>
                 <ul class="dropdown-menu" role="menu">
                {if $user['grouptype']==1}
                       <li>


                    <a href="{url topic/pushhot/$topicone['id']}" data-toggle="tooltip" data-html="true" data-original-title="被推荐文章将会在首页展示">
                        <span>推荐文章</span>
                    </a>
                      </li>
                        <li>


                    <a href="{url topicdata/pushindex/$topicone['id']/topic}" data-toggle="tooltip" data-html="true" data-original-title="被顶置的文章将会在首页列表展示">
                    <span>首页顶置</span>
                    </a>
                      </li>
                      {/if}
                           <li>

                    <a href="{url user/editxinzhi/$topicone['id']}">
                      <span>编辑文章</span>
                    </a>
                      </li>
                        <li>

                    <a href="{url user/deletexinzhi/$topicone['id']}">
                       <span>删除文章</span>
                    </a>
                      </li>

                             </ul>
                               <!--{/if}-->
                                </div>
                            </div>
                        </div>
                    </div>
                    




<div class="top-author-card follow-box col-md-8">
    <div class="card-designer-list-details details-right-author-wrap card-media">

        <input type="hidden" name="creator" value="14876008">
        <div class="avatar-container-80">
            <a href="{url user/space/$member['uid']}" title="{$topicone['author']}" class="avatar" >
                <img src="{$member['avatar']}" width="80" height="80" alt="">
            </a>
            
        </div>
        <div class="author-info">
            <p class="author-info-title">
           
             
                <a href="{url user/space/$member['uid']}" title="{$topicone['author']}" class="title-content">
               {$topicone['author']}
                  {if $topicone['author_has_vertify']!=false}<i class="fa fa-vimeo {if $topicone['author_has_vertify'][0]=='0'}v_person {else}v_company {/if}  " data-toggle="tooltip" data-placement="right" title="" {if $topicone['author_has_vertify'][0]=='0'}data-original-title="个人认证" {else}data-original-title="企业认证" {/if} ></i>{/if}
                </a>





            </p>
        <div class="position-info">
                <span>{if $member['gender']==0}女{/if}{if $member['gender']==1}男{/if}&nbsp;|&nbsp;{$member['signature']}</span>
            </div>
            <div class="btn-area">
                
      
                
                        <div class="js-project-focus-btn">
                                   <!-- 关注用户按钮 -->
                 {if  $is_followedauthor}
                                <input type="button" title="已关注" id="attenttouser_{$topicone['authorid']}" onclick="attentto_user($topicone['authorid'])" class="btn-current attention btn-default-secondary following" value="已关注" >
                              {else}
                               <input type="button" title="添加关注" id="attenttouser_{$topicone['authorid']}" onclick="attentto_user($topicone['authorid'])" class="btn-current attention btn-default-main notfollow" value="关注">
                                {/if}
                        </div>
                            {if $user['uid']}
                        <a href="{url message/sendmessage/$topicone['authorid']}" title="发私信" class="btn-default-secondary btn-current private-letter">私信</a>
                     
                     {else}
                       <a href="javascript:login()" title="发私信" class="btn-default-secondary btn-current private-letter">私信</a>
                     
                      {/if}
            </div>
               
        </div>
    </div>
</div>

                </div>
                
                 <div class="container ">
      
                 <div class="content-wrap">
  
   <div class="details-con-other border-top"  {if !$topicone['describtion']} style="margin-top:0px;"  {/if}>
                <div class="">
                   
             
                         <div class="three-link">
                         
                            <span class="report" onclick="openinform(0,'{$topicone['title']}',{$topicone['id']})"  id="report-modal"><a href="javascript:;">举报 </a></span>
                       
                    {if $isfollowarticle}
                    <input type="button"  class="btn-default-secondary details-collection-btn collection js-not-fav js-project-fav" value="已收藏" data-on="1" title="已收藏">
                        
                   {else}
                   {if $user['uid']}
                    <input type="button" onclick="window.location.href='{url favorite/topicadd/$topicone['id']}'" z-st="favorite" class="btn-default-secondary details-collection-btn collection js-not-fav js-project-fav" value="收藏文章" data-on="1" title="收藏文章">
                      
                      {else}
                       <input type="button" onclick="login()" z-st="favorite" class="btn-default-secondary details-collection-btn collection js-not-fav js-project-fav" value="收藏" data-on="1" title="收藏文章">
                     
                      {/if}  
                    {/if}
                        
                         <button type="button" class="btneditanswer" {if $user['uid']==0}  onclick="login()" {else} onclick="showeditor()" {/if} ><i class="fa fa-pencil"></i>写评论</button>
                      
                     
                    </div>
                    
                
                </div>
            </div>
   
   </div>
                </div>
                
            </div>
            <div class="container">
        
            <div class=" row">
                   <div class="col-md-17 bb" style="margin-top:20px;">
                <div class="work-details-content" >
    
<div class=" ">
    <div class="">

        <div class="work-show-box" style="margin-top:0px;  padding:10px">
            
                     {if $topicone['price']!=0&&$haspayprice==0&&$user['uid']!=$topicone['authorid']}
  {eval echo replacewords(html_entity_decode($topicone['freeconent']));}
  
                         <div class="box_toukan ">

										{if $user['uid']==0}
											<div onclick="login()" class="thiefbox font-12" style="color:#fff;text-decoration:none;" ><i class="icon icon-lock font-12"></i> &nbsp;更多阅读需支付&nbsp;$topicone['price']&nbsp;&nbsp;{eval if ($topicone['readmode']==2) echo '财富值'; }{eval if ($topicone['readmode']==3) echo '元'; }……</div>
											{else}
											<div onclick="viewtopic($topicone['id'])"  class="thiefbox font-12" style="color:#fff;text-decoration:none;" ><i class="icon icon-lock font-12"></i> &nbsp;更多阅读需支付&nbsp;$topicone['price']&nbsp;&nbsp;{eval if ($topicone['readmode']==2) echo '财富值'; }{eval if ($topicone['readmode']==3) echo '元'; }……</div>
											{/if}


										</div>
                   {else}
                   {eval echo  replacewords(html_entity_decode($topicone['describtion']));}
           
              
                    {/if}





        </div>

    </div>
</div>

                



            </div>
      <hr>
         <div class="index">
<div class="row">
   <div class=" main" style="padding:10px">
   <div class="note">
    <div class="post">
       


 


       <div id="comment-list" class="comment-list"><div>
            {if $user['uid']!=0}
 <form class="new-comment">
  <input type="hidden" id="artitle" value="{$topicone['title']}" />
    <input type="hidden" id="artid" value="{$topicone['id']}" />
 <a class="avatar">
 <img src="{$user['avatar']}">
 </a>
 <textarea onkeydown="return topickeydownlistener(event)"  placeholder="写下你的评论..." class="comment-area"></textarea>
 <div class="write-function-block"> <div class="hint">Ctrl+Enter 发表</div>
 <a class="btn btn-send btn-cm-submit" name="comments" id="comments">发送</a> </div>
 </form>
   {else}
  <form class="new-comment"><a class="avatar"><img src="{$user['avatar']}"></a> <div class="sign-container"><a href="{url user/login}" class="btn btn-sign">登录</a> <span>后发表评论</span></div></form>

            {/if}

        </div>
        <div id="normal-comment-list" class="normal-comment-list">

        <div class="top">
        <span>{$commentrownum}条评论</span>


           </div>
           </div>
           <!----> <!---->
            <!--{if $commentlist==null}-->
            <div class="no-comment"></div>
  <center>      
           还没有人评论过~
         </center>
           

              <!--{/if}-->
          <!--{loop $commentlist $index $comment}-->
            <div id="comment-{$comment['id']}" class="comment">
            <div>
            <div class="author">
            <a href="{url user/space/$comment['authorid']}" target="_self" class="avatar">
            <img src="{$comment['avatar']}">
            </a>
            <div class="info">
            <a href="{url user/space/$comment['authorid']}" target="_self" class="name">
            {$comment['author']}
              {if $comment['author_has_vertify']!=false}<i class="fa fa-vimeo {if $comment['author_has_vertify'][0]=='0'}v_person {else}v_company {/if}  " data-toggle="tooltip" data-placement="right" title="" {if $comment['author_has_vertify'][0]=='0'}data-original-title="个人认证" {else}data-original-title="企业认证" {/if} ></i>{/if}
            </a>
            <!---->
             <div class="meta">
             <span>{eval echo ++$index;}楼 · {$comment['time']}</span>
             </div>
             </div>
             </div>
             <div class="comment-wrap">
             <p>
              {$comment['content']}
             </p>

                </div>
                </div>
                 <div class="tool-group">
             <a class="button_agree" id='{$comment['id']}'><i class="fa fa-thumbs-o-up"></i> <span>{$comment['supports']}人赞</span></a>

<a class="getcommentlist" dataid='{$comment['id']}' datatid="{$topicone['id']}"><i class="fa fa-comment"></i> <span>回复{$comment['comments']}</span></a>

                <!--{if 1==$user['grouptype'] ||$user['uid']==$comment['authorid']}-->

    <a data-placement="bottom" title="" data-toggle="tooltip" data-original-title="删除评论"   href="javascript:void(0);" onclick="deletewenzhang($comment['id'])"><i class="fa fa-bookmark-o"></i> <span>删除</span></a>
     <!--{/if}-->

                <!---->
                </div>
               <div class="sub-comment-list  hide" dataflag="0" id="articlecommentlist{$comment['id']}">
              <div class="commentlist{$comment['id']}">

              </div>
              <div class="sub-comment more-comment">
              <a class="add-comment-btn" dataid="{$comment['id']}"><i class="fa fa-edit"></i>
               <span>添加新评论</span></a>
               <!----> <!----> <!---->
               </div>
                <div class="formcomment{$comment['id']} hide">
                <form class="new-comment">
                <!---->
                <textarea placeholder="写下你的评论..." class="commenttext{$comment['id']}"></textarea>
                 <div class="write-function-block">


                  <a class="btn btn-send  btn-sendartcomment" id="btnsendcomment{$comment['id']}"  dataid="{$comment['id']}" datatid="{$topicone['id']}">发送</a>

                  </div>
                  </form>
                   <!---->
                   </div>
                   </div>
                </div>
 <!--{/loop}-->
  <div class="pages" >{$departstr}</div>
            
    
             </div>
            
    </div>


</div>



   </div>
   
</div>
</div>
                  </div>
                         <div class="col-md-7 aside">
                         
     <div class="standing">
  <div class="positions bb" id="rankScroll">
      <h3 class="title">Ta的文章  <a target="_blank" href="{url topic/userxinzhi/$member['uid']}" class="more">更多<font> &gt;&gt; </font></a></h3>
       {if  $topiclist1}
      <ul>
      


      <!--{loop $topiclist1 $index $topic}-->
              <li class="no-video">
        <a href="{url topic/getone/$topic['id']}" title="{$topic['title']}" >  {$topic['title']}</a>
               <div class="num-ask">
               <a href="{url topic/getone/$topic['id']}" title="{$topic['title']}" class="anum"> {$topic['articles']} 个评论</a>
               </div>
              </li>
                <!--{/loop}-->
               

              </ul>
                {/if}
  </div>
  </div>

  
  <!--{template sider_hotarticle}-->
                  </div>
            </div>
               </div>
            
        </div>
      
<!-- 举报 -->
<div class="modal fade panel-report" id="dialog_inform">
<div class="modal-dialog">
  <div class="modal-content">
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">关闭</span></button>
      <h4 class="modal-title">举报内容</h4>
    </div>
    <div class="modal-body">

<form id="rp_form" class="rp_form"  action="{url inform/add}" method="post">
<input value="" type="hidden" name="qid" id="myqid">
<input value="" type="hidden" name="aid" id="myaid">
<input value="" type="hidden" name="qtitle" id="myqtitle">
<div class="js-group-type group group-2">
<h4>检举类型</h4><ul>
<li class="js-report-con">
<label><input type="radio" name="group-type" value="1"><span>检举内容</span></label>
</li>
<li class="js-report-user">
<label><input type="radio" name="group-type" value="2"><span>检举用户</span></label>
</li>
</ul>
</div>
<div class="group group-2">
<h4>检举原因</h4><div class="list">
<ul>
<li>
<label class="reason-btn"><input type="radio" name="type" value="4"><span>广告推广</span></label>
</li>
<li>
<label class="reason-btn"><input type="radio" name="type" value="5"><span>恶意灌水</span></label>
</li>
<li>
<label class="reason-btn"><input type="radio" name="type" value="6"><span>回答内容与提问无关</span>
</label>
</li>
<li>
<label class="copy-ans-btn"><input type="radio" name="type" value="7"><span>抄袭答案</span></label>
</li>
<li>
<label class="reason-btn"><input type="radio" name="type" value="8"><span>其他</span></label>
</li>
</ul>
</div>
</div>
<div class="group group-3">
<h4>检举说明(必填)</h4>
<div class="textarea">
<ul class="anslist" style="display:none;line-height:20px;overflow:auto;height:171px;">
</ul>
<textarea name="content" maxlength="200" placeholder="请输入描述200个字以内">
</textarea>
</div>
</div>
    <div class="mar-t-1">

                <button type="submit" id="btninform" class="btn btn-success">提交</button>
                 <button type="button" class="btn btn-default mar-ly-1" data-dismiss="modal">关闭</button>
      </div>
</form>


    </div>

  </div>
</div>
</div>
<div class="modal share-wechat animated" style="display: none;"><div class="modal-dialog"><div class="modal-content"><div class="modal-header"><button type="button" data-dismiss="modal" class="close">×</button></div> <div class="modal-body"><h5>打开微信“扫一扫”，打开网页后点击屏幕右上角分享按钮</h5> <div data-url="{url topic/getone/$topicone['id']}" class="qrcode" title="{url topic/getone/$topicone['id']}"><canvas width="170" height="170" style="display: none;"></canvas>
<div id="qr_wxcode">
</div></div></div> <div class="modal-footer"></div></div></div></div>
<script type="text/javascript" src="{SITE_URL}static/ckplayer/ckplayer.js" charset="utf-8"></script>
<script type="text/javascript" src="{SITE_URL}static/ckplayer/video.js" charset="utf-8"></script>
<script>

$(".work-show-box").find("img").each(function(){
	var imgurl=$(this).attr("data-original");
	$(this).attr("src",imgurl);
})
$(".work-show-box").find("img").attr("data-toggle","lightbox");

$(".getcommentlist").each(function(){
	var _id=$(this).attr("dataid");
	var _tid=$(this).attr("datatid");
	$("#articlecommentlist"+_id).toggleClass("hide");
	var flag=$("#articlecommentlist"+_id).attr("dataflag");
	if(flag==1){
		flag=0;
	}else{
		flag=1;
		//加载评论
		loadarticlecommentlist(_id,_tid);
	}
	$("#articlecommentlist"+_id).attr("dataflag",flag);
	
})

function showeditor(){
	
	scrollTo(0,$('#comment-list').offset().top-100);
	$(".comment-area").focus();
	  }
//投诉
function openinform(qid ,qtitle,aid) {
	  $("#myqid").val(qid);
	  $("#myqtitle").val(qtitle);
	  $("#myaid").val(aid);
	 $('#dialog_inform').modal('show');

}






function deletewenzhang(current_aid){
	window.location.href=g_site_url + "index.php" + query + "topic/deletearticlecomment/"+current_aid+"/$topicone['id']";

}

$(function(){
    $(".edui-upload-video").attr("preload","");
		//微信二维码生成
		$('#qr_wxcode').qrcode("{url topic/getone/$topicone['id']}");
	     //显示微信二维码
	     $(".share-weixin").click(function(){
	    	 $(".share-wechat").show();
	     });
	     //关闭微信二维码
	     $(".close").click(function(){
	    	 $(".share-wechat").hide();
	     })
})
 $(".button_agree").click(function(){
             var supportobj = $(this);
                     var tid = $(this).attr("id");
                     $.ajax({
                     type: "GET",
                             url:"{SITE_URL}index.php?topic/ajaxhassupport/" + tid,
                             cache: false,
                             success: function(hassupport){
                             if (hassupport != '1'){






                                     $.ajax({
                                     type: "GET",
                                             cache:false,
                                             url: "{SITE_URL}index.php?topic/ajaxaddsupport/" + tid,
                                             success: function(comments) {

                                             supportobj.find("span").html(comments+"人赞");
                                             }
                                     });
                             }else{
                            	 alert("您已经赞过");
                             }
                             }
                     });
             });
</script>
<!--{template footer}-->