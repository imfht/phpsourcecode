$(function(){
    // 
    app.createTree();
    // 删除项目确认
    $('#del_prj_btn').click(function(){
        var url = $(this).attr('dataid');
        Cro.confirm('您确定要删除该项目吗，删除后数据将不可恢复？',function(){
            location.href = url;
        });
    });
    // 面板toggle
    Cro.panelToggle(['.panel-toggle-link']);
});
var Cro = new Conero();
var app = Cro.extends(function(th){
    // 生成 项目树形图
    this.createTree = function(){
        var ptree = th.getJsVar('ptree');
        var uLogin = th.getJsVar('uLogin');
        //th.log(ptree);
        if(ptree && th.objectLength(ptree) > 0){
            var option = {
                'core' : {
                    'data' : ptree
                }                
            };
            var evts = function (e, data) {
                if(data.selected.length) {
                    // alert('The selected node is: ' + data.instance.get_node(data.selected[0]).text);
                    //th.log(data.instance.get_node(data.selected[0]));
                    var no = data.instance.get_node(data.selected[0]).a_attr.no;
                    var text = data.instance.get_node(data.selected[0]).text;
                    // th.log(data.instance.get_node(data.selected[0]).text,data.instance.get_node(data.selected[0]).a_attr.no);
                    console.log(data.instance.get_node(data.selected[0]).text,data.instance.get_node(data.selected[0]).a_attr.no);
                    var xhtml = '';
                    var url = '/conero/geek/protree.html?no='+th.bsjson({no:no});
                    xhtml = '<a href="'+url+'" target="_blank"><h4>'+text+'</h4></a>'+
                            '<div class="embed-responsive embed-responsive-16by9">'+
                            '<iframe class="embed-responsive-item" src="'+url+'"></iframe>'+
                            '</div>'
                            ;                     
                    $('#prjoect_tree_detail').html(xhtml);
                }
            };
            if(uLogin == 'Y'){
                option.plugins = [
                    "contextmenu", "dnd", "search",
                    "state", "types", "wholerow"
                ];          
                option.contextmenu = {
                    items:{
                        "newitem":{
                            "separator_before"	: false,
                            "separator_after"	: true,
                            "_disabled"			: false, 
                            "label"				: "新建项",
                            "action"			: function (data) {
                                var inst = $.jstree.reference(data.reference),
                                    obj = inst.get_node(data.reference);                                                      
                                var no = obj.a_attr.no;
                                var href = '/conero/geek/protree/edit.html?code='+th.getQuery('code')+'&uid='+th.bsjson({pnode:no,mode:'A'});
                                location.href = href;                           
                            }
                        },
                        "newlogs":{
                            "separator_before"	: false,
                            "separator_after"	: true,
                            "_disabled"			: false, 
                            "label"				: "新建日志",
                            "action"			: function (data) {
                                var inst = $.jstree.reference(data.reference),
                                    obj = inst.get_node(data.reference);                                                      
                                var no = obj.a_attr.no;
                                var href = '/conero/geek/prologs/edit.html?code='+th.getQuery('code')+'&uid='+th.bsjson({pnode:no,mode:'A'});
                                location.href = href;                           
                            }
                        },
                        "delitem":{
                            "separator_before"	: false,
                            "separator_after"	: true,
                            "_disabled"			: false, //(this.check("create_node", data.reference, {}, "last")),
                            "label"				: "删除项",
                            "action"			: function (data) {
                                var inst = $.jstree.reference(data.reference),
                                    obj = inst.get_node(data.reference);
                                inst.create_node(obj, {}, "last", function (new_node) {
                                    setTimeout(function () { inst.edit(new_node); },0);
                                });
                            }
                        }
                    }
                };     
                $('#prjoect_tree').on("changed.jstree", evts).jstree(option);
            }
            else $('#prjoect_tree').on("changed.jstree",evts).jstree(option);
        }
        else{
            var xhtml = '<div class="alert alert-info" role="alert">'+
                            '<strong>Oh, No!</strong> 该项目还没有未创建项目树!'+
                            ((uLogin == 'Y')? '<a href="/conero/geek/protree/edit.html?code='+th.getQuery('code')+'" class="btn btn-info">新增</a>':'')+
                        '</div>';
            $('#prjoect_tree').html(xhtml);
        }
    }
});