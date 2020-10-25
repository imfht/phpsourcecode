$(document).ready(function(){
    kineditorWork();
});   
//kineditor 在线编辑器
function kineditorWork(){
    var editor;
    KindEditor.ready(function(K) {
    editor = K.create('textarea[name="content"]', {
        themeType : 'default',
        resizeType: 1,
        height : "600px", //编辑器的高度为600px
        width:"100%",
        filterMode : false, //不会过滤HTML代码
        dialogAlignType:"page",
        items: [
            'fontname', 'fontsize', '|', 'forecolor', 'hilitecolor', 'bold', 'italic', 'underline',
            'removeformat', '|', 'justifyleft', 'justifycenter', 'justifyright', 'insertorderedlist',
            'insertunorderedlist', '|', 'emoticons', 'image', 'link'
        ]
        });
    }); 
}
