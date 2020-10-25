{include file="public/toper" /}
<div class="layui-tab layui-tab-brief main-tab-container">
    <ul class="layui-tab-title main-tab-title">
      <li class="layui-this">修改文章</li>
      <div class="main-tab-item">文章管理</div>
    </ul> 
    <div class="layui-tab-content"> 
       <form class="layui-form">
        <div class="layui-tab-item layui-show">
          <input type="hidden" name="id" value="<?php echo $article['id'] ?>">
          <?php echo Form::select_no_option('category_id',$article['category_id'],'所属栏目','',$model_category_select_option,'required');?>
          <?php echo Form::input('text','title',$article['title'],'标题','','请输入标题','required');?>
          <?php echo Form::file('image_url',$article['image_url'],'图片','','','images','','图片','image');?>
          <?php echo Form::radio('is_recommend',$article['is_recommend'],'是否推荐','用于前台推荐调用',array(1=>'是',0=>'否'));?>
          <?php echo Form::radio('is_top',$article['is_top'],'是否置顶','用于前台置顶调用',array(1=>'是',0=>'否'));?>
          <?php echo Form::input('text','keywords',$article['keywords'],'关键词','关键词以英文逗号隔开');?> 
          <?php echo Form::textarea('description',$article['description'],'摘要','留空时默认截取内容的前250个字符','请输入摘要');?>
          <?php if($settings['editor'] && ($settings['editor']=='umeditor')){
                echo Form::umeditor('content',$article['content'],'内容');
          }else{
                echo Form::layedit('content',$article['content'],'内容','','请输入内容','layedit','content');
          } ?>
          <?php echo Form::date('create_time',$article['create_time'],'添加时间','');?>
          <?php echo Form::input('text','hits',$article['hits'],'点击量','','请输入点击量，默认是0','number');?>
          <?php echo Form::input('text','url',$article['url'],'链接地址');?>
          <div class="layui-form-item">
            <div class="layui-input-block">
              <button class="layui-btn" lay-submit="" lay-filter="article_edit">立即提交</button>
            </div>
          </div>
        </div>   
      </form>
    </div>
</div>
<script type="text/javascript">
layui.use(['element', 'form', 'upload', 'layedit', 'laydate'], function(){
  var element = layui.element()
  ,form = layui.form()
  ,layedit = layui.layedit
  ,laydate = layui.laydate
  ,jq = layui.jquery;

  //创建一个编辑器
  var content = layedit.build('content',{
    uploadImage: { url: '<?php echo url("upload/layedit_upimage") ?>' ,type: 'post'  }
    ,height: 400
  });
  //表单验证
  form.verify({
    //编辑器数据同步
    layedit: function(value){
      layedit.sync(content);
    }
  });
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
  form.on('submit(article_edit)', function(data){
    loading = layer.load(2, {
      shade: [0.2,'#000'] //0.2透明度的白色背景
    });
    var param = data.field;
    jq.post('{:url("article/edit")}',param,function(data){
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

  //获取标题分词
  jq('input[name=title]').blur(function(){
    if(!jq('input[name=keywords]').val()){
      var title = jq(this).val();
      var param = {'source':title}
      jq.get('{:url("setting/get_keywords")}',param,function(data){
        if(data.code == 200){
          jq('input[name=keywords]').val(data.keywords);
        }
      });
    }
  });
  
  
})
</script>

{include file="public/footer" /}