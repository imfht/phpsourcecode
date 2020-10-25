/**
 * 岗位角色--编辑
 * Organization
 * @author 		inaki
 * @version 	$Id$
 */

$(function(){
	// 表单验证
	$.formValidator.initConfig({ formID:"position_edit_form", errorFocus:true });

	// 角色名称
	$("#role_name").formValidator()
	.regexValidator({
		regExp:"notempty",
		dataType:"enum",
		onError: Ibos.l("ORG.ROLE_NAME_CANNOT_BE_EMPTY")
	});
	
	// 权限选择处理
	$("#limit_setup").bindEvents({
		// 选中功能
		"change [data-node='funcCheckbox']": function(){
			$(this).closest("label").toggleClass("active", this.checked);
		},
		// 选中模块 
		"change [data-node='modCheckbox']": function(evt){
			var id = $.attr(this, "data-id");
			Organization.auth.selectMod(id, $.prop(this, "checked"));
		},
		// 选中分类
		"click [data-node='cateCheckbox']": function(evt){
			var id = $.attr(this, "data-id"),
				checked = $.attr(this, "data-checked") === "1";
			Organization.auth.selectCate(id,  !checked);
			$.attr(this, "data-checked", checked ? "0" : "1");
		},
		// 分类切换显示/隐藏
		"click [data-node='toggle']": function(){
			var $this = $(this),
				$body = $this.closest(".org-limit-header").siblings(".org-limit-body"),
				$selectBtn = $this.siblings(".btn");
			$this.toggleClass("active");
			$selectBtn.fadeToggle();
			$body.slideToggle();
		},
		// CRM权限列表表头垂直批量操作
		"click [data-node='batchVtc']": function(){
			var $this = $(this),
				index = $this.closest("th").index(),
				$limiteTable = $this.closest('table'),
				$limiteTr = $limiteTable.find("tbody tr");
			$limiteTr.each(function(i, el) {
				var $limiteTd = $(el).children("td").eq(index),
				    $level = $limiteTd.children(".privilege-level"),
				    insTooltip = $level.data("tooltip");

				if(insTooltip){
					$level.trigger("click.level");
					var level = $level.siblings("[data-toggle='privilegeLevel']").val();
					insTooltip.options.title = Ibos.l("DB.AUTH_LEVEL_" + level);
				}
			});
		},
		// CRM权限列表横向批量操作
		"click [data-node='batchHoz']": function(){
			var $this = $(this),
				$limiteTd = $this.closest("tr").children("td");
			$limiteTd.each(function(index, el) {
				var $level = $(el).children(".privilege-level"),
					insTooltip = $level.data("tooltip");

				if(insTooltip){
					$level.trigger("click.level");
					var level = $level.siblings("[data-toggle='privilegeLevel']").val();
			 		insTooltip.options.title = Ibos.l("DB.AUTH_LEVEL_" + level);
				}
	
			});
		}
	});

	// 岗位成员列表
	Organization.memberList.init(Ibos.app.g("members"));
});