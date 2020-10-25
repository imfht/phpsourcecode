<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <title>{{T . "lessCreator"}}</title>
  <link href="/lesscreator/~/lesscreator/img/favicon.ico" rel="shortcut icon" type="image/x-icon" />
  <script src="/lesscreator/~/lessui/js/sea.js"></script>
  <script src="/lesscreator/~/lesscreator/js/main.js"></script>
  
  <link href="/lesscreator/~/lessui/less/lessui.less" rel="stylesheet/less" />
  <link href="/lesscreator/~/lesscreator/less/defx.less" rel="stylesheet/less" />
  <script src="/lesscreator/~/lessui/less/less.min.js"></script>

  <script type="text/javascript">
    var lessfly_api = "{{.lessfly_api}}";
    window.onload = lc.Boot;
  </script>
</head>
<body>
<div id="body-content">
    
    <style>
    .loading {
      margin: 0;
      padding: 30px 40px;
      font-size: 24px;
      color: #000;
    }
    </style>
    
    <div class="loading">loading ...</div>

    <div class="lcx-loadwell" style="display:none">
        <div class="">
            <div id="_load-alert" class="alert alert-success">
                {{T . "Initializing System Environment"}} ...</div>    
        </div>

        <div class="load-progress-msg">{{T . "Loading dependencies"}} ... </div>
        <div class="load-progress progress progress-success">
            <div class="bar load-progress-num" style="width: 1%"></div>
        </div>
    </div>

</div>

</body>
</html>


<script>

// v2
var _load_box_once = 0;
function _load_box_config()
{
    $(".load-progress-msg").append("<br />Loading settings ...");

    var req = {
        access_token: lessCookie.Get("access_token"),
    }

    $.ajax({
        url     : "/lesscreator/index/desk?basedir=&_="+ Math.random(),
        type    : "GET",
        timeout : 30000,
        success : function(rsp) {

            $(".load-progress-num").css({"width": "100%"});

            setTimeout(function() {
                $('body').html(rsp);
                //_env_init();
            }, _load_sleep);
        },
        error: function(xhr, textStatus, error) {
            $(".load-progress").removeClass("progress-success").addClass("progress-danger");
            lessAlert("#_load-alert", "alert-error", "{{T . "Failed on Initializing System Environment"}}");
        }
    });

    return;

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
                lessAlert("#_load-alert", "alert-error", "{{T . "Service Unavailable"}} ("+url+")");
                return;
            }

            if (rsj.status == 401) {
                $(".load-progress").removeClass("progress-success").addClass("progress-danger");
                lessAlert("#_load-alert", "alert-error", "{{T . "Unauthorized"}}, <a href='/user'>{{T . "try login again"}}</a>");
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
                            "{{T . "Local database (IndexedDB) initialization failed"}}");
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
            lessAlert("#_load-alert", "alert-error", "{{T . "Service Unavailable"}}");
        }
    });

    _load_box_once++;
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
                lessAlert("#_load-alert", "alert-error", "{{T . "Service Unavailable"}} ("+url+")");
                return;
            }

            if (rsj.status == 401) {
                $(".load-progress").removeClass("progress-success").addClass("progress-danger");
                lessAlert("#_load-alert", "alert-error", "{{T . "Unauthorized"}}, <a href='/user'>{{T . "try login again"}}</a>");
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
                            "{{T . "Local database (IndexedDB) initialization failed"}}");
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
            lessAlert("#_load-alert", "alert-error", "{{T . "Service Unavailable"}}");
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
            lessAlert("#_load-alert", "alert-error", "{{T . "Failed on Initializing System Environment"}}");
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
        lcLayout.Resize();
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

            lcLayout.Resize();
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

            lcLayout.Resize();
        });
    });
    

    $(document).bind('selectstart',function() {return false;});
    $(document).bind('mouseup', function() {
        $("#hdev_layout").unbind('mousemove');
    });

    h5cProjectOpen('{{.proj}}');
        
    lcLayout.Resize();
    setTimeout(lcLayout.Resize, 3000);
}
</script>
