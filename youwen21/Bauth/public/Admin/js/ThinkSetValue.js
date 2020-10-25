var ThinkPHP = window.Think = {};
/* 设置表单的值 */
ThinkPHP.setValue = function(name, value) {
    var first = name.substr(0, 1),
        input, i = 0,
        val;
    if (value === "") return;
    if ("#" === first || "." === first) {
        input = $(name);
    } else {
        input = $("[name='" + name + "']");
    }

    if (input.eq(0).is(":radio")) { //单选按钮
        input.filter("[value='" + value + "']").each(function() {
            this.checked = true
        });
    } else if (input.eq(0).is(":checkbox")) { //复选框
        if (!$.isArray(value)) {
            val = new Array();
            val[0] = value;
        } else {
            val = value;
        }
        for (i = 0, len = val.length; i < len; i++) {
            input.filter("[value='" + val[i] + "']").each(function() {
                this.checked = true
            });
        }
    } else { //其他表单选项直接设置值
        input.val(value);
    }
}