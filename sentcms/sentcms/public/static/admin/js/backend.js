define(['jquery', 'sent', 'adminlte'], function($, sent, AdminLTE){
	//Create the new tab
	var tab_pane = $("<div />", {
		"id": "control-sidebar-theme-demo-options-tab",
		"class": "tab-pane active"
	});

	//Create the tab button
	var tab_button = $("<li />", {"class": "active"})
		.html("<a href='#control-sidebar-theme-demo-options-tab' data-toggle='tab'>"
		+ "<i class='fa fa-wrench'></i>"
		+ "</a>");

	//Create the menu
	var demo_settings = $("<div />");

	//Layout options
	demo_settings.append(
		"<h4 class='control-sidebar-heading'>"
		+ "布局配置"
		+ "</h4>"
		//Fixed layout
		+ "<div class='form-group'>"
		+ "<label class='control-sidebar-subheading'>"
		+ "<input type='checkbox' data-layout='fixed' class='pull-right'/> "
		+ "固定布局"
		+ "</label>"
		+ "</div>"
		//Boxed layout
		+ "<div class='form-group'>"
		+ "<label class='control-sidebar-subheading'>"
		+ "<input type='checkbox' data-layout='layout-boxed'class='pull-right'/> "
		+ "盒式布局"
		+ "</label>"
		+ "</div>"
		//Sidebar Toggle
		+ "<div class='form-group'>"
		+ "<label class='control-sidebar-subheading'>"
		+ "<input type='checkbox' data-layout='sidebar-collapse' class='pull-right'/> "
		+ "切换边栏"
		+ "</label>"
		+ "</div>"
		//Sidebar mini expand on hover toggle
		+ "<div class='form-group'>"
		+ "<label class='control-sidebar-subheading'>"
		+ "<input type='checkbox' data-enable='expandOnHover' class='pull-right'/> "
		+ "悬停边栏展开"
		+ "</label>"
		+ "</div>"
		//Control Sidebar Skin Toggle
		+ "<div class='form-group'>"
		+ "<label class='control-sidebar-subheading'>"
		+ "<input type='checkbox' data-sidebarskin='toggle' class='pull-right'/> "
		+ "切换右侧边栏皮肤"
		+ "</label>"
		+ "</div>"
	);
	var skins_list = $("<ul />", {"class": 'list-unstyled clearfix'});

	//Dark sidebar skins
	var skin_blue =
		$("<li />", {style: "float:left; width: 33.33333%; padding: 5px;"})
			.append("<a href='javascript:void(0);' data-skin='skin-blue' style='display: block; box-shadow: 0 0 3px rgba(0,0,0,0.4)' class='clearfix full-opacity-hover'>"
			+ "<div><span style='display:block; width: 20%; float: left; height: 7px; background: #367fa9;'></span><span class='bg-light-blue' style='display:block; width: 80%; float: left; height: 7px;'></span></div>"
			+ "<div><span style='display:block; width: 20%; float: left; height: 20px; background: #222d32;'></span><span style='display:block; width: 80%; float: left; height: 20px; background: #f4f5f7;'></span></div>"
			+ "</a>"
			+ "<p class='text-center no-margin'>蓝色</p>");
	skins_list.append(skin_blue);
	var skin_black =
		$("<li />", {style: "float:left; width: 33.33333%; padding: 5px;"})
			.append("<a href='javascript:void(0);' data-skin='skin-black' style='display: block; box-shadow: 0 0 3px rgba(0,0,0,0.4)' class='clearfix full-opacity-hover'>"
			+ "<div style='box-shadow: 0 0 2px rgba(0,0,0,0.1)' class='clearfix'><span style='display:block; width: 20%; float: left; height: 7px; background: #fefefe;'></span><span style='display:block; width: 80%; float: left; height: 7px; background: #fefefe;'></span></div>"
			+ "<div><span style='display:block; width: 20%; float: left; height: 20px; background: #222;'></span><span style='display:block; width: 80%; float: left; height: 20px; background: #f4f5f7;'></span></div>"
			+ "</a>"
			+ "<p class='text-center no-margin'>黑色</p>");
	skins_list.append(skin_black);
	var skin_purple =
		$("<li />", {style: "float:left; width: 33.33333%; padding: 5px;"})
			.append("<a href='javascript:void(0);' data-skin='skin-purple' style='display: block; box-shadow: 0 0 3px rgba(0,0,0,0.4)' class='clearfix full-opacity-hover'>"
			+ "<div><span style='display:block; width: 20%; float: left; height: 7px;' class='bg-purple-active'></span><span class='bg-purple' style='display:block; width: 80%; float: left; height: 7px;'></span></div>"
			+ "<div><span style='display:block; width: 20%; float: left; height: 20px; background: #222d32;'></span><span style='display:block; width: 80%; float: left; height: 20px; background: #f4f5f7;'></span></div>"
			+ "</a>"
			+ "<p class='text-center no-margin'>紫色</p>");
	skins_list.append(skin_purple);
	var skin_green =
		$("<li />", {style: "float:left; width: 33.33333%; padding: 5px;"})
			.append("<a href='javascript:void(0);' data-skin='skin-green' style='display: block; box-shadow: 0 0 3px rgba(0,0,0,0.4)' class='clearfix full-opacity-hover'>"
			+ "<div><span style='display:block; width: 20%; float: left; height: 7px;' class='bg-green-active'></span><span class='bg-green' style='display:block; width: 80%; float: left; height: 7px;'></span></div>"
			+ "<div><span style='display:block; width: 20%; float: left; height: 20px; background: #222d32;'></span><span style='display:block; width: 80%; float: left; height: 20px; background: #f4f5f7;'></span></div>"
			+ "</a>"
			+ "<p class='text-center no-margin'>绿色</p>");
	skins_list.append(skin_green);
	var skin_red =
		$("<li />", {style: "float:left; width: 33.33333%; padding: 5px;"})
			.append("<a href='javascript:void(0);' data-skin='skin-red' style='display: block; box-shadow: 0 0 3px rgba(0,0,0,0.4)' class='clearfix full-opacity-hover'>"
			+ "<div><span style='display:block; width: 20%; float: left; height: 7px;' class='bg-red-active'></span><span class='bg-red' style='display:block; width: 80%; float: left; height: 7px;'></span></div>"
			+ "<div><span style='display:block; width: 20%; float: left; height: 20px; background: #222d32;'></span><span style='display:block; width: 80%; float: left; height: 20px; background: #f4f5f7;'></span></div>"
			+ "</a>"
			+ "<p class='text-center no-margin'>红色</p>");
	skins_list.append(skin_red);
	var skin_yellow =
		$("<li />", {style: "float:left; width: 33.33333%; padding: 5px;"})
			.append("<a href='javascript:void(0);' data-skin='skin-yellow' style='display: block; box-shadow: 0 0 3px rgba(0,0,0,0.4)' class='clearfix full-opacity-hover'>"
			+ "<div><span style='display:block; width: 20%; float: left; height: 7px;' class='bg-yellow-active'></span><span class='bg-yellow' style='display:block; width: 80%; float: left; height: 7px;'></span></div>"
			+ "<div><span style='display:block; width: 20%; float: left; height: 20px; background: #222d32;'></span><span style='display:block; width: 80%; float: left; height: 20px; background: #f4f5f7;'></span></div>"
			+ "</a>"
			+ "<p class='text-center no-margin'>黄色</p>");
	skins_list.append(skin_yellow);

	//Light sidebar skins
	var skin_blue_light =
		$("<li />", {style: "float:left; width: 33.33333%; padding: 5px;"})
			.append("<a href='javascript:void(0);' data-skin='skin-blue-light' style='display: block; box-shadow: 0 0 3px rgba(0,0,0,0.4)' class='clearfix full-opacity-hover'>"
			+ "<div><span style='display:block; width: 20%; float: left; height: 7px; background: #367fa9;'></span><span class='bg-light-blue' style='display:block; width: 80%; float: left; height: 7px;'></span></div>"
			+ "<div><span style='display:block; width: 20%; float: left; height: 20px; background: #f9fafc;'></span><span style='display:block; width: 80%; float: left; height: 20px; background: #f4f5f7;'></span></div>"
			+ "</a>"
			+ "<p class='text-center no-margin' style='font-size: 12px'>亮蓝</p>");
	skins_list.append(skin_blue_light);
	var skin_black_light =
		$("<li />", {style: "float:left; width: 33.33333%; padding: 5px;"})
			.append("<a href='javascript:void(0);' data-skin='skin-black-light' style='display: block; box-shadow: 0 0 3px rgba(0,0,0,0.4)' class='clearfix full-opacity-hover'>"
			+ "<div style='box-shadow: 0 0 2px rgba(0,0,0,0.1)' class='clearfix'><span style='display:block; width: 20%; float: left; height: 7px; background: #fefefe;'></span><span style='display:block; width: 80%; float: left; height: 7px; background: #fefefe;'></span></div>"
			+ "<div><span style='display:block; width: 20%; float: left; height: 20px; background: #f9fafc;'></span><span style='display:block; width: 80%; float: left; height: 20px; background: #f4f5f7;'></span></div>"
			+ "</a>"
			+ "<p class='text-center no-margin' style='font-size: 12px'>亮</p>");
	skins_list.append(skin_black_light);
	var skin_purple_light =
		$("<li />", {style: "float:left; width: 33.33333%; padding: 5px;"})
			.append("<a href='javascript:void(0);' data-skin='skin-purple-light' style='display: block; box-shadow: 0 0 3px rgba(0,0,0,0.4)' class='clearfix full-opacity-hover'>"
			+ "<div><span style='display:block; width: 20%; float: left; height: 7px;' class='bg-purple-active'></span><span class='bg-purple' style='display:block; width: 80%; float: left; height: 7px;'></span></div>"
			+ "<div><span style='display:block; width: 20%; float: left; height: 20px; background: #f9fafc;'></span><span style='display:block; width: 80%; float: left; height: 20px; background: #f4f5f7;'></span></div>"
			+ "</a>"
			+ "<p class='text-center no-margin' style='font-size: 12px'>亮紫</p>");
	skins_list.append(skin_purple_light);
	var skin_green_light =
		$("<li />", {style: "float:left; width: 33.33333%; padding: 5px;"})
			.append("<a href='javascript:void(0);' data-skin='skin-green-light' style='display: block; box-shadow: 0 0 3px rgba(0,0,0,0.4)' class='clearfix full-opacity-hover'>"
			+ "<div><span style='display:block; width: 20%; float: left; height: 7px;' class='bg-green-active'></span><span class='bg-green' style='display:block; width: 80%; float: left; height: 7px;'></span></div>"
			+ "<div><span style='display:block; width: 20%; float: left; height: 20px; background: #f9fafc;'></span><span style='display:block; width: 80%; float: left; height: 20px; background: #f4f5f7;'></span></div>"
			+ "</a>"
			+ "<p class='text-center no-margin' style='font-size: 12px'>亮绿</p>");
	skins_list.append(skin_green_light);
	var skin_red_light =
		$("<li />", {style: "float:left; width: 33.33333%; padding: 5px;"})
			.append("<a href='javascript:void(0);' data-skin='skin-red-light' style='display: block; box-shadow: 0 0 3px rgba(0,0,0,0.4)' class='clearfix full-opacity-hover'>"
			+ "<div><span style='display:block; width: 20%; float: left; height: 7px;' class='bg-red-active'></span><span class='bg-red' style='display:block; width: 80%; float: left; height: 7px;'></span></div>"
			+ "<div><span style='display:block; width: 20%; float: left; height: 20px; background: #f9fafc;'></span><span style='display:block; width: 80%; float: left; height: 20px; background: #f4f5f7;'></span></div>"
			+ "</a>"
			+ "<p class='text-center no-margin' style='font-size: 12px'>亮红</p>");
	skins_list.append(skin_red_light);
	var skin_yellow_light =
		$("<li />", {style: "float:left; width: 33.33333%; padding: 5px;"})
			.append("<a href='javascript:void(0);' data-skin='skin-yellow-light' style='display: block; box-shadow: 0 0 3px rgba(0,0,0,0.4)' class='clearfix full-opacity-hover'>"
			+ "<div><span style='display:block; width: 20%; float: left; height: 7px;' class='bg-yellow-active'></span><span class='bg-yellow' style='display:block; width: 80%; float: left; height: 7px;'></span></div>"
			+ "<div><span style='display:block; width: 20%; float: left; height: 20px; background: #f9fafc;'></span><span style='display:block; width: 80%; float: left; height: 20px; background: #f4f5f7;'></span></div>"
			+ "</a>"
			+ "<p class='text-center no-margin' style='font-size: 12px;'>亮黄</p>");
	skins_list.append(skin_yellow_light);

	demo_settings.append("<h4 class='control-sidebar-heading'>皮肤</h4>");
	demo_settings.append(skins_list);

	tab_pane.append(demo_settings);
	$("#control-sidebar-content").append(tab_pane);
	var backend = {
		/**
		* List of all the available skins
		*
		* @type Array
		*/
		mySkins: ['skin-blue', 'skin-black', 'skin-red', 'skin-yellow', 'skin-purple', 'skin-green', 'skin-blue-light', 'skin-black-light', 'skin-red-light', 'skin-yellow-light', 'skin-purple-light', 'skin-green-light'],

		change_layout: function(){
			layout = sent.store.get({name: 'layout'}) || [{name: 'fixed', value: false}, {name: 'layout-boxed', value: false}, {name: 'sidebar-collapse', value: false}];
			if (typeof(layout) == 'string'){ return false; }
			for (i in layout) {
				var item = layout[i];
				if (item.value) {
					$("[data-layout='"+item.name+"']").attr('checked', 'checked');
					$("body").hasClass(item.name) || $("body").addClass(item.name);
					if ($('body').hasClass('fixed') && item.name == 'fixed') {
						AdminLTE.pushMenu.expandOnHover();
						AdminLTE.layout.activate();
					}
				}else{
					$("body").removeClass(item.name);
				}
			}
			AdminLTE.layout.fixSidebar();
		},
		change_skin: function(cls){
			$.each(backend.mySkins, function (i) {
				$('body').removeClass(backend.mySkins[i])
			})
	
			$('body').addClass(cls)
			sent.store.set({name:'skin', content:cls})
			return false
		},
		init: function(){
			var skin = sent.store.get({name:'skin'});
			if (skin && $.inArray(skin, backend.my_skins))
				backend.change_skin(skin);
		
			//Add the change skin listener
			$("[data-skin]").on('click', function (e) {
				if($(this).hasClass('knob'))
					return;
				e.preventDefault();
				backend.change_skin($(this).data('skin'));
			});
		
			backend.change_layout();
			//Add the layout manager
			$("[data-layout]").on('click', function () {
				var layout = [];
				$("[data-layout]").each(function(i, item){
					layout.push({name:$(item).data('layout'), value: $(item).is(':checked')})
				})
				sent.store.set({name:'layout', content: layout});
				backend.change_layout();
			});
		
			$("[data-sidebarskin='toggle']").on('click', function () {
			  var sidebar = $(".control-sidebar");
			  if (sidebar.hasClass("control-sidebar-dark")) {
				sidebar.removeClass("control-sidebar-dark")
				sidebar.addClass("control-sidebar-light")
			  } else {
				sidebar.removeClass("control-sidebar-light")
				sidebar.addClass("control-sidebar-dark")
			  }
			});
		
			$("[data-enable='expandOnHover']").on('click', function () {
				$(this).attr('disabled', true);
				AdminLTE.pushMenu.expandOnHover();
				if (!$('body').hasClass('sidebar-collapse'))
					$("[data-layout='sidebar-collapse']").click();
			});

			if ($('.editable').size() > 0) {
				require(['bootstrap-editable'], function(){
					$.fn.editable.defaults.mode = 'popup';
					$('.editable').editable();
				})
			}
		}
	}

	backend.init();
	return backend;
})