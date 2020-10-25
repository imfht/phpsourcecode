
<div class="lcx-fsnav">

    <span class="lfn-title">Files</span>

    <ul class="lfn-menus">
        <li class="lfnm-item">
            
            <i class="icon-plus-sign icon-white lfnm-item-ico"></i>
            
            <ul class="lfnm-item-submenu">
                <li>
                    <a href="#proj/fs/file-new" onclick="lcProjectFs.FileNew('file', null, '')">
                        <img src="/lesscreator/~/lesscreator/img/page_white_add.png" class="h5c_icon" />
                        {{T . "New File"}}
                    </a>
                </li>
                <li>
                    <a href="#proj/fs/file-new-dir" onclick="lcProjectFs.FileNew('dir', null, '')">
                        <img src="/lesscreator/~/lesscreator/img/folder_add.png" class="h5c_icon" />
                        {{T . "New Folder"}}
                    </a>
                </li>
                <li>
                    <a href="#proj/fs/file-upl" onclick="lcProjectFs.FileUpload(null)">
                        <img src="/lesscreator/~/lesscreator/img/page_white_get.png" class="h5c_icon" />
                        {{T . "Upload"}}
                    </a>
                </li>
            </ul>
        </li>

        <li class="lfnm-item">
            <a href="#fs/file-upl" 
                onclick="_fs_tree_dir('', 1)" 
                class="icon-refresh icon-white lfnm-item-ico" title="Refresh">
            </a>
        </li>
    </ul>
</div>


<!-- Project Files Tree -->
<div id="lcbind-fsnav-fstree" class="less_scroll">
    <div id="fstdroot" class="lcx-fstree">loading</div>
</div>


<!--- TPL: File Item -->
<div id="lcx-filenav-tree-tpl" class="hide">
{[~it :v]}
<div id="ptp{[=v.fsid]}" class="lcx-fsitem" 
  lc-fspath="{[=v.path]}" lc-fstype="{[=v.fstype]}" lc-fsico="{[=v.ico]}">
    <img src="/lesscreator/~/lesscreator/img/{[=v.ico]}.png" align="absmiddle">
    <a href="#" class="anoline">{[=v.name]}</a>
</div>
{[~]}
</div>


<!--- TPL: File Right Click Menu -->
<div id="lcbind-fsnav-rcm" class="hide">
  
  <div class="lcbind-fsrcm-item fsrcm-isdir" lc-fsnav="new-file">
    <div class="rcico">
        <img src="/lesscreator/~/lesscreator/img/page_white_add.png" align="absmiddle" />
    </div>
    <a href="#" class="rcctn">{{T . "New File"}}</a>
  </div>
  <div class="lcbind-fsrcm-item fsrcm-isdir" lc-fsnav="new-dir">
    <div class="rcico">
        <img src="/lesscreator/~/lesscreator/img/folder_add.png" align="absmiddle">
    </div>
    <a href="#" class="rcctn">{{T . "New Folder"}}</a>
  </div>
  <div class="lcbind-fsrcm-item fsrcm-isdir" lc-fsnav="upload">
    <div class="rcico">
        <img src="/lesscreator/~/lesscreator/img/page_white_get.png" align="absmiddle">
    </div>
    <a href="#" class="rcctn">{{T . "Upload"}}</a>
  </div>

  <div class="rcm-sepline fsrcm-isdir"></div>

  <div class="lcbind-fsrcm-item" lc-fsnav="rename">
    <div class="rcico">
        <img src="/lesscreator/~/lesscreator/img/page_white_copy.png" align="absmiddle">
    </div>
    <a href="#" class="rcctn">{{T . "Rename"}}</a>
  </div>
  <div class="lcbind-fsrcm-item" lc-fsnav="file-del">
    <div class="rcico">
        <img src="/lesscreator/~/lesscreator/img/delete.png" align="absmiddle">
    </div>
    <a href="#" class="rcctn">{{T . "Delete"}}</a>
  </div>
</div>


<!-- TPL : File New -->
<div id="lcbind-fstpl-filenew" class="hide"> 
<form id="{[=it.formid]}" action="#" onsubmit="lcProjectFs.FileNewSave('{[=it.formid]}');return false;">
  <div class="input-prepend" style="margin-left:2px">
    <span class="add-on">
        <img src="/lesscreator/~/lesscreator/img/folder_add.png" class="h5c_icon">
        {[=it.path]}/
    </span>
    <input type="text" name="name" value="{[=it.file]}" class="span2">
    <input type="hidden" name="path" value="{[=it.path]}">
    <input type="hidden" name="type" value="{[=it.type]}">
  </div>
</form>
</div>


<!-- TPL : File Rename -->
<div id="lcbind-fstpl-filerename" class="hide"> 
<form id="{[=it.formid]}" action="#" onsubmit="lcProjectFs.FileRenameSave('{[=it.formid]}');return false;">
  <div class="input-prepend" style="margin-left:2px">
    <span class="add-on">
        <img src="/lesscreator/~/lesscreator/img/folder_edit.png" class="h5c_icon">
    </span>
    <input type="text" name="pathset" value="{[=it.path]}" style="width:500px;">
    <input type="hidden" name="path" value="{[=it.path]}">
  </div>
</form>
</div>


<!-- TPL : File Delete -->
<div id="lcbind-fstpl-filedel" class="hide"> 
<form id="{[=it.formid]}" action="#" onsubmit="lcProjectFs.FileDelSave('{[=it.formid]}');return false;">
  <input type="hidden" name="path" value="{[=it.path]}">
  <div class="alert alert-danger" role="alert">
    <p>Are you sure to delete this file or folder?</p>
    <p><strong>{[=it.path]}</strong><p>
  </div>
</form>
</div>


<!-- TPL : File Upload -->
<style type="text/css">
.lsarea {
    margin: 0;
    display: inline-block;
    height: 160px;
    width: 100%;
    color: #333;
    font-size: 18px;
    padding: 10px;
    border: 3px dashed #5cb85c;
    border-radius: 10px;
    text-align: center;
    vertical-align: middle;
    -webkit-box-sizing: border-box;
       -moz-box-sizing: border-box;
            box-sizing: border-box;
}
</style>
<div id="lcbind-fstpl-fileupload" class="hide">
<div id="{[=it.reqid]}">
  <div>{{T . "The target of Upload directory"}}</div>
  <div class="input-prepend">
    <span class="add-on"><img src="/lesscreator/~/lesscreator/img/page_white_get.png" align="absmiddle"></span>
    <input style="width:400px;" name="path" type="text" value="{[=it.path]}">
    <button class="btn hide" type="button" onclick="_fs_upl_chgdir()">{{T . "Change directory"}}</button>
  </div>
  <div id="{[=it.areaid]}" class="lsarea">
    {{T . "Drag and Drop your files or folders to here"}}
  </div>
  <div class="alert alert-info hide lsstate" style="width:430px;"></div>
</div>
</div>
