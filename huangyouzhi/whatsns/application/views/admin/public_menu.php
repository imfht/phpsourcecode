{eval $regular=$this->regular;}
<ul class="sidebar-menu" id="root_menu">
    <li class="header">管理菜单</li>
    <li><a href="{SITE_URL}index.php?admin_main/stat{$setting['seo_suffix']}"><i class="fa fa-dashboard"></i> <span>首页</span> </a></li>
    <li class=""><a href="http://store.whatsns.com/#/" target="_blank"><i class="iconnew"></i> <span class="red">应用商店</span> </a></li>

         <!--{template chajian}-->
        
        <li class="treeview">
        <a href="#">
            <i class="fa  fa-certificate"></i> <span>系统设置</span>
            <i class="fa fa-angle-left pull-right"></i>
        </a>
        <ul class="treeview-menu" id="manage_user">
             <li><a href="{SITE_URL}index.php?admin_setting/sitesetting{$setting['seo_suffix']}" target="main"><i class="fa fa-genderless text-success"></i>站点设置</a></li>
             <li><a href="{SITE_URL}index.php?admin_totalset/default{$setting['seo_suffix']}" target="main"><i class="fa fa-genderless text-success"></i>全局设置</a></li>

             <li><a href="{SITE_URL}index.php?admin_sitelog/default{$setting['seo_suffix']}" target="main"><i class="fa fa-genderless text-success"></i>站点日志查看</a></li>


                <li><a href="{SITE_URL}index.php?admin_setting/time{$setting['seo_suffix']}" target="main"><i class="fa fa-genderless text-success"></i>时间设置</a> </li>
                <li><a href="{SITE_URL}index.php?admin_setting/clist{$setting['seo_suffix']}" target="main"><i class="fa fa-genderless text-success"></i>首页设置</a> </li>
                <li><a href="{SITE_URL}index.php?admin_setting/search{$setting['seo_suffix']}" target="main"><i class="fa fa-genderless text-success"></i>搜索管理</a></li>
                <li><a href="{SITE_URL}index.php?admin_setting/register{$setting['seo_suffix']}" target="main"><i class="fa fa-genderless text-success"></i>注册设置</a></li>
                <li><a href="{SITE_URL}index.php?admin_nav/default{$setting['seo_suffix']}" target="main"><i class="fa fa-genderless text-success"></i>导航管理</a> </li>
                <li><a href="{SITE_URL}index.php?admin_link/default{$setting['seo_suffix']}" target="main"><i class="fa fa-genderless text-success"></i>友情链接</a> </li>

        </ul>
    </li>

    
     <li class="treeview">
        <a href="#">
            <i class="fa fa-cutlery"></i> <span>认证管理</span>
            <i class="fa fa-angle-left pull-right"></i>
        </a>
        <ul class="treeview-menu" id="operations">

      <li>
      <a href="{SITE_URL}index.php?admin_vertifyuser/default{$setting['seo_suffix']}" target="main"><i class="fa fa-genderless text-success"></i>认证管理列表</a> </li>
   <li>
      <a href="{SITE_URL}index.php?admin_vertifyuser/userlist{$setting['seo_suffix']}" target="main"><i class="fa fa-genderless text-success"></i>已认证列表</a> </li>

        </ul>
    </li>
     <li class="treeview">
        <a href="#">
            <i class="fa fa-cutlery"></i> <span>高级管理</span>
            <i class="fa fa-angle-left pull-right"></i>
        </a>
        <ul class="treeview-menu" id="operations">


                              <li><a href="{SITE_URL}index.php?admin_setting/sms{$setting['seo_suffix']}" target="main"><i class="fa fa-genderless text-success"></i>短信设置</a> </li>
                <li><a href="{SITE_URL}index.php?admin_setting/mail{$setting['seo_suffix']}" target="main"><i class="fa fa-genderless text-success"></i>邮件设置</a> </li>

                <li><a href="{SITE_URL}index.php?admin_setting/settingcredit{$setting['seo_suffix']}" target="main"><i class="fa fa-genderless text-success"></i>财富值设置</a> </li>

                <li><a href="{SITE_URL}index.php?admin_setting/seo{$setting['seo_suffix']}" target="main"><i class="fa fa-genderless text-success"></i>seo设置</a> </li>

                <li><a href="{SITE_URL}index.php?admin_editor/setting{$setting['seo_suffix']}" target="main"><i class="fa fa-genderless text-success"></i>编辑器设置</a> </li>

                <li><a href="{SITE_URL}index.php?admin_setting/qqlogin{$setting['seo_suffix']}" target="main"><i class="fa fa-genderless text-success"></i>qq互联设置</a> </li>
                <li><a href="{SITE_URL}index.php?admin_setting/sinalogin{$setting['seo_suffix']}" target="main"><i class="fa fa-genderless text-success"></i>sina互联设置</a> </li>
        </ul>
    </li>
    <li class="treeview">
        <a href="#">
            <i class="fa fa-comments-o"></i> <span>内容管理</span>
            <i class="fa fa-angle-left pull-right"></i>
        </a>
        <ul class="treeview-menu" id="manage_content">
          <li><a href="{SITE_URL}index.php?admin_topic/topicdatalist{$setting['seo_suffix']}" target="main"><i class="fa fa-genderless text-success"></i>顶置内容管理</a></li>
                  <li><a href="{SITE_URL}index.php?admin_topic/topichotlist{$setting['seo_suffix']}" target="main"><i class="fa fa-genderless text-success"></i>推荐内容管理</a></li>
            <li><a href="{SITE_URL}index.php?admin_question/examine{$setting['seo_suffix']}" target="main"><i class="fa fa-genderless text-success"></i>问答审核</a></li>
                <li><a href="{SITE_URL}index.php?admin_question/default{$setting['seo_suffix']}" target="main"><i class="fa fa-genderless text-success"></i>问题管理</a></li>
                <li><a href="{SITE_URL}index.php?admin_question/searchanswer{$setting['seo_suffix']}" target="main"><i class="fa fa-genderless text-success"></i>回答管理</a></li>
                <li><a href="{SITE_URL}index.php?admin_category/default{$setting['seo_suffix']}" target="main"><i class="fa fa-genderless text-success"></i>分类管理</a></li>
                <li><a href="{SITE_URL}index.php?admin_topic/default{$setting['seo_suffix']}" target="main"><i class="fa fa-genderless text-success"></i>文章管理</a></li>

                <li><a href="{SITE_URL}index.php?admin_tag/default{$setting['seo_suffix']}" target="main"><i class="fa fa-genderless text-success"></i>标签管理</a></li>
                <li><a href="{SITE_URL}index.php?admin_keywords/default{$setting['seo_suffix']}" target="main"><i class="fa fa-genderless text-success"></i>关键词库</a></li>
                <li><a href="{SITE_URL}index.php?admin_word/default{$setting['seo_suffix']}" target="main"><i class="fa fa-genderless text-success"></i>词语过滤</a></li>
                <li><a href="{SITE_URL}index.php?admin_inform/default{$setting['seo_suffix']}" target="main"><i class="fa fa-genderless text-success"></i>举报管理</a></li>
                <li><a href="{SITE_URL}index.php?admin_note/default{$setting['seo_suffix']}" target="main"><i class="fa fa-genderless text-success"></i>公告管理</a></li>

        </ul>
    </li>
        <li class="treeview">
        <a href="#">
            <i class="fa fa-user"></i> <span>用户管理</span>
            <i class="fa fa-angle-left pull-right"></i>
        </a>
        <ul class="treeview-menu" id="manage_user">
            <li><a href="{SITE_URL}index.php?admin_user/add{$setting['seo_suffix']}" target="main"><i class="fa fa-genderless text-success"></i>添加用户</a> </li>
                <li><a href="{SITE_URL}index.php?admin_user/default{$setting['seo_suffix']}" target="main"><i class="fa fa-genderless text-success"></i>用户管理</a> </li>
                <li><a href="{SITE_URL}index.php?admin_banned/add{$setting['seo_suffix']}" target="main"><i class="fa fa-genderless text-success"></i>禁止IP</a> </li>
                <li><a href="{SITE_URL}index.php?admin_expert/default{$setting['seo_suffix']}" target="main"><i class="fa fa-genderless text-success"></i>专家管理</a> </li>
                <li><a href="{SITE_URL}index.php?admin_usergroup/default{$setting['seo_suffix']}" target="main"><i class="fa fa-genderless text-success"></i>用户组</a></li>
                <li><a href="{SITE_URL}index.php?admin_usergroup/system{$setting['seo_suffix']}" target="main"><i class="fa fa-genderless text-success"></i>系统用户组</a></li>
        </ul>
    </li>

  <li class="treeview">
        <a href="#">
            <i class="fa fa-archive"></i> <span>模板管理</span>
            <i class="fa fa-angle-left pull-right"></i>
        </a>
        <ul class="treeview-menu" id="operations">
            <li><a href="{SITE_URL}index.php?admin_template/default/pc{$setting['seo_suffix']}" target="main"><i class="fa fa-genderless text-success"></i>PC模板</a> </li>
                <li><a href="{SITE_URL}index.php?admin_template/default/wap{$setting['seo_suffix']}" target="main"><i class="fa fa-genderless text-success"></i>手机Wap模板</a> </li>
        </ul>
    </li>

 
        <li class="treeview">
        <a href="#">
            <i class="fa fa-recycle"></i> <span>系统工具</span>
            <i class="fa fa-angle-left pull-right"></i>
        </a>
        <ul class="treeview-menu" id="third_part">
           <li><a href="{SITE_URL}index.php?admin_setting/cache{$setting['seo_suffix']}" target="main"><i class="fa fa-genderless text-success"></i>更新缓存</a> </li>
                <li><a href="{SITE_URL}index.php?admin_datacall/default{$setting['seo_suffix']}" target="main"><i class="fa fa-genderless text-success"></i>js数据调用</a> </li>
                <li><a href="{SITE_URL}index.php?admin_main/regulate{$setting['seo_suffix']}" target="main"><i class="fa fa-genderless text-success"></i>数据校正</a> </li>
   
    
                 <li class=""><a href="{SITE_URL}index.php?admin_setting/ucenter{$setting['seo_suffix']}" target="main"><i class="fa fa-genderless text-success"></i>UCenter</a> </li>
                  <li  class="hide"><a href="{SITE_URL}index.php?admin_cms/setting{$setting['seo_suffix']}" target="main"><i class="fa fa-genderless text-success"></i>CMS系统</a> </li>
                   <li><a href="https://tongji.baidu.com/web/welcome/login" target="_blank"><i class="fa fa-genderless text-success"></i> <span>百度统计</span></a></li>
           <li><a href="https://i.umeng.com/?" target="_blank"><i class="fa fa-genderless text-yellow"></i> <span>CNZZ统计</span></a> </li>
    <li><a href="http://zhanzhang.baidu.com/" target="_blank"><i class="fa fa-genderless text-yellow"></i> <span>百度站长平台</span></a></li>
        <li><a href="http://zhanzhang.so.com/" target="_blank"><i class="fa fa-genderless text-yellow"></i> <span>360站长平台</span></a></li>
        </ul>
    </li>

    <li class="treeview">
        <a href="#">
            <i class="fa fa-shopping-cart"></i> <span>礼品商店</span>
            <i class="fa fa-angle-left pull-right"></i>
        </a>
        <ul class="treeview-menu" id="operations">
            <li><a href="{SITE_URL}index.php?admin_gift/default{$setting['seo_suffix']}" target="main"><i class="fa fa-genderless text-success"></i>礼品列表</a></li>
                <li><a href="{SITE_URL}index.php?admin_gift/add{$setting['seo_suffix']}" target="main"><i class="fa fa-genderless text-success"></i>添加礼品</a></li>
                <li><a href="{SITE_URL}index.php?admin_gift/note{$setting['seo_suffix']}" target="main"><i class="fa fa-genderless text-success"></i>礼品公告</a></li>
                <li><a href="{SITE_URL}index.php?admin_gift/addrange{$setting['seo_suffix']}" target="main"><i class="fa fa-genderless text-success"></i>礼品价格区间</a></li>
                <li><a href="{SITE_URL}index.php?admin_gift/log{$setting['seo_suffix']}" target="main"><i class="fa fa-genderless text-success"></i>礼品兑换日志</a></li>
        </ul>
    </li>




    <li class="header">常用菜单</li>
    <li><a href="{SITE_URL}" target="_blank"><i class="fa fa-genderless text-success"></i> <span>网站首页</span></a></li>

           <li><a href="{SITE_URL}index.php?admin_setting/cache{$setting['seo_suffix']}" target="main"><i class="fa fa-genderless text-yellow"></i> <span>更新缓存</span></a> </li>
    <li><a href="http://wenda.whatsns.com/" target="_blank"><i class="fa fa-genderless text-yellow"></i> <span>官方求助</span></a></li>
</ul>
<script>
var url='{$regular}';
url=url.replace('/index','/default');
if(url=='admin_category/view'||url=='admin_category/add'||url=='admin_category/edit'){
	 url='admin_category/default';
}
if(url=='admin_user/edit'){
	 url='admin_user/default';
}
if(url=='admin_usergroup/regular'){
	 url='admin_usergroup/default';
}
if(url=='admin_topic/edit'){
	 url='admin_topic/default';
}
if(url=='admin_note/edit'){
	 url='admin_note/default';
}
if(url=='admin_template/editdirfile'){
	 {eval $lastsubfix=substr($this->uri->segment ( 3 ), -3);}
	 {if $lastsubfix=='wap'}
	 url='admin_template/default/wap';
	 {else}
	 url='admin_template/default/pc';
	 {/if}
}
if(url=='admin_template/default'){

	{eval $ddff=$this->uri->segment ( 3);}
	var tmpname="{$ddff}";

	 {if $this->uri->segment ( 3 )=='pc'||$ddff=='default'}
	 url='admin_template/default/pc';
	 {/if}
		 {if $this->uri->segment ( 3 )=='wap'}
		 url='admin_template/default/wap';
		 {/if}
}
		 if(url=='admin_course/add'){

				{eval $ddff=$this->uri->segment ( 3);}
				var tmpname="{$ddff}";

	
					 {if $this->uri->segment ( 3 )=='new'}
					 url='admin_course/add/new';
					 {/if}
			}
					 if(url=='admin_course/myview'){
		
						
								 url='admin_course/default';
							
						}

   // url=url.replace('/default','');
   var tmp_urls=url.split('/');
   var sublink='';
if(url.indexOf('default')>=0){
	if(tmp_urls[0]!='admin_template'){
		//url=tmp_urls[0]+".html";
	}

}
else{
	sublink=tmp_urls[0];
}
url=url+".html";
$(".treeview-menu li").each(function(){
	 var tmp_a=$(this).find("a").attr("href");

	 if(tmp_a.indexOf(url)>=0){

		 $(this).addClass("current");
		 $(this).find("a").css("color","#ffffff");
		 $(this).parent().parent().addClass("active");

	 }




});

if(url.indexOf("admin_emailpush")>=0){
	 $("#manage_emailpush li:first-child").addClass("current");
	 $("#manage_emailpush li:first-child").find("a").css("color","#ffffff");
	 $("#manage_emailpush li:first-child").parent().parent().addClass("active");
}
if(url.indexOf("admin_xiongzhang")>=0){
	 $("#manage_plugin li.xiongzhang").addClass("current");
	 $("#manage_plugin li.xiongzhang").find("a").css("color","#ffffff");
	 $("#manage_plugin li.xiongzhang").parent().parent().addClass("active");
}
if(url.indexOf("admin_course")>=0){

	 $("#manage_course ").addClass("active");
}


</script>
