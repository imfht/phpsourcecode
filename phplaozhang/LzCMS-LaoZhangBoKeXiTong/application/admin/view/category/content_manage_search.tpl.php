{include file="public/toper" /}
<div class="layui-tab layui-tab-brief main-tab-container">
    <ul class="layui-tab-title main-tab-title">
      <div class="main-tab-item">内容管理</div>
    </ul>
    <div class="layui-tab-content">
      <div class="layui-tab-item layui-show">

      </div>
    </div>
</div>


<script type="text/javascript">
layui.use(['layer', 'jquery','tree'], function(){
  var layer = layui.layer
  ,jq = layui.jquery

  layer.open({
    type: 1, 
    closeBtn: 0,
    shade: 0.1,
    title: '快速进入',
    area: ['500px', '450px'],
    content: '<div id="search_content" class="layui-form-select"><div class="layui-input-block"><input type="text" name="title" lay-verify="title" autocomplete="off" placeholder="请输入栏目名称可快速搜索" class="layui-input"><dl class="layui-anim layui-anim-upbit search_content_list"></dl></div></div>' //这里content是一个普通的String
  });

  jq('#search_content .layui-input').keyup(function(event){
  	var keywords = jq(this).val();
  	var	last_categorys = <?php echo $category_list ?>;
    jq('.search_content_list').empty();
    if(keywords != ''){
      for(var o in last_categorys){
        var reg = new RegExp(keywords) ;
        if( reg.test( last_categorys[o].name )){
          jq('.search_content_list').append('<a href="'+last_categorys[o].href+'"><dd>'+last_categorys[o].name+'</dd></a>');
          jq('.search_content_list').show();
        }
      }
    }else{
      jq('.search_content_list').hide();
    }
  });

});
</script>
{include file="public/footer" /}