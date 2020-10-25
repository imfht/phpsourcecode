{include file="public/toper" /}
<div class="layui-layout layui-layout-admin">
    <div class="layui-header header">
      <div class="layui-main">
        <a class="logo" href="<?php echo url('index/index/index') ?>" target="_blank">
          <img src="/static/images/logo-top.png" alt="LzCMS">
        </a>
        <ul class="layui-nav top-nav-container">
          <li class="layui-nav-item layui-this">
            <a href="javascript:void(0)">首页</a>
          </li>
          <li class="layui-nav-item">
            <a href="javascript:void(0)">内容</a>
          </li>
          <li class="layui-nav-item">
            <a href="javascript:void(0)">会员</a>
          </li>
          <li class="layui-nav-item">
            <a href="javascript:void(0)">设置</a>
          </li>
        </ul>
        <div class="top_admin_user">
          <a href="/" target="_blank">网站首页</a> |<a class="update_cache" href="javascript:void(0)">更新缓存</a> | <a class="logout_btn" href="javascript:void(0)">退出</a>
        </div>
      </div>
    </div>
    <div class="layui-side layui-bg-black">
        <div class="layui-side-scroll">
            <ul class="layui-nav layui-nav-tree left_menu_ul">
              <li class="layui-nav-item layui-nav-title">
                <a href="<?php echo url('index/home') ?>" target="main">首页</a>
              </li>
              <li class="layui-nav-item first-item layui-this">
                <a href="<?php echo url('index/home') ?>" target="main">
                  <i class="layui-icon">&#xe638;</i>
                  <cite>首页面板</cite>
                </a>
              </li>
              <li class="layui-nav-item ">
                <a href="<?php echo url('admin/edit') ?>" target="main">
                  <i class="layui-icon">&#xe642;</i>
                  <cite>管理员信息</cite>
                </a>
              </li>
            </ul>
            <ul class="layui-nav layui-nav-tree left_menu_ul content_put_manage hide">
              <li class="layui-nav-item layui-nav-title">
                <a href="<?php echo url('category/index') ?>" target="main">内容发布管理</a>
              </li>
              <li class="layui-nav-item first-item">
                <a href="<?php echo url('category/index') ?>" target="main">
                  <i class="layui-icon">&#xe609;</i>
                  <cite>栏目管理</cite>
                </a>
              </li>
              <li class="layui-nav-item content_manage">
                <a href="<?php echo url('category/content_manage_search') ?>" target="main">
                  <i class="layui-icon">&#xe60a;</i>
                  <cite>内容管理</cite>
                </a>
              </li>
              <li class="layui-nav-item">
                <a href="<?php echo url('feedback/index') ?>" target="main">
                  <i class="layui-icon">&#xe63a;</i>
                  <cite>留言管理</cite>
                </a>
              </li>              
            </ul>
            <ul class="layui-nav layui-nav-tree left_menu_ul hide">
              <li class="layui-nav-item layui-nav-title">
                <a href="<?php echo url('member/index') ?>" target="main">会员管理</a>
              </li>
              <li class="layui-nav-item first-item">
                <a href="<?php echo url('member/index') ?>" target="main">
                  <i class="layui-icon">&#xe613;</i>
                  <cite>会员列表</cite>
                </a>
              </li>
            </ul>
            <ul class="layui-nav layui-nav-tree left_menu_ul setting_ul hide">
              <li class="layui-nav-item layui-nav-title">
                <a href="<?php echo url('setting/base','tab=1') ?>" target="main">相关设置</a>
              </li>
              <li class="layui-nav-item first-item ">
                <a href="<?php echo url('setting/base','tab=1') ?>" target="main">
                  <i class="layui-icon">&#xe620;</i>
                  <cite>站点设置</cite>
                </a>
              </li>
              <li class="layui-nav-item">
                <a href="<?php echo url('setting/base','tab=2') ?>" target="main">
                  <i class="layui-icon">&#xe612;</i>
                  <cite>站长信息</cite>
                </a>
              </li>
              <li class="layui-nav-item">
                <a href="<?php echo url('setting/base','tab=3') ?>" target="main">
                  <i class="layui-icon"></i>
                  <cite>SEO设置</cite>
                </a>
              </li>
              <li class="layui-nav-item">
                <a href="<?php echo url('setting/base','tab=4') ?>" target="main">
                  <i class="layui-icon">&#xe634;</i>
                  <cite>水印设置</cite>
                </a>
              </li>
              <li class="layui-nav-item">
                <a href="<?php echo url('setting/base','tab=5') ?>" target="main">
                  <i class="layui-icon">&#xe63a;</i>
                  <cite>畅言评论设置</cite>
                </a>
              </li>
              <li class="layui-nav-item">
                <a href="<?php echo url('setting/base','tab=6') ?>" target="main">
                  <i class="layui-icon">&#xe611;</i>
                  <cite>QQ登陆设置</cite>
                </a>
              </li>
              <li class="layui-nav-item">
                <a href="<?php echo url('setting/base','tab=10') ?>" target="main">
                  <i class="layui-icon">&#xe631;</i>
                  <cite>其他设置</cite>
                </a>
              </li>
              <li class="layui-nav-item">
                <a href="<?php echo url('setting/links') ?>" target="main">
                  <i class="layui-icon">&#xe62c;</i>
                  <cite>友情连接</cite>
                </a>
              </li>
              <li class="layui-nav-item">
                <a href="<?php echo url('setting/sitemap') ?>" target="main">
                  <i class="layui-icon">&#xe60d;</i>
                  <cite>Sitemap</cite>
                </a>
              </li>
              
            </ul>
    			<div class="content_manage_container left_menu_ul hide">
    				<div class="content_manage_title">内容管理</div>
        		<div id="content_manage_tree"></div>
        	</div>
        </div>
    </div>

    <div class="layui-body iframe-container">
        <div class="iframe-mask" id="iframe-mask"></div>
        <iframe class="admin-iframe" id="admin-iframe" name="main" src="<?php echo url('home') ?>"></iframe>
    </div>
    
    <div class="layui-footer footer">
      <div class="layui-main">
        <p>2017 © <a target="_blank" href="http://www.lzcms.top">LzCMS</a></p>
      </div>
    </div>
</div>


<script type="text/javascript">
layui.use(['layer', 'element','jquery','tree'], function(){
  var layer = layui.layer
  ,element = layui.element() //导航的hover效果、二级菜单等功能，需要依赖element模块
  ,jq = layui.jquery

  //头部菜单切换
  jq('.top-nav-container .layui-nav-item').click(function(){
    var menu_index = jq(this).index('.top-nav-container .layui-nav-item');
    jq('.top-nav-container .layui-nav-item').removeClass('layui-this');
    jq(this).addClass('layui-this');
    jq('.left_menu_ul').addClass('hide');
    jq('.left_menu_ul:eq('+menu_index+')').removeClass('hide');
    jq('.left_menu_ul .layui-nav-item').removeClass('layui-this');
    jq('.left_menu_ul:eq('+menu_index+')').find('.first-item').addClass('layui-this');
    var url = jq('.left_menu_ul:eq('+menu_index+')').find('.first-item a').attr('href');
    jq('.admin-iframe').attr('src',url);
    //出现遮罩层
    jq("#iframe-mask").show();
    //遮罩层消失
    jq("#admin-iframe").load(function(){   
      jq("#iframe-mask").fadeOut(100);
    });
  });
  //左边菜单点击
  jq('.left_menu_ul .layui-nav-item').click(function(){
    jq('.left_menu_ul .layui-nav-item').removeClass('layui-this');
    jq(this).addClass('layui-this');
    //出现遮罩层
    jq("#iframe-mask").show();
    //遮罩层消失
    jq("#admin-iframe").load(function(){   
      jq("#iframe-mask").fadeOut(100);
    });
  });
   
  //点击回到内容页面
  jq('.content_manage_title').click(function(){
  	jq('.left_menu_ul .layui-nav-item').removeClass('layui-this');
  	jq(this).parent().addClass('hide');
  	jq('.content_put_manage').find('.first-item').addClass('layui-this');
  	var url = jq('.content_put_manage').find('.first-item a').attr('href');
    jq('.admin-iframe').attr('src',url);
  	jq('.content_put_manage').removeClass('hide');

  });
  //内容管理树
  jq('.content_manage').click(function(){
    loading = layer.load(2, {
      shade: [0.2,'#000'] //0.2透明度的白色背景
    });
    jq.getJSON('{:url("category/manage_tree")}',function(data){
      jq('#content_manage_tree').empty();
      layui.tree({
        elem: '#content_manage_tree' //传入元素选择器
        ,skin: 'white'
        ,target: 'main'
        ,nodes: data
      });
      jq('.left_menu_ul').addClass('hide');
      jq('.content_manage_container').removeClass('hide');
      layer.close(loading);
    });
  });

  //更新缓存
  jq('.update_cache').click(function(){
    loading = layer.load(2, {
      shade: [0.2,'#000'] //0.2透明度的白色背景
    });
    jq.getJSON('{:url("cache/update")}',function(data){
      if(data.code == 200){
        layer.close(loading);
        layer.msg(data.msg, {icon: 1, time: 1000}, function(){
          location.reload();//do something
        });
      }else{
        layer.close(loading);
        layer.msg(data.msg, {icon: 2, anim: 6, time: 1000});
      }
    });
  });

  //退出登陆
  jq('.logout_btn').click(function(){
    loading = layer.load(2, {
      shade: [0.2,'#000'] //0.2透明度的白色背景
    });
    jq.getJSON('{:url("login/logout")}',function(data){
      if(data.code == 200){
        layer.close(loading);
        layer.msg(data.msg, {icon: 1, time: 1000}, function(){
          location.reload();//do something
        });
      }else{
        layer.close(loading);
        layer.msg(data.msg, {icon: 2, anim: 6, time: 1000});
      }
    });
  });

});
</script> 
</body>
</html>