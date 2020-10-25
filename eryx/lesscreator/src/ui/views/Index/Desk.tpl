
<table class="lcx-header">
  <tr>
    <td width="10px"></td>

    <td width="60px" align="left">

        <div id="lcx-start-entry">
            <div>
                <img class="lse-logo" src="/lesscreator/~/lesscreator/img/gen/creator-white-96.png" />
            </div>
            <div class="lcc-box-entered">
                <img class="lse-logo-ar" src="/lesscreator/~/lesscreator/img/gen/arrow-b2-white-32.png" />
            </div>
        </div>

        <div class="lcx-start-well hide">

            <div class="lc-head">
                <div class="lc-logo"><img src="/lesscreator/~/lesscreator/img/gen/creator-white-96.png" /></div>
                <div class="lc-title">less Creator <em id="lcbind-lc-version">{{.lc_version}}</em></div>
                <div class="lc-close">&times;</div>
            </div>

            <div class="lc-line"></div>
            
            <div class="lc-body less-tile-area">

                <div class="less-tile-group x4">

                    <div class="ltg-title ">
                        {{T . "Project"}}
                    </div>
                    
                    <div class="less-tile flatui-bg-peter-river" onclick="lcProject.Open()">
                        <div class="lt-content icon">
                            <img src="/lesscreator/~/lesscreator/img/gen/pen0-48.png">
                        </div>
                        <div class="lt-status">
                            <div class="lts-name">{{T . "New Project"}}</div>
                        </div>
                    </div>

                    <div class="less-tile flatui-bg-nephritis" onclick="lcProjOpen()">
                        <div class="lt-content icon">
                            <img src="/lesscreator/~/lesscreator/img/gen/pen0-48.png">
                        </div>
                        <div class="lt-status">
                            <div class="lts-name">{{T . "Open Project"}}</div>
                        </div>
                    </div>

                    <a class="less-tile flatui-bg-alizarin" 
                        href="http://git.oschina.net/eryx/lesscreator/issues/new" target="_blank">
                        <div class="lt-content icon">
                            <div class="lcx-icon-bug lcx-icon-white"></div>
                        </div>
                        <div class="lt-status">
                            <div class="lts-name">{{T . "Report Issue"}}</div>
                        </div>
                    </a>

                    <a class="less-tile flatui-bg-amethyst" 
                        href="http://www.lesscompute.com" target="_blank">
                        <div class="lt-content icon">
                            <div class="lcx-icon-help lcx-icon-white"></div>
                        </div>
                        <div class="lt-status">
                            <div class="lts-name">{{T . "Help"}}</div>
                        </div>
                    </a>
                </div>

                <!--
                <div class="less-tile-group x3">

                    <div class="ltg-title ">
                        {{T . "Recent Projects"}}
                    </div>

                    <div class="less-tile w3 h2 flatui-bg-amethyst">
                        <div class="lt-content icon">
                            <img src="/lesscreator/~/lesscreator/img/gen/pen0-48.png">
                        </div>
                        <div class="lt-status">
                            <div class="lts-name">New Project</div>
                        </div>
                    </div>

                </div>
                -->
           
            </div>

      </div>
    </td>

    <td align="left">
        
        <div id="lcbind-box-nav" class="lcx-nav-grp">

            <div class="lcx-nav-item">
                <div class="lni-label">Box</div>
                <div class="lni-title" id="nav-box-state-msg">Connecting</div>
            </div>

        </div>

        <div id="lcbind-proj-nav" class="lcx-nav-grp hide">
            <div class="lcx-nav-item">
                <div class="lni-label">Project</div>
                <div class="lni-title" id="nav-proj-name">Connecting</div>
            </div>
        </div>

        <div class="lcx-nav-grp">
            <div id="lcx_halert" class="success hide"></div>
        </div>

        <div id="lcbind-proj-navstart" class="lcx-proj-navbox"></div>
    </td>
    
    <td align="right">      
      {{if .nav_user}}
      <div id="lcx-nav-user-box">
        <!-- <span class="lunb-name">{{.nav_user.name}}</span> -->
        <span><img class="lnub-photo" src="{{.nav_user.photo}}" /></span>
      </div>

      <div id="lcx-nav-user-modal" style="display:none;">

        <img class="lnum-photo" src="{{.nav_user.photo}}">

        <div class="lnum-name">{{.nav_user.name}}</div>

        <div id="lcx-nav-user-alert" class="alert hide"></div>

        <a class="btn btn-primary lnum-btn" href="{{.nav_user.lessids_url}}" target="_blank">Account Center</a>
        <a class="btn btn-default lnum-btn" href="{{.nav_user.lessids_url_signout}}">Sign out</a>

      </div>
      {{end}}
    </td>

    <td width="10px"></td>
  </tr>  
</table>

<div style="height:10px;"></div>

<!--
<table id="hdev_header" width="100%" border="0">
  <tr>
    <td width="10px"></td>

    <td class="" width="300px">
      <img class="lc_icon" src="/lesscreator/~/lesscreator/img/for-test/test.png" />
    </td>

    <td align="center">
        <div class="hdev-header-alert border_radius_5 hdev_alert">workspace files, open files, run and debug, deploy, preferences</div>
    </td>

    <td align="right" style="">
       
        <div class="btn-group" style="margin-left:0;">
            

            <div class="btn btn-small dropdown-toggle " data-toggle="dropdown" href="#">
                <i class="icon-user"></i>&nbsp;&nbsp;eryx&nbsp;&nbsp;<b class="caret"></b>
            </div>

            <ul class="dropdown-menu pull-right text-left">
                <?php
                /* $menus = Session::NavMenus('ue'); // TODO
                $prev = false;
                foreach ($menus as $menu) {
                    echo "<li><a href=\"/{$menu->projid}\">{$menu->name}</a></li>";
                    $prev = true;
                }
                if ($prev) {
                    echo '<li class="divider"></li>';
                }*/
                ?> 
                <li><a href="/user">{{T . "Account Settings"}}</a></li>
                <li><a href="/lesscreator">{{T . "lessCreator"}}</a></li>
                <li class="divider"></li>
                <li><a href="/user/logout">{{T . "Logout"}}</a></li>
            </ul>

        </div>

    </td>

    <td width="10px"></td>
  </tr>
</table>
-->

<table id="lcbind-layout" border="0" cellpadding="0" cellspacing="0" class="">
  <tr>
    <!--
    http://www.daqianduan.com/jquery-drag/
    -->
    <td width="10px" class="lclay-colsep"></td>
    <td id="lcbind-proj-filenav" class="lcx-lay-colbg" width="220px"></td>
    <!-- <td id="lcx-proj-box" class="lcx-lay-colbg" valign="top" width="280px"></td> -->

    <td width="10px" class="lclay-colsep lclay-col-resize" lc-layid="lclay-colmain"></td>
    <td id="lclay-colmain" class="lcx-lay-colbg"></div></td>

    <!-- column blank 2 -->
    <!-- <td width="10px" id="h5c-lyo-col-w-ctrl" class="h5c_resize_col"></td>
    
    <td id="h5c-lyo-col-w" valign="top" class="lcx-lay-colbg">
      <table width="100%" height="100%">
        <tr>
          <td id="h5c-tablet-framew0" class="hdev-layout-container hide" valign="top">
            
            <div id="h5c-tablet-tabs-framew0" class="h5c_tablet_tabs_frame">
              <div class="h5c_tablet_tabs_lm">
                <div id="h5c-tablet-tabs-w0" class="h5c_tablet_tabs"></div>
              </div>
              <div class="h5c_tablet_tabs_lr">
                <div class="pgtab_more lc_pgtab_more" href="#w0"></div>
              </div>
            </div>

            <div id="h5c-tablet-toolbar-w0" class="hide"></div>
            <div id="h5c-tablet-body-w0" class="h5c_tablet_body less_scroll"></div>

          </td>
        </tr>

        <tr><td height="10px" id="h5c-resize-roww0" class="h5c_resize_row hide"></td></tr>
        
        <tr>
          <td id="h5c-tablet-framew1" class="hdev-layout-container hide" valign="top">
            
            <div id="h5c-tablet-tabs-framew1" class="h5c_tablet_tabs_frame pgtabs_frame">
              <div class="h5c_tablet_tabs_lm">
                <div id="h5c-tablet-tabs-w1" class="h5c_tablet_tabs"></div>
              </div>
              <div class="h5c_tablet_tabs_lr">
              </div>
            </div>

            <div id="h5c-tablet-body-w1" class="h5c_tablet_body less_scroll"></div>

          </td>
        </tr>
      
      </table>
    </td> -->

    <td width="10px" id="lcbind-laycol"></td>

  </tr>
</table>

<div id="lc_editor_tools" class="hide">

    <div class="editor_bar hdev-ws hdev-tabs hcr-pgbar-editor">
        
        <div class="tabitem" onclick="lcEditor.SaveCurrent()">
            <div class="ctn"><i class="icon-hdd"></i> {{T . "Save"}}</div>
        </div>

        <div class="tabitemline"></div>
        <div class="tabitem" onclick="lcEditor.Search()">
            <div class="ctn"><i class="icon-search"></i> {{T . "Search"}}</div>
        </div>

        <div class="tabitemline"></div>
        <div class="tabitem" onclick="lcEditor.Undo()">
            <div class="ctn"><i class="icon-chevron-left"></i> {{T . "Undo"}}</div>
        </div>

        <div class="tabitem" onclick="lcEditor.Redo()">
            <div class="ctn"><i class="icon-chevron-right"></i> {{T . "Redo"}}</div>
        </div>
        
        <!-- <div class="tabitemline"></div>
        <div class="tabitem">
            <div class="ico"><img src="/lesscreator/~/lesscreator/img/disk.png" align="absmiddle" /></div>
            <div class="ctn"><input onclick="lcEditor.ConfigSet('editor_autosave')" type="checkbox" id="editor_autosave" name="editor_autosave" value="on" /> Auto Saving</div>
        </div> -->

        <div class="tabitemline"></div>
        <div class="tabitem" onclick="lcEditor.ConfigEditMode()">
            <div class="ico lc-editor-editmode"><img src="/lesscreator/~/lesscreator/img/editor/mode-win-48.png" class="h5c_icon" /></div>
            <div class="ctn">{{T . "Editor Mode"}}</div>
        </div>

        <div class="tabitemline"></div>
        <div class="tabitem" onclick="lcEditor.ConfigModal()">
            <div class="ctn"><i class="icon-cog"></i> {{T . "Setting"}}</div>
        </div>
    </div>

    <div class="lc_editor_searchbar hide form-inline">
        <div class="input-prepend input-append">
            <span class="add-on"><i class="icon-search"></i></span>
            <input class="input-small" type="text" name="find" value="{{T . "Find Word"}}" />
            <button class="btn" onclick="lcEditor.SearchNext()">{{T . "Search"}}</button>
        </div>

        <label class="inline"> {{T . "or"}} </label>
        
        <div class="input-append">
            <input class="input-small" name="replace" type="text" value="{{T . "Replace with"}}">
            <button class="btn" type="button" onclick="lcEditor.SearchReplace(false)">{{T . "Replace"}}</button>
            <button class="btn" type="button" onclick="lcEditor.SearchReplace(true)">{{T . "Replace All"}}</button>
        </div>
        
        <!-- <label class="checkbox inline">
          <input onclick="lcEditor.ConfigSet('editor_search_case')" type="checkbox" id="editor_search_case" name="editor_search_case" value="on" />
          Match case
        </label> -->

        <button type="button" class="close" onclick="lcEditor.Search()">&times;</button>
    </div>
</div>

<div id="lctab-tpl" class="hide">
  <table id="lctab-box{[=it.tabid]}" class="lctab-box" width="100%" height="100%">
    <tr>
      <td class="" valign="top">

        <div id="lctab-nav{[=it.tabid]}" class="lctab-nav">
          <div class="lctab-navm">
            <div id="lctab-navtabs{[=it.tabid]}" class="lctab-navs"></div>
          </div>
          <div class="lctab-navr">
            <div class="pgtab_more lc_pgtab_more" href="#{[=it.tabid]}"></div>
          </div>
        </div>

        <div id="lctab-bar{[=it.tabid]}" class="lctab-bar"></div>
        <div id="lctab-body{[=it.tabid]}" class="lctab-body less_scroll"></div>
      </td>
    </tr>
  </table>
</div>

<div id="lctab-openfiles-ol" class="lctab-navmore less_scroll"></div>

<script>

$("#lcx-nav-user-box").hover(
    function() {
        $("#lcx-nav-user-modal").fadeIn(300);
    },
    function() {
    }
);
$("#lcx-nav-user-modal").hover(
    function() {
    },
    function() {
        $("#lcx-nav-user-modal").fadeOut(300);
    }
);


$("#lcx-start-entry").click(function() {
    $("#lcx-start-entry").fadeOut(150);
    $(".lcx-start-well").show(150);
});
$("#lcx-start-entry").hover(function() {  
    $("#lcx-start-entry").fadeOut(150);
    $(".lcx-start-well").show(150);
});
$(".lcx-start-well").click(function() {
    $("#lcx-start-entry").fadeIn(300);
    $(".lcx-start-well").hide(300);
});


//$("#lcx-start-entry").fadeOut(150);
//$(".lcx-start-well").show(150);
//$(body).css({
//    "-webkit-filter": blur(2px) contrast(0.4) brightness(1.4)
//});

var opt = {
    'img': '/lesscreator/~/lesscreator/img/app-t3-16.png',
    'title': 'Quick Start',
    'close': '1',
};
//h5cTabOpen("/lesscreator/app/quick-start?", 'w0', 'html', opt);

function _lc_nav_terminal()
{
    var domobj = document.getElementById("lc-terminal");
    if (!domobj) {
        lcWebTerminal(1);
        return;
    }

    if (!lc_terminal_conn.IsOk()) {
        lcWebTerminal(1);
    } else if (lc_terminal_conn.IsOk()) {
        lc_terminal_conn.CloseAll();
        var urid = lessCryptoMd5("/lesscreator/term/index?");
        lcTabClose(urid, 1);
    }
}
// lcWebTerminal(0);


lcLayout.Resize();
lcLayout.BindRefresh();

// setTimeout(lcLayout.Resize, 3000)
lcBoxRefresh();

</script>
