define(function (require,exports,module){
    //var $ = require('jquery');

    var dialog = require('dialog');
    if (typeof(parent.dialog) != "undefined") { 
        dialog = parent.dialog;
    }

    var fresh = function(url){
        var nowPage=window.location;
        if (typeof(iframe) != "undefined") { 
            nowPage=iframe.contentWindow.location;
        }
        if(!url){
            nowPage.href = nowPage.pathname + nowPage.search
        }else{
            nowPage.href=url; 
        }
    };

    var loading = function(type){
        if(type == undefined) type=1;
        if(type){
            dialog({id:'LoadingBox',content: '<div i="content" class="ui-dialog-content" id="content:LoadingBox"><span class="ui-dialog-loading">Loading..</span></div><span style="margin-left:5px;">数据加载中...</span>'}).show();
        }else{
            dialog.get('LoadingBox').close().remove();
        }
    };

    //GET
    exports.get = function (name){
        if(name == undefined) name=".AjaxGet";
        $(name).click(function(e) {
            var url=$(this).data('url'),
                title=$(this).text();
            loading(1);
            $.get(url, function(result){
                dialog({
                    id: 'AjaxGetBox',
                    title: title,
                    content: '<p class="tc">'+result.info+'</p>',
                    width: 300,
                    padding: 10,
                    quickClose: true,
                    onshow: function (){
                        loading(0);
                    },
                }).show();
                if(result.status){
                    setTimeout(function(){
                        fresh(result.url);
                    }, 2000); 
                }
            });
        });
    };

    //Confirm
    exports.confirm = function (name){
        if(name == undefined) name=".AjaxConfirm";
        $(name).click(function(){
            var url=$(this).data('url'),
                title=$(this).text();
            dialog({
                align: 'top right',
                content: '您确定要 '+title+' 么?',
                okValue: '确定',
                ok: function () {
                    loading(1);
                    $.get(url, function(result){
                        dialog({
                            id: 'AjaxConfirmBox',
                            content: result.info,
                            quickClose: true,
                            onshow: function (){
                                loading(0);
                            },
                        }).show();
                        if(result.status){
                            setTimeout(function(){
                                fresh(result.url);
                            }, 2000);
                        }
                    });
                },
                cancelValue: '取消',
                cancel: function () {},
                quickClose: true,
            }).show(this);
        });
    };

    //GET返回HTML模板
    exports.html = function (name){
        if(name == undefined) name=".AjaxHtml";
        $(name).click(function(e) {
            var url=$(this).data('url'),
                title=$(this).text();
            loading(1);

            $.ajax({
                type: "GET",
                url: url,
                success: function (result) {
                    dialog({
                        id: 'AjaxHtmlBox',
                        title: title,
                        width: 600,
                        content: result,
                        onshow: function (){loading(0);},
                    }).show();
                },
                error: function(x, t, e){
                    dialog({
                        id: 'AjaxHtmlBox',
                        title: title,
                        quickClose: true,
                        content: x.responseText,
                        onshow: function (){loading(0);},
                    }).show();
                }
            });
        }); 
    };

    //GET返回HTML模板
    exports.image = function (name){
        if(name == undefined) name=".AjaxImage";
        $(name).click(function(e) {
            var url=$(this).data('url');
            dialog({
                id: 'AjaxImageBox',
                title: '图片预览',
                content: '<img class="auto" src="'+url+'" />',
                quickClose: true,
            }).show();
        }); 
    };

    exports.bacth = function (name){
        if(name == undefined) name=".AjaxBatch";
        if(!$(name).length) return false;

        $(name).submit(function(e) {
            var url=$(this).data('action');
            var data=$(this).serializeArray();

            //选中
            var obj_id = $('#AjaxListBox input:checkbox:checked');
            if(obj_id.length) $.merge(data,obj_id.serializeArray());

            //排序
            var obj_sort = $('#AjaxListBox input:text');
            if(obj_sort.length){
                var obj_sort_num = 0;
                $.each(obj_sort,function(i,n) {
                    if(obj_sort.eq(i).val() != obj_sort.eq(i).attr('value')){
                        $.merge(data,obj_sort.eq(i).serializeArray());
                    }
                });
            }

            //有效数据为空返回
            if(data.length < 2) return false;

            loading(1);
            var errorBox=function(content){
                dialog({
                    id: 'errorBox',
                    title: '错误提示',
                    quickClose: true,
                    content: content,
                    onshow: function (){loading(0);},
                }).show();
            };
            $.ajax({
                url:url,
                data:data,
                type:'POST',
                success:function(result){
                    if(result.status == undefined){
                        errorBox(result);
                        return false;
                    }
                    dialog({
                        id: 'AjaxBatchBox',
                        content: result.info,
                        quickClose: true,
                        onshow: function (){loading(0);},
                    }).show();
                    if(result.status){
                        setTimeout(function(){
                            fresh(result.url);
                        }, 1000);
                    }
                },
                error: function(x, t, e){
                    errorBox(x.responseText);
                }
            });
            return false;
        });
    };

    //带验证的validform
    exports.valid = function(name){
        if(name == undefined) name=".AjaxForm";
        if($(name).length){
            require.async('validform',function(){
                $(name).Validform({
                    beforeSubmit:function(curform){
                        var url = curform.data('action'),
                            data = curform.serialize();
                        loading(1);
                        var errorBox=function(content){
                            dialog({
                                id: 'errorBox',
                                title: '错误提示',
                                quickClose: true,
                                content: content,
                                onshow: function (){loading(0);},
                            }).show();
                        };
                        $.ajax({
                            type: "POST",
                            url: url,
                            data:data,
                            success: function (result) {
                                if(result.status == undefined){
                                    errorBox(result);
                                    return false;
                                }
                                dialog({
                                    id:'AjaxSubmitBox',
                                    title: '操作提示',
                                    content: result.info,
                                    quickClose: true,
                                    onshow: function (){loading(0);},
                                }).show();
                                if(result.status){  
                                    setTimeout(function(){
                                        dialog.get('AjaxSubmitBox').close().remove();
                                        fresh(result.url);
                                    }, 1000); 
                                }
                            },
                            error: function(x, t, e){
                                errorBox(x.responseText);
                            }
                        });
                        return false;
                    }
                });
            })
        }
    }

    //ajaxform
    exports.form = function(name){
        if(name == undefined) name=".AjaxForm";
        if($(name).length){
            $(name).submit(function(e) {
                var url = $(this).data('action'),
                    data = $(this).serialize();
                loading(1);
                var errorBox=function(content){
                    dialog({
                        id: 'errorBox',
                        title: '错误提示',
                        quickClose: true,
                        content: content,
                        onshow: function (){loading(0);},
                    }).show();
                };
                $.ajax({
                    type: "POST",
                    url: url,
                    data:data,
                    success: function (result) {
                        if(result.status == undefined){
                            errorBox(result);
                            return false;
                        }
                        dialog({
                            id:'AjaxSubmitBox',
                            title: '操作提示',
                            content: result.info,
                            quickClose: true,
                            onshow: function (){loading(0);},
                        }).show();
                        if(result.status){
                            setTimeout(function(){
                                dialog.get('AjaxSubmitBox').close().remove();
                                fresh(result.url);
                            }, 1000); 
                        }
                    },
                    error: function(x, t, e){
                        errorBox(x.responseText);
                    }
                });
                return false;
            });
        }
    }

    //ajax分页
    //配合Common/lib/page
    exports.page = function (list_table,page_box,page_a){
        if(page_box == undefined) page_box=".AjaxPage";
        if(page_a == undefined) page_a="a";
        if(list_table == undefined) list_table=".list-base table";

        $(page_box +' '+ page_a).click(function(e) {
            if($(this).hasClass('current')) return false;
            if($(this).hasClass('next')){
                var btn_all = $(page_box +' '+ page_a).length;
                var ben_now = $(page_box +' .current').html()*1;
                if( btn_all == ben_now+1) return false;
                $(page_box +' '+ page_a).eq(ben_now).addClass('current').siblings('.current').removeClass('current');;
            }else{
                $(this).addClass('current').siblings('.current').removeClass('current');
            }
            var url=$(this).data('url');
            loading(1);
            $.get(url, function(result){
                loading(0);
                $(list_table).html(result);
            });
        });
    };


});