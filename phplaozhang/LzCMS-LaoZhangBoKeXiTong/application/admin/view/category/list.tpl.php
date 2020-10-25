{include file="public/toper" /}
<div class="layui-tab layui-tab-brief main-tab-container">
    <ul class="layui-tab-title main-tab-title">
      <a href="<?php echo url('category/index') ?>"><li class="layui-this">栏目列表</li></a>
      <a href="<?php echo url('category/add') ?>"><li>添加栏目</li></a>
      <a href="<?php echo url('category/add','model_id=0') ?>"><li>添加外部链接</li></a>
      <a href="javascript:void(0)" class="update_content_links"><li>更新内容链接</li></a>
      <div class="main-tab-item">栏目管理</div>
    </ul>
    <div class="layui-tab-content">
      <div class="layui-tab-item layui-show">
      <form class="layui-form">
        <table class="list-table">
          <thead>
            <tr>
              <th style="width:40px">排序</th>
              <th style="min-width:25px">ID</th>
              <th>栏目名称</th>
              <th>所属模型</th>
              <th style="width:60px;text-align: center;">导航显示</th>
              <th style="width:90px">操作</th>
            </tr> 
          </thead>
          <tbody>
          <?php foreach ($category_list as $v) { ?>
            <tr>
              <td><input name="sorts[<?php echo $v['id'] ?>]" type='text' value="<?php echo $v['sort'] ?>" lay-verify="number" class='layui-input'></td>
              <td><?php echo $v['id'] ?></td>
              <td>
                <?php echo $v['sep_name']; ?> 
                <?php if($v['image_url']){ ?>
                <a class="thumb" href="<?php echo $v['image_url'] ?>" target="_blank" thumb="<?php echo thumb($v['image_url'],200,200) ?>"><i class="layui-icon">&#xe64a;</i></a>
                <?php  } ?> 
                <?php if($v['url'] && $v['model_id'] == 0){ ?>
                <a href="<?php echo $v['url'] ?>" target="_blank" class="link"><i class="layui-icon">&#xe64c;</i></a>
                <?php  } ?> 
              </td>
              <td><?php 
              if($v['model_id'] == 0){
                echo '<span style="color:red">外部链接</span>';
              }else{
                echo $v['model_name'];
              }
              ?></td>
              <td style="text-align: center;">
              <?php if($v['is_menu'] != 1){ ?>
                <a href="javascript:void(0)" class="list-switch list-switch-off" category-id="<?php echo $v['id'] ?>" title="点击开启"><i class="layui-icon">&#x1006;</i></a>
              <?php }else{ ?>
                <a href="javascript:void(0)" class="list-switch list-switch-on" category-id="<?php echo $v['id'] ?>" title="点击关闭"><i class="layui-icon">&#xe605;</i></a>
              <?php } ?>
              
              </td>
              <td style="text-align: center;">
              <a href="<?php echo url('category/edit','id='.$v['id']) ?>" class="layui-btn layui-btn-small" title="编辑"><i class="layui-icon"></i></a>
              <a class="layui-btn layui-btn-small layui-btn-danger del_btn" category-id="<?php echo $v['id'] ?>" title="删除" category-name='<?php echo $v['name'] ?>'><i class="layui-icon"></i></a>
              </td>
            </tr>
          <?php } ?>
          </tbody>
          <thead>
            <tr>
              <th colspan="6"><button class="layui-btn layui-btn-small" lay-submit lay-filter="sort">排序</button></th>
            </tr> 
          </thead>
        </table>
      </form>
      </div>
    </div>
</div>

<script type="text/javascript">
layui.use(['element', 'layer', 'form'], function(){
  var element = layui.element()
  ,jq = layui.jquery
  ,form = layui.form()
  ,laypage = layui.laypage;

  //图片预览
  jq('.list-table td .thumb').hover(function(){
    jq(this).append('<img class="thumb-show" src="'+jq(this).attr('thumb')+'" >');
  },function(){
    jq(this).find('img').remove();
  });
  //链接预览
  jq('.list-table td .link').hover(function(){
    var link = jq(this).attr('href');
    layer.tips( link, this, {
      tips: [2, '#009688'],
      time: false
    });
  },function(){
    layer.closeAll('tips');
  });

  //监听提交
  form.on('submit(sort)', function(data){
    loading = layer.load(2, {
      shade: [0.2,'#000'] //0.2透明度的白色背景
    });
    var param = data.field;
    jq.post('{:url("category/sort")}',param,function(data){
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
    var name = jq(this).attr('category-name');
    var id = jq(this).attr('category-id');
    layer.confirm('确定删除【'+name+'】?', function(index){
      loading = layer.load(2, {
        shade: [0.2,'#000'] //0.2透明度的白色背景
      });
      jq.post('{:url("category/del")}',{'id':id},function(data){
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

  //导航是否显示
  jq('.list-switch').click(function(){
    var id = jq(this).attr('category-id');
      loading = layer.load(2, {
        shade: [0.2,'#000'] //0.2透明度的白色背景
      });
      var row = jq(this);
      jq.post('{:url("category/menu_switch")}',{'id':id},function(data){
        if(data.code == 200){
          if(row.hasClass('list-switch-off')){
            row.removeClass('list-switch-off').find('.layui-icon').html('&#xe605;');
            row.attr('title','点击开启');
          }else{
            row.addClass('list-switch-off').find('.layui-icon').html('&#x1006;');
            row.attr('title','点击关闭');
          }
          layer.close(loading);
          layer.msg(data.msg, {icon: 1, time: 1000});
        }else{
          layer.close(loading);
          layer.msg(data.msg, {icon: 2, anim: 6, time: 1000});
        }
      });
  });

  //更新内容链接
  jq('.update_content_links').click(function(){
    loading = layer.load(2, {
        shade: [0.2,'#000'] //0.2透明度的白色背景
    });
    jq.post('{:url("category/update_content_links")}',{},function(data){
        if(data.code == 200){
          layer.close(loading);
          layer.msg(data.msg, {icon: 1, time: 1000});
        }else{
          layer.close(loading);
          layer.msg(data.msg, {icon: 2, anim: 6, time: 1000});
        }
      });
  });
  
})
</script> 
</body>
</html>