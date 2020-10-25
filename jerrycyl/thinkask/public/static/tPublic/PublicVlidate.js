/* 
* @Author: Jerry
* @Date:   2015-08-28 14:35:08
* @Last Modified by:   Administrator
* @Last Modified time: 2017-01-06 17:09:24
* @公共JS
*/
/**
 * [insertAtCursor 插入到字符到光标处]
 * <input type=button onclick=insertAtCursor(document.getElementById('demo'),"插入语句") value=插入 > 
 * @param  {[type]} myField [description]
 * @param  {[type]} myValue [description]
 * @return {[type]}         [description]
 */
function insertAtCursor(myField, myValue)
{
    //IE support
    if (document.selection)
    {
    myField.focus();
    sel = document.selection.createRange();
    sel.text = myValue;
    sel.select();
    }
    //MOZILLA/NETSCAPE support
    else if (myField.selectionStart || myField.selectionStart == '0')
    {
    var startPos = myField.selectionStart;
    var endPos = myField.selectionEnd;
    // save scrollTop before insert
    var restoreTop = myField.scrollTop;
    myField.value = myField.value.substring(0, startPos) + myValue + myField.value.substring(endPos,myField.value.length);
    if (restoreTop > 0)
    {
    // restore previous scrollTop
    myField.scrollTop = restoreTop;
    }
    myField.focus();
    myField.selectionStart = startPos + myValue.length;
    myField.selectionEnd = startPos + myValue.length;
    } else {
    myField.value += myValue;
    myField.focus();
    }
}
// ==========================================================================================
//将form中的值转换为键值对。
//获得FORM里面的所有值 ;
function getFormJson(frm) {
    var o = {};
    var a = $(frm).serializeArray();
    $.each(a, function () {
        if (o[this.name] !== undefined) {
            if (!o[this.name].push) {
                o[this.name] = [o[this.name]];
            }
            o[this.name].push(this.value || '');
        } else {
            o[this.name] = this.value || '';
        }
    });
 
    return o;
}

// 只能輸入數字，且第一數字不能為0
function digitalOnly(obj) {
 	// 先把非数字的都替换掉
 	obj.value=obj.value.replace(/\D/g, "");
 	// 必须保证第一个为数字
 	//obj.value = obj.value.replace(/^0/g, "");
}

 /******************** 
 * 取窗口滚动条高度  
 ******************/  
function getScrollTop()  
 {  
     var scrollTop=0;  
     if(document.documentElement&&document.documentElement.scrollTop)  
     {  
         scrollTop=document.documentElement.scrollTop;  
     }  
     else if(document.body)  
     {  
         scrollTop=document.body.scrollTop;  
     }  
     return scrollTop;  
 }  
   


 //只能輸入數字
function isNumberKey(evt){
 	var charCode = (evt.which) ? evt.which : event.keyCode;
 	if (charCode > 31 && (charCode < 48 || charCode > 57)){
 		return false;
 	}else{		
 		return true;
 	}
 }  

 //只能輸入數字和小數點
function isNumberdoteKey(evt){
 	var e = evt || window.event; 
 	var srcElement = e.srcElement || e.target;
 	
 	var charCode = (evt.which) ? evt.which : event.keyCode;			
 	if (charCode > 31 && ((charCode < 48 || charCode > 57) && charCode!=46)){
 		return false;
 	}else{
 		if(charCode==46){
 			var s = srcElement.value;			
 			if(s.length==0 || s.indexOf(".")!=-1){
 				return false;
 			}			
 		}		
 		return true;
 	}
 }

 //只能輸入數字和字母
function isNumberCharKey(evt){
 	var e = evt || window.event; 
 	var srcElement = e.srcElement || e.target;	
 	var charCode = (evt.which) ? evt.which : event.keyCode;

 	if((charCode>=48 && charCode<=57) || (charCode>=65 && charCode<=90) || (charCode>=97 && charCode<=122) || charCode==8 || charCode ==46){
 		return true;
 	}else{		
 		return false;
 	}
 }

 function isChinese(obj,isReplace){
 	var pattern = /[\u4E00-\u9FA5]|[\uFE30-\uFFA0]/i
 	if(pattern.test(obj.value)){
 		if(isReplace)obj.value=obj.value.replace(/[\u4E00-\u9FA5]|[\uFE30-\uFFA0]/ig,"");
 		return true;
 	}
 	return false;
 }   
 

function isEmail(v){
		var tel = new RegExp("^\\w+((-\\w+)|(\\.\\w+))*\\@[A-Za-z0-9]+((\\.|-)[A-Za-z0-9]+)*\\.[A-Za-z0-9]+$");
		return(tel.test(v));
}   
//判断是否电话
function isTel(v){
	 var tel = new RegExp("^[[0-9]{3}-|\[0-9]{4}-]?(\[0-9]{8}|[0-9]{7})?$");
	 return(tel.test(v));
}
function isPhone(v){
	 var tel = new RegExp("^[1][0-9]{10}$");
	 return(tel.test(v));
}
//判断url
function isUrl(str){
    if(str==null||str=="") return false;
    var result=str.match(/^http:\/\/[A-Za-z0-9]+\.[A-Za-z0-9]+[\/=\?%\-&_~`@[\]\’:+!]*([^<>\"])*$/);
    if(result==null)return false;
    return true;
}
//比较时间差
function getTimeDiff(startTime,endTime,diffType){
    //将xxxx-xx-xx的时间格式，转换为 xxxx/xx/xx的格式
    startTime = startTime.replace(/-/g, "/");
    endTime = endTime.replace(/-/g, "/");
    //将计算间隔类性字符转换为小写
    diffType = diffType.toLowerCase();
    var sTime = new Date(startTime); //开始时间
    var eTime = new Date(endTime); //结束时间
    //作为除数的数字
    var divNum = 1;
    switch (diffType) {
        case "second":
             divNum = 1000;
             break;
        case "minute":
             divNum = 1000 * 60;
             break;
        case "hour":
             divNum = 1000 * 3600;
             break;
        case "day":
             divNum = 1000 * 3600 * 24;
             break;
        default:
             break;
     }
     return parseInt((eTime.getTime() - sTime.getTime()) / parseInt(divNum));
}

/***
 * 获取字节数
 * @param str
 * @returns
 */
function getBytes(str) {
	var cArr = str.match(/[^\x00-\xff]/ig);
	return str.length + (cArr == null ? 0 : cArr.length);
};

/***
 * 判断最小字节数
 * @param o
 * @param minLength
 * @returns
 */
function checkMinLength(o,minLength){
	if(CMZ.getBytes(o)<=minLength){
		return false;
	}
	return true;
}
/***
 * 判断最大字节数
 * @param o
 * @param maxLength
 * @returns
 */
function checkMaxLength(o,maxLength){
	if(CMZ.getBytes(o)>maxLength){
		return false;
	}
	return true;
}




/**
 * 替换url
 */
function replaceURL(url,ar){
	if(ar instanceof Array){
		for(var i=0;i<ar.length;i++){
			url = url.replace('__'+i,ar[i]);
		}
		return url;
	}else{
		return url.replace('__0',ar);
	}
}
/**
 * [cmzAlert 封装LAYER的方法]
 * @return {[type]} [description]
 */
function cmzAlert(msg,ico){
    //解决ID下面NOT DEFINED 的问题
    if(!ico){
         ico = 1
    }
	parent.layer.alert(
			msg, {
		    icon: ico,
		    skin: 'layer-ext-moon' //该皮肤由layer.seaning.com友情扩展。关于皮肤的扩展规则，去这里查阅
		})
}
/**
 * [cmzChangeStatus封装 适应于更改值1：或者0的方法时使用]
 * @param  {[type]} btn [类无素，也就是按纽]
 * @return {[type]}     [description]
 * @return {[from]}     [原值 ]
 * 
 */
function cmzChangeStatus(btn,from,fieldName,statusname,statusValue){
     $('.'+btn).click(function() {

        var table = _getAttr($(this),'table')
        var where = _getAttr($(this),'where')
        var from = _getAttr($(this),'from')
        var to = _getAttr($(this),'to')
        // var status = _getAttr($(this),'status')
        status = (status==1)?0:1;
         var o = {};
       o['table'] = table;
       o['where'] = where;
       o['from'] = from;
       o['to'] = to;
       cmzAjax('/Ajax/Status/changeStatus',"json",o);
     });
}

/**
 * [cmzEdit description]
 * @param  {[type]} btn       [类无素，也就是按纽]
 * @param  {[type]} form      [表单名，数据为此表单里面的数据]
 * @param  {[type]} fieldName [description]
 * @return {[type]}           [description]
 */
function cmzEdit(btn,form){
     $('.'+btn).click(function() {

        var table = _getAttr($(this),'table')
        var where = _getAttr($(this),'where')
        var returnurl = _getAttr($(this),'returnurl')
        var o = getFormJson('.'+form);
        o['table'] = table;
        o['where'] = where;
        o['returnurl'] = returnurl;
       cmzAjax('/Ajax/Db/edit',"json",o);
     });
}
/**
 * [cmzAdd description]
 * @param  {[type]} btn       [类无素，也就是按纽]
 * @param  {[type]} form      [表单名，数据为此表单里面的数据]
 * @param  {[type]} fieldName [description]
 * @return {[type]}           [description]
 */
function cmzAdd(btn,form){
     $('.'+btn).click(function() {

        var table = _getAttr($(this),'table')
        var where = _getAttr($(this),'where')
        var o = getFormJson('.'+form);
        o['table'] = table;
        o['where'] = where;
        // cmzAlert(o);
       cmzAjax('/Ajax/Db/add',"json",o);
     });

}
/**
 * [cmzDel description]
 * @param  {[type]} btn       [类无素，也就是按纽]
 * @param  {[type]} form      [表单名，数据为此表单里面的数据]
 * @param  {[type]} fieldName [description]
 * @return {[type]}           [description]
 */
function cmzDel(btn){
     $('.'+btn).click(function() {
         layer.confirm('您确定此操作行为吗？', {
                        btn: ['确定'] //按钮
                    }, function(){
                         var table = _getAttr($(this),'table')
                            var where = _getAttr($(this),'where')
                            var o = {};
                            o['table'] = table;
                            o['where'] = where;
                            // cmzAlert(o);
                           cmzAjax('/Ajax/Db/delete',"json",o);
                    },function(){

                        return false;
                    });
       
     });

}
/**
 * [cmzAddMode 模型进的添加]
 * @param  {[type]} btn       [类无素，也就是按纽]
 * @param  {[type]} form      [表单名，数据为此表单里面的数据]
 * @param  {[type]} fieldName [description]
 * @return {[type]}           [description]
 */
function cmzAddModel(btn,form){
     $('.'+btn).click(function() {

        var table = _getAttr($(this),'table')
        var where = _getAttr($(this),'where')
        var o = getFormJson('.'+form);
        o['table'] = table;
       _cmzAjaxModel('/Ajax/Db/addModel',"json",o);
     });

}
/**
 * [cmzAjax description]
 * @param  {[type]} url      [请求的URL]
 * @param  {[type]} dataType [数据返出的方式]
 * @param  {[type]} data     [JSON数据]
 * @return {[type]}          [description]
 */
function _cmzAjaxModel(url,dataType,data){
    if(!dataType){
        dataType = "html";
    }
    $.ajax({
        url: url,
        type: 'post',
        dataType: dataType,
        data: data,
        success:function(d){
            if(d.code==1){
                if(d.url==""){
                    layer.confirm(d.msg, {
                        btn: ['确定'] //按钮
                    }, function(){
                        location.reload();  
                    });
                   
               }else{
                  layer.confirm(d.msg, {
                        btn: ['确定'] //按钮
                    }, function(){
                        location.href = d.url; 
                    });
                    
               }
               
           }else{
            cmzAlert(d.msg,2)
           }
            
        }
    })
}
/**
 * [_getAttr 获得属性值]
 * @param  {[type]} name [description]
 * @return {[type]}      [description]
 */
function _getAttr(thisclass,atrrName){
    return $(thisclass).attr(atrrName);
}
/**
 * [cmzAjaxMobil 手机AJAX请求]
 * @param  {[type]} url      [请求的URL]
 * @param  {[type]} dataType [数据返出的方式]
 * @param  {[type]} data     [JSON数据]
 * @return {[type]}          [description]
 */
function cmzAjaxMobil(url,dataType,data){
  if(!dataType){
        dataType = "html";
    }
    $.ajax({
        url: url,
        type: 'post',
        dataType: dataType,
        data: data,
       beforeSend:function(){
         //询问框
          //提示
              layer.open({
                content: '数据请求中...'
                ,skin: 'msg'
                ,time: 2 //2秒后自动关闭
              });
        //加载层
       // var index = layer.load(0, {shade: false}); //0代表加载的风格，支持0-2
       },
        success:function(d){
            if(d.code==1){
                if(d.url==""){
                   
                     layer.open({
                    content: 'd.msg'
                    ,skin: 'msg'
                    ,time: 2 //2秒后自动关闭
                  });
                  
               }else{
                  layer.open({
                    content: 'd.msg'
                    ,skin: 'msg'
                    ,time: 2 //2秒后自动关闭
                  });
               }
              
               
           }else{
            var index = layer.load(0, {shade: false,time: 1000}); //0代表加载的风格，支持0-2
            cmzAlert(d.msg,2)
           }
            
        }
    })   
}
/**
 * [cmzAjax description]
 * @param  {[type]} url      [请求的URL]
 * @param  {[type]} dataType [数据返出的方式]
 * @param  {[type]} data     [JSON数据]
 * @return {[type]}          [description]
 */
function cmzAjax(url,dataType,data){
    if(!dataType){
        dataType = "html";
    }
    $.ajax({
        url: url,
        type: 'post',
        dataType: dataType,
        data: data,
       beforeSend:function(){
        //加载层
        var index = layer.load(0, {shade: false}); //0代表加载的风格，支持0-2
       },
        success:function(d){
            if(d.code==1){
                if(d.url==""){
                    layer.confirm(d.msg, {
                        btn: ['确定'] //按钮
                    }, function(){
                        location.reload();  
                    });
                  
               }
               if(d.url=="close-parent-reload"){
                   layer.confirm(d.msg, {
                          btn: ['确定'] //按钮
                      }, function(){
                        var index = parent.layer.getFrameIndex(window.name); //获取窗口索引
                         //关闭iframe
                         parent.location.reload();
                          parent.layer.close(index);
                          
                      });

               }else{
                layer.confirm(d.msg, {
                        btn: ['确定'] //按钮
                    }, function(){
                        location.href = d.url;  
                    });
                    
               }
               
           }else{
            var index = layer.load(0, {shade: false,time: 1000}); //0代表加载的风格，支持0-2
            cmzAlert(d.msg,2)
           }
            
        }
    })
}
$(function(){

$('.cmzBtn').click(function(event) {
    // alert('sdfa')
    cmzPost('cmzPost');
});

    
})

function cmzPost(frm){
    //获得表中所有表单值
    var data = getFormJson('.'+frm);
    var url = $('.'+frm).attr('action');
     $.ajax({
        url: url,
        type: 'post',
        dataType: 'json',
        data: data,
        success:function(d){
            if(d.code==1){
                if(d.url==""){
                    layer.confirm(d.msg, {
                        btn: ['确定'] //按钮
                    }, function(){
                          location.reload();
                    });
                  
               }else{
                layer.confirm(d.msg, {
                        btn: ['确定'] //按钮
                    }, function(){
                         parent.location.href = d.url;
                    });
               }
           }else{
            cmzAlert(d.msg,2)
           }
            
        }
    })


}
//AJAX提交
//刷新得到数据
/**
 * [J_ajax_admin_recipients description]
 * @param {[type]} btn        [点击的按钮]
 * @param {[type]} area       [刷新的区域]
 * @param {[type]} requestUrl [请求的地址]
 */
function J_ajax_admin_recipients(btn,area) {
    
    $(btn).click(function(event) {

        var jsondb = $(this).attr('jsondb');
        $.ajax({
        url: '/Admin/Recipients/ajax_getinfo',
        data: {'jsondb':jsondb},
        type: 'POST',
        dataType: 'json',
            success: function (d) {
              $('#'+jsondb+'_area').html(d);
                   

                
            }
        })
    return false;
    });
  
   
}


/**
 * [checkEmail description]
 * @param {[type]} btn        [点击的按钮]
 * @param {[type]} area       [刷新的区域]
 * @param {[type]} requestUrl [请求的地址]
 */
function checkEmail(userEmail){
    url = "/Ajax/Ucent/checkEmail"
    //获得表中所有表单值
     $.ajax({
        url: url,
        type: 'post',
        dataType: 'json',
        data: {'userEmail':userEmail},
        success:function(d){
            if(d.code!=1){
                cmzAlert(d.msg,2)
               return '0000';
           }else{
                 return '0000';
           }
            
        }
    })


}
/**
 * [frAlert 新页面弹窗]
 * @param  {[type]} title [标题]
 * @param  {[type]} url   [新页面的地址]
 * @param  {[type]} wd    [宽度]
 * @param  {[type]} hi    [高度]
 * @return {[type]}       [description]
 */
 function frAlert(title,url,wd,hi){
      if(!wd){
        wd ="80%";
      }
      if(!hi){
        hi="70%";
      }
        layer.open({
        type: 2,
        title: title,
        // btn: ['<i class="fa fa-refresh mr10"></i>刷新'],
        //  yes: function(index, layero){
        //     //按钮【按钮一】的回调
        //   },
        shadeClose: true,
        shade: 0.8,
        maxmin: true, //开启最大化最小化按钮
        area: [wd, hi],
        content: url //iframe的url
    }); 

}
function checkUser(userEmail){
    url = "/Ajax/Ucent/checkEmail"
    //获得表中所有表单值
     $.ajax({
        url: url,
        type: 'post',
        dataType: 'json',
        data: {'userEmail':userEmail},
        success:function(d){
            if(d.code!=1){
                cmzAlert(d.msg,2)
               return ('false');
           }else{
            return ('true');
           }
            
        }
    })


}



/**
 * [prOpj 打印对象文件]
 * @return {[type]} [description]
 */
function prOpj(e){
     var description = ""; 
         for(var i in e.delegateTarget ){ 
          var property=e.delegateTarget[i]; 
          description+=i+" = "+property+"\n"; 
         } 
         alert(description); 
}

/**
 * [url url的跳转方式]
 * @param  {[type]} $type [description]
 * @return {[type]}       [description]
 */
function url(type){
   switch(type)
  {
  case 1:
    // 执行代码块 1
    break;
  case 2:
    // 执行代码块 2
    break;
  default:
    // n 与 case 1 和 case 2 不同时执行的代码
  }
}
//================================================input===================================
/**
 * 全选
 * @param element
 */
function select_all(element) {
    $(element).find($("[type='checkbox']")).attr("checked", "checked");
}

/**
 * 反选
 * @param element
 */
function reverse_select(element) {
    $(element).find($("[type='checkbox']")).attr("checked", function () {
        return !$(this).attr("checked") == 1
    });
}
//================================================input===================================
//================================================ico版本===================================
/**
 * 全选
 * [ico_select_all description]
 * @return {[type]} [description]
 * 未选择状态<i class="fa fa-square-o checkbox" style="cursor:pointer;"></i>
 * 已选择状态<i class="fa fa-check-square-o checkbox" style="cursor:pointer;"></i>
 */
function ico_select_all(element){
    $(element).find($("[class='checkbox']")).attr("checked", "checked");
    $(element).removeClass('fa-square-o').addClass('fa-check-square-o');
}
/**
 * 反选
 * [ico_reverse_select description]
 * @return {[type]} [description]
 */
function ico_reverse_select(element){
       $(element).find($("[class='checkbox']")).removeAttr('checked') 
        $(element).removeClass('fa-check-square-o').addClass('fa-square-o');
      
}
/**
 * [changeFrameHeight 获取IFRAME的高度]
 * @return {[type]} [description]
 */
 function autoIframeHeight(element){
        var ifm= document.getElementById(element); 
        var ifh = document.documentElement.clientHeight;
        ifh = ifh>900?ifh:900;
        ifm.height=ifh
}
