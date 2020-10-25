function autoTip(data) {
    if (data.status == 1) {
        cigoLayer.msg(data.info, {icon: 6});
        return true;
    } else {
        cigoLayer.msg(data.info, {icon: 5});
        return false;
    }
}

function refreshDataList(dataListView, url, getDataListFun, showPageFilter, makeArgsFun, createTrFun, bindEventFlag, notTableMode, afterCtrlDataListFunc, ctrlFlag) {
    if (url == undefined || url == '') {
        var data = getDataListFun(dataListView);
        ctrlDataList(dataListView, data, getDataListFun, showPageFilter, makeArgsFun, createTrFun, bindEventFlag, notTableMode, afterCtrlDataListFunc, ctrlFlag);
        (afterCtrlDataListFunc != undefined && afterCtrlDataListFunc != '') ? afterCtrlDataListFunc(dataListView, data) : false;
    } else {
        dataListView.attr('url-curr', url);	//记录当前数据地址
        var argsData = (makeArgsFun != undefined && makeArgsFun != '') ? makeArgsFun(dataListView) : {};//获取查询参数
        //加载列表数据
        $.post(url, argsData, function (data) {
            ctrlDataList(dataListView, data, getDataListFun, showPageFilter, makeArgsFun, createTrFun, bindEventFlag, notTableMode, afterCtrlDataListFunc, ctrlFlag);
            (afterCtrlDataListFunc != undefined && afterCtrlDataListFunc != '') ? afterCtrlDataListFunc(dataListView, data) : false;
            if (!bindEventFlag && !ctrlFlag)
                cigoLayer.msg('加载完毕！', {icon: 6});
        });
    }
}

function ctrlDataList(dataListView, data, getDataListFun, showPageFilter, makeArgsFun, createTrFun, bindEventFlag, notTableMode, afterCtrlDataListFunc, ctrlFlag) {
    //清空原列表
    if (notTableMode != undefined && notTableMode == true) {
        dataListView.empty();
    } else {
        var itemViewList = dataListView.find('.list-item');
        if (itemViewList.length > 0) {
            itemViewList.remove();
        }
    }

    //根据数据处理列表
    if (data.status != 1) {
        cigoLayer.msg(data.info, {icon: 5});
    } else {
        var dataList = ('dataList' in data.info) ? data.info.dataList : data.info;
        //加载列表数据
        if (!isArray(dataList)) {
            cigoLayer.msg('数据错误!');
            return;
        }
        var itemSubViewList = new Array();
        createList(dataListView, itemSubViewList, dataList, 0, createTrFun); //建立列表
        dataListView.append(itemSubViewList.join(''));//添加新建立列表
        //分页数据
        refreshPagination(dataListView, getDataListFun, showPageFilter, makeArgsFun, createTrFun, bindEventFlag, data, notTableMode);


        //设置默认展开/闭合状态
        (notTableMode != undefined && notTableMode == true) ? false : setDefaultExpandState(dataListView);
    }
    //绑定相关事件
    if (bindEventFlag) {
        //绑定AjaxGet事件
        bindAjaxGetEvent(dataListView, getDataListFun, showPageFilter, makeArgsFun, createTrFun, notTableMode, afterCtrlDataListFunc, ctrlFlag);
        //绑定快速编辑事件
        bindQuikEditEvetn(dataListView, getDataListFun, showPageFilter, makeArgsFun, createTrFun, notTableMode, afterCtrlDataListFunc, ctrlFlag);
        //绑定列表开闭事件
        (notTableMode != undefined && notTableMode == true) ? false : bindListExpanCloseEvent(dataListView);
        //绑定分页点击事件
        bindPaginationEvent(dataListView, getDataListFun, showPageFilter, makeArgsFun, createTrFun, notTableMode, afterCtrlDataListFunc, ctrlFlag)
    }
}

function bindListExpanCloseEvent(dataListView) {
    dataListView.on('click', '.expand-icon.has-sub', function () {
        var expandView = $(this);
        var trView = expandView.parent().parent();
        var level = trView.attr('list-level');
        if (expandView.hasClass('cigo-icon-one')) { //展开状态
            expandView.removeClass('cigo-icon-one');
            expandView.addClass('cigo-icon-open');
            expandView.attr('title', '点击展开');
            if (!isNaN(level)) {
                expandCloseSubList(trView, parseInt(level));
            }
        } else { //关闭状态
            expandView.removeClass('cigo-icon-open');
            expandView.addClass('cigo-icon-one');
            expandView.attr('title', '点击关闭');
            if (!isNaN(level)) {
                expandOpenSubList(trView, parseInt(level));
            }
        }

        return false;
    });
}

function setDefaultExpandState(dataListView) {
    var headTrView = dataListView.find('.list-head:first');
    expandCloseSubList(headTrView, -1);
    expandOpenSubList(headTrView, -1);
}

function expandCloseSubList(preTrView, clickTrLevel) {
    var nextTrView = preTrView.next();
    if (nextTrView.length > 0) {
        var nextTrLevel = nextTrView.attr('list-level');
        if (parseInt(nextTrLevel) > clickTrLevel) {
            //隐藏列表项
            nextTrView.css('display', 'none');
            //继续下一个Tr
            expandCloseSubList(nextTrView, clickTrLevel);
        }
    }
}

function expandOpenSubList(preTrView, clickTrLevel) {
    var nextTrView = preTrView.next();
    if (nextTrView.length > 0) {
        var nextTrLevel = nextTrView.attr('list-level');
        if (parseInt(nextTrLevel) > clickTrLevel) {
            //展开第一级子列表
            if (nextTrLevel == (clickTrLevel + 1)) {
                //显示子项
                nextTrView.css('display', '');
                //修改含子项Expand图标
                var nextExpandView = nextTrView.find('.expand-icon.has-sub');
                if (nextExpandView.length > 0) {
                    nextExpandView.removeClass('cigo-icon-one');
                    nextExpandView.addClass('cigo-icon-open');
                    nextExpandView.attr('title', '点击展开');
                }
            }
            //继续下一个Tr
            expandOpenSubList(nextTrView, clickTrLevel);
        }
    }
}

function createList(dataListView, itemSubViewList, pList, level, createTrFun) {
    $.each(pList, function (listKey, dataItem) {
        var hasSubFlag = ('subList' in dataItem);
        //创建当前列表项
        createTrFun(dataListView, itemSubViewList, listKey, dataItem, level, hasSubFlag, 'has-sub');
        //创建子列表
        if (hasSubFlag) {
            createList(dataListView, itemSubViewList, dataItem['subList'], level + 1, createTrFun);
        }
    });
}

function refreshPagination(dataListView, getDataListFun, showPageFilter, makeArgsFun, createTrFun, bindEventFlag, data, notTableMode) {
    if ('' == showPageFilter || undefined == showPageFilter) {
        return;
    }

    $(showPageFilter).each(function () {
        if ('showPage' in data.info) {
            var showPageView = $(this);
            showPageView.html(data.info.showPage);
        } else {
            var showPageView = $(this);
            showPageView.html('');
        }
    });
}

function bindPaginationEvent(dataListView, getDataListFun, showPageFilter, makeArgsFun, createTrFun, notTableMode, afterCtrlDataListFunc, ctrlFlag) {
    if ('' == showPageFilter || undefined == showPageFilter) {
        return;
    }
    $(showPageFilter).each(function () {
        $(this).on('click', '.pagination>.pageItem>a', function () {
            var target = $(this).attr('href') || $(this).attr('url');
            if (target === undefined || target === '' || target === '#') {
                return false;
            }
            refreshDataList(dataListView, target, getDataListFun, showPageFilter, makeArgsFun, createTrFun, false, notTableMode, afterCtrlDataListFunc, ctrlFlag);
            return false;
        });
    });

}

function bindAjaxGetEvent(dataListView, getDataListFun, showPageFilter, makeArgsFun, createTrFun, notTableMode, afterCtrlDataListFunc, ctrlFlag) {
    dataListView.on('click', '.ajax-get', function () {
        if ($(this).hasClass('confirm')) {
            if (!confirm('确认要执行该操作吗?')) {
                cigoLayer.msg('操作已取消!');
                return false;
            }
        }

        var target = $(this).attr('href') || $(this).attr('url');
        if (target !== undefined && target !== '' && target !== '#') {
            $.get(target, function (data) {
                if (autoTip(data)) {
                    refreshDataList(dataListView, dataListView.attr('url-curr'), getDataListFun,
                        showPageFilter, makeArgsFun, createTrFun, false, notTableMode, afterCtrlDataListFunc, true);
                }
            });
        }
        return false;
    });
}

function bindQuikEditEvetn(dataListView, getDataListFun, showPageFilter, makeArgsFun, createTrFun, notTableMode, afterCtrlDataListFunc, ctrlFlag) {
    dataListView.on('focusout', '.cigo-edit.quik-edit', function (e) {
        var editor = $(this);

        var oldVal = editor.attr('cigo-edit-val-item-val');
        var newVal = editor.val();

        if (newVal == '') {
            editor.val(oldVal);
            return;
        }

        if (newVal != oldVal) {
            var ctrlTarget = editor.attr('cigo-edit-url');
            var key = editor.attr('cigo-edit-val-item-key');
            var argsData = {};
            argsData['id'] = editor.attr('cigo-edit-id');
            argsData[key] = newVal;

            $.post(ctrlTarget, argsData, function (data) {
                if (data.status == 1) {
                    cigoLayer.msg(data.info, {icon: 6});
                    editor.attr('cigo-edit-val-item-val', newVal);

                    //刷新列表
                    refreshDataList(dataListView, dataListView.attr('url-curr'), getDataListFun, showPageFilter,
                        makeArgsFun, createTrFun, false, notTableMode, afterCtrlDataListFunc, true);
                } else {
                    cigoLayer.msg(data.info, {icon: 5});
                }
            });
        }
    });
}
