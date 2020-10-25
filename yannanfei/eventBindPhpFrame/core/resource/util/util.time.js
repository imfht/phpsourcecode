define(function(require, exports, module) {
    return {
//获取当前时间戳
        time:function(){
            var date=new Date();
            //毫秒变成秒
            return parseInt(date.getTime()/1000);
        },
//时间转化为时间戳
        strtotime:function(time_str){
            return  parseInt(Date.parse(time_str)/1000);
        },
        /*
         //格式化时间
         // 对Date的扩展，将 Date 转化为指定格式的String
         // 月(M)、日(d)、小时(h)、分(m)、秒(s)、季度(q) 可以用 1-2 个占位符，
         // 年(y)可以用 1-4 个占位符，毫秒(S)只能用 1 个占位符(是 1-3 位的数字)
         // 例子：
         // (new Date()).Format("yyyy-MM-dd hh:mm:ss.S") ==> 2006-07-02 08:09:04.423
         // (new Date()).Format("yyyy-M-d h:m:s.S")      ==> 2006-7-2 8:9:4.18*/
        date:function(format,time_stamp){
            var date=new Date();
            if(time_stamp){
                date=new Date(time_stamp*1000);
            }
            var o = {
                "M+" : date.getMonth()+1, //月份
                "d+" : date.getDate(), //日
                "h+" : date.getHours()%12 == 0 ? 12 : date.getHours()%12, //小时
                "H+" : date.getHours(), //小时
                "m+" : date.getMinutes(), //分
                "s+" : date.getSeconds(), //秒
                "q+" : Math.floor((date.getMonth()+3)/3), //季度
                "S" : date.getMilliseconds() //毫秒
            };
            var week = {
                "0" : "/u65e5",
                "1" : "/u4e00",
                "2" : "/u4e8c",
                "3" : "/u4e09",
                "4" : "/u56db",
                "5" : "/u4e94",
                "6" : "/u516d"
            };
            if(/(y+)/.test(format)){
                format=format.replace(RegExp.$1, (date.getFullYear()+"").substr(4 - RegExp.$1.length));
            }
            if(/(E+)/.test(format)){
                format=format.replace(RegExp.$1, ((RegExp.$1.length>1) ? (RegExp.$1.length>2 ? "/u661f/u671f" : "/u5468") : "")+week[date.getDay()+""]);
            }
            for(var k in o){
                if(new RegExp("("+ k +")").test(format)){
                    format = format.replace(RegExp.$1, (RegExp.$1.length==1) ? (o[k]) : (("00"+ o[k]).substr((""+ o[k]).length)));
                }
            }
            return format;
        }

    };
});
