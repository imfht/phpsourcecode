<!--{template header}-->
<style>
.mymenulist{
background:#ebebeb;
}
</style>
<section class="ui-container mymenulist">
<!--{template user_title}-->
{if $this->uri->segments[3]=='set'}
   <div class="usertoolist">
 <div class="weui-flex toolitem">
   
     <div class="weui-flex__item text">
            <a href="{url user/usernotify}"> 私信和通知设置</a>
     </div>
      <div><i class="icon_jinru"></i></div>
 </div>
  <div class="weui-flex toolitem">
   
     <div class="weui-flex__item text">
            <a href="{url user/editimg}"> 修改头像</a>
     </div>
      <div><i class="icon_jinru"></i></div>
 </div>
   <div class="weui-flex toolitem">
   
     <div class="weui-flex__item text">
            <a href="{url user/profile}"> 修改个人信息</a>
     </div>
      <div><i class="icon_jinru"></i></div>
 </div>
     <div class="weui-flex toolitem">
   
     <div class="weui-flex__item text">
            <a href="{url user/uppass}">修改密码</a>
     </div>
      <div><i class="icon_jinru"></i></div>
 </div>
      <div class="weui-flex toolitem">
   
     <div class="weui-flex__item text">
            <a href="{url user/editemail}">激活邮箱</a>
     </div>
      <div><i class="icon_jinru"></i></div>
 </div>
       <div class="weui-flex toolitem">
   
     <div class="weui-flex__item text">
            <a href="{url user/logout}">退出</a>
     </div>
    
 </div>
 </div>
 
{else}
 <div class="usertoolist">
 
        <!--{if $user['groupid']<=3}-->
        
                                  
                        
            
                 <div class="weui-flex toolitem">
    <div class="myicon"><i class="icon_inans"></i></div>
     <div class="weui-flex__item text">
           <a href="{SITE_URL}index.php?admin_main">
                       后台管理
                    </a>
     </div>
      <div><i class="icon_jinru"></i></div>
 </div>
                                    <!--{/if}--> 
                                    

  <div class="weui-flex toolitem">
    <div class="myicon"><i class="icon_sixin"></i></div>
     <div class="weui-flex__item text">
     <a href="{url message/personal}">
       我的私信    <span class="weui-badge fmsg-count" style="position: absolute;top: -.4em;left: 7em;"></span>
     </a>
          
     </div>
      <div><i class="icon_jinru"></i></div>
 </div>
 
 </div>
 
   <div class="usertoolist">
 <div class="weui-flex toolitem">
    <div class="myicon"><i class="fa fa-registered"></i></div>
     <div class="weui-flex__item text">
            <a href="{url user/myjifen}"> 我的财富</a>
     </div>
      <div><i class="icon_jinru"></i></div>
 </div>
      <!--{if $user['groupid']>3}-->
  <div class="weui-flex toolitem">
    <div class="myicon"><i class="fa fa-sort-amount-desc"></i></div>
     <div class="weui-flex__item text">
           
            <a href="{url user/level}"> 我的等级</a>
     </div>
      <div><i class="icon_jinru"></i></div>
 </div>
       <!--{/if}--> 
    
 </div>
 


   
  <div class="usertoolist">
   <div class="weui-flex toolitem">
    <div class="myicon"><i class="fa fa-heart-o" style="top:0px"></i></div>
     <div class="weui-flex__item text">
            <a href="{url user/attention/question}"> 我的收藏</a>
     </div>
      <div><i class="icon_jinru"></i></div>
 </div>
 <div class="weui-flex toolitem">
    <div class="myicon"><i class="icon_tiwen"></i></div>
     <div class="weui-flex__item text">
            <a href="{url user/ask}"> 我的提问</a>
     </div>
      <div><i class="icon_jinru"></i></div>
 </div>
  <div class="weui-flex toolitem">
    <div class="myicon"><i class="icon_daan"></i></div>
     <div class="weui-flex__item text">
     <a href="{url user/answer}">
       我的回答
     </a>
          
     </div>
      <div><i class="icon_jinru"></i></div>
 </div>
  <div class="weui-flex toolitem">
    <div class="myicon"><i class="icon_wenzhang"></i></div>
     <div class="weui-flex__item text">
            <a href="{url topic/userxinzhi/$user['uid']}"> 我的文章</a>
     </div>
      <div><i class="icon_jinru"></i></div>
 </div>

   <div class="weui-flex toolitem">
    <div class="myicon"><i class="icon_inans"></i></div>
     <div class="weui-flex__item text">
     <a href="{url user/invateme}">
       邀请回答
     </a>
          
     </div>
      <div><i class="icon_jinru"></i></div>
 </div>
 </div>
 
  <div class="usertoolist">
 <div class="weui-flex toolitem">
    <div class="myicon"><i class="icon_haoyouyaoqing"></i></div>
     <div class="weui-flex__item text">
            <a href="{url user/invatelist}"> 邀请好友</a>
     </div>
      <div><i class="icon_jinru"></i></div>
 </div>
  <div class="weui-flex toolitem">
    <div class="myicon"><i class="icon_renzheng"></i></div>
     <div class="weui-flex__item text">
     <a href="{url user/vertify}">
           个人 认证
     </a>
          
     </div>
      <div><i class="icon_jinru"></i></div>
 </div>
 
 </div>
{/if}
</section>


<!--{template footer}-->