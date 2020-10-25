{include file="public/toper" /}
<form class="layui-form">
<div class="layui-tab layui-tab-brief main-tab-container" lay-filter="main-tab">
    <ul class="layui-tab-title main-tab-title">
      <a href="<?php echo url('setting/links') ?>"><li class="layui-this">友情链接</li></a>
      <div class="main-tab-item">相关设置</div>
    </ul>    
    <div class="layui-tab-content">
      <div class="layui-tab-item layui-show">
      <form class="layui-form">
        <table class="list-table">
          <thead>
            <tr>
              <th style="width:40px">排序</th>
              <th>ID</th>
              <th>链接名称</th>
              <th>链接地址</th>
              <th>操作</th>
            </tr> 
          </thead>
          <tbody>
          <?php foreach ($links as $k=>$v) { ?>
            <tr class="show_tr show_tr<?php echo $v['id']; ?>">
              <td><input name="sorts[<?php echo $v['id'] ?>]" type='text' value="<?php echo $v['sort'] ?>" class='layui-input'></td>
              <td><?php echo $v['id']; ?></td>
              <td><?php echo $v['name']; ?></td>
              <td><a href="<?php echo $v['link_url'] ?>" target="_blank" style="color:#009688;"><?php echo $v['link_url']; ?></a></td>
              <td style="text-align: center;">
              <a class="layui-btn layui-btn-small edit_btn" link-id="<?php echo $v['id'] ?>" link-info='<?php echo json_encode($v) ?>' title="编辑"><i class="layui-icon"></i></a>
              <a class="layui-btn layui-btn-small layui-btn-danger del_btn" link-id="<?php echo $v['id'] ?>" title="删除" link-name='<?php echo $v['name'] ?>'><i class="layui-icon"></i></a>
              </td>
            </tr>
          <?php } ?>
          </tbody>
          <thead>
            <tr>
              <th colspan="4"><button class="layui-btn layui-btn-small" lay-submit lay-filter="sort">排序</button></th>
              <th colspan="4" style="text-align: center;"><a class="layui-btn layui-btn-small add_btn">新增链接</a></th>
            </tr> 
          </thead>
        </table>
      </form>
      </div>
    </div>
</div>
</form>
<script>
layui.use(['form', 'element', 'upload'], function(){
  var element = layui.element() //Tab的切换功能，切换事件监听等，需要依赖element模块
  ,form = layui.form()
  ,jq = layui.jquery;

  //点击添加
  jq('.add_btn').on('click',function(){
  	var add_tr = '<tr class="add_tr"><form class="layui-form" ><td><input name="sort" type="text" value="<?php echo $add_id ?>" lay-verify="number" class="layui-input"></td><td style="width:40px"><?php echo $add_id ?><input name="id" type="hidden" value="<?php echo $add_id ?>" class="layui-input"></td><td><input name="name" type="text" value="" placeholder="连接名称" lay-verify="required" class="layui-input"></td><td><input name="link_url" type="text" value="" placeholder="http:// （连接以 http:// 开头）" lay-verify="url" class="layui-input"></td><td style="text-align: center;"><button class="layui-btn layui-btn-small" lay-submit lay-filter="add">添加</button><a class="layui-btn layui-btn-small cancel_add_btn">取消</a></td></form>	</tr>';
  	jq('.add_tr').remove();
  	jq('.list-table tbody').append(add_tr);
  	jq('.show_tr').show(); 
  	jq('.edit_tr').remove();
  });
  //点击取消添加cancel_add
  jq('.list-table tbody').on('click','.cancel_add_btn',function(){
  	jq('.add_tr').remove();
  });
  //监听添加提交
  form.on('submit(add)', function(data){
    loading = layer.load(2, {
      shade: [0.2,'#000'] //0.2透明度的白色背景
    }); 
    var param = data.field;
    jq.post('{:url("setting/edit_link")}',param,function(data){
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
  
  //点击修改
  jq('.edit_btn').click(function(){
  	var id = jq(this).attr('link-id');
  	var info = jq(this).attr('link-info');
  	info = jq.parseJSON( info );
  	var edit_str = '<tr class="edit_tr edit_tr'+info.id+'"><form class="layui-form" ><td><input name="sort" type="text" value="'+info.sort+'" lay-verify="number" class="layui-input"></td><td style="width:40px">'+info.id+'<input name="id" type="hidden" value="'+info.id+'" class="layui-input"></td><td><input name="name" type="text" value="'+info.name+'" placeholder="连接名称" lay-verify="required" class="layui-input"></td><td><input name="link_url" type="text" value="'+info.link_url+'" placeholder="http:// （连接以 http:// 开头）" lay-verify="url" class="layui-input"></td><td style="text-align: center;"><button class="layui-btn layui-btn-small" lay-submit lay-filter="edit">修改</button><a class="layui-btn layui-btn-small cancel_edit_btn" link-id="'+info.id+'">取消</a></td></form>	</tr>';
  	jq('.show_tr'+id).after(edit_str);
  	jq('.add_tr').remove();
  	jq('.show_tr'+id).hide();
 
  });
  //点击取消修改
  jq('.list-table tbody').on('click','.cancel_edit_btn',function(){
  	var id = jq(this).attr('link-id');
  	jq('.show_tr'+id).show();
  	jq('.edit_tr').remove();
  });

  //监听修改提交
  form.on('submit(edit)', function(data){
    loading = layer.load(2, {
      shade: [0.2,'#000'] //0.2透明度的白色背景
    }); 
    var param = data.field;
    jq.post('{:url("setting/edit_link")}',param,function(data){
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
  
  //ajax删除
  jq('.del_btn').click(function(){
    var name = jq(this).attr('link-name');
    var id = jq(this).attr('link-id');
    layer.confirm('确定删除【'+name+'】?', function(index){
      loading = layer.load(2, {
        shade: [0.2,'#000'] //0.2透明度的白色背景
      });
      jq.post('{:url("setting/del_link")}',{'id':id},function(data){
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
  
  //排序
  form.on('submit(sort)', function(data){
    loading = layer.load(2, {
      shade: [0.2,'#000'] //0.2透明度的白色背景
    }); 
    var param = data.field;
    jq.post('{:url("setting/sort_link")}',param,function(data){
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