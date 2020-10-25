
var lcTab = {
    def         : "lctab-default",
    pageArray   : {},
    pageCurrent : 0,

    // frame[frame] = {
    //     "colid"  : "lclay-colmain",
    //     "urid"   : "string",
    //     "editor" : null,
    //     "state"  : "current/null"
    // }
    frame       : {},

    // pool[urid] = {
    //     "url"	: "string",
    //     "colid"  : "lclay-colmain",
    //     "target" : "t0/t1",
    //     "data"	: "string",
    //     "type"	: "html/code",
    //     "mime"	: "*",
    //     "hash"	: "*",
    // }
    pool        : {}
}

lcTab.Open = function(options)
{
    options = options || {};

    if (typeof options.success !== "function") {
        options.success = function(){};
    }

    if (typeof options.error !== "function") {
        options.error = function(){};
    }

    if (options.colid === undefined) {
        options.colid = "lclay-colmain";
    }

    if (options.target === undefined) {
        options.target = lcTab.def;
    }

    var urid = lessCryptoMd5(options.uri);

    if (!lcTab.frame[options.target]) {
        lcTab.frame[options.target] = {
            urid   : 0,
            colid  : options.colid,
            editor : null,
            state  : ""
        };
    }

    if (!lcTab.pool[urid]) {

        lcTab.pool[urid] = {
            url    : options.uri,
            colid  : options.colid,
            target : options.target,
            type   : options.type,
            icon   : options.icon,
            success : options.success,
            error   : options.error,
        }

        if (options.close) {
        	lcTab.pool[urid].close = true;
        }
    }

    if (document.getElementById("lctab-box"+ options.target) == null) {
        
        var tpl = lessTemplate.RenderById("lctab-tpl", {tabid: lcTab.def});
        
        if (tpl == "") {
            return;
        }

        // console.log(tpl);
        $("#"+ options.colid).append(tpl);
        lcLayout.ColumnSet({
            id   : "lclay-colmain",
            hook : lcTab.LayoutResize
        });

        // TODO
        $(".lc_pgtab_more").click(function(event) {

            event.stopPropagation();

            lcTab.TabletMore($(this).attr('href').substr(1));

            $(document).click(function() {
                $("#lctab-openfiles-ol").empty().hide();
                $(document).unbind('click');
            });
        });
    }

    lcTab.Switch(urid);
}

lcTab.Switch = function(urid)
{
    var item = lcTab.pool[urid];
    if (item === undefined) {
        return;
    }

    if (lcTab.frame[item.target].urid == urid) {
        return;
    }

    // TODO
    // if (lcTab.frame[item.target].editor != null) {

    //     var prevEditorScrollInfo = lcTab.frame[item.target].editor.getScrollInfo();
    //     var prevEditorCursorInfo = lcTab.frame[item.target].editor.getCursor();

    //     lcData.Get("files", lcTab.frame[item.target].urid, function(prevEntry) {

    //         if (!prevEntry) {
    //             return;
    //         }

    //         prevEntry.scrlef = prevEditorScrollInfo.left;
    //         prevEntry.scrtop = prevEditorScrollInfo.top;
    //         prevEntry.curlin = prevEditorCursorInfo.line;
    //         prevEntry.curch  = prevEditorCursorInfo.ch;

    //         lcData.Put("files", prevEntry, function() {
    //             // TODO
    //         });
    //     });
    // }

    if (lcTab.frame[item.target].urid != urid) {
        //lcEditor.Save(lcEditor.urid, 1);
        lcTab.frame[item.target].urid = 0;
    }

    lcTab.TabletTitle(urid, true);

    if (item.titleonly) {
        lcTab.TabletTitleImage(urid);
        lcTab.pool[urid].titleonly = false;
        return;
    }

    switch (item.type) {
    case "html":
    case "webterm":
        if (true || item.data.length < 1) {
            // console.log(item);
            $.ajax({
                url     : item.url,
                type    : "GET",
                timeout : 30000,
                success : function(rsp) {

                    lcTab.pool[urid].data = rsp;
                    lcTab.TabletTitleImage(urid);
                    lcTab.frame[item.target].urid = urid;

                    $("#lctab-bar"+ item.target).hide();
                    $("#lctab-body"+ item.target).empty().html(rsp);
                    lcLayout.Resize();
                },
                error: function(xhr, textStatus, error) {
                    lcHeaderAlert("error", xhr.responseText);
                }
            });
        } else {
            lcTab.TabletTitleImage(urid);
            lcTab.frame[item.target].urid = urid;
            
            $("#lctab-bar"+ item.target).empty();
            $("#lctab-body"+ item.target).empty().html(item.data);
            lcLayout.Resize();
        }
        break;

    case "editor":

        lcEditor.TabletOpen(urid, function(ret) {
            
            if (!ret) {
                return;
            }

            //console.log("lcEditor.TabletOpen OK");
            lcTab.TabletTitleImage(urid);
            lcTab.frame[item.target].urid = urid;
            // lessLocalStorage.Set("tab.fra.urid."+ item.target, urid);
            lessLocalStorage.Set(lessSession.Get("boxid") +"."+ lessSession.Get("proj_id") +".tab."+ item.target, urid);
        
            item.success();
        });

        break;

    default :
        return;
    }
}

lcTab.TabletTitleImage = function(urid, imgsrc)
{
    var item = lcTab.pool[urid];

    if (imgsrc === undefined && item.icon !== undefined) {
        
        if (item.icon.slice(0, 1) == "/") {
            imgsrc = item.icon;
        } else {
            imgsrc = lc.base + "~/lesscreator/img/"+ item.icon +".png";
        }
    }

    if (imgsrc !== undefined) {
        $("#pgtab"+ urid +" .ico img").attr("src", imgsrc);
    }
}

lcTab.TabletTitle = function(urid, loading)
{
    var item = lcTab.pool[urid];
    
    if (!item.target) {
        return;
    }

    if ($("#pgtab"+ urid).length < 1) {

        if (!item.title) {
            item.title = item.url.replace(/^.*[\\\/]/, '');
        }

        entry  = '<table id="pgtab'+ urid +'" class="pgtab"><tr>';
        
        if (item.icon) {

            if (loading) {
                var imgsrc = lc.base + "~/lesscreator/img/loading4.gif";
            } else {
                var imgsrc = lc.base + "~/lesscreator/img/"+ item.icon +".png";
            }

            //
            if (item.icon.slice(0, 1) == '/') {
                imgsrc = item.icon;
            }

            entry += "<td class='ico' onclick=\"lcTab.Switch('"+ urid +"')\">\
                <img src='"+ imgsrc +"' align='absmiddle' /></td>";
        }

        entry += '<td class="chg">*</td>';
        entry += "<td class=\"pgtabtitle\" onclick=\"lcTab.Switch('"+ urid +"')\">"+item.title+"</td>";
        
        if (item.close) {
            // entry += '<td><div class="pgtabclose" onclick="lcTab.Close(\''+ urid +'\', 0)"><div class="pgtabcloseitem">&times;</div></div></td>';
            entry += '<td><span class="pgtabclose" onclick="lcTab.Close(\''+ urid +'\', 0)"></span></td>';

        }

        entry += '</tr></table>';
        
        $("#lctab-navtabs"+ item.target).append(entry);            
    }

    if (!item.titleonly) {
        $('#lctab-navtabs'+ item.target +' .pgtab.current').removeClass('current');
        $('#pgtab'+ urid).addClass("current");
    }

    var pg = $('#lctab-nav'+ item.target +' .lctab-navm').innerWidth();
    //console.log("h5c-tablet-tabs t*"+ pg);
    
    var tabp = $('#pgtab'+ urid).position();
    //console.log("tab pos left:"+ tabp.left);
    
    var mov = tabp.left + $('#pgtab'+ urid).outerWidth(true) - pg;
    if (mov < 0) {
        mov = 0;
    }
    
    var pgl = $('#lctab-navtabs'+ item.target +' .pgtab').last().position().left 
            + $('#lctab-navtabs'+ item.target +' .pgtab').last().outerWidth(true);
    
    if (pgl > pg) {
        //$('#lctab-nav'+ item.target +' .pgtab_more').show();
        $('#lctab-nav'+ item.target +' .pgtab_more').html("»");
    } else {
        //$('#lctab-nav'+ item.target +' .pgtab_more').hide();
        $('#lctab-nav'+ item.target +' .pgtab_more').empty();
    }

    $('#lctab-nav'+ item.target +' .lctab-navs').animate({left: "-"+mov+"px"}); // COOL!
}

lcTab.TabletMore = function(tg)
{
    // console.log("TabletMore: "+ tg);

    var ol = '';
    for (i in lcTab.pool) {

        if (lcTab.pool[i].target != tg) {
            continue;
        }

        var href = "javascript:lcTab.Switch('"+ i +"')";
        ol += '<div class="ltm-item lctab-nav-moreitem">';
        ol += '<div class="ltm-ico"><img src="'+ lc.base + '~/lesscreator/img/'+ lcTab.pool[i].icon +'.png" align="absmiddle" /></div>';
        ol += '<div class="ltm-ctn"><a href="'+ href +'">'+ lcTab.pool[i].title +'</a></div>';
        ol += '</div>';
    }
    $("#lctab-openfiles-ol").empty().html(ol);
    
    e = lessPosGet();
    w = 100;
    h = 100;
    //console.log("event top:"+e.top+", left:"+e.left);
    
    $("#lctab-openfiles-ol").css({
        width: w +'px',
        height: 'auto',
        top: (e.top + 10)+'px',
        left: (e.left - w - 10)+'px'
    }).toggle();

    rw = $("#lctab-openfiles-ol").outerWidth(true);   
    if (rw > 400) {
        $("#lctab-openfiles-ol").css({
            width: '400px',
            left: (e.left - 410) +'px'
        });
    } else if (rw > w) {
        $("#lctab-openfiles-ol").css({
            width: rw+'px',
            left: (e.left - rw - 10)+'px'
        });
    }
    
    rh = $("#lctab-openfiles-ol").height();
    bh = $('body').height();
    hmax = bh - e.top - 30;
    //console.log("hmax: "+hmax);
    if (rh > hmax) {
        $("#lctab-openfiles-ol").css({height: hmax+"px"});
    }
    
    $(".lctab-openfiles-ol").find(".lctab-nav-moreitem").click(function() {
        $("#lctab-openfiles-ol").hide();
    });
}

lcTab.Close = function(urid, force)
{
    var item = lcTab.pool[urid];

    switch (item.type) {
    case 'html':
        lcTab.CloseClean(urid);
        break;
    case 'webterm':
        $('#lctab-nav-w1').hide();
        lcTab.CloseClean(urid);
        lessLocalStorage.Set("lcWebTerminal0", "0");
        break;
    case 'editor':

        if (force == 1) {
        
            lcTab.CloseClean(urid);

        } else {

            lcEditor.IsSaved(urid, function(ret) {
                
                if (ret) {
                    lcTab.CloseClean(urid);
                    return;
                }

                lessModalOpen(lc.base + "editor/changes2save?urid="+ urid, 
                    1, 500, 180, 'Save changes before closing', null);
            });
        }
        break;
    default :
        return;
    }
}

lcTab.CloseClean = function(urid)
{
    var item = lcTab.pool[urid];
    if (item == undefined || !item.url) {
        return;
    }

    var j = 0;
    for (var i in lcTab.pool) {

        if (item.target != lcTab.pool[i].target) {
            continue;
        }

        if (!lcTab.pool[i].target) {
            delete lcTab.pool[i];
            continue;
        }

        if (i == urid) {
            
            lcData.Del("files", urid, function(rs) {
                //console.log("del: "+ rs);
            });

            $('#pgtab'+ urid).remove();
            delete lcTab.pool[urid];

            if (urid != lcTab.frame[item.target].urid) {
                return;
            }

            $("#lctab-body"+ item.target).empty();
            $("#lctab-bar"+ item.target).empty();

            lcTab.frame[item.target].urid = 0;
            if (j != 0) {
                break;
            }

        } else {            
            j = i;            
            if (lcTab.frame[item.target].urid == 0) {
                break;
            }
        }
    }
    
    if (j != 0) {
        lcTab.Switch(j);
        lcTab.frame[item.target].urid = j;
    }

    lcLayout.Resize();
}

lcTab.LayoutResize = function(options)
{
    for (var i in lcTab.frame) {

        if (lcTab.frame[i].colid != options.id) {
            continue;
        }

        if ($("#lctab-box"+ i).length < 1) {
            continue;
        }

        var _w = options.width * lcLayout.width / 100;

        var _tabs_h = $("#lctab-nav"+ i).height();
        var _tbar_h = 0;
        if ($("#lctab-bar"+ i).is(":visible")) {
            _tbar_h = $("#lctab-bar"+ i).height();
            console.log("lctab-bar height: "+ _tbar_h);
        }
        var _body_h = lcLayout.height - _tabs_h - _tbar_h;

        $("#lctab-body"+ i).height(_body_h);
        $("#lctab-nav"+ i +" .lctab-navm").width(_w - 20);

        if ($("#lctab-body"+ i +" .CodeMirror").length > 0) {
            $("#lctab-body"+ i +" .CodeMirror").width(_w);
            $("#lctab-body"+ i +" .CodeMirror").height(_body_h);
        }
    }

    // return;
    // var ctn0_tab_h = $('# h5c-tablet-tabs-framew0').height();
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
}
