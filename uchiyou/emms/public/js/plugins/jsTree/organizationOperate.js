
$(function() {
	  $('#jsTreeContainer').jstree({
		  'plugins': [ "wholerow", "types", 'dnd', 'contextmenu'],
	    'core' : {
	    	'check_callback': true,
	      'data' : function (node, cb) {
	    	  
	    	  $.ajax({  
	              type : "get",  
	              url : "/admin/organization/tree/data/get/"+escape(node.id),  
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
	        	"icon" : "fa fa-users",
	        	"valid_children" : ["trunk","user","material"],
	        },
	        "trunk" : {
	          "icon" : "fa fa-users",
	          "valid_children" : ["trunk","user","material"],
	        },
	        "user" : {
	        	"icon" : "glyphicon glyphicon-user",
	        },
	      },
        "contextmenu": {
            show_at_node: false,
            select_node: false,
            "items": {
                "添加部门": {
                    "separator_before": false,
                    "separator_after": true,
                    "_disabled": false,
                    "label": "添加部门信息",
                    "icon": "fa fa-plus",
                    "action": function (data) {

                        var inst = $.jstree.reference(data.reference),
                        node = inst.get_node(data.reference);
                        if(node.type == 'user'){
                        	layer.msg('请在目录下存放的目录信息');
                        	return false;
                        }
                        openCreateTrunkCatalogDialog(node);
                    }
                },
                "添加员工": {
                	"separator_before": false,
                	"separator_after": true,
                	"_disabled": false,
                	"label": "添加员工信息",
                	"icon": "fa fa-plus",
                	"action": function (data) {
                		
                		var inst = $.jstree.reference(data.reference),
                		node = inst.get_node(data.reference);
                		 if(node.type == 'user'){
                         	layer.msg('请在部门目录下添加员工信息');
                         	return false;
                         }
                		openCreateUserCatalogDialog(node);
                	}
                },
                "编辑": {
                    "separator_before": false,
                    "separator_after": true,
                    "_disabled": false,
                    "label": "编辑",
                    "icon": "fa fa-edit",
                    "action": function (data) {
                        var inst = $.jstree.reference(data.reference);
                        var node = inst.get_node(data.reference);
                        editCurrentInfoDialog(node);
                    }
                },
                "删除": {
                    "separator_before": false,
                    "separator_after": true,
                    "_disabled": false,
                    "label": "删除",
                    "icon": "fa fa-trash-o",
                    "action": function (data) {
                    	
                        var inst = $.jstree.reference(data.reference);
                        var node = inst.get_node(data.reference);
                        deleteNodeDialog(node);
                    }
                }
            }
        }
	  }).on('loaded.jstree', function () {
          window.treeCatalog = $(this).jstree();
          $select_node_id = window.treeCatalog.get_selected();
          if($select_node_id) {
              $select_node = window.treeCatalog.get_node($select_node_id[0])
              if ($select_node) {
                  $select_node.node = {
                      id: $select_node.id
                  };

                  //window.loadDocument($select_node);
              }
            //  console.log($select_node);
          }

      }).on('select_node.jstree', function (node, selected, event) {
    	  window.loadNodeInfo(selected);
      }).on("move_node.jstree", function (node, parent) {

          var parentNode = window.treeCatalog.get_node(parent.parent);

          var nodeData = window.getSiblingSort(parentNode);

          if (parent.parent != parent.old_parent) {
              parentNode = window.treeCatalog.get_node(parent.old_parent);
              //console.log(parentNode);
              var newNodeData = window.getSiblingSort(parentNode);
              if (newNodeData.length > 0) {
                  nodeData = nodeData.concat(newNodeData);
              }
          }

          var index = layer.load(1, {
              shade: [0.1, '#fff'] //0.1透明度的白色背景
          });

          $.post("tree/organization/sort", JSON.stringify(nodeData)).done(function (res) {
              layer.close(index);
             // layer.msg("保存排序成功");
              if (res.errcode != 0) {
                  layer.msg(res.message);
              } else {
                  layer.msg("保存排序成功");
              }
          }).fail(function () {
              layer.close(index);
              layer.msg("保存排序失败");
          });
      });
	  
	  $("#create_directory_button").click(function () {
		  openCreateTrunkCatalogDialog();
	    });

	});