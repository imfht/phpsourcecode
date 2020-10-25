//加载指定的节点信息
function loadNodeInfo(selected) {
	  var index = layer.load(1, {
          shade: [0.1,'#fff'] //0.1透明度的白色背景
      });
///tree/material/anode/{nodeId}/data/get
      $.get("tree/material/anode/"+escape(selected.node.id)+"/data/get/table"
      ).done(function (data) {
          layer.close(index);
          $('#nodeInfoShow').html(data);
          
      }).fail(function () {
          layer.close(index);
          layer.msg("加载信息失败");
      });
}
// 监听树形结构的点击事件
$('#jsTreeContainer').jstree({
	  'plugins': [ "wholerow", "types"],
	    'core' : {
	    	'check_callback': true,
	      'data' : function (node, cb) {
	    	  
	    	  $.ajax({  
	              type : "get",  
	              url : "/admin/tree/data/get/"+escape(node.id),  
	              data : "",  
	              async : false,  
	              success : function(data){  
	                data = eval("(" + data + ")"); 
	                cb(data);  
	              }  
	              }); 
	          },
		      'animation': 0,
            "multiple": false
	    },
	    "types" : {
	        "#" : {
	        	"icon" : "glyphicon glyphicon-file",
		          "valid_children" : ["trunk","user","material"],
	        },
	        "default" : {
	        	"icon" : "glyphicon glyphicon-folder-open",
	        	"valid_children" : ["trunk","user","material"],
	        },
	        "trunk" : {
	          "icon" : "fa fa-building-o",
	          "valid_children" : ["trunk","user","material"],
	        },
	        "material" : {
	        	"icon" : "fa fa-gear",
	        },
	        "user" : {
	        	"icon" : "glyphicon glyphicon-user",
	        },
	      },

	  }).on('select_node.jstree', function (node, selected, event) {
	loadNodeInfo(selected);
});

