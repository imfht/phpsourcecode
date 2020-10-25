<?php

if ($this->req->proj == null) {
    die($this->T('Internal Error'));
}
$proj = preg_replace("/\/+/", "/", rtrim($this->req->proj,'/'));
$projPath = lesscreator_proj::path($proj);
if (strlen($projPath) < 1) {
    die($this->T('Internal Error'));
}

$path = preg_replace("/\/+/", "/", $this->req->path);

$rs = lesscreator_fs::FsFileGet($projPath ."/lcproject.json");
if ($rs->status != 200) {
    die($this->T('Internal Error'));
}

?>

<div>

<?php
$glob = preg_replace(array("/\.+/", "/\/+/"), array(".", "/"), "{$projPath}/{$path}/*");

$prt = $prt0 = '';

$srvall = lesscreator_service::listAll();
$ls = lesscreator_fs::FsList($glob);
//echo "<pre>";
//print_r($ls);
//echo "</pre>";
foreach ($ls->data as $f) {

    $fn = $f->name;

    if (in_array($fn, array(".git"))) {
        continue;
    }

    if (strlen($path) < 1 && isset($srvall[$fn])) {
        continue;
    }

    $fs = 0;
    $fm = $f->mime;

    if ($f->isdir != 1) {
        
        $fs = $f->size;

        if ($fm == 'application/octet-stream' && $fs < (10 * 1024 * 1024)) { // < 10MB
            if (is_string($_s->data->body)) {
                $fm = 'text/plain';
            }
        }
    }
    
    $fmi = 'page_white';
    $href = null;

    $p = trim("{$path}/$fn", '/');
    $p = preg_replace("/\/+/", "/", $p);
    $pdiv = md5($p);
    //echo $fm;
    //$p = urlencode($p);
    if ($f->isdir == 1) {
        
        if ($fn == 'pagelet') {
            $fmi = 'layers';
            $fn = 'Pagelet Engine';
        } else if ($fn == 'dataflow') {
            $fmi = 'database_refresh';
            $fn = 'Data Flow Engine';
        } else {
            $fmi = 'folder';
        }
        
        $href   = "javascript:_fs_tree_dir('{$p}', 0)";
        
    } else if (substr($fm, 0, 4) == "text"
        || substr($fn, -4) == ".tpl"
        || substr($fm, 0, 23) == "application/x-httpd-php"
        || $fm == "application/javascript"
        || $fm == "application/x-empty"
        || $fm == "inode/x-empty"
        || $fm == "application/json") {
        
        if (strlen($path) == 0 && $fn == 'lcproject.json') {
            $fmi = 'app-t3-16';
        } else if ($fm == 'text/x-php' || substr($fn, -4) == '.php') {
            $fmi = 'page_white_php';
        } else if (substr($fn, -2) == '.h' || substr($fn, -4) == '.hpp') {
            $fmi = 'page_white_h';
        } else if (substr($fn, -2) == '.c') {
            $fmi = 'page_white_c';
        } else if (substr($fn, -4) == '.cpp' || substr($fn, -3) == '.cc') {
            $fmi = 'page_white_cplusplus';
        } else if (substr($fn, -3) == '.js' || substr($fn, -4) == '.css') {
            $fmi = 'page_white_code';
        } else if (substr($fn, -5) == '.html' 
            || substr($fn, -4) == '.htm' 
            || substr($fn, -6) == '.phtml'
            || substr($fn, -6) == '.xhtml'
            || substr($fn, -4) == '.tpl') {
            $fmi = 'page_white_world';
        } else if (substr($fn, -3) == '.sh' || $fm == 'text/x-shellscript') {
            $fmi = 'application_osx_terminal';
        } else if (substr($fn, -3) == '.rb') {
            $fmi = 'page_white_ruby';
        } else if (substr($fn, -3) == '.go') {
            $fmi = 'ht-page_white_golang';
        } else if (substr($fn, -3) == '.py' 
            || substr($fn, -4) == '.yml'
            || substr($fn, -5) == '.yaml'
            ) {
            $fmi = 'page_white_code';
        }
        
        //$href = "javascript:hdev_page_open('{$p}','editor','','{$fmi}')";
        $href = "javascript:h5cTabOpen('{$p}','w0','editor',{'img':'{$fmi}', 'close':'1'})";
       
    } else if (substr($fm, 0, 5) == 'image') {
        $fmi = 'page_white_picture';
    }
    
    $li  = "<div id=\"ptp{$pdiv}\" class=\"hdev-proj-tree fileitem\">";
    $li .= "<img src='/lesscreator/static/img/{$fmi}.png' align='absmiddle' title='{$fm}' /> ";
    $li .= ($href === null) ? $fn : "<a href=\"{$href}\" class=\"anoline\">{$fn}</a>";
    
    $lip = "";
    
    if ($f->isdir == 1) {
        $lip .= "<div class='rcitem'>
            <div class='rcico'><img src='/lesscreator/static/img/page_white_add.png' align='absmiddle' /></div>
            <a href='#{$p}' class='rcctn hdev_rcobj_file'>".$this->T('New File')."</a></div>";
        $lip .= "<div class='rcitem'>
            <div class='rcico'><img src='/lesscreator/static/img/folder_add.png' align='absmiddle' /></div>
            <a href='#{$p}' class='rcctn hdev_rcobj_dir'>".$this->T('New Folder')."</a></div>";
        $lip .= "<div class='rcitem'>
            <div class='rcico'><img src='/lesscreator/static/img/page_white_get.png' align='absmiddle' /></div>
            <a href='#{$p}' class='rcctn hdev_rcobj_upload'>".$this->T('Upload')."</a></div>";
    }
    
    if (strlen($path) != 0 || $fn != 'lcproject.json') {
        
        $lip .= "<div class='rcitem'>
            <div class='rcico'><img src='/lesscreator/static/img/page_white_copy.png' align='absmiddle' /></div>
            <a href='#{$p}' class='rcctn hdev_rcobj_rename'>".$this->T('Rename')."</a></div>";
        
        if (strlen($lip)) {
            $lip .= "<div class=\"rcsepli\"></div>";
        }
        $lip .= "<div class='rcitem'>
            <div class='rcico'><img src='/lesscreator/static/img/delete.png' align='absmiddle' /></div>
            <a href=\"javascript:_fs_file_del('{$p}');\" onclick=\"return confirm('".$this->T('Are you sure you want to delete')."')\" class='rcctn'>".$this->T('Delete')."</a></div>";
    }
    
    if (strlen($lip)) {
        $li .= "<div class=\"hdev-rcmenu displaynone\">".$lip."</div>";
    }

    //$li .= "<div class='pull-right gray'>".date("Y-m-d H:i:s", strtotime($f->modtime))."</div>";
    
    $li .= "</div>";
    $li .= "<div id=\"pt{$pdiv}\" style=\"padding-left:20px;\"></div>";
    
    if ($fn == 'lcproject.json') {
        continue;
        $prt0 = $li; // TODO
    } else {
        $prt .= $li;
    }
}
echo $prt0 . $prt;
?>
</div>
<script>
_fs_tree_refresh();
</script>
