/**
 * Created by Happy on 2016/5/24 0024.
 */
//没有命名就是文件名作为模块id名为config
define(function(){
    var APP_SITE_URL='{{app_site_url}}'; //直接从config.ini.php的键值中获取 模板可以自行设置，设置resource plugin的模板位置参数即可
    var Config={
         get_url:function(act,op){
             var s=APP_SITE_URL+'/index.php';
             return  s+'?act='+act+'&op='+op;
             //return T(s,{act:act,op:op});
         },
         url:APP_SITE_URL
    };
  return Config;
});