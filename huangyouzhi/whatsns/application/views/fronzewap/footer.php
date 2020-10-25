<div id="footer">
    <div class="container">
                <div class="text-center">
            <a href="{SITE_URL}">{$setting['site_name']}</a><span class="span-line">|</span>
            <a href="mailto:huangyouzhi@zhengyingkeji.com" target="_blank">联系我们</a><span class="span-line">|</span>
                        <a href="http://beian.miit.gov.cn" target="_blank">{$setting['site_icp']}</a><span class="span-line">|</span>
                        <a href="{eval echo str_replace('.html','.xml',url('rss/list'));}">sitemap</a>
        </div>
        <div class="copyright mt-10">
            Powered By <a href="https://www.whatsns.com" target="_blank">WHATSNSV{ASK2_VERSION}</a> Release {ASK2_RELEASE} 
        </div>
    </div>
</div>
</section>
<script src="{SITE_URL}static/css/fronze/js/main.js?v1.1"></script>
  <div style="display:none;"> <!--{if $setting['site_statcode']}--> {eval echo decode($setting['site_statcode'],'tongji');}<!--{/if}--></div>
<style>
  .ui-footer-btn .ui-tiled .current, .ui-footer-btn .ui-tiled i.current {
        color:#0085ee;
    }
    .ui-footer li i{
        font-size: .28rem;
        color: #333333;
    }
    .ui-footer li h6{
        font-size: .13rem;
    }
    .ui-footer li h6 {
    font-size: .15rem;
}
    .ui-footer-btn .ui-tiled  i.fa-question-circle-o{
    margin-top: -24px;
    font-size: 56px;
    line-height: 50px;
    color: #0085ee;
    border-radius: 50%;
    background: #fff;
    box-shadow: 0 -2px 4px 0 rgba(223,223,223,.5);
    border: none;
    }
    .ui-footer li .myaddquestion h6{
     position:relative;
     top:6px;
    }
</style>
<footer class="ui-footer ui-footer-btn {if $hidefooter} hide {/if}">
    <ul class="ui-tiled ui-border-t">
        <li class="<!--{if $regular=='index/default' || $regular==''|| $regular=='index'|| $regular=='index/index'}--> current<!--{/if}-->">
            <a href="{SITE_URL}" class="">

                <i class="fa fa-home <!--{if $regular=='index/default' || $regular==''|| $regular=='index'|| $regular=='index/index'}--> current<!--{/if}-->" style="line-height: 34px;"></i>
                <div class="ui-txt-muted <!--{if $regular=='index/default' || $regular==''|| $regular=='index'|| $regular=='index/index'}--> current<!--{/if}-->"><h6>首页</h6></div>
            </a>

        </li>
         <li>
            <a href="{url seo/index}">
                <i class="fa fa-sticky-note-o <!--{if $regular=='seo/index'}--> current<!--{/if}-->" style="line-height: 34px;"></i>
                <div class="ui-txt-muted <!--{if $regular=='seo/index'}--> current<!--{/if}-->"><h6>专栏</h6></div>

            </a>
        </li>
        
        <li>
            <a href="{url question/add}">
                <i class="fa fa-question-circle-o <!--{if $regular=='question/add'}--> current<!--{/if}-->" style="line-height: 34px;"></i>
                <div  class="myaddquestion ui-txt-muted <!--{if $regular=='question/add'}--> current<!--{/if}-->"><h6>提问</h6></div>

            </a>
        </li>
       
             
        <li>
            <a href="{url expert/index}">
                <i class="fa fa-user <!--{if $regular=='expert/index'}--> current<!--{/if}-->" style="line-height: 34px;"></i>
                <div  class="ui-txt-muted <!--{if $regular=='expert/index'}--> current<!--{/if}-->"><h6>专家</h6></div>

            </a>
        </li>
        
        <li>
         <!--{if $user['uid']!=0}--> 
         
          <a href="{url user/default}">
                <img src="{$user['avatar']}" style="width:.3rem;height:.3rem;border-radius:50%;position:relative;top:.03rem;"/>
               
                <div class="ui-txt-muted <!--{if $regular=='user/index'}--> current<!--{/if}-->"><h6>我的</h6></div>

            </a>
            
  
         <!--{/if}-->
           
            {if $user['uid']==0}
             <a href="{url user/login}" >
               
                <i class="fa fa-user-o " style="line-height: 34px;"></i>
                <div class="ui-txt-muted "><h6>我的</h6></div>

            </a>
            {/if}
        </li>

    </ul>

</footer>
<div id="to_top"></div>

<script src="{SITE_URL}static/js/jquery-1.11.3.min.js"></script>
<script>$.noConflict();</script>
<script src="{SITE_URL}static/js/jquery.lazyload.min.js"></script>
<script type="text/javascript">
jQuery(".qlist img").addClass("lazy");


 jQuery("img.lazy").lazyload({effect: "fadeIn" });
</script>
<script>
$(document).ready(function(){  
	  $(".edui-upload-video").attr("preload","");
    var p=0,t=0;  
    var oTop = document.getElementById("to_top");
    var screenw = document.documentElement.clientWidth || document.body.clientWidth;
    var screenh = document.documentElement.clientHeight || document.body.clientHeight;
    $(window).scroll(function(e){  
            p = $(this).scrollTop();  
            var scrolltop = document.documentElement.scrollTop || document.body.scrollTop;
            if(scrolltop<=screenh){
            	oTop.style.display="none";
            }else{
            	oTop.style.display="block";
            }
            if(t<=p){//下滚  
            	if(scrolltop>50){
            		 $(".nav_top").hide();
            	}
        
            }  
              
            else{//上滚  
            	$(".nav_top").show();
            	
            }  
            setTimeout(function(){t = p;},0);         
    });  
    oTop.onclick = function(){
        document.documentElement.scrollTop = document.body.scrollTop =0;
      }
});  
  

</script>
</body>
</html>