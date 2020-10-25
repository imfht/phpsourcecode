/**
 * 数字输出
 * @param number
 * @param decimals
 * @param dec_point
 * @param thousands_sep
 * @returns {jQuery|The|string}
 */
function erpNumber(number, decimals, dec_point, thousands_sep) {
    return $.number(number, decimals, '.', '');
}

/**
 * 地区级联
 */
/* 地区选择函数 */
function regionInit(divId)
{
    $("#" + divId + " > select").change(regionChange); // select的onchange事件
}

function regionChange()
{

    // 删除后面的select
    $(this).nextAll("select").remove();
    // 计算当前选中到id和拼起来的name
    var selects = $(this).siblings("select").addBack();
    var id = 0;
    var i;
    var names = new Array();
    for (i = 0; i < selects.length; i++)
    {
        sel = selects[i];
        if (sel.value > 0)
        {
            id = sel.value;
            name = sel.options[sel.selectedIndex].text;
            names.push(name);
        }
    }
    $(".region_ids").val(id);
    $(".region_names").val(names.join(""));
    // ajax请求下级地区
    if (this.value > 0)
    {
        var _self = this;
        var url = SITE_REGION_URL;
        $.post(url, {'region_id':this.value}, function(data){
                if (data)
                {
                    if (data.length > 0)
                    {
                        $("<select class='db_show_area' style='height:30px;line-height: 30px;margin-left: 5px;'><option>"+AREA_SELECT_LANG+"</option></select>").change(regionChange).insertAfter(_self);
                        var data  = data;
                        for (i = 0; i < data.length; i++)
                        {
                            $(_self).next("select").append("<option value='" + data[i].region_id + "'>" + data[i].region_name + "</option>");
                        }
                    }
                }
            },
            'json');
    }
}

function regionEdit()
{
    $("#show_address_area").show();
    $(".show_region_value").hide();
}