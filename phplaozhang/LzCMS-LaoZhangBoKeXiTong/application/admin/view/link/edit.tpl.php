{include file="public/toper" /}
<div class="layui-tab layui-tab-brief main-tab-container">
    <ul class="layui-tab-title main-tab-title">
      <li class="layui-this">修改链接</li>
      <div class="main-tab-item">链接管理</div>
    </ul>
    <div class="layui-tab-content">
       <form class="layui-form">
        <div class="layui-tab-item layui-show">
          <input type="hidden" name="id" value="<?php echo $link['id'] ?>">
          <?php echo Form::select_no_option('category_id',$link['category_id'],'所属栏目','',$model_category_select_option,'required');?>
          <?php echo Form::input('text','title',$link['title'],'标题','','请输入标题','required');?>
          <?php echo Form::file('image_url',$link['image_url'],'图片','','','images','','图片','image');?>
          <?php echo Form::input('text','url',$link['url'],'链接地址','','http://开始');?>
          <?php echo Form::textarea('description',$link['description'],'链接描述','','请输入链接描述');?>
          <?php echo Form::radio('is_recommend',$link['is_recommend'],'是否推荐','用于前台推荐调用',array(1=>'是',0=>'否'));?>
          <?php echo Form::radio('is_top',$link['is_top'],'是否置顶','用于前台置顶调用',array(1=>'是',0=>'否'));?>
          <?php echo Form::date('create_time',$link['create_time'],'添加时间','');?>
          <div class="layui-form-item">
            <div class="layui-input-block">
              <button class="layui-btn" lay-submit="" lay-filter="link_edit">立即提交</button>
            </div>
          </div>
        </div>   
      </form>
    </div>
</div>
<script type="text/javascript">
layui.use(['element', 'form', 'upload', 'laydate'], function(){
  var element = layui.element()
  ,form = layui.form()
  ,laydate = layui.laydate
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
  //图片预览
  jq('input[name=image_url]').hover(function(){
    jq(this).after('<img class="input-img-show" src="'+jq(this).val()+'" >');
  },function(){
    jq(this).next('img').remove();
  });
  
  //监听提交
  form.on('submit(link_edit)', function(data){
    loading = layer.load(2, {
      shade: [0.2,'#000'] //0.2透明度的白色背景
    });
    var param = data.field;
    jq.post('{:url("link/edit")}',param,function(data){
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

{include file="public/footer" /}