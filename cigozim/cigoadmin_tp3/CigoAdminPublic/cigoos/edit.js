$(function () {
    cigoEditInit();
});

function cigoEditInit(pNode) {
    ((undefined === pNode) || ('' === pNode))
        ? pNode = $('body')
        : (
            (pNode instanceof jQuery)
                ? false
                : pNode = $(pNode)
        );

    pNode.find('.cigo-edit.item-input').each(function () {
        cigoEditCreateInput($(this));
    });
    pNode.find('.cigo-edit.item-input-multi').each(function () {
        cigoEditCreateInputMulti($(this));
    });
    pNode.find('.cigo-edit.item-radio').each(function () {
        cigoEditCreateRadio($(this));
    });
    pNode.find('.cigo-edit.item-checkbox').each(function () {
        cigoEditCreateCheckbox($(this));
    });
    pNode.find('.cigo-edit.item-datetimepicker').each(function () {
        cigoEditCreateDateTimePicker($(this));
    });
    pNode.find('.cigo-edit.item-select').each(function () {
        cigoEditCreateSelect($(this));
    });
    pNode.find('.cigo-edit.item-select-cascade').each(function () {
        cigoEditCreateSelectCascade($(this));
    });
    pNode.find('.cigo-edit.item-textarea').each(function () {
        cigoEditCreateTextarea($(this));
    });
    pNode.find('.cigo-edit.item-editor-ueditor').each(function () {
        cigoEditCreateUEditor($(this));
    });
    pNode.find('.cigo-edit.item-editor-ckeditor').each(function () {
        cigoEditCreateCkEditor($(this));
    });
    pNode.find('.cigo-edit.item-img-single').each(function () {
        cigoEditCreateImgSingle($(this));
    });
    pNode.find('.cigo-edit.item-img-multi').each(function () {
        cigoEditCreateImgMulti($(this));
    });
    pNode.find('.cigo-edit.item-img-show').each(function () {
        cigoEditCreateImgShow($(this));
    });
    pNode.find('.cigo-edit.item-video-single').each(function () {
        cigoEditCreateVideoSingle($(this));
    });
    pNode.find('.cigo-edit.item-file-single').each(function () {
        cigoEditCreateFileSingle($(this));
    });
}

function cigoEditCreateInput(viewContainer) {
    var content = new Array();
    var label = viewContainer.attr('cigo-edit-label');
    var type = viewContainer.attr('cigo-edit-type');
    var disabled = viewContainer.attr('cigo-edit-disabled');
    var readonly = viewContainer.attr('cigo-edit-readonly');
    var value = viewContainer.attr('cigo-edit-value');
    var cls = viewContainer.attr('cigo-edit-class');
    var style = viewContainer.attr('cigo-edit-style');
    var placeHolder = viewContainer.attr('cigo-edit-placeholder');
    var name = viewContainer.attr('cigo-edit-name');
    var helpBLock = viewContainer.attr('cigo-edit-helpblock');

    content.push(
        ((label != undefined) ? '<label>' + label + '</label>' : '') +
        '<div class="controls">' +
        '   <input type="' + ((type != undefined && type != '') ? type : 'text') + '" ' +
        '       ' + ((disabled != undefined) ? 'disabled="disabled" ' : ' ') +
        '       ' + ((readonly == 'readonly') ? 'readonly ' : ' ') +
        '       ' + ((value != undefined && value != '') ? 'value="' + value + '" ' : ' ') +
        '       ' + ((cls != undefined && cls != '') ? 'class="' + cls + '" ' : '') +
        '       ' + ((style != undefined && style != '') ? 'style="' + style + '" ' : ' ') +
        '       ' + ((placeHolder != undefined && placeHolder != '') ? 'placeholder="' + placeHolder + '" ' : ' ') +
        '       ' + ((name != undefined && name != '') ? 'name="' + name + '" ' : '') +
        '   />' +
        '   ' + ((helpBLock != undefined && helpBLock != '') ? '<p class="help-block">' + helpBLock + '</p>' : '') +
        '</div>'
    );

    viewContainer.html(content.join(''));
}

function cigoEditCreateInputMulti(viewContainer) {
    var content = new Array();
    var viewId = getRandStr() + (new Date().getTime());
    var label = viewContainer.attr('cigo-edit-label');
    var value = viewContainer.attr('cigo-edit-value');
    var labelImgList = viewContainer.attr('cigo-edit-label-img-list');
    var helpBLock = viewContainer.attr('cigo-edit-helpblock');

    content.push(
        '<div id="' + viewId + '" class="panel panel-default">' +
        '   <div class="panel-heading"> ' +
        '       ' + label +
        '       <span class="add-new glyphicon glyphicon-plus-sign" style="cursor: pointer" title="添加新项"></span> ' +
        '   </div> ' +
        '   <div class="panel-body"> ' +
        '   </div> ' +
        '   ' + ((helpBLock != undefined && helpBLock != '') ? '<p class="help-block">&nbsp;&nbsp;' + helpBLock + '</p>' : '') +
        '</div>'
    );
    viewContainer.html(content.join(''));
    $('#' + viewId).on('click', '.add-new', function () {
        inputMultiAddNew(viewContainer, viewId, '', labelImgList);
    });

    if (value != undefined && value != '') {
        value = eval('(' + value + ')');
        $.each(value, function (key, dataItem) {
            return inputMultiAddNew(viewContainer, viewId, dataItem, labelImgList);
        });
    }
}

function inputMultiAddNew(viewContainer, viewId, itemValue, labelImgList) {
    var type = viewContainer.attr('cigo-edit-type');
    var cls = viewContainer.attr('cigo-edit-class');
    var placeHolder = viewContainer.attr('cigo-edit-placeholder');
    var name = viewContainer.attr('cigo-edit-name');
    var itemId = getRandStr() + (new Date().getTime());

    var itemIndex = $('#' + viewId).find(".input-group").length;

    if (labelImgList != undefined && labelImgList != '') {
        labelImgList = eval('(' + labelImgList + ')');
    }

    $('#' + viewId).find('.panel-body').append(
        '<div id="' + itemId + '" class="input-group" style="margin-bottom: 8px;">' +
        '   ' + (
            (isArray(labelImgList) && labelImgList.length > itemIndex)
                ? ' <span class="input-group-addon" style="padding: 0px;">' +
                '       <img style="width: 25px;height: 25px;" src="' + labelImgList[itemIndex] + '" />' +
                '   </span>'
                : ''
        ) +
        '   <input type="' + ((type != undefined && type != '') ? type : 'text') + '" ' +
        '       ' + ((itemValue != undefined && itemValue != '') ? 'value="' + itemValue + '" ' : ' ') +
        '       ' + ((cls != undefined && cls != '') ? 'class="' + cls + '" ' : '') +
        '       ' + ((placeHolder != undefined && placeHolder != '') ? 'placeholder="' + placeHolder + '" ' : ' ') +
        '       ' + ((name != undefined && name != '') ? 'name="' + name + '[]" ' : '') +
        '   />' +
        '   <div class="input-group-addon">' +
        '      <span class="minus-this glyphicon glyphicon-minus-sign" style="cursor: pointer" title="移除当前项"></span>' +
        '   </div>' +
        '</div>'
    )
    ;

    $('#' + itemId).on('click', '.minus-this', function () {
        $('#' + itemId).remove();
        checkMultiInputNum(viewContainer, viewId);
    });

    return checkMultiInputNum(viewContainer, viewId);
}

function checkMultiInputNum(viewContainer, viewId) {
    var numLimit = viewContainer.attr('cigo-edit-input-num-limit');
    var inputNum = $('#' + viewId).find('input').length;

    if (inputNum < numLimit) {
        $('#' + viewId).find('.add-new').show('fast');
        return true;
    } else {
        $('#' + viewId).find('.add-new').hide();
        return false;
    }
}

function cigoEditCreateRadio(viewContainer) {
    var content = new Array();

    var viewId = getRandStr() + (new Date().getTime());
    var type = viewContainer.attr('cigo-edit-type');
    var label = viewContainer.attr('cigo-edit-label');
    var name = viewContainer.attr('cigo-edit-name');
    var value = viewContainer.attr('cigo-edit-value');
    var helpBLock = viewContainer.attr('cigo-edit-helpblock');
    var options = viewContainer.attr('cigo-edit-data-radio-options');
    var valueChangedFun = viewContainer.attr('cigo-edit-fun-value-changed');

    content.push(
        ((label != undefined) ? '<label>' + label + '</label>' : '<label></label>')
    );
    if (options != undefined && options != '') {
        options = eval('(' + options + ')');
        content.push(
            '<div id="' + viewId + '" class="controls" style="padding-left: 10px;">'
        );
        $.each(options, function (key, dataItem) {
            switch (type) {
                case 'landscape':
                    content.push(
                        '<label class="radio-inline ' +
                        '   ' + ('disabled' in dataItem ? ' disabled ' : '') +
                        '">' +
                        '   <input type="radio" ' +
                        '       ' + ((name != undefined && name != '') ? 'name="' + name + '" ' : '') +
                        '       value="' + dataItem['id'] + '" ' +
                        (
                            (value != undefined && value != '') ?
                                (dataItem['id'] == value ? ' checked = "checked" ' : '') :
                                (key == 0 ? '  checked = "checked" ' : '')
                        ) +
                        '   ' + ('disabled' in dataItem ? ' disabled = "disabled" ' : '') +
                        '   />' + dataItem['text'] +
                        '</label>'
                    );
                    break;
                case 'portrait':
                    content.push(
                        '<div class="radio ' +
                        '   ' + ('disabled' in dataItem ? ' disabled ' : '') +
                        '">' +
                        '   <label>' +
                        '       <input type="radio" ' +
                        '           ' + ((name != undefined && name != '') ? 'name="' + name + '" ' : '') +
                        '           value="' + dataItem['id'] + '" ' +
                        (
                            (value != undefined && value != '') ?
                                (dataItem['id'] == value ? ' checked = "checked" ' : '') :
                                (key == 0 ? '  checked = "checked" ' : '')
                        ) +
                        '   ' + ('disabled' in dataItem ? ' disabled = "disabled" ' : '') +
                        '/>' + dataItem['text'] +
                        '   </label>' +
                        '</div>'
                    );
                    break;
            }
        });
        content.push(
            '</div>'
        );
    }
    content.push(
        '<div style="clear: both;"></div>' +
        ((helpBLock != undefined && helpBLock != '') ? '<p class="help-block">' + helpBLock + '</p>' : '')
    );
    viewContainer.html(content.join(''));

    //判断是否绑定切换监听函数
    if (valueChangedFun != undefined && valueChangedFun != '') {
        valueChangedFun = eval(valueChangedFun);
        //绑定切换事件
        $('#' + viewId).on('click', 'input', function () {
            return valueChangedFun($(this).val(), false, $(this), viewContainer);
        });
        //通知默认选择
        $('#' + viewId).find('input').each(function () {
            if ($(this).is(':checked')) {
                return valueChangedFun($(this).val(), true, $(this), viewContainer);
            }
        });
    }
}

function cigoEditCreateCheckbox(viewContainer) {
    var content = new Array();

    var viewId = getRandStr() + (new Date().getTime());
    var type = viewContainer.attr('cigo-edit-type');
    var label = viewContainer.attr('cigo-edit-label');
    var name = viewContainer.attr('cigo-edit-name');
    var value = viewContainer.attr('cigo-edit-value');
    var helpBLock = viewContainer.attr('cigo-edit-helpblock');
    var options = viewContainer.attr('cigo-edit-data-checkbox-options');
    var valueChangedFun = viewContainer.attr('cigo-edit-fun-value-changed');

    content.push(
        ((label != undefined) ? '<label>' + label + '</label>&nbsp;&nbsp;' : '')
    );
    if (options != undefined && options != '') {
        options = eval('(' + options + ')');
        if (value != undefined && value != '') {
            value = eval('(' + value + ')');
        }
        content.push(
            '<div id="' + viewId + '" style="padding-left: 10px;">'
        );

        $.each(options, function (key, dataItem) {
            switch (type) {
                case 'landscape':
                    content.push(
                        '<label class="checkbox-inline ' +
                        '   ' + ('disabled' in dataItem ? ' disabled ' : '') +
                        '">' +
                        '   <input type="checkbox" ' +
                        '       ' + ((name != undefined && name != '') ? 'name="' + name + '[]" ' : '') +
                        '       value="' + dataItem['id'] + '" ' +
                        '       data-text="' + dataItem['text'] + '" ' +
                        (
                            (value != undefined && value != '') ?
                                (($.inArray(dataItem['id'], value) > -1) ? ' checked = "checked" ' : '') : ''
                        ) +
                        '   ' + ('disabled' in dataItem ? ' disabled = "disabled" ' : '') +
                        '   />' + dataItem['text'] +
                        '</label>'
                    );
                    break;
                case 'portrait':
                    content.push(
                        '<div class="checkbox ' +
                        '   ' + ('disabled' in dataItem ? ' disabled ' : '') +
                        '">' +
                        '   <label>' +
                        '       <input type="checkbox" ' +
                        '           ' + ((name != undefined && name != '') ? 'name="' + name + '[]" ' : '') +
                        '           value="' + dataItem['id'] + '" ' +
                        '           data-text="' + dataItem['text'] + '" ' +
                        (
                            (value != undefined && value != '') ?
                                (($.inArray(dataItem['id'], value) > -1) ? ' checked = "checked" ' : '') : ''
                        ) +
                        '   ' + ('disabled' in dataItem ? ' disabled = "disabled" ' : '') +
                        '/>' + dataItem['text'] +
                        '   </label>' +
                        '</div>'
                    );
                    break;
            }
        });
        content.push(
            '</div>'
        );
    }
    content.push(
        ((helpBLock != undefined && helpBLock != '') ? '<p class="help-block">' + helpBLock + '</p>' : '')
    );

    viewContainer.html(content.join(''));

    //绑定事件
    if (valueChangedFun != undefined && valueChangedFun != '') {
        valueChangedFun = eval(valueChangedFun);
        $('#' + viewId).on('click', 'input', function () {
            var selectInfo = getChecBoxSelectList(viewId);
            valueChangedFun(selectInfo.idList, selectInfo.textList);
        });

        //通知默认选择
        var selectInfo = getChecBoxSelectList(viewId);
        valueChangedFun(selectInfo.idList, selectInfo.textList);
    }
}

function getChecBoxSelectList(viewId) {
    var selectInfo = {
        idList: new Array(),
        textList: new Array()
    };
    $('#' + viewId).find('input').each(function () {
        if ($(this).is(':checked')) {
            selectInfo.idList.push($(this).val());
            selectInfo.textList.push($(this).data('text'));
        }
    });
    return selectInfo;
}

function cigoEditCreateDateTimePicker(viewContainer) {
    var content = new Array();

    var viewId = getRandStr() + (new Date().getTime());
    var label = viewContainer.attr('cigo-edit-label');
    var value = viewContainer.attr('cigo-edit-value');
    var cls = viewContainer.attr('cigo-edit-class');
    var style = viewContainer.attr('cigo-edit-style');
    var placeHolder = viewContainer.attr('cigo-edit-placeholder');
    var name = viewContainer.attr('cigo-edit-name');
    var changeDateFunc = viewContainer.attr('cigo-edit-change-date-func');
    var helpBLock = viewContainer.attr('cigo-edit-helpblock');
    var format = viewContainer.attr('cigo-edit-datetime-format');
    var minView = viewContainer.attr('cigo-edit-datetime-minview');
    var readonly = viewContainer.attr('cigo-edit-readonly');
    content.push(
        ((label != undefined) ? '<label>' + label + '</label>' : '<label></label>') +
        '<div class="controls">' +
        '   <input id="' + viewId + '" type="text" ' +
        '       ' + ((value != undefined && value != '') ? 'value="' + value + '" ' : ' ') +
        '       ' + ((readonly == 'readonly') ? 'readonly ' : ' ') +
        '       ' + ((cls != undefined && cls != '') ? 'class="' + cls + '" ' : '') +
        '       ' + ((style != undefined && style != '') ? 'style="' + style + '" ' : ' ') +
        '       ' + ((placeHolder != undefined && placeHolder != '') ? 'placeholder="' + placeHolder + '" ' : ' ') +
        '       ' + ((name != undefined && name != '') ? 'name="' + name + '" ' : '') +
        '   />' +
        '   ' + ((helpBLock != undefined && helpBLock != '') ? '<p class="help-block">' + helpBLock + '</p>' : '') +
        '</div>'
    );
    viewContainer.html(content.join(''));
    var datetimePicker = $('#' + viewId).datetimepicker({
        language: 'zh-CN',
        format: (format != undefined && format != '') ? format : 'yyyy-mm-dd hh:ii',
        minuteStep: 1,
        minView: (minView != undefined && minView != '') ? minView : 'month',
        autoclose: true,
        todayBtn: true
    });
    if (undefined !== changeDateFunc && '' !== changeDateFunc) {
        changeDateFunc = eval(changeDateFunc);
        datetimePicker.on('changeDate', function (ev) {
            var dateTime = $("#" + viewId).val();
            changeDateFunc(datetimePicker, dateTime);
        });
    }
}

function cigoEditCreateSelect(viewContainer) {
    var content = new Array();

    var viewId = getRandStr() + (new Date().getTime());
    var label = viewContainer.attr('cigo-edit-label');
    var name = viewContainer.attr('cigo-edit-name');
    var value = viewContainer.attr('cigo-edit-value');
    var noSearchForResult = viewContainer.attr('cigo-edit-select-no-search-for-result');
    var placeHolder = viewContainer.attr('cigo-edit-placeholder');
    var helpBLock = viewContainer.attr('cigo-edit-helpblock');
    var width = viewContainer.attr('cigo-edit-select-width');
    var options = viewContainer.attr('cigo-edit-data-select-options');
    var createItemFun = viewContainer.attr('cigo-edit-fun-createitem');
    var itemChangeFun = viewContainer.attr('cigo-edit-fun-item-change');
    var allowClear = viewContainer.attr('cigo-edit-select-allowClear');
    var clearDefault = viewContainer.attr('cigo-edit-select-clearDefault');

    content.push(
        ((label != undefined) ? '<label>' + label + '</label>&nbsp;&nbsp;' : '<label></label>') +
        '<select id="' + viewId + '" ' +
        '   ' + ((name != undefined && name != '') ? 'name="' + name + '" ' : '') +
        '>' +
        '</select>' +
        ((helpBLock != undefined && helpBLock != '') ? '<p class="help-block">' + helpBLock + '</p>' : '')
    );

    viewContainer.html(content.join(''));

    var config = {};
    config.data = (options != undefined && options != '') ? eval('(' + options + ')') : [];
    config.placeholder = (placeHolder != undefined && placeHolder != '') ? placeHolder : "--请选择--";
    config.allowClear = (allowClear != undefined);
    config.width = ((width != undefined && width != '') ? width : 200) + 'px';
    if (noSearchForResult != undefined) {
        config.minimumResultsForSearch = -1;
    }
    if (createItemFun != undefined && createItemFun != '') {
        config.templateResult = eval(createItemFun);
    }
    var select = $('#' + viewId).select2(config);
    if (value != undefined && '' != value) {
        select.val([value]).trigger('change');
    } else if (clearDefault != undefined) {
        select.val([]).trigger('change');
    }

    if (itemChangeFun != undefined && itemChangeFun != '') {
        itemChangeFun = eval(itemChangeFun);
        $('#' + viewId).on("change", function (e) {
            var optionItemData = $(this).select2('data');
            itemChangeFun(optionItemData, viewContainer);
        });

        //触发默认值
        var optionItemData = $('#' + viewId).select2('data');
        itemChangeFun(optionItemData, viewContainer);
    }
}

function cigoEditCreateSelectCascade(viewContainer) {
    var content = new Array();

    var label = viewContainer.attr('cigo-edit-label');
    var valueViewId = getRandStr() + (new Date().getTime());
    var name = viewContainer.attr('cigo-edit-name');
    var value = viewContainer.attr('cigo-edit-value');
    var selectListContainerViewId = getRandStr() + (new Date().getTime());
    var placeHolder = viewContainer.attr('cigo-edit-placeholder');
    var helpBLock = viewContainer.attr('cigo-edit-helpblock');
    var width = viewContainer.attr('cigo-edit-select-width');
    var options = viewContainer.attr('cigo-edit-data-select-options');
    var allowClear = viewContainer.attr('cigo-edit-select-allowClear');
    content.push(
        ((label != undefined) ? '<label>' + label + '</label>&nbsp;&nbsp;' : '') +
        '<input type="hidden" id="' + valueViewId + '" ' + ((name != undefined && name != '') ? 'name="' + name + '" ' : '') + '/>' +
        '<div id="' + selectListContainerViewId + '" class="controls"></div>' +
        ((helpBLock != undefined && helpBLock != '') ? '<p class="help-block">' + helpBLock + '</p>' : '')
    );
    viewContainer.html(content.join(''));

    if (options != undefined && options != '') {
        options = eval('(' + options + ')');
        var selectListContainerView = $('#' + selectListContainerViewId);
        selectListContainerView.attr('cigo-edit-init-flag', ((value != undefined && value != '') ? '1' : ''));

        cigoEditCreateSelectCascadeItem(selectListContainerView, $('#' + valueViewId),
            ((value != undefined && value != '') ? value : ''), placeHolder, allowClear,
            width, options, 1);
    }
}

function cigoEditCreateSelectCascadeItem(selectListContainer, valueView, value, placeHolder, allowClear, width, itemOptions, level) {
    var options = [];
    $.each(itemOptions, function (key, dataItem) {
        options.push({
            'id': dataItem.path + dataItem.id + ',',
            'text': dataItem.title
        });
    });
    if (options.length <= 0) {
        return;
    }
    var viewId = getRandStr() + (new Date().getTime());
    selectListContainer.append(
        '<select id="' + viewId + '"></select>'
    );
    var config = {};
    config.data = options
    config.placeholder = (placeHolder != undefined && placeHolder != '') ? placeHolder : "--请选择--";
    config.allowClear = (allowClear != undefined);
    config.width = ((width != undefined && width != '') ? width : 200) + 'px';
    var select = $('#' + viewId).select2(config);
    $('#' + viewId).on("change", function (e) {
        cigoEditSelectCascadeItemOnChange(selectListContainer, valueView, value, placeHolder, allowClear, width, itemOptions, viewId, level);
    });

    var initFlag = selectListContainer.attr('cigo-edit-init-flag');
    var currLevelValueIndex = cigoFindCharIndexFromStringByNum(value, ',', 0, level + 1);
    if (initFlag == '1' && currLevelValueIndex > 0) {
        select.val([value.substring(0, currLevelValueIndex + 1)]).trigger('change');
    } else {
        cigoEditSelectCascadeItemOnChange(selectListContainer, valueView, value, placeHolder, allowClear, width, itemOptions, viewId, level);
    }
}

function cigoEditSelectCascadeItemOnChange(selectListContainer, valueView, value, placeHolder, allowClear, width, itemOptions, ctrlSelectViewId, level) {
    var ctrlSelectView = $('#' + ctrlSelectViewId);
    //删除后续关联子列表
    var selectedIndex = document.getElementById(ctrlSelectViewId).selectedIndex;
    ctrlSelectView.next().nextAll().remove();

    //保存当前项目值
    var ctrlSelectedOptionData = ctrlSelectView.select2('data');
    if (ctrlSelectedOptionData.length > 0) {
        //记录当前值
        valueView.val($('#' + ctrlSelectViewId).select2('data')[0].id);
        //渲染关联子列表
        if ('subList' in itemOptions[selectedIndex]) {
            cigoEditCreateSelectCascadeItem(selectListContainer, valueView, value, placeHolder, allowClear, width, itemOptions[selectedIndex]['subList'], level + 1);
        } else {
            selectListContainer.removeAttr('cigo-edit-init-flag');
        }
    } else {
        var currValue = valueView.val();
        var currLevelValueIndex = cigoFindCharIndexFromStringByNum(currValue, ',', 0, level);
        if (currLevelValueIndex > 0) {
            valueView.val(currValue.substring(0, currLevelValueIndex + 1));
        }
        selectListContainer.removeAttr('cigo-edit-init-flag');
    }
}

function cigoEditCreateTextarea(viewContainer) {
    var content = new Array();

    var label = viewContainer.attr('cigo-edit-label');
    var name = viewContainer.attr('cigo-edit-name');
    var value = viewContainer.attr('cigo-edit-value');
    var rows = viewContainer.attr('cigo-edit-rows');
    var cls = viewContainer.attr('cigo-edit-class');
    var style = viewContainer.attr('cigo-edit-style');
    var helpBLock = viewContainer.attr('cigo-edit-helpblock');
    var placeHolder = viewContainer.attr('cigo-edit-placeholder');

    content.push(
        '<div class="panel panel-default" ' +
        '   ' + ((style != undefined && style != '') ? 'style="' + style + '">' : '>') +
        '   ' + ((label != undefined) ? '<div class="panel-heading">' + label + '</div>' : '') +
        '   <div class="panel-body" style="padding: 6px;">' +
        '       <textarea ' +
        '           ' + ((name != undefined && name != '') ? 'name="' + name + '" ' : '') +
        '           ' + ((rows != undefined && rows != '') ? 'rows="' + rows + '" ' : '') +
        '           ' + ((cls != undefined && cls != '') ? 'class="' + cls + '" ' : '') +
        '           ' + ((placeHolder != undefined && placeHolder != '') ? 'placeholder="' + placeHolder + '" ' : ' ') +
        '           >' + ((value != undefined && value != '') ? value : '') + '</textarea>' +
        '   </div>' +
        '</div>' +
        ((helpBLock != undefined && helpBLock != '') ? '<p class="help-block">' + helpBLock + '</p>' : '')
    );

    viewContainer.html(content.join(''));
}

function cigoEditCreateUEditor(viewContainer) {
    var content = new Array();

    var viewId = getRandStr() + (new Date().getTime());
    var label = viewContainer.attr('cigo-edit-label');
    var name = viewContainer.attr('cigo-edit-name');
    var value = viewContainer.attr('cigo-edit-value');
    var style = viewContainer.attr('cigo-edit-style');
    var helpBLock = viewContainer.attr('cigo-edit-helpblock');

    content.push(
        '<div class="panel panel-default" ' +
        '   ' + ((style != undefined && style != '') ? 'style="' + style + '">' : '>') +
        '   <div class="panel-heading" data-toggle="collapse" href="#' + viewId + '"> ' +
        '       ' + label + ' (点击展开编辑) ' +
        '       <span class="glyphicon glyphicon-plus-sign" aria-hidden="true"></span> ' +
        '   </div> ' +
        '   <div id="' + viewId + '" class="panel-collapse collapse"> ' +
        '       <div class="panel-body"> ' +
        '           <script id="' + viewId + '_editor" type="text/plain"' +
        '           ' + ((name != undefined && name != '') ? 'name="' + name + '" ' : '') +
        '           >' + ((value != undefined && value != '') ? value : '') + '</script> ' +
        '       </div> ' +
        ((helpBLock != undefined && helpBLock != '') ? '<p class="help-block" style="padding-left: 15px;">' + helpBLock + '</p>' : '') +
        '   </div> ' +
        '</div>'
    );
    viewContainer.html(content.join(''));

    //加载Ueditor
    var editorConfig = viewContainer.attr('cigo-edit-editor-config');
    if (editorConfig != undefined && editorConfig != '') {
        editorConfig = eval('(' + editorConfig + ')');
        UE.getEditor(viewId + '_editor', editorConfig);
    } else {
        UE.getEditor(viewId + '_editor', {
            initialFrameHeight: 250,
            autoHeightEnabled: false
        });
    }
}

function cigoEditCreateCkEditor(viewContainer) {
    var content = new Array();

    var viewId = getRandStr() + (new Date().getTime());
    var label = viewContainer.attr('cigo-edit-label');
    var name = viewContainer.attr('cigo-edit-name');
    var value = viewContainer.attr('cigo-edit-value');
    var style = viewContainer.attr('cigo-edit-style');
    var helpBLock = viewContainer.attr('cigo-edit-helpblock');

    content.push(
        '<div class="panel panel-default" ' +
        '   ' + ((style != undefined && style != '') ? 'style="' + style + '">' : '>') +
        '   <div class="panel-heading" data-toggle="collapse" href="#' + viewId + '"> ' +
        '       ' + label + ' (点击展开编辑) ' +
        '       <span class="glyphicon glyphicon-plus-sign" aria-hidden="true"></span> ' +
        '   </div> ' +
        '   <div id="' + viewId + '" class="panel-collapse collapse"> ' +
        '       <div class="panel-body"> ' +
        '           <textarea id="' + viewId + '_editor" ' +
        '           ' + ((name != undefined && name != '') ? 'name="' + name + '" ' : '') +
        '           >' + ((value != undefined && value != '') ? value : '') + '</textarea> ' +
        '       </div> ' +
        ((helpBLock != undefined && helpBLock != '') ? '<p class="help-block" style="padding-left: 15px;">' + helpBLock + '</p>' : '') +
        '   </div> ' +
        '</div>'
    );

    viewContainer.html(content.join(''));

    //加载CkEditor
    var editorConfig = viewContainer.attr('cigo-edit-editor-config');
    if (editorConfig != undefined && editorConfig != '') {
        editorConfig = eval('(' + editorConfig + ')');
        CKEDITOR.replace(viewId + '_editor', editorConfig);
    } else {
        CKEDITOR.replace(viewId + '_editor');
    }
}

/*******************************************************************************************************************************/

function cigoEditCreateImgSingle(viewContainer) {
    var content = [];
    var viewId = getRandStr() + (new Date().getTime());
    var label = viewContainer.attr('cigo-edit-label');
    var value = viewContainer.attr('cigo-edit-value');
    var helpBLock = viewContainer.attr('cigo-edit-helpblock');
    var name = viewContainer.attr('cigo-edit-name');
    var imgSrc = viewContainer.attr('cigo-edit-img-src');
    var imgChangeFunc = viewContainer.attr('cigo-img-change-func');

    content.push(
        ((undefined !== label) ? '<label>' + label + '</label>' : '') +
        '<div id="' + viewId + '" class="cigo-img" title="点击修改"' +
        '   ' + ((undefined !== value && value !== '') ? 'cigo-edit-value="' + value + '" ' : ' ') + '>' +
        '   <a href="#" class="thumbnail" onclick="return false;">' +
        '       ' + ((imgSrc === undefined || imgSrc === '') ?
        '   <i class="cigo-iconfont cigo-icon-tianjiatupian"></i>' :
        '   <img src="' + imgSrc + '?' + Math.random() + '" alt="西谷后台" />') +
        '   </a>' +
        '   <input type="hidden" ' +
        '       ' + ((undefined !== value && value !== '') ? 'value="' + value + '" ' : ' ') +
        '       ' + ((undefined !== name && name !== '') ? 'name="' + name + '" ' : '') +
        '   />' +
        '</div>' +
        ((undefined !== helpBLock && helpBLock !== '') ? '<p class="help-block">' + helpBLock + '</p>' : '')
    );
    viewContainer.html(content.join(''));
    //例化图片上传插件进行上传
    var view = $('#' + viewId);
    bindPlUploadImg(view, viewContainer, function (data) {
        cigoEditUpdateImgItem(view.attr('id'), data.id, data.path);

        if (undefined !== imgChangeFunc && '' !== imgChangeFunc) {
            imgChangeFunc = eval(imgChangeFunc);
            imgChangeFunc(data.id, data.path);
        }
    });
}

function bindPlUploadImg(browseView, bindConfigView, successCallbackFunc) {
    //初始化截图上传插件
    var uploadingTipIndex = -1;
    browseView.plUpload({
        'tag_browse_btn': browseView.attr('id'),
        'uploadUrl': Think.CONTROLLER + Think.DEEP + 'upload' + Think.DEEP + 'sessionid' + Think.DEEP + Think.SESSION_ID,
        'filters': {
            max_file_size: '5mb',
            mime_types: [{title: "选择图片：", extensions: "jpg,jpeg,png,gif"}]
        },
        'fileAdded': function (uploader, upArgs) {
            makeUploadImgArgs(uploader, bindConfigView, upArgs, function () {
                uploadingTipIndex = cigoLayer.load();
                uploader.start();
            });
        },
        'completedUpload': function (response) {
            //解析上传结果
            var result = $.parseJSON(response.response);
            if (result.status) {
                successCallbackFunc(result.data);
                // 提示消息
                cigoLayer.msg(result.msg);
            } else {
                cigoLayer.msg(result.msg);
            }
            cigoLayer.close(uploadingTipIndex);
        },
        'pluploadError': function (uploader, error) {
            cigoLayer.msg('上传错误 ' + error.code + ':' + error.message);
        }
    });
}

function makeUploadImgArgs(uploader, bindConfigView, upArgs, completeCallBackFunc) {
    //获取当前绑定图片上传配置参数
    getBindUploadImgArgs(bindConfigView, upArgs);
    //定义上传文件类型
    upArgs.fileType = 'Img';
    // 分析参数获取方式，获取其它图片处理参数
    var imgArgsType = bindConfigView.attr('cigo-edit-img-args-type');
    switch (imgArgsType) {
        case 'manual-select'://手动选择
            openGetImgArgsByManualPage(bindConfigView, upArgs, completeCallBackFunc);
            break;
        case 'manual-tools-crop-common'://手动截图(普通)插件工具操作
            openGetImgArgsByToolsCropCommonPage(uploader, bindConfigView, upArgs, completeCallBackFunc);
            break;
        case 'common'://常规直接配置参数型
        default:
            completeCallBackFunc();
            break;
    }
}

function getBindUploadImgArgs(bindConfigView, upArgs) {

    //TODO 注意压缩和剪切不能同时生效，如果都配置只生效压缩图片
    //**********************
    //压缩图片
    getBindUploadImgArgsForThumb(bindConfigView, upArgs);
    //剪切图片
    getBindUploadImgArgsForCrop(bindConfigView, upArgs);
    //**********************

    //TODO 水印类可以同压缩或剪切共存
    //**********************
    //图片水印
    getBindUploadImgArgsForWater(bindConfigView, upArgs);
    //字符串水印
    getBindUploadImgArgsForText(bindConfigView, upArgs);
    //**********************
}

function getBindUploadImgArgsForThumb(bindConfigView, upArgs) {
    //获取记录配置参数
    var imgCtrlTypeThumb = bindConfigView.attr('cigo-edit-img-ctrltype-thumb');
    var imgThumbConfig = bindConfigView.attr('cigo-edit-img-thumb-config');
    //检查记录配置参数
    if ((undefined !== imgCtrlTypeThumb)) {
        upArgs.thumb = {'ctrlTypeFlag': "thumb"};//保证thumb字段能够上传
        if ((undefined !== imgThumbConfig) && ('' !== imgThumbConfig)) {
            var config = eval('(' + imgThumbConfig + ')');
            if (
                (('width' in config) && '' !== config.width) &&
                (('height' in config) && '' !== config.width)
            ) {
                upArgs.thumb = {
                    'width': config.width,
                    'height': config.height
                };
                (('type' in config) && '' !== config.type)
                    ? upArgs.thumb.type = config.type
                    : false;
            }
        }
    }
}

function getBindUploadImgArgsForCrop(bindConfigView, upArgs) {
    //获取记录配置参数
    var imgCtrlTypeCrop = bindConfigView.attr('cigo-edit-img-ctrltype-crop');
    var imgCropConfig = bindConfigView.attr('cigo-edit-img-crop-config');
    //检查记录配置参数
    if ((undefined !== imgCtrlTypeCrop)) {
        upArgs.crop = {'ctrlTypeFlag': "crop"};//保证crop字段能够上传
        if ((undefined !== imgCropConfig) && ('' !== imgCropConfig)) {
            var config = eval('(' + imgCropConfig + ')');
            if (
                (
                    (('w' in config) && '' !== config.w) &&
                    (('h' in config) && '' !== config.h) &&
                    (('x' in config) && '' !== config.x) &&
                    (('y' in config) && '' !== config.y)
                ) ||
                (
                    (('cropWidth' in config) && '' !== config.cropWidth) &&
                    (('cropHeight' in config) && '' !== config.cropHeight)
                )
            ) {
                upArgs.crop = {};
                (('w' in config) && '' !== config.w)
                    ? upArgs.crop.w = config.w
                    : false;
                (('h' in config) && '' !== config.h)
                    ? upArgs.crop.h = config.h
                    : false;
                (('x' in config) && '' !== config.x)
                    ? upArgs.crop.x = config.x
                    : false;
                (('y' in config) && '' !== config.y)
                    ? upArgs.crop.y = config.y
                    : false;
                (('cropWidth' in config) && '' !== config.cropWidth)
                    ? upArgs.crop.cropWidth = config.cropWidth
                    : false;
                (('cropHeight' in config) && '' !== config.cropHeight)
                    ? upArgs.crop.cropHeight = config.cropHeight
                    : false;
                (('width' in config) && '' !== config.width)
                    ? upArgs.crop.width = config.width
                    : false;
                (('height' in config) && '' !== config.height)
                    ? upArgs.crop.height = config.height
                    : false;
            } else {

            }
        }
    }
}

function getBindUploadImgArgsForWater(bindConfigView, upArgs) {
    //获取记录配置参数
    var imgCtrlTypeWater = bindConfigView.attr('cigo-edit-img-ctrltype-water');
    var imgWaterConfig = bindConfigView.attr('cigo-edit-img-water-config');
    //检查记录配置参数
    if ((undefined !== imgCtrlTypeWater)) {
        upArgs.water = {'ctrlTypeFlag': 'water'};//保证water字段能够上传
        if ((undefined !== imgWaterConfig) && ('' !== imgWaterConfig)) {
            var config = eval('(' + imgWaterConfig + ')');
            (('locate' in config) && '' !== config.locate)
                ? upArgs.water.locate = config.locate
                : false;
            (('alpha' in config) && '' !== config.alpha)
                ? upArgs.water.alpha = config.alpha
                : false;
        }
    }
}

function getBindUploadImgArgsForText(bindConfigView, upArgs) {
    //获取记录配置参数
    var imgCtrlTypeText = bindConfigView.attr('cigo-edit-img-ctrltype-text');
    var imgTextConfig = bindConfigView.attr('cigo-edit-img-text-config');
    //检查记录配置参数
    if ((undefined !== imgCtrlTypeText)) {
        upArgs.text = {'ctrlTypeFlag': 'text'};//保证water字段能够上传
        if ((undefined !== imgTextConfig) && ('' !== imgTextConfig)) {
            var config = eval('(' + imgTextConfig + ')');
            (('text' in config) && '' !== config.text)
                ? upArgs.text.text = config.text
                : false;
            (('size' in config) && '' !== config.size)
                ? upArgs.text.size = config.size
                : false;
            (('color' in config) && '' !== config.color)
                ? upArgs.text.color = config.color
                : false;
            (('locate' in config) && '' !== config.locate)
                ? upArgs.text.locate = config.locate
                : false;
            (('offset' in config) && '' !== config.offset)
                ? upArgs.text.offset = config.offset
                : false;
            (('angle' in config) && '' !== config.angle)
                ? upArgs.text.angle = config.angle
                : false;
        }
    }
}

function openGetImgArgsByManualPage(bindConfigView, upArgs, completeCallBackFunc) {
    //检查是否存在配置项
    if (
        !('thumb' in upArgs) &&
        !('crop' in upArgs) &&
        !('water' in upArgs) &&
        !('text' in upArgs)
    ) {
        cigoLayer.msg('控件未配置图片操作，请检查配置项！！');
        return;
    }

    //开启配置界面
    cigoLayer.open({
        title: false,
        closeBtn: 0,
        type: 2,
        area: ['700px', '600px'],
        fix: true,
        scrollbar: true,
        maxmin: false,
        shade: [0.5, '#ffffff'],
        shadeClose: false,
        skin: 'layui-layer-rim',
        content: Think.CONTROLLER + Think.DEEP + 'imgArgsByManual',
        success: function (layero, index) {
            var frameId = layero.find('iframe')[0]['id'];
            $('#' + frameId)[0].contentWindow.initData(layero.attr('id'), index, bindConfigView, upArgs, function () {
                completeCallBackFunc();
            });
        }
    });
}

function openGetImgArgsByToolsCropCommonPage(uploader, bindConfigView, upArgs, completeCallBackFunc) {
    //检查是否存在配置项
    if (!('crop' in upArgs)) {
        cigoLayer.msg('控件未配置截图图片操作，请检查配置项！！');
        return;
    }

    //开启配置界面
    cigoLayer.open({
        title: false,
        closeBtn: 0,
        type: 2,
        area: ['700px', '600px'],
        fix: true,
        scrollbar: true,
        maxmin: false,
        shade: [0.5, '#ffffff'],
        shadeClose: false,
        skin: 'layui-layer-rim',
        content: Think.CONTROLLER + Think.DEEP + 'imgArgsByToolsCropCommon',
        success: function (layero, index) {
            var frameId = layero.find('iframe')[0]['id'];
            $('#' + frameId)[0].contentWindow.initData(layero.attr('id'), index, bindConfigView, upArgs, function () {
                previewImage(uploader.files[0], function (imgSrc, width, height) {
                    $('#' + frameId)[0].contentWindow.initJcrop(imgSrc, width, height);
                });
            }, function () {
                completeCallBackFunc();
            });
        }
    });
}

function previewImage(file, callback) {
    var preloader = new mOxie.Image();
    preloader.onload = function () {
        var imgsrc = (preloader.type == 'image/jpeg') ?
            preloader.getAsDataURL('image/jpeg', 80) :
            preloader.getAsDataURL();
        callback && callback(imgsrc, preloader.width, preloader.height);
        preloader.destroy();
        preloader = null;
    };
    preloader.load(file.getSource());
}

/*******************************************************************************************************************************/

function cigoEditCreateImgMulti(viewContainer) {
    var content = [];
    var label = viewContainer.attr('cigo-edit-label');
    if (label !== undefined && '' !== label) {
        content.push('<label>' + label + '</label><br/>');
    }
    // //图片列表
    var imgMultiConfig = viewContainer.attr('cigo-edit-data-img-multi-config');
    if (imgMultiConfig !== undefined && '' !== imgMultiConfig) {
        imgMultiConfig = eval('(' + imgMultiConfig + ')');
        var name = viewContainer.attr('cigo-edit-name');
        var value = viewContainer.attr('cigo-edit-value');
        if (value !== undefined && '' !== value) {
            value = eval('(' + value + ')');
        } else {
            value = false;
        }

        content.push('<div class="btn-group" role="group" aria-label="...">');
        $.each(imgMultiConfig, function (key, dataItem) {
            var imgViewId = getRandStr() + (new Date().getTime());
            var imgId = '';
            var imgSrc = '';
            if (value && (dataItem.name in value)) {
                imgId = value[dataItem.name]['img-id'];
                imgSrc = value[dataItem.name]['img-src'];
            }
            var ctrlType = '';
            if (('ctrlType' in dataItem) && ('' !== dataItem.ctrlType)) {
                ($.inArray('thumb', dataItem.ctrlType) >= 0)
                    ? ctrlType += ' cigo-edit-img-ctrltype-thumb '
                    : false;
                ($.inArray('crop', dataItem.ctrlType) >= 0)
                    ? ctrlType += ' cigo-edit-img-ctrltype-crop '
                    : false;
                ($.inArray('water', dataItem.ctrlType) >= 0)
                    ? ctrlType += ' cigo-edit-img-ctrltype-water '
                    : false;
                ($.inArray('text', dataItem.ctrlType) >= 0)
                    ? ctrlType += ' cigo-edit-img-ctrltype-text '
                    : false;
            }
            var config = "";
            if (('config' in dataItem) && ('' !== dataItem.config)) {
                (('thumb' in dataItem.config) && ('' !== dataItem.config.thumb))
                    ? config += " cigo-edit-img-thumb-config='" + JSON.stringify(dataItem.config.thumb) + "' "
                    : false;
                (('crop' in dataItem.config) && ('' !== dataItem.config.crop))
                    ? config += " cigo-edit-img-crop-config='" + JSON.stringify(dataItem.config.crop) + "' "
                    : false;
                (('water' in dataItem.config) && ('' !== dataItem.config.water))
                    ? config += " cigo-edit-img-water-config='" + JSON.stringify(dataItem.config.water) + "' "
                    : false;
                (('text' in dataItem.config) && ('' !== dataItem.config.text))
                    ? config += " cigo-edit-img-text-config='" + JSON.stringify(dataItem.config.text) + "' "
                    : false;
            }
            content.push(
                '<div id="' + imgViewId + '" type="button" class="imgItem btn btn-default" title="点击修改" ' +
                '   cigo-edit-value="' + imgId + '" cigo-edit-img-src="' + imgSrc + '"' +
                '       ' + ((('argsType' in dataItem) && ('' !== dataItem.argsType)) ? 'cigo-edit-img-args-type="' + dataItem.argsType + '" ' : ' ') +
                '   ' + ctrlType +
                '   ' + config +
                '   >' +
                '   <span>' + dataItem['label'] + '</span>' +
                '   <span title="预览" class="glyphicon glyphicon-eye-open" aria-hidden="true"></span>' +
                '   <input name="' + name + '[' + dataItem.name + ']" type="hidden" value="' + imgId + '" /> ' +
                '</div>'
            );
        });
        content.push('</div>');
    }
    var helpBLock = viewContainer.attr('cigo-edit-helpblock');
    if (helpBLock !== undefined && '' !== helpBLock) {
        content.push('<p class="help-block">' + helpBLock + '</p>');
    }
    viewContainer.html(content.join(''));
    //遍历图片项
    viewContainer.find('div.imgItem').each(function () {
        var itemView = $(this);
        bindPlUploadImg(itemView, itemView, function (data) {
            cigoEditUpdateImgItem(itemView.attr('id'), data.id, data.path);
        });
    });
    //图片预览
    viewContainer.on('mouseenter', ' span.glyphicon.glyphicon-eye-open', function () {
        var viewItem = $(this);
        return cigoEditImgPreviewWin(viewItem.parent(), viewItem);
    });
}

function cigoEditImgPreviewWin(paramBindView, previewWinAliginView) {
    var imgSrc = paramBindView.attr('cigo-edit-img-src');
    if (undefined == imgSrc || '' == imgSrc) {
        cigoLayer.tips('请上传图片后进行预览！', previewWinAliginView, {
            tips: [2, '#0FA6D8'],
            tipsMore: true
        });
    } else {
        cigoLayer.tips('<img style="width: auto;max-width: 160px;" src=' + imgSrc + ' />', previewWinAliginView, {
            tips: [2, '#cdcdcd'],
            tipsMore: true
        });
    }
    return false;
}

/*******************************************************************************************************************************/
function cigoEditCreateImgShow(viewContainer) {
    var imgListRowItemNum = viewContainer.attr('cigo-edit-img-list-row-item-num');
    ((undefined === imgListRowItemNum) || ('' === imgListRowItemNum)) ? imgListRowItemNum = 5 : false;
    var imgListRowItemWidth = 100 / imgListRowItemNum;

    var content = [];
    content.push('<div class="panel panel-default">');
    var label = viewContainer.attr('cigo-edit-label');
    if ((label !== undefined) && ('' !== label)) {
        content.push('<div class="panel-heading">' + label + '</div>');
    }
    //图片列表
    content.push('<div class="img-list row">');
    var name = viewContainer.attr('cigo-edit-name');
    var value = viewContainer.attr('cigo-edit-value');
    if ((value !== undefined) && ('' !== value)) {
        value = eval('(' + value + ')');
        $.each(value, function (key, dataItem) {
            var imgViewId = getRandStr() + (new Date().getTime());
            var imgId = value[key]['img-id'];
            var imgSrc = value[key]['img-src'];
            content.push(
                '<div id="' + imgViewId + '" class="cigo-img" style="width: ' + imgListRowItemWidth + '%;"  cigo-edit-value="' + imgId + '" title="点击修改">' +
                '   <a href="#" class="thumbnail" onclick="return false;">' +
                '       <img src="' + imgSrc + '?' + Math.random() + '" alt="西谷后台" />' +
                '   </a>' +
                '   <div class="tool-bar">' +
                '       <i class="cigo-icon-btn btn-del cigo-iconfont cigo-icon-del pull-right" title="删除"></i>' +
                '   </div>' +
                '   <input name="' + name + '[]" type="hidden" value="' + imgId + '" /> ' +
                '</div>'
            );
        });
    }
    //添加按钮
    content.push(
        '<div id="' + getRandStr() + (new Date().getTime()) + '" title="添加图片" class="cigo-img-add cigo-img" style="width: ' + imgListRowItemWidth + '%;text-align: center;">' +
        '   <a href="#" class="thumbnail" onclick="return false;">' +
        '       <svg class="cigo-icon" aria-hidden="true">' +
        '           <use xlink:href="#cigo-icon-tianjiatupian"></use>' +
        '       </svg>' +
        '   </a>' +
        '</div>'
    );
    content.push('</div>');
    //帮助信息
    var helpBLock = viewContainer.attr('cigo-edit-helpblock');
    if ((helpBLock !== undefined) && ('' !== helpBLock)) {
        content.push('<p class="help-block">&nbsp;&nbsp;' + helpBLock + '</p>');
    }
    content.push('</div>');
    viewContainer.html(content.join(''));
    //图片项目绑定事件
    viewContainer.find('.cigo-img').each(function () {
        var imgView = $(this);
        bindPlUploadImg(imgView, viewContainer, function (data) {
            //判断是否添加元素
            if (imgView.hasClass('cigo-img-add')) {
                cigoEditAddNewImgShowItem(imgListRowItemNum, imgListRowItemWidth, imgView.attr('id'), data.id, data.path);
            } else {
                cigoEditUpdateImgItem(imgView.attr('id'), data.id, data.path);
            }
        });
        //橱窗图片Hover事件
        cigoEditBindImgShowImgItemHoverEvent(viewContainer, imgView);
        //橱窗图片Del事件
        cigoEditBindImgShowImgItemDelEvent(viewContainer, imgView, imgListRowItemNum);
    });
    //重置图片尺寸
    cigoEditImgShowImgItemResize(viewContainer, viewContainer, imgListRowItemNum);
    //判断添加按钮是否可见
    cigoEditImgShowCheckNumLimit(viewContainer, viewContainer);
}

function cigoEditImgShowImgItemResize(paramBindView, viewContainerView, imgListRowItemNum) {
    var width = viewContainerView.find('.img-list .cigo-img:first>.thumbnail').outerWidth() - 10;
    //延迟判断是否加载完成
    if (width < 0) {
        setTimeout(function () {
            cigoEditImgShowImgItemResize(paramBindView, viewContainerView, imgListRowItemNum);
        }, 369);
        return;
    }
    var height = width;
    viewContainerView.find('.img-list').find('.cigo-img').each(function (index, ele) {
        var viewItem = $(this);
        viewItem.css('clear', ((index === imgListRowItemNum) ? 'left' : 'none'));
        viewItem.find('>.thumbnail>img').width(width);
        viewItem.find('>.thumbnail>img').height(height);
        viewItem.find('>.thumbnail>svg').width(width);
        viewItem.find('>.thumbnail>svg').height(height);
    });
}

function cigoEditImgShowCheckNumLimit(paramBindView, viewContainer) {
    var numLimit = paramBindView.attr('cigo-edit-img-num-limit');
    var cigoImgNum = viewContainer.find('.img-list .cigo-img').length;

    if (cigoImgNum <= numLimit) {
        viewContainer.find('.img-list .cigo-img.cigo-img-add').show('fast');
    } else {
        viewContainer.find('.img-list .cigo-img.cigo-img-add').hide();
    }
}

function cigoEditBindImgShowImgItemHoverEvent(paramsBindView, imgView) {
    //hover事件
    imgView.on('mouseenter mouseleave', function (e) {
        switch (e.type) {
            case 'mouseenter':
                $(this).find('.tool-bar').show('fast');
                break;
            case 'mouseleave':
                $(this).find('.tool-bar').hide('fast');
                break;
        }
    });
}

function cigoEditBindImgShowImgItemDelEvent(paramsBindView, imgView, imgListRowItemNum) {
    //删除按钮事件
    imgView.find('.tool-bar').on('click', '.btn-del', function () {
        cigoLayer.msg('确定删除？', {
            time: 0,
            btn: ['删 除', '取 消'],
            yes: function (index) {
                //删除当前图片
                imgView.remove();
                //重置图片尺寸
                cigoEditImgShowImgItemResize(paramsBindView, paramsBindView, imgListRowItemNum);
                //判断添加按钮是否可见
                cigoEditImgShowCheckNumLimit(paramsBindView, paramsBindView);

                //关闭提示窗口
                cigoLayer.close(index);
            }
        });
        return false;
    });
}

function cigoEditAddNewImgShowItem(imgListRowItemNum, imgListRowItemWidth, addNewImgBtnViewId, imgId, imgSrc) {
    var addNewImgBtn = $('#' + addNewImgBtnViewId);
    var paramsBindView = addNewImgBtn.parents('.cigo-edit.item-img-show');

    var name = paramsBindView.attr('cigo-edit-name');
    var newImgViewId = getRandStr() + (new Date().getTime());
    addNewImgBtn.before(
        '<div id="' + newImgViewId + '" class="cigo-img" style="width: ' + imgListRowItemWidth + '%;"  cigo-edit-value="' + imgId + '" title="点击修改">' +
        '   <a href="#" class="thumbnail" onclick="return false;">' +
        '       <img src="' + imgSrc + '?' + Math.random() + '" alt="西谷后台" />' +
        '   </a>' +
        '   <div class="tool-bar">' +
        '       <i class="cigo-icon-btn btn-del cigo-iconfont cigo-icon-del pull-right" title="删除"></i>' +
        '   </div>' +
        '   <input name="' + name + '[]" type="hidden" value="' + imgId + '" /> ' +
        '</div>'
    );

    //图片项目绑定事件
    var newImgView = $('#' + newImgViewId);
    bindPlUploadImg(newImgView, paramsBindView, function (data) {
        cigoEditUpdateImgItem(newImgViewId, data.id, data.path);
    });
    //橱窗图片Hover事件
    cigoEditBindImgShowImgItemHoverEvent(paramsBindView, newImgView);
    //橱窗图片Del事件
    cigoEditBindImgShowImgItemDelEvent(paramsBindView, newImgView, imgListRowItemNum);
    //重置图片尺寸
    cigoEditImgShowImgItemResize(paramsBindView, paramsBindView, imgListRowItemNum);
    //判断添加按钮是否可见
    cigoEditImgShowCheckNumLimit(paramsBindView, paramsBindView);
}

function cigoEditUpdateImgItem(imgViewId, imgId, imgSrc) {
    var imgView = $('#' + imgViewId);
    imgView.attr('cigo-edit-img-src', imgSrc);//渲染面图片
    imgView.find('>input').val(imgId);//保存上传图片ID
    imgView.find('>.thumbnail').html('<img src="' + imgSrc + '?' + Math.random() + '" alt="西谷后台" />');//渲染面图片
}

/*************************************** 视频上传 *************************************/
function cigoEditCreateVideoSingle(viewContainer) {
    var content = [];
    var viewId = getRandStr() + (new Date().getTime());
    var label = viewContainer.attr('cigo-edit-label');
    var value = viewContainer.attr('cigo-edit-value');
    var helpBLock = viewContainer.attr('cigo-edit-helpblock');
    var name = viewContainer.attr('cigo-edit-name');
    //TODO 视频截图封面
    // var videoPoster = viewContainer.attr('cigo-edit-video-poster');
    var videoSrc = viewContainer.attr('cigo-edit-video-src');
    var videoMime = viewContainer.attr('cigo-edit-video-mime');
    content.push(
        ((undefined !== label) ? '<label>' + label + '</label>' : '') +
        '<div id="' + viewId + '" class="cigo-video" title="点击修改"' +
        '   ' + ((undefined !== value && value !== '') ? 'cigo-edit-value="' + value + '" ' : ' ') + '>' +
        '   <a href="#" class="thumbnail" onclick="return false;">' +
        '       ' +
        (
            (videoSrc === undefined || videoSrc === '') ?
                '   <i class="cigo-iconfont cigo-icon-shipin1"></i>' : ''
            //TODO 视频缩略图，点击全屏播放
            // '   <video controls poster="' + videoPoster + '">' +
            // '   <video controls>' +
            // '       <source src="' + videoSrc + '?' + Math.random() + '" type="' + videoMime + '">' +
            // '       您的浏览器不能使用最新的视频播放方式呢 ' +
            // '   </video>'
        ) +
        '       ' +
        '   </a>' +
        '   <input type="hidden" ' +
        '       ' + ((undefined !== value && value !== '') ? 'value="' + value + '" ' : ' ') +
        '       ' + ((undefined !== name && name !== '') ? 'name="' + name + '" ' : '') +
        '   />' +
        '</div>' +
        ((undefined !== helpBLock && helpBLock !== '') ? '<p class="help-block">' + helpBLock + '</p>' : '')
    )
    ;
    viewContainer.html(content.join(''));
    //例化图片上传插件进行上传
    var view = $('#' + viewId);
    bindPlUploadVideo(view, viewContainer, function (data) {
        cigoEditUpdateVideoItem(view.attr('id'), data.id, data.path, data.poster, data.mime);
    });
}

function bindPlUploadVideo(browseView, bindConfigView, successCallbackFunc) {
    //初始化上传插件
    var uploadingTipIndex = -1;
    browseView.plUpload({
        'tag_browse_btn': browseView.attr('id'),
        'uploadUrl': Think.CONTROLLER + Think.DEEP + 'upload' + Think.DEEP + 'sessionid' + Think.DEEP + Think.SESSION_ID,
        'filters': {
            max_file_size: '10mb',
            mime_types: [{title: "选择视频：", extensions: "mp4,rmvb,mov"}]
        },
        'fileAdded': function (uploader, upArgs) {
            //定义上传文件类型
            upArgs.fileType = 'Video';
            uploadingTipIndex = cigoLayer.load();
            uploader.start();
        },
        'completedUpload': function (response) {
            //解析上传结果
            var result = $.parseJSON(response.response);
            if (result.status) {
                successCallbackFunc(result.data);
                // 提示消息
                cigoLayer.msg(result.msg);
            } else {
                cigoLayer.msg(result.msg);
            }
            cigoLayer.close(uploadingTipIndex);
        },
        'pluploadError': function (uploader, error) {
            cigoLayer.msg('上传错误 ' + error.code + ':' + error.message);
        }
    });
}

function cigoEditUpdateVideoItem(viewId, videoId, videoSrc, videoPoster, videoMime) {
    // var imgView = $('#' + imgViewId);
    // imgView.attr('cigo-edit-img-src', imgSrc);//渲染面图片
    // imgView.find('>input').val(imgId);//保存上传图片ID
    // imgView.find('>.thumbnail').html('<img src="' + imgSrc + '?' + Math.random() + '" alt="西谷后台" />');//渲染面图片
}

/*************************************** 文件上传 *************************************/
function cigoEditCreateFileSingle(viewContainer) {
    var content = [];
    var viewId = getRandStr() + (new Date().getTime());
    var label = viewContainer.attr('cigo-edit-label');
    var value = viewContainer.attr('cigo-edit-value');
    var helpBLock = viewContainer.attr('cigo-edit-helpblock');
    var name = viewContainer.attr('cigo-edit-name');
    //TODO 视频截图封面
    // var videoPoster = viewContainer.attr('cigo-edit-video-poster');
    var videoSrc = viewContainer.attr('cigo-edit-video-src');
    var videoMime = viewContainer.attr('cigo-edit-video-mime');
    content.push(
        ((undefined !== label) ? '<label>' + label + '</label>' : '') +
        '<div id="' + viewId + '" class="cigo-video" title="点击修改"' +
        '   ' + ((undefined !== value && value !== '') ? 'cigo-edit-value="' + value + '" ' : ' ') + '>' +
        '   <a href="#" class="thumbnail" onclick="return false;">' +
        '       ' +
        (
            (videoSrc === undefined || videoSrc === '') ?
                '   <i class="cigo-iconfont cigo-icon-shipin1"></i>' : ''
            //TODO 视频缩略图，点击全屏播放
            // '   <video controls poster="' + videoPoster + '">' +
            // '   <video controls>' +
            // '       <source src="' + videoSrc + '?' + Math.random() + '" type="' + videoMime + '">' +
            // '       您的浏览器不能使用最新的视频播放方式呢 ' +
            // '   </video>'
        ) +
        '       ' +
        '   </a>' +
        '   <input type="hidden" ' +
        '       ' + ((undefined !== value && value !== '') ? 'value="' + value + '" ' : ' ') +
        '       ' + ((undefined !== name && name !== '') ? 'name="' + name + '" ' : '') +
        '   />' +
        '</div>' +
        ((undefined !== helpBLock && helpBLock !== '') ? '<p class="help-block">' + helpBLock + '</p>' : '')
    )
    ;
    viewContainer.html(content.join(''));
    //例化图片上传插件进行上传
    var view = $('#' + viewId);
    bindPlUploadVideo(view, viewContainer, function (data) {
        cigoEditUpdateVideoItem(view.attr('id'), data.id, data.path, data.poster, data.mime);
    });
}

function bindPlUploadFile(browseView, bindConfigView, successCallbackFunc) {
    //初始化上传插件
    var uploadingTipIndex = -1;
    browseView.plUpload({
        'tag_browse_btn': browseView.attr('id'),
        'uploadUrl': Think.CONTROLLER + Think.DEEP + 'upload' + Think.DEEP + 'sessionid' + Think.DEEP + Think.SESSION_ID,
        'filters': {
            max_file_size: '10mb',
            mime_types: [{title: "选择视频：", extensions: "mp4,rmvb,mov"}]
        },
        'fileAdded': function (uploader, upArgs) {
            //定义上传文件类型
            upArgs.fileType = 'Video';
            uploadingTipIndex = cigoLayer.load();
            uploader.start();
        },
        'completedUpload': function (response) {
            //解析上传结果
            var result = $.parseJSON(response.response);
            if (result.status) {
                successCallbackFunc(result.data);
                // 提示消息
                cigoLayer.msg(result.msg);
            } else {
                cigoLayer.msg(result.msg);
            }
            cigoLayer.close(uploadingTipIndex);
        },
        'pluploadError': function (uploader, error) {
            cigoLayer.msg('上传错误 ' + error.code + ':' + error.message);
        }
    });
}

function cigoEditUpdateFileItem(viewId, videoId, videoSrc, videoPoster, videoMime) {
    // var imgView = $('#' + imgViewId);
    // imgView.attr('cigo-edit-img-src', imgSrc);//渲染面图片
    // imgView.find('>input').val(imgId);//保存上传图片ID
    // imgView.find('>.thumbnail').html('<img src="' + imgSrc + '?' + Math.random() + '" alt="西谷后台" />');//渲染面图片
}
