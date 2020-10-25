/*! 批量选择删除*/
function selectall(whosform){
 for(var i=0;i<whosform.elements.length;i++){
    var box = whosform.elements[i];
   if (box.name != 'chkall')
    box.checked = whosform.chkall.checked;
 }
}
function switchall(whosform){
 for(var i=0;i<whosform.elements.length;i++){
    var box = whosform.elements[i];
   if (box.name != 'chkall')
    box.checked = !box.checked;
 }
 var chkall = document.getElementById("chkall");
 if (chkall.checked)
 chkall.checked = !chkall.checked;
}


//批量确认通用
function doConfirmBatch(url, str){
    //if 没有被选中的checkbox
    if (!getCheckboxNum()){
        alert('请选择项！');
        return false;
    }
    if (window.open(str)){
        var form_do = document.getElementById("form_do"); 
        form_do.action = url;
        form_do.submit(); 
    }
      

}

//同一个表单的值提交到不同的页面

function subform(i) { 

var theform=document.form_do; 
theform.action=i; 
theform.submit();
} 


