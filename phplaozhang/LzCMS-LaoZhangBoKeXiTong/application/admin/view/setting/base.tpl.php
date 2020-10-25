{include file="public/toper" /}
<form class="layui-form">
<div class="layui-tab layui-tab-brief main-tab-container" lay-filter="main-tab">
    <ul class="layui-tab-title main-tab-title">
      <li class="<?php if(input('param.tab')==1) echo 'layui-this'; ?>">站点设置</li>
      <li class="<?php if(input('param.tab')==2) echo 'layui-this'; ?>">站长信息</li>
      <li class="<?php if(input('param.tab')==3) echo 'layui-this'; ?>">SEO设置</li>
      <li class="<?php if(input('param.tab')==4) echo 'layui-this'; ?>">水印设置</li>
      <li class="<?php if(input('param.tab')==5) echo 'layui-this'; ?>">畅言评论设置</li>
      <li class="<?php if(input('param.tab')==6) echo 'layui-this'; ?>">QQ登陆设置</li>
      <li class="<?php if(input('param.tab')==10) echo 'layui-this'; ?>">其他设置</li>
      <div class="main-tab-item">相关设置</div>
    </ul>    
    <div class="layui-tab-content">
      <div class="layui-tab-item <?php if(input('param.tab')==1) echo 'layui-show'; ?>">  
        <?php echo Form::input('text','site_name',$setting['site_name'],'站点名称','站点名称，将显示在浏览器窗口标题等位置','请输入站点名称');?>    
        <?php echo Form::file('logo',$setting['logo'],'网站logo','网站logo，将显示在网站前台','','images','','图片','logo');?>
        <?php echo Form::file('qr_code',$setting['qr_code'],'二维码','公众号二维码，将显示在网站前台','','images','','图片','qr_code');?>
        <?php echo Form::input('text','site_url',$setting['site_url'],'网站根网址','网站 URL，请以http://开头','请输入网站根网址');?>
        <?php echo Form::input('text','icp',$setting['icp'],'网站备案号','在此输入网站ICP备案号，它将显示在页面底部','请输入网站备案号');?>
    		<?php echo Form::textarea('copy',$setting['copy'],'网站版权信息','在此输入网站版权信息，它将显示在页面底部','请输入网站版权信息');?>
        <?php echo Form::textarea('site_statistice',$setting['site_statistice'],'第三方统计代码','页面底部可以显示第三方统计','请输入第三方统计代码');?>
        <?php echo Form::textarea('head_html',$setting['head_html'],'网站头部代码','网站头部代码将出现在<head></head>之间','');?>
        <?php echo Form::checkbox('search_model',$setting['search_model'],'前台检索模型','选中的模型在前台搜索会出现该模型的内容',$model_select);?>
        <?php echo Form::input('text','threshold',$setting['threshold'],'分词概率','从0到1，文章标题分词的概率','请输入分词概率');?>
        <?php echo Form::radio('editor',$setting['editor']?$setting['editor']:'umeditor','后台编辑器','后台内容编辑所用的富文本编辑器',array('umeditor'=>'umeditor','layedit'=>'layedit'));?>
        <?php echo Form::radio('is_excel',$setting['is_excel'],'excel导入文章','开启之后可以excel导入文章',array(1=>'开启',0=>'关闭'));?>
        <?php echo Form::radio('guest_feedback',$setting['guest_feedback'],'游客留言','关闭之后的不能游客留言，必须登陆之后留言',array(1=>'开启',0=>'关闭'));?>
        <?php echo Form::radio('site_status',$setting['site_status'],'网站是否开启','暂时将站点关闭，其他人无法访问，但不影响管理员访问',array(1=>'开启',0=>'关闭'),'site_status');?>
        <?php echo Form::textarea('site_closedreason',$setting['site_closedreason'],'站点关闭原因','请填写站点关闭原因，将在前台显示','请填写站点关闭原因，将在前台显示');?>
        
      </div>
      <div class="layui-tab-item <?php if(input('param.tab')==2) echo 'layui-show'; ?>">
        <?php echo Form::input('text','stationmaster_name',$setting['stationmaster_name'],'站长姓名','','请输入站长姓名');?>
        <?php echo Form::input('text','stationmaster_occupation',$setting['stationmaster_occupation'],'站长职业','','请输入站长职业');?>
        <?php echo Form::input('text','stationmaster_motto',$setting['stationmaster_motto'],'站长座右铭','','请输入站长座右铭');?>
        <?php echo Form::input('text','stationmaster_qq',$setting['stationmaster_qq'],'站长QQ','','请输入站长QQ');?>
        <?php echo Form::input('text','stationmaster_qqnet',$setting['stationmaster_qqnet'],'交流群号码','','请输入交流群号码');?> 
        <?php echo Form::textarea('stationmaster_qqnet_code',$setting['stationmaster_qqnet_code'],'交流群加群代码','从http://qun.qq.com/join.html的加群组件获取网页加群代码','请输入交流群加群代码');?>
      </div>
      <div class="layui-tab-item <?php if(input('param.tab')==3) echo 'layui-show'; ?>">
        <?php echo Form::input('text','title_add',$setting['title_add'],'标题附加字','网页标题通常是搜索引擎关注的重点，本附加字设置出现在标题中商城名称后，如有多个关键字，建议用分隔符分隔','请输入标题附加字');?>
        <?php echo Form::textarea('keywords',$setting['keywords'],'网站关键词','Keywords项出现在页面头部的标签中，用于记录本页面的关键字，多个关键字请用分隔符分隔','请输入网站关键词');?>
        <?php echo Form::textarea('description',$setting['description'],'关键词描述','Description出现在页面头部的Meta标签中，用于记录本页面的摘要与描述，建议不超过80个字','请输入网站关键词描述');?>
      </div>
      <div class="layui-tab-item <?php if(input('param.tab')==4) echo 'layui-show'; ?>">
        <?php echo Form::radio('is_watermark',$setting['is_watermark'],'是否启用水印','只有图片高度大于500px并且高度大于300px才会添加水印',array(1=>'开启',0=>'关闭'));?>
        <?php echo Form::input('text','watermark_width',$setting['watermark_width'],'被水印图片宽度','输入数字，被添加水印的图片大于此宽度才会被添加水印','请输入被水印图片宽度');?>
        <?php echo Form::input('text','watermark_height',$setting['watermark_height'],'被水印图片高度','输入数字，被添加水印的图片大于此高度才会被添加水印','请输入被水印图片高度');?>
        <?php echo Form::file('watermark',$setting['watermark'],'水印图片','水印图片，网站上传的图片会加上此水印','','images','','图片','watermark');?>
        <?php echo Form::radio('watermark_locate',$setting['watermark_locate'],'水印位置','水印图片在图片中显示的位置',array(1=>'左上角',2=>'上居中',3=>'右上角',4=>'左居中',5=>'全居中',6=>'右居中',7=>'左下角',8=>'下居中',9=>'右下角'));?>
        <?php echo Form::input('text','watermark_alpha',$setting['watermark_alpha'],'水印透明度','输入1~100的数字，0为完全透明，100为完全不透明','请输入水印透明度');?>
      </div>
      <div class="layui-tab-item <?php if(input('param.tab')==5) echo 'layui-show'; ?>">
        <?php echo Form::input('text','changyan_app_id',$setting['changyan_app_id'],'APP ID');?>
        <?php echo Form::input('text','changyan_app_key',$setting['changyan_app_key'],'APP Key');?>
      </div>
      <div class="layui-tab-item <?php if(input('param.tab')==6) echo 'layui-show'; ?>">
        <?php echo Form::input('text','qq_app_id',$setting['qq_app_id'],'APP ID');?>
        <?php echo Form::input('text','qq_app_key',$setting['qq_app_key'],'APP Key','网站回调域 : http://域名/index/member/other_login.html');?>
      </div>
      <div class="layui-tab-item <?php if(input('param.tab')==10) echo 'layui-show'; ?>">
        <?php echo Form::file('index_banner',$setting['index_banner'],'首页banner图','首页banner图，将显示在网站首页,为空则不显示','','images','','图片','index_banner');?>
        <?php echo Form::input('text','index_banner_bg',$setting['index_banner_bg'],'banner背景色','请输入十六进制颜色值如：#393D49');?>
        <?php echo Form::file('lzcms_banner',$setting['lzcms_banner'],'首页通栏广告','首页通栏广告，将显示在网站首页,为空则不显示','','images','','图片','lzcms_banner');?>
        <?php echo Form::input('text','lzcms_banner_link',$setting['lzcms_banner_link'],'通栏广告链接','通栏广告链接，请以http://开头');?>
        <?php echo Form::textarea('site_idea1',$setting['site_idea1'],'网站理念1','显示在网站首页','请输入网站理念1');?>
        <?php echo Form::textarea('site_idea2',$setting['site_idea2'],'网站理念2','显示在网站首页','请输入网站理念2');?>
        <?php echo Form::textarea('site_idea3',$setting['site_idea3'],'网站理念3','显示在网站首页','请输入网站理念3');?>
      </div>
      <div class="layui-form-item">
        <div class="layui-input-block">
          <button class="layui-btn" lay-submit="" lay-filter="site_base">立即提交</button>
        </div>
      </div>
    </div>
</div>
</form>
<script>
layui.use(['form', 'element', 'upload'], function(){
  var element = layui.element() //Tab的切换功能，切换事件监听等，需要依赖element模块
  ,form = layui.form()
  ,jq = layui.jquery;
  //监听radio
  form.on('radio(site_status)', function(data){
    if(data.value=='1'){
      jq('textarea[name=site_closedreason]').parents('.layui-form-item').hide();
    }else{
      jq('textarea[name=site_closedreason]').parents('.layui-form-item').show();
    }
  }); 
  if(jq('input[name=site_status]:checked').val()=='1'){
    jq('textarea[name=site_closedreason]').parents('.layui-form-item').hide();
  }
  //logo
  layui.upload({
    url: '<?php echo url("upload/upimage") ?>'
    ,elem:'#logo'
    ,before: function(input){
      loading = layer.load(2, {
        shade: [0.2,'#000'] //0.2透明度的白色背景
      });
    }
    ,success: function(res){
      layer.close(loading);
      jq('input[name=logo]').val(res.path);
      layer.msg(res.msg, {icon: 1});
    }
  }); 
  jq('input[name=logo]').hover(function(){
    jq(this).after('<img class="input-img-show" src="'+jq(this).val()+'" >');
  },function(){
    jq(this).next('img').remove();
  });
  //二维码
  layui.upload({
    url: '<?php echo url("upload/upimage") ?>'
    ,elem:'#qr_code'
    ,before: function(input){
      loading = layer.load(2, {
        shade: [0.2,'#000'] //0.2透明度的白色背景
      });
    }
    ,success: function(res){
      layer.close(loading);
      jq('input[name=qr_code]').val(res.path);
      layer.msg(res.msg, {icon: 1});
    }
  }); 
  jq('input[name=qr_code]').hover(function(){
    jq(this).after('<img class="input-img-show" src="'+jq(this).val()+'" >');
  },function(){
    jq(this).next('img').remove();
  });
  //水印
  layui.upload({
    url: '<?php echo url("upload/upimage") ?>'
    ,elem:'#watermark'
    ,before: function(input){
      loading = layer.load(2, {
        shade: [0.2,'#000'] //0.2透明度的白色背景
      });
    }
    ,success: function(res){
      layer.close(loading);
      jq('input[name=watermark]').val(res.path);
      layer.msg(res.msg, {icon: 1});
    }
  }); 
  jq('input[name=watermark]').hover(function(){
    jq(this).after('<img class="input-img-show" src="'+jq(this).val()+'" >');
  },function(){
    jq(this).next('img').remove();
  });
  //首页banner
  layui.upload({
    url: '<?php echo url("upload/upimage") ?>'
    ,elem:'#index_banner'
    ,before: function(input){
      loading = layer.load(2, {
        shade: [0.2,'#000'] //0.2透明度的白色背景
      });
    }
    ,success: function(res){
      layer.close(loading);
      jq('input[name=index_banner]').val(res.path);
      layer.msg(res.msg, {icon: 1});
    }
  }); 
  jq('input[name=index_banner]').hover(function(){
    jq(this).after('<img class="input-img-show" src="'+jq(this).val()+'" >');
  },function(){
    jq(this).next('img').remove();
  });
  //首页通栏广告
  layui.upload({
    url: '<?php echo url("upload/upimage") ?>'
    ,elem:'#lzcms_banner'
    ,before: function(input){
      loading = layer.load(2, {
        shade: [0.2,'#000'] //0.2透明度的白色背景
      });
    }
    ,success: function(res){
      layer.close(loading);
      jq('input[name=lzcms_banner]').val(res.path);
      layer.msg(res.msg, {icon: 1});
    }
  }); 
  jq('input[name=lzcms_banner]').hover(function(){
    jq(this).after('<img class="input-img-show" src="'+jq(this).val()+'" >');
  },function(){
    jq(this).next('img').remove();
  });
  //监听提交
  form.on('submit(site_base)', function(data){
    loading = layer.load(2, {
      shade: [0.2,'#000'] //0.2透明度的白色背景
    });
    var param = data.field;
    jq.post('{:url("setting/base")}',param,function(data){
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
    return false;
  });
  
  //选项卡切换监听，改变iframe外层导航选项
  element.on('tab(main-tab)', function(data){
  	console.log(data.index); //得到当前Tab的所在下标
  	var index = 1 + data.index;
  	jq('.setting_ul .layui-nav-item', window.parent.document).removeClass('layui-this');
  	jq('.setting_ul .layui-nav-item:eq('+index+')', window.parent.document).addClass('layui-this');
  });

});
</script>
</body>
</html>