/**
 * Created by Happy on 2016/5/19 0019.
 */
//定义通用函数模块 精简版,去掉不用的函数
define(function(require, exports, module) {
    function isType(type) {
        return function(obj) {
            return {}.toString.call(obj) == "[object " + type + "]"
        }
    }

    return {
        isObject :isType("Object"),
        isString :isType("String"),
        isArray : Array.isArray || isType("Array"),
        isFunction : isType("Function"),
        isUndefined : isType("Undefined"),

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
        }
    };

});