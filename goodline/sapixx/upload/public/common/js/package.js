/*JS组件包*/
var js = document.scripts;
var jspath = js[js.length-1].src.substring(0,js[js.length-1].src.lastIndexOf("/")+1);
Do.setConfig('coreLib', [jspath + 'jquery-1.12.4.min.js']);
Do.add('base',{path :jspath + 'function.js'});
var package = jspath + 'package/';
//layer
Do.add('layer_css',{path :package + 'layer/theme/default/layer.css',type : 'css'});
Do.add('layer',{path :package + 'layer/layer.js',type : 'js',requires : ['layer_css']});
//form
Do.add('form_css',{path : package + 'form/Validform.css',type : 'css'});
Do.add('form_js',{path : package + 'form/Validform.js',type : 'js'});
Do.add('form',{path : package + 'form/Validform_Datatype.js',requires : ['form_js', 'form_css']});
//layout
Do.add('ztree_css',{path : package + 'zTree/zTreeStyle.css',type:'css'});
Do.add('ztree',{path : package + 'zTree/jquery.ztree.js',requires:['ztree_css']});
//Tab
Do.add('tab',{path :package + 'tab/jquery.idTabs.min.js',type : 'js'});
//JS模板引擎
Do.add('tpl',{path :package + 'art-tpl/template-web.js',type : 'js'});
//SuperSlide
Do.add('slide',{path : package + 'slide/SuperSlide.js'});
//uploadify
Do.add('upload_css',{path : package + 'uploader/webuploader.css',type : 'css'});
Do.add('upload',{path : package + 'uploader/webuploader.min.js',requires : ['upload_css']});
//tip
Do.add('tip_css',{path : package + 'tip/style.css',type : 'css'});
Do.add('tip',{path : package + 'tip/powerFloat.js',requires : ['tip_css']});
//editor
Do.add('editor_js',{path : package + 'editor/kindeditor-min.js'});
Do.add('editor', { path: package + 'editor/lang/zh_CN.js', requires: ['editor_js'] });
//DatePicker
Do.add('date',{path : package + 'laydate/laydate.js'});
//color
Do.add('colorcss',{path : package + 'color/style.css',type : 'css'});
Do.add('color',{path : package + 'color/ColorPacker.js',requires : ['colorcss']});
//图片延迟加载
Do.add('lazyimg',{path : package + 'lazyimg/lazyimg.js'});
//表单
Do.add('jform', {path: package + 'form/jquery.form.js',type: 'js'});
//图片动画效果
Do.add('swipercss',{path : package + 'swiper/swiper.css',type:'css'});
Do.add('swiper',{path : package + 'swiper/swiper.js',type : 'js',requires : ['swipercss']});
//抛物线动画
Do.add('requestAnimationFrame', {path: package + 'fly/requestAnimationFrame.js',type: 'js'}); 
Do.add('fly', {path: package + 'fly/jquery.fly.min.js',type: 'js',requires: ['requestAnimationFrame']});
//城市
Do.add('city', {path: package + 'city/distpicker.min.js',type: 'js'});
//菜单
Do.add('menucss',{path : package + 'menu/selectmenu.css',type : 'css'});
Do.add('menu', { path: package + 'menu/selectmenu.min.js', type: 'js', requires: ['menucss'] });
//二维码
Do.add('qrcode', {path: package + 'qrcode/qrcode.min.js',type: 'js'});