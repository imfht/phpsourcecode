
   <!--{if $setting['cancopy']==1}-->
              <script src="{SITE_URL}static/js/nocopy.js"></script>
                <!--{/if}-->

<script src="{SITE_URL}static/js/jquery.lazyload.min.js"></script>
<script>

 <!--{if $setting['opensinglewindow']==1}-->
 $("a").attr("target","_self");

                <!--{/if}-->
  

                    $("img.lazy").lazyload({effect: "fadeIn" });

</script>

  <div class="side-tool" id="to_top"><ul><li data-placement="left" data-toggle="tooltip" data-container="body" data-original-title="回到顶部" >
    <a href="#" class="function-button"><i class="fa fa-angle-up"></i></a>
    </li>



      </ul></div>
      <script>
window.onload = function(){
	  $(".edui-upload-video").attr("preload","");
  var oTop = document.getElementById("to_top");

  var screenw = document.documentElement.clientWidth || document.body.clientWidth;
  var screenh = document.documentElement.clientHeight || document.body.clientHeight;
  window.onscroll = function(){
    var scrolltop = document.documentElement.scrollTop || document.body.scrollTop;
 
    if(scrolltop<=screenh){
    	oTop.style.display="none";
    }else{
    	oTop.style.display="block";
    }
    if(scrolltop>30){
	     
    	$(".scrollshow").show();
    }else{
    	$(".scrollshow").hide();
    }
  }
  oTop.onclick = function(){
    document.documentElement.scrollTop = document.body.scrollTop =0;
  }
}

</script>

    <footer id="footer">
        <div class="footer-wrapper">
            <div class="footer-wrapper-top">
                <div class="footer-wrapper-top-left">
                    <a href="{url tags}"><i class="hide"></i>标签大全</a>
                    <a href="{url new}" >站内问题</a>
                    <a href="{url topic/default}" >专栏文章</a>
                    <a href="{url expert/default}">站内专家</a>
                    <a href="{url category/viewtopic/hot}" >站内话题</a>
                    <a href="{url note/list}" >站内公告</a>
                     <a href="{url rule/index}">财富值规则</a>
     <!--{if $setting['site_statcode']}--> {eval echo decode($setting['site_statcode'],'tongji');}<!--{/if}-->

                </div>
               
            </div>
            
                     {if $regular=='index/index'}
                     
                            <div class="container youlian">
                <ul class="list-unstyled list-inline">
            <li>友情链接</li>
             <!--{eval $links=$this->fromcache('link');}-->

         <!--{if $links }-->


              <!--{loop $links $link}-->
              
                        <li><a target="_blank" href="{$link['url']}" title="{$link['description']}">    {$link['name']}</a></li>
                <!--{/loop}-->
   <!--{/if}-->
                    </ul>
       
      
    </div>
    
    
            
            
            
   {/if}
   
         
           
            <div class="footer-wrapper-bottom space-footer-bottom">
                <a href="http://www.12377.cn/" target="_blank">网上有害信息举报专区</a>
                    <i></i>
                    <a href="http://beian.miit.gov.cn" target="_blank">{$setting['site_icp']}</a>
                <i></i>
                <span ><a href="{eval echo str_replace('.html','.xml',url('rss/articlelist'));}" target="_blank">站内文章地图xml</a></span>
                <i></i>
                <span><a href="{eval echo str_replace('.html','.xml',url('rss/list'));}" target="_blank">站内问答地图xml</a></span>
                <i></i>
                <span><a href="{eval echo str_replace('.html','.xml',url('rss/userspace'));}" target="_blank">站内作者地图xml</a></span>
               <i></i>
                <span><a href="{eval echo str_replace('.html','.xml',url('rss/tag'));}" target="_blank">站内标签地图xml</a></span>
            
                <span class="copyrightLink">Copyright © 2016-2018 <a href="https://www.whatsns.com/" target="_blank">WHATSNSV{ASK2_VERSION}</a></span>
            </div>
        </div>
    </footer>

</div>

</body>
</html>
