<?php

use LessPHP\Encoding\Json;


if ($this->req->proj == null) {
    die($this->T('Internal Error'));
}
$proj = preg_replace("/\/+/", "/", rtrim($this->req->proj, '/'));

$projPath = lesscreator_proj::path($proj);
if (strlen($projPath) < 1) {
    die($this->T('Internal Error'));
}

$projInfo = lesscreator_proj::info($proj);

$basedir = $_COOKIE["basedir"];

if (isset($projInfo['name'])) {

    $pjc = $basedir .'/conf/lesscreator/projlist.json';

    $pjs = null;
    $rs = lesscreator_fs::FsFileGet($pjc);
    //print_r($rs);
    if ($rs->status == 200) {
        $pjs = json_decode($rs->data->body, true);
    }

    if (!is_array($pjs)) {
        $pjs = array();
    }

    if (!isset($pjs[$projInfo['projid']])
        || $pjs[$projInfo['projid']]['name'] != $projInfo['name']
        || $pjs[$projInfo['projid']]['path'] != $projPath) {

        $pjs[$projInfo['projid']]['name'] = $projInfo['name'];
        $pjs[$projInfo['projid']]['path'] = $projPath;

        lesscreator_fs::FsFilePut($pjc, Json::prettyPrint($pjs));
    }
}

$props = isset($projInfo['props']) ? explode(",", $projInfo['props']) : array();
$props_def = lesscreator_service::listAll();

$ptpath = md5("");

$title = $projInfo['name'];
if (strlen($title) < 20) {
    //
} else if (mb_strlen($title, 'utf-8') > 20) {
    $title = mb_substr($title, 0, 20, 'utf-8'). "...";
}
?>

<div class="lcx-start-nav">
    
    <ul>
        <li class="lcx-start-navbtn">
            <i class="icon-plus-sign icon-white lcx-start-ico"></i>
            
            <ul class="lcx-start-submenu">
                <li>
                    <a href="#proj/fs/file-new" onclick="_proj_fs_nav_olclick(this)">
                        <img src="/lesscreator/static/img/page_white_add.png" class="h5c_icon" />
                        <?php echo $this->T('New File')?>
                    </a>
                </li>
                <li>
                    <a href="#proj/fs/file-new-dir" onclick="_proj_fs_nav_olclick(this)">
                        <img src="/lesscreator/static/img/folder_add.png" class="h5c_icon" />
                        <?php echo $this->T('New Folder')?>
                    </a>
                </li>
                <li>
                    <a href="#proj/fs/file-upl" onclick="_proj_fs_nav_olclick(this)">
                        <img src="/lesscreator/static/img/page_white_get.png" class="h5c_icon" />
                        <?php echo $this->T('Upload')?>
                    </a>
                </li>
            </ul>
        </li>
        
        <li class="lcx-start-navbtn">
            <a href="#proj/fs/file-upl" 
                onclick="_fs_tree_dir('', 1)" 
                class="lcx-start-navbtn" title="Refresh">
                <i class="icon-refresh icon-white lcx-start-ico"></i>
            </a>
        </li>
    </ul>

</div>


<!--ProjectFilesManager-->
<div id="lcx-start-fstree" class="less_scroll">
    <div id="pt<?=$ptpath?>" class="hdev-proj-files"></div>
</div>

<div id="_proj_inlet_body"></div>

<script>

$("title").text('<?php echo $projInfo['name']?> - lessCreator');

<?php
echo "sessionStorage.ProjPath = '{$projPath}';";
echo "sessionStorage.ProjId = '{$projInfo['projid']}';";
?>

$(document).click(function(event) {

    // Mark the last active project path
    //  will be used in launch the enter project after user signed
    //  or recover the status after browser crashed
    var uname = lessSession.Get("SessUser");
    if (lessLocalStorage.Get(uname +"LastProjPath") != sessionStorage.ProjPath) {
        lessLocalStorage.Set(uname +"LastProjPath") = sessionStorage.ProjPath;
    }
});

function _proj_nav_open(plg)
{
    $.ajax({
        type    : "GET",
        url     : '/lesscreator/proj/'+ plg +'/index?proj='+ projCurrent,
        success : function(rsp) {

            $("#_proj_inlet_body").html(rsp);
            
            if (sessionStorage.ProjNavLast != plg) {
                sessionStorage.ProjNavLast = plg;
            }
            
            $("._proj_nav li.active").removeClass("active");
            $(".ueg14o_"+plg).addClass("active");
            
            lcLayoutResize();
        }
    });
}

if (!sessionStorage.ProjNavLast) {
    sessionStorage.ProjNavLast = 'fs';
}

//_proj_nav_open(sessionStorage.ProjNavLast);
function _proj_plugins_lessdata()
{
    var opt = {
        'title': '<?php echo sprintf($this->T('%s Settings'), $this->T('Database'))?>',
        'close':'1',
        'img': '/lesscreator/static/img/plugins/lessdata/aliyun-rds.png',
    }

    var url = '/lesscreator/plugins/lessdata/index?proj='+ lessSession.Get("ProjPath");

    h5cTabOpen(url, 'w0', 'html', opt);
}
function _proj_plugins_phpyaf()
{
    var opt = {
        'title': 'Yaf Framework',
        'close':'1',
        'img': '/lesscreator/static/img/plugins/php-yaf/yaf-s2-48.png',
    }

    var url = '/lesscreator/plugins/php-yaf/index?proj='+ lessSession.Get("ProjPath");

    h5cTabOpen(url, 'w0', 'html', opt);
}


/*
$('._proj_tab_href').click(function() {
    
    var uri = $(this).attr('href').substr(1);

    switch (uri) {
    case 'plugins/lessdata/index':

        var opt = {
            'title': '<?php echo sprintf($this->T('%s Settings'), $this->T('Database'))?>',
            'close':'1',
            'img': '/lesscreator/static/img/plugins/lessdata/aliyun-rds.png',
        }

        var url = '/lesscreator/'+ uri +'?proj='+ lessSession.Get("ProjPath");

        h5cTabOpen(url, 'w0', 'html', opt);
        break;
    }
    //_proj_nav_open(url);
});
*/

var _proj_tab_active = false;
var _proj_tab_last = lessLocalStorage.Get("tab.fra.urid.w0");


lcData.Query("files", "projdir", sessionStorage.ProjPath, function(ret) {
    
    //console.log("Query files");
    if (ret == null) {
        return;
    }
    
    var opt = {close: 1};

    if (ret.value.id && ret.value.projdir == sessionStorage.ProjPath) {        

        if (ret.value.icon) {
            opt.img = ret.value.icon;
        }       

        if (!_proj_tab_active || _proj_tab_last == ret.value.id) {
            _proj_tab_active = true;
            //console.log("real open:"+ ret.value.filepth);
        } else {
            opt.titleonly = true;            
        }

        h5cTabOpen(ret.value.filepth, "w0", "editor", opt);

        if (ret.value.ctn1_sum.length > 10 && ret.value.ctn1_sum != ret.value.ctn0_sum) {
            $("#pgtab"+ ret.value.id +" .chg").show();
            $("#pgtab"+ ret.value.id +" .pgtabtitle").addClass("chglight");
        }
    }

    ret.continue();
});



$(".navitem_more").click(function(event) {
    
    event.stopPropagation();

    $(document).click(function() {
        //console.log("lc-navlet-moreol, out click, empty/hide");
        $('.lc-navlet-moreol').empty().hide();
        $(document).unbind('click');
    });
});

//lcNavletRefresh("projfs");

function _proj_fs_nav_hdr(uri)
{
    switch (uri) {
    case "proj/fs/file-new":
        _fs_file_new_modal("file", "", "", 0);
        break;
    case "proj/fs/file-upl":
        _fs_file_upl_modal("");
        break;
    case "proj/fs/file-new-dir":
        _fs_file_new_modal("dir", "", "", 0);
        break;
    /* case "plugins/php-yaf/index":
        var opt = {
            'title': 'Yaf Framework',
            'close':'1',
            'img': '/lesscreator/static/img/plugins/php-yaf/yaf-ico-48.png',
        }

        var url = '/lesscreator/'+ uri +'?proj='+ lessSession.Get("ProjPath");

        h5cTabOpen(url, 'w0', 'html', opt);
        break;
    case "plugins/php-zf/index":
        var opt = {
            'title': 'Zend Framework',
            'close':'1',
            'img': '/lesscreator/static/img/plugins/php-zf/zf-ico-48.png',
        }

        var url = '/lesscreator/'+ uri +'?proj='+ lessSession.Get("ProjPath");

        h5cTabOpen(url, 'w0', 'html', opt);
        break;
    */
    }
}
function _proj_fs_nav_olclick(node)
{
    var uri = node.getAttribute("href").substr(1);
    _proj_fs_nav_hdr(uri);
}


/* $("#lc-navlet-frame-projfs .navitem").click(function() {

    var uri = $(this).attr('href').substr(1);

    _proj_fs_nav_hdr(uri);
});*/

/*
function _proj_set_refresh()
{
    $("#hdev-proj-set").bind("click", function(e) {
    
        $(this).find(".hdev-rcmenu").css({
            top: e.pageY+'px',
            left: e.pageX
        }).toggle();
       
        $(this).find(".hdev_rcobj_rename").click(function() {
            _fs_file_mov_modal("");
        });
        
        $(document).click(function() {
            $(this).find('.hdev-rcmenu').hide();
        });
        
        return false;
    });
}*/

function _fs_file_new_modal(type, path, file, readonly)
{
    var tit = "<?php echo $this->T('New File')?>";
    if (type == 'dir') {
        tit = "<?php echo $this->T('New Folder')?>";
    }

    var url = "/lesscreator/proj/fs/file-new?path="+ path +"&type="+ type;
    url += "&readonly="+ readonly;
    url += "&file="+ file;
    
    lessModalOpen(url, 0, 550, 160, tit, null);
}

function _fs_file_new_callback(path)
{
    _fs_tree_dir(path, 1);
}

function _fs_file_upl_modal(path)
{
    // Check for the various File API support.
    if (window.File && window.FileReader && window.FileList && window.Blob) {
        // Great success! All the File APIs are supported.
    } else {
        alert("<?php echo $this->T('The File APIs are not fully supported in this browser')?>");
        return;
    }
    
    var tit = "<?php echo $this->T('Upload File From Location')?>";
    var url = "/lesscreator/proj/fs/file-upl?path="+ path;
    lessModalOpen(url, 0, 600, 400, tit, null);
}

function _fs_file_mov_modal(path)
{
    var tit = "<?php echo $this->T('Rename File/Folder')?>";
    var url = "/lesscreator/proj/fs/file-mov?path="+ path;
    lessModalOpen(url, 0, 550, 160, tit, null);
}


/**
    How to use jQuery contextmenu:
    
    1. http://www.webdeveloperjuice.com/demos/jquery/vertical_menu.html
    2. http://www.electrictoolbox.com/jquery-modify-right-click-menu/
 */
function _fs_tree_refresh()
{
    //console.log("_fs_tree_refresh ...");

    $(".hdev-proj-tree").bind("contextmenu", function(e) {
        
        h = $(this).find(".hdev-rcmenu").height();
        t = e.pageY;
        bh = $('body').height() - 20;        
        if ((t + h) > bh) {
            t = bh - h;
        }
        
        bw = $('body').width() - 20;
        l = e.pageX;
        if (l > (bw - 200)) {
            l = bw - 200;
        }

        $(this).find('.hdev-rcmenu').hide();
        
        $(this).find(".hdev-rcmenu").css({
            top: t +'px',
            left: l +'px'
        }).show();
    
        $(this).find(".hdev-rcmenu").click(function() {
            $(this).find(".hdev-rcmenu").hide();
        });
        
        $(this).find(".hdev_rcobj_file").click(function() {
            p = $(this).position();
            path = $(this).attr('href').substr(1);
            _fs_file_new_modal("file", path, "", 0);
        });
        $(this).find(".hdev_rcobj_dir").click(function() {
            path = $(this).attr('href').substr(1);
            _fs_file_new_modal("dir", path, "", 0);
        });
        $(this).find(".hdev_rcobj_upload").click(function() {
            path = $(this).attr('href').substr(1);
            _fs_file_upl_modal(path);
        });
        $(this).find(".hdev_rcobj_rename").click(function() {
            path = $(this).attr('href').substr(1);
            _fs_file_mov_modal(path);
        });
        
        $(document).click(function() {
            $(this).find('.hdev-rcmenu').hide();
        });
    
        return false;
    });
}

function _fs_tree_dir(path, force)
{
    path = path.replace(/(^\/*)|(\/*$)/g, "");
    p = lessCryptoMd5(path);
    //console.log("do path"+ path)
    if (force != 1 && $("#pt"+p).html() && $("#pt"+p).html().length > 1) {
        $("#pt"+p).empty();
        return;
    }
    
    $.ajax({
        type    : "GET",
        url     : '/lesscreator/proj/fs/tree?_='+ Math.random(),
        data    : 'proj='+projCurrent+'&path='+path,
        async   : false,
        success : function(data) {
            $("#pt"+p).html(data);
            lcLayoutResize();
        }
    });
}

function _fs_file_del(path)
{
    path = path.replace(/(^\/*)|(\/*$)/g, "");
    p = lessCryptoMd5(path);
    
    var req = {
        "access_token" : lessCookie.Get("access_token"),
        "data" : lessSession.Get("ProjPath") +"/"+ path,
    }

    $.ajax({
        type    : "POST",
        url     : "/lesscreator/api?func=fs-file-del",
        data    : JSON.stringify(req),
        timeout : 3000,
        success : function(rsp) {

            var obj = JSON.parse(rsp);
            if (obj.status == 200) {
                hdev_header_alert('success', "<?php echo $this->T('Successfully Done')?>");
                $("#ptp"+p).remove();
                $("#pt"+p).remove();
            } else {
                hdev_header_alert('error', obj.message);
            }
        },
        error   : function(xhr, textStatus, error) {
            hdev_header_alert('error', textStatus+' '+xhr.responseText);
        }
    });
}
_fs_tree_dir('', 1);
//_proj_set_refresh();
//_fs_tree_dir('', 1);

$.ajax({
    url     : "/lesscreator/api/proj/start?proj="+ lessSession.Get("ProjPath"),
    type    : "GET",
    timeout : 10000,
    success : function(rsp) {
        
        var rsj = JSON.parse(rsp);        
        if (rsj.status != 200) {
            return;
        }

        lessPagelet.Render(rsj.data, "proj/navstart.tpl", "lcbind-proj-navstart");
    }
});

</script>
