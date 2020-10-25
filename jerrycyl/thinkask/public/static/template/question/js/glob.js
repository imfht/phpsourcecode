
layui.use(['element','layer','flow'], function(){
  var element = layui.element(),layer = layui.layer; //导航的hover效果、二级菜单等功能，需要依赖element模块
  // var $ = layui.jquery;
  
  //监听导航点击
  element.on('nav(demo)', function(elem){
    //console.log(elem)
    layer.msg(elem.text());
  });

  var flow = layui.flow
    //按屏加载图片
      flow.lazyimg({
        elem: 'img'
        ,scrollElem: '' //一般不用设置，此处只是演示需要。
      });
      
  $('.dropdown-toggle').dropdown()
// $('.dropdown-toggle').click(function(event) {
//   $('.dropdown-menu').show()
// }).mouseout(function(event) {
//  $('.dropdown-menu').hide()
// });;

});