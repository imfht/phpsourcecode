{include file="public/toper" /}
<div class="layui-tab layui-tab-brief main-tab-container">
    <ul class="layui-tab-title main-tab-title">
      <div class="main-tab-item">会员管理</div>
    </ul>
    <div class="layui-tab-content">
      <div class="layui-tab-item layui-show">
      <!-- 搜索 -->
      <form class="layui-form layui-form-pane search-form">
        <div class="layui-form-item">
          <label class="layui-form-label">昵称</label>
          <div class="layui-input-inline">
            <input type="text" name="search[nickname]" value="<?php echo $search['nickname'] ?>" lay-verify="" placeholder="请输入昵称搜索" autocomplete="off" class="layui-input">
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
              <th>ID</th>
              <th>昵称</th>
              <th>性别</th>
              <th class="can_click">
              <?php if($order['create_time'] == 'desc'){ ?>
                <a href="?<?php echo $url_params ?>&order[create_time]=asc">添加时间 ▼</a>
              <?php }elseif($order['create_time'] == 'asc'){ ?>
                <a href="?<?php echo $url_params ?>&order[create_time]=desc">添加时间 ▲</a>
              <?php }else{ ?>
                <a href="?<?php echo $url_params ?>&order[create_time]=desc">添加时间</a>
              <?php } ?>
              </th>
              <th>修改时间</th>
              <th>最后登陆时间</th>
              <th style="width:90px">操作</th>
            </tr> 
          </thead>
          <tbody>
          <?php foreach ($members as $v) { ?>
            <tr>
              <td><input type="checkbox" name="ids[<?php echo $v['id'] ?>]" lay-filter="checkOne" value="<?php echo $v['id'] ?>" title=" "></td>
              <td><?php echo $v['id'] ?></td>
              <td>
                <?php echo $v['nickname']; ?> 
                <?php if($v['avatar']){ ?>
                <a class="thumb" href="<?php echo $v['avatar'] ?>" target="_blank" thumb="<?php echo $v['avatar'] ?>"><i class="layui-icon">&#xe64a;</i></a>
                <?php  } ?>
              </td>
              <td><?php echo $v['sex'] ?></td>
              <td><?php echo $v['create_time'] ?></td>
              <td><?php echo $v['update_time'] ?></td>
              <td><?php echo $v['last_login_time'] ?></td>
              <td style="text-align: center;">
              <!-- <a href="<?php echo url('member/edit','id='.$v['id']) ?>" class="layui-btn layui-btn-small" title="编辑"><i class="layui-icon"></i></a> -->
              <a class="layui-btn layui-btn-small layui-btn-danger del_btn" member-id="<?php echo $v['id'] ?>" title="删除" nickname='<?php echo $v['nickname'] ?>'><i class="layui-icon"></i></a>
              </td>
            </tr>
          <?php } ?>
          </tbody>
          <thead>
            <tr>
               <th colspan="1"><button class="layui-btn layui-btn-small" lay-submit lay-filter="delete">删除</button></th>
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
  
  //图片预览
  jq('.list-table td .thumb').hover(function(){
    jq(this).append('<img class="thumb-show" src="'+jq(this).attr('thumb')+'" >');
  },function(){
    jq(this).find('img').remove();
  });


  //ajax删除
  jq('.del_btn').click(function(){
    var name = jq(this).attr('nickname');
    var id = jq(this).attr('member-id');
    layer.confirm('确定删除【'+name+'】?', function(index){
      loading = layer.load(2, {
        shade: [0.2,'#000'] //0.2透明度的白色背景
      });
      jq.post('{:url("member/del")}',{'id':id},function(data){
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
      jq.post('{:url("member/batches_delete")}',param,function(data){
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