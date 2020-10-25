{include file="public/toper" /}
<div class="layui-tab layui-tab-brief main-tab-container">
    <ul class="layui-tab-title main-tab-title">
      <a href="<?php echo url('link/add','category_id='.$category_id) ?>"><li>添加链接</li></a>
      <div class="main-tab-item">链接管理</div>
    </ul>
    <div class="layui-tab-content">
      <div class="layui-tab-item layui-show">
      <!-- 搜索 -->
      <form class="layui-form layui-form-pane search-form">
        <div class="layui-form-item">
          <label class="layui-form-label">标题</label>
          <div class="layui-input-inline">
            <input type="text" name="search[title]" value="<?php echo $search['title'] ?>" lay-verify="" placeholder="请输入标题搜索" autocomplete="off" class="layui-input">
          </div>
          <button class="layui-btn" lay-submit="" lay-filter="">搜索</button>
        </div>
        <!-- 每页数据量 -->
        <div class="layui-form-item page-size">
          <label class="layui-form-label total">共计 <?php echo $total; ?> 条</label>
          <label class="layui-form-label">每页数据条</label>
          <div class="layui-input-inline">
            <input type="text" name="page_size" value="<?php echo $per_page ?>" lay-verify="number" placeholder="" autocomplete="off" class="layui-input">
          </div>
          <button class="layui-btn" lay-submit="" lay-filter="">确定</button>
        </div>
      </form>
      <form class="layui-form">
      <input type="hidden" name="category_id" value="<?php echo $category_id ?>">
        <table class="list-table">
          <thead>
            <tr>
              <th style="width:40px"><input type="checkbox" name="checkAll" lay-filter="checkAll" title=" "></th>
              <th style="min-width:25px">ID</th>
              <th>标题</th>
              <th>所属栏目</th>
              <th class="can_click">
              <?php if($order['create_time'] == 'desc'){ ?>
                <a href="?<?php echo $url_params ?>&order[create_time]=asc">添加时间 ▼</a>
              <?php }elseif($order['create_time'] == 'asc'){ ?>
                <a href="?<?php echo $url_params ?>&order[create_time]=desc">添加时间 ▲</a>
              <?php }else{ ?>
                <a href="?<?php echo $url_params ?>&order[create_time]=desc">添加时间</a>
              <?php } ?>
              </th>
              <th style="width:60px;text-align: center;" class="can_click">
              <?php if($order['is_recommend'] == 'desc'){ ?>
                <a href="?<?php echo $url_params ?>&order[is_recommend]=asc">推荐 ▼</a>
              <?php }elseif($order['is_recommend'] == 'asc'){ ?>
                <a href="?<?php echo $url_params ?>&order[is_recommend]=desc">推荐 ▲</a>
              <?php }else{ ?>
                <a href="?<?php echo $url_params ?>&order[is_recommend]=desc">推荐</a>
              <?php } ?>
              </th>
              <th style="width:60px;text-align: center;" class="can_click">
              <?php if($order['is_top'] == 'desc'){ ?>
                <a href="?<?php echo $url_params ?>&order[is_top]=asc">置顶 ▼</a>
              <?php }elseif($order['is_top'] == 'asc'){ ?>
                <a href="?<?php echo $url_params ?>&order[is_top]=desc">置顶 ▲</a>
              <?php }else{ ?>
                <a href="?<?php echo $url_params ?>&order[is_top]=desc">置顶</a>
              <?php } ?>
              </th>
              <th style="width:90px">操作</th>
            </tr> 
          </thead>
          <tbody>
          <?php foreach ($links as $v) { ?>
            <tr>
              <td><input type="checkbox" name="ids[<?php echo $v['id'] ?>]" lay-filter="checkOne" value="<?php echo $v['id'] ?>" title=" "></td>
              <td><?php echo $v['id'] ?></td>
              <td>
                <?php echo $v['title']; ?> 
                <?php if($v['image_url']){ ?>
                <a class="thumb" href="<?php echo $v['image_url'] ?>" target="_blank" thumb="<?php echo thumb($v['image_url'],200,200) ?>"><i class="layui-icon">&#xe64a;</i></a>
                <?php  } ?>
                <?php if($v['url']){ ?>
                <a href="<?php echo $v['url'] ?>" target="_blank" class="link"><i class="layui-icon">&#xe64c;</i></a>
                <?php  } ?>  
              </td>
              <td><?php echo $v['category_name'] ?></td>
              <td><?php echo $v['create_time'] ?></td>
              <td style="text-align: center;">
              <?php if($v['is_recommend'] != 1){ ?>
                <a href="javascript:void(0)" class="list-switch list-switch-off is_recommend" data-id="<?php echo $v['id'] ?>" title="点击开启"><i class="layui-icon">&#x1006;</i></a>
              <?php }else{ ?>
                <a href="javascript:void(0)" class="list-switch list-switch-on is_recommend" data-id="<?php echo $v['id'] ?>" title="点击关闭"><i class="layui-icon">&#xe605;</i></a>
              <?php } ?>
              </td>
              <td style="text-align: center;">
              <?php if($v['is_top'] != 1){ ?>
                <a href="javascript:void(0)" class="list-switch list-switch-off is_top" data-id="<?php echo $v['id'] ?>" title="点击开启"><i class="layui-icon">&#x1006;</i></a>
              <?php }else{ ?>
                <a href="javascript:void(0)" class="list-switch list-switch-on is_top" data-id="<?php echo $v['id'] ?>" title="点击关闭"><i class="layui-icon">&#xe605;</i></a>
              <?php } ?>
              </td>
              <td style="text-align: center;">
              <a href="<?php echo url('link/edit','id='.$v['id']) ?>" class="layui-btn layui-btn-small" title="编辑"><i class="layui-icon"></i></a>
              <a class="layui-btn layui-btn-small layui-btn-danger del_btn" link-id="<?php echo $v['id'] ?>" title="删除" link-name='<?php echo $v['title'] ?>'><i class="layui-icon"></i></a>
              </td>
            </tr>
          <?php } ?>
          </tbody>
          <thead>
            <tr>
               <th colspan="2"><button class="layui-btn layui-btn-small" lay-submit lay-filter="delete">删除</button><button class="layui-btn layui-btn-small" lay-submit lay-filter="move">移动</button></th>
              <th colspan="7"><div id="page"></div></th>
            </tr> 
          </thead>
        </table>
      </form>
      </div>
    </div>
</div>
<script type="text/javascript">
layui.use(['element', 'laypage', 'layer', 'form'], function(){
  var element = layui.element()
  ,jq = layui.jquery
  ,form = layui.form()
  ,laypage = layui.laypage;

  //推荐
  jq('.is_recommend').click(function(){
    var id = jq(this).attr('data-id');
      loading = layer.load(2, {
        shade: [0.2,'#000'] //0.2透明度的白色背景
      });
      var row = jq(this);
      jq.post('{:url("link/to_recommend")}',{'id':id},function(data){
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
  //置顶
  jq('.is_top').click(function(){
    var id = jq(this).attr('data-id');
      loading = layer.load(2, {
        shade: [0.2,'#000'] //0.2透明度的白色背景
      });
      var row = jq(this);
      jq.post('{:url("link/to_top")}',{'id':id},function(data){
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

  //ajax删除
  jq('.del_btn').click(function(){
    var name = jq(this).attr('link-name');
    var id = jq(this).attr('link-id');
    layer.confirm('确定删除【'+name+'】?', function(index){
      loading = layer.load(2, {
        shade: [0.2,'#000'] //0.2透明度的白色背景
      });
      jq.post('{:url("link/del")}',{'id':id},function(data){
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
  
  //全选
  form.on('checkbox(checkAll)', function(data){
    if(data.elem.checked){
      jq("input[type='checkbox']").prop('checked',true);
    }else{
      jq("input[type='checkbox']").prop('checked',false);
    }
    form.render('checkbox');
  });  

  form.on('checkbox(checkOne)', function(data){
    var is_check = true;
    if(data.elem.checked){
      jq("input[lay-filter='checkOne']").each(function(){
        if(!jq(this).prop('checked')){ is_check = false; }
      });
      if(is_check){
        jq("input[lay-filter='checkAll']").prop('checked',true);
      }
    }else{
      jq("input[lay-filter='checkAll']").prop('checked',false);
    } 
    form.render('checkbox');
  });

  //监听提交
  form.on('submit(delete)', function(data){
    //判断是否有选项
    var is_check = false;
    jq("input[lay-filter='checkOne']").each(function(){
      if(jq(this).prop('checked')){ is_check = true; }
    });
    if(!is_check){
      layer.msg('请选择数据', {icon: 2,anim: 6,time: 1000});
      return false;
    }
    //确认删除
    layer.confirm('确定批量删除?', function(index){
      loading = layer.load(2, {
        shade: [0.2,'#000'] //0.2透明度的白色背景
      });
      var param = data.field;
      jq.post('{:url("link/batches_delete")}',param,function(data){
        if(data.code == 200){
          layer.close(loading);
          layer.msg(data.msg, {icon: 1, time: 1000}, function(){
            location.reload();//do something
          });
        }else{
          layer.close(loading);
          layer.msg(data.msg, {icon: 2,anim: 6, time: 1000});
        }
      });
    });
    return false;
  });

  //监听移动提交
  form.on('submit(move)', function(data){
    //判断是否有选项
    var is_check = false;
    jq("input[lay-filter='checkOne']").each(function(){
      if(jq(this).prop('checked')){ is_check = true; }
    });
    if(!is_check){
      layer.msg('请选择数据', {icon: 2,anim: 6,time: 1000});
      return false;
    }
    //移动到位置选择 var params = JSON.stringify(data.field);
    loading = layer.load(2, {
      shade: [0.2,'#000'] //0.2透明度的白色背景
    });
    var param = data.field;
    jq.post('{:url("category/model_category_select")}',param,function(data){
      layer.open({
        type: 1, 
        title: '数据移动',
        area: ['auto', '490px'],
        content: data //这里content是一个普通的String
      });
      form.render('select');
      layer.close(loading);
      form.on('select(move)', function(data){
        param.to_category_id = data.value;
        var selected_text = jq(data.elem).find("option:selected").text();
        //确认移动
        layer.confirm('确定批量移动至【'+selected_text+'】?', function(index){
          loading = layer.load(2, {
            shade: [0.2,'#000'] //0.2透明度的白色背景
          });
          jq.post('{:url("link/batches_move")}',param,function(data){
            if(data.code == 200){
              layer.close(loading);
              layer.msg(data.msg, {icon: 1, time: 1000}, function(){
                location.reload();//do something
              });
            }else{
              layer.close(loading);
              layer.msg(data.msg, {icon: 2,anim: 6, time: 1000});
            }
          });
        });
      });
    });

    return false;
  });


  laypage({
    cont: 'page'
    ,skip: true
    ,pages: <?php echo ceil($total/$per_page) ?> //总页数
    ,groups: 5 //连续显示分页数
    ,curr: <?php echo $current_page ?>
    ,jump: function(e, first){ //触发分页后的回调
      if(!first){ //一定要加此判断，否则初始时会无限刷新
        loading = layer.load(2, {
          shade: [0.2,'#000'] //0.2透明度的白色背景
        });
        location.href = '?<?php echo $url_params ?>&page='+e.curr;
      }
    }
  });
})
</script>

{include file="public/footer" /}