/**
 * @Date March 17, 2014
 */

/**
 * String startWith.
 *
 * @param string
 * @returns {boolean}
 */
String.prototype.startWith = function (string) {
    if (string == null || string == "" || this.length == 0 || string.length > this.length) {
        return false;
    } else {
        return this.substr(0, string.length) == string;
    }
};

/**
 * String endWith.
 *
 * @param string
 * @returns {boolean}
 */
String.prototype.endWith = function (string) {
    if (string == null || string == "" || this.length == 0 || string.length > this.length) {
        return false;
    } else {
        return this.substring(this.length - string.length) == string;
    }
};

/**
 * String replaceAll.
 *
 * @param string1
 * @param string2
 * @returns {string}
 */
String.prototype.replaceAll = function (string1, string2) {
    return this.replace(new RegExp(string1, "gm"), string2);
};

function StringBuilder() {
    if (arguments.length) {
        this.append.apply(this, arguments);
    }
}
/**
 * StringBuilder.
 * Property: length
 * Method: append,appendFormat,size,toString,valueOf
 *
 * From: http://webreflection.blogspot.com/2008/06/lazy-developers-stack-concept-and.html
 * (C) Andrea Giammarchi - Mit Style License
 * @type {StringBuilder.prototype}
 */
StringBuilder.prototype = function () {
    var join = Array.prototype.join, slice = Array.prototype.slice, RegExp = /\{(\d+)\}/g, toString = function () {
        return join.call(this, "");
    };
    return {
        constructor: StringBuilder,
        length: 0,
        append: Array.prototype.push,
        appendFormat: function (String) {
            var i = 0, args = slice.call(arguments, 1);
            this.append(RegExp.test(String) ? String.replace(RegExp, function (String, i) {
                return args[i];
            }) : String.replace(/\?/g, function () {
                return args[i++];
            }));
            return this;
        },
        size: function () {
            return this.toString().length;
        },
        toString: toString,
        valueOf: toString
    };
}();


/**
 * jQuery.bsgrid v1.36 by @Baishui2004
 * Copyright 2014 Apache v2 License
 * https://github.com/baishui2004/jquery.bsgrid
 */
/**
 * require common.js.
 *
 * @author Baishui2004
 * @Date August 31, 2014
 */
(function ($) {

    $.fn.bsgrid = {

        version: '1.36',

        // defaults settings
        defaults: {
            dataType: 'json',
            localData: false, // values: false, json data, xml data
            url: '', // page request url
            otherParames: false, // other parameters, values: false, A Object or A jquery serialize Array
            autoLoad: true, // load onReady
            pageAll: false, // display all datas, no paging only count
            pageSize: 20, // page size. if set value little then 1, then pageAll will auto set true
            pageSizeSelect: false, // if display pageSize select option
            pageSizeForGrid: [5, 10, 20, 25, 50, 100, 200, 500], // pageSize select option
            pageIncorrectTurnAlert: true, // if turn incorrect page alert(firstPage, prevPage, nextPage, lastPage)
            multiSort: false, // multi column sort support
            displayBlankRows: true,
            lineWrap: false, // if grid cell content wrap, if false then td use style: white-space: nowrap; overflow: hidden; text-overflow: ellipsis; if true then td use style: word-break: break-all;
            stripeRows: false, // stripe rows
            rowHoverColor: false, // row hover color
            rowSelectedColor: true, // row selected color
            pagingLittleToolbar: false, // if display paging little toolbar
            pagingToolbarAlign: 'right',
            pagingBtnClass: 'pagingBtn', // paging toolbar button css class
            displayPagingToolbarOnlyMultiPages: false,
            isProcessLockScreen: true,
            // longLengthAotoSubAndTip: if column's value length longer than it, auto sub and tip it.
            //    sub: content.substring(0, MaxLength-3) + '...'. if column's render is not false, then this property is not make effective to it.
            longLengthAotoSubAndTip: true,
            colsProperties: {
                // body row every column config
                align: 'left',
                maxLength: 40, // every column's value display max length
                // config properties's name
                indexAttr: 'w_index',
                sortAttr: 'w_sort', // use: w_sort="id" or w_sort="id,desc" or w_sort="id,asc"
                alignAttr: 'w_align',
                lengthAttr: 'w_length', // per column's value display max length, default maxLength
                renderAttr: 'w_render', // use: w_render="funMethod"
                hiddenAttr: 'w_hidden',
                tipAttr: 'w_tip'
            },
            // request params name
            requestParamsName: {
                pageSize: 'pageSize',
                curPage: 'curPage',
                sortName: 'sortName',
                sortOrder: 'sortOrder'
            },
            // before page ajax request send
            beforeSend: function (options, XMLHttpRequest) {
            },
            // after page ajax request complete
            complete: function (options, XMLHttpRequest, textStatus) {
            },
            // process userdata, process before grid render data
            processUserdata: function (userdata, options) {
            },
            // event
            event: {
                selectRowEvent: false, // method params: record, rowIndex, trObj, options
                unselectRowEvent: false, // method params: record, rowIndex, trObj, options
                // custom row events: click, dbclick, focus ......
                customRowEvents: {}, // method params: record, rowIndex, trObj, options
                // custom cell events: click, dbclick, focus ......
                customCellEvents: {} // method params: record, rowIndex, colIndex, tdObj, trObj, options
            },
            // extend
            extend: {
                // extend init grid methods
                initGridMethods: {
                    // methodAlias: methodName // method params: gridId, options
                },
                // extend before render grid methods
                beforeRenderGridMethods: {
                    // methodAlias: methodName // method params: parseSuccess, gridData, options
                },
                // extend render per row methods, no matter blank row or not blank row, before render per column methods
                renderPerRowMethods: {
                    // methodAlias: methodName // method params: record, rowIndex, trObj, options
                },
                // extend render per column methods, no matter blank column or not blank column
                renderPerColumnMethods: {
                    // methodAlias: methodName // method params: record, rowIndex, colIndex, tdObj, trObj, options
                },
                // extend after render grid methods
                afterRenderGridMethods: {
                    // methodAlias: methodName // method params: parseSuccess, gridData, options
                }
            },
            /**
             * additional before render grid.
             *
             * @param parseSuccess if ajax data parse success, true or false
             * @param gridData page ajax return data
             * @param options
             */
            additionalBeforeRenderGrid: function (parseSuccess, gridData, options) {
            },
            /**
             * additional render per row, no matter blank row or not blank row, before additional render per column.
             *
             * @param record row record, may be null
             * @param rowIndex row index, from 0
             * @param trObj row tr obj
             * @param options
             */
            additionalRenderPerRow: function (record, rowIndex, trObj, options) {
            },
            /**
             * additional render per column, no matter blank column or not blank column.
             *
             * @param record row record, may be null
             * @param rowIndex row index, from 0
             * @param colIndex column index, from 0
             * @param tdObj column td obj
             * @param trObj row tr obj
             * @param options
             */
            additionalRenderPerColumn: function (record, rowIndex, colIndex, tdObj, trObj, options) {
            },
            /**
             * additional after render grid.
             *
             * @param parseSuccess if ajax data parse success, true or false
             * @param gridData page ajax return data
             * @param options
             */
            additionalAfterRenderGrid: function (parseSuccess, gridData, options) {
            }
        },

        gridObjs: {},

        init: function (gridId, settings) {
            if (!$('#' + gridId).hasClass('bsgrid')) {
                $('#' + gridId).addClass('bsgrid');
            }

            var options = {
                settings: $.extend(true, {}, $.fn.bsgrid.defaults, settings),
                gridId: gridId,
                noPagingationId: gridId + '_no_pagination',
                pagingOutTabId: gridId + '_pt_outTab',
                pagingId: gridId + '_pt',
                // sort
                sortName: '',
                sortOrder: '',
                otherParames: settings.otherParames,
                totalRows: 0,
                totalPages: 0,
                curPage: 1,
                curPageRowsNum: 0,
                startRow: 0,
                endRow: 0
            };

            if ($('#' + gridId).find('thead').length == 0) {
                $('#' + gridId).prepend('<thead></thead>');
                $('#' + gridId).find('tr:lt(' + ($('#' + gridId + ' tr').length - $('#' + gridId + ' tfoot tr').length) + ')').appendTo($('#' + gridId + ' thead'));
            }
            if ($('#' + gridId).find('tbody').length == 0) {
                $('#' + gridId + ' thead').after('<tbody></tbody>');
            }
            if ($('#' + gridId).find('tfoot').length == 0) {
                $('#' + gridId).append('<tfoot style="display: none;"></tfoot>');
            }

            options.columnsModel = $.fn.bsgrid.initColumnsModel(options);

            if (settings.pageSizeForGrid != undefined) {
                options.settings.pageSizeForGrid = settings.pageSizeForGrid;
            }

            options.settings.dataType = options.settings.dataType.toLowerCase();
            if (options.settings.pageSizeSelect) {
                if ($.inArray(options.settings.pageSize, options.settings.pageSizeForGrid) == -1) {
                    options.settings.pageSizeForGrid.push(options.settings.pageSize);
                }
                options.settings.pageSizeForGrid.sort(function (a, b) {
                    return a - b;
                });
            }

            var gridObj = {
                options: options,
                getPageCondition: function (curPage) {
                    return $.fn.bsgrid.getPageCondition(curPage, options);
                },
                page: function (curPage) {
                    $.fn.bsgrid.page(curPage, options);
                },
                search: function (params) {
                    $.fn.bsgrid.search(params, options);
                },
                loadGridData: function (dataType, gridData) {
                    $.fn.bsgrid.loadGridData(dataType, gridData, options);
                },
                getRows: function () {
                    return $.fn.bsgrid.getRows(options);
                },
                getRow: function (row) {
                    return $.fn.bsgrid.getRow(row, options);
                },
                getRowCells: function (row) {
                    return $.fn.bsgrid.getRowCells(row, options);
                },
                getColCells: function (col) {
                    return $.fn.bsgrid.getColCells(col, options);
                },
                getCell: function (row, col) {
                    return $.fn.bsgrid.getCell(row, col, options);
                },
                getSelectedRow: function () {
                    return $.fn.bsgrid.getSelectedRow(options);
                },
                getSelectedRowIndex: function () {
                    return $.fn.bsgrid.getSelectedRowIndex(options);
                },
                selectRow: function (row) {
                    return $.fn.bsgrid.selectRow(row, options);
                },
                unSelectRow: function () {
                    return $.fn.bsgrid.unSelectRow(options);
                },
                getUserdata: function () {
                    return $.fn.bsgrid.getUserdata(options);
                },
                getRowRecord: function (rowObj) {
                    return $.fn.bsgrid.getRowRecord(rowObj);
                },
                getAllRecords: function () {
                    return $.fn.bsgrid.getAllRecords(options);
                },
                getRecord: function (row) {
                    return $.fn.bsgrid.getRecord(row, options);
                },
                getRecordIndexValue: function (record, index) {
                    return $.fn.bsgrid.getRecordIndexValue(record, index, options);
                },
                getColumnValue: function (row, index) {
                    return $.fn.bsgrid.getColumnValue(row, index, options);
                },
                getCellRecordValue: function (row, col) {
                    return $.fn.bsgrid.getCellRecordValue(row, col, options);
                },
                sort: function (obj) {
                    $.fn.bsgrid.sort(obj, options);
                },
                getGridHeaderObject: function () {
                    return $.fn.bsgrid.getGridHeaderObject(options);
                },
                getColumnModel: function (colIndex) {
                    return $.fn.bsgrid.getColumnModel(colIndex, options);
                },
                appendHeaderSort: function () {
                    $.fn.bsgrid.appendHeaderSort(options);
                },
                setGridBlankBody: function () {
                    $.fn.bsgrid.setGridBlankBody(options);
                },
                createPagingOutTab: function () {
                    $.fn.bsgrid.createPagingOutTab(options);
                },
                clearGridBodyData: function () {
                    $.fn.bsgrid.clearGridBodyData(options);
                },
                getPagingObj: function () {
                    return $.fn.bsgrid.getPagingObj(options);
                },
                getCurPage: function () {
                    return $.fn.bsgrid.getCurPage(options);
                },
                refreshPage: function () {
                    $.fn.bsgrid.refreshPage(options);
                },
                firstPage: function () {
                    $.fn.bsgrid.firstPage(options);
                },
                prevPage: function () {
                    $.fn.bsgrid.prevPage(options);
                },
                nextPage: function () {
                    $.fn.bsgrid.nextPage(options);
                },
                lastPage: function () {
                    $.fn.bsgrid.lastPage(options);
                },
                gotoPage: function (goPage) {
                    $.fn.bsgrid.gotoPage(options, goPage);
                },
                initPaging: function () {
                    return $.fn.bsgrid.initPaging(options);
                },
                setPagingValues: function () {
                    $.fn.bsgrid.setPagingValues(options);
                }
            };

            // store mapping grid id to gridObj
            $.fn.bsgrid.gridObjs[gridId] = gridObj;

            // if no pagination
            if (options.settings.pageAll || options.settings.pageSize < 1) {
                options.settings.pageAll = true;
                options.settings.pageSize = 0;
            }

            gridObj.appendHeaderSort();

            // init paging
            gridObj.createPagingOutTab();

            if (!options.settings.pageAll) {
                gridObj.pagingObj = gridObj.initPaging();
                try {
                    var minWidth = $.trim($('#' + options.pagingId).children().width());
                    minWidth = minWidth == '' ? 0 : parseInt(minWidth);
                    if (minWidth != 0) {
                        $('#' + gridId).css('min-width', minWidth + 16);
                        $('#' + options.pagingOutTabId).css('min-width', minWidth + 16);
                    }
                    $('#' + options.pagingOutTabId).width($('#' + gridId).width());
                    $(window).resize(function () {
                        $('#' + options.pagingOutTabId).width($('#' + gridId).width());
                    });
                } catch (e) {
                }
            }

            if (options.settings.isProcessLockScreen) {
                $.fn.bsgrid.addLockScreen(options);
            }

            try {
                // init grid extend options
                $.fn.bsgrid.extendInitGrid.initGridExtendOptions(gridId, options);
            } catch (e) {
                // do nothing
            }

            for (var key in options.settings.extend.initGridMethods) {
                options.settings.extend.initGridMethods[key](gridId, options);
            }

            // auto load
            if (options.settings.autoLoad) {
                // delay 10 millisecond for return gridObj first, then page
                setTimeout(function () {
                    gridObj.page(1);
                }, 10);
            } else {
                gridObj.setGridBlankBody();
            }

            return gridObj;
        },

        initColumnsModel: function (options) {
            var columnsModel = [];
            $.fn.bsgrid.getGridHeaderObject(options).each(function () {
                var colsProperties = options.settings.colsProperties;
                var columnModel = {};
                // column sort name, order
                columnModel.sortName = '';
                columnModel.sortOrder = '';
                var sortInfo = $.trim($(this).attr(colsProperties.sortAttr));
                if (sortInfo.length != 0) {
                    var sortInfoArray = sortInfo.split(',');
                    columnModel.sortName = $.trim(sortInfoArray[0]);
                    columnModel.sortOrder = $.trim(sortInfoArray.length > 1 ? sortInfoArray[1] : '');
                }
                // column index
                columnModel.index = $.trim($(this).attr(colsProperties.indexAttr));
                // column render
                columnModel.render = $.trim($(this).attr(colsProperties.renderAttr));
                // column tip
                columnModel.tip = $.trim($(this).attr(colsProperties.tipAttr));
                // column text max length
                var maxLen = $.trim($(this).attr(colsProperties.lengthAttr));
                columnModel.maxLen = maxLen.length != 0 ? parseInt(maxLen) : colsProperties.maxLength;
                // column align
                var align = $.trim($(this).attr(colsProperties.alignAttr));
                columnModel.align = align == '' ? colsProperties.align : align;
                // column hidden
                columnModel.hidden = $.trim($(this).attr(colsProperties.hiddenAttr));
                columnsModel.push(columnModel);
            });
            return columnsModel;
        },

        getGridObj: function (gridId) {
            var obj = $.fn.bsgrid.gridObjs[gridId];
            return obj ? obj : null;
        },

        buildData: {
            gridData: function (type, curPage, data) {
                if (type == 'json') {
                    return $.fn.bsgrid.buildJsonData.gridData(curPage, data);
                } else if (type == 'xml') {
                    return $.fn.bsgrid.buildXmlData.gridData(curPage, data);
                }
                return false;
            }
        },

        parseData: {
            success: function (type, gridData) {
                if (type == 'json') {
                    return $.fn.bsgrid.parseJsonData.success(gridData);
                } else if (type == 'xml') {
                    return $.fn.bsgrid.parseXmlData.success(gridData);
                }
                return false;
            },
            totalRows: function (type, gridData) {
                if (type == 'json') {
                    return $.fn.bsgrid.parseJsonData.totalRows(gridData);
                } else if (type == 'xml') {
                    return $.fn.bsgrid.parseXmlData.totalRows(gridData);
                }
                return false;
            },
            curPage: function (type, gridData) {
                if (type == 'json') {
                    return $.fn.bsgrid.parseJsonData.curPage(gridData);
                } else if (type == 'xml') {
                    return $.fn.bsgrid.parseXmlData.curPage(gridData);
                }
                return false;
            },
            data: function (type, gridData) {
                if (type == 'json') {
                    return $.fn.bsgrid.parseJsonData.data(gridData);
                } else if (type == 'xml') {
                    return $.fn.bsgrid.parseXmlData.data(gridData);
                }
                return false;
            },
            userdata: function (type, gridData) {
                if (type == 'json') {
                    return $.fn.bsgrid.parseJsonData.userdata(gridData);
                } else if (type == 'xml') {
                    return $.fn.bsgrid.parseXmlData.userdata(gridData);
                }
                return false;
            },
            getDataLen: function (type, gridData) {
                if (type == 'json' || type == 'xml') {
                    return $.fn.bsgrid.parseData.data(type, gridData).length;
                }
                return 0;
            },
            getRecord: function (type, data, row) {
                if (type == 'json') {
                    return $.fn.bsgrid.parseJsonData.getRecord(data, row);
                } else if (type == 'xml') {
                    return $.fn.bsgrid.parseXmlData.getRecord(data, row);
                }
                return false;
            },
            getColumnValue: function (type, record, index) {
                if (type == 'json') {
                    return $.fn.bsgrid.parseJsonData.getColumnValue(record, index);
                } else if (type == 'xml') {
                    return $.fn.bsgrid.parseXmlData.getColumnValue(record, index);
                }
                return false;
            }
        },

        buildJsonData: {
            gridData: function (curPage, data) {
                return {
                    "success": true,
                    "totalRows": data.length,
                    "curPage": curPage,
                    "data": data
                };
            }
        },

        parseJsonData: {
            success: function (json) {
                return json.success;
            },
            totalRows: function (json) {
                return json.totalRows;
            },
            curPage: function (json) {
                return json.curPage;
            },
            data: function (json) {
                return json.data;
            },
            userdata: function (json) {
                return json.userdata;
            },
            getRecord: function (data, row) {
                return data[row];
            },
            getColumnValue: function (record, index) {
                return $.trim(record[index]);
            }
        },

        buildXmlData: {
            gridData: function (curPage, data) {
                return '<?xml version="1.0" encoding="UTF-8"?>'
                    + '<gridData>'
                    + '<success>true</success>'
                    + '<totalRows>' + $('<xml>' + data + '</xml>').find('row').length + '</totalRows>'
                    + '<curPage>' + curPage + '</curPage>'
                    + '<data>'
                    + data
                    + '</data>'
                    + '</gridData>';
            }
        },

        parseXmlData: {
            success: function (xml) {
                return $.trim($(xml).find('gridData success').text()) == 'true';
            },
            totalRows: function (xml) {
                return parseInt($(xml).find('gridData totalRows').text());
            },
            curPage: function (xml) {
                return parseInt($(xml).find('gridData curPage').text());
            },
            data: function (xml) {
                return $(xml).find('gridData data row');
            },
            userdata: function (xml) {
                return $(xml).find('gridData userdata');
            },
            getRecord: function (data, row) {
                return data.eq(row);
            },
            getColumnValue: function (record, index) {
                return $.trim(record.find(index).text());
            }
        },

        getPageCondition: function (curPage, options) {
            // other parames
            var params = new StringBuilder();
            if (options.otherParames == false) {
                // do nothing
            } else if ((typeof options.otherParames).toLowerCase() == 'string' || options.otherParames instanceof String) {
                params.append('&' + options.otherParames);
            } else if (options.otherParames instanceof Array) {
                $.each(options.otherParames, function (i, objVal) {
                    params.append('&' + objVal.name + '=' + objVal.value);
                });
            } else {
                for (var key in options.otherParames) {
                    params.append('&' + key + '=' + options.otherParames[key]);
                }
            }

            var condition = params.length == 0 ? '' : params.toString().substring(1);
            condition += (condition.length == 0 ? '' : '&')
            + options.settings.requestParamsName.pageSize + '=' + options.settings.pageSize
            + '&' + options.settings.requestParamsName.curPage + '=' + curPage
            + '&' + options.settings.requestParamsName.sortName + '=' + options.sortName
            + '&' + options.settings.requestParamsName.sortOrder + '=' + options.sortOrder;
            return condition;
        },

        search: function (params, options) {
            options.otherParames = params;
            $.fn.bsgrid.page(1, options);
        },

        page: function (curPage, options) {
            if ($.trim(curPage) == '' || isNaN(curPage)) {
                $.fn.bsgrid.alert($.bsgridLanguage.needInteger);
                return;
            }
            var dataType = options.settings.dataType;
            if (options.settings.localData != false) {
                if (dataType == 'json') {
                    $.fn.bsgrid.loadGridData(dataType, $.fn.bsgrid.buildData.gridData(dataType, curPage, options.settings.localData), options);
                } else if (dataType == 'xml') {
                    $.fn.bsgrid.loadGridData(dataType, '<xml>' + $.fn.bsgrid.buildData.gridData(dataType, curPage, options.settings.localData) + '</xml>', options);
                }
                return;
            }
            $.ajax({
                type: 'post',
                url: options.settings.url,
                data: $.fn.bsgrid.getPageCondition(curPage, options),
                dataType: dataType,
                beforeSend: function (XMLHttpRequest) {
                    if (options.settings.isProcessLockScreen) {
                        $.fn.bsgrid.lockScreen(options);
                    }
                    options.settings.beforeSend(options, XMLHttpRequest);
                },
                complete: function (XMLHttpRequest, textStatus) {
                    options.settings.complete(options, XMLHttpRequest, textStatus);
                    if (options.settings.isProcessLockScreen) {
                        $.fn.bsgrid.unlockScreen(options);
                    }
                },
                success: function (gridData, textStatus) {
                    $.fn.bsgrid.loadGridData(dataType, gridData, options);
                },
                error: function (XMLHttpRequest, textStatus, errorThrown) {
                    $.fn.bsgrid.alert($.bsgridLanguage.errorForSendOrRequestData);
                }
            });
        },

        loadGridData: function (dataType, gridData, options) {
            var parseSuccess = $.fn.bsgrid.parseData.success(dataType, gridData);
            for (var key in options.settings.extend.beforeRenderGridMethods) {
                options.settings.extend.beforeRenderGridMethods[key](parseSuccess, gridData, options);
            }
            options.settings.additionalBeforeRenderGrid(parseSuccess, gridData, options);
            if (parseSuccess) {
                // userdata
                var userdata = $.fn.bsgrid.parseData.userdata(dataType, gridData);
                $.fn.bsgrid.storeUserdata(userdata, options);
                options.settings.processUserdata(userdata, options);

                var totalRows = parseInt($.fn.bsgrid.parseData.totalRows(dataType, gridData));
                var curPage = parseInt($.fn.bsgrid.parseData.curPage(dataType, gridData));
                curPage = Math.max(curPage, 1);

                if (options.settings.pageAll) {
                    // display all datas, no paging
                    curPage = 1;
                    options.settings.pageSize = totalRows;
                    $('#' + options.noPagingationId).html(totalRows);
                }

                var pageSize = options.settings.pageSize;
                var totalPages = parseInt(totalRows / pageSize);
                totalPages = parseInt((totalRows % pageSize == 0) ? totalPages : totalPages + 1);
                var curPageRowsNum = $.fn.bsgrid.parseData.getDataLen(dataType, gridData);
                curPageRowsNum = curPageRowsNum > pageSize ? pageSize : curPageRowsNum;
                curPageRowsNum = (curPage * pageSize < totalRows) ? curPageRowsNum : (totalRows - (curPage - 1) * pageSize);
                var startRow = (curPage - 1) * pageSize + 1;
                var endRow = startRow + curPageRowsNum - 1;
                startRow = curPageRowsNum <= 0 ? 0 : startRow;
                endRow = curPageRowsNum <= 0 ? 0 : endRow;

                // set options pagination values
                options.totalRows = totalRows;
                options.totalPages = totalPages;
                options.curPage = curPage;
                options.curPageRowsNum = curPageRowsNum;
                options.startRow = startRow;
                options.endRow = endRow;

                if (!options.settings.pageAll) {
                    $.fn.bsgrid.setPagingValues(options);
                }

                if (options.settings.displayPagingToolbarOnlyMultiPages && totalPages <= 1) {
                    $('#' + options.pagingId).hide();
                    $('#' + options.pagingOutTabId).hide();
                } else {
                    $('#' + options.pagingOutTabId).show();
                    $('#' + options.pagingId).show();
                }

                $.fn.bsgrid.setGridBlankBody(options);
                if (curPageRowsNum == 0) {
                    return;
                }

                var data = $.fn.bsgrid.parseData.data(dataType, gridData);
                var dataLen = data.length;
                // add rows click event
                $.fn.bsgrid.addRowsClickEvent(options);
                $.fn.bsgrid.getRows(options).each(
                    function (i) {
                        var trObj = $(this);
                        var record = null;
                        if (i < curPageRowsNum && i < dataLen) {
                            // support parse return all datas or only return current page datas
                            record = $.fn.bsgrid.parseData.getRecord(dataType, data, dataLen != totalRows ? i : startRow + i - 1);
                        }
                        $.fn.bsgrid.storeRowData(i, record, options);

                        for (var key in options.settings.extend.renderPerRowMethods) {
                            options.settings.extend.renderPerRowMethods[key](record, i, trObj, options);
                        }
                        options.settings.additionalRenderPerRow(record, i, trObj, options);
                        for (var key in options.settings.event.customRowEvents) {
                            trObj.bind(key, {
                                record: record,
                                rowIndex: i,
                                trObj: trObj,
                                options: options
                            }, function (event) {
                                options.settings.event.customRowEvents[key](event.data.record, event.data.rowIndex, event.data.trObj, event.data.options)
                            });
                        }

                        var columnsModel = options.columnsModel;
                        $(this).find('td').each(function (j) {
                            var tdObj = $(this);
                            if (i < curPageRowsNum && i < dataLen) {
                                // column index
                                var index = columnsModel[j].index;
                                // column render
                                var render = columnsModel[j].render;
                                if (render != '') {
                                    var render_method = eval(render);
                                    var render_html = render_method(record, i, j, options);
                                    tdObj.html(render_html);
                                } else if (index != '') {
                                    var value = $.fn.bsgrid.parseData.getColumnValue(dataType, record, index);
                                    // column tip
                                    if (columnsModel[j].tip == 'true') {
                                        $.fn.bsgrid.columnTip(this, value, record);
                                    }
                                    if (options.settings.longLengthAotoSubAndTip) {
                                        $.fn.bsgrid.longLengthSubAndTip(this, value, columnsModel[j].maxLen, record);
                                    } else {
                                        tdObj.html(value);
                                    }
                                }
                            } else {
                                tdObj.html('&nbsp;');
                            }
                            for (var key in options.settings.extend.renderPerColumnMethods) {
                                var renderPerColumn_html = options.settings.extend.renderPerColumnMethods[key](record, i, j, tdObj, trObj, options);
                                if (renderPerColumn_html != null && renderPerColumn_html != false) {
                                    tdObj.html(renderPerColumn_html);
                                }
                            }
                            options.settings.additionalRenderPerColumn(record, i, j, tdObj, trObj, options);
                            for (var key in options.settings.event.customCellEvents) {
                                tdObj.bind(key, {
                                    record: record,
                                    rowIndex: i,
                                    colIndex: j,
                                    tdObj: tdObj,
                                    trObj: trObj,
                                    options: options
                                }, function (event) {
                                    options.settings.event.customCellEvents[key](event.data.record, event.data.rowIndex, event.data.colIndex, event.data.tdObj, event.data.trObj, event.data.options);
                                });
                            }
                        });
                    }
                );
            } else {
                $.fn.bsgrid.alert($.bsgridLanguage.errorForRequestData);
            }
            for (var key in options.settings.extend.afterRenderGridMethods) {
                options.settings.extend.afterRenderGridMethods[key](parseSuccess, gridData, options);
            }
            options.settings.additionalAfterRenderGrid(parseSuccess, gridData, options);
        },

        addRowsClickEvent: function (options) {
            $.fn.bsgrid.getRows(options).filter(':lt(' + options.curPageRowsNum + ')').click(function () {
                if ($(this).hasClass('selected')) {
                    $.fn.bsgrid.unSelectRow(options);
                } else {
                    $.fn.bsgrid.selectRow($.fn.bsgrid.getRows(options).index($(this)), options);
                }
            });
        },

        getRows: function (options) {
            return $('#' + options.gridId + ' tbody tr');
        },

        getRow: function (row, options) {
            return $.fn.bsgrid.getRows(options).eq(row);
        },

        getRowCells: function (row, options) {
            return $.fn.bsgrid.getRow(row, options).find('td');
        },

        getColCells: function (col, options) {
            return $.fn.bsgrid.getRows(options).find('td:nth-child(' + (col + 1) + ')');
        },

        getCell: function (row, col, options) {
            return $.fn.bsgrid.getRowCells(row, options).eq(col);
        },

        getSelectedRow: function (options) {
            return $.fn.bsgrid.getRows(options).filter('.selected');
        },

        getSelectedRowIndex: function (options) {
            return $.fn.bsgrid.getRows(options).index($.fn.bsgrid.getSelectedRow(options));
        },

        selectRow: function (row, options) {
            $.fn.bsgrid.unSelectRow(options);
            var trObj = $.fn.bsgrid.getRow(row, options);
            trObj.addClass('selected');
            if (options.settings.rowSelectedColor) {
                trObj.addClass('success');
            }
            if (!!options.settings.event.selectRowEvent) {
                options.settings.event.selectRowEvent($.fn.bsgrid.getRowRecord(trObj), row, trObj, options);
            }
        },

        unSelectRow: function (options) {
            var row = $.fn.bsgrid.getSelectedRowIndex(options);
            if (row != -1) {
                var trObj = $.fn.bsgrid.getRow(row, options);
                trObj.removeClass('selected').removeClass('success');
                if (!!options.settings.event.unselectRowEvent) {
                    options.settings.event.unselectRowEvent($.fn.bsgrid.getRowRecord(trObj), row, trObj, options);
                }
            }
        },

        getUserdata: function (options) {
            $('#' + options.gridId).data('userdata');
        },

        storeUserdata: function (userdata, options) {
            $('#' + options.gridId).data('userdata', userdata);
        },

        getRowRecord: function (rowObj) {
            var record = rowObj.data('record');
            return record == undefined ? null : record;
        },

        storeRowData: function (row, record, options) {
            $.fn.bsgrid.getRow(row, options).data('record', record);
        },

        getAllRecords: function (options) {
            var records = [];
            $.fn.bsgrid.getRows(options).each(function () {
                var record = $.fn.bsgrid.getRowRecord($(this));
                if (record != null) {
                    records[records.length] = record;
                }
            });
            return records;
        },

        getRecord: function (row, options) {
            return $.fn.bsgrid.getRowRecord($.fn.bsgrid.getRow(row, options));
        },

        getRecordIndexValue: function (record, index, options) {
            if (record == null) {
                return '';
            } else {
                return $.fn.bsgrid.parseData.getColumnValue(options.settings.dataType, record, index);
            }
        },

        getColumnValue: function (row, index, options) {
            var record = $.fn.bsgrid.getRecord(row, options);
            return $.fn.bsgrid.getRecordIndexValue(record, index, options);
        },

        getCellRecordValue: function (row, col, options) {
            var index = $.trim($.fn.bsgrid.getColumnModel(col, options).index);
            if (index == '') {
                return '';
            } else {
                return $.fn.bsgrid.getColumnValue(row, index, options);
            }
        },

        sort: function (obj, options) {
            options.sortName = '';
            options.sortOrder = '';
            var aObj = $(obj).find('a');
            var field = $(aObj).attr('sortName');
            var columnsModel = options.columnsModel;
            $.fn.bsgrid.getGridHeaderObject(options).each(function (i) {
                var sortName = columnsModel[i].sortName;
                if (sortName != '') {
                    var sortOrder = $.fn.bsgrid.getSortOrder($(this), options);

                    if (!options.settings.multiSort && sortName != field) {
                        // revert style
                        $(this).find('a').attr('class', 'sort sort-view');
                    } else {
                        if (sortName == field) {
                            if (sortOrder == '') {
                                sortOrder = 'desc';
                            } else if (sortOrder == 'desc') {
                                sortOrder = 'asc';
                            } else if (sortOrder == 'asc') {
                                sortOrder = '';
                            }
                            $(this).find('a').attr('class', 'sort sort-' + (sortOrder == '' ? 'view' : sortOrder));
                        }
                        if (sortOrder != '') {
                            options.sortName = ($.trim(options.sortName) == '') ? sortName : (options.sortName + ',' + sortName);
                            options.sortOrder = ($.trim(options.sortOrder) == '') ? sortOrder : (options.sortOrder + ',' + sortOrder);
                        }
                    }
                }
            });

            $.fn.bsgrid.refreshPage(options);
        },

        getSortOrder: function (obj, options) {
            var sortOrder = $.trim($(obj).find('a').attr('class'));
            if (sortOrder == 'sort sort-view') {
                sortOrder = '';
            } else if (sortOrder == 'sort sort-asc') {
                sortOrder = 'asc';
            } else if (sortOrder == 'sort sort-desc') {
                sortOrder = 'desc';
            } else {
                sortOrder = '';
            }
            return sortOrder;
        },

        /**
         * Note only return thead last tr's th.
         *
         * @param options
         * @returns {*}
         */
        getGridHeaderObject: function (options) {
            return $('#' + options.gridId + ' thead tr:last').find('th');
        },

        getColumnModel: function (colIndex, options) {
            return options.columnsModel[colIndex];
        },

        appendHeaderSort: function (options) {
            var columnsModel = options.columnsModel;
            // grid header
            $.fn.bsgrid.getGridHeaderObject(options).each(function (i) {
                // sort
                if (columnsModel[i].sortName != '') {
                    var sortName = columnsModel[i].sortName;
                    // default sort and direction
                    var sortOrder = columnsModel[i].sortOrder;
                    var sortHtml = '<a href="javascript:void(0);" sortName="' + sortName + '" class="sort ';
                    if (sortOrder != '' && (sortOrder == 'desc' || sortOrder == 'asc')) {
                        options.sortName = ($.trim(options.sortName) == '') ? sortName : (options.sortName + ',' + sortName);
                        options.sortOrder = ($.trim(options.sortOrder) == '') ? sortOrder : (options.sortOrder + ',' + sortOrder);
                        sortHtml += 'sort-' + sortOrder;
                    } else {
                        sortHtml += 'sort-view';
                    }
                    sortHtml += '">&nbsp;&nbsp;&nbsp;</a>'; // use: "&nbsp;&nbsp;&nbsp;", different from: "&emsp;" is: IE8 and IE9 not display "&emsp;"
                    $(this).append(sortHtml).find('.sort').click(function () {
                        $.fn.bsgrid.sort($(this).parent('th'), options);
                    });
                }
            });
        },

        setGridBlankBody: function (options) {
            // remove rows
            $.fn.bsgrid.getRows(options).remove();

            var header = $.fn.bsgrid.getGridHeaderObject(options);
            // add rows
            var rowSb = '';
            if (options.settings.pageSize > 0) {
                var columnsModel = options.columnsModel;

                var trSb = new StringBuilder();
                trSb.append('<tr>');
                for (var hi = 0; hi < header.length; hi++) {
                    trSb.append('<td style="text-align: ' + columnsModel[hi].align + ';');
                    if (columnsModel[hi].hidden == 'true') {
                        header.eq(hi).css('display', 'none');
                        trSb.append(' display: none;');
                    }
                    trSb.append('"');
                    trSb.append('>&nbsp;</td>');
                }
                trSb.append('</tr>');
                rowSb = trSb.toString();
            }
            var rowsSb = new StringBuilder();
            var curPageRowsNum = options.settings.pageSize;
            if (!options.settings.displayBlankRows) {
                curPageRowsNum = options.endRow - options.startRow + 1;
                curPageRowsNum = options.endRow > 0 ? curPageRowsNum : 0;
            }
            if (curPageRowsNum == 0) {
                rowsSb.append('<tr><td colspan="' + header.length + '">' + $.bsgridLanguage.noDataToDisplay + '</td></tr>');
            } else {
                for (var pi = 0; pi < curPageRowsNum; pi++) {
                    rowsSb.append(rowSb);
                }
            }
            $('#' + options.gridId + ' tbody').append(rowsSb.toString());

            if (curPageRowsNum != 0) {
                if (options.settings.stripeRows) {
                    $.fn.bsgrid.getRows(options).filter(':even').addClass('even_index_row');
                }
                if (options.settings.rowHoverColor) {
                    $('#' + options.gridId + ' tbody tr').hover(function () {
                        $(this).addClass('row_hover');
                    }, function () {
                        $(this).removeClass('row_hover');
                    });
                }
            }

            if (!options.settings.lineWrap) {
                $.fn.bsgrid.getRows(options).find('td').addClass('lineNoWrap');
            } else {
                $.fn.bsgrid.getRows(options).find('td').addClass('lineWrap');
            }
        },

        createPagingOutTab: function (options) {
            var pagingOutTabSb = new StringBuilder();
            pagingOutTabSb.append('<table id="' + options.pagingOutTabId + '" class="bsgridPagingOutTab" style="display: none;"><tr><td align="' + options.settings.pagingToolbarAlign + '">');
            // display all datas, no paging
            if (options.settings.pageAll) {
                pagingOutTabSb.append($.bsgridLanguage.noPagingation(options.noPagingationId) + '&nbsp;&nbsp;&nbsp;');
            }
            pagingOutTabSb.append('</td></tr></table>');
            var pger = $('#' + options.gridId+'-pager');
            if (pger.length == 1) {
                pger.append(pagingOutTabSb.toString());
            }else{
                $('#' + options.gridId).after(pagingOutTabSb.toString());
            }
        },

        clearGridBodyData: function (options) {
            $.fn.bsgrid.getRows(options).find('td').html('&nbsp;');
        },

        /**
         * add lock screen.
         *
         * @param options
         */
        addLockScreen: function (options) {
            if ($('.bsgrid.lockscreen').length == 0) {
                var lockScreenHtml = new StringBuilder();
                lockScreenHtml.append('<div class="bsgrid lockscreen" times="0">');
                lockScreenHtml.append('</div>');
                lockScreenHtml.append('<div class="bsgrid loading_div">');
                lockScreenHtml.append('<table><tr><td><center><div class="bsgrid loading"><span>&nbsp;&emsp;</span>&nbsp;' + $.bsgridLanguage.loadingDataMessage + '&emsp;<center></div></td></tr></table>');
                lockScreenHtml.append('</div>');
                $('body').append(lockScreenHtml.toString());
            }
        },

        /**
         * open lock screen.
         *
         * @param options
         */
        lockScreen: function (options) {
            $('.bsgrid.lockscreen').attr('times', parseInt($('.bsgrid.lockscreen').attr('times')) + 1);
            if ($('.bsgrid.lockscreen').css('display') == 'none') {
                $('.bsgrid.lockscreen').show();
                $('.bsgrid.loading_div').show();
            }
        },

        /**
         * close lock screen.
         *
         * @param options
         */
        unlockScreen: function (options) {
            $('.bsgrid.lockscreen').attr('times', parseInt($('.bsgrid.lockscreen').attr('times')) - 1);
            if ($('.bsgrid.lockscreen').attr('times') == '0') {
                // delay 0.05s, to make lock screen look better
                setTimeout(function () {
                    $('.bsgrid.lockscreen').hide();
                    $('.bsgrid.loading_div').hide();
                }, 50);
            }
        },

        /**
         * tip column.
         *
         * @param obj column td obj
         * @param value column's value
         * @param record row record
         */
        columnTip: function (obj, value, record) {
            $(obj).attr('title', value);
        },

        /**
         * alert message.
         *
         * @param msg message
         */
        alert: function (msg) {
            try {
                $.bsgrid.alert(msg);
            } catch (e) {
                alert(msg);
            }
        },

        /**
         * if column's value length longer than it, auto sub and tip it.
         *    sub: txt.substring(0, MaxLength-3) + '...'.
         *
         * @param obj column td obj
         * @param value column's value
         * @param maxLen max length
         * @param record row record
         */
        longLengthSubAndTip: function (obj, value, maxLen, record) {
            var tip = false;
            if (value.length > maxLen) {
                try {
                    if (value.indexOf('<') < 0 || value.indexOf('>') < 2 || $(value).text().length == 0) {
                        tip = true;
                    }
                } catch (e) {
                    tip = true;
                }
            }
            if (tip) {
                $(obj).html(value.substring(0, maxLen - 3) + '...');
                $.fn.bsgrid.columnTip(obj, value, record);
            } else {
                $(obj).html(value);
            }
        },

        getPagingObj: function (options) {
            return $.fn.bsgrid.getGridObj(options.gridId).pagingObj;
        },

        getCurPage: function (options) {
            return $.fn.bsgrid.getPagingObj(options).getCurPage();
        },

        refreshPage: function (options) {
            if (!options.settings.pageAll) {
                $.fn.bsgrid.getPagingObj(options).refreshPage();
            } else {
                $.fn.bsgrid.page(1, options);
            }
        },

        firstPage: function (options) {
            $.fn.bsgrid.getPagingObj(options).firstPage();
        },

        prevPage: function (options) {
            $.fn.bsgrid.getPagingObj(options).prevPage();
        },

        nextPage: function (options) {
            $.fn.bsgrid.getPagingObj(options).nextPage();
        },

        lastPage: function (options) {
            $.fn.bsgrid.getPagingObj(options).lastPage();
        },

        gotoPage: function (options, goPage) {
            $.fn.bsgrid.getPagingObj(options).gotoPage(goPage);
        },

        /**
         * init paging.
         *
         * @param options grid options
         */
        initPaging: function (options) {
            $('#' + options.pagingOutTabId + ' td').attr('id', options.pagingId);
            // config same properties's
            return $.fn.bsgrid_paging.init(options.pagingId, {
                gridId: options.gridId,
                pageSize: options.settings.pageSize,
                pageSizeSelect: options.settings.pageSizeSelect,
                pageSizeForGrid: options.settings.pageSizeForGrid,
                pageIncorrectTurnAlert: options.settings.pageIncorrectTurnAlert,
                pagingLittleToolbar: options.settings.pagingLittleToolbar,
                pagingBtnClass: options.settings.pagingBtnClass
            });
        },

        /**
         * Set paging values.
         *
         * @param options grid options
         */
        setPagingValues: function (options) {
            $.fn.bsgrid.getPagingObj(options).setPagingValues(options.curPage, options.totalRows);
        }

    };

})(jQuery);


/**
 * jQuery.bsgrid v1.36 by @Baishui2004
 * Copyright 2014 Apache v2 License
 * https://github.com/baishui2004/jquery.bsgrid
 */
/**
 * require common.js.
 *
 * @author Baishui2004
 * @Date August 31, 2014
 */
(function ($) {

    $.fn.bsgrid_paging = {

        // defaults settings
        defaults: {
            loopback: false, // if true, page 1 prev then totalPages, totalPages next then 1
            pageSize: 20, // page size
            pageSizeSelect: false, // if display pageSize select option
            pageSizeForGrid: [5, 10, 20, 25, 50, 100, 200, 500], // pageSize select option
            pageIncorrectTurnAlert: true, // if turn incorrect page alert(firstPage, prevPage, nextPage, lastPage)
            pagingLittleToolbar: false, // if display paging little toolbar
            pagingBtnClass: 'pagingBtn' // paging toolbar button css class
        },

        pagingObjs: {},

        /**
         * init paging.
         */
        init: function (pagingId, settings) {
            var options = {
                settings: $.extend(true, {}, $.fn.bsgrid_paging.defaults, settings),

                pagingId: pagingId,
                totalRowsId: pagingId + '_totalRows',
                totalPagesId: pagingId + '_totalPages',
                curPageId: pagingId + '_curPage',
                gotoPageInputId: pagingId + '_gotoPageInput',
                gotoPageId: pagingId + '_gotoPage',
                refreshPageId: pagingId + '_refreshPage',
                pageSizeId: pagingId + '_pageSize',
                firstPageId: pagingId + '_firstPage',
                prevPageId: pagingId + '_prevPage',
                nextPageId: pagingId + '_nextPage',
                lastPageId: pagingId + '_lastPage',
                startRowId: pagingId + '_startRow',
                endRowId: pagingId + '_endRow',

                totalRows: 0,
                totalPages: 0,
                curPage: 1,
                curPageRowsNum: 0,
                startRow: 0,
                endRow: 0
            };
            if (settings.pageSizeForGrid != undefined) {
                options.settings.pageSizeForGrid = settings.pageSizeForGrid;
            }

            var pagingObj = {
                options: options,
                page: function (curPage) {
                    $.fn.bsgrid_paging.page(curPage, options);
                },
                getCurPage: function () {
                    return $.fn.bsgrid_paging.getCurPage(options);
                },
                refreshPage: function () {
                    $.fn.bsgrid_paging.refreshPage(options);
                },
                firstPage: function () {
                    $.fn.bsgrid_paging.firstPage(options);
                },
                prevPage: function () {
                    $.fn.bsgrid_paging.prevPage(options);
                },
                nextPage: function () {
                    $.fn.bsgrid_paging.nextPage(options);
                },
                lastPage: function () {
                    $.fn.bsgrid_paging.lastPage(options);
                },
                gotoPage: function (goPage) {
                    $.fn.bsgrid_paging.gotoPage(options, goPage);
                },
                createPagingToolbar: function () {
                    return $.fn.bsgrid_paging.createPagingToolbar(options);
                },
                setPagingToolbarEvents: function () {
                    $.fn.bsgrid_paging.setPagingToolbarEvents(options);
                },
                dynamicChangePagingButtonStyle: function () {
                    $.fn.bsgrid_paging.dynamicChangePagingButtonStyle(options);
                },
                setPagingValues: function (curPage, totalRows) {
                    $.fn.bsgrid_paging.setPagingValues(curPage, totalRows, options);
                }
            };

            // store mapping paging id to pagingObj
            $.fn.bsgrid_paging.pagingObjs[pagingId] = pagingObj;

            $('#' + pagingId).append(pagingObj.createPagingToolbar());
            // page size select
            if (options.settings.pageSizeSelect) {
                if ($.inArray(options.settings.pageSize, options.settings.pageSizeForGrid) == -1) {
                    options.settings.pageSizeForGrid.push(options.settings.pageSize);
                }
                options.settings.pageSizeForGrid.sort(function (a, b) {
                    return a - b;
                });
                var optionsSb = new StringBuilder();
                for (var i = 0; i < options.settings.pageSizeForGrid.length; i++) {
                    var pageVal = options.settings.pageSizeForGrid[i];
                    optionsSb.append('<option value="' + pageVal + '">' + pageVal + '</option>');
                }
                $('#' + options.pageSizeId).html(optionsSb.toString()).val(options.settings.pageSize);
            }
            pagingObj.setPagingToolbarEvents();

            return pagingObj;
        },

        getPagingObj: function (pagingId) {
            var obj = $.fn.bsgrid_paging.pagingObjs[pagingId];
            return obj ? obj : null;
        },

        page: function (curPage, options) {
            var gridObj = $.fn.bsgrid.getGridObj(options.settings.gridId);
            gridObj.options.settings.pageSize = options.settings.pageSize;
            $.fn.bsgrid.page(curPage, gridObj.options);
        },

        getCurPage: function (options) {
            var curPage = $('#' + options.curPageId).html();
            return curPage == '' ? 1 : curPage;
        },

        refreshPage: function (options) {
            $.fn.bsgrid_paging.page($.fn.bsgrid_paging.getCurPage(options), options);
        },

        firstPage: function (options) {
            var curPage = $.fn.bsgrid_paging.getCurPage(options);
            if (curPage <= 1) {
                $.fn.bsgrid_paging.incorrectTurnAlert(options, $.bsgridLanguage.isFirstPage);
                return;
            }
            $.fn.bsgrid_paging.page(1, options);
        },

        prevPage: function (options) {
            var curPage = $.fn.bsgrid_paging.getCurPage(options);
            if (curPage <= 1) {
                if (options.settings.loopback && options.totalPages > 0) {
                    $.fn.bsgrid_paging.page(options.totalPages, options);
                    return;
                } else {
                    $.fn.bsgrid_paging.incorrectTurnAlert(options, $.bsgridLanguage.isFirstPage);
                    return;
                }
            }
            $.fn.bsgrid_paging.page(parseInt(curPage) - 1, options);
        },

        nextPage: function (options) {
            var curPage = $.fn.bsgrid_paging.getCurPage(options);
            if (curPage >= options.totalPages) {
                if (options.settings.loopback && curPage > 0) {
                    $.fn.bsgrid_paging.page(1, options);
                    return;
                } else {
                    $.fn.bsgrid_paging.incorrectTurnAlert(options, $.bsgridLanguage.isLastPage);
                    return;
                }
            }
            $.fn.bsgrid_paging.page(parseInt(curPage) + 1, options);
        },

        lastPage: function (options) {
            var curPage = $.fn.bsgrid_paging.getCurPage(options);
            if (curPage >= options.totalPages) {
                $.fn.bsgrid_paging.incorrectTurnAlert(options, $.bsgridLanguage.isLastPage);
                return;
            }
            $.fn.bsgrid_paging.page(options.totalPages, options);
        },

        gotoPage: function (options, goPage) {
            if (goPage == undefined) {
                goPage = $('#' + options.gotoPageInputId).val();
            }
            if ($.trim(goPage) == '' || isNaN(goPage)) {
                $.fn.bsgrid_paging.alert($.bsgridLanguage.needInteger);
            } else if (parseInt(goPage) < 1 || parseInt(goPage) > options.totalPages) {
                $.fn.bsgrid_paging.alert($.bsgridLanguage.needRange(1, options.totalPages));
            } else {
                $('#' + options.gotoPageInputId).val(goPage);
                $.fn.bsgrid_paging.page(parseInt(goPage), options);
            }
        },

        incorrectTurnAlert: function (options, msg) {
            if (options.settings.pageIncorrectTurnAlert) {
                $.fn.bsgrid_paging.alert(msg);
            }
        },

        /**
         * alert message.
         *
         * @param msg message
         */
        alert: function (msg) {
            try {
                $.bsgrid.alert(msg);
            } catch (e) {
                alert(msg);
            }
        },

        /**
         * create paging toolbar.
         *
         * @param options
         */
        createPagingToolbar: function (options) {
            var pagingSb = new StringBuilder();
            var littleBar = options.settings.pagingLittleToolbar;

            pagingSb.append('<table class="bsgridPaging' + ( littleBar ? ' pagingLittleToolbar' : '') + (options.settings.pageSizeSelect ? '' : ' noPageSizeSelect') + '">');
            pagingSb.append('<tr>');
            if (options.settings.pageSizeSelect) {
                pagingSb.append('<td>' + $.bsgridLanguage.pagingToolbar.pageSizeDisplay(options.pageSizeId, littleBar) + '</td>');
            }
            pagingSb.append('<td>' + $.bsgridLanguage.pagingToolbar.currentDisplayRows(options.startRowId, options.endRowId, littleBar) + '</td>');
            pagingSb.append('<td>' + $.bsgridLanguage.pagingToolbar.totalRows(options.totalRowsId) + '</td>');
            var btnClass = options.settings.pagingBtnClass;
            pagingSb.append('<td>');
            pagingSb.append('<input class="' + btnClass + ' firstPage" type="button" id="' + options.firstPageId + '" value="' + ( littleBar ? '' : $.bsgridLanguage.pagingToolbar.firstPage) + '" />');
            pagingSb.append('&nbsp;');
            pagingSb.append('<input class="' + btnClass + ' prevPage" type="button" id="' + options.prevPageId + '" value="' + ( littleBar ? '' : $.bsgridLanguage.pagingToolbar.prevPage) + '" />');
            pagingSb.append('</td>');
            pagingSb.append('<td>' + $.bsgridLanguage.pagingToolbar.currentDisplayPageAndTotalPages(options.curPageId, options.totalPagesId) + '</td>');
            pagingSb.append('<td>');
            pagingSb.append('<input class="' + btnClass + ' nextPage" type="button" id="' + options.nextPageId + '" value="' + ( littleBar ? '' : $.bsgridLanguage.pagingToolbar.nextPage) + '" />');
            pagingSb.append('&nbsp;');
            pagingSb.append('<input class="' + btnClass + ' lastPage" type="button" id="' + options.lastPageId + '" value="' + ( littleBar ? '' : $.bsgridLanguage.pagingToolbar.lastPage) + '" />');
            pagingSb.append('</td>');
            pagingSb.append('<td class="gotoPageInputTd">');
            pagingSb.append('<input class="gotoPageInput" type="text" id="' + options.gotoPageInputId + '" />');
            pagingSb.append('</td>');
            pagingSb.append('<td class="gotoPageButtonTd">');
            pagingSb.append('<input class="' + btnClass + ' gotoPage" type="button" id="' + options.gotoPageId + '" value="' + ( littleBar ? '' : $.bsgridLanguage.pagingToolbar.gotoPage) + '" />');
            pagingSb.append('</td>');
            pagingSb.append('<td class="refreshPageTd">');
            pagingSb.append('<input class="' + btnClass + ' refreshPage" type="button" id="' + options.refreshPageId + '" value="' + ( littleBar ? '' : $.bsgridLanguage.pagingToolbar.refreshPage) + '" />');
            pagingSb.append('</td>');
            pagingSb.append('</tr>');
            pagingSb.append('</table>');

            return pagingSb.toString();
        },

        /**
         * set paging toolbar events.
         *
         * @param options
         */
        setPagingToolbarEvents: function (options) {
            if (options.settings.pageSizeSelect) {
                $('#' + options.pageSizeId).change(function () {
                    options.settings.pageSize = parseInt($(this).val());
                    $(this).trigger('blur');
                    // if change pageSize, then page first
                    $.fn.bsgrid_paging.page(1, options);
                });
            }

            $('#' + options.firstPageId).click(function () {
                $.fn.bsgrid_paging.firstPage(options);
            });
            $('#' + options.prevPageId).click(function () {
                $.fn.bsgrid_paging.prevPage(options);
            });
            $('#' + options.nextPageId).click(function () {
                $.fn.bsgrid_paging.nextPage(options);
            });
            $('#' + options.lastPageId).click(function () {
                $.fn.bsgrid_paging.lastPage(options);
            });
            $('#' + options.gotoPageInputId).keyup(function (e) {
                if (e.which == 13) {
                    $.fn.bsgrid_paging.gotoPage(options);
                }
            });
            $('#' + options.gotoPageId).click(function () {
                $.fn.bsgrid_paging.gotoPage(options);
            });
            $('#' + options.refreshPageId).click(function () {
                $.fn.bsgrid_paging.refreshPage(options);
            });
        },

        /**
         * dynamic change paging button style.
         *
         * @param options
         */
        dynamicChangePagingButtonStyle: function (options) {
            var disabledCls = 'disabledCls';
            if (options.curPage <= 1) {
                $('#' + options.firstPageId).addClass(disabledCls);
                $('#' + options.prevPageId).addClass(disabledCls);
            } else {
                $('#' + options.firstPageId).removeClass(disabledCls);
                $('#' + options.prevPageId).removeClass(disabledCls);
            }
            if (options.curPage >= options.totalPages) {
                $('#' + options.nextPageId).addClass(disabledCls);
                $('#' + options.lastPageId).addClass(disabledCls);
            } else {
                $('#' + options.nextPageId).removeClass(disabledCls);
                $('#' + options.lastPageId).removeClass(disabledCls);
            }
        },

        /**
         * Set paging values.
         *
         * @param curPage current page number
         * @param totalRows total rows number
         * @param options paging options
         */
        setPagingValues: function (curPage, totalRows, options) {
            curPage = Math.max(curPage, 1);

            var pageSize = options.settings.pageSize;
            var totalPages = parseInt(totalRows / pageSize);
            totalPages = parseInt((totalRows % pageSize == 0) ? totalPages : totalPages + 1);
            var curPageRowsNum = (curPage * pageSize < totalRows) ? pageSize : (totalRows - (curPage - 1) * pageSize);
            var startRow = (curPage - 1) * pageSize + 1;
            var endRow = startRow + curPageRowsNum - 1;
            startRow = curPageRowsNum <= 0 ? 0 : startRow;
            endRow = curPageRowsNum <= 0 ? 0 : endRow;

            options.totalRows = totalRows;
            options.totalPages = totalPages;
            options.curPage = curPage;
            options.curPageRowsNum = curPageRowsNum;
            options.startRow = startRow;
            options.endRow = endRow;

            $('#' + options.totalRowsId).html(options.totalRows);
            $('#' + options.totalPagesId).html(options.totalPages);
            $('#' + options.curPageId).html(options.curPage);
            $('#' + options.startRowId).html(options.startRow);
            $('#' + options.endRowId).html(options.endRow);

            $.fn.bsgrid_paging.dynamicChangePagingButtonStyle(options);
        }
    };

})(jQuery);



/**
 * jQuery.bsgrid v1.36 by @Baishui2004
 * Copyright 2014 Apache v2 License
 * https://github.com/baishui2004/jquery.bsgrid
 */
/**
 * require common.js, util.js, grid.js.
 *
 * @author Baishui2004
 * @Date May 18, 2015
 */
(function ($) {

    // extend settings
    $.fn.bsgrid.defaults.extend.settings = {
        supportGridEdit: false, // if support extend grid edit
        supportGridEditTriggerEvent: 'rowClick', // values: ''(means no need Trigger), 'rowClick', 'rowDoubleClick', 'cellClick', 'cellDoubleClick'
        supportColumnMove: false, // if support extend column move
        searchConditionsContainerId: '', // simple search conditions's container id
        fixedGridHeader: false, // fixed grid header, auto height scroll
        fixedGridHeight: '320px', // fixed grid height, auto scroll
        gridEditConfigs: {
            text: {
                build: function (edit, value, record, rowIndex, colIndex, tdObj, trObj, options) {
                    return value + '<input class="' + 'bsgrid_editgrid_edit' + '" type="' + edit + '" value="' + value + '"/>';
                },
                val: function (formObj) {
                    return formObj.val();
                }
            },
            checkbox: {
                build: function (edit, value, record, rowIndex, colIndex, tdObj, trObj, options) {
                    return value + '<input class="' + 'bsgrid_editgrid_checkbox' + '" type="' + edit + '" value="' + value + '"/>';
                },
                val: function (formObj) {
                    return formObj.val();
                }
            },
            textarea: {
                build: function (edit, value, record, rowIndex, colIndex, tdObj, trObj, options) {
                    return value + '<textarea class="bsgrid_editgrid_edit">' + value + '</textarea>';
                },
                val: function (formObj) {
                    return formObj.val();
                }
            }
        }
    };
    $.fn.bsgrid.defaults.extend.settings.gridEditConfigs.hidden = $.fn.bsgrid.defaults.extend.settings.gridEditConfigs.text;
    $.fn.bsgrid.defaults.extend.settings.gridEditConfigs.password = $.fn.bsgrid.defaults.extend.settings.gridEditConfigs.text;
    $.fn.bsgrid.defaults.extend.settings.gridEditConfigs.radio = $.fn.bsgrid.defaults.extend.settings.gridEditConfigs.text;
    $.fn.bsgrid.defaults.extend.settings.gridEditConfigs.button = $.fn.bsgrid.defaults.extend.settings.gridEditConfigs.text;

    // config properties's name
    $.extend(true, $.fn.bsgrid.defaults.colsProperties, {
        lineNumberAttr: 'w_num', // line number, value: line, total_line
        checkAttr: 'w_check', // value: true
        editAttr: 'w_edit', // grid edit forms' values: text, hidden, password, radio, button, checkbox, textarea
        aggAttr: 'w_agg' // aggregation, values: count, countNotNone, sum, avg, max, min, concat
    });

    // custom cell edit events: change, click, dbclick, focus ......
    $.fn.bsgrid.defaults.event.customCellEditEvents = {}; // method params: formObj, record, rowIndex, colIndex, tdObj, trObj, options

    $.fn.bsgrid.extendInitGrid = {}; // extend init grid
    $.fn.bsgrid.extendBeforeRenderGrid = {}; // extend before render grid
    $.fn.bsgrid.extendRenderPerRow = {}; // extend render per row
    $.fn.bsgrid.extendRenderPerColumn = {}; // extend render per column
    $.fn.bsgrid.extendAfterRenderGrid = {}; // extend after render grid
    $.fn.bsgrid.extendOtherMethods = {}; // extend other methods


    /*************** extend init grid start ***************/
    $.fn.bsgrid.extendInitGrid.initGridExtendOptions = function (gridId, options) {
        var columnsModel = options.columnsModel;
        var colsProperties = options.settings.colsProperties;
        $.fn.bsgrid.getGridHeaderObject(options).each(function (i) {
            columnsModel[i].lineNumber = $.trim($(this).attr(colsProperties.lineNumberAttr));
            columnsModel[i].check = $.trim($(this).attr(colsProperties.checkAttr));
            columnsModel[i].edit = $.trim($(this).attr(colsProperties.editAttr));
        });

        if ($('#' + options.gridId + ' tfoot tr td[' + colsProperties.aggAttr + '!=\'\']').length != 0) {
            $('#' + options.gridId + ' tfoot tr td').each(function (i) {
                columnsModel[i].aggName = '';
                columnsModel[i].aggIndex = '';
                var aggInfo = $.trim($(this).attr(colsProperties.aggAttr));
                if (aggInfo.length != 0) {
                    var aggInfoArray = aggInfo.split(',');
                    columnsModel[i].aggName = aggInfoArray[0].toLocaleLowerCase();
                    columnsModel[i].aggIndex = aggInfoArray.length > 1 ? aggInfoArray[1] : '';
                }
            });
        }

        if ($.fn.bsgrid.getGridHeaderObject(options).filter('[' + colsProperties.lineNumberAttr + '$=\'line\']').length != 0) {
            options.settings.extend.afterRenderGridMethods.renderLineNumber = $.fn.bsgrid.extendAfterRenderGrid.renderLineNumber;
        }
        if ($.fn.bsgrid.getGridHeaderObject(options).filter('[' + colsProperties.checkAttr + '=\'true\']').length != 0) {
            options.settings.extend.initGridMethods.initGridCheck = $.fn.bsgrid.extendInitGrid.initGridCheck;
            options.settings.extend.renderPerColumnMethods.renderCheck = $.fn.bsgrid.extendRenderPerColumn.renderCheck;
            options.settings.extend.afterRenderGridMethods.addCheckChangedEvent = $.fn.bsgrid.extendAfterRenderGrid.addCheckChangedEvent;
        }
        if (options.settings.extend.settings.supportGridEdit) {
            options.settings.extend.renderPerColumnMethods.renderForm = $.fn.bsgrid.extendRenderPerColumn.renderForm;
            options.settings.extend.afterRenderGridMethods.addGridEditEvent = $.fn.bsgrid.extendAfterRenderGrid.addGridEditEvent;
            var gridObj = $.fn.bsgrid.getGridObj(gridId);
            gridObj.activeGridEditMode = function () {
                return $.fn.bsgrid.defaults.extend.activeGridEditMode(options);
            };
            gridObj.getChangedRowsIndexs = function () {
                return $.fn.bsgrid.defaults.extend.getChangedRowsIndexs(options);
            };
            gridObj.getChangedRowsOldRecords = function () {
                return $.fn.bsgrid.defaults.extend.getChangedRowsOldRecords(options);
            };
            gridObj.getRowsChangedColumnsValue = function () {
                return $.fn.bsgrid.defaults.extend.getRowsChangedColumnsValue(options);
            };
            gridObj.deleteRow = function (row) {
                $.fn.bsgrid.defaults.extend.deleteRow(row, options);
            };
            gridObj.addNewEditRow = function () {
                $.fn.bsgrid.defaults.extend.addNewEditRow(options);
            };
        }
        if (options.settings.extend.settings.supportColumnMove) {
            options.settings.extend.initGridMethods.initColumnMove = $.fn.bsgrid.extendInitGrid.initColumnMove;
        }
        if (options.settings.extend.settings.fixedGridHeader) {
            options.settings.extend.initGridMethods.initFixedHeader = $.fn.bsgrid.extendOtherMethods.initFixedHeader;
            options.settings.extend.afterRenderGridMethods.fixedHeader = function (parseSuccess, gridData, options) {
                $.fn.bsgrid.extendOtherMethods.fixedHeader(false, options);
            };
        }
        if ($.trim(options.settings.extend.settings.searchConditionsContainerId) != '') {
            options.settings.extend.initGridMethods.initSearchConditions = $.fn.bsgrid.extendInitGrid.initSearchConditions;
        }
        if ($('#' + options.gridId + ' tfoot td[' + colsProperties.aggAttr + '!=\'\']').length != 0) {
            options.settings.extend.afterRenderGridMethods.aggregation = $.fn.bsgrid.extendAfterRenderGrid.aggregation;
        }
    };

    // init grid check
    $.fn.bsgrid.extendInitGrid.initGridCheck = function (gridId, options) {
        $.fn.bsgrid.getGridHeaderObject(options).each(function (hi) {
            if (options.columnsModel[hi].check == 'true') {
                if ($.trim($(this).html()) == '') {
                    $(this).html('<input class="bsgrid_editgrid_check" type="checkbox"/>');
                }
                $(this).find('input[type=checkbox]').change(function () {
                    var checked = $.bsgrid.adaptAttrOrProp($(this), 'checked') ? true : false;
                    $.bsgrid.adaptAttrOrProp($.fn.bsgrid.getRows(options).find('td:nth-child(' + (hi + 1) + ')>input[type=checkbox]'), 'checked', checked);
                });
            }
        });

        var gridObj = $.fn.bsgrid.getGridObj(gridId);
        gridObj.getCheckedRowsIndexs = function () {
            return $.fn.bsgrid.defaults.extend.getCheckedRowsIndexs(options);
        };
        gridObj.getCheckedRowsRecords = function () {
            return $.fn.bsgrid.defaults.extend.getCheckedRowsRecords(options);
        };
        gridObj.getCheckedValues = function (index) {
            return $.fn.bsgrid.defaults.extend.getCheckedValues(index, options);
        };
    };

    // init search conditions
    $.fn.bsgrid.extendInitGrid.initSearchConditions = function (gridId, options) {
        var conditionsHtml = new StringBuilder();
        conditionsHtml.append('<select class="bsgrid_conditions_select">');
        var params = {};
        $.fn.bsgrid.getGridHeaderObject(options).each(function (i) {
            var index = options.columnsModel[i].index;
            var text = $.trim($(this).text());
            if (index != '' && text != '' && $.trim(params[index]) == '') {
                params[index] = text;
            }
        });
        for (var key in params) {
            conditionsHtml.append('<option value="' + key + '">' + params[key] + '</option>');
        }
        conditionsHtml.append('</select>');
        conditionsHtml.append('&nbsp;');
        conditionsHtml.append('<input type="text" class="bsgrid_conditions_input" />');
        $('#' + options.settings.extend.settings.searchConditionsContainerId).html(conditionsHtml.toString());
        $('#' + options.settings.extend.settings.searchConditionsContainerId + ' select.bsgrid_conditions_select').change(function () {
            $(this).next('input.bsgrid_conditions_input').attr('name', $(this).val());
        }).trigger('change');
    };

    // init column move
    $.fn.bsgrid.extendInitGrid.initColumnMove = function (gridId, options) {
        if ($('#' + options.gridId + ' thead tr').length != 1) {
            return;
        }
        $('#' + options.gridId).css({'table-layout': 'fixed'});
        var headObj = $.fn.bsgrid.getGridHeaderObject(options);
        var headLen = headObj.length;
        headObj.each(function (i) {
            var obj = this;

            // disable select text when mouse moving
            $(obj).bind('selectstart', function () { // IE/Safari/Chrome
                return false;
            });
            $(obj).css('-moz-user-select', 'none'); // Firefox/Opera

            $(obj).mousedown(function () {
                bindDownData(obj, i, headLen);
            });
            $(obj).mousemove(function (e) {
                e = e || event;
                var left = $(obj).offset().left;
                var nObj = 0, nLeft = 0;
                if (i != headLen - 1) {
                    nObj = $(obj).next();
                    nLeft = nObj.offset().left;
                }
                var mObj = obj;
                if (i != headLen - 1 && e.clientX - nLeft > -10) {
                    mObj = nObj;
                }
                if ((i != 0 && e.clientX - left < 10) || (i != headLen - 1 && e.clientX - nLeft > -10)) {
                    $(obj).css({'cursor': 'e-resize'});
                    if ($.trim($(obj).data('ex_mousedown')) != 'mousedown') {
                        return;
                    }

                    var mWidth = $(mObj).width();
                    var newMWidth = mWidth - e.clientX + $(mObj).offset().left;
                    var preMWidth = $(mObj).prev().width();
                    var preNewMWidth = preMWidth + e.clientX - $(mObj).offset().left;
                    if (parseInt(newMWidth) > 19 && parseInt(preNewMWidth) > 19) {
                        $(mObj).width(newMWidth).prev().width(preNewMWidth);
                    }
                } else {
                    $(mObj).css({'cursor': 'default'});
                    releaseDownData(obj, i, headLen);
                }
            });
            $(obj).mouseup(function () {
                releaseDownData(obj, i, headLen);
            });
            $(obj).mouseout(function (e) {
                e = e || event;
                var objOffect = $(obj).offset();
                if (objOffect.top > e.clientY || objOffect.top + $(obj).height() < e.clientY) {
                    releaseDownData(obj, i, headLen);
                }
            });

            function bindDownData(obj, i, headLen) {
                if (i != 0) {
                    $(obj).prev().data('ex_mousedown', 'mousedown');
                }
                $(obj).data('ex_mousedown', 'mousedown');
                if (i != headLen - 1) {
                    $(obj).next().data('ex_mousedown', 'mousedown');
                }
            }

            function releaseDownData(obj, i, headLen) {
                if (i != 0) {
                    $(obj).prev().data('ex_mousedown', '');
                }
                $(obj).data('ex_mousedown', '');
                if (i != headLen - 1) {
                    $(obj).next().data('ex_mousedown', '');
                }
            }
        });
    };
    /*************** extend init grid end ***************/


    /*************** extend render per column start ***************/
        // render checkbox to check rows
    $.fn.bsgrid.extendRenderPerColumn.renderCheck = function (record, rowIndex, colIndex, tdObj, trObj, options) {
        if (rowIndex < options.curPageRowsNum) {
            var columnModel = options.columnsModel[colIndex];
            if (columnModel.check == 'true') {
                return '<input class="' + 'bsgrid_editgrid_check' + '" type="checkbox" value="' + $.fn.bsgrid.getRecordIndexValue(record, columnModel.index, options) + '"/>';
            }
        }
        return false;
    };

    // render form methods: text, hidden, password, radio, button, checkbox, textarea
    $.fn.bsgrid.extendRenderPerColumn.renderForm = function (record, rowIndex, colIndex, tdObj, trObj, options) {
        if (rowIndex < options.curPageRowsNum) {
            var columnModel = options.columnsModel[colIndex];
            var edit = columnModel.edit;
            var value = $.fn.bsgrid.getRecordIndexValue(record, columnModel.index, options);
            var tdHtml = '&nbsp;';
            if (edit in options.settings.extend.settings.gridEditConfigs) {
                tdHtml = options.settings.extend.settings.gridEditConfigs[edit].build(edit, value, record, rowIndex, colIndex, tdObj, trObj, options);
            } else {
                return false;
            }
            tdObj.html(tdHtml);
            tdObj.find(':input').addClass('bsgrid_editgrid_hidden');
            for (var key in options.settings.event.customCellEditEvents) {
                tdObj.find(':input').each(function () {
                    var formObj = $(this);
                    formObj.bind(key, {
                        formObj: formObj,
                        record: record,
                        rowIndex: rowIndex,
                        colIndex: colIndex,
                        tdObj: tdObj,
                        trObj: trObj,
                        options: options
                    }, function (event) {
                        options.settings.event.customCellEditEvents[key](event.data.formObj, event.data.record, event.data.rowIndex, event.data.colIndex, event.data.tdObj, event.data.trObj, event.data.options);
                    });
                });
            }
        }
        return false;
    };
    /*************** extend render per column end ***************/


    /*************** extend after render grid start ***************/
        // render line number
    $.fn.bsgrid.extendAfterRenderGrid.renderLineNumber = function (parseSuccess, gridData, options) {
        $.fn.bsgrid.getGridHeaderObject(options).each(function (i) {
            var num = options.columnsModel[i].lineNumber;
            if (num == 'line' || num == 'total_line') {
                $.fn.bsgrid.getRows(options).filter(':lt(' + options.curPageRowsNum + ')').find('td:nth-child(' + (i + 1) + ')').each(function (li) {
                    $(this).html((num == 'line') ? (li + 1) : (li + options.startRow));
                });
            }
        });
    };

    // add check changed event
    $.fn.bsgrid.extendAfterRenderGrid.addCheckChangedEvent = function (parseSuccess, gridData, options) {
        $.fn.bsgrid.getGridHeaderObject(options).each(function (hi) {
            if (options.columnsModel[hi].check == 'true') {
                var checkboxObj = $(this).find('input[type=checkbox]');
                var checkboxObjs = $.fn.bsgrid.getRows(options).find('td:nth-child(' + (hi + 1) + ')>input[type=checkbox]');
                checkboxObjs.change(function () {
                    var allCheckboxObjs = $.fn.bsgrid.getRows(options).find('td:nth-child(' + (hi + 1) + ')>input[type=checkbox]');
                    var checked = $.bsgrid.adaptAttrOrProp(checkboxObj, 'checked') ? true : false;
                    if (!checked && allCheckboxObjs.filter(':checked').length == allCheckboxObjs.length) {
                        $.bsgrid.adaptAttrOrProp(checkboxObj, 'checked', true);
                    } else if (checked && allCheckboxObjs.filter(':checked').length != allCheckboxObjs.length) {
                        $.bsgrid.adaptAttrOrProp(checkboxObj, 'checked', false);
                    }
                });
            }
        });
    };

    // add grid edit event
    $.fn.bsgrid.extendAfterRenderGrid.addGridEditEvent = function (parseSuccess, gridData, options) {
        var gridObj = $.fn.bsgrid.getGridObj(options.gridId);
        $.fn.bsgrid.getRows(options).filter(':lt(' + options.curPageRowsNum + ')').each(function () {
            var columnsModel = options.columnsModel;
            $(this).find('td').each(function (ci) {
                if (columnsModel[ci].edit != '') {
                    // edit form change event
                    $(this).find(':input').change(function () {
                        var rowObj = $(this).parent('td').parent('tr');
                        var isNew = $.trim(rowObj.data('new'));
                        var value = (isNew == 'true' ? '' : gridObj.getRecordIndexValue(gridObj.getRowRecord(rowObj), columnsModel[ci].index));
                        if ($.trim($(this).val()) != value) {
                            $(this).addClass('bsgrid_editgrid_change');
                        } else {
                            $(this).removeClass('bsgrid_editgrid_change');
                        }
                        // store change cell number
                        rowObj.data('change', rowObj.find('.bsgrid_editgrid_change').length);
                    });
                }
            });

            if (options.settings.extend.settings.supportGridEditTriggerEvent == '') {
                $(this).find('.bsgrid_editgrid_hidden').each(function () {
                    showCellEdit(this);
                });
            } else if (options.settings.extend.settings.supportGridEditTriggerEvent == 'rowClick') {
                $(this).click(function () {
                    $(this).find('.bsgrid_editgrid_hidden').each(function () {
                        showCellEdit(this);
                    });
                });
            } else if (options.settings.extend.settings.supportGridEditTriggerEvent == 'rowDoubleClick') {
                $(this).dblclick(function () {
                    $(this).find('.bsgrid_editgrid_hidden').each(function () {
                        showCellEdit(this);
                    });
                });
            } else if (options.settings.extend.settings.supportGridEditTriggerEvent == 'cellClick') {
                $(this).find('.bsgrid_editgrid_hidden').each(function () {
                    var formObj = this;
                    $(formObj).parent('td').click(function () {
                        showCellEdit(formObj);
                    });
                });
            } else if (options.settings.extend.settings.supportGridEditTriggerEvent == 'cellDoubleClick') {
                $(this).find('.bsgrid_editgrid_hidden').each(function () {
                    var formObj = this;
                    $(formObj).parent('td').dblclick(function () {
                        showCellEdit(formObj);
                    });
                });
            }
        });

        function showCellEdit(formObj) {
            var cloneObj = $(formObj).removeClass('bsgrid_editgrid_hidden').clone(true);
            $(formObj).parent('td').html(cloneObj);
        }
    };

    // aggregation
    $.fn.bsgrid.extendAfterRenderGrid.aggregation = function (parseSuccess, gridData, options) {
        var gridObj = $.fn.bsgrid.getGridObj(options.gridId);
        var columnsModel = options.columnsModel;
        $('#' + options.gridId + ' tfoot tr td[' + options.settings.colsProperties.aggAttr + '!=\'\']').each(function (i) {
            if (columnsModel[i].aggName != '') {
                var aggName = columnsModel[i].aggName;
                var val = null;
                if (aggName == 'count') {
                    val = options.curPageRowsNum;
                } else if (aggName == 'countnotnone' || aggName == 'sum' || aggName == 'avg' || aggName == 'max' || aggName == 'min' || aggName == 'concat') {
                    if (aggName == 'countnotnone') {
                        val = 0;
                    }
                    var valHtml = new StringBuilder();
                    $.fn.bsgrid.getRows(options).filter(':lt(' + options.curPageRowsNum + ')').each(function (ri) {
                        var rval = gridObj.getColumnValue(ri, columnsModel[i].aggIndex);
                        if (rval == '') {
                        } else if (aggName == 'countnotnone') {
                            val = (val == null ? 0 : val) + 1;
                        } else if (aggName == 'sum' || aggName == 'avg') {
                            if (!isNaN(rval)) {
                                val = (val == null ? 0 : val) + parseFloat(rval);
                            }
                        } else if (aggName == 'max' || aggName == 'min') {
                            if (!isNaN(rval) && (val == null || (aggName == 'max' && parseFloat(rval) > val) || (aggName == 'min' && parseFloat(rval) < val))) {
                                val = parseFloat(rval);
                            }
                        } else if (aggName == 'concat') {
                            valHtml.append(rval);
                        }
                    });
                    if (aggName == 'avg' && val != null) {
                        val = val / options.curPageRowsNum;
                    } else if (aggName == 'concat') {
                        val = valHtml.toString();
                    }
                } else if (aggName == 'custom') {
                    val = eval(columnsModel[i].aggIndex)(gridObj, options);
                }
                val = val == null ? '' : val;
                $(this).html(val);
            }
        });
    };
    /*************** extend after render grid end ***************/


    /*************** extend edit methods start ***************/
    /**
     * Gget checked rows indexs, from 0.
     *
     * @param options
     * @returns {Array}
     */
    $.fn.bsgrid.defaults.extend.getCheckedRowsIndexs = function (options) {
        var rowIndexs = [];
        $.fn.bsgrid.getRows(options).each(function (i) {
            if ($(this).find('td>input:checked').length == 1) {
                rowIndexs[rowIndexs.length] = i;
            }
        });
        return rowIndexs;
    };

    /**
     * Get checked rows records.
     *
     * @param options
     * @returns {Array}
     */
    $.fn.bsgrid.defaults.extend.getCheckedRowsRecords = function (options) {
        var records = [];
        $.each($.fn.bsgrid.defaults.extend.getCheckedRowsIndexs(options), function (i, rowIndex) {
            records[records.length] = $.fn.bsgrid.getRecord(rowIndex, options);
        });
        return records;
    };

    /**
     * Get checked values by index.
     *
     * @param index
     * @param options
     * @returns {Array}
     */
    $.fn.bsgrid.defaults.extend.getCheckedValues = function (index, options) {
        var values = [];
        $.each($.fn.bsgrid.defaults.extend.getCheckedRowsRecords(options), function (i, record) {
            values[values.length] = $.fn.bsgrid.getRecordIndexValue(record, index, options);
        });
        return values;
    };

    /**
     * Active grid edit mode.
     *
     * @param options
     */
    $.fn.bsgrid.defaults.extend.activeGridEditMode = function (options) {
        if (!options.settings.extend.settings.supportGridEdit) {
            return;
        }
        $.fn.bsgrid.getRows(options).filter(':lt(' + options.curPageRowsNum + ')').find('td .bsgrid_editgrid_hidden').each(function () {
            var cloneObj = $(this).removeClass('bsgrid_editgrid_hidden').clone(true);
            $(this).parent('td').html(cloneObj);
        });
    };

    /**
     * Get changed rows indexs, from 0.
     *
     * @param options
     * @returns {Array}
     */
    $.fn.bsgrid.defaults.extend.getChangedRowsIndexs = function (options) {
        var rowIndexs = [];
        $.fn.bsgrid.getRows(options).each(function (i) {
            var cellChangedNumStr = $.trim($(this).data('change'));
            if (!isNaN(cellChangedNumStr) && parseInt(cellChangedNumStr) > 0) {
                rowIndexs[rowIndexs.length] = i;
            }
        });
        return rowIndexs;
    };

    /**
     * Get changed rows old records.
     *
     * @param options
     * @returns {Array}
     */
    $.fn.bsgrid.defaults.extend.getChangedRowsOldRecords = function (options) {
        var records = [];
        $.each($.fn.bsgrid.defaults.extend.getChangedRowsIndexs(options), function (i, rowIndex) {
            records[records.length] = $.fn.bsgrid.getRecord(rowIndex, options);
        });
        return records;
    };

    /**
     * Get rows changed columns value, return Object's key is 'row_'+rowIndex, value is a object.
     *
     * @param options
     * @returns {Object}
     */
    $.fn.bsgrid.defaults.extend.getRowsChangedColumnsValue = function (options) {
        var values = {};
        $.each($.fn.bsgrid.defaults.extend.getChangedRowsIndexs(options), function (i, rowIndex) {
            values['row_' + rowIndex] = {};
            $.fn.bsgrid.getRows(options).filter(':eq(' + rowIndex + ')').find('td').each(function (ci) {
                if ($(this).find('.bsgrid_editgrid_change').length > 0) {
                    values['row_' + rowIndex][options.columnsModel[ci].index] = options.settings.extend.settings.gridEditConfigs[options.columnsModel[ci].edit].val($(this).find('.bsgrid_editgrid_change'));
                }
            })
        });
        return values;
    };

    /**
     * delete row.
     *
     * @param row
     * @param options
     */
    $.fn.bsgrid.defaults.extend.deleteRow = function (row, options) {
        $.fn.bsgrid.getRow(row, options).remove();
    };

    /**
     * add new edit row.
     *
     * @param options
     */
    $.fn.bsgrid.defaults.extend.addNewEditRow = function (options) {
        var gridObj = $.fn.bsgrid.getGridObj(options.gridId);
        if (gridObj.getRows().length < 1) {
            return;
        }
        $('#' + options.gridId + ' tbody').prepend(gridObj.getRow(0).clone(true));
        gridObj.getRowCells(0).each(function (colIndex) {
            var columnModel = options.columnsModel[colIndex];
            if (columnModel.render != '') {
                var render_method = eval(columnModel.render);
                var render_html = render_method(null, 0, colIndex, options);
                $(this).html(render_html);
            } else {
                if (columnModel.edit != 'textarea') {
                    $(this).children().val('');
                } else {
                    $(this).children().text('');
                }
                $(this).html($(this).children().removeClass('bsgrid_editgrid_change').clone(true)).removeAttr('title');
            }
        });
        gridObj.getRow(0).data('record', null).data('new', 'true');
    };
    /*************** extend edit methods end ***************/


    /*************** extend other methods start ***************/
    $.fn.bsgrid.extendOtherMethods.fixedHeader = function (iFirst, options) {
        if ($.trim($('#' + options.gridId + '_fixedDiv').data('fixedGridLock')) == 'lock') {
            return;
        }
        $('#' + options.gridId + '_fixedDiv').data('fixedGridLock', 'lock');
        var headTrNum = $('#' + options.gridId + ' thead tr').length;
        if (!iFirst) {
            headTrNum = headTrNum / 2;
            $('#' + options.gridId + ' thead tr:lt(' + headTrNum + ')').remove();
        }
        var fixedGridHeight = getSize(options.settings.extend.settings.fixedGridHeight);
        if (fixedGridHeight < $('#' + options.gridId).height()) {
            $('#' + options.gridId + '_fixedDiv').height(fixedGridHeight);
            $('#' + options.gridId).width($('#' + options.gridId + '_fixedDiv').width() - 18);
            $('#' + options.gridId + '_fixedDiv').animate({scrollTop: '0px'}, 0);
        } else {
            $('#' + options.gridId + '_fixedDiv').height($('#' + options.gridId).height());
            $('#' + options.gridId).width($('#' + options.gridId + '_fixedDiv').width() - 1);
        }
        $('#' + options.gridId + ' thead tr:lt(' + headTrNum + ')').clone(true).prependTo('#' + options.gridId + ' thead');
        $('#' + options.gridId + ' thead tr:lt(' + headTrNum + ')').css({
            'z-index': 10,
            position: 'fixed'
        }).width($('#' + options.gridId + ' thead tr:last').width());
        $('#' + options.gridId + ' thead tr:lt(' + headTrNum + ')').each(function (i) {
            var position = $('#' + options.gridId + ' thead tr:eq(' + (headTrNum + i) + ')').position();
            $(this).css({top: position.top - getSize($(this).find('th').css('border-top-width')), left: position.left});
        });

        $('#' + options.gridId + ' thead tr:gt(' + (headTrNum - 1) + ')').each(function (ri) {
            $(this).find('th').each(function (i) {
                var thObj = $(this);
                $('#' + options.gridId + ' thead tr:eq(' + ri + ') th:eq(' + i + ')').height(thObj.height() + ((ri == headTrNum - 1) ? 2 : 1) * getSize(thObj.css('border-top-width'))).width(thObj.width() + getSize(thObj.css('border-left-width')));
            });
        });
        $('#' + options.gridId + '_fixedDiv').data('fixedGridLock', '');

        function getSize(sizeStr) {
            sizeStr = $.trim(sizeStr).toLowerCase().replace('px', '');
            var sizeNum = parseFloat(sizeStr);
            return isNaN(sizeNum) ? 0 : sizeNum;
        }
    };

    // init fixed header
    $.fn.bsgrid.extendOtherMethods.initFixedHeader = function (gridId, options) {
        $('#' + gridId).wrap('<div id="' + gridId + '_fixedDiv"></div>');
        $('#' + gridId + '_fixedDiv').data('fixedGridLock', '');
        $('#' + gridId + '_fixedDiv').css({
            padding: 0,
            'border-width': 0,
            width: '98%',
            'overflow-y': 'auto',
            'margin-bottom': '-1px'
        });
        $('#' + gridId).css({width: 'auto'});
        $('#' + gridId + '_pt_outTab').css({'border-top-width': '1px'});
        $.fn.bsgrid.extendOtherMethods.fixedHeader(true, options);
        $(window).resize(function () {
            $.fn.bsgrid.extendOtherMethods.fixedHeader(false, options);
        });
    };
    /*************** extend other methods end ***************/

})(jQuery);