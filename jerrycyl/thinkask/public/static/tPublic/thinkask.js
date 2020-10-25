//提示：模块也可以依赖其它模块，如：layui.define('layer', callback);
layui.define(['jquery','layer','flow'],function(exports){
	 var $=layui.jquery,layer = layui.layer; 
      var obj = {
        hello: function(str){
          alert('Hello '+ (str||'test'));
        }
        /**
         * [tajax ajax 提交 ]
         * demo: thinkask.tajax('/jblog/api/blog_detail/encry_id/'+$("#encry_id").val()).done(function(res){
		  if(res.status<1){
		    d = res.data;
		          $('.article-detail-title').text(d.title)
		           $('.creat_time').text('编辑时间：'+d.creat_date)
		          $('.author').text('作者:'+d.user_name)
		           $('.article-detail-content').html(d.message)
		   }
         });
         * @param  {[type]} url    [description]
         * @param  {[type]} params [description]
         * @param  {[type]} type   [description]
         * @return {[type]}        [description]
         */
        ,tajax:function(url,params,ajaxLoad,type,async){
            if(!type){type="json"}
        	if(!ajaxLoad){type="true"}
            if(!async){async=true}else{async=false} //是否异步加载
        	return $.ajax({
        		url: url,
        		 beforeSend:function(){
                    if(ajaxLoad=="true"){
		               var index = layer.load(0, {shade: false}); //0代表加载的风格，支持0-2
                    }
		         }, 
        		type: 'post',
        		async:async, 
        		dataType: type,
        		data: params,
                
        	})
        }
      
        ,getFormJson:function(frm){
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
        /**
         * [_getAttr 获得属性值]
         * @param  {[type]} name [description]
         * @return {[type]}      [description]
         */
       , _getAttr:function (thisclass,atrrName){
            return $(thisclass).attr(atrrName);
        }
        
        /**
         * [_getFormJson 获得表单数据]
         * @Author   Jerry
         * @DateTime 2017-04-30
         * @Example  eg:
         * @param    {[type]}   frm [description]
         * @return   {[type]}       [description]
         */
        ,_getFormJson:function(frm){
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
        ,alert:function(msg,ico){
            //解决ID下面NOT DEFINED 的问题
            if(!ico){
                 ico = 1
            }
            parent.layer.alert(
                    msg, {
                    icon: ico,
                    //skin: 'layer-ext-moon' //该皮肤由layer.seaning.com友情扩展。关于皮肤的扩展规则，去这里查阅
                })

        }
        ,frAlert:function(title,url,wd,hi){
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
        ,_showInfo:function(re){
            var alert = this.alert;
             if(re.status>0){
                     layer.close(layer.index)
                      alert(re.msg,2)
                   
                }else{
                     if(!re.data.url){
                            if(re.msg!=''){
                                layer.confirm(re.msg, {
                                    btn: ['确定'] //按钮
                                }, function(){
                                    window.location.reload();  
                                });
                            }else{
                                  window.location.reload();  
                            }
                        
                        return false;
                    }
                    if(re.data.url=="close-parent-reload"){
                           layer.confirm(re.msg, {
                                  btn: ['确定'] //按钮
                              }, function(){
                                var index = parent.layer.getFrameIndex(window.name); //获取窗口索引
                                 //关闭iframe
                                 parent.location.reload();
                                  parent.layer.close(index);
                                  
                              });
                       }else{
                        if(re.msg!=''){
                                 layer.confirm(re.msg, {
                                    btn: ['确定'] //按钮
                                }, function(){
                                    location.href = re.data.url;  
                                });
                            }else{
                                  location.href = re.data.url;    
                            }
                        
                       }
                }
        }
        ,_chlickStart:function(elements,btnmsg){
            if(!btnmsg){
                btnmsg = '处理中...';
            }
            $(elements).attr('btnText', $(elements).html());
            $(elements).attr('btnStatus', "off");
            $(elements).text(btnmsg);
            $(elements).css('cursor', 'wait');
        }
        ,_chlickEnd:function(elements){
            $(elements).html($(elements).attr('btnText'));
            $(elements).css('cursor', 'pointer');
            $(elements).attr('btnStatus', "on");
        }


      };
      
      //输出test接口
      exports('thinkask', obj);
});   

      