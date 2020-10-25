$(function(){
    app.pageInit();
});
var Cro = new Conero();
var app = Cro.extends(function(th){
    this.pageInit = function(){
        var $self = this;
        // 参数设置面板
        $('#set_post_data').click(function(){
            var url = $('#ipter_post_data').val();
            if(th.empty(url)){
                th.modal_alert('地址不可为空！');
                return;
            }
            var body = $('#set_post_panel').find('div.modal-body');
            body.find('span.url').text(url);
            $('#set_post_panel').modal();
        });
        // 参数设置面板 - 确认按钮
        $('#set_post_save').click(function(){
            var url = $('#ipter_post_data').val();            
            $('#set_post_panel').modal('hide');
            var body = $('#set_post_panel').find('div.modal-body');
            var data = th.formJson(body);
            data['url'] = url;            
            data.noContent = data.noContent? data.noContent:'Y';
            th.log(data);
            th.post('/conero/geek/apibug.html',data);
        });
        // 参数设置面板 - 请求类型判断
        $('#request_type_selector').change(function(){
            var type = $('#request_type_selector option:selected').val();
            var xhtml = '';
            if(type == 'post'){
                xhtml = '<p>POST 数据参数</p>'
                    + '<textarea class="form-control" name="post" placeholder="输入post数据"></textarea>';
            }
            $('#request_type_content').html(xhtml);
        });

        // 参数设置面板 - 设置请求值
        $('#spp_json_plus').click(function(){
            var spp_keys = $('#spp_keys').val();
            if(th.empty(spp_keys)){
                $('#spp_keys').focus();
                return;
            }
            var spp_values = $('#spp_values').val();
            if(th.empty(spp_values)){
                $('#spp_values').focus();
                return;
            }
            var textarea = $('#request_type_content').find('textarea');
            var json = {};
            if(textarea.length >0){
                var jsonStr = textarea.val();
                if(jsonStr){
                    try{
                        json = JSON.parse(jsonStr);
                    }catch(e){}
                }
            }
            else{
                app.requestType('post');
                textarea = $('#request_type_content').find('textarea');
            }
            var type = $('#request_type_selector option:selected').val();
            if(type != 'post'){
                $('#request_type_selector').find('option[value="post"]').attr("selected",true);
            }
            json[spp_keys] = spp_values;
            textarea.val(JSON.stringify(json));
            $('#spp_keys').val('');
            $('#spp_values').val('');
        });

        // query 切换为json 数据参数
        $('#query_exjson_btn').click(function(){
            var query = $('#query_exjson_ipter').val();
            if(query){
                var type = $('#request_type_selector option:selected').val();
                if(type != 'post'){
                    $('#request_type_selector').find('option[value="post"]').attr("selected",true);
                    app.requestType('post');
                }
                var json = th.queryBuild(query);                
                var textarea = $('#request_type_content').find('textarea');
                var jsonOld = textarea.val();
                if(jsonOld){
                    json = th.array_merge(json,jsonOld);
                }
                textarea.val(JSON.stringify(json));
            }
            else $('#query_exjson_ipter').focus();
        });
        // ajax 获取后端请求数据
        $('#query_exjson_4ajax').change(function(){
            var url = $(this).val();
            if(url){
                var type = $('#request_type_selector option:selected').val();
                if(type != 'post'){
                    $('#request_type_selector').find('option[value="post"]').attr("selected",true);
                    app.requestType('post');
                }
                var textarea = $('#request_type_content').find('textarea');  
                try {
                    $.get(url).done(function(json){                                            
                        textarea.val(JSON.stringify(json));
                    });
                } catch (error) {
                    Cro.log(error);
                    textarea.val(error);
                }
            }
        });
    }
    // 类型切换器
    this.requestType = function(type){
        type = type? type : $('#request_type_selector option:selected').val();
        var xhtml = '';
        if(type == 'post'){
            xhtml = '<p>POST 数据参数</p>'
                + '<textarea class="form-control" name="post" placeholder="输入post数据"></textarea>';
        }
        $('#request_type_content').html(xhtml);
    }
});