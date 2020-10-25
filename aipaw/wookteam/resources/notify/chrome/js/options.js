var configLists = $A.jsonParse($A.getStorage("configLists"), {});
var html = '';

for (var index in configLists) {
    if (!configLists.hasOwnProperty(index)) {
        continue;
    }
    const config = configLists[index];
    html += '<li>\n' +
        '            <label>\n' +
        '                <input type="checkbox" value="disabled" class="checkbox" name="' + config.hostname + '" ' + (config.disabled === true ? 'checked="checked"' : '') + '> 禁用 (' + config.hostname + ')\n' +
        '            </label>\n' +
        '        </li>';
    $("#lists").html(html);
}

if (html == '') {
    $("#lists").html('没有相关的记录！');
} else {
    $("input[class=checkbox]").on('change', function () {
        $A.updateConfigLists($(this).attr("name"), {
            disabled: $(this).is(':checked')
        });
    });
}
