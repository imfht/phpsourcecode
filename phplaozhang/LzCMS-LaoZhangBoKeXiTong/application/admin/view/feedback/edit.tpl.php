{include file="public/toper" /}
<div class="layui-tab layui-tab-brief main-tab-container">
    <ul class="layui-tab-title main-tab-title">
      <li class="layui-this">修改留言</li>
      <div class="main-tab-item">留言管理</div>
    </ul>
    <div class="layui-tab-content">
       <form class="layui-form">
        <div class="layui-tab-item layui-show">
          <input type="hidden" name="id" value="<?php echo $feedback['id'] ?>">
          <?php echo Form::input('text','title',$feedback['title'],'标题','','请输入标题','required');?>
          <?php echo Form::date('create_time',$feedback['create_time'],'留言时间','');?>
          <?php if($settings['editor'] && ($settings['editor']=='umeditor')){
                echo Form::umeditor('content',$feedback['content'],'留言内容');
                echo Form::umeditor('reply',$feedback['reply'],'回复内容');
          }else{
                echo Form::layedit('content',$feedback['content'],'留言内容','','请输入留言内容','layedit','content');
                echo Form::layedit('reply',$feedback['reply'],'回复内容','','请输入回复内容','layedit','reply');
          } ?>
          <?php echo Form::date('reply_time',($feedback['reply_time']>0)?$feedback['reply_time']:date('Y-m-d'),'回复时间','');?>
          <div class="layui-form-item">
            <div class="layui-input-block">
              <button class="layui-btn" lay-submit="" lay-filter="feedback_edit">立即提交</button>
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
    ,height: 200
  });
  //创建一个编辑器
  var reply = layedit.build('reply',{
    uploadImage: { url: '<?php echo url("upload/layedit_upimage") ?>' ,type: 'post'  }
    ,height: 150
  });
  //表单验证
  form.verify({
    //编辑器数据同步
    layedit: function(value){
      layedit.sync(content);
      layedit.sync(reply);
    }
  });
  
  
  //监听提交
  form.on('submit(feedback_edit)', function(data){
    loading = layer.load(2, {
      shade: [0.2,'#000'] //0.2透明度的白色背景
    });
    var param = data.field;
    jq.post('{:url("feedback/edit")}',param,function(data){
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