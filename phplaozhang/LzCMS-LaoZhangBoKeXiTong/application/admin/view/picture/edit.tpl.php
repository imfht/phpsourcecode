{include file="public/toper" /}
<div class="layui-tab layui-tab-brief main-tab-container">
    <ul class="layui-tab-title main-tab-title">
      <li class="layui-this">修改图片</li>
      <div class="main-tab-item">图片管理</div>
    </ul>
    <div class="layui-tab-content">
       <form class="layui-form">
        <div class="layui-tab-item layui-show">
          <input type="hidden" name="id" value="<?php echo $picture['id'] ?>">
          <?php echo Form::select_no_option('category_id',$picture['category_id'],'所属栏目','',$model_category_select_option,'required');?>
          <?php echo Form::input('text','title',$picture['title'],'标题','','请输入标题','required');?>
          <?php echo Form::radio('is_recommend',$picture['is_recommend'],'是否推荐','用于前台推荐调用',array(1=>'是',0=>'否'));?>
          <?php echo Form::radio('is_top',$picture['is_top'],'是否置顶','用于前台置顶调用',array(1=>'是',0=>'否'));?>
          <?php echo Form::textarea('description',$picture['description'],'图集描述');?>
          <?php echo Form::images('images',json_decode($picture['images'],true),'图集上传','默认第一张为主图');?>
          <?php if($settings['editor'] && ($settings['editor']=='umeditor')){
                echo Form::umeditor('content',$picture['content'],'内容');
          }else{
                echo Form::layedit('content',$picture['content'],'内容','','请输入内容','layedit','content');
          } ?>
          <?php echo Form::date('create_time',$picture['create_time'],'添加时间','默认是当前时间');?>
          <?php echo Form::input('text','hits',$picture['hits'],'点击量','请输入数字','请输入点击量，默认是0','number');?>
          <?php echo Form::input('text','url',$picture['url'],'链接地址');?>
          <div class="layui-form-item">
            <div class="layui-input-block">
              <button class="layui-btn" lay-submit="" lay-filter="picture_edit">立即提交</button>
            </div>
          </div>
        </div>   
      </form>
    </div>
</div>
<script type="text/javascript">
layui.use(['element', 'form', 'upload', 'laydate', 'layedit'], function(){
  var element = layui.element()
  ,form = layui.form()
  ,laydate = layui.laydate
  ,layedit = layui.layedit
  ,jq = layui.jquery;

  //图片上传
  layui.upload({
    url: '<?php echo url("upload/upimages") ?>'
    ,elem:'#images'
    ,before: function(input){
      loading = layer.load(2, {
        shade: [0.2,'#000'] //0.2透明度的白色背景
      });
    }
    ,success: function(res){
      layer.close(loading);
      var id = jq('.image-block').length;
      var image_str = '';
      jq.each(res.data,function(i,item){
        id++;
        image_str += '<div class="image-block"><input type="hidden" name="images['+id+']" value="'+item.path+'" class="images-input"><img class="img" src="'+item.path+'"><div class="image-block-mask"><span class="del_btn"><i class="layui-icon">&#x1006;</i></span><a class="layui-btn set-index">设为主图</a></div></div>';
      })
      jq('.image-add-blcok').before(image_str);
      layer.msg(res.msg, {icon: 1, time: 1000});
    }
  }); 
  //设为主图
  jq('.images-block-container').on('click','.set-index',function(){
    var index_block = jq(this).parents('.image-block').clone();
    jq(this).parents('.image-block').remove();
    jq('.images-block-container').prepend(index_block);
    jq('.image-block').each(function(e){
      var index = e+1;
      jq(this).find('.images-input').attr('name','images['+index+']')
    });
  });
  //删除图片
  jq('.images-block-container').on('click','.del_btn',function(){
    var index_image = jq(this).parents('.image-block').remove();
  });

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
  
  //监听提交
  form.on('submit(picture_edit)', function(data){
    loading = layer.load(2, {
      shade: [0.2,'#000'] //0.2透明度的白色背景
    });
    var param = data.field;
    jq.post('{:url("picture/edit")}',param,function(data){
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