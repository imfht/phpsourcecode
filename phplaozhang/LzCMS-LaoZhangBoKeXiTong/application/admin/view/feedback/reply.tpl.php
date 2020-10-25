{include file="public/toper" /}
<div class="layui-tab-content">
<form class="layui-form">
<div class="layui-tab-item layui-show">
  <input type="hidden" name="id" value="<?php echo $feedback['id'] ?>">
  <?php if($settings['editor'] && ($settings['editor']=='umeditor')){
        echo Form::umeditor('reply',$feedback['reply'],'回复内容');
  }else{
        echo Form::layedit('reply',$feedback['reply'],'回复内容内容','','请输入回复内容','layedit','reply');
  } ?>
  <?php echo Form::date('reply_time',($feedback['reply_time']>0)?$feedback['reply_time']:date('Y-m-d H:i:s'),'回复时间','');?>
  <div class="layui-form-item">
    <div class="layui-input-block">
      <button class="layui-btn" lay-submit="" lay-filter="reply">立即提交</button>
    </div>
  </div>
</div>   
</form>
</div>
<script type="text/javascript">
layui.use(['form', 'upload', 'layedit', 'laydate'], function(){
  var form = layui.form()
  ,layedit = layui.layedit
  ,laydate = layui.laydate
  ,jq = layui.jquery;

  //创建一个编辑器
  var reply = layedit.build('reply',{
    uploadImage: { url: '<?php echo url("upload/layedit_upimage") ?>' ,type: 'post'  }
    ,height: 250
  });
  //表单验证
  form.verify({
    //编辑器数据同步
    layedit: function(value){
      layedit.sync(reply);
    }
  });
  
  
  //监听提交
  form.on('submit(reply)', function(data){
    loading = layer.load(2, {
      shade: [0.2,'#000'] //0.2透明度的白色背景
    });
    var param = data.field;
    jq.post('{:url("feedback/reply")}',param,function(data){
      if(data.code == 200){
        layer.close(loading);
        layer.msg(data.msg, {icon: 1, time: 1000}, function(){
          var ifram = parent.layer.getFrameIndex(window.name); //先得到当前iframe层的索引
          parent.location.reload(); //刷新父层页面
			    parent.layer.close(ifram); //再执行关闭   
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