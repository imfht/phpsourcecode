{include file="public/toper" /}
<div class="layui-tab layui-tab-brief main-tab-container">
    <ul class="layui-tab-title main-tab-title">
      <div class="main-tab-item">留言管理</div>
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
        <table class="list-table">
          <thead>
            <tr>
              <th style="width:40px"><input type="checkbox" name="checkAll" lay-filter="checkAll" title=" "></th>
              <th style="min-width:25px">ID</th>
              <th>标题</th>
              <th class="can_click">
              <?php if($order['create_time'] == 'desc'){ ?>
                <a href="?<?php echo $url_params ?>&order[create_time]=asc">留言时间 ▼</a>
              <?php }elseif($order['create_time'] == 'asc'){ ?>
                <a href="?<?php echo $url_params ?>&order[create_time]=desc">留言时间 ▲</a>
              <?php }else{ ?>
                <a href="?<?php echo $url_params ?>&order[create_time]=desc">留言时间</a>
              <?php } ?>
              </th>
              <th class="can_click">
              <?php if($order['reply_time'] == 'desc'){ ?>
                <a href="?<?php echo $url_params ?>&order[reply_time]=asc">回复时间 ▼</a>
              <?php }elseif($order['reply_time'] == 'asc'){ ?>
                <a href="?<?php echo $url_params ?>&order[reply_time]=desc">回复时间 ▲</a>
              <?php }else{ ?>
                <a href="?<?php echo $url_params ?>&order[reply_time]=desc">回复时间</a>
              <?php } ?>
              </th>
              <th style="width:90px">操作</th>
            </tr> 
          </thead>
          <tbody>
          <?php foreach ($feedbacks as $v) { ?>
            <tr>
              <td><input type="checkbox" name="ids[<?php echo $v['id'] ?>]" lay-filter="checkOne" value="<?php echo $v['id'] ?>" title=" "></td>
              <td><?php echo $v['id'] ?></td>
              <td><a class="list-title" href="javascript:void(0)" feedback-id="<?php echo $v['id'] ?>"><?php echo $v['title']; ?></a></td>
              <td><?php echo $v['create_time']; ?></td>
              <td><?php if($v['reply_time'] != 0){echo $v['reply_time'];}else{ echo '未回复[<a class="reply_btn" href="javascript:void(0)" feedback-id="'.$v['id'].'">点击回复</a>]';} ?></td>
              <td style="text-align: center;">
              <a href="<?php echo url('feedback/edit','id='.$v['id']) ?>" class="layui-btn layui-btn-small" title="编辑"><i class="layui-icon"></i></a>
              <a class="layui-btn layui-btn-small layui-btn-danger del_btn" feedback-id="<?php echo $v['id'] ?>" title="删除" feedback-name='<?php echo $v['title'] ?>'><i class="layui-icon"></i></a>
              </td>
            </tr>
          <?php } ?>
          </tbody>
          <thead>
            <tr>
               <th><button class="layui-btn layui-btn-small" lay-submit lay-filter="delete">删除</button></th>
              <th colspan="6"><div id="page"></div></th>
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

  //留言预览
  jq('.list-title').click(function(){
    loading = layer.load(2, {
      shade: [0.2,'#000'] //0.2透明度的白色背景
    });
    var id = jq(this).attr('feedback-id');
    jq.post('{:url("feedback/get_content")}',{'id':id}, function(data){
      if(data.code == 200){
        layer.close(loading);
        layer.open({
          type: 1,
          area: ['500px'],
          title: '留言内容',
          content: '<div style="padding:15px 20px;min-height: 300px;">'+data.content+'</div>' //注意，如果str是object，那么需要字符拼接。
        }); 
      }else{
        layer.close(loading);
        layer.msg(data.msg, {icon: 2, anim: 6, time: 1000});
      }
      
    });
  });
 
  //留言回复
  jq('.reply_btn').click(function(){
  	var id = jq(this).attr('feedback-id');
    layer.open({
      type: 2,
      icon: 2,
      maxmin: true,
      area: ['800px','500px'],
      title: '回复内容',
      content: ['{:url("feedback/reply")}?id='+id, 'no']
    });
  });
  

  //ajax删除
  jq('.del_btn').click(function(){
    var name = jq(this).attr('feedback-name');
    var id = jq(this).attr('feedback-id');
    layer.confirm('确定删除【'+name+'】?', function(index){
      loading = layer.load(2, {
        shade: [0.2,'#000'] //0.2透明度的白色背景
      });
      jq.post('{:url("feedback/del")}',{'id':id},function(data){
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
      jq.post('{:url("feedback/batches_delete")}',param,function(data){
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