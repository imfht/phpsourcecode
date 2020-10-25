@extends('layouts.app')
<link type="text/css" rel="stylesheet" href="{{ url('/css/jsmind.css')}}" />

@section('content')

<script type="text/javascript" src="{{ url('/js/jsmind.js').'?'.time()}}"></script>
<script type="text/javascript" src="{{ url('/js/jsmind.screenshot.js')}}"></script>
<script type="text/javascript" src="{{ url('/js/jsmind.draggable.js')}}"></script>

<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.13/css/all.css" integrity="sha384-DNOHZ68U8hZfKXOrtjWvjxusGo9WQnrNx2sqG0tfsghAvtVlRW3tvkXWZh58N9jp" crossorigin="anonymous">

<!-- Markdown IT Main Library -->
<script src="/plugins/markdown-it//markdown-it.min.js"></script>
<!-- Markdown IT Definition List Plugin -->
<script src="/plugins/markdown-it//markdown-it-deflist.min.js"></script>
<!-- Markdown IT Footnote Plugin -->
<script src="/plugins/markdown-it//markdown-it-footnote.min.js"></script>
<!-- Markdown IT Abbreviation Plugin -->
<script src="/plugins/markdown-it//markdown-it-abbr.min.js"></script>
<!-- Markdown IT Subscript Plugin -->
<script src="/plugins/markdown-it//markdown-it-sub.min.js"></script>
<!-- Markdown IT Superscript Plugin -->
<script src="/plugins/markdown-it//markdown-it-sup.min.js"></script>
<!-- Markdown IT Underline/Inserted Text Plugin -->
<script src="/plugins/markdown-it//markdown-it-ins.min.js"></script>
<!-- Markdown IT Mark Plugin -->
<script src="/plugins/markdown-it//markdown-it-mark.min.js"></script>
<!-- Markdown IT SmartArrows Plugin -->
<script src="/plugins/markdown-it//markdown-it-smartarrows.min.js"></script>
<!-- Markdown IT Checkbox Plugin -->
<script src="/plugins/markdown-it//markdown-it-checkbox.min.js"></script>
<!-- Markdown IT East Asian Characters Line Break Plugin -->
<script src="/plugins/markdown-it//markdown-it-cjk-breaks.min.js"></script>
<!-- Markdown IT Emoji Plugin -->
<script src="/plugins/markdown-it//markdown-it-emoji.min.js"></script>

<link href="/css/markdown-editor.css" rel="stylesheet">
<script src="/js/markdown-editor.js"></script>

<style type="text/css">
button, input, optgroup, select, textarea {
    margin: 0;
    font: inherit;
    color: black;
}
.rowone{
        overflow: hidden;
	    text-overflow: ellipsis;
	    display: -webkit-box;
	    -webkit-box-orient: vertical;
	    -webkit-line-clamp: 1;
      }
</style>
<script type="text/javascript">
jQuery(document).ready(function($) {
    $('#mind_content').markdownEditor({
        preview: true,
        onPreview: function (content, callback) {
            callback( marked(content) );
        }
    });
});

$(document).ready(function () {
	$('#work_mode').click(function(){
		$('.container').css('max-width', '1980px');
	});
});
</script>


    <div class="container">
    
        <div class=" col-md-12">
        	@include('common.success')
            <div class="card">
                <div class="card-header">
                    	想法-{{$mind->name}} 
                    	<button class="btn-info" onclick="add_node();">
	                    	<span  class="glyphicon glyphicon-file"></span>
	                    	<span>增加[Insert]</span>
                    	</button>
                    	<button class="btn-info" onclick="modify_node();">
	                    	<span  class="glyphicon glyphicon-pencil"></span>
	                    	<span>修改[F2]</span>
                    	</button>
                    	<button class="btn-info" onclick="show_selected();">
	                    	<span  class="glyphicon glyphicon-search"></span>
	                    	<span>详情[x]</span>
                    	</button>
                    	<button class="btn-info" onclick="remove_node();">
	                    	<span  class="glyphicon glyphicon-remove"></span>
	                    	<span>删除[x]</span>
                    	</button>
                    	<button class="btn-info" onclick="toggle();">
	                    	<span  class="glyphicon glyphicon-fast-forward"></span>
	                    	<span>展开[x]</span>
                    	</button>
                    	<button class="btn-info" onclick="screen_shot();">
	                    	<span  class="glyphicon glyphicon-camera"></span>
	                    	<span>截屏[x]</span>
                    	</button>
                    	
                    	<div style="float:right">
                    		<a href="javascript:void(0)" id="work_mode">[工作模式]</a>
                    		<a href="{{'/minds'}}">[返回]</a>
                    	</div>
                </div>

                <div class="card-body row">
					<div id="jsmind_container" class=" col-md-8">
					</div>

					<div id="" class=" col-md-4">
						<b id="mind_name"  style="margin-top:15px;" class="col-md-12 rowone">详细描述:{{$mind->name}}</b>
                        
						<textarea  class="col-md-12" id="mind_content" data-toolbarHeaderL="" onfocus="mind_content_focus()" style="margin: 0px; height: 189px; " id="mind_content">{{$mind->content}}</textarea>
						<input type="hidden" id="mind_id" value="{{$mind->id}}">
						<input type="hidden" id="mind_token" value="{{ csrf_token() }}">
						<button class="btn btn-info col-md-12" onclick="mind_update()">保存</button>
                        
						<div id="mind_content_show" style="margin:15px;height: 400px;line-height: 180%;"></div>
					</div>

                    
                </div>
            </div>

        </div>
    </div>
<script type="text/javascript">
    var options = {
        container:'jsmind_container',
        editable:true,
        theme:'primary',
        mode :'side',           // 显示模式
        support_html : true,    // 是否支持节点里的HTML元素
        view:{
            hmargin:100,        // 思维导图距容器外框的最小水平距离
            vmargin:50,         // 思维导图距容器外框的最小垂直距离
            line_width:1,       // 思维导图线条的粗细
            line_color:'#555'   // 思维导图线条的颜色
        },
        layout:{
            hspace:30,          // 节点之间的水平间距
            vspace:10,          // 节点之间的垂直间距
            pspace:13           // 节点收缩/展开控制器的尺寸
        },
        shortcut:{
            enable:true,        // 是否启用快捷键
            handles:{
        		
            },// 命名的快捷键事件处理器
            mapping:{           // 快捷键映射
                addchild   : 45,    // <Insert>
                addbrother : 13,    // <Enter>
                editnode   : 113,   // <F2>
                delnode    : 46,    // <Delete>
                toggle     : 32,    // <Space>
                left       : 37,    // <Left>
                up         : 38,    // <Up>
                right      : 39,    // <Right>
                down       : 40,    // <Down>
            }
        },
    };

    var _jm = new jsMind(options);

    task_token = "{{ csrf_token() }}";
	var mind;
	$.ajax({
	    url: "{{ url('/mindajaxget') }}"+"/"+<?php echo $mind->id;?>,
	    type: 'GET',
	    data: {_token:task_token},
	    success: function(result) {
	    	result_arr = JSON.parse(result);
			if(result_arr.code != 9999){
				alert('处理失败，请稍后再试');
			} else {
				mind = JSON.parse(result_arr.result.jsmind_datas);
				
				 // 让 _jm 显示这个 mind 即可
		        _jm.show(mind); 
		        _jm.select_node({{$mind->id}});
			}
	    }
	});
	
    function add_node(){
        var selected_node = _jm.get_selected_node(); // as parent of new node
        if(!selected_node){prompt_info('please select a node first.');return;}

        var selected_id = get_selected_nodeid();

        var name = prompt("Please enter content!", "")
        if (name != null && name != "")
        {
        	task_token = "{{ csrf_token() }}";
        	
        	$.ajax({
    		    url: "{{ url('mind') }}",
    		    type: 'POST',
    		    data: {_token:task_token,name:name,parent_mind_id:selected_id},
    		    success: function(result) {
    		    	result_arr = JSON.parse(result);
    				if(result_arr.code != 9999){
    					alert('处理失败，请稍后再试');
    				} else {
    					var nodeid = result_arr['result']['id'];
    					var topic = result_arr['result']['name'];
    										
   					 	var node = _jm.add_node(selected_node, nodeid, topic);
    				}
    		    }
    		});
        }

    }

    function remove_node(){
        var selected_id = get_selected_nodeid();
        if(!selected_id){prompt_info('please select a node first.');return;}

        if(confirm("确认删除这个节点和子节点?")){
        	task_token = "{{ csrf_token() }}";

            //执行移除
            $.ajax({
    		    url: "{{ url('mind') }}"+"/"+selected_id,
    		    type: 'DELETE',
    		    data: {_token:task_token},
    		    success: function(result) {
    		    	result_arr = JSON.parse(result);
    				if(result_arr.code != 9999){
    					alert('处理失败，请稍后再试');
    				} else {
    					_jm.remove_node(selected_id);
    				}
    		    }
    		});
        }
    }

    function toggle(){
        var selected_id = get_selected_nodeid();
        if(!selected_id){prompt_info('please select a node first.');return;}

        _jm.toggle_node(selected_id);
    }

    function get_selected_nodeid(){
        var selected_node = _jm.get_selected_node();
        if(!!selected_node){
            return selected_node.id;
        }else{
            return null;
        }
    }
    
    function show_selected(){
        var selected_node = _jm.get_selected_node();
        if(!!selected_node){
        	//$("#mind_content_show").html(selected_node.data.content);
        	$("#mind_content").val(selected_node.data.content);
        	$("#mind_id").val(selected_node.id);
        	$("#mind_name").html('描述:'+selected_node.topic);
        }else{
            prompt_info('nothing');
        }
    }
    
    function prompt_info(msg){
        alert(msg);
    }
    
    function screen_shot(){
        _jm.screenshot.shootDownload();
    }
    
    function modify_node(){
        var selected_id = get_selected_nodeid();
        if(!selected_id){prompt_info('please select a node first.');return;}
        
        var selected_node = _jm.get_selected_node();

        var name = prompt("Please enter content!",selected_node.topic)
        if (name != null && name != "")
        {
        	task_token = "{{ csrf_token() }}";
        	
        	$.ajax({
    		    url: "{{ url('mind') }}"+"/"+selected_id,
    		    type: 'POST',
    		    data: {_token:task_token,name:name},
    		    success: function(result) {
    		    	result_arr = JSON.parse(result);
    				if(result_arr.code != 9999){
    					alert('处理失败，请稍后再试');
    				} else {
    					// modify the topic
    		            _jm.update_node(selected_id, name);
    				}
    		    }
    		});
        }
    }

    function mind_content_focus(){
        _jm.select_clear();
    }

    function mind_update(){
    	var content = $("#mind_content").val();
    	var selected_id = $("#mind_id").val();
    	
        if (selected_id != null && selected_id != "" && content != null && content != "")
        {
        	var selected_node = _jm.get_node(selected_id);
        	
        	task_token = "{{ csrf_token() }}";
        	
        	$.ajax({
    		    url: "{{ url('mind') }}"+"/"+selected_id,
    		    type: 'POST',
    		    data: {_token:task_token,content:content},
    		    success: function(result) {
    		    	result_arr = JSON.parse(result);
    				if(result_arr.code != 9999){
    					alert('处理失败，请稍后再试');
    				} else {
    					selected_node.data.content = content;
    					_jm.select_node(selected_id);
    				}
    		    }
    		});
        } else {
            alert('请先选中获取信息');
        }
    }
</script>
@endsection
