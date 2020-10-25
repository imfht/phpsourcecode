<?php

use LessPHP\User\Session;

// if (!Session::IsLogin()) {
//     header('Location: /user');
// }

$lcinfo = file_get_contents(LESSCREATOR_DIR ."/lcproject.json");
$lcinfo = json_decode($lcinfo, true);

//setcookie("runtime_mode", 'dev', time()+36000000000, '/');
//print_r($lcinfo);
?><!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title><?php echo $this->T('lessCreator')?></title>
  <script src="/lesscreator/~/lessui/js/sea.js"></script>
  <script src="/lesscreator/static/js/jquery.js"></script>
  <script src="/lesscreator/~/lessui/js/BrowserDetect.js"></script>

  <script src="/lesscreator/~/lessui/js/lessui.js?v=<?php echo $lcinfo['version']?>"></script>
  <script src="/lesscreator/static/js/c.js?v=<?php echo $lcinfo['version']?>"></script>
  <script src="/lesscreator/static/js/gen.js?v=<?php echo $lcinfo['version']?>"></script>
  <script src="/lesscreator/static/js/genx.js?v=<?php echo $lcinfo['version']?>"></script>
  <script src="/lesscreator/static/js/editor.js?v=<?php echo $lcinfo['version']?>"></script>
  <script src="/lesscreator/~/codemirror3/3.21.0/codemirror.min.js"></script>

  <link href="/lesscreator/~/bootstrap2/css/bootstrap.min.css" rel="stylesheet" />
  
  <?php
  if (isset($_COOKIE['runtime_mode']) && $_COOKIE['runtime_mode'] == "dev") {
    echo '<link href="/lesscreator/~/lessui/less/lessui.less" rel="stylesheet/less" />';
    echo '<link href="/lesscreator/static/less/defx.less" rel="stylesheet/less" />';
    echo '<script src="/lessui/less/less.min.js"></script>';
  } else {
    echo "<link href=\"/lesscreator/~/lessui/css/lessui.min.css?v={$lcinfo['version']}\" rel=\"stylesheet\" />";
    echo "<link href=\"/lesscreator/static/css/defx.css?v={$lcinfo['version']}\" rel=\"stylesheet\" />";
  }
  ?>  

  <link href="/lesscreator/static/css/def.css?v=<?php echo $lcinfo['version']?>" rel="stylesheet" />
  <link href="/lesscreator/static/img/favicon.ico" rel="shortcut icon" type="image/x-icon" />

</head>
<body>


<div class="loadwell">
  <div class="">
    <div id="_load-alert" class="alert alert-success">
        <?php echo $this->T('Initializing System Environment')?> ...</div>    
  </div>

  <div class="load-progress-msg"><?php echo $this->T('Loading dependencies')?> ...</div>
  <div class="load-progress progress progress-success">
    <div class="bar load-progress-num" style="width: 1%"></div>
  </div>
</div>


</body>
</html>


<script>


var _load_sleep = 50;

function _lc_loadwell_resize()
{
    var bh = $('body').height();
    var bw = $('body').width();

    if (bh < 300) {
        bh = 300;
    }
    if (bw < 600) {
        bw = 600;
    }

    var eh = $('.loadwell').height();
    var ew = $('.loadwell').width();

    $('.loadwell').css({
        "top" : ((bh - eh) / 3) + "px",
        "left": ((bw - ew) / 2) + "px"
    });
}

$(document).ready(function() {

    var browser = BrowserDetect.browser;
    var version = BrowserDetect.version;
    var OS      = BrowserDetect.OS;
    if (!(browser == 'Chrome' && version >= 22)) { 
        $('body').css({
            width: '100%',
            height: '100%',
            'min-height': '100px',
            'min-width': '400px',
            'background': '#eee'
        });
        $('body').load('/lesscreator/err/browser');
        return;
    }

    _lc_loadwell_resize();

    setTimeout(_load_deps, _load_sleep);
});

function _load_deps()
{
    var rqs = [
        "/lesscreator/~/bootstrap2/js/bootstrap.min.js",
        "/lesscreator/~/codemirror3/3.21.0/codemirror.min.css",

        "/lesscreator/~/codemirror3/3.21.0/addon/hint/show-hint.min.css",

        "/lesscreator/~/codemirror3/3.21.0/addon/mode/loadmode.min.js",
        "/lesscreator/~/codemirror3/3.21.0/addon/search/searchcursor.min.js",
        "/lesscreator/~/codemirror3/3.21.0/keymap/vim.min.js",
        "/lesscreator/~/codemirror3/3.21.0/keymap/emacs.min.js",
        "/lesscreator/~/codemirror3/3.21.0/addon/fold/foldcode.min.js",
        "/lesscreator/~/codemirror3/3.21.0/addon/fold/foldgutter.min.js",
        "/lesscreator/~/codemirror3/3.21.0/addon/fold/brace-fold.min.js",
        "/lesscreator/~/codemirror3/3.21.0/addon/hint/show-hint.min.js",
        "/lesscreator/~/codemirror3/3.21.0/addon/hint/javascript-hint.min.js",
        "/lesscreator/~/codemirror3/3.21.0/mode/all.min.js",
        "/lesscreator/~/codemirror3/3.21.0/addon/dialog/dialog.min.js",
        "/lesscreator/~/codemirror3/3.21.0/addon/dialog/dialog.min.css",

        "/lesscreator/static/js/term.js?v=<?php echo $lcinfo['version']?>",
    ];

    seajs.use(rqs, function() {
        $(".load-progress-num").css({"width": "90%"});
        setTimeout(_load_sys_config, _load_sleep);
    });
}

var _load_desk_once = 0;
function _load_sys_config()
{
    $(".load-progress-msg").append("<br />Loading settings ...");

    var req = {
        access_token: lessCookie.Get("access_token"),
    }

    var url = "/lesscreator/api?func=env-init&_="+ Math.random();
    
    $.ajax({
        url     : url,
        type    : "POST",
        timeout : 30000,
        data    : JSON.stringify(req),
        async   : false,
        success : function(rsp) {

            //console.log(rsp);
            try {
                var rsj = JSON.parse(rsp);
            } catch (e) {
                $(".load-progress").removeClass("progress-success").addClass("progress-danger");
                lessAlert("#_load-alert", "alert-error", "<?php echo $this->T('Service Unavailable')?> ("+url+")");
                return;
            }

            if (rsj.status == 401) {
                $(".load-progress").removeClass("progress-success").addClass("progress-danger");
                lessAlert("#_load-alert", "alert-error", "<?php echo $this->T('Unauthorized')?>, <a href='/user'><?php echo $this->T('try login again')?></a>");
            } else if (rsj.status == 200) {

                if (rsj.data.basedir != lessSession.Get("basedir")) {
                    lessSession.Del("basedir");
                    lessSession.Del("ProjPath");
                }

                lessSession.Set("basedir", rsj.data.basedir);
                lessCookie.Set("basedir", rsj.data.basedir, 0);
                lessSession.Set("SessUser", rsj.data.user);

                lcData.Init(rsj.data.user, function(ret) {
                    
                    if (!ret) {
                        return lessAlert("#_load-alert", "alert-error", 
                            "<?php echo $this->T('Local database (IndexedDB) initialization failed')?>");
                    }

                    _load_desk_once++;

                    _load_desk(rsj.data.basedir);
                });                             

            } else {
                $(".load-progress").removeClass("progress-success").addClass("progress-danger");
                lessAlert("#_load-alert", "alert-error", rsj.message);
            }
        },
        error: function(xhr, textStatus, error) {
            $(".load-progress").removeClass("progress-success").addClass("progress-danger");
            lessAlert("#_load-alert", "alert-error", "<?php echo $this->T('Service Unavailable')?>");
        }
    });
}

function _load_desk(basedir)
{
    if (_load_desk_once > 1) {
        return;
    }

    $.ajax({
        url     : "/lesscreator/desk-design?basedir="+ basedir +"&_="+ Math.random(),
        type    : "GET",
        timeout : 30000,
        success : function(rsp) {

            $(".load-progress-num").css({"width": "100%"});

            setTimeout(function() {
                $('body').html(rsp);
                _env_init();
            }, _load_sleep);
        },
        error: function(xhr, textStatus, error) {
            $(".load-progress").removeClass("progress-success").addClass("progress-danger");
            lessAlert("#_load-alert", "alert-error", "<?php echo $this->T('Failed on Initializing System Environment')?>");
        }
    });
}


function _env_init()
{   
    lcInitSetting();

    window.onbeforeunload = function() {
        lcEditor.HookOnBeforeUnload();
        //lessLocalStorage.Set(lessSession.Get("SessUser") +".lastproj", proj);
        //return "Leave the page and lose your changes?";
    }

    $(window).resize(function() {
        lcLayoutResize();
    });

    var spacecol = 10;

    $("#h5c-lyo-col-w-ctrl").bind('mousedown', function() {
        
        $("#hdev_layout").mousemove(function(e) {

            var w = $('body').width() - (3 * spacecol);
            //var p = $('#h5c-lyo-col-t').position();
            var p = $('#lcx-start-lyo').position();
            var wrs = e.pageX - p.left - 5;

            if (w * (1 - (wrs / w)) < 400) {
                return;
            }

            lessLocalStorage.Set("lcLyoLeftW", wrs / w);
            lessSession.Set("lcLyoLeftW", wrs / w);

            lcLayoutResize();
        });
    });

    $("#h5c-resize-roww0").bind('mousedown', function() {
        
        $("#hdev_layout").mousemove(function(e) {
            
            var h = $('#hdev_layout').height() - spacecol;
            var p = $('#h5c-tablet-framew0').position();
            var hrs = e.pageY - p.top - 5;
           
            if (hrs < 0) {
                hrs = 0;
            }

            lessLocalStorage.Set("lcLyoCtn0H", hrs / h);
            lessSession.Set("lcLyoCtn0H", hrs / h);

            lcLayoutResize();
        });
    });
    

    $(document).bind('selectstart',function() {return false;});
    $(document).bind('mouseup', function() {
        $("#hdev_layout").unbind('mousemove');
    });

    <?php
    echo "h5cProjectOpen('{$this->req->proj}');";
    ?>
    
    lcLayoutResize();
    setTimeout(lcLayoutResize, 3000);

}
</script>