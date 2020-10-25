ALEXWANG.LabelHandler = function()
{
	var ProjectId = 0;
	var Store=[];
	var LabelColor;
	var LabelSelector;
	var CheckBoxAll;
	var uncheck_after_action = true; /* Do we want to uncheck the checkbox after apply/remove label */

	return {
		/*
		 * options:
		 *    options.project_id
		 *    options.bugids[]
		 *    options.checkbox_prefix: the checkbox element id prefix, e.g. check_## where
		 *                             ## is bugid, the prefix is check_. If the checkbox_prefix
		 *                             is false, we will have a checkbox always checked.
		 *    options.container_prefix
		 *    options.label_color: [{label_id, font_color, background_color}, {}, {}...]
		 *                         The label configuration.
		 *    options.label_selector: The id of the select dropdown for label operation
		 */
		Init: function(options) {
			ProjectId = options.project_id;
			LabelSelector = document.getElementById(options.label_selector);
			CheckBoxAll = document.getElementById(options.checkbox_prefix+'all');
			if (!LabelSelector) {
				/* Configuration error. There should be a <select id="label_selector">.	*/
				return;
			}

			if (!CheckBoxAll && options.checkbox_prefix) {
				/* Configuration error. There should be a <input id="checkbox_prefix+all"> to select all. */
				return;
			}
			/* The CheckBoxAll is the checkbox that will toggle all checkboxes when it is clicked.*/
			if (CheckBoxAll) {
				var parent = this;
				CheckBoxAll.onclick = function() {
					parent.SelectAll(this);
				};
			}

			var fn_checkbox_click = function() {
				ALEXWANG.SelectorOP.re_generate_remove(LabelSelector);
			};
			for (var i = 0; i < options.bugids.length; i++) {
				var checkbox;
				if (options.checkbox_prefix) {
					checkbox = document.getElementById(options.checkbox_prefix+options.bugids[i]);
					checkbox.onclick = fn_checkbox_click;
				} else {
					checkbox = {checked: true};
					uncheck_after_action = false;
				}
				
				var el = document.getElementById(options.container_prefix+options.bugids[i]);
				if (!checkbox || !el) {
					continue;
				}
				Store.push({
					bugid: options.bugids[i],
					checkbox: checkbox,
					container: el
				});
			}
			LabelColor = options.label_color;
			ALEXWANG.SelectorOP.re_generate_remove(LabelSelector);
		},
		/* Remember the label color, So we can use it when apply the label to the bug */
		AddLabelColor: function(options) {
			LabelColor.push(options);
		},
		GetLabelColor: function(label_id) {
			for (var i = 0; i < LabelColor.length; i++) {
				if (LabelColor[i].label_id == label_id) {
					return LabelColor[i];
				}
			}
			return LabelColor[0];
		},
		SearchLabel: function(nodes, label) {
			for (var i = 0; i < nodes.length; i++) {
				var list = nodes[i].id.split(":");
				var label_id = list[2];
				// Chrome has bug when using label.name (when label has html tag), so we add label.id
				if (nodes[i].innerHTML == label.name || label_id == label.id) {
					return i;
				}
			}
			return -1;
		},
		/* Apply the label to the bug */
		ApplyLabel: function(label) {
			for (var i = 0; i < Store.length; i++) {
				if (!Store[i].checkbox.checked) {
					continue;
				}
				var childnodes = Store[i].container.getElementsByTagName('DIV');
				
				if (this.SearchLabel(childnodes, label) >= 0) {
					continue;
				}

				var color = this.GetLabelColor(label.id);
				var div = document.createElement('DIV');
				div.id = 'label:'+Store[i].bugid+':'+label.id;
				div.className = 'report_label';
				div.innerHTML = label.name;
				div.style.color = color.font_color;
				div.style.backgroundColor = color.background_color;
				if (childnodes.length) {
					Store[i].container.insertBefore(div, childnodes[childnodes.length - 1]);
				} else {
					Store[i].container.appendChild(div);
				}
			}
			ALEXWANG.SelectorOP.add_to_apply(LabelSelector, label);
			if (uncheck_after_action) {
				CheckBoxAll.checked = false;
				this.SelectAll(CheckBoxAll);
			} else {
				ALEXWANG.SelectorOP.add_to_remove(LabelSelector, label);
			}
		},
		/* Remove the label div from the bug */
		RemoveLable: function(label) {
			for (var i = 0; i < Store.length; i++) {
				if (!Store[i].checkbox.checked) {
					continue;
				}
				var childnodes = Store[i].container.getElementsByTagName('DIV');
				var idx = this.SearchLabel(childnodes, label);
				if (idx < 0) {
					// Not found
					continue;
				}
				Store[i].container.removeChild(childnodes[idx]);
			}
			if (uncheck_after_action) {
				CheckBoxAll.checked = false;
				this.SelectAll(CheckBoxAll);
			} else {
				ALEXWANG.SelectorOP.remove_from_remove(LabelSelector, label);
			}
		},
		/* Select all checkboxes*/
		SelectAll: function(checkbox) {
			for (var i = 0; i < Store.length; i++) {
				Store[i].checkbox.checked = checkbox.checked;
			}
			ALEXWANG.SelectorOP.re_generate_remove(LabelSelector);
		},
		GetProjectId: function() {
			return ProjectId;
		},
		/* return the total checkboxes that are checked. */
		GetCheckedCount: function() {
			var count = 0;

			for (var i = 0; i < Store.length; i++) {
				if (Store[i].checkbox.checked) {
					count++;
				}
			}
			return count;
		},
		/* Return the labels of bugs whose checkbox is checked. */
		GetCheckedLabels: function() {
			var labels = [];
			for (var i = 0; i < Store.length; i++) {
				if (!Store[i].checkbox.checked) {
					continue;
				}
				childnodes = Store[i].container.getElementsByTagName("DIV");
				for (var j = 0; j < childnodes.length-1; j++) {
					var match = false;
					for (var k = 0; k < labels.length; k++) {
						if (childnodes[j].innerHTML == labels[k].name) {
							match = true;
							break;
						}
					}
					if (!match) {
						var list = childnodes[j].id.split(":");
						labels.push({id:list[2],name:childnodes[j].innerHTML});
					}
				}
			}
			return labels;
		},
		/* Get the bug IDs that don't have the label_name yet. */
		GetFilteredCheckedIDs: function(label_name) {
			var ids = [];
			for (var i = 0; i < Store.length; i++) {
				if (!Store[i].checkbox.checked) {
					continue;
				}
				var match = false;
				childnodes = Store[i].container.getElementsByTagName("DIV");
				for (var j = 0; j < childnodes.length-1; j++) {
					if (childnodes[j].innerHTML == label_name) {
						match = true;
						break;
					}
				}
				if (!match) {
					ids.push(Store[i].bugid);
				}				
			}
			return ids;
		},
		/* Get all checked bug id. */
		GetCheckedIDs: function() {
			var ids = [];
			for (var i = 0; i < Store.length; i++) {
				if (!Store[i].checkbox.checked) {
					continue;
				}
				ids.push(Store[i].bugid);
			}
			return ids;
		}
	};
}();

function HTMLEncode(str)
{
	return str.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;");
}
function HTMLDecode(str)
{
	return str.replace(/&amp;/g, "&").replace(/&gt;/g, ">").replace(/&lt;/g, "<");
}

ALEXWANG.SelectorOP = function()
{
	return {
		add_to_apply: function(dropdown, label) {
			var found = false;
			for (var i = dropdown.length - 1; i >= 0; i--) {
				var option = dropdown.options[i];
				if (option.value > 0) {
					if (option.value == label.id) {
						found = true;
						break;
					}
				}
			}
			if (!found) {
				var optgroup = document.getElementById('label_selector_applygroup');
				var el = document.createElement('OPTION');
				el.value = label.id;
				el.innerHTML = label.name;
				optgroup.appendChild(el);
			}
		},
		add_to_remove: function(dropdown, label) {
			var found = false;
			var id = (-1)*label.id;
			for (var i = dropdown.length - 1; i >= 0; i--) {
				if (dropdown.options[i].value < 0) {
					if (dropdown.options[i].value == id) {
						found = true;
						break;
					}
				}
			}
			var optgroup = document.getElementById('label_selector_removegroup');
			optgroup.style.display = 'block';
			if (!found) {
				var el = document.createElement('OPTION');
				el.value = (-1)*label.id;
				el.innerHTML = label.name;
				optgroup.appendChild(el);
			}
		},
		remove_from_remove: function(dropdown, label) {
			var id = (-1)*label.id;
			var count = 0;
			for (var i = dropdown.length - 1; i >= 0; i--) {
				if (dropdown.options[i].value < 0) {
					if (dropdown.options[i].value == id) {
						dropdown.remove(i);
					} else {
						count++;
					}
				}
			}
			if (count === 0) {
				var optgroup = document.getElementById('label_selector_removegroup');
				optgroup.style.display = 'none';
			}
		},
		re_generate_remove: function(dropdown) {
			var optgroup = document.getElementById('label_selector_removegroup');
			var labels = ALEXWANG.LabelHandler.GetCheckedLabels();
			for (var i = dropdown.length - 1; i >= 0; i--) {
				if (dropdown.options[i].value < 0) {
					dropdown.remove(i);
				}
			}
			if (labels.length === 0) {
				optgroup.style.display = 'none';
				return;
			}
			
			optgroup.style.display = 'block';
			for (i = 0; i < labels.length; i++) {
				var el = document.createElement('OPTION');
				el.value = (-1)*labels[i].id;
				el.innerHTML = labels[i].name;
				optgroup.appendChild(el);
			}
		}
	};
}();

function PopupInputNewLabelCB(button)
{
	if (button == 'cancel') {
		return;
	}

	var input = document.getElementById('new_label_name');
	if (!input) {
		return;
	}

	if (input.value.trim() === '') {
		ALEXWANG.Dialog.Show({
			title: STRING['new_label'],
			msg: STRING['no_empty'].replace(/@key@/, STRING['label']),
			buttons: ['ok'],
			width: 300,
			fn: function(button) {
				PopupInputNewLabel();
			}
		});
		return;
	}
	
	ALEXWANG.Ajax.request({
		url: '../report/label.php',
		method: "POST",
		param: {
			action: 'new',
			project_id: ALEXWANG.LabelHandler.GetProjectId(),
			label_name: input.value,
			ids: ALEXWANG.LabelHandler.GetCheckedIDs().join(",")
		},
		callback: function (options, success, response) {
			if (success) {
				var ret;
				try {
					ret = eval("(" + response + ')');
				} catch (ex) {
					ret = null;
				}
				 
				if (ret && ret.label_id) {
					ALEXWANG.LabelHandler.AddLabelColor(ret);
					ALEXWANG.LabelHandler.ApplyLabel({
						id: ret.label_id,
						name: HTMLEncode(input.value.trim())
					});
				} else {
					alert(response);
				}
			}
		},
		scope: this
	});
		
}

function PopupInputNewLabel()
{
	ALEXWANG.Dialog.Show({
		title: STRING['new_label'],
		msg: STRING['label']+STRING['colon']+
			'<br><input class="input-form-text-field" id="new_label_name" type="text" size="30" maxlength="30">',
			buttons: ['submit', 'cancel'],
			width: 300,
			fn: PopupInputNewLabelCB
	});
	return;
}

/**
 * Called when apply, create, remove label to certain
 * bugs.
 * 
 * @param dropdown The <select> dropdown element of the lable action
 *                 control.
 */
function LabelActionHandler(dropdown)
{
	var item = dropdown.options[dropdown.selectedIndex];
	var LabelIDReg = /[\-0-9]/;

	dropdown.selectedIndex = 0;

	if (item.value == 'manage') {
		document.location = '../report/label_admin.php?project_id='+
			ALEXWANG.LabelHandler.GetProjectId();
		return;
	}
	if (!LabelIDReg.test(item.value) && item.value != 'new') {
		// the 'More action', 'Apply label' or 'Remove label'
		return;
	}

	// Must check a bug item
	if (ALEXWANG.LabelHandler.GetCheckedCount() === 0) {
		ALEXWANG.Dialog.Show({
			title: STRING['label'],
			msg: STRING['please_select_item'],
			buttons: ['ok'],
			width: 300
		});
		dropdown.selectedIndex = 0;
		return;
	}
	
	if (item.value == 'new') {   // apply new label
		PopupInputNewLabel();
	} else if (item.value > 0) { // apply

		// Get bug ids that do not have the label yet
		var ids = ALEXWANG.LabelHandler.GetFilteredCheckedIDs(HTMLEncode(item.text));
		if (ids.length === 0) {
			return;
		}
		// Apply label
		ALEXWANG.Ajax.request({
			url: '../report/label.php',
			method: "POST",
			param: {
				action: 'apply',
				project_id: ALEXWANG.LabelHandler.GetProjectId(),
				label_id: item.value,
				ids: ids.join(",")
			},
			callback: function (options, success, response) {
				if (success) {
					if (response === '0') {
						ALEXWANG.LabelHandler.ApplyLabel({
							id: item.value,
							name: HTMLEncode(item.text.trim())
						});
					} else {
						alert(response);
					}
				}
			},
			scope: this
		});
	} else if (item.value < 0) {
		var label_id = (-1) * item.value;
		var label_name = item.text.trim();
		// Remove label
		ALEXWANG.Ajax.request({
			url: '../report/label.php',
			method: "POST",
			param: {
				action: 'remove',
				project_id: ALEXWANG.LabelHandler.GetProjectId(),
				label_id: label_id,
				ids: ALEXWANG.LabelHandler.GetCheckedIDs().join(",")
			},
			callback: function (options, success, response) {
				if (success) {
					if (response === '0') {
						ALEXWANG.LabelHandler.RemoveLable({
							id: label_id,
							name: HTMLEncode(label_name)
						});
					} else {
						alert(response);
					}
				}
			},
			scope: this
		});
		
	}
		
	return;
}
