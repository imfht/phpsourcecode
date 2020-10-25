
var lcProject = {
    ProjectIndex: "/home/action/.lesscreator/projects.json",
    ProjectInfoDef: {
        projid  : "",
        name    : "",
        desc    : "",
        version : "0.0.1",
        release : "1",
        arch    : "all",
        grp_app : "",
        grp_dev : "",
    }
}


lcProject.New = function(options)
{
    options = options || {};

    if (typeof options.success !== "function") {
        options.success = function(){};
    }
        
    if (typeof options.error !== "function") {
        options.error = function(){};
    }
    
    if (options.projid === undefined) {
        options.error(400, "Project ID can not be null");
        return;
    }

    if (options.name === undefined || options.name.length < 1) {
        options.error(400, "Project Name can not be null");
        return;
    }

    var projinfo = this.ProjectInfoDef;
    projinfo.name = options.name;
    projinfo.projid = options.projid;

    if (options.grp_app !== undefined) {
        projinfo.grp_app = options.grp_app;
    }
    if (options.grp_dev !== undefined) {
        projinfo.grp_dev = options.grp_dev;
    }
    if (options.desc !== undefined) {
        projinfo.desc = options.desc;
    }

    // TODO valid options.projid
    var projpath = "/home/action/projects/"+ options.projid;

    BoxFs.Post({
        path: projpath + "/lcproject.json",
        data: JSON.stringify(projinfo),
        success: function(rsp) {
            options.success({
                path : projpath,
                info : projinfo, 
            });
        },
        error: function(status, message) {
            options.error(status, message);
        }
    });
}

lcProject.Open = function(proj)
{
    var ukey = lessSession.Get("access_userkey");

    if (!proj) {
        proj = lessSession.Get("proj_current");
    }

    if (!proj) {
        proj = lessLocalStorage.Get(ukey +"_proj_current");
    }    

    if (!proj) {
        // TODO
        lessModalOpen(lc.base + "project/open-nav", 1, 800, 450, "Start a Project from ...", null);
        return;
    }

    var uri = "proj="+ proj;

    if (projCurrent == proj) {
        // TODO
        return;
    }

    // if (projCurrent != proj) {
    //     if (projCurrent.split("/").pop(-1) != proj.split("/").pop(-1)) {
    //         window.open(lc.base + "index?"+ uri, '_blank');
    //     }
    //     return;
    // }

    var req = {
        path: proj +"/lcproject.json",
    }

    req.error = function(status, message) {

        if (status == 404) {
            // TODO
        }
        alert("Can Not Found Project: "+ proj +"/lcproject.json, Error:"+ message);
    }

    req.success = function(file) {
            
        // console.log(file);
        if (file.size < 10) {
            alert("Can Not Found Project: "+ proj +"/lcproject.json");
            // TODO
            return;
        }

        var pinfo = JSON.parse(file.body);
        if (pinfo.projid === undefined) {
            alert("Can Not Found Project: "+ proj +"/lcproject.json");
            // TODO
            return
        }

        lessSession.Set("proj_id", pinfo.projid);
        lessSession.Set("proj_current_name", pinfo.name);
        lessSession.Set("proj_current", proj);
        lessLocalStorage.Set(ukey +"_proj_current", proj);

        $("#nav-proj-name").text("loading");
        $("#lcbind-proj-nav").show(100);

        $.ajax({
            url     : lc.base + "project/file-nav?_="+ Math.random(),
            type    : "GET",
            timeout : 10000,
            success : function(rsp) {
                
                $("#lcbind-proj-filenav").empty().html(rsp);

                lcLayout.ColumnSet({
                    id   : "lcbind-proj-filenav",
                    hook : lcProjectFs.LayoutResize
                });

                // console.log("open filenav");

                var treeload = {
                    path : proj,
                }

                treeload.success = function() {
                    
                    // console.log("open filenav, treeload.success");

                    lcProject.OpenHistoryTabs();

                    lcLayout.Resize();
                }

                lcProject.FsTreeLoad(treeload);
            },
            error: function(xhr, textStatus, error) {
                // TODO
            }
        });
    }

    BoxFs.Get(req);
}

lcProject.OpenHistoryTabs = function()
{
    // console.log("lcProject.OpenHistoryTabs");

    // var last_tab_urid = lessLocalStorage.Set(lessSession.Get("boxid") +"."+ lessSession.Get("proj_id") +".tab."+ item.target);

    lcData.Query("files", "projdir", lessSession.Get("proj_current"), function(ret) {
    
        // console.log("Query files");
        if (ret == null) {
            return;
        }
        
        if (ret.value.id && ret.value.projdir == lessSession.Get("proj_current")) {

            var icon = undefined;
            if (ret.value.icon) {
                icon = ret.value.icon;
            }

            // TODO
            // if (!_proj_tab_active || _proj_tab_last == ret.value.id) {
            //     _proj_tab_active = true;
            //     //console.log("real open:"+ ret.value.filepth);
            // } else {
            //     opt.titleonly = true;            
            // }

            lcTab.Open({
                uri   : ret.value.filepth,
                type  : "editor",
                icon  : icon,
                close : true,
                success : function() {
                    // $('#pgtab'+ ret.value.id).addClass("current");
                }
            });

            if (ret.value.ctn1_sum.length > 10 && ret.value.ctn1_sum != ret.value.ctn0_sum) {
                $("#pgtab"+ ret.value.id +" .chg").show();
                $("#pgtab"+ ret.value.id +" .pgtabtitle").addClass("chglight");
            }
        }

        ret.continue();
    });
}

lcProject.FsTreeLoad = function(options)
{
    options = options || {};

    if (typeof options.success !== "function") {
        options.success = function(){};
    }

    if (typeof options.error !== "function") {
        options.error = function(){};
    }

    var ptdid = lessCryptoMd5(options.path);
    if (options.path == lessSession.Get("proj_current")) {
        ptdid = "root";
    }

    if (ptdid != "root" && document.getElementById("fstd"+ ptdid)) {
        $("#fstd"+ ptdid).remove();
        return;
    }

    var req = {
        path: options.path,// lessSession.Get("proj_current"),
    }

    req.success = function(rs) {
        
        var ls = rs.items;

        for (var i in ls) {
            
            if (ls[i].name == "lcproject.json") {
                // TODO
            }

            var fspath = rs.path +"/"+ ls[i].name;
            ls[i].path = fspath.replace(/\/+/g, "/");
            ls[i].fsid = lessCryptoMd5(ls[i].path);

            ls[i].fstype = "none";

            var ico = "page_white";

            if (ls[i].isdir !== undefined && ls[i].isdir == true) {
            
                ico = "folder";
                ls[i].fstype = "dir";

            } else if (ls[i].mime.substring(0, 4) == "text"
                || ls[i].name.slice(-4) == ".tpl"
                || ls[i].mime.substring(0, 23) == "application/x-httpd-php"
                || ls[i].mime == "application/javascript"
                || ls[i].mime == "application/x-empty"
                || ls[i].mime == "inode/x-empty"
                || ls[i].mime == "application/json") {

                if (ls[i].mime == "text/x-php" 
                    || ls[i].name.slice(-4) == ".php") {
                    ico = "page_white_php";
                } else if (ls[i].name.slice(-2) == ".h" 
                    || ls[i].name.slice(-4) == ".hpp") {
                    ico = "page_white_h";
                } else if (ls[i].name.slice(-2) == ".c") {
                    ico = "page_white_c";
                } else if (ls[i].name.slice(-4) == ".cpp" 
                    || ls[i].name.slice(-3) == ".cc") {
                    ico = "page_white_cplusplus";
                } else if (ls[i].name.slice(-3) == ".js" 
                    || ls[i].name.slice(-4) == ".css") {
                    ico = "page_white_code";
                } else if (ls[i].name.slice(-5) == ".html" 
                    || ls[i].name.slice(-4) == ".htm" 
                    || ls[i].name.slice(-6) == ".phtml"
                    || ls[i].name.slice(-6) == ".xhtml"
                    || ls[i].name.slice(-4) == ".tpl") {
                    ico = "page_white_world";
                } else if (ls[i].name.slice(-3) == ".sh" 
                    || ls[i].mime == "text/x-shellscript") {
                    ico = "application_osx_terminal";
                } else if (ls[i].name.slice(-3) == ".rb") {
                    ico = "page_white_ruby";
                } else if (ls[i].name.slice(-3) == ".go") {
                    ico = "ht-page_white_golang";
                } else if (ls[i].name.slice(-3) == ".py" 
                    || ls[i].name.slice(-4) == ".yml"
                    || ls[i].name.slice(-5) == ".yaml"
                    ) {
                    ico = "page_white_code";
                }

                // ls[i].href = "javascript:h5cTabOpen('{$p}','w0','editor',{'img':'{$fmi}', 'close':'1'})";
                
                ls[i].fstype = "text";

            } else if (ls[i].mime.slice(-5) == "image") {
                ico = "page_white_picture";
            }

            ls[i].ico = ico;
        }

        if (document.getElementById("fstd"+ ptdid) == null) {
            $("#ptp"+ ptdid).after("<div id=\"fstd"+ptdid+"\" style=\"padding-left:20px;\"></div>");
        } else {
            // TODO
        }

        lessTemplate.RenderFromId("fstd"+ ptdid, "lcx-filenav-tree-tpl", ls);
        
        options.success();

        setTimeout(function() {
            lcProject.FsTreeEventRefresh();
            lcLayout.Resize();
            $("#nav-proj-name").text(lessSession.Get("proj_current_name"));
        }, 10);
    }

    req.error = function(status, message) {
        // console.log(status, message);
        options.error(status, message);
    }

    BoxFs.List(req);
}

var _fsItemPath = "";
lcProject.FsTreeEventRefresh = function()
{
    // if ($("#lclay-col"+ 1).length < 1) {
    //     $("#lcbind-laycol").before("<td width=\"10px\" class=\"lclay-col-resize\"></td>\
    //         <td id=\"lclay-col"+ 1 +"\" class=\"lcx-lay-colbg\">"+1+"</td>");
    
    //     $("#lcbind-laycol").before("<td width=\"10px\" class=\"lclay-col-resize\"></td>\
    //         <td id=\"lclay-col"+ 2 +"\" class=\"lcx-lay-colbg\">"+2+"</td>");
    // }

    // console.log("lcProject.FsTreeEventRefresh");
    $(".lcx-fsitem").unbind();
    $(".lcx-fsitem").bind("contextmenu", function(e) {

        var h = $("#lcbind-fsnav-rcm").height();
        // h = $(this).find(".hdev-rcmenu").height();
        var t = e.pageY;
        var bh = $('body').height() - 20;        
        if ((t + h) > bh) {
            t = bh - h;
        }
        
        var bw = $('body').width() - 20;
        var l = e.pageX;
        if (l > (bw - 200)) {
            l = bw - 200;
        }
        // console.log("pos"+ t +"x"+ l);
        $("#lcbind-fsnav-rcm").css({
            top: t +'px',
            left: l +'px'
        }).show(10);

        _fsItemPath = $(this).attr("lc-fspath");
        
        var fstype = $(this).attr("lc-fstype");
        if (fstype == "dir") {
            $(".fsrcm-isdir").show();
        } else {
            $(".fsrcm-isdir").hide();
        }

        return false;
    });
    $(".lcx-fsitem").bind("click", function() {
    
        var fstype = $(this).attr("lc-fstype");
        var fspath = $(this).attr("lc-fspath");
        var fsicon = $(this).attr("lc-fsico")
    
        switch (fstype) {
        case "dir":
            lcProject.FsTreeLoad({path: fspath});
            break;
        case "text":
            lcTab.Open({
                uri    : fspath,
                // colid : "lclay-colmain",
                type   : "editor",
                icon   : fsicon,
                close  : true
            });
            break;
        default:
            //
        }
    });

    // 
    $(".lcbind-fsrcm-item").unbind(); 
    $(".lcbind-fsrcm-item").bind("click", function() {

        var action = $(this).attr("lc-fsnav");

        // var ppath = path.slice(0, path.lastIndexOf("/"));
        // var fname = path.slice(path.lastIndexOf("/") + 1);
        // console.log("right click: "+ action);
        switch (action) {
        case "new-file":
            lcProjectFs.FileNew("file", _fsItemPath, "");
            break;
        case "new-dir":
            lcProjectFs.FileNew("dir", _fsItemPath, "");
            break;
        case "upload":
            lcProjectFs.FileUpload(_fsItemPath);
            break;
        case "rename":
            lcProjectFs.FileRename(_fsItemPath);
            break;
        case "file-del":
            lcProjectFs.FileDel(_fsItemPath);
            break;
        default:
            break;
        }

        $("#lcbind-fsnav-rcm").hide();
    });
    
    $(document).click(function() {
        $("#lcbind-fsnav-rcm").hide();
    });
}

var lcProjectFs = {}

lcProjectFs.LayoutResize = function(options)
{
    var fsp = $("#lcbind-fsnav-fstree").position();
    if (fsp) {
        // $(".lcx-fsnav").width((lcLayout.width * options.width / 100));
        // console.log((lcLayout.width * options.width / 100));
        $("#lcbind-fsnav-fstree").width((lcLayout.width * options.width / 100));
        $("#lcbind-fsnav-fstree").height(lcLayout.height - (fsp.top - lcLayout.postop));
    }
}

lcProjectFs.FileNew = function(type, path, file)
{
    if (path === undefined || path === null) {
        path = lessSession.Get("proj_current");
    }

    var formid = Math.random().toString(36).slice(2);

    var req = {
        header_title : (type == "dir") ? "New Folder" : "New File",
        position     : "cursor",
        width        : 550,
        height       : 160,
        tplid        : "lcbind-fstpl-filenew",
        data         : {
            formid   : formid,
            file     : file,
            path     : path,
            type     : type
        },
        buttons      : [
            {
                onclick : "lcProjectFs.FileNewSave(\""+ formid +"\")",
                title   : "Create",
                style   : "btn-inverse"
            },
            {
                onclick : "lessModal.Close()",
                title   : "Close"
            }
        ]
    }

    req.success = function() {
        $("#"+ formid +" :input[name=name]").focus();
    }

    lessModal.Open(req);
}

lcProjectFs.FileNewSave = function(formid)
{
    var path = $("#"+ formid +" :input[name=path]").val();
    var name = $("#"+ formid +" :input[name=name]").val();
    if (name === undefined || name.length < 1) {
        alert("Filename can not be null"); // TODO
        return;
    }

    BoxFs.Post({
        path: path +"/"+ name,
        data: "\n",
        success: function(rsp) {
            
            // hdev_header_alert('success', "{{T . "Successfully Done"}}");

            // if (typeof _plugin_yaf_cvlist == 'function') {
            //     _plugin_yaf_cvlist();
            // }

            // lcProject.FsTreeLoad({path: path});
            lessModal.Close();
        },
        error: function(status, message) {
            console.log(status, message);
            // hdev_header_alert('error', textStatus+' '+xhr.responseText);
        }
    });

    return false;
}

// html5 file uploader
var _fsUploadRequestId = "";
var _fsUploadAreaId    = "";
var _fsUploadBind      = null;

function _fsUploadTraverseTree(reqid, item, path)
{
    path = path || "";
  
    if (item.isFile) {
    
        // Get file
        item.file(function(file) {
            
            //console.log("File:", path + file.name);
            if (file.size > 10 * 1024 * 1024) {
                $("#"+ reqid +" .state").show().append("<div>"+ path +" Failed: File is too large to upload</div>");
                return;
            }

            _fsUploadCommit(reqid, file);
        });

    } else if (item.isDirectory) {
        // Get folder contents
        var dirReader = item.createReader();
        dirReader.readEntries(function(entries) {
            for (var i = 0; i < entries.length; i++) {
                _fsUploadTraverseTree(reqid, entries[i], path + item.name + "/");
            }
        });
    }
}

function _fsUploadHanderDragEnter(evt)
{
    this.setAttribute('style', 'border-style:dashed;');
}

function _fsUploadHanderDragLeave(evt)
{
    this.setAttribute('style', '');
}

function _fsUploadHanderDragOver(evt)
{
    evt.stopPropagation();
    evt.preventDefault();
}

function _fsUploadCommit(reqid, file)
{
    var reader = new FileReader();
    
    reader.onload = (function(file) {  
        
        return function(e) {
            
            if (e.target.readyState != FileReader.DONE) {
                return;
            }

            var ppath = $("#"+ reqid +" :input[name=path]").val();
            // console.log("upload path: "+ ppath);

            BoxFs.Post({
                path    : ppath +"/"+ file.name,
                size    : file.size,
                data    : e.target.result,
                encode  : "base64",
                success : function(rsp) {

                    $("#"+ reqid +" .state").show().append("<div>"+ file.name +" OK</div>");

                    // console.log(rsp);
                    // hdev_header_alert('success', "{{T . "Successfully Done"}}");

                    // if (typeof _plugin_yaf_cvlist == 'function') {
                    //     _plugin_yaf_cvlist();
                    // }

                    // lcProject.FsTreeLoad({path: ppath});
                    // lessModal.Close();
                },
                error: function(status, message) {

                    $("#"+ reqid +" .state").show().append("<div>"+ file.name +" Failed</div>");
                    console.log(status, message);
                    // hdev_header_alert('error', textStatus+' '+xhr.responseText);
                }
            });
        };

    })(file); 
    
    reader.readAsDataURL(file);
}

function _fsUploadHander(evt)
{            
    evt.stopPropagation();
    evt.preventDefault();

    var items = evt.dataTransfer.items;
    for (var i = 0; i < items.length; i++) {
        // webkitGetAsEntry is where the magic happens
        var item = items[i].webkitGetAsEntry();
        if (item) {
            _fsUploadTraverseTree(_fsUploadRequestId, item);
        }
    }
}

lcProjectFs.FileUpload = function(path)
{
    if (path === undefined || path === null) {
        path = lessSession.Get("proj_current");
        // alert("Path can not be null"); // TODO
        // return;
    }

    // Check for the various File API support.
    if (window.File && window.FileReader && window.FileList && window.Blob) {
        // Great success! All the File APIs are supported.
    } else {
        alert("The File APIs are not fully supported in this browser");
        return;
    }

    var reqid  = Math.random().toString(36).slice(2);
    var areaid = Math.random().toString(36).slice(2);

    // console.log("ids 1: "+ reqid +", "+ areaid);

    var req = {
        header_title : "Upload File From Location",
        position     : "cursor",
        width        : 600,
        height       : 400,
        tplid        : "lcbind-fstpl-fileupload",
        data         : {
            areaid   : areaid,
            reqid    : reqid,
            path     : path
        },
        buttons      : [
            // {
            //     onclick : "lcProjectFs.FileUploadSave(\""+ reqid +"\",\""+ areaid +"\")",
            //     title   : "Commit",
            //     style   : "btn-inverse"
            // },
            {
                onclick : "lessModal.Close()",
                title   : "Close"
            }
        ]
    }

    req.success = function() {    

        _fsUploadRequestId = reqid;

        // console.log("ids: "+ _fsUploadRequestId +", "+ areaid);

        if (_fsUploadBind != null) {

            _fsUploadBind.removeEventListener('dragenter', _fsUploadHanderDragEnter, false);
            _fsUploadBind.removeEventListener('dragover', _fsUploadHanderDragOver, false);
            _fsUploadBind.removeEventListener('drop', _fsUploadHander, false);
            _fsUploadBind.removeEventListener('dragleave', _fsUploadHanderDragLeave, false);

            _fsUploadBind = null;
        }

        // console.log("id:"+ areaid);

        _fsUploadBind = document.getElementById(areaid);

        // console.log(_fsUploadBind);

        _fsUploadBind.addEventListener('dragenter', _fsUploadHanderDragEnter, false);
        _fsUploadBind.addEventListener('dragover', _fsUploadHanderDragOver, false);
        _fsUploadBind.addEventListener('drop', _fsUploadHander, false);
        _fsUploadBind.addEventListener('dragleave', _fsUploadHanderDragLeave, false);
    }

    lessModal.Open(req);
}


lcProjectFs.FileRename = function(path)
{
    if (path === undefined || path === null) {
        alert("Path can not be null"); // TODO
        return;
    }

    var formid = Math.random().toString(36).slice(2);

    var req = {
        header_title : "Rename File/Folder",
        position     : "cursor",
        width        : 550,
        height       : 160,
        tplid        : "lcbind-fstpl-filerename",
        data         : {
            formid   : formid,
            path     : path
        },
        buttons      : [
            {
                onclick : "lcProjectFs.FileRenameSave(\""+ formid +"\")",
                title   : "Rename",
                style   : "btn-inverse"
            },
            {
                onclick : "lessModal.Close()",
                title   : "Close"
            }
        ]
    }

    req.success = function() {
        $("#"+ formid +" :input[name=pathset]").focus();
    }

    lessModal.Open(req);
}

lcProjectFs.FileRenameSave = function(formid)
{
    var path = $("#"+ formid +" :input[name=path]").val();
    var pathset = $("#"+ formid +" :input[name=pathset]").val();
    if (pathset === undefined || pathset.length < 1) {
        alert("Path can not be null"); // TODO
        return;
    }

    if (path == pathset) {
        lessModal.Close();
        return;
    }

    BoxFs.Rename({
        path    : path,
        pathset : pathset,
        success : function(rsp) {
            
            // hdev_header_alert('success', "{{T . "Successfully Done"}}");

            // if (typeof _plugin_yaf_cvlist == 'function') {
            //     _plugin_yaf_cvlist();
            // }
            var ppath = path.slice(0, path.lastIndexOf("/"));
            // console.log(ppath);

            lcProject.FsTreeLoad({path: ppath});
            lessModal.Close();
        },
        error: function(status, message) {
            console.log(status, message);
            // hdev_header_alert('error', textStatus+' '+xhr.responseText);
        }
    });
}

lcProjectFs.FileDel = function(path)
{
    if (path === undefined || path === null) {
        alert("Path can not be null"); // TODO
        return;
    }

    var formid = Math.random().toString(36).slice(2);

    var req = {
        header_title : "Delete File or Folder",
        position     : "cursor",
        width        : 550,
        height       : 180,
        tplid        : "lcbind-fstpl-filedel",
        data         : {
            formid   : formid,
            path     : path
        },
        buttons      : [
            {
                onclick : "lcProjectFs.FileDelSave(\""+ formid +"\")",
                title   : "Confirm and Delete",
                style   : "btn-danger"
            },
            {
                onclick : "lessModal.Close()",
                title   : "Cancel"
            }
        ]
    }

    lessModal.Open(req);
}

lcProjectFs.FileDelSave = function(formid)
{
    var path = $("#"+ formid +" :input[name=path]").val();
    if (path === undefined || path.length < 1) {
        alert("Path can not be null"); // TODO
        return;
    }

    BoxFs.Del({
        path    : path,
        success : function(rsp) {
            
            var fsid = "ptp" + lessCryptoMd5(path);
            $("#"+ fsid).remove();

            lessModal.Close();
        },
        error: function(status, message) {
            alert(message);
            // console.log(status, message);
            // hdev_header_alert('error', textStatus+' '+xhr.responseText);
        }
    });
}
