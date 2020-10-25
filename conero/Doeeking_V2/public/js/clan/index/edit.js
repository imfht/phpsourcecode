$(function(){
    app.pageInit();
});
var Cro = new Conero();
var app = Cro.extends(function(th){    
    this.pageInit = function(){
        var mode = $('input[name="mode"]').val();
        if(mode == 'M'){
            $('.add_file').hide();
        }
        // 文件附件
        $('#add_file_ckbox').click(function(){
            var isSeted = $('#add_file_ckbox:checked').val();
            if(th.empty(isSeted)) $('.add_file').hide();
            else{
                $('.add_file').show();
                // $('#sfile_plus_btn').click();
            }
        });
        // 上传文件以后
        $('#sfile_plus_btn').change(function(){
            var filename = $(this).val();
            if(!th.empty(filename)){
                // th.log(filename);                
                $('#fname_ipter').val(filename);
            }
            else{
                $('#fname_ipter').val('');
            }
        });
        // 保存来自互联网的图片
        $('#get_img_4net_link').click(function(){
            var content = th.formGroup({
                param:[
                    {name:'url',label:'地址'},
                    {name:'name',label:'文件名称(默认为网络资源的名称)'},
                    {name:'remark',label:'备注'}
                ]
            });
            content += '<div class="alert"></div>';
            th.modal({
                title:'保存来自互联网的图片',
                content:content,
                save:function(){
                    var modal = $(this).parents('div.modal');
                    var body = modal.find('div.modal-body');
                    var data = th.formJson(body);
                    if(th.empty(data['url'])){
                        th.alert(body.find('div.alert'),'地址不可为空？');                        
                    }
                    else{
                        data['gen_no'] = $('input[name="gen_no"]').val();
                        th.post('/conero/clan/index/svnetimg.html',data);
                    }
                }
            });
        });
    }
});