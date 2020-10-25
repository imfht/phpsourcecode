$(function(){
    app.pageInit();
});
var Cro = new Conero();
var app = Cro.extends(function(th){
    var _self = this;
    this.pageInit = function(){
        // 富文本
        tinymce.init({
            selector: '#textedit',
            plugins: [
                /*
                'advlist autolink lists link image charmap print preview anchor',
                'searchreplace visualblocks code fullscreen',
                'textcolor colorpicker fullscreen',
                'insertdatetime media table contextmenu paste code'
                */
                'advlist autolink link image lists charmap print preview hr anchor pagebreak spellchecker',
                'searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking',
                'save table contextmenu directionality emoticons template paste textcolor'
            ]
            
            ,toolbar: 'insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | preview fullpage | forecolor backcolor emoticons'
            // ,theme: 'modern'
            ,skin: 'lightgray'
        });
        th.panelToggle('.huijuan_panel');
        // 手动保存按钮点击
        $('#handler_svbtn').click(function(){
            var text = tinymce.get('textedit').getContent();
            text = $.trim(text);
            if(th.empty(text)){
                th.modal_alert('文本框内容不可为空！');
            }
            else _self.saveText(text);
        });
    }
    this.saveText = function(text){
        text = Base64.encode(text);
        var json = {
            item: 'text_save_req',
            uid: th.bsjson(th.getJsVar('uid')),
            text: text
        };
        $.post(th._baseurl + 'index/textedit/save.html',json,function(data){
            var tip = (data == 1)? '数据更新保存成功！':'很遗憾，数据保存失败了';
            th.modal_alert(tip);
        });
    }
});