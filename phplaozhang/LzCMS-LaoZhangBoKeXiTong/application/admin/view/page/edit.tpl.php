{include file="public/toper" /}
<div class="layui-tab-brief main-tab-container">
    <ul class="layui-tab-title main-tab-title">
      <div class="main-tab-item">编辑单页</div>
    </ul>
    <div class="layui-tab-content">
      <div class="layui-tab-item layui-show">
        <form class="layui-form">
      		  <input type="hidden" name="id" value="<?php echo $page['id'] ?>">
      		  <input type="hidden" name="category_id" value="<?php echo $category_id ?>">
            <?php echo Form::input('text','title',$page['title']?$page['title']:$categorys[$category_id]['name'],'标题','','请输入标题','required');?>
            <?php echo Form::file('image_url',$page['image_url'],'图片','','点击上传或者填写图片地址','images','','图片','image');?>
            <?php echo Form::textarea('description',$page['description'],'摘要','','请输入摘要');?>
            <?php if($settings['editor'] && ($settings['editor']=='umeditor')){
                  echo Form::umeditor('content',$page['content'],'内容');
            }else{
                  echo Form::layedit('content',$page['content'],'内容','','请输入内容','layedit','content');
            } ?>
            <div class="layui-form-item">
              <div class="layui-input-block">
                <button class="layui-btn" lay-submit="" lay-filter="page_edit">立即提交</button>
              </div>
            </div>
        </form>
      </div>
    </div>
</div>
<script type="text/javascript">
layui.use(['element', 'form', 'upload', 'layedit'], function(){
  var element = layui.element()
  ,form = layui.form()
  ,layedit = layui.layedit
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
  form.on('submit(page_edit)', function(data){
    loading = layer.load(2, {
      shade: [0.2,'#000'] //0.2透明度的白色背景
    });
    var param = data.field;
    jq.post('{:url("page/edit")}',param,function(data){
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