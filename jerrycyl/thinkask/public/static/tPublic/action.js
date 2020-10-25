layui.config({
  base: '/public/static/tPublic/' 
}).extend({ //设定组件别名
  thinkask: 'thinkask' //相对于上述base目录的子目录
});

layui.use(['jquery','layer','flow','thinkask', 'laytpl', 'form', 'upload', 'util','element'], function(){
  var $ = layui.jquery
      ,element = layui.element()
      ,layer = layui.layer
      ,thinkask = layui.thinkask;
  //导航的hover效果、二级菜单等功能，需要依赖element模块
  //监听导航点击
  element.on('nav(demo)', function(elem){
    //console.log(elem)
    // layer.msg(elem.text());
  });

  var flow = layui.flow
    //按屏加载图片
      flow.lazyimg({
        elem: 'img'
        ,scrollElem: '' //一般不用设置，此处只是演示需要。
      });

$(window).keydown(function(event){ 
 switch (event.which) { 
   case(13): //ebter键
    $('.tPost').click()
   break; 
 
 } 
 
 });
/**
 * [加密POST提交]
 * @Author   Jerry
 * @DateTime 2017-04-29
 * @Example  eg:
 * @param    {[type]}   event) {             } [description]
 * @return   {[type]}          [description]
 */
$('.tPost').click(function(event) {
      var encode    = thinkask._getAttr($(this),'encode')?thinkask._getAttr($(this),'encode'):"";//默认不加密处理
      var table     = thinkask._getAttr($(this),'table')?thinkask._getAttr($(this),'table'):"";//默认不加密处理
      var where     = thinkask._getAttr($(this),'where')?thinkask._getAttr($(this),'where'):"";
      var form      = thinkask._getAttr($(this),'form')?thinkask._getAttr($(this),'form'):"tForm";
      var url       = thinkask._getAttr($(this),'url')?thinkask._getAttr($(this),'url'):"/systems/api/tpost";
      var btnStatus = thinkask._getAttr($(this),'btnStatus')?thinkask._getAttr($(this),'btnStatus'):"";
      var o = thinkask._getFormJson('.'+form);
      // console.log(o)
      var clickbtn = $(this);
        o['encode'] = encode;
        o['table'] = table;
        o['where'] = where;
    if(!btnStatus||btnStatus=="on"){
        thinkask._chlickStart(clickbtn)
        thinkask.tajax(url,o,'true','json','true').done(function(re){
        thinkask._chlickEnd(clickbtn)
        thinkask._showInfo(re)
        })
    }

});
/**
 * [前台退出]
 * @Author   Jerry
 * @DateTime 2017-06-12T17:02:34+0800
 * @Example  eg:
 * @param    {String}                 event) {             thinkask.tajax("/ucenter/api/logout","",'true','json','true').done(function(re){      window.location.href [description]
 * @return   {[type]}                        [description]
 */
$('.tLoginOut').click(function(event) {
  thinkask.tajax("/ucenter/api/logout","",'true','json','true').done(function(re){
      window.location.href="/";
  })
});

/**
 * [后台退出]
 * @Author   Jerry
 * @DateTime 2017-06-12T17:03:13+0800
 * @Example  eg:
 * @param    {String}                 event) {              thinkask.tajax("/ucenter/api/logout","",'true','json','true').done(function(re){      window.location.href [description]
 * @return   {[type]}                        [description]
 */
$('.tAdminLoginOut').click(function(event) {
   thinkask.tajax("/ucenter/api/adminLoginOut","",'true','json','true').done(function(re){
      window.location.href="/";
  })
});

  //<a class="btn btn-danger btn-xs" table="{:encode('category')}" where="{:encode('id-'.[$v['id']])}"  href="javascript:;">删除</a>
  /**
   * [公共删除]
   * @Author   Jerry
   * @DateTime 2017-05-01
   * @Example  eg:
   * @param    {[type]}   ) {                  var encode [description]
   * @return   {[type]}     [description]
   */
  $('.tDel').click(function() {
      var encode     = thinkask._getAttr($(this),'encode')?thinkask._getAttr($(this),'encode'):"";//默认不加密处理
      var where      = thinkask._getAttr($(this),'where')?thinkask._getAttr($(this),'where'):"";//默认不加密处理
      var table      = thinkask._getAttr($(this),'table')
      var form       = thinkask._getAttr($(this),'form')?thinkask._getAttr($(this),'form'):"tForm";
      var where      = thinkask._getAttr($(this),'where')
      var url        = thinkask._getAttr($(this),'url')?thinkask._getAttr($(this),'url'):"/systems/api/tdel";
      var o          = thinkask._getFormJson('.'+form);
      var btnStatus  = thinkask._getAttr($(this),'btnStatus');
      var clickbtn   = $(this);
      o['encode']    = encode;
      o['table']     = table;
      o['where']     = where;
        if(!btnStatus||btnStatus=="on"){
          thinkask._chlickStart(clickbtn)
          thinkask.tajax(url,o,'true','json','true').done(function(re){
          thinkask._chlickEnd(clickbtn)
          thinkask._showInfo(re)
        })
    }


  });


// demo:<a href="javascript:;" url="{:U('Userscategory/edit')}" title="新建组织结构" class="btn btn-primary frAlert" type="button"><i class="fa fa-sticky-note"></i> 新建组织结构</a>
/**
 * [IFRAME打开网页]
 * @Author   Jerry
 * @DateTime 2017-05-01
 * @Example  eg:
 * @param    {[type]}   event) {              var title [description]
 * @return   {[type]}          [description]
 */
$('.frAlert').click(function(event) {
   var title = $(this).attr('title');
   var url = $(this).attr('url');
   var wh = $(this).attr('wh');
   var hi = $(this).attr('hi');
   if(!title){
    title = "信息";
   }
   if(!wh){
    wh="80%"
   }

  thinkask.frAlert(title,url,wh,"70%");
}); 




});

