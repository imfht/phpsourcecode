<?php
include("../config/config.php");
include("include/function.php");
if (!Login_Status()) {
    header("Location:login.php");
    exit;
}
?>
<!DOCTYPE HTML>
<html>
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Video Encode Server</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <a class="navbar-brand" href="#">
        Video Encode System
    </a>
    <span class="badge badge-secondary">V&nbsp;<?php echo $version;?></span>&nbsp;
    <span class="badge badge-danger">Dev</span>&nbsp;
    <span class="badge badge-primary">Windows</span>&nbsp;
    <span class="badge badge-success">Open Source Project</span>&nbsp;
    <span class="badge badge-info">Build:<?php echo $build;?></span>
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav mr-auto">

        </ul>
        <!--
        <div class="form-inline my-2 my-lg-0">
            &nbsp;&nbsp;&nbsp;&nbsp;
            <a class="btn btn-success" href="studio/index.php" target="_blank">&nbsp;Go to Studio&nbsp;</a>
        </div>-->
    </div>
</nav>
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-2 col-md-3">
            <div class="list-group">
                <a id="dashboard" href="dashboard.php" target="main_page" class="list-group-item list-group-item-action active" onclick="Change_Tab('dashboard')">Dashboard</a>
                <a id="video_list" href="video_list.php" target="main_page" class="list-group-item list-group-item-action" onclick="Change_Tab('video_list')">Video List</a>
                <a id="config" href="config.php" target="main_page" class="list-group-item list-group-item-action" onclick="Change_Tab('config')">Config</a>
            </div>
        </div>
        <div class="col-lg-10 col-md-9">
            <iframe id="main_page" name="main_page" frameborder="0" width="100%" src="dashboard.php"></iframe>
        </div>
    </div>
</div>
</body>
<script src="js/PackEngine.js"></script>
<script src="js/jquery-3.3.1.min.js"></script>
<script src="js/popper.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script>
    var now_tab='dashboard';
    set_frame();
    window.onresize = function () {
        var height = window.innerHeight - 65;
        document.getElementById('main_page').setAttribute('height', height + 'px');
    };

    function set_frame() {
        var height = window.innerHeight - 65;
        document.getElementById('main_page').setAttribute('height', height + 'px');
    }
    function Change_Tab(name) {
        document.getElementById(now_tab).setAttribute('class','list-group-item list-group-item-action');
        document.getElementById(name).setAttribute('class','list-group-item list-group-item-action active');
        now_tab=name;
    }
</script>
</html>