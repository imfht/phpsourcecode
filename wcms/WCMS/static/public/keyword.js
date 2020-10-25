$.fn.extend({
    /*
     * http://www.cnblogs.com/SeaSun/archive/2011/06/21/2085732.html
    **標籤控件
    **功能：按Enter或Tab確定標籤輸入完畢，雙擊文字可以編輯該標籤，單擊叉叉（×）表示刪除該標籤
    **tabControl:function
    **參數說明：
    *initTabCount:int 一開始初始化標籤輸入框的數量；
    *maxTabCount:int 容器可接受最大的標籤數量；
    *tabMaxLen:int 每個標籤允許接受最大的字符長度；
    *tabW:int 標籤輸入框的寬度；
    *tabH:int 標籤輸入框的高度；
    *tipTOffset:int 提示信息與標籤輸入框的top偏移量；
    *tipLOffset:int 提示信息與標籤輸入框的left偏移量；
    */
    tabControl: function (options) {
        var defOpt = { initTabCount: 1, maxTabCount: 100, tabMaxLen: 10, tabW: 150, tabH: 15, tipTOffset: 5, tipLOffset: 0 };
        var opts = $.extend(defOpt, options);
        var checkReg = /[^A-Za-z0-9_\u4E00-\u9FA5]+/gi; //匹配非法字符
        //初始化標籤輸入框
        var initTab = function (obj) {
            var textHtml = "<input class='tabinput' name='tabinput' style='width:" + opts.tabW + "px;height:" + opts.tabH + "px;' type='text'/>";
            obj.append(textHtml);
            $("input[type='text'][name='tabinput']:last", obj).bind("keydown", function (event) {
                if (event.keyCode == 13 || event.keyCode == 32) {//enter|tab
                    event.preventDefault(); //主要是為了tab鍵，不要讓當前元素失去焦點（即別讓他切換元素）
                    var inputObj = $(this);
                    //var value = $.trim($(this).val());
                    var value = $(this).val().replace(/\s+/gi, "");
                    if (value != "") {
                        if (value.length > opts.tabMaxLen) {
                            showMes($(this), "請輸入1到" + opts.tabMaxLen + "個字符長度的標籤");
                            return;
                        }
                        var _match = value.match(checkReg);
                        if (!_match) {
                            compTab(obj, inputObj, value);
                            if ($("input[type='text'][name='tabinput']", obj).length < opts.maxTabCount) {
                                if (!inputObj.data("isModify"))
                                    initTab(obj);
                                else if (!$("input[type='text'][name='tabinput']", obj).is(":hidden")) {
                                    initTab(obj);
                                }
                            }
                            $("input[type='text']:last", obj).focus();
                            hideErr();
                        }
                        else {
                            showMes(inputObj, "內容不能包含非法字符「{0}」！".replace("{0}", _match.join(" ")));
                        }
                    }
                    else
                        showMes(inputObj, "內容不為空");
                }
            }).bind("focus blur", function () {
                hideErr();
            });
        }
        //完成標籤編寫
        var compTab = function (obj, inputObj, value) {
            inputObj.next("span").remove(); //刪除緊跟input元素後的span
            var _span = "<span name='tab' id='radius'><b>" + value + "</b><a id='deltab'>×</a></span>";
            inputObj.after(_span).hide();
            inputObj.next("span").find("a").click(function () {//刪除tab
                if (confirm("確定刪除該標籤？")) {
                    inputObj.next("span").remove();
                    inputObj.remove();
                    if ($("span[name='tab']", obj).length == opts.maxTabCount - 1)
                        initTab(obj);
                }
            });
            inputObj.next("span").dblclick(function () {//修改tab
                inputObj.data("isModify", true).next("span").remove();
                inputObj.show().focus();
            });
        }

        return this.each(function () {
            var jqObj = $(this);
            for (var i = 0; i < opts.initTabCount; i++) {
                initTab(jqObj);
            }
            //$("input[type='text'][name='tabinput']:first", jqObj).focus();
        });
        //生成tip
        function showMes(inputObj, mes) {
            var _offset = inputObj.offset();
            var _mesHtml = "<div id='errormes' class='radius_shadow' style='position:absolute;left:" + (_offset.left + opts.tipLOffset) + "px;top:" + (_offset.top + opts.tabH + opts.tipTOffset) + "px;'>" + mes + "</div>";
            $("#errormes").remove();
            $("body").append(_mesHtml);
        } //隱藏tip
        function hideErr() {
            $("#errormes").hide();
        } //顯示tip
        function showErr() {
            $("#errormes").show();
        }
    },
    //獲取當前容器所生成的tab值，結果是一維數組
    getTabVals: function () {
        var obj = $(this);
        var values = [];
        obj.children("span[name=\"tab\"][id^=\"radius\"]").find("b").text(function (index, text) {
            var checkReg = /[^A-Za-z0-9_\u4E00-\u9FA5]+/gi; //匹配非法字符
            values.push(text.replace(checkReg,""));
        });
        return values;
    }
});
