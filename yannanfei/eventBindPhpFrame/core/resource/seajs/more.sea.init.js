/**
 * Created by Happy on 2016/10/9 0009.
 * lcc 2016-10-9 多美容院管理系统版本的初始化
 */

(function(){
    if (location.href.indexOf('.php') > -1||location.href.indexOf('.html') < 0) {//从后台初始化
        var short=location.href.substring(0,location.href.indexOf('/index.php'));
        var root_url=short.substr(0,short.lastIndexOf('/'));
        var app_name=short.substr(short.lastIndexOf('/'));
       root_url= root_url.substr(0,root_url.lastIndexOf('/'));

        //alert(part1);
        //alert(app_name);
        //root_url=part1;
         var base_url=root_url+app_name;
        seajs.config({
            path: {
                'lib': base_url+'/resource'
            },
            // 路径配置
            paths: {
                'root': root_url+app_name,
                'core':root_url+'/core/resource'
            },
            base: base_url+"/resource",
            charset: "utf-8"
        });
        //根据act_op执行前台js相应的初始化
        (function () {
            function getQueryString(e) { //获取页面参数
                var t = new RegExp("(^|&)" + e + "=([^&]*)(&|$)");
                var a = window.location.search.substr(1).match(t);
                if (a != null) return a[2];
                return ""
            }

            var act = getQueryString('act');
            var op = getQueryString('op');
            if (!act) {
                act = 'index';
            }
            if (!op) {
                op = 'index';
            }

            seajs.use('common/js/' + act + '.js', function (actObj) {

                if(actObj&&typeof actObj[op]=='function')
                { actObj[op]();} //执行代码
                else {
                    console.log('you may forget define '+op+' function in '+act+'.js');
                }
            });

        })();
    }

//根据url分析act 和op的值,引入对应的js


})();
