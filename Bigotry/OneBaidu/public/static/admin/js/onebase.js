  $(function () {

    //刷新验证码
    $(".captcha_change").click(function(){

        var captcha_img_obj = $("#captcha_img");

        captcha_img_obj.attr("src",captcha_img_obj.attr("src")+"?"+Math.random());
    });
    
    //退出
    $(".logout").click(function(){
        
        $.post($(this).attr('url'), {}, success, "json");
        return false;
        
        function success(data){ obalert(data); }
    });
    
    //清理缓存
    $(".clear_cache").click(function(){
        
        $.post($(this).attr('url'), {}, success, "json");
        return false;
        
        function success(data){ obalert(data); }
    });
    
    //登录
    $(".admin-login-form").submit(function(){
        
            button.start('.login-submit-button');
        
            var self = $(this);
            $.post(self.attr("action"), self.serialize(), success, "json");
            return false;
            
            function success(data){

                obalert(data);

                $(".verify").val('');
                
                $(".captcha_change").click();
                
                button.stop('.login-submit-button');
            }
    });
    
    
    //搜索功能
    $("#search").click(function(){
        
        window.location.href = searchFormUrl(this);
    });

    //回车搜索
    $(".search-input").keyup(function(e){
        if(e.keyCode === 13){
                $("#search").click();
                return false;
        }
    });
    
    //ajax get请求
    $('.ajax-get').click(function(){
        
        var target;
        
        if ( $(this).hasClass('confirm') ) {
            
            if(!confirm('确认要执行该操作吗?')){
                
                return false;
            }
        }
        
        if ( (target = $(this).attr('href')) || (target = $(this).attr('url')) ) {
            
            $.get(target).success(function(data){  obalert(data);  });
        }
        
        return false;
    });

    //ajax post submit请求
    $('.ajax-post').click(function(){

        var target,query,form;

        var target_form = $(this).attr('target-form');

        var that = this;

        var nead_confirm=false;

        if( ($(this).attr('type')=='submit') || (target = $(this).attr('href')) || (target = $(this).attr('url')) ){

            form = $('.'+target_form);

            if ($(this).attr('hide-data') === 'true'){//无数据时也可以使用的功能
                form = $('.hide-data');
                query = form.serialize();
            }else if (form.get(0)==undefined){
                return false;
            }else if ( form.get(0).nodeName=='FORM' ){

                if ( $(this).hasClass('confirm') ) {

                    if(!confirm('确认要执行该操作吗?')){

                        return false;
                    }
                }
                if($(this).attr('url') !== undefined){
                        target = $(this).attr('url');
                }else{
                        target = form.get(0).action;
                }
                query = form.serialize();
            }else if( form.get(0).nodeName=='INPUT' || form.get(0).nodeName=='SELECT' || form.get(0).nodeName=='TEXTAREA') {

                form.each(function(k,v){
                    if(v.type=='checkbox' && v.checked==true){
                        nead_confirm = true;
                    }
                })

                if ( nead_confirm && $(this).hasClass('confirm') ) {
                    if(!confirm('确认要执行该操作吗?')){
                        return false;
                    }
                }

                query = form.serialize();
            }else{

                if ( $(this).hasClass('confirm') ) {
                    if(!confirm('确认要执行该操作吗?')){
                        return false;
                    }
                }
                query = form.find('input,select,textarea').serialize();
            }


            var is_ladda_button =  $(that).hasClass('ladda-button');

            is_ladda_button ? button.start('.ladda-button') : $(that).addClass('disabled').attr('autocomplete','off').prop('disabled',true);

            $.post(target,query).success(function(data){

                obalert(data);
                
                is_ladda_button ? button.stop('.ladda-button') : $(that).removeClass('disabled').prop('disabled',false);
            });
        }
        return false;
    });
    
    setTimeout(function(){ $('.fakeloader').hide(); }, 500);
});

/**
 * 提示或提示并跳转
 */
var obalert = function (data) {
    	
    data.code ? toast.success(data.msg) : toast.error(data.msg);

    if(data.url){

        setTimeout(function(){

            location.href = data.url;
        },1500);
    }
    
    if(data.code && !data.url){

        setTimeout(function(){

            location.reload();
        },1500);
    }
};

/**
 * 按钮状态便捷类
 * @type {start, stop}
 */
var button = {
    
    start: function (selectors) {
    	
        var btn = Ladda.create(document.querySelector(selectors));
        btn.start();
    },
    
    stop: function (selectors) {
        
        var btn = Ladda.create(document.querySelector(selectors));
        btn.stop();
    }
};



/**
 * 操纵toastor的便捷类
 * @type {{success: success, error: error, info: info, warning: warning}}
 */
var toast = {
    /**
     * 成功提示
     * @param text 内容
     * @param title 标题
     */
    success: function (text, title) {
    	
    	$(".toast").remove();
    	
        toastr.options = {
            "closeButton": true,
            "debug": false,
            "positionClass": "toast-top-center",
            "onclick": null,
            "showDuration": "1000",
            "hideDuration": "1000",
            "timeOut": "5000",
            "extendedTimeOut": "1000",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        };
        toastr.success(text, title);
    },
    /**
     * 失败提示
     * @param text 内容
     * @param title 标题
     */
    error: function (text, title) {
    	
    	$(".toast").remove();
    	
        toastr.options = {
            "closeButton": true,
            "debug": false,
            "positionClass": "toast-top-center",
            "onclick": null,
            "showDuration": "1000",
            "hideDuration": "1000",
            "timeOut": "5000",
            "extendedTimeOut": "1000",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        };
        toastr.error(text, title);
    },
    /**
     * 信息提示
     * @param text 内容
     * @param title 标题
     */
    info: function (text, title) {
    	
    	$(".toast").remove();
    	
        toastr.options = {
            "closeButton": true,
            "debug": false,
            "positionClass": "toast-top-center",
            "onclick": null,
            "showDuration": "1000",
            "hideDuration": "1000",
            "timeOut": "5000",
            "extendedTimeOut": "1000",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        };
        toastr.info(text, title);
    },
    /**
     * 警告提示
     * @param text 内容
     * @param title 标题
     */
    warning: function (text, title) {

    	$(".toast").remove();
    	
        toastr.options = {
            "closeButton": true,
            "debug": false,
            "positionClass": "toast-top-center",
            "onclick": null,
            "showDuration": "1000",
            "hideDuration": "1000",
            "timeOut": "5000",
            "extendedTimeOut": "1000",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        };
        toastr.warning(text, title);
    }
};

/**
 * 搜索表单url
 */
var searchFormUrl = function (obj) {

    var url = $(obj).attr('url');
    var query  = $('.search-form').find('input').serialize();
    query = query.replace(/(&|^)(\w*?\d*?\-*?_*?)*?=?((?=&)|(?=$))/g,'');
    query = query.replace(/^&/g,'');
    if( url.indexOf('?')>0 ){
        url += '&' + query;
    }else{
        url += '?' + query;
    }
    
    return url;
};
