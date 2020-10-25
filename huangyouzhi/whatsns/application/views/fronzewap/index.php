<!--{template header}-->
<style>
<!--
.ui-container{
    background: #F6F6F6;
    min-height: 100vh;
    }
    
.tit-money {
    font-size: 14px;
    color: #f19049;
    height: 30px;
    line-height: 30px;
    padding: 0 10px;
    display: inline-block;
    background: #fff8e5;
    -webkit-border-radius: 10px;
    border-radius: 20px;
    vertical-align: bottom;
    margin-right: 10px;
    font-weight: 400;
}
-->
</style>


<div class="Card Topstory-noMarginCard Topstory-tabCard">
<ul role="tablist" class="Tabs">
<li role="tab" class="Tabs-item Tabs-item--noMeta">
<a class="Tabs-link {if !$_valname}is-active{/if}" href="{SITE_URL}">最新</a></li>
<li role="tab" class="Tabs-item Tabs-item--noMeta" >
<a class="Tabs-link {if $_valname=='xuanshang'}is-active{/if}" href="{url content/xuanshang}">悬赏</a></li>
<li role="tab" class="Tabs-item Tabs-item--noMeta" >
<a class="Tabs-link {if $_valname=='tuijian'}is-active{/if}" href="{url content/tuijian}">推荐</a></li>
<li role="tab" class="Tabs-item Tabs-item--noMeta" >
<a class="Tabs-link  {if $_valname=='solve'}is-active{/if}" href="{url content/solve}">热门</a></li>
<li role="tab" class="Tabs-item Tabs-item--noMeta" >
<a class="Tabs-link {if $_valname=='nosolve'}is-active{/if}" href="{url content/nosolve}">待解答</a></li>
</ul><div><div><div class="Sticky" style=""></div></div></div></div>
    <div class="whatsns_list">
    {if !$_valname||$_valname=='new'}
        <!--{eval $topdatalist=$this->fromcache('topdata');}-->
                <!--{loop $topdatalist  $topdata}-->
                
       <div class="whatsns_listitem">
         <div class="l_title"><h2><a href="{$topdata['url']}">{$topdata['title']}</a></h2></div>

       <div class="whatsns_content">
          
    {if $topdata['model']['image']}
<div class="weui-flex">



   <div class="weui-flex__item"><div class="imgthumbbig"><a href="{$topdata['url']}"><img class="lazy" src="{SITE_URL}static/images/lazy.jpg" data-original="$topdata['model']['image']"></a></div></div>



</div>
 {/if}
      {if $topdata['description']}
 <div class="whatsns_des">
 <span class="mtext" >{eval echo clearhtml($topdata['description'],100);}</span>
 
 <div class="whatsns_readmore">查看更多<i class="fa fa-angle-down"></i></div>
 </div>
  {/if}
       </div>
     <div class="ask-bottom">
     
          <a href="{$topdata['url']}" class="" ><i class="fa fa-commentingicon"></i>{$topdata['answers']} 个回复</a>
          <a href="{$topdata['url']}"  class=" "><i class="fa fa-qshoucang"></i>{$topdata['attentions']}个收藏</a>
             </div>
              </div>
                   <!--{/loop}-->
              
                {if !$_valname}
                       <!--{eval $doinglist=$this->fromcache('doinglist');}-->
        {/if}   
        
         <!--{loop $doinglist $doing}-->
                <div class="whatsns_listitem">
         <div class="l_title"><h2><a href="{$doing['url']}">
         {$doing['content']}{if $doing['question']['price']}<label class="tit-money">奖励$doing['question']['price']财富值</label>{/if}{if $doing['question']['shangjin']}
               
                <span data-toggle="tooltip" data-placement="bottom" title="" data-original-title="如果回答被采纳将获得 $doing['question']['shangjin']元，可提现" class="icon_hot"><i class="fa fa-hongbao mar-r-03"></i>悬赏$doing['question']['shangjin']元</span>
               
                {/if}
         </a></h2></div>

       <div class="whatsns_content">
  {if $doing['image']}
<div class="weui-flex">



   <div class="weui-flex__item"><div class="imgthumbbig"><a href="{$doing['url']}"><img class="lazy" src="{SITE_URL}static/images/lazy.jpg" data-original="$doing['image']"></a></div></div>



</div>
 {/if}
  {if $doing['description']}
 <div class="whatsns_des">
 <span class="mtext" >{$doing['description']}</span>
 <div class="whatsns_readmore" onclick="window.location='{$doing['url']}'">查看更多<i class="fa fa-angle-down"></i></div>
 </div>
  {/if}
       </div>
        <div class="ask-bottom">

          <a href="{$doing['url']}" class="" ><i class="fa fa-commentingicon"></i>{$doing['answers']} 个{if $doing['action']==9}评论{else}回复{/if}</a>
          <a href="{$doing['url']}"  class=" "><i class="fa fa-qshoucang"></i>{$doing['attentions']}个收藏</a>
     </div>
              </div>
                    <!--{/loop}-->    
                    
                     {else}
                     
                       <!--{loop $questionlist $question}-->
                       
                        <div class="whatsns_listitem">
         <div class="l_title"><h2><a href="{url question/view/$question['id']}">
      {$question['title']}{if $question['price']}<label class="tit-money">奖励$question['price']财富值</label>{/if}{if $question['shangjin']}
              
                  <span data-toggle="tooltip" data-placement="bottom" title="" data-original-title="如果回答被采纳将获得 $question['shangjin']元，可提现" class="icon_hot"><i class="fa fa-hongbao mar-r-03"></i>悬赏$question['shangjin']元</span>
               
                {/if}</a></h2></div>

       <div class="whatsns_content">
  
 
   {if $question['image']}
<div class="weui-flex">



   <div class="weui-flex__item"><div class="imgthumbbig"><a href="{url question/view/$question['id']}"><img class="lazy" src="{SITE_URL}static/images/lazy.jpg" data-original="$question['image']"></a></div></div>



</div>
 {/if}
 
 
         {if $question['description']}
 <div class="whatsns_des">
 <span class="mtext" >{$question['description']}</span>
 <div class="whatsns_readmore" onclick="window.location='{url question/view/$question['id']}'">查看更多<i class="fa fa-angle-down"></i></div>
 </div>
  {/if}
       </div>
<div class="ask-bottom">
   
          <a href="{url question/view/$question['id']}" class="" ><i class="fa fa-commentingicon"></i>{$question['answers']} 个回复</a>
          <a href="{url question/view/$question['id']}"  class=" "><i class="fa fa-qshoucang"></i>{$question['attentions']}个收藏</a>
               </div>
              </div>
                          <!--{/loop}-->    
                          
                             {/if}
     <div class="pages">{$departstr}</div>
             
    </div>

 

</section>

<script src="{SITE_URL}static/js/jquery-1.11.3.min.js"></script>
<script>$.noConflict();</script>
<script>


    jQuery(".au_tabs .au_tab").click(function(){

    	jQuery(".xm-tag").addClass("hide");
    	jQuery(".au_tabs .au_tab").removeClass("current");
    	jQuery(this).addClass("current");
    	jQuery("."+$(this).attr("data-tag")).removeClass("hide");
    })
</script>
<!--{template footer}-->
