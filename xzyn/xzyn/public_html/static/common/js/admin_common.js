/**
 * common
 */
$(function(){

    $.pjax.defaults.timeout = 5000;
    $.pjax.defaults.maxCacheLength = 0;
    $(document).pjax('a:not(a[target="_blank"])', {container:'#pjax-container', fragment:'#pjax-container'});

    $(document).ajaxStart(function(){
        layer.load(2);  //加载状态
    }).ajaxStop(function(){
        layer.closeAll('loading'); //关闭加载层
    });

    //提交
    $('body').off('click', '.submits');
    $('body').on("click", '.submits', function(event){
        var _this = $(this);
        _this.button('loading');
        var form = _this.closest('form');
        if(form.length){
            var ajax_option={
                dataType:'json',
                success:function(data){
                    if(data.status == '1'){
                        layer.msg(data.info,{offset:'100px'});
                        var url = data.url;
                        $.pjax({url: url, container: '#pjax-container', fragment:'#pjax-container'})
                    }else if(data.status == '2'){
                        restlogin(data.info);
                    }else{
                        _this.button('reset');
                        layer.msg(data.info,{offset:'100px'});
                    }
                }
            }
            form.ajaxSubmit(ajax_option);
        }
    });

    //搜索
    $(document).on('submit', 'form[pjax-search]', function(event) {
        $.pjax.submit(event, {container:'#pjax-container', fragment:'#pjax-container'})
    })

    //授权用户
    $('body').off('click', '.submitss');
    $('body').on("click", '.submitss', function(event){
        $("#search_to option").each(function (){
            $(this).prop('selected', 'selected');
        });
        $("#search option").each(function (){
            $(this).prop('selected', 'selected');
        });
        var _this = $(this);
        _this.button('loading');
        var form = _this.closest('form');
        if(form.length){
            var ajax_option={
                dataType:'json',
                success:function(data){
                    if(data.status == '1'){
                        layer.msg(data.info,{offset:'100px'});
                        var url = data.url;
                        $.pjax({url: url, container: '#pjax-container', fragment:'#pjax-container'})
                    }else if(data.status == '2'){
                        restlogin(data.info);
                    }else{
                        _this.button('reset');
                        layer.msg(data.info,{offset:'100px'});
                    }
                }
            }
            form.ajaxSubmit(ajax_option);
        }
    });

    //单条删除-批量删除
    $('body').off('click', '.delete-one,.delete-all');
    $('body').on("click", '.delete-one,.delete-all', function(event){
        event.preventDefault();
        var _this = $(this);
        var title = _this.data('title')?_this.data('title'):'删除';
        var url_del = _this.data('url')||'';
        var message = _this.data('message')?_this.data('message'):'确认操作？';
        if(_this.hasClass('delete-all')){   //批量删除
            var id = '';
            var str = '';
            var table_box = _this.closest('.box-header').next('.box-body').find(".table tr td input[type='checkbox']");
            $(table_box).each(function(){
                if(true == $(this).is(':checked')){
                    str += $(this).val() + ",";
                }
            });
            if(str.substr(str.length-1)== ','){
                id = str.substr(0, str.length-1);
            }
        }else{                              //单条删除
            var id = _this.data('id')||'';
        }
        if(id && url_del){
            BootstrapDialog.confirm({
                title: title,
                message: message,
                btnCancelLabel: '取消',
                btnOKLabel: '确定',
                callback: function(resultDel) {
                    if(resultDel === true) {
                        $.ajax({
                            type : "post",
                            url : url_del,
                            dataType : 'json',
                            data : { id:id, },
                            success : function(data) {
                                if(data.status == '1'){
                                    layer.msg(data.info,{offset:'100px'});
                                    var url = data.url;
                                    $.pjax({url: url, container: '#pjax-container', fragment:'#pjax-container'})
                                }else if(data.status == '2'){
                                    restlogin(data.info);
                                }else{
                                    layer.msg(data.info,{offset:'100px'});
                                }
                            }
                        });
                    }
                }
            });
        }
    });

    //上传
    $('body').off('click', '.up-btn');
    $('body').on("click", '.up-btn', function(event){
        var _this_up_btn = $(this);   //当前上传按钮
        var up_url = _this_up_btn.data('url');   //上传地址
        //var $('.modal-dialog .Uploads').val();

        BootstrapDialog.confirm({
            title: "上传 - Upload",
            message: '<form method="POST" action="'+up_url+'" enctype="multipart/form-data" ><input type="file" name="imgFile" class="Uploads" /></form>',
            btnCancelLabel: '取消',
            btnOKLabel: '确定',
            callback: function(result) {
                if(result) {
                    var form = $('.modal-dialog').find('form');
                    var ajax_option={
                        dataType:'json',
                        success:function(data){
                        	console.log(data);
                            if(data.error == '0'){
                                _this_up_btn.prev().attr("href", data.url);
                                _this_up_btn.prev().find('img').attr("src", data.url);
                                _this_up_btn.closest('.input-group').find('input').val(data.url);
                                layer.msg('上传成功',{offset:'100px'});
                            }else{
                                layer.msg(data.info,{offset:'100px'});
                            }
                        }
                    }
                    form.ajaxSubmit(ajax_option);
                }
            }
        });
    });

    //状态status列表修改（只能进行0和1值的切换）
    $('body').off('click', 'td a.editimg');
    $('body').on('click', 'td a.editimg', function(event){
        var addclass;
        var removeclass;
        var pvalue;   //提交值
        var _this = $(this);
        var field = _this.data('field');
        var id = _this.data('id');
        var value = _this.data('value');
        var url = _this.data('url');
        if ( value == 1){
            pvalue = 0;
            addclass = "fa-check-circle text-green";
            removeclass = "fa-times-circle text-red";
        }else{
            pvalue = 1;
            addclass = "fa-times-circle text-red";
            removeclass = "fa-check-circle text-green";
        }
        var dataStr = jQuery.parseJSON( '{"id":"'+id+'","'+field+'":"'+pvalue+'"}' );   //字符串转json
        $.ajax({
            type : "post",
            url : url,
            dataType : 'json',
            data : dataStr,
            success : function(data) {
                if(data.status == '1'){
                    _this.data('value', pvalue);
                    _this.removeClass(addclass);
                    _this.addClass(removeclass);
                }else if(data.status == '2'){
                    restlogin(data.info);
                }else{
                    layer.msg(data.info+'或检查验证类',{offset:'100px'});
                }
            }
        });
    });

})

/*
 * 输入框只能输入  1111.11【数字和小数点后两位】
 */
function clearNoNum(obj){
    obj.value = obj.value.replace(/[^\d.]/g,"");        //清除“数字”和“.”以外的字符
    obj.value = obj.value.replace(/\.{2,}/g,".");       //只保留第一个. 清除多余的
    obj.value = obj.value.replace(".","$#$").replace(/\./g,"").replace("$#$",".");
    obj.value = obj.value.replace(/^(\-)*(\d+)\.(\d\d).*$/,'$1$2.$3');          //只能输入两个小数
    if(obj.value.indexOf(".")< 0 && obj.value !=""){    //以上已经过滤，此处控制的是如果没有小数点，首位不能为类似于 01、02的金额
        obj.value= parseFloat(obj.value);
    }
}

function restlogin($info){
    var dialog = BootstrapDialog.show({
        title: $info,
        message: $('<div></div>').load('/admin/login/restlogin'),
        closable: false,   //右上角是否显示'x'
        buttons: [{
            label: '退出',
            action: function(dialog) {
                window.location.href = "/admin/login/loginout";
            }
        },{
            label: '登录',
            cssClass: 'btn-primary',
            action: function(dialog) {
                var $button = this;   //当前按钮
                $button.button('loading');
                var form = $('.modal-dialog').find('form');
                var ajax_option={
                    dataType:'json',
                    success:function(data){
                        if(data.status == '1'){
                            dialog.close();
                            layer.msg(data.info,{offset:'100px'});
                        }else{
                            $button.button('reset');
                            $('#code').click();
                            layer.msg(data.info,{offset:'100px'});
                        }
                    }
                }
                form.ajaxSubmit(ajax_option);
            }
        }]
    });
    dialog.getModal().css('background-color','rgba(0,0,0,0.9)');
}

//获取icon图标列表
function my_iconlist(){
    var iconlistarr = [];
    var iconlists = $('<div class="modal fade" id="iconkuang" >'+
			'<div class="modal-dialog">'+
				'<div class="modal-content">'+
					'<div class="modal-body x-gd-y" style="height:500px;"><div class="row x-tc x-plr-15" id="icon_list"></div></div>'+
				'</div>'+
			'</div>'+
		'</div>');
    $('body').append(iconlists);
    if (iconlistarr.length == 0) {
	    $.get("/static/xzyn/css/variables.less", function (ret) {
	        var exp = /fa-var-(.*):/ig;
	        var result;
	        while ((result = exp.exec(ret)) != null) {
	            iconlistarr.push(result[1]);
	            var icons = $('<div class="col-sm-1 col-xs-2 x-ptb-10 x-plr-0 x-b x-mb-10 thisicon" data-iconname="fa-' + result[1] +
	            	'"title="' + result[1] + ' ">' +
	            	'<i class="x-f20 fa fa-' + result[1] + '"></i></div>'
	            );
	        	$('#icon_list').append(icons);
	       	}
	   });
   }
}


