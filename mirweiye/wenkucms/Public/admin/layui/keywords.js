$(function() {
$('#tpt_input').keydown(function(e) {
if (e.which === 13) {
$("#tpt_btn").click();
e.preventDefault();
return false;
}
});
$("#tpt_sel").on('click', 'a>em', function() {
var name = "keywords";
var tag = $(this).parent().text();
$(this).parent().remove();
var tags = new Array();
$("#tpt_sel").find('a').each(function() {
tags.push($(this).text());
});
$("input[name=" + name + "]").val(tags.join(","));
$("#tpt_pre a:contains('" + tag + "')").removeClass("selected");
});
$("#tpt_btn").click(function() {
var name = "keywords";
var tags = $.trim($("input[name=" + name + "]").val());
if (tags !== "") {
tags = tags.split(",");
} else {
tags = new Array();
}
var tag = $.trim($("#tpt_input").val());
if (tag !== '' && $.inArray(tag, tags) === -1) {
tags.push(tag);
$("#tpt_pre a:contains('" + tag + "')").addClass("selected");
}
$("#tpt_sel").children('span').empty();
$.each(tags, function(k, v) {
$("#tpt_sel").children('span').append('<a href="javascript:;">' + v + '<em></em></a>');
});
$("input[name=" + name + "]").val(tags.join(","));
$("#tpt_input").val('');
});
$("#tpt_pre").on('click', 'a:not(.selected)', function() {
var name = "keywords";
var tags = $.trim($("input[name=" + name + "]").val());
if (tags !== "") {
tags = tags.split(",");
} else {
tags = new Array();
}
var tag = $.trim($(this).text());
if (tag !== '' && $.inArray(tag, tags) === -1) {
tags.push(tag);
}
$("#tpt_sel").children('span').empty();
$.each(tags, function(k, v) {
$("#tpt_sel").children('span').append('<a href="javascript:;">' + v + '<em></em></a>');
});
$("input[name=" + name + "]").val(tags.join(","));
$(this).addClass('selected');
});
});