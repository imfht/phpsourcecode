{include file="public/toper" /}
<div class="layui-tab layui-tab-brief main-tab-container" lay-filter="main-tab">
    <ul class="layui-tab-title main-tab-title">
      <a href="<?php echo url('setting/sitemap') ?>"><li class="layui-this">Sitemap</li></a>
      <div class="main-tab-item">相关设置</div>
    </ul>    
    <div class="layui-tab-content">
      <div class="layui-tab-item layui-show">
        <form class="layui-form"> 
          <?php echo Form::checkbox('sitemap_model',$setting['sitemap_model'],'选择模型','',$model_select);?>
          <?php echo Form::select('changefreq',$setting['changefreq'],'更新频率','',$changefreq_select,'required');?>
          <div class="layui-form-item">
            <div class="layui-input-block">
              <button class="layui-btn" lay-submit="" lay-filter="sitemap">立即生成</button>
            </div>
          </div>
          <div class="fill_100"></div><div class="fill_100"></div>
        </form>
      </div>
    </div>
</div>
<script>
layui.use(['form', 'element'], function(){
  var element = layui.element() //Tab的切换功能，切换事件监听等，需要依赖element模块
  ,form = layui.form()
  ,jq = layui.jquery;

  //监听提交
  form.on('submit(sitemap)', function(data){
    loading = layer.load(2, {
      shade: [0.2,'#000'] //0.2透明度的白色背景
    });
    var param = data.field;
    jq.post('{:url("setting/sitemap")}',param,function(data){
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

});
</script>
</body>
</html>