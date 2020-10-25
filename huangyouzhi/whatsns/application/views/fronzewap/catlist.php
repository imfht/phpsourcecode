<!--{template meta}-->
<section class="ui-container">
    <style>
        body{
            background: #f1f5f8;
        }
    </style>
    <div class="ws_header">
        <i class="fa fa-home" onclick="window.location.href='{url index}'"></i>
        <div class="ws_h_title">{$setting['site_name']}</div>
        <i class="fa fa-search"  onclick="window.location.href='{url question/searchkey}'"></i>
    </div>
     <!--话题介绍-->

                    <div class="au_category_huati_info">
                           <div class="ui-row-flex">
                               <div class="ui-col">
                                   <div class="au_category_huati_img">
                                    <a href="{url topic/catlist/$catmodel['id']}">
                                       <img src="$catmodel['bigimage']">
                                       </a>
                                   </div>
                               </div>
                               <div class="ui-col ui-col-3">
                                   <div class="au_category_huati_name"> <a  href="{url topic/catlist/$catmodel['id']}"> {$catmodel['name']}</a></div>
                                   <div class="au_category_info_meta">
                                       <div class="au_category_info_meta_item"><i class="fa fa-question-circle hong"></i>{$catmodel['questions']}个问题</div>
                                       <div class="au_category_info_meta_item"><i class="fa fa-user lan"></i>{$catmodel['followers']}人关注</div>
                                       <div class="au_category_info_meta_item"><i class="fa fa-file-text-o ju "></i>{$trownum}篇文章</div>
                                   </div>
                               </div>
                         
                           </div>

                        <!--子话题-->
                        {if $catlist}
                        <div class="ui-row-flex">
                            <div class="i-col ui-col  au_category_info_childlist">

                                <div class="swiper-container" >
                                    <div class="swiper-wrapper">
                                        <!--{loop $catlist $index $cat}-->
                                        
                                        <div class="swiper-slide" data-swiper-autoplay="2000">
                                         
                                            
                                                    <div class="au_category_info_child">
                                                        <a href="{url topic/catlist/$cat['id']}">
                                                        <div class="au_category_info_child_img">
                                                            <img src="$cat['image']">
                                                        </div>
                                                        <p class="au_category_info_child_text">{$cat['name']}</p>
                                                        </a>
                                                    </div>
                                                

                                           
                                        </div>
                                  <!--{/loop}--> 
                                    </div>

                                </div>
                                <!-- 如果需要导航按钮 -->
                                <div class="swiper-button-prev"></div>
                                <div class="swiper-button-next"></div>



                            </div>

                        </div>
                       {/if}
                    </div>
                  
                     <!--导航提示-->
                    <div class="au_brif wzbrif">
                        <span class="au_bref_item current "> 
                           <a href="{url topic/catlist/$catmodel['id']}">全部文章</a> </span>
                               {if $catmodel['isuseask']}
                         <span class="au_bref_item">
                         <a href="{url category/view/$catmodel['id']}">相关讨论</a>
  </span> {/if}
                    </div>
                       <!--列表部分-->
                      <div class="qlists">
 
    <div class="stream-list blog-stream">
     <!--{loop $topiclist $index $topic}-->   

<section class="stream-list__item"><div class="blog-rank stream__item"><div  class="stream__item-zan   btn btn-default mt0"><span class="stream__item-zan-icon"></span><span class="stream__item-zan-number">{$topic['articles']}</span></div></div><div class="summary"><h2 class="title blog-type-common blog-type-1"><a href="{url topic/getone/$topic['id']}">{$topic['title']}</a></h2><ul class="authorme list-inline"><li>
<span style="vertical-align:middle;"><a href="{url user/space/$topic['authorid']}">{$topic['author']}   {if $topic['author_has_vertify']!=false}
        <i class="fa fa-vimeo {if $topic['author_has_vertify'][0]=='0'}v_person {else}v_company {/if}  "  ></i>{/if}</a>
                    
                    发布于
                                           {$topic['format_time']}</span></li><li class="bookmark " title="{$topic['articles']} 收藏" ></li></ul></div></section>

  <!--{/loop}-->
</div>
    
   
   

</div>
                 
              
               <div class="pages">{$departstr}</div>
                    








<script>
    var swiper = new Swiper('.swiper-container', {
        loop:true,
        autoplay:2000,
        slidesPerView: 3,
        paginationClickable: true,
        spaceBetween: 10,
        // 如果需要前进后退按钮
        navigation: {
            nextEl: '.swiper-button-next',
            prevEl: '.swiper-button-prev',
        }
    });
    var intswper=setInterval("swiper.slideNext()", 2000);
    $(".swiper-container").hover(function(){
        clearInterval(intswper);
    },function(){
        intswper=setInterval("swiper.slideNext()", 2000);
    })
</script>
</section>
<!--{template footer}-->