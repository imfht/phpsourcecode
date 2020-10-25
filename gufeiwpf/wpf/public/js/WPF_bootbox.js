/*
bootbox弹层请求

__Examples__

WPF.bootbox({
   type:"dialog",//可选择dialog或者alert 默认dialog
   status:"error",//可选择success或error 默认success
   message:"可以包含HTML",
   title:"标题",//
   size:"large"  //可选择large大或者small小，或者为空
});


WPF.ajax({
   url:"a.php",
   type:"get",//可选择GET或POST,默认GET
   data:{id:1,name:"aaa"},
   title:"管理页面",
   bootboxtype:"dialog",//可选择dialog或者alert
   size:"large",  //可选择large大或者small小，或者为空
   success:function(ret,UTbox){
      alert(111);//执行成功后要执行的函数
   },
   error:function(ret,UTbox){
      alert(222);//执行失败后要执行的函数
   }
});
*/

/*
if(typeof WPF !== "object"){
    WPF = {};
    WPF.files_hosts = "<!--{C('URL_FILES')}-->";
}
*/
if(typeof bootbox !== "object" || WPF.bootbox !== "function"){
    $.getScript(WPF.files_hosts+"/js/bootbox/bootbox.js", function(){
        WPF.bootbox = function(options){
            if (typeof options !== "object") {
                throw new Error("参数需要是一个对象");
            }
            
            if (!options.type) {
                options.type = "dialog";
            }
            if (!options.status) {
                options.status = "success";
            }
            if (!options.message) {
                options.message = "";
            }
            if (!options.title) {
                options.title = "";
            }
            if (!options.size) {
                options.size = "";
            }
            var WPFbox = false;
            switch(options.type){
                case "dialog":
                    WPFbox = bootbox.dialog({
                        message: options.message,
                        title: options.title,
                        size:options.size
                    });
                    break;
                case "alert":
                    switch(options.status){
                        case "success":
                            var successhtml = '<div class="row">'+
                                                    '<div class="col-md-11">'+
                                                        '<div class="portlet-body">'+
                                                    		'<div class="alert alert-success">'+
                                                    			'<strong>消息：'+options.message+'</strong>'+
                                                    		'</div>'+
                                                        '</div>'+
                                                    '</div>'+
                                                '</div>';
                            WPFbox = bootbox.alert(successhtml);
                            break;
                        case "error":
                            var errorhtml = '<div class="row">'+
                                                '<div class="col-md-11">'+
                                                    '<div class="portlet-body">'+
                                                		'<div class="alert alert-danger">'+
                                                			'<strong>错误：'+options.message+'</strong>'+
                                                		'</div>'+
                                                    '</div>'+
                                                '</div>'+
                                            '</div>';
                            WPFbox = bootbox.alert(errorhtml);
                            break;
                        
                    }
                    break;
            }
            return WPFbox;
        }
        
        
        WPF.ajax = function(options){
            if (typeof options !== "object") {
                throw new Error("参数需要是一个对象");
            }
            
            if (!options.url) {
                throw new Error("请求的URL必须指定");
            }
            if (!options.type) {
                options.type = "post";
            }
            if (!options.data) {
                options.data = "";
            }
            if (!options.title) {
                options.title = "";
            }
            
            if (!options.bootboxtype) {
                options.bootboxtype = "dialog";
            }
            
            if (!options.size) {
                options.size = "";
            }
            var WPFbox = false;
            jQuery.ajax({
                url:options.url,
                type:options.type,
                dataType:"json",
                async:false,
                data:options.data,
                success:function(ret){
                    
                    if(ret.status){
                        switch(options.bootboxtype){
                            case "dialog":
                                WPFbox = ASIACATION.bootbox({
                                    message: ret.info,
                                    title: options.title,
                                    size:options.size
                                });
                                break;
                            case "alert":
                                bootbox.hideAll();
                                
                                WPFbox = ASIACATION.bootbox({
                                    message: ret.info,
                                    type:"alert",
                                    status : "success"
                                });
                                
                                setTimeout(function(){
                                    bootbox.hideAll();
                                },5000);
                                break;
                            
                        }
                                           
                    }else{
                        
                        WPFbox = ASIACATION.bootbox({
                            message: ret.info,
                            type:"alert",
                            status : "error"
                        });
                        
                    }
                    if ( typeof options.success === "function" ) {
                        options.success = options.success.call(this, ret, WPFbox);
                    }
                },
                error:function(XMLHttpRequest, textStatus, errorThrown){                
                    WPFbox = WPF.bootbox({
                            message: XMLHttpRequest.status+" "+errorThrown,
                            type:"alert",
                            status : "error"
                        });                
                    var ret = {XMLHttpRequest:XMLHttpRequest, textStatus:textStatus, errorThrown:errorThrown};
                    if ( typeof options.error === "function" ) {
                        options.error = options.error.call(this, ret, WPFbox);
                    }
                }
            });
            return WPFbox;
        }
        
    });

}
