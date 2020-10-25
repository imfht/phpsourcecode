/**
 * Created by Yuri2 on 2016/12/9.
 */
//naples 助手

function Naples() {
    this.data={};
    if (typeof Naples._initialized == "undefined") {
        Naples.prototype.has = function(key) {
            if (key==undefined){
                return false;
            }else{
                return this.data[key]==undefined;
            }
        };
        Naples.prototype.get = function(key) {
            if (key==undefined){
                return this.data;
            }else{
                return this.data[key];
            }
        };
        Naples.prototype.set = function(key,value) {
            if (value==undefined){
                this.data=key;
            }else{
                this.data[key]=value;
            }
        };
        Naples.prototype.log = function (msg) {
            if (window.console) {
                window.console.log(msg);
            }
        };
        Naples._initialized = true;
    }
}

Date.prototype.Format = function (fmt) { //author: meizz   
    var o = {
        "M+": this.getMonth() + 1, //月份   
        "d+": this.getDate(), //日   
        "h+": this.getHours(), //小时   
        "m+": this.getMinutes(), //分   
        "s+": this.getSeconds(), //秒   
        "q+": Math.floor((this.getMonth() + 3) / 3), //季度   
        "S": this.getMilliseconds() //毫秒   
    };
    if (/(y+)/.test(fmt)) fmt = fmt.replace(RegExp.$1, (this.getFullYear() + "").substr(4 - RegExp.$1.length));
    for (var k in o)
        if (new RegExp("(" + k + ")").test(fmt)) fmt = fmt.replace(RegExp.$1, (RegExp.$1.length == 1) ? (o[k]) : (("00" + o[k]).substr(("" + o[k]).length)));
    return fmt;
}; 