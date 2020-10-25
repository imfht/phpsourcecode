var lc = {
    base : "/lesscreator/",
    // ns   : ""
}

lc.Boot = function()
{
    seajs.config({
        base: lc.base,
    });

    var rqs = [
        "~/lesscreator/js/jquery.js",
        "~/lessui/js/BrowserDetect.js",
    ];
    seajs.use(rqs, function() {

        var browser = BrowserDetect.browser;
        var version = BrowserDetect.version;
        var OS      = BrowserDetect.OS;

        // if (!((browser == 'Chrome' && version >= 20)
        //     || (browser == 'Firefox' && version >= 3.6)
        //     || (browser == 'Safari' && version >= 5.0 && OS == 'Mac'))) {
        //     $('#body-content').load(lc.base + "error/browser");
        //     return;
        // }
        if (!(browser == 'Chrome' && version >= 22)) { 
            $('body').load(lc.base + "error/browser");
            return;
        }

        rqs = [
            "~/lessui/js/lessui.js?v={{.version}}&_="+ Math.random(),
            "~/lesscreator/js/c.js?v={{.version}}",
            "~/lesscreator/js/gen.js?v={{.version}}",
            "~/lesscreator/js/genx.js?v={{.version}}",
            "~/lesscreator/js/editor.js?v={{.version}}&_="+ Math.random(),
            "~/codemirror/3.21.0/codemirror.min.js",
            
            "~/twitter-bootstrap/2.3.2/css/bootstrap.min.css",

            // DEV
            // "~/lessui/less/lessui.less",
            // "~/lesscreator/less/defx.less",
            // "~/lessui/less/less.min.js",
            // PUB
            // "~/lessui/css/lessui.min.css",
            // "~/lesscreator/css/defx.css?v={{.version}}",

            "~/lesscreator/css/def.css?v={{.version}}",
        ];
        seajs.use(rqs, function() {
            lcLoadDeps();
        });
    });

    document.oncontextmenu = function() {
        // return false;
    }
}

function lcLoadDeps() {
    
    $(".loading").hide();
    $(".lcx-loadwell").show(0, function() {
    
        var bh = $('body').height();
        var bw = $('body').width();

        if (bh < 300) {
            bh = 300;
        }
        if (bw < 600) {
            bw = 600;
        }

        var eh = $('.lcx-loadwell').height();
        var ew = $('.lcx-loadwell').width();

        $('.lcx-loadwell').css({
            "top" : ((bh - eh) / 3) + "px",
            "left": ((bw - ew) / 2) + "px"
        });

        var rqs = [
            // "~/lesscreator/js/eventproxy.js",

            "~/lesscreator/js/box.js?_="+ Math.random(),
            "~/lesscreator/js/project.js?_="+ Math.random(),
            "~/lesscreator/js/tablet.js?_="+ Math.random(),

            // "~/twitter-bootstrap/2.3.2/js/bootstrap.min.js",
            "~/codemirror/3.21.0/codemirror.min.css",

            "~/codemirror/3.21.0/addon/hint/show-hint.min.css",

            "~/codemirror/3.21.0/addon/mode/loadmode.min.js",
            "~/codemirror/3.21.0/addon/search/searchcursor.min.js",
            "~/codemirror/3.21.0/keymap/vim.min.js",
            "~/codemirror/3.21.0/keymap/emacs.min.js",
            "~/codemirror/3.21.0/addon/fold/foldcode.min.js",
            "~/codemirror/3.21.0/addon/fold/foldgutter.min.js",
            "~/codemirror/3.21.0/addon/fold/brace-fold.min.js",
            "~/codemirror/3.21.0/addon/hint/show-hint.min.js",
            "~/codemirror/3.21.0/addon/hint/javascript-hint.min.js",
            "~/codemirror/3.21.0/mode/all.min.js",
            "~/codemirror/3.21.0/addon/dialog/dialog.min.js",
            "~/codemirror/3.21.0/addon/dialog/dialog.min.css",
            "~/codemirror/3.21.0/theme/monokai.min.css",

            "~/lesscreator/js/term.js?v={{.version}}",
        ];

        seajs.use(rqs, function() {

            lcData.Init(lessCookie.Get("access_userkey"), function(ret) {

                if (!ret) {
                    
                    $(".load-progress").removeClass("progress-success").addClass("progress-danger");
                    
                    lessAlert("#_load-alert", "alert-error", "Local database (IndexedDB) initialization failed");

                    return;
                }

                lcBoxList();
            });


            // lcBodyLoader("index/desk");
            
            // $(".load-progress-num").css({"width": "90%"});
            // $(".load-progress-msg").append("OK<br />Connecting lessOS Cloud Engine to get your boxes ... ");
            
            // setTimeout(_load_sys_config, _load_sleep);
            // setTimeout(_load_box_config, _load_sleep);
        });
    });
}

function lcBoxList()
{
    if (lessCookie.Get("access_userkey") == null) {
        return;
    }
    lessSession.Set("access_userkey", lessCookie.Get("access_userkey"));

    if (lessSession.Get("boxid") != null) {
        lcBodyLoader("index/desk");
        return;
    }

    var url = lessfly_api + "/box/list?";
    url += "access_token="+ lessCookie.Get("access_token");
    url += "&project=lesscreator";

    lessModalOpen(lc.base + "index/box-list", 1, 660, 400, "Boxes", null);

    return;

    $.ajax({
        url     : url,
        type    : "GET",
        timeout : 30000,
        success : function(rsp) {

            var rsj = JSON.parse(rsp);

            if (rsj.status == 200) {
                
                $(".load-progress-msg").append("OK");

                if (rsj.data.totalItems == 0) {
                    // TODO
                } else if (rsj.data.totalItems == 1) {
                    // Launch Immediately
                } else if (rsj.data.totalItems > 1) {
                    // Select one to Launch ...
                }

            } else {
                $(".load-progress").removeClass("progress-success").addClass("progress-danger");
                lessAlert("#_load-alert", "alert-error", rsj.message);
            }
        },
        error   : function(xhr, textStatus, error) {
            $(".load-progress").removeClass("progress-success").addClass("progress-danger");
            lessAlert("#_load-alert", "alert-error", "Failed on Initializing System Environment");
        }
    });
}

function lcAjax(obj, url, cb)
{
    if (/\?/.test(url)) {
        url += "&_=";
    } else {
        url += "?_=";
    }
    url += Math.random();
    //console.log("req: lesscreator/"+ url);
    $.ajax({
        url     : lc.base + ""+ url,
        type    : "GET",
        timeout : 30000,
        success : function(rsp) {
            //console.log(rsp);
            $(obj).html(rsp);
            if (cb != undefined) {
                cb();
            }
        },
        error: function(xhr, textStatus, error) {

            if (xhr.status == 401) {
                lcBodyLoader('user/login');
            } else {
                alert("Internal Server Error"); //+ xhr.responseText);
            }
        }
    });
}

function lcBodyLoader(uri)
{
    lcAjax("#body-content", uri);

    if (uri == "index/desk") {
        $(window).resize(function() {
            lcLayout.Resize();
        });
    }
}

function lcComLoader(uri)
{
    lcAjax("#com-content", uri);
}

function lcWorkLoader(uri)
{
    lcAjax("#work-content", uri);
}

function lcHeaderAlert(status, alert)
{
    $("#lcx_halert").removeClass().addClass(status).html(alert).fadeOut(200).fadeIn(200);
}

var lcLayout = {
    init   : false,
    colsep : 0,
    width  : 0,
    height : 0,
    postop : 0,
    cols   : [
        {
            id       : "lcbind-proj-filenav",
            width    : 15,
            minWidth : 200
        },
        {
            id    : "lclay-colmain",
            width : 85
        }
    ]
}

lcLayout.Initialize = function()
{
    if (lcLayout.init) {
        return;
    }

    for (var i in lcLayout.cols) {
        
        var wl = lessLocalStorage.Get(lessSession.Get("proj_id") +"_laysize_"+ lcLayout.cols[i].id);

        if (wl !== undefined && parseInt(wl) > 0) {
            lcLayout.cols[i].width = parseInt(wl);
        } else {

            var ws = lessSession.Get("laysize_"+ lcLayout.cols[i].id);
            if (ws !== undefined && parseInt(ws) > 0) {
                lcLayout.cols[i].width = parseInt(ws);
            }
        }
    }
}

lcLayout.BindRefresh = function()
{
    $(".lclay-col-resize").bind("mousedown", function(e) {
        
        var layid = $(this).attr("lc-layid");

        // console.log("lclay-col-resize mousedown: "+ layid);

        var leftLayId = "", rightLayId = "";
        var leftIndexId = 0, rightIndexId = 1;
        var leftWidth = 0, rightWidth = 0;
        var leftMinWidth = 0, rightMinWidth = 0;
        for (var i in lcLayout.cols) {
            
            rightLayId = lcLayout.cols[i].id;
            rightWidth = lcLayout.cols[i].width;
            rightMinWidth = 100 * 200 / lcLayout.width;
            rightIndexId = i;
            if (lcLayout.cols[i].minWidth !== undefined) {
                rightMinWidth = 100 * lcLayout.cols[i].minWidth / lcLayout.width;
            }

            if (rightLayId == layid) {
                break;
            }

            leftLayId = rightLayId;
            leftWidth = rightWidth;
            leftMinWidth = rightMinWidth;
            leftIndexId = rightIndexId;
        }

        var leftStart = $("#"+ leftLayId).position().left;

        // $("#lcbind-col-rsline").remove();
        // $("body").append("<div id='lcbind-col-rsline'></div>");
        // $("#lcbind-col-rsline").css({
        //     height : lcLayout.height,
        //     left   : e.pageX,
        //     bottom : 10
        // }).show();

        var posLast = e.pageX;

        $("#lcbind-layout").bind("mousemove", function(e) {
            
            // console.log("lcbind-layout mousemove: "+ e.pageX);
            
            // $("#lcbind-col-rsline").css({left: e.pageX});

            if (Math.abs(posLast - e.pageX) < 4) {
                return;
            }
            posLast = e.pageX;

            var leftWidthNew = 100 * (e.pageX - 5 - leftStart) / lcLayout.width;
            // var fixWidthRate = leftWidthNew - leftWidth;
            var rightWidthNew = rightWidth - leftWidthNew + leftWidth;
            
            if (leftWidthNew <= leftMinWidth || rightWidthNew <= rightMinWidth) {
                return;
            }

            lcLayout.cols[leftIndexId].width = leftWidthNew;
            lcLayout.cols[rightIndexId].width = rightWidthNew;

            lessLocalStorage.Set(lessSession.Get("proj_id") +"_laysize_"+ leftLayId, leftWidthNew);
            lessSession.Set("laysize_"+ leftLayId, leftWidthNew);
            lessLocalStorage.Set(lessSession.Get("proj_id") +"_laysize_"+ rightLayId, rightWidthNew);
            lessSession.Set("laysize_"+ rightLayId, rightWidthNew);

            setTimeout(function() {
                lcLayout.Resize();
            }, 0);
        });
    });

    $(document).bind('mouseup', function() {

        $("#lcbind-layout").unbind("mousemove");
        // $("#lcbind-col-rsline").remove();
        
        lcLayout.Resize();

        setTimeout(function() {
            lcLayout.Resize();
        }, 10);
    });
}

lcLayout.ColumnSet = function(options)
{
    options = options || {};

    if (typeof options.success !== "function") {
        options.success = function(){};
    }
        
    if (typeof options.error !== "function") {
        options.error = function(){};
    }

    if (options.id === undefined) {
        options.error(400, "ID can not be null");
        return;
    }

    var exist = false;
    for (var i in lcLayout.cols) {
        if (lcLayout.cols[i].id == options.id) {
            exist = true;

            if (options.hook !== undefined && options.hook != lcLayout.cols[i].hook) {
                lcLayout.cols[i].hook = options.hook;
            }
        }
    }

    if (!exist) {
        
        colSet = {
            id     : options.id, // Math.random().toString(36).slice(2),
            width  : 15
        }

        if (options.width !== undefined) {
            colSet.width = options.width;
        }

        if (options.minWidth !== undefined) {
            colSet.minWidth = options.minWidth;
        }

        lcLayout.cols.push(colSet);

        lcLayout.BindRefresh();
    }
}

lcLayout.Resize = function()
{
    lcLayout.Initialize();

    var colSep = 10;
    
    //
    var bodyHeight = $("body").height();
    var bodyWidth = $("body").width();
    if (bodyWidth != lcLayout.width) {
        lcLayout.width = bodyWidth;
        $("#lcbind-layout").width(lcLayout.width);
    }

    //
    var lyo_p = $("#lcbind-layout").position();
    var lyo_h = bodyHeight - lyo_p.top - colSep;
    lcLayout.postop = lyo_p.top;
    if (lyo_h < 400) {
        lyo_h = 400;
    }
    if (lyo_h != lcLayout.height) {
        lcLayout.height = lyo_h;
        $("#lcbind-layout").height(lcLayout.height);
    }

    //
    var colSep1 = 100 * (colSep / lcLayout.width);
    if (colSep1 != lcLayout.colsep) {
        lcLayout.colsep = colSep1;
        $(".lclay-colsep").width(lcLayout.colsep +"%");
    }
    // console.log("colSep1: "+ colSep1);

    //
    // console.log("lcLayout.cols.length: "+ lcLayout.cols.length)
    var colSepAll = (lcLayout.cols.length + 1) * colSep1;

    var rangeUsed = 0.0;
    for (var i in lcLayout.cols) {

        if (lcLayout.cols[i].minWidth !== undefined) {
            if ((lcLayout.cols[i].width * lcLayout.width / 100) < lcLayout.cols[i].minWidth) {
                lcLayout.cols[i].width = 100 * ((lcLayout.cols[i].minWidth + 50) / lcLayout.width);
            }
        }

        if (lcLayout.cols[i].width < 10) {
            lcLayout.cols[i].width = 15;
        } else if (lcLayout.cols[i].width > 90) {
            lcLayout.cols[i].width = 80;
        }        

        rangeUsed += lcLayout.cols[i].width;
    }
    // console.log("rangeUsed: "+ rangeUsed);
    // for (var i in lcLayout.cols) {
    //     console.log("2 id: "+ lcLayout.cols[i].id +", width: "+ lcLayout.cols[i].width); 
    // }

    var fixRate = (100 - colSepAll) / 100;
    var fixRateSpace = rangeUsed / 100;
    
    for (var i in lcLayout.cols) {
        lcLayout.cols[i].width = (lcLayout.cols[i].width / fixRateSpace) * fixRate;
        
        $("#"+ lcLayout.cols[i].id).width(lcLayout.cols[i].width + "%");

        if (typeof lcLayout.cols[i].hook === "function") {
            lcLayout.cols[i].hook(lcLayout.cols[i]);
        }
    }

    // for (var i in lcLayout.cols) {
    //     console.log("3 id: "+ lcLayout.cols[i].id +", width: "+ lcLayout.cols[i].width); 
    // }
}

function lcxLayoutResize()
{
    alert("lcxLayoutResize");
    return;
    // console.log(lcLayout.cols);

    var spacecol = 10;

    var bh = $('body').height();
    var bw = $('body').width();

    $("#hdev_layout").width(bw);
    
    var toset = lessSession.Get('lcLyoLeftW');
    if (toset == 0 || toset == null) {   
        toset = lessLocalStorage.Get('lcLyoLeftW');
    }
    if (toset == 0 || toset == null) {
        toset = 0.1;
        lessLocalStorage.Set("lcLyoLeftW", toset);
        lessSession.Set("lcLyoLeftW", toset);
    }

    var left_w = (bw - (3 * spacecol)) * toset;
    if (left_w < 200) {
        left_w = 200;
    } else if (left_w > 600) {
        left_w = 600;
    } else if ((left_w + 200) > bw) {
        left_w = bw - 200;
    }
    // var ctn_w = (bw - (3 * spacecol)) - left_w;
    // $("#lc-proj-start").width(left_w);


    var lyo_p = $('#hdev_layout').position();
    var lyo_h = bh - lyo_p.top - spacecol;
    if (lyo_h < 400) {
        lyo_h = 400;
    }
    $('#hdev_layout').height(lyo_h);

    // // content
    // var ctn0_tab_h = $('#h5c-tablet-tabs-framew0').height();
    // var ctn0_tool_h = $('#h5c-tablet-toolbar-w0').height();

    // if ($('#h5c-tablet-framew1').is(":visible")) {

    //     $('#h5c-resize-roww0').show();

    //     toset = lessSession.Get('lcLyoCtn0H');
    //     if (toset == 0 || toset == null) {
    //         toset = lessLocalStorage.Get('lcLyoCtn0H');
    //     }
    //     if (toset == 0 || toset == null) {
    //         toset = 0.7;
    //         lessLocalStorage.Set("lcLyoCtn0H", toset);
    //         lessSession.Set("lcLyoCtn0H", toset);
    //     }

    //     var ctn1_tab_h = $('#h5c-tablet-tabs-framew1').height();

    //     var ctn0_h = toset * (lyo_h - 10);
    //     if ((ctn0_h + ctn1_tab_h + 10) > lyo_h) {
    //         ctn0_h = lyo_h - ctn1_tab_h - 10;   
    //     }
    //     var ctn0b_h = ctn0_h - ctn0_tab_h - ctn0_tool_h;
    //     if (ctn0b_h < 0) {
    //         ctn0b_h = 0;
    //         ctn0_h = ctn0_tab_h;
    //     } 
    //     $('#h5c-tablet-body-w0').height(ctn0b_h);  
    //     if ($('.h5c_tablet_body .CodeMirror').length) {
    //         $('.h5c_tablet_body .CodeMirror').width(ctn_w);
    //         $('.h5c_tablet_body .CodeMirror').height(ctn0b_h);
    //     }
        
    //     var ctn1_h = lyo_h - ctn0_h - 10;
    //     var ctn1b_h = ctn1_h - ctn1_tab_h;
    //     if (ctn1b_h < 0) {
    //         ctn1b_h = 0;
    //     }
    //     $('#h5c-tablet-body-w1').width(ctn_w);
    //     $('#h5c-tablet-body-w1').height(ctn1b_h);
    //     if (document.getElementById("lc-terminal")) {
    //         $('#lc-terminal').height(ctn1b_h);
    //         $('#lc-terminal').width(ctn_w - 16);
    //         lc_terminal_conn.Resize();
    //     }

    // } else {

    //     $('#h5c-resize-roww0').hide();

    //     $('#h5c-tablet-body-w0').height(lyo_h - ctn0_tab_h - ctn0_tool_h);  
        
    //     if ($('.h5c_tablet_body .CodeMirror').length) {
    //         $('.h5c_tablet_body .CodeMirror').width(ctn_w);
    //         $('.h5c_tablet_body .CodeMirror').height(lyo_h - ctn0_tab_h - ctn0_tool_h);
    //     }
    // }

    // //
    // $('#h5c-tablet-tabs-framew0').width(ctn_w);
    // $('#h5c-tablet-framew0 .h5c_tablet_tabs_lm').width(ctn_w - 20);

    // // project start box
    $("#lcbind-proj-filenav").width(left_w);
    var sf_p = $("#lcbind-fsnav-fstree").position();
    if (sf_p) {
        $("#lcbind-fsnav-fstree").width(left_w);
        $("#lcbind-fsnav-fstree").height(lyo_h - (sf_p.top - lyo_p.top));
    }

    // TODO rightbar
}
