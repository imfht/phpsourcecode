/*
插入文字到光标处
insertText(document.getElementById('text'),' NewWord ')；
*/
function insertText(obj,str) {
    if (document.selection) {
        var sel = document.selection.createRange();
        sel.text = str;
    } else if (typeof obj.selectionStart === 'number' && typeof obj.selectionEnd === 'number') {
        var startPos = obj.selectionStart,
            endPos = obj.selectionEnd,
            cursorPos = startPos,
            tmpStr = obj.value;
        obj.value = tmpStr.substring(0, startPos) + str + tmpStr.substring(endPos, tmpStr.length);
        cursorPos += str.length;
        obj.selectionStart = obj.selectionEnd = cursorPos;
    } else {
        obj.value += str;
    }
}

/*
移动光标到尾部
moveEnd(document.getElementById('text'));
*/
function moveEnd(obj){
    obj.focus();
    var len = obj.value.length;
    if (document.selection) {
        var sel = obj.createTextRange();
        sel.moveStart('character',len);
        sel.collapse();
        sel.select();
    } else if (typeof obj.selectionStart == 'number' && typeof obj.selectionEnd == 'number') {
        obj.selectionStart = obj.selectionEnd = len;
    }
}

/*
<?php 
//从数据库读取的用户信息,需要注意的是，数组的键名应和表单域保持一致
$user = array(
  'id'=>1,
  'name'=>'张三',
  'area'=>'hexi',
  'sex'=>'male',
  'hobby'=>'music,movie',
  'intro'=>'你好，世界'
);

//将数组序列化为json字符串
$json = json_encode($user);
?>

//将PHP生成的json字符串赋值给js变量
var user = '<?php echo $json;?>';

$(function(){
  //将数据加载到表单中
  loadFormData(user);
});
*/
function loadFormData(jsonStr){
  var obj = eval("("+jsonStr+")");
  var key,value,tagName,type,arr;
  for(x in obj){
    key = x;
    value = obj[x];
    
    $("[name='"+key+"'],[name='"+key+"[]']").each(function(){
      tagName = $(this)[0].tagName;
      type = $(this).attr('type');
      if(tagName=='INPUT'){
        if(type=='radio'){
          $(this).attr('checked',$(this).val()==value);
        }else if(type=='checkbox'){
          arr = value.split(',');
          for(var i =0;i<arr.length;i++){
            if($(this).val()==arr[i]){
              $(this).attr('checked',true);
              break;
            }
          }
        }else{
          $(this).val(value);
        }
      }else if(tagName=='SELECT' || tagName=='TEXTAREA'){
        $(this).val(value);
      }
    });
  }
}

//获取指定form中的所有的<input>对象    
function getElements(formId) {    
    var form = document.getElementById(formId);    
    var elements = new Array();    
    var tagElements = form.getElementsByTagName('input');    
    for (var j = 0; j < tagElements.length; j++){  
         elements.push(tagElements[j]);  
  
    }  
    return elements;    
}   
  
//获取单个input中的【name,value】数组  
function inputSelector(element) {    
  if (element.checked)    
     return [element.name, element.value];    
}    
      
function input(element) {    
    switch (element.type.toLowerCase()) {    
      case 'submit':    
      case 'hidden':    
      case 'password':    
      case 'text':    
        return [element.name, element.value];    
      case 'checkbox':    
      case 'radio':    
        return inputSelector(element);    
    }    
    return false;    
}    
  
//组合URL  
function serializeElement(element) {    
    var method = element.tagName.toLowerCase();    
    var parameter = input(element);    
    
    if (parameter) {
      var key = encodeURIComponent(parameter[0]);    
      if (key.length == 0) return;    
    
      if (parameter[1].constructor != Array)    
        parameter[1] = [parameter[1]];    
          
      var values = parameter[1];    
      var results = [];    
      for (var i=0; i<values.length; i++) {    
        results.push(key + '=' + encodeURIComponent(values[i]));    
      }    
      return results.join('&');    
    }    
 }    
  
//调用方法     
function serializeForm(formId) {
    var elements = getElements(formId);    
    var queryComponents = new Array();    
    
    for (var i = 0; i < elements.length; i++) {    
      var queryComponent = serializeElement(elements[i]);    
      if (queryComponent)    
        queryComponents.push(queryComponent);    
    }    
    
    return queryComponents.join('&');  
}  