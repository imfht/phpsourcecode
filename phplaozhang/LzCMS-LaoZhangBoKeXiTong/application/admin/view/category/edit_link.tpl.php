{include file="public/toper" /}
<div class="layui-tab-brief main-tab-container">
    <ul class="layui-tab-title main-tab-title">
      <a href="<?php echo url('category/index') ?>"><li>栏目列表</li></a>
      <li class="layui-this">修改外部链接</li>
      <div class="main-tab-item">栏目管理</div>
    </ul>
    <div class="layui-tab-content">
      <div class="layui-tab-item layui-show">
        <form class="layui-form">
          <div class="layui-tab layui-tab-card">
            <ul class="layui-tab-title">
              <li class="layui-this">基本选项</li>
            </ul>
            <div class="layui-tab-content">
              <div class="layui-tab-item layui-show">
                <input type="hidden" name="id" value="<?php echo $category['id'] ?>">
                <input type="hidden" name="model_id" value="<?php echo $category['model_id'] ?>">
                <?php echo Form::select('parent_id',$category['parent_id'],'上级栏目','',$category_select);?>
                <?php echo Form::input('text','name',$category['name'],'栏目名称','','请输入栏目名称','required');?>
                <?php echo Form::input('text','url',$category['url'],'链接地址','','http://开始','url');?>
                <?php echo Form::file('image_url',$category['image_url'],'栏目图片','','','images','','图片','image');?>
                <?php echo Form::textarea('description',$category['description'],'栏目描述','','请输入栏目描述');?>
                <?php echo Form::input('text','sort',$category['sort'],'排序','','数字越小越靠前','number');?>
                <?php echo Form::radio('is_menu',$category['is_menu'],'是否导航显示','',array(1=>'是',0=>'否'));?>
                
              </div>
              <div class="layui-form-item">
                <div class="layui-input-block">
                  <button class="layui-btn" lay-submit="" lay-filter="cate_edit">立即提交</button>
                </div>
              </div>
              
            </div>
          </div>
          
        </form>
      </div>
    </div>
</div>
<script type="text/javascript">
layui.use(['element', 'form', 'upload'], function(){
  var element = layui.element()
  ,form = layui.form()
  ,jq = layui.jquery;
  //图片上传
  layui.upload({
    url: '<?php echo url("upload/upimage") ?>'
    ,elem:'#image'
    ,before: function(input){
      loading = layer.load(2, {
        shade: [0.2,'#000'] //0.2透明度的白色背景
      });
    }
    ,success: function(res){
      layer.close(loading);
      jq('input[name=image_url]').val(res.path);
      layer.msg(res.msg, {icon: 1, time: 1000});
    }
  }); 
  jq('input[name=image_url]').hover(function(){
    jq(this).after('<img class="input-img-show" src="'+jq(this).val()+'" >');
  },function(){
    jq(this).next('img').remove();
  });

  //监听提交
  form.on('submit(cate_edit)', function(data){
    loading = layer.load(2, {
      shade: [0.2,'#000'] //0.2透明度的白色背景
    });
    var param = data.field;
    jq.post('{:url("category/edit")}',param,function(data){
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
  
})
</script>
</body>
</html>