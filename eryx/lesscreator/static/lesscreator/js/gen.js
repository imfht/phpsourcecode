
function lcInitSetting()
{
    /* var autosave = lessCookie.Get('editor_autosave');
    if (autosave == null) {
        lessCookie.SetByDay("editor_autosave", "on", 365);
        autosave = 'on';
    }
    if (autosave == 'on') {
        $("#editor_autosave").prop("checked", true);
    } */
    
    var theme = lessCookie.Get('editor_theme');
    if (theme == null) {
        lessCookie.SetByDay("editor_theme", "monokai", 365);
        theme = "monokai";
    }
    lcEditor.Config.theme = theme;
    if (theme != "default") {
        seajs.use("/lesscreator/~/codemirror3/3.21.0/theme/"+theme+".css");
    }
    
    var editor_editmode = lessLocalStorage.Get('editor_editmode');
    if (editor_editmode == 'vim' || editor_editmode == 'emacs') {
        lcEditor.Config.EditMode = editor_editmode;
    }
        
    var search_case = lessCookie.Get('editor_search_case');
    if (search_case == null) {
        lessCookie.SetByDay("editor_search_case", "off", 365);
        search_case = 'off';
    }
    if (search_case == 'on') {
        $("#editor_search_case").prop("checked", true);
    }
    
    var tabSize = lessCookie.Get('editor_tabSize');
    if (tabSize != null) {
        lcEditor.Config.tabSize = parseInt(tabSize);
    }

    var fontSize = lessCookie.Get('editor_fontSize');
    if (fontSize != null) {
        lcEditor.Config.fontSize = parseInt(fontSize);
    }
    
    lcEditor.Config.tabs2spaces = (lessCookie.Get('editor_tabs2spaces') == 'false') ? false : true;
    
    lcEditor.Config.smartIndent = (lessCookie.Get('editor_smartIndent') == 'false') ? false : true;
    
    lcEditor.Config.lineWrapping = (lessCookie.Get('editor_lineWrapping') == 'false') ? false : true;

    lcEditor.Config.codeFolding = (lessCookie.Get('editor_codeFolding') == 'true') ? true : false;
}

////////////////////////////////////////////////////////////////////////////////
function lcNavletRefresh(target)
{  
    pg = $('#lc-navlet-frame-'+ target +' .lc_navlet_lm').innerWidth();
    //console.log("lc_navlet_lm"+ pg);

    pgl = $('#lc-navlet-frame-'+ target +' .navitem').last().position().left 
            + $('#lc-navlet-frame-'+ target +' .navitem').last().outerWidth(true);
    //console.log("lc_navlet_lm pgl"+ pgl);

    if (pgl > pg) {
        $('#lc-navlet-frame-'+ target +' .navitem_more').html("»");
    } else {
        $('#lc-navlet-frame-'+ target +' .navitem_more').empty();
    }
}
function lcNavletMore(target)
{
    var ls = $('#lc-navlet-frame-'+ target +' .lc_navlet_navs').html();
    
    if (!$('.lc-navlet-moreol').length) {
        $("body").append('<div class="lc-navlet-moreol"></div>');
    }

    $('.lc-navlet-moreol').html(ls);
    
    e = lessPosGet();
    w = 100;
    h = 100;
    
    $('.lc-navlet-moreol').css({
        width: w+'px',
        height: 'auto',
        top: (e.top + 10)+'px',
        left: (e.left - w - 10)+'px'
    }).toggle();

    rw = $('.lc-navlet-moreol').outerWidth(true);   
    if (rw > 400) {
        $('.lc-navlet-moreol').css({
            width: '400px',
            left: (e.left - 410)+'px'
        });
    } else if (rw > w) {
        $('.lc-navlet-moreol').css({
            width: rw+'px',
            left: (e.left - rw - 10)+'px'
        });
    }
    
    rh = $('.lc-navlet-moreol').height();
    bh = $('body').height();
    hmax = bh - e.top - 30;
    if (rh > hmax) {
        $('.lc-navlet-moreol').css({height: hmax+"px"});
    }

    $(".lc-navlet-moreol").find(".navitem").click(function() {
        $('.lc-navlet-moreol').hide();
    });
}


////////////////////////////////////////////////////////////////////////////////
function h5cPluginDataOpen()
{
    lessModalOpen('/lesscreator/data/open', 1, 700, 450, 
        'Open Database', null);
}
function h5cPluginDataNew()
{
    lessModalOpen('/lesscreator/data/create', 1, 700, 450, 
        'Create Database', null);
}

///////////////////////////////////////////////////////////////////////////////

var projCurrent = null;
var pageArray   = {};
var pageCurrent = 0;

var h5cTabletFrame = {};
/**
    h5cTabletFrame[frame] = {
        'urid': 'string',
        'editor': null,
        'status':  'current/null'
    }
 */
var h5cTabletPool = {};
/**
    h5cTablePool[urid] = {
        'url': 'string',
        'target': 't0/t1',
        'data': 'string',
        'type': 'html/code',
        'mime': '*',
        'hash': '*',
    }
 */

function h5cTabOpen(uri, target, type, opt)
{
    var urid = lessCryptoMd5(uri);

    if (!h5cTabletFrame[target]) {
        h5cTabletFrame[target] = {
            'urid'   : 0,
            'editor' : null,
            'status' : ''
        };
    }

    if (!h5cTabletPool[urid]) {
        h5cTabletPool[urid] = {
            'url'    : uri,
            'target' : target,
            'data'   : '',
            'type'   : type,
        };
        for (i in opt) {
            h5cTabletPool[urid][i] = opt[i];
        }
    }

    h5cTabSwitch(urid);
}

function h5cTabSwitch(urid)
{
    var item = h5cTabletPool[urid];
    if (h5cTabletFrame[item.target].urid == urid) {
        return;
    }

    if (h5cTabletFrame[item.target].editor != null) {
        
        var prevEditorScrollInfo = h5cTabletFrame[item.target].editor.getScrollInfo();
        var prevEditorCursorInfo = h5cTabletFrame[item.target].editor.getCursor();

        lcData.Get("files", h5cTabletFrame[item.target].urid, function(prevEntry) {

            if (!prevEntry) {
                return;
            }

            prevEntry.scrlef = prevEditorScrollInfo.left;
            prevEntry.scrtop = prevEditorScrollInfo.top;
            prevEntry.curlin = prevEditorCursorInfo.line;
            prevEntry.curch  = prevEditorCursorInfo.ch;

            lcData.Put("files", prevEntry, function() {
                // TODO
            });
        });
    }

    if (h5cTabletFrame[item.target].urid != urid) {
        //lcEditor.Save(lcEditor.urid, 1);
        h5cTabletFrame[item.target].urid = 0;
    }

    h5cTabletTitle(urid, true);

    if (item.titleonly) {
        h5cTabletTitleImage(urid);
        h5cTabletPool[urid].titleonly = false;
        return;
    }

    switch (item.type) {
    case 'html':
    case 'webterm':
        if (true || item.data.length < 1) {
            $.ajax({
                url     : item.url,
                type    : "GET",
                timeout : 30000,
                success : function(rsp) {
                    
                    h5cTabletPool[urid].data = rsp;
                    h5cTabletTitleImage(urid);
                    h5cTabletFrame[item.target].urid = urid;

                    $("#h5c-tablet-toolbar-"+ item.target).empty();
                    $("#h5c-tablet-body-"+ item.target).empty().html(rsp);
                    lcLayout.Resize();
                },
                error: function(xhr, textStatus, error) {
                    hdev_header_alert('error', xhr.responseText);
                }
            });
        } else {
            h5cTabletTitleImage(urid);
            h5cTabletFrame[item.target].urid = urid;
            
            $("#h5c-tablet-toolbar-"+ item.target).empty();
            $("#h5c-tablet-body-"+ item.target).empty().html(item.data);
            lcLayout.Resize();
        }
        break;

    case 'editor':

        lcEditor.TabletOpen(urid, function(ret) {
            
            if (!ret) {
                return;
            }

            //console.log("lcEditor.TabletOpen OK");
            h5cTabletTitleImage(urid);
            h5cTabletFrame[item.target].urid = urid;
            lessLocalStorage.Set("tab.fra.urid."+ item.target, urid);
        });

        break;

    default :
        return;
    }
}

function h5cTabletTitleImage(urid, imgsrc)
{
    var item = h5cTabletPool[urid];
    if (!item.img) {
        return;
    }

    var imgsrc = "/lesscreator/static/img/"+item.img+".png";
    if (item.img.slice(0, 1) == '/') {
        imgsrc = item.img;
    }

    $("#pgtab"+ urid +" .ico img").attr("src", imgsrc);
}

function h5cTabletTitle(urid, loading)
{
    var item = h5cTabletPool[urid];
    
    if (!item.target) {
        return;
    }

    if (!$("#pgtab"+urid).length) {
        
        if (!item.title) {
            item.title = item.url.replace(/^.*[\\\/]/, '');
        }

        entry  = '<table id="pgtab'+urid+'" class="pgtab"><tr>';
        
        if (item.img) {
            
            if (loading) {
                var imgsrc = "/lesscreator/static/img/loading4.gif";
            } else {
                var imgsrc = "/lesscreator/static/img/"+item.img+".png";
            }
            //
            if (item.img.slice(0, 1) == '/') {
                imgsrc = item.img;
            }
            entry += "<td class='ico' onclick=\"h5cTabSwitch('"+urid+"')\">\
                <img src='"+ imgsrc +"' align='absmiddle' /></td>";
        }
        entry += '<td class="chg">*</td>';
        entry += "<td class=\"pgtabtitle\" onclick=\"h5cTabSwitch('"+urid+"')\">"+item.title+"</td>";
        
        if (item.close) {
            entry += '<td><span class="close" onclick="lcTabClose(\''+urid+'\', 0)">&times;</span></td>';
        }
        entry += '</tr></table>';
        $("#h5c-tablet-tabs-"+ item.target).append(entry);            
    }

    if (!item.titleonly) {
        $('#h5c-tablet-tabs-'+ item.target +' .pgtab.current').removeClass('current');
        $('#pgtab'+ urid).addClass("current");
    }
   
    pg = $('#h5c-tablet-tabs-frame'+ item.target +' .h5c_tablet_tabs_lm').innerWidth();
    //console.log("h5c-tablet-tabs t*"+ pg);
    
    tabp = $('#pgtab'+ urid).position();
    //console.log("tab pos left:"+ tabp.left);
    
    mov = tabp.left + $('#pgtab'+ urid).outerWidth(true) - pg;
    if (mov < 0) {
        mov = 0;
    }
    
    pgl = $('#h5c-tablet-tabs-'+ item.target +' .pgtab').last().position().left 
            + $('#h5c-tablet-tabs-'+ item.target +' .pgtab').last().outerWidth(true);
    
    if (pgl > pg) {
        //$('#h5c-tablet-frame'+ item.target +' .pgtab_more').show();
        $('#h5c-tablet-frame'+ item.target +' .pgtab_more').html("»");
    } else {
        //$('#h5c-tablet-frame'+ item.target +' .pgtab_more').hide();
        $('#h5c-tablet-frame'+ item.target +' .pgtab_more').empty();
    }

    $('#h5c-tablet-frame'+ item.target +' .h5c_tablet_tabs').animate({left: "-"+mov+"px"}); // COOL!
}

function h5cTabletMore(tg)
{
    var ol = '';
    for (i in h5cTabletPool) {

        if (h5cTabletPool[i].target != tg) {
            continue;
        }
        
        href = "javascript:h5cTabSwitch('"+ i +"')";
        ol += '<div class="lcitem hdev_lcobj_file">';
        ol += '<div class="lcico"><img src="/lesscreator/static/img/'+ h5cTabletPool[i].img +'.png" align="absmiddle" /></div>';
        ol += '<div class="lcctn"><a href="'+ href +'">'+ h5cTabletPool[i].title +'</a></div>';
        ol += '</div>';
    }
    $('.pgtab-openfiles-ol').empty().html(ol);
    
    e = lessPosGet();
    w = 100;
    h = 100;
    //console.log("event top:"+e.top+", left:"+e.left);
    
    $('.pgtab-openfiles-ol').css({
        width: w+'px',
        height: 'auto',
        top: (e.top + 10)+'px',
        left: (e.left - w - 10)+'px'
    }).toggle();

    rw = $('.pgtab-openfiles-ol').outerWidth(true);   
    if (rw > 400) {
        $('.pgtab-openfiles-ol').css({
            width: '400px',
            left: (e.left - 410)+'px'
        });
    } else if (rw > w) {
        $('.pgtab-openfiles-ol').css({
            width: rw+'px',
            left: (e.left - rw - 10)+'px'
        });
    }
    
    rh = $('.pgtab-openfiles-ol').height();
    bh = $('body').height();
    hmax = bh - e.top - 30;
    //console.log("hmax: "+hmax);
    if (rh > hmax) {
        $('.pgtab-openfiles-ol').css({height: hmax+"px"});
    }
    
    $(".pgtab-openfiles-ol").find(".hdev_lcobj_file").click(function() {
        $('.pgtab-openfiles-ol').hide();
    });
}


function lcTabClose(urid, force)
{
    var item = h5cTabletPool[urid];

    switch (item.type) {
    case 'html':
        _lcTabCloseClean(urid);
        break;
    case 'webterm':
        $('#h5c-tablet-framew1').hide();
        _lcTabCloseClean(urid);
        lessLocalStorage.Set("lcWebTerminal0", "0");
        break;
    case 'editor':

        if (force == 1) {
        
            _lcTabCloseClean(urid);

        } else {

            lcEditor.IsSaved(urid, function(ret) {
                
                if (ret) {
                    _lcTabCloseClean(urid);
                    return;
                }

                lessModalOpen("/lesscreator/editor/changes2save?urid="+ urid, 
                    1, 500, 180, 'Save changes before closing', null);
            });
        }
        break;
    default :
        return;
    }
}

function _lcTabCloseClean(urid)
{
    var item = h5cTabletPool[urid];
    if (item == undefined || !item.url) {
        return;
    }

    var j = 0;
    for (var i in h5cTabletPool) {

        if (item.target != h5cTabletPool[i].target) {
            continue;
        }

        if (!h5cTabletPool[i].target) {
            delete h5cTabletPool[i];
            continue;
        }

        if (i == urid) {
            
            lcData.Del("files", urid, function(rs) {
                //console.log("del: "+ rs);
            });

            $('#pgtab'+ urid).remove();
            delete h5cTabletPool[urid];

            if (urid != h5cTabletFrame[item.target].urid) {
                return;
            }

            $("#h5c-tablet-body-"+ item.target).empty();
            $("#h5c-tablet-toolbar-"+ item.target).empty();

            h5cTabletFrame[item.target].urid = 0;
            if (j != 0) {
                break;
            }

        } else {            
            j = i;            
            if (h5cTabletFrame[item.target].urid == 0) {
                break;
            }
        }
    }
    
    if (j != 0) {
        h5cTabSwitch(j);
        h5cTabletFrame[item.target].urid = j;
    }

    lcLayout.Resize();
}

var lcLayoutWebTermPos = null;
var lcLayoutWebTermInterv = null;
function lcLayoutWebTermSizeFix()
{
    if (!document.getElementById("lc-terminal")) {
        clearInterval(lcLayoutWebTermInterv);
        lcLayoutWebTermPos = null;
        return;
    }

    var obj = JSON.parse(rsp);
    
    var pnew = JSON.stringify($("#lc-terminal").position());
    if (lcLayoutWebTermPos == pnew) {
        return;
    }

    lcLayoutWebTermPos = pnew;
    lcLayout.Resize();
}

function lcLayoutResize()
{
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
    var ctn_w = (bw - (3 * spacecol)) - left_w;
    $("#lc-proj-start").width(left_w);


    var lyo_p = $('#hdev_layout').position();
    var lyo_h = bh - lyo_p.top - spacecol;
    if (lyo_h < 400) {
        lyo_h = 400;
    }
    $('#hdev_layout').height(lyo_h);

    // content
    var ctn0_tab_h = $('#h5c-tablet-tabs-framew0').height();
    var ctn0_tool_h = $('#h5c-tablet-toolbar-w0').height();

    if ($('#h5c-tablet-framew1').is(":visible")) {

        $('#h5c-resize-roww0').show();

        toset = lessSession.Get('lcLyoCtn0H');
        if (toset == 0 || toset == null) {
            toset = lessLocalStorage.Get('lcLyoCtn0H');
        }
        if (toset == 0 || toset == null) {
            toset = 0.7;
            lessLocalStorage.Set("lcLyoCtn0H", toset);
            lessSession.Set("lcLyoCtn0H", toset);
        }

        var ctn1_tab_h = $('#h5c-tablet-tabs-framew1').height();

        var ctn0_h = toset * (lyo_h - 10);
        if ((ctn0_h + ctn1_tab_h + 10) > lyo_h) {
            ctn0_h = lyo_h - ctn1_tab_h - 10;   
        }
        var ctn0b_h = ctn0_h - ctn0_tab_h - ctn0_tool_h;
        if (ctn0b_h < 0) {
            ctn0b_h = 0;
            ctn0_h = ctn0_tab_h;
        } 
        $('#h5c-tablet-body-w0').height(ctn0b_h);  
        if ($('.h5c_tablet_body .CodeMirror').length) {
            $('.h5c_tablet_body .CodeMirror').width(ctn_w);
            $('.h5c_tablet_body .CodeMirror').height(ctn0b_h);
        }
        
        var ctn1_h = lyo_h - ctn0_h - 10;
        var ctn1b_h = ctn1_h - ctn1_tab_h;
        if (ctn1b_h < 0) {
            ctn1b_h = 0;
        }
        $('#h5c-tablet-body-w1').width(ctn_w);
        $('#h5c-tablet-body-w1').height(ctn1b_h);
        if (document.getElementById("lc-terminal")) {
            $('#lc-terminal').height(ctn1b_h);
            $('#lc-terminal').width(ctn_w - 16);
            lc_terminal_conn.Resize();
        }

    } else {

        $('#h5c-resize-roww0').hide();

        $('#h5c-tablet-body-w0').height(lyo_h - ctn0_tab_h - ctn0_tool_h);  
        
        if ($('.h5c_tablet_body .CodeMirror').length) {
            $('.h5c_tablet_body .CodeMirror').width(ctn_w);
            $('.h5c_tablet_body .CodeMirror').height(lyo_h - ctn0_tab_h - ctn0_tool_h);
        }
    }

    //
    $('#h5c-tablet-tabs-framew0').width(ctn_w);
    $('#h5c-tablet-framew0 .h5c_tablet_tabs_lm').width(ctn_w - 20);

    // project start box
    $("#lcx-proj-box").width(left_w);
    var sf_p = $("#lcx-start-fstree").position();
    if (sf_p) {
        $("#lcx-start-fstree").width(left_w);
        $("#lcx-start-fstree").height(lyo_h - (sf_p.top - lyo_p.top));
    }

    // TODO rightbar
}


function h5cProjectOpen(proj)
{
    var uname = lessSession.Get("SessUser");

    if (!proj) {
        proj = lessLocalStorage.Get(uname +"LastProjPath");
    }

    if (!proj) {
        proj = lessSession.Get("ProjPath");
    }

    if (!proj) {
        lessModalOpen("/lesscreator/app/well", 1, 800, 450,
            "Start a Project from ...", null);
        return;
    }

    var uri = "basedir="+ lessSession.Get("basedir");
    uri += "&proj="+ proj;

    if (projCurrent) {
        if (projCurrent.split("/").pop(-1) != proj.split("/").pop(-1)) {
            window.open("/lesscreator/index?"+ uri, '_blank');
        }
        return;
    }
    
    $.ajax({
        url     : "/lesscreator/proj/start?"+ uri,
        type    : "GET",
        timeout : 30000,
        success : function(rsp) {
            $("#lcx-proj-box").empty().html(rsp);
            lcLayout.Resize();
        },
        error: function(xhr, textStatus, error) {
            //
        }
    });

    
    projCurrent = proj;    
    lessSession.Set("ProjPath", proj);
    lessLocalStorage.Set(uname +"LastProjPath", proj);    
}

function lcProjOpen()
{
    lessModalOpen('/lesscreator/proj/open-recent?basedir='+ lessSession.Get("basedir"), 1, 800, 450, 'Open Project', null);
}

function lcProjNew()
{
    lessModalOpen("/lesscreator/app/well", 1, 800, 450,
            "Start a Project from ...", null);
}

function lcProjSet()
{
    var opt = {
        'title': 'Project Settings',
        'close':'1',
        'img': '/lesscreator/static/img/app-t3-16.png',
    }

    var url = '/lesscreator/proj/set?proj='+ lessSession.Get("ProjPath");

    h5cTabOpen(url, 'w0', 'html', opt);
}

var lc_launch_def = null;
function lcProjLaunch(title)
{
    if (lc_launch_def == null) {

    }

    if (title == null) {
        title = "Launch the Project";
    }

    //var uri = "/lesscreator/launch/webserver";
    //var uri = "/lesscreator/launch/dataset";
    var uri = "/lesscreator/launch/index";
    uri += "?proj="+ lessSession.Get("ProjPath");
    uri += "&user="+ lessSession.Get("SessUser"); // TODO access_token

    lessModalOpen(uri, 1, 900, 500, title, null);
}

function lcWebTerminal(force)
{
    if (force != 1 && lessLocalStorage.Get("lcWebTerminal0") != 1) {
        return;
    }

    $('#h5c-tablet-framew1').show();
    var opt = {
        //'img': '/lesscreator/static/img/app-t3-16.png',
        'title': 'Terminal',
        'close': '1',
    };

    h5cTabOpen("/lesscreator/term/index?", 'w1', 'webterm', opt);
}

////////////////////////////////////////////////////////////////////////////////
//prefixes of implementation that we want to test
window.indexedDB = window.indexedDB || window.mozIndexedDB || window.webkitIndexedDB || window.msIndexedDB;

//prefixes of window.IDB objects
window.IDBTransaction = window.IDBTransaction || window.webkitIDBTransaction || window.msIDBTransaction;
window.IDBKeyRange = window.IDBKeyRange || window.webkitIDBKeyRange || window.msIDBKeyRange

if (!window.indexedDB) {
    window.alert("Your browser doesn't support a stable version of IndexedDB.")
}

var lcData = {};
lcData.db = null;
lcData.version = 11;
lcData.schema = [
    {
        name: "files",
        pri: "id",
        idx: ["projdir"]
    },
    {
        name: "config",
        pri: "id",
        idx: ["type"]
    }
];
lcData.Init = function(dbname, cb)
{
    var req = indexedDB.open(dbname, lcData.version);  

    req.onsuccess = function (event) {
        lcData.db = event.target.result;
        cb(true);
    };

    req.onerror = function (event) {
        //console.log("IndexedDB error: " + event.target.errorCode);
        cb(true);
    };

    req.onupgradeneeded = function (event) {
        
        lcData.db = event.target.result;

        for (var i in lcData.schema) {
            
            var tbl = lcData.schema[i];
            
            if (lcData.db.objectStoreNames.contains(tbl.name)) {
                lcData.db.deleteObjectStore(tbl.name);
            }

            var objectStore = lcData.db.createObjectStore(tbl.name, {keyPath: tbl.pri});

            for (var j in tbl.idx) {
                objectStore.createIndex(tbl.idx[j], tbl.idx[j], {unique: false});
            }
        }
        cb(true);
    };
}

lcData.Put = function(tbl, entry, cb)
{    
    if (lcData.db == null) {
        return;
    }

    //console.log("put: "+ entry.id);

    var req = lcData.db.transaction([tbl], "readwrite").objectStore(tbl).put(entry);

    req.onsuccess = function(event) {
        if (cb != null && cb != undefined) {
            cb(true);
        }
    };

    req.onerror = function(event) {
        if (cb != null && cb != undefined) {
            cb(false);
        }
    }
}

lcData.Get = function(tbl, key, cb)
{
    if (lcData.db == null) {
        return;
    }

    var req = lcData.db.transaction([tbl]).objectStore(tbl).get(key);

    req.onsuccess = function(event) {
        cb(req.result);
    };

    req.onerror = function(event) {
        cb(req.result);
    }
}

lcData.Query = function(tbl, column, value, cb)
{
    if (lcData.db == null) {
        //console.log("lcData is NULL");
        return;
    }
    var req = lcData.db.transaction([tbl]).objectStore(tbl).index(column).openCursor();

    req.onsuccess = function(event) {
        cb(event.target.result);
    };

    req.onerror = function(event) {
        //
    }
}

lcData.Del = function(tbl, key, cb)
{
    if (lcData.db == null) {
        return;
    }

    var req = lcData.db.transaction([tbl], "readwrite").objectStore(tbl).delete(key);

    req.onsuccess = function(event) {
        cb(true);
    };

    req.onerror = function(event) {
        cb(false);
    }
}

lcData.List = function(tbl, cb)
{
    if (lcData.db == null) {
        return;
    }

    var req = lcData.db.transaction([tbl], "readwrite").objectStore(tbl).openCursor();

    req.onsuccess = function(event) {
        var cursor = event.target.result;
        if (cursor) {
            cb(cursor);
        }
    };

    req.onerror = function(event) {

    }
}

