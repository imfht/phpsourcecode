/**
 * Created by Happy on 2016/5/19 0019.
 */
//定义通用函数模块
define(function(require, exports, module) {
    function isType(type) {
        return function(obj) {
            return {}.toString.call(obj) == "[object " + type + "]"
        }
    }
    //超时函数检测
    function callback(obj,callback,interval,timeout,timeoutCallback){
        var timestamp= Date.parse(new Date());
        if(typeof obj!='undefined'){
            callback(obj);
        }
        else if(timestamp>timeout){
            setTimeout(function(){
                callback(obj,callback,interval,timeout,timeoutCallback);
            },interval);
        }
        else{//已经超时
            timeoutCallback();
        }
    }

    return {
        isObject :isType("Object"),
        isString :isType("String"),
        isArray : Array.isArray || isType("Array"),
        isFunction : isType("Function"),
        isUndefined : isType("Undefined"),

        //轮询查询指定对象是否存在,如果存在执行回调函数，如果不存在直到超时，不再轮询，回调也不执行,超时和时间都是毫秒
        callback:function(obj,callback,interval,timeout,timeoutCallback){
            var timestamp= Date.parse(new Date());
            timeout=timestamp+timeout;
            callback(obj,callback,interval,timeout,timeoutCallback);
        },
        trim:function(str){
            return str.replace(/(^\s*)|(\s*$)/g, "");
        },

        json: function(str_obj) {//使用eval更通用
            if (typeof  str_obj == 'object') {
                return str_obj;
            }
            window.json_errors = str_obj;
            try {
                return $.parseJSON(str_obj);
            }
            catch (e) {

                console.log('json解析出错，出错内容如下：');
                console.log(e);
                console.log(window.json_errors);
                throw new Error(10,"js解析错误");
              //  $('html').html(window.json_errors);//全屏显示错误，及时发现错误，线上应该去掉这句话

                return {};
            }},

        loadCss: function(e) {
            var t = document.createElement("link");
            t.setAttribute("type", "text/css");
            t.setAttribute("href", e);
            // t.setAttribute("href", e);
            t.setAttribute("rel", "stylesheet");
            css_id = document.getElementById("auto_css_id");
            if (css_id) {
                document.getElementsByTagName("head")[0].removeChild(css_id)
            }
            document.getElementsByTagName("head")[0].appendChild(t)
        },
         //加载js
        loadJs:function (e) {
            var t = document.createElement("script");
            t.setAttribute("type", "text/javascript");
            t.setAttribute("src", e);
            t.setAttribute("id", "auto_script_id");
            script_id = document.getElementById("auto_script_id");
            if (script_id) {
                document.getElementsByTagName("head")[0].removeChild(script_id)
            }
            document.getElementsByTagName("head")[0].appendChild(t)
        },

        //最简单的模板引擎 传入内容和变量
        T :function (html, options) {//后又添加了关键字var
            //对于%和>要做特殊处理 去掉对>号的特殊处理 先暂时不能用%号了
            var re = /<%([^%]+)?%>/g, reExp = /(^( )?(if|for|else|switch|case|break|{|}|var))(.*)?/g, code = 'var r=[];\n', cursor = 0;
            var add = function (line, js) {
                // console.log(line);
                js ? (code += line.match(reExp) ? line + '\n' : 'r.push(' + line + ');\n') :
                    (code += line != '' ? 'r.push("' + line.replace(/"/g, '\\"') + '");\n' : '');
                return add;
            };
            while (match = re.exec(html)) {
                add(html.slice(cursor, match.index))(match[1], true);
                cursor = match.index + match[0].length;
            }
            add(html.substr(cursor, html.length - cursor));
            code += 'return r.join("");';
            return new Function(code.replace(/[\r\t\n]/g, '')).apply(options);
        },
        SimpleT:function(str,obj){ //简单的变量提花模板
            /*
             var obj={
             'a':'bb',
             'b':'cc',
             'c':'dd'
             };
             var str='---{a}-----{b}-------{c}--';
             */
            var match= str.match(/{.+?}/g);
            var newObj={};
            for(var i in obj){
                newObj['{'+i+'}']=obj[i];
            }
            return str.replace(/{.+?}/g,function(o){
                if(isNaN(newObj[o])){ return newObj[o]?newObj[o]:''; }
                else{
                   return newObj[o];
                }

            });
        },
        simpleT:function(str,obj){
           return this.SimpleT(str,obj);
        },

        getQueryString:function(e) { //获取页面参数
            var t = new RegExp("(^|&)" + e + "=([^&]*)(&|$)");
            var a = window.location.search.substr(1).match(t);
            if (a != null) return a[2];
            return ""
        },
        is_pc:function(){
            var userAgentInfo = navigator.userAgent; var Agents =["Android", "iPhone", "SymbianOS", "Windows Phone", "iPad", "iPod"]; var flag = true; for (var v = 0; v < Agents.length; v++) { if (userAgentInfo.indexOf(Agents[v]) > 0) { flag = false; break; } } return flag;
        },
        //获取触屏或点击事件的事件名
        get_event:function(haveOn){
            var event='touchstart';//触屏事件
            if(this.is_pc()){
                event='click'; //点击事件
            }
            if(haveOn){ //是否有on
                return 'on'+event;
            }
            else{
                return event;
            }
        },
        parse:function(json_str){
         return this.json(json_str);
        },
        //阻止表单重复提交
        prevent_repeat_submit:function(){
            $('body').on('submit','form',function(){
                $(this).find('input[type=submit]').attr('disabled',true);
            });
        },
        //绑定图片预览事件
        /*dom 模型
        *<img style="max-width: 200px; max-height: 200px;" name="set_effect_img" src="">
        * <input type="file" name="set_effect_img" >

        * 事件
        *util.bind_image_preview(set_effect_img)
        *最终生成hidden     <input type="hidden" name="set_effect_img">存储16进制文本
        * */
        bind_image_preview:function(img_set_name,value,callback){//绑定图片上传事件
            var set_name=img_set_name;//图片设置
            var img=$("img[name="+set_name+"]");
            var file=$("input[type=file][name="+set_name+"]");
            file.attr('name','');
            file.attr('data-name',set_name);
            file.parent().append('<input type="hidden" name="'+set_name+'" />');
            if(value){ //默认值
                img.attr('src',value);
            }

            if(file.size()>0){ //说明有上传图片的功能
                //绑定事件
                file.on('change',function(){
                    var f = this.files[0]; //this就是file的dom
                    //判断类型是不是图片
                    if(!/image\/\w+/.test(f.type)){
                        alert("请确保文件为图像类型");
                        return false;
                    }
                    if(Math.floor(f.size/1024/1024)>10){
                        alert('图片大小超过1MB，请上传10MB以内的图片');
                        return false;
                    }

                    var set_name=$(this).attr('data-name');
                    var reader = new FileReader();
                    reader.readAsDataURL(f);
                    reader.onload = function(e){
                        if(callback){ //如果有回调直接调用回调函数
                            callback(this.result);
                        }
                        else{
                            $("img[name="+set_name+"]").attr('src',this.result);
                            $("input[name="+set_name+"]").val(this.result);
                        }
                    }
                });
            }
        },
        //绑定图片改变
        bind_image_resize_preview:function(img_set_name,value,max_width,max_height){
            // 参数，最大高度
            this.bind_image_preview(img_set_name,value,function(base64_img){
                var src=base64_img;
                // 创建一个 Image 对象
                var image = new Image();
// 绑定 load 事件处理器，加载完成后执行

                image.onload = function(){
// 获取 canvas DOM 对象
                    var canvas_html='<canvas  style="display: none"></canvas>';
                    //创建一个canvas内存对象
                    var canvas =$(canvas_html)[0];// document.getElementById("myCanvas");
                    var ctx = canvas.getContext("2d");
// 如果高度超标或者宽度超标
                    if(max_height&&max_width){//如果宽度和高度都限制了
                        if(image.height>max_height){
                            image.height=max_height;
                        }
                        if(image.width>max_width){
                            image.width=max_width;
                        }
                    }
                    else if(max_width&&image.width>max_width){//仅仅限制宽度
                        image.height *= max_width / image.width;
                        image.width = max_width;
                    }


                    if(max_height&&image.height >max_height) {
// 宽度等比例缩放 *=
                        image.width *= max_height / image.height;
                        image.height = max_height;
                    }
// 获取 canvas的 2d 环境对象,
// 可以理解Context是管理员，canvas是房子

// canvas清屏
                    ctx.clearRect(0, 0, canvas.width, canvas.height);
// 重置canvas宽高
                    canvas.width = image.width;
                    canvas.height = image.height;
// 将图像绘制到canvas上
                    ctx.drawImage(image, 0, 0, image.width, image.height);
// !!! 注意，image 没有加入到 dom之中 //设置图片和hidden
                    var new_src=canvas.toDataURL();
                    //重新赋值
                    $("img[name="+img_set_name+"]").attr('src',new_src);
                    $("input[name="+img_set_name+"]").val(new_src);
                };
// 设置src属性，浏览器会自动加载。
// 记住必须先绑定事件，才能设置src属性，否则会出同步问题。
                image.src = src;
            });
        },

        /*新增select设置默认值绑定
        *dom模型
        * <select data-value="1" class="set_val"></select>
        * 使用方式
        * Util.set_default_value();
        * **/
        set_default_value:function (wrapper){
            var items=[];
            if(wrapper){
                items=  $(wrapper).find('.set_val');
            }
            else{
                items=  $('form .set_val');
            }

        items.each(function(){
            var $this=$(this);
            var data=$this.data();
            var type=$(this).attr('type');
            if(type=='hidden'&&data.name&&data.type){
                    if(data.type=='radio'){//设置多个radio
                        var selector='input:radio[name='+data.name+'][value='+data.value+']';
                        $(selector).attr("checked",'checked');
                    }
            }
            else{
                var val=$this.data('value');
                if(val&&val!='0'){
                    $this.val(val);
                }
            }


        });
    },
        //设置和获取localstorage的值
        get_local:function(key){
            var L= localStorage;
           return L.getItem(key);
        },
        set_local:function(key,value){
            var L= localStorage;
            L.setItem(key,value);
        },
        serial_form:function(selector){//序列化表单为json对象
            if(selector.substr(0,1)!='#'){
                selector='#'+selector;
            }
         var arr=  $(selector).serializeArray();
            var serializeObj={};
            $(arr).each(function(){
                serializeObj[this.name]=this.value;
            });
            return serializeObj;
        },
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