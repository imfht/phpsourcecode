// 首页 app生成器
var app = {
    /**
     * 任务栏生成器
     * 1. dom => element opt => title
     * 2. dom => string opt => {url,title}
     */
    task: function(dom,opt){
        var url,dataid,id,title;
        if(typeof dom == 'string' && typeof opt != 'object'){
            var element = $('.win').find('a[dataid="'+dom+'"]');
            if(element.length>0){
                url = element.attr("dataurl");
                dataid = dom;
                title = element.text();
            }
        }else if(typeof dom == 'string' && typeof opt == 'object'){
            dataid = dom;
            url = opt['url'];
            title = opt['title'];
        }else{
            title = opt;
            url = dom.attr('dataurl');
            dataid = this.dataid(dom);            
            //var taskBtn = 'link_'+dataid;
        }
        // 过滤 undefind 等无效数据
        if(Cro.undefind(title) || Cro.undefind(dataid)) return;
        id = 'cro_page_'+dataid;
        app.close(id,'OPEN');
        // 页面存在时关闭
        if($('#'+id).length > 0 ) return ;
        var content = '';
        var iframeId = 'app_'+dataid;
        this.navDb.array('_task',dataid);
        // 重新更新选中状态
        $('#task_bar').find('a.task_active').removeClass('task_active');
        if(url) content = '<iframe src="'+url+'" id="'+iframeId+'"></iframe>';
        $('#task_bar').append('<a href="javascript:void(0);" class="task_btn task_active" dataid="'+dataid+'"><span>'+title+'</span></a>');

        var iframeHtml = '<div class="app_page" id="'+id+'">'
                +  '<div class="header">'
                +  '<h4>'+title+'</h4>'
                +  '<p>'                    
                    +  '<a href="javascript:void(0);" onClick="app.close(this,\'CLOSE\')">最小化</a>'
                    +  '<a href="javascript:void(0);" onClick="app.maxWin(this)">最大化</a>'
                    +  '<a href="javascript:void(0);" onClick="app.close(this)">关闭</a>'
                    +  '<a href="javascript:void(0);" onClick="app.flush(this)">刷新</a>'
                +  '</p></div>'
                +  content+'</div>'
        ;
        $('.conero').append(iframeHtml);
        //Cro.uWin(iframeId).post('bind_request');
        this.app_trage(id);
    },
    //	浮动框可拖动
	app_trage: function(id){
        id = id.indexOf('#')>-1? id : "#"+id;
        var page = $(id);
		 // 浮动框拖动设置
		var move = false;
		var _x,_y;
		//mousemove 鼠标移到dom上
		page.mousedown(function(e){
			move = true;
			_x=e.pageX-parseInt(page.css("left"));
			_y=e.pageY-parseInt(page.css("top")); 
			//$.log(_x+':'+_y);
		});
		$(document).mousemove(function(e){
			if(move){
				var x=e.pageX-_x;//控件左上角到屏幕左上角的相对位置
				var y=e.pageY-_y;
				page.css({"top":y,"left":x});
				page.css({'cursor':'move'});//pointer 手型，
				//$('html').css({'cursor':'pointer'});//pointer 手型，move 移动型
			}
		}).mouseup(function(){
			move=false;
		//$('html').css({'cursor':'auto'});//pointer 手型，
		}); 
	},
    // 任务栏相关操作
    task_bar: function(type,value){
        if(type && Cro.is_string(type)) type = type.toLowerCase();
        var ret;
        switch(type){
            case 'reset':// 重置任务栏
                $('#task_bar').find('a.task_active').removeClass('task_active');
                if(Cro.is_string(value) && value){
                    $('#task_bar').find('a[dataid="'+value+'"]').addClass('task_active');
                }
                $ret = true;
                break;
            case 'length':// 长度
                ret = $('#task_bar').find('a.task_btn').length;
                break;
            default:// 返回当前的激活状态下的任务栏按钮
                ret = $('#task_bar').find('a.task_active').attr("dataid");
        }
        return ret;
    },
    // app 刷新自动恢复应用状态
    recover: function(){
        var tasks = this.navDb.array('_task');
        var opt;
        if(tasks){
            for(var i=0; i<tasks.length; i++){
                this.task(tasks[i]);
            }
        }
    },
    // app 关闭以及打开 th=> string/element; type=> CLOSE/OPEN/删除
    close: function(th,type){
        var id;
        if(typeof(th) == 'string'){
            id = th;
        }else{
            var dom = $(th);
            id = dom.parents('.app_page').attr('id');
        }
        var dataid = id.replace('cro_page_','');
        if('CLOSE' == type){
            $('#'+id).hide();
            return;
        }
        else if('OPEN' == type){
            $('#'+id).show();
            return;
        }
        $('#'+id).remove();
        $('#task_bar').find('[dataid="'+dataid+'"]').remove();
        this.navDb.removeArray('_task',dataid);
    },
    // 窗口最大化
    maxWin: function(dom){
        var th = $(dom);
        id = th.parents('.app_page').attr('id');
        var page = $('#'+id);
        page.css({'left':'10px','right':'5px','top':'5px','bottom':'20px'});
        th.attr("onclick",'app.resetWin(this)');
        th.text('还原');
        /*
        var heigth = document.documentElement.clientHeight;
        var width = document.documentElement.clientWidth;
        page.css({"heigth":heigth,"width":width});
        Cro.log(page);
        */
    },
    // 窗口还原
    resetWin: function(dom){
        var th = $(dom);
        id = th.parents('.app_page').attr('id');
        var page = $('#'+id);
        page.css({'top':'20px','right':'200px','bottom':'20px','left':'100px','padding':'0px'});
        th.attr("onclick",'app.maxWin(this)');
        th.text('最大化');
    },
    // 刷新窗口
    flush: function(dom){
        var th = $(dom);
        var win = th.parents('.app_page').find('iframe');
        var url = win.attr('src');
        win.attr('src',url);
    },
    // 获取数据ID值
    dataid: function(dom,v){
        if(v){
            dom.attr('dataid',v);
            return true;
        }
        return dom.attr('dataid');
    },
    // app 高度自控撑高
    autoWin: function(){
        var height = document.documentElement.clientHeight - 10;
        $('.conero .win').css({"height":height});
    }
};