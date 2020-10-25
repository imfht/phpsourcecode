@extends('layouts.app')
<script language="javascript" type="text/javascript"> 
	// 设置cookie
	function setCookie(c_name,value,expiredays)
	{
		var exdate=new Date()
		exdate.setDate(exdate.getDate()+expiredays)
		document.cookie=c_name+ "=" +escape(value)+((expiredays==null) ? "" : ";expires="+exdate.toGMTString())
	}

	// 获取cookie
	function getCookie(c_name) {
		if(document.cookie.length > 0) {
			c_start = document.cookie.indexOf(c_name + "=");
			if(c_start != -1) {
				c_start = c_start + c_name.length + 1;
				c_end = document.cookie.indexOf(";", c_start);
				if(c_end == -1) c_end = document.cookie.length;
				return decodeURI(document.cookie.substring(c_start, c_end));
			}
		}
		return "";
	}

	//定时器
	var timer;

	//待办列表模式
	var mode=1;

	//间隔 1S
	var interval = 1000; 
	//剩余时间
	var remain = {{ $current_pomo_remain }};
	//当前状态
	var status = {{ $current_pomo_status }};

	var title = '蒙太奇 - 但行好事，用心生活';

	//日期格式化工具
	Date.prototype.format = function (fmt) {
	  var o = {
	      "M+": this.getMonth() + 1, //月份
	      "d+": this.getDate(), //日
	      "h+": this.getHours(), //小时
	      "m+": this.getMinutes(), //分
	      "s+": this.getSeconds(), //秒
	      "q+": Math.floor((this.getMonth() + 3) / 3), //季度
	      "S": this.getMilliseconds() //毫秒
	  };
	  if (/(y+)/.test(fmt)) {
	    fmt = fmt.replace(RegExp.$1, (this.getFullYear() + "").substr(4 - RegExp.$1.length));
	  }
	  for (var k in o) {
	    if (new RegExp("(" + k + ")").test(fmt)) {
	      fmt = fmt.replace(RegExp.$1, (RegExp.$1.length == 1) ?
	        (o[k]) : (("00" + o[k]).substr(("" + o[k]).length)));
	    }
	  }
	  return fmt;
	}
	
	//尝试获取通知权限
	document.addEventListener('DOMContentLoaded', function () {
	  if (!Notification) {
	    alert('Desktop notifications not available in your browser. Try Chromium.'); 
	    return;
	  }

	  if (Notification.permission !== "granted"){
	    Notification.requestPermission();
	  }
	});

	//通知消息
	function notify(message)
	{
		if (Notification.permission !== "granted")
		    Notification.requestPermission();
		  else {
		    var notification = new Notification(title, {
		      icon: '{{'/favicon.ico'}}',
		      body: message,
		    });

		    notification.onclick = function () {
		      location.href="{{'/index'}}";      
		    };
		  }
	}

	//倒计时
	function ShowCountDown(leftsecond, pomoBtnId) 
	{ 
		var minute=Math.floor(leftsecond/60); 
		if(minute<0){
			minute = 0;
		}
		
		var second=Math.floor(leftsecond - minute * 60); 
		if(second<0){
			second = 0;
		}
		
		if(remain <= 0){
			clearInterval(timer);
			remain = 0;
			if(status == 2){
				var message = '您已经完成了一个番茄，快来记录一下吧~';
				document.getElementById('pomoBtn').style.display = "none";
				document.getElementById('recordPomo').style.display = "";
				document.title = message + title;
				pomonotify(message);
				return false;
			} else if(status == 4){
				var message = '休息完成，快来开始下一个番茄吧~';
				document.title = message + title;
				pomonotify(message);
				location.href = '{{url('/index')}}';
				return false;
			} else {
				location.href = '{{url('/index')}}';
				return false;
			}
		} else {
			remain = remain - 1;
		}

		var add_content = (status == 2?'#此番茄还剩#':'#休息还剩#')+((minute >= 10)?minute:"0"+minute) +":"+ ((second >= 10)?second:"0"+second);
		
		document.title = add_content + title;
		document.getElementById(pomoBtnId).innerHTML = add_content; 
	}

	//放弃番茄 or 放弃休息
	function discard(){
		if (confirm("确认要放弃咩？")) {
			location.href = '{{ url("pomos/discard/") }}/'+$('#pomo_id').val();
		}
	}

	// 请求提示
	function clearTips($suffix){
		setCookie('{{ date('Ymd') }}'+$suffix,"close",1);
	}

	//展示待办列表
	function showtasks() {
		$.ajax({
		    url: "{{ url('tasksall') }}",
		    type: 'GET',
		    data: {"_token":"{{ csrf_token() }}","status":1, "mode":mode},
		    success: function(result) {
		    	result_arr = JSON.parse(result);
				if(result_arr.code != 9999){
					alert('处理失败，请稍后再试');
				} else {
					$("#tasks").html("");
					$.each( result_arr.result, function( index, data ){
						$("#tasks").append(create_task_li(data));
					});
					$('#taskCount').text(Object.getOwnPropertyNames(result_arr.result).length - 1);
				}
		    }
		});
	}

	//展示番茄列表
	function showpomos() {
		$.ajax({
		    url: "{{ url('pomos') }}",
		    type: 'GET',
		    data: {"_token":"{{ csrf_token() }}",'type':'time'},
		    success: function(result) {
		    	result_arr = JSON.parse(result);
				if(result_arr.code != 9999){
					alert('处理失败，请稍后再试');
				} else {
					$.each( result_arr.result, function( index, data ){
						$("#pomos").append(create_pomo_li(data));
					});
					$('#pomoCount').text(Object.getOwnPropertyNames(result_arr.result).length - 1);
				}
		    }
		});
	}
	
	function pomostatus() {
		$.ajax({
		    url: "{{ url('pomostatus') }}",
		    type: 'GET',
		    data: {"_token":"{{ csrf_token() }}"},
		    success: function(result) {
		    	result_arr = JSON.parse(result);
				if(result_arr.code != 9999){
					alert('处理失败，请稍后再试');
				} else {
					if(result_arr.result.current_pomo_status != status) {
						location.href = "";
					}
				}
		    }
		});
	}

	// 通知
	function pomonotify(message){
		// app通知

		//浏览器通知
		notify(message);
	}

	function create_pomo_li(pomo_data){

		$str = '<li id="pomo'+pomo_data.id+'" class="pomo_li">';
		$str += '<span class="time">';
		$str += (new Date(pomo_data.created_at)).format("hh:mm") +' - '+ (new Date(pomo_data.updated_at)).format("hh:mm");
		$str += '</span>';
		$str += '<p>';
		$str += '<a href="/notes?pomo_id='+pomo_data.id+'" class="record_pomo" style="display:none" target="_blank"><img src="/img/icon/text.png" style="height: 20px;"></a>';
		$str += pomo_data.name;
		$str += '</p>';
		$str += '</li>';

		return $str;
	}
	
	function create_task_li(data){
		if(data.parent_task_id==null){
			$str = '<li id="task'+data.id+'" class="task_li">';
		} else {
			$str = '<li id="task'+data.id+'" class="task_li" style="margin-left:25px;">';
		}
		$str += '<p class="task_content" task_value="'+data.id+'" task_is_top="' + data.is_top + '">';
		$str += '<input type="checkbox" class="finish_task" task_type="finish" task_value="'+data.id+'"/>';
		if(data.parent_task_id==null){
			$str += '<a href="javascript:void(0)" class="add_child_task" task_value="'+data.id+'" task_name="'+data.name+'"><img src="/img/icon/add.png" style="height: 20px;"></a>';
		}
		if(data.is_top == 1){
			$str += '<a href="javascript:void(0)" class="top_task" task_value="'+data.id+'" task_is_top="' + data.is_top + '"><img src="/img/icon/top.png" style="height: 20px;"></a>';
		} else {
			$str += '<a href="javascript:void(0)" class="top_task" style="display:none" task_value="'+data.id+'" task_is_top="' + data.is_top + '"><img src="/img/icon/top.png" style="height: 20px;"></a>';
		}
		$str += '<a href="/task/'+data.id+'" class="update_task" style="display:none" ><img src="/img/icon/editor.png" style="height: 20px;"></a>';
		$str += '<a href="javascript:void(0)" class="finish_task delete_task" style="display:none" task_type="delete" task_value="'+data.id+'"><img src="/img/icon/ashbin.png" style="height: 20px;"></a>';
		$str += '<a href="/notes?task_id='+data.id+'" class="record_task" style="display:none" target="_blank"><img src="/img/icon/text.png" style="height: 20px;"></a>';
		$str += data.name;
		$str += '</p>';
		$str += '</li>';
		return $str;
	}

	//如果是休息或者番茄状态展示倒计时
	if(status == 2 || status == 4){
		timer = setInterval(function(){ShowCountDown( remain, "pomoBtn" );}, interval); 
	}
</script>

@section('content')

<script src="{{'/js/bootstro.min.js'}}"></script>
<link href="{{'/css/bootstro.min.css'}}" rel="stylesheet">
<style>
.recent-list {
    list-style: none;
    font-size: .9em;
    line-height: 1.5em;
    margin-left: -30px; 
}
.time {
    float: left;
    padding-right: .8em;
    color: #999;
}
.number {
    font-size: .9em;
    color: #666;
    float: right;
}
.head {
    padding-bottom: 25px;
}

input[type=checkbox]{        
   margin-right: 5px;        
   /*同样，首先去除浏览器默认样式*/  
   -webkit-appearance: none;        
   -moz-appearance: none;        
   appearance: none;        
   /*编辑我们自己的样式*/   
   position: relative;        
   width: 13px;        
   height: 13px;        
   background: transparent;        
   border:1px solid #0b91bd;        
   -webkit-border-radius: 4px;        
   -moz-border-radius: 4px;        
   border-radius: 4px;        
   outline: none;        
   cursor: pointer;    
}
input[type=checkbox]:after{        
   content: '√';        
   position: absolute;        
   display: block;        
   width: 100%;        
   height: 100%;        
   background: #00BFFF;        
   color: #fff;        
   text-align: center;        
   line-height: 18px;        
   /*增加动画*/   
   -webkit-transition: all ease-in-out 300ms;        
   -moz-transition: all ease-in-out 300ms;        
   transition: all ease-in-out 300ms;        
   /*利用border-radius和opacity达到填充的假象，首先隐藏此元素*/  
    -webkit-border-radius: 20px;        
   -moz-border-radius: 20px;        
   border-radius: 20px;        
   opacity: 0;    
}
input[type=checkbox]:checked:after{        
   -webkit-border-radius: 0;        
   -moz-border-radius: 0;        
   border-radius: 0;        
   opacity: 1;    
}
</style>
<script type="text/javascript">
$(document).ready(function () {

	if(getCookie("mode") == 2){
		mode = 2;
		$(".mode_name").text("生活");
	} else {
		mode = 1;
		$(".mode_name").text("工作");
	}
	
	//主动加载列表
    showtasks();

	//主动加载番茄列表
    showpomos();
    
	//开始做番茄
	$("#pomoBtn").click(function(){
		if(status == 1){
			$.ajax({
			    url: "{{ url('pomos/start') }}",
			    type: 'GET',
			    data: {"_token":"{{ csrf_token() }}"},
			    success: function(result) {
			    	result_arr = JSON.parse(result);
					if(result_arr.code != 9999){
						alert('处理失败，请稍后再试');
					} else {
						$("#pomo_id").val(result_arr.result.active_pomo.id);
						remain = result_arr.result.current_pomo_remain;
						status = result_arr.result.current_pomo_status;
						timer = setInterval(function(){ShowCountDown( remain, "pomoBtn" );}, interval); 
					}
			    }
			});
		} else if( status == 2 || status == 4) {
			discard();
		}
	});

	//删除待办
	$("#tasks").on('click','.finish_task',function(){
		task_value = $(this).attr("task_value");
		task_type = $(this).attr("task_type");

		if (task_type == 'delete' && !confirm("确认要删除此任务咩？")) {
			return false;
		}
		
		$.ajax({
		    url: "{{ url('task') }}"+"/"+task_value,
		    type: 'DELETE',
		    data: {"type":task_type,"_token":"{{ csrf_token() }}"},
		    success: function(result) {
		    	result_arr = JSON.parse(result);
				if(result_arr.code != 9999){
					alert('处理失败，请稍后再试');
				} else {
					$('#task'+task_value).remove();
				}
		    }
		});
	});

	//置顶待办
	$("#tasks").on('click','.top_task',function(){
		task_value = $(this).attr("task_value");
		task_is_top = $(this).attr("task_is_top");

		if(task_is_top != 1){
			task_is_top = 1;
		} else {
			task_is_top = 0;
		}
		
		$.ajax({
		    url: "{{ url('task') }}"+"/"+task_value,
		    type: 'POST',
		    data: {"is_top":task_is_top,"_token":"{{ csrf_token() }}"},
		    success: function(result) {
		    	result_arr = JSON.parse(result);
				if(result_arr.code != 9999){
					alert('处理失败，请稍后再试');
				} else {
					if(task_is_top == 1){
						alert("置顶成功");
					} else {
						alert("取消置顶成功");
					}
					location.href = '{{url('/index')}}';
				}
		    }
		});
	});

	
	$("#tasks").on('click','.add_child_task',function(){
		task_value = $(this).attr("task_value");
		task_name = $(this).attr("task_name");

		var parent = $(this).parent();
		
		var name = prompt("创建[" + task_name + "]子任务","");
		if (name != null) {
			$.ajax({
			    url: "{{ url('task') }}",
			    type: 'POST',
			    data: {"name":name,"mode":mode,"parent_task_id":task_value,"_token":"{{ csrf_token() }}"},
			    success: function(result) {
			    	result_arr = JSON.parse(result);
					if(result_arr.code != 9999){
						alert('处理失败，请稍后再试');
						
					} else {
						var data = result_arr.result;
						parent.append(create_task_li(data));
					}
			    }
			});
		} else {
		    return true;
		}
		$.ajax({
		    url: "{{ url('task') }}"+"/"+task_value,
		    type: 'POST',
		    data: {"is_top":task_is_top,"_token":"{{ csrf_token() }}"},
		    success: function(result) {
		    	result_arr = JSON.parse(result);
				if(result_arr.code != 9999){
					alert('处理失败，请稍后再试');
				} else {
					if(task_is_top == 1){
						alert("置顶成功");
					} else {
						alert("取消置顶成功");
					}
					location.href = '{{url('/index')}}';
				}
		    }
		});
	});

	//将待办内容放到正在做的番茄
	$("#tasks").on('dblclick','.task_content',function(){
		task_value = $(this).text();
		pomo_value = $("#pomo_name").val();
		
		if(pomo_value == ''){
			$("#pomo_name").val(pomo_value+task_value);
		} else if(pomo_value.indexOf(task_value)==-1){
			$("#pomo_name").val(pomo_value+ ' + ' +task_value);
		} else {
			$("#pomo_name").val(pomo_value.replace(task_value,''));
		}
		
	});

	//鼠标滑过展示该条待办操作区 鼠标离去隐藏该条待办操作区
	$("#tasks").on('mouseenter','.task_li',function(){
		$(this).find(".record_task").show();
		$(this).find(".delete_task").show();
		$(this).find(".update_task").show();
		$(this).find(".top_task").show();
	}).on('mouseleave','.task_li',function(){
		$(this).find(".record_task").hide();
		$(this).find(".delete_task").hide();
		$(this).find(".update_task").hide();
		if($(this).find(".top_task").attr("task_is_top") != 1){
			$(this).find(".top_task").hide();
		}
	});
	
	//鼠标滑过展示该条番茄操作区 鼠标离去隐藏该条番茄操作区
	$("#pomos").on('mouseenter','.pomo_li',function(){
		$(this).find(".record_pomo").show();
	}).on('mouseleave','.pomo_li',function(){
		$(this).find(".record_pomo").hide();
	});

	//新手引导
	$(".new_user_guide").click(function(){
		 bootstro.start('.bootstro', {stopOnBackdropClick : true, stopOnEsc:true});       
    });

	// 切换待办模式
    $(".change_mode").click(function(){
		if(mode == 2){
			mode = 1;
			$(".mode_name").text("工作");
		} else {
			mode = 2;
			$(".mode_name").text("生活");
		}
		setCookie("task_mode",mode,30);
		$(".change_mode").text("[切换加载中...]");
		showtasks();
		$(".change_mode").text("[切换]");
    });

});

/**
 * 监听键盘回车事件
 */
$(document).keyup(function(event){  
	if(event.keyCode ==13){  
		// 新增待办时按回车键
		if($("#task_name").is(":focus")){
			task_name = $("#task_name").val();
			$.ajax({
			    url: "{{ url('task') }}",
			    type: 'POST',
			    data: {"name":task_name,"mode":mode,"_token":"{{ csrf_token() }}"},
			    success: function(result) {
			    	result_arr = JSON.parse(result);
					if(result_arr.code != 9999){
						alert('处理失败，请稍后再试');
					} else {
						//temp
						$("#task_name").val("");
						var data = result_arr.result;
						$("#tasks").prepend(create_task_li(data));
					}
			    }
			});
			// 新增番茄描述时按回车键
		} else if($("#pomo_name").is(":focus")){
			pomo_name = $("#pomo_name").val();
			pomo_id = $("#pomo_id").val();
			$.ajax({
			    url: "{{ url('pomo') }}/"+pomo_id,
			    type: 'POST',
			    data: {"name":pomo_name,"_token":"{{ csrf_token() }}"},
			    success: function(result) {
			    	result_arr = JSON.parse(result);
					if(result_arr.code != 9999){
						alert('处理失败，请稍后再试');
					} else {
						$("#pomo_name").val("");
						$("#pomo_id").val("");
						remain = result_arr.result.current_pomo_remain;
						status = result_arr.result.current_pomo_status;
						timer = setInterval(function(){ShowCountDown( remain, "pomoBtn" );}, interval); 
						$("#recordPomo").css("display", "none");
						$("#pomoBtn").css("display", "block");
						
						$("#pomos").prepend(create_pomo_li(result_arr.result.active_pomo));
					}
			    }
			});
		}
	}  
}); 
</script>
<div class="container">
	@include('common.success')
	<div class="row">
		<div class=" col-md-6 bootstro" data-bootstro-step="0"
			data-bootstro-placement="bottom" data-bootstro-nextButtonText="下一步"
			data-bootstro-content="使用番茄工作法，选择一个待完成的任务，将番茄时间设为25分钟，专注工作，中途不允许做任何与该任务无关的事，直到番茄时钟响起，然后在纸上画一个X短暂休息一下（5分钟就行），每4个番茄时段多休息一会儿。"
			data-bootstro-finishButton="返回网站，开启高效生活~">
			<div class="card card-default">
				<div class="card-header">
					开蕃走起
					<div style="float: right">
						<a href="{{'pomos'}}">[历史]</a> <a href="{{'things'}}">[记事]</a> <a
							href="javascript:void(0)" class="new_user_guide">[?]</a>
					</div>
				</div>

				<div class="card-body">
					<a class="btn btn-outline-info btn-shadow btn-block" href="javascript:void(0)"  @if($current_pomo_status ==3) style="display: none" @endif role="button" id="pomoBtn">开始一个新的番茄吧!</a> 

					<div class="form-group" @if($current_pomo_status !=3) style="display: none" @endif id="recordPomo">
						<div class="col-md-12">
							<input type="text" name="name" id="pomo_name"
								class="form-control" value="" placeholder="记录刚完成的番茄内容？点击任务名快速添加">
							<a href="javascript:void(0)" onclick="discard()">x</a>
						</div>
					</div>
					
					<input type="hidden" value="{{ $active_pomo->id }}" id="pomo_id">

					<hr width=100% size=1 color=#bbbcbc
						style="FILTER: alpha(opacity = 100, finishopacity = 0)">

					<div class = "head">
						<datetime class="time">{{ date('m月d日') }}</datetime>
						<div class="number">完成了 <span id="pomoCount">0</span> 个番茄</div>
					</div>
					
					<ul id="pomos" class="recent-list">
					
					</ul>
				</div>
			</div>

		</div>



		<div class=" col-md-6 bootstro" data-bootstro-step="1"
			data-bootstro-placement="bottom" data-bootstro-prevButtonText="上一步"
			data-bootstro-content="在这里创建待办事项，高级功能里面可以增加提醒、优先级设定等功能"
			data-bootstro-finishButton="返回网站，开启高效生活~">
			<div class="card card-default">
				<div class="card-header">
					新的待办事项
					<div style="float: right">
						<a href="{{'tasks'}}">[待办列表]</a> 
						<a href="{{'/taskpriority'}}">[待办四象限]</a> 
						<a href="javascript:void(0)"
							class="new_user_guide">[?]</a>
					</div>
				</div>

				<div class="card-body">
					<div class="form-group">
						<input type="text" name="name" id="task_name" class="form-control"
							value="" style="" placeholder="添加新任务">
					</div>

					<hr width=100% size=1 color=#bbbcbc
						style="FILTER: alpha(opacity = 100, finishopacity = 0)">
						
					<div class = "head">
						<datetime class="time"><span class="mode_name"></span><span class="change_mode">[切换]</span></datetime>
						<div class="number">共 <span id="taskCount">0</span> 待办任务</div>
					</div>
						
					<ul id="tasks" class="recent-list">
					
					</ul>
				</div>
			</div>

		</div>
	</div>
</div>
@endsection