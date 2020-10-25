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
<div class="container-fluid">
    <ul class="nav nav-tabs" id="myTab" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" id="encode_config" data-toggle="tab" href="#encode_config_tab" role="tab"
               aria-controls="encode_config" aria-selected="true">Encode Config</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="api_config" data-toggle="tab" href="#api_config_tab" role="tab"
               aria-controls="api_config" aria-selected="false">API Config</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="video_service" data-toggle="tab" href="#video_service_tab" role="tab"
               aria-controls="video_service" aria-selected="false">Video Service</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="screen_shot" data-toggle="tab" href="#screen_shot_tab" role="tab"
               aria-controls="screen_shot" aria-selected="false">ScreenShot</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="delogo_config" data-toggle="tab" href="#delogo_config_tab" role="tab"
               aria-controls="delogo_config" aria-selected="false">DELogo Config</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="nginx_config" data-toggle="tab" href="#nginx_config_tab" role="tab"
               aria-controls="nginx_config" aria-selected="false">Nginx Config</a>
        </li>
    </ul>
    <div class="tab-content" id="myTabContent">
        <div class="tab-pane fade show active" id="encode_config_tab" role="tabpanel" aria-labelledby="encode_config">
            <div class="card">
                <div class="card-header">
                    Encode Config
                </div>
                <div class="card-body">
                    <div class="alert alert-info"><strong>.ts File Encryption</strong> is Default Enable in This
                        Version.
                    </div>
                    <div id="alert_encode"></div>
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label" for="encode_bitrate_video">Video Bitrate</label>
                        <div class="input-group col-sm-10">
                            <input class="form-control" id="encode_bitrate_video" placeholder="Video Bitrate"
                                   value="<?php echo Get_Config('encode_bitrate_video'); ?>">
                            <div class="input-group-append">
                                <span class="input-group-text">Kbps</span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label" for="encode_bitrate_video">Audio Bitrate</label>
                        <div class="input-group col-sm-10">
                            <input class="form-control" id="encode_bitrate_audio" placeholder="Audio Bitrate"
                                   value="<?php echo Get_Config('encode_bitrate_audio'); ?>">
                            <div class="input-group-append">
                                <span class="input-group-text">Kbps</span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label" for="encode_ts_time">Time For .ts File</label>
                        <div class="input-group col-sm-10">
                            <input class="form-control" id="encode_ts_time" placeholder="Time"
                                   value="<?php echo Get_Config('encode_ts_time'); ?>">
                            <div class="input-group-append">
                                <span class="input-group-text">Seconds</span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label" for="encode_ts_frame">Frame For .ts File</label>
                        <div class="input-group col-sm-10">
                            <input class="form-control" id="encode_ts_frame" placeholder="Frame"
                                   value="<?php echo Get_Config('encode_ts_frame'); ?>">
                            <div class="input-group-append">
                                <span class="input-group-text">Frames</span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label" for="encode_res">Resolution</label>
                        <div class="input-group col-sm-10">
                            <input class="form-control" id="encode_res"
                                   placeholder="X:Y Like 1280:720 -2:720"
                                   value="<?php echo Get_Config('encode_res'); ?>">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label" for="encode_framerate">FrameRate</label>
                        <div class="input-group col-sm-10">
                            <input class="form-control" id="encode_framerate" placeholder="Frame/Sec"
                                   value="<?php echo Get_Config('encode_framerate'); ?>">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label" for="worker_thread">Encode Thread(s)</label>
                        <div class="col-sm-10">
                            <input class="form-control" id="worker_thread" placeholder="Thread"
                                   value="<?php echo Get_Config('worker_thread'); ?>">
                        </div>
                    </div>
                    <div align="right">
                        <button class="btn btn-outline-success btn-lg" onclick="Update_Config_Encode()">Submit</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="tab-pane fade" id="api_config_tab" role="tabpanel" aria-labelledby="api_config">
            <div class="card">
                <div class="card-header">
                    API Config
                </div>
                <div class="card-body">
                    <div class="alert alert-danger"><strong>WARNING:</strong>This version's API not design for high
                        performance
                        application.
                    </div>
                    <div id="alert_api"></div>
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label" for="api_key">API Key</label>
                        <div class="input-group col-sm-10">
                            <input class="form-control" id="api_key" placeholder="API Key"
                                   value="<?php echo Get_Config('api_key'); ?>">
                            <div class="input-group-append">
                                <button class="btn btn-primary" type="button" onclick="Random_API_Key(32)">Random
                                </button>
                            </div>
                        </div>
                    </div>
                    <div align="right">
                        <button class="btn btn-outline-success btn-lg" onclick="Update_API_Encode()">Submit</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="tab-pane fade" id="video_service_tab" role="tabpanel" aria-labelledby="video_service">
            <div class="card">
                <div class="card-header">
                    Video Service Config
                </div>
                <div class="card-body">
                    <div class="alert alert-danger"><strong>WARNING:</strong>Update this part will restart Nginx
                        service.
                    </div>
                    <div id="alert_video_service"></div>
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label" for="video_port">Port</label>
                        <div class="col-sm-10">
                            <input class="form-control" id="video_port" placeholder="Port"
                                   value="<?php echo Get_Config('video_port'); ?>">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label" for="video_domain">Video Domain</label>
                        <div class="col-sm-10">
                            <input class="form-control" id="video_domain" placeholder="Domain"
                                   value="<?php echo Get_Config('video_domain'); ?>">
                        </div>
                    </div>
                    <?php
                    $ps = Get_Config('play_secure');
                    ?>
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label" for="play_secure">Video Secure</label>
                        <div class="btn-group col-sm-10 btn-group-toggle" data-toggle="buttons">
                            <label class="btn btn-primary <?php if ($ps == 1) {
                                echo "active";
                            } ?>" onclick="play_secure=1;">
                                <input type="radio" name="play_secure" id="option1"
                                       autocomplete="off" <?php if ($ps == 1) {
                                    echo "checked";
                                } ?>> On
                            </label>
                            <label class="btn btn-primary <?php if ($ps == 0) {
                                echo "active";
                            } ?>" onclick="play_secure=0;">
                                <input type="radio" name="play_secure" id="option2"
                                       autocomplete="off" <?php if ($ps == 0) {
                                    echo "checked";
                                } ?>> Off
                            </label>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label" for="allow_domain">Allow Domain</label>
                        <div class="col-sm-10">
                            <input class="form-control" id="allow_domain" placeholder="Domain(use , separate)"
                                   value="<?php echo Get_Config('allow_domain'); ?>">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label" for="jump_link">Jump Link</label>
                        <div class="col-sm-10">
                            <input class="form-control" id="jump_link" placeholder="Link(http:// or https://)"
                                   value="<?php echo Get_Config('jump_link'); ?>">
                        </div>
                    </div>
                    <div align="right">
                        <button class="btn btn-outline-success btn-lg" onclick="Update_Video_Service()">Submit</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="tab-pane fade" id="screen_shot_tab" role="tabpanel" aria-labelledby="screen_shot">
            <div id="alert_sc"></div>
            <div class="card">
                <div class="card-header">
                    JPEG Setting
                </div>
                <div class="card-body">
                    <div class="form-group row">
                        <?php
                        $sc_jpeg = Get_Config('sc_jpeg');
                        ?>
                        <label class="col-sm-2 col-form-label" for="sc_jpeg">ScreenShot</label>
                        <div class="btn-group col-sm-10 btn-group-toggle" data-toggle="buttons">
                            <label class="btn btn-primary <?php if ($sc_jpeg == 1) {
                                echo "active";
                            } ?>" onclick="sc_jpeg=1;">
                                <input type="radio" name="sc_jpeg" id="option1"
                                       autocomplete="off" <?php if ($sc_jpeg == 1) {
                                    echo "checked";
                                } ?>> On
                            </label>
                            <label class="btn btn-primary <?php if ($sc_jpeg == 0) {
                                echo "active";
                            } ?>" onclick="sc_jpeg=0;">
                                <input type="radio" name="sc_jpeg" id="option2"
                                       autocomplete="off" <?php if ($sc_jpeg == 0) {
                                    echo "checked";
                                } ?>> Off
                            </label>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label" for="sc_jpeg_start_time">Start Time</label>
                        <div class="col-sm-10">
                            <input class="form-control" id="sc_jpeg_start_time" placeholder="XX:XX(Like 10:02)"
                                   value="<?php echo Get_Config('sc_jpeg_start_time'); ?>">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label" for="sc_jpeg_number">Number of Shots</label>
                        <div class="col-sm-10">
                            <input class="form-control" id="sc_jpeg_number" placeholder="Number"
                                   value="<?php echo Get_Config('sc_jpeg_number'); ?>">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label" for="sc_jpeg_int">Shot Interval</label>
                        <div class="col-sm-10">
                            <input class="form-control" id="sc_jpeg_int" placeholder="Second(s)"
                                   value="<?php echo Get_Config('sc_jpeg_int'); ?>">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label" for="sc_jpeg_res">Resolution</label>
                        <div class="col-sm-10">
                            <input class="form-control" id="sc_jpeg_res"
                                   placeholder="XxY(Like 320x240) Set 0 will auto fit"
                                   value="<?php echo Get_Config('sc_jpeg_res'); ?>">
                        </div>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-header">
                    GIF Setting
                </div>
                <div class="card-body">
                    <div class="form-group row">
                        <?php
                        $sc_gif = Get_Config('sc_gif');
                        ?>
                        <label class="col-sm-2 col-form-label" for="sc_gif">ScreenShot</label>
                        <div class="btn-group col-sm-10 btn-group-toggle" data-toggle="buttons">
                            <label class="btn btn-primary <?php if ($sc_gif == 1) {
                                echo "active";
                            } ?>" onclick="sc_gif=1;">
                                <input type="radio" name="sc_gif" id="option1"
                                       autocomplete="off" <?php if ($sc_gif == 1) {
                                    echo "checked";
                                } ?>> On
                            </label>
                            <label class="btn btn-primary <?php if ($sc_gif == 0) {
                                echo "active";
                            } ?>" onclick="sc_gif=0;">
                                <input type="radio" name="sc_gif" id="option2"
                                       autocomplete="off" <?php if ($sc_gif == 0) {
                                    echo "checked";
                                } ?>> Off
                            </label>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label" for="sc_gif_start_time">Start Time</label>
                        <div class="col-sm-10">
                            <input class="form-control" id="sc_gif_start_time" placeholder="XX:XX(Like 10:02)"
                                   value="<?php echo Get_Config('sc_gif_start_time'); ?>">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label" for="sc_gif_time">GIF Time</label>
                        <div class="col-sm-10">
                            <input class="form-control" id="sc_gif_time" placeholder="Second(s)"
                                   value="<?php echo Get_Config('sc_gif_time'); ?>">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label" for="sc_gif_framerate">FrameRate</label>
                        <div class="col-sm-10">
                            <input class="form-control" id="sc_gif_framerate" placeholder="Frames/Sec"
                                   value="<?php echo Get_Config('sc_gif_framerate'); ?>">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label" for="sc_gif_res">Resolution</label>
                        <div class="col-sm-10">
                            <input class="form-control" id="sc_gif_res"
                                   placeholder="XxY(Like 320x240) Set 0 will auto fit"
                                   value="<?php echo Get_Config('sc_gif_res'); ?>">
                        </div>
                    </div>
                </div>
            </div>
            <div align="right">
                <button class="btn btn-outline-success btn-lg" onclick="Update_ScreenShot()">Submit</button>
            </div>
        </div>
        <div class="tab-pane fade" id="delogo_config_tab" role="tabpanel" aria-labelledby="delogo_config">
            <div id="alert_delogo"></div>
            <div class="card">
                <div class="card-header">
                    DELogo Config
                </div>
                <div class="card-body">
                    <div class="form-group row">
                        <?php
                        $sc_jpeg = Get_Config('sc_jpeg');
                        ?>
                        <label class="col-sm-2 col-form-label" for="sc_jpeg">DELogo</label>
                        <div class="btn-group col-sm-10 btn-group-toggle" data-toggle="buttons">
                            <?php
                            $delogo = Get_Config('delogo');
                            ?>
                            <label class="btn btn-primary <?php if ($delogo == 1) {
                                echo "active";
                            } ?>" onclick="delogo=1;">
                                <input type="radio" name="delogo" id="option1"
                                       autocomplete="off" <?php if ($delogo == 1) {
                                    echo "checked";
                                } ?>> On
                            </label>
                            <label class="btn btn-primary <?php if ($delogo == 0) {
                                echo "active";
                            } ?>" onclick="delogo=0;">
                                <input type="radio" name="delogo" id="option2"
                                       autocomplete="off" <?php if ($delogo == 0) {
                                    echo "checked";
                                } ?>> Off
                            </label>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label" for="delogo_pos">Position:</label>
                        <div class="col-sm-10">
                            <input class="form-control" id="delogo_pos" placeholder="X:Y:Width:Height"
                                   value="<?php echo Get_Config('delogo_pos'); ?>">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label" for="delogo_start_cut">Start Cut:</label>
                        <div class="col-sm-10">
                            <input class="form-control" id="delogo_start_cut" placeholder="XX:XX (Set 0 will not cut)"
                                   value="<?php echo Get_Config('delogo_start_cut'); ?>">
                        </div>
                    </div>
                </div>
            </div>
            <div align="right">
                <button class="btn btn-outline-success btn-lg" onclick="Update_DELogo()">Submit</button>
            </div>
        </div>
        <div class="tab-pane fade" id="nginx_config_tab" role="tabpanel" aria-labelledby="nginx_config">
            <div class="card">
                <div class="card-header">
                    Nginx Config
                </div>
                <div class="card-body">
                    <div class="alert alert-danger"><strong>WARNING:</strong>Update this part will restart Nginx
                        service.
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label" for="nginx_worker">Thread(s)</label>
                        <div class="col-sm-10">
                            <input class="form-control" id="nginx_worker" placeholder="Thread(s)"
                                   value="<?php echo Get_Config('nginx_worker'); ?>">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
<script src="js/jquery-3.3.1.min.js"></script>
<script src="js/popper.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script>
    var play_secure = <?php echo Get_Config('play_secure');?>;
    var sc_jpeg =<?php echo Get_Config('sc_jpeg');?>;
    var sc_gif =<?php echo Get_Config('sc_gif');?>;
    var delogo = <?php echo Get_Config('delogo');?>;

    function Update_Config_Encode() {
        var encode_bitrate_video = document.getElementById('encode_bitrate_video').value;
        var encode_bitrate_audio = document.getElementById('encode_bitrate_audio').value;
        var encode_ts_frame = document.getElementById('encode_ts_frame').value;
        var encode_ts_time = document.getElementById('encode_ts_time').value;
        var encode_framerate = document.getElementById('encode_framerate').value;
        var encode_res = document.getElementById('encode_res').value;
        var worker_thread = document.getElementById('worker_thread').value;
        var ajax = new XMLHttpRequest();
        var alert = document.getElementById('alert_encode');
        ajax.open('POST', 'ajax/setting.php?action=update&type=encode', true);
        ajax.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        ajax.send('encode_bitrate_video=' + encode_bitrate_video + '&encode_bitrate_audio=' + encode_bitrate_audio + '&encode_ts_frame=' + encode_ts_frame + '&encode_ts_time=' + encode_ts_time + '&encode_framerate=' + encode_framerate + '&encode_res=' + encode_res + '&worker_thread=' + worker_thread);
        ajax.onreadystatechange = function () {
            if (ajax.readyState == 4 && ajax.status == 200) {
                var result = JSON.parse(ajax.responseText);
                if (result['code'] == 201) {
                    alert.setAttribute('class', 'alert alert-success');
                    alert.innerHTML = result['data']['message'];
                }
                if (result['code'] == 101) {
                    alert.setAttribute('class', 'alert alert-danger');
                    alert.innerHTML = result['data']['message'];
                }
            }
        }
    }

    function Update_API_Encode() {
        var api_key = document.getElementById('api_key').value;
        var ajax = new XMLHttpRequest();
        var alert = document.getElementById('alert_api');
        ajax.open('POST', 'ajax/setting.php?action=update&type=api', true);
        ajax.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        ajax.send('api_key=' + api_key);
        ajax.onreadystatechange = function () {
            if (ajax.readyState == 4 && ajax.status == 200) {
                var result = JSON.parse(ajax.responseText);
                if (result['code'] == 201) {
                    alert.setAttribute('class', 'alert alert-success');
                    alert.innerHTML = result['data']['message'];
                }
                if (result['code'] == 101) {
                    alert.setAttribute('class', 'alert alert-danger');
                    alert.innerHTML = result['data']['message'];
                }
            }
        }
    }

    function Update_Video_Service() {
        var video_port = document.getElementById('video_port').value;
        var allow_domain = document.getElementById('allow_domain').value;
        var jump_link = document.getElementById('jump_link').value;
        var video_domain = document.getElementById('video_domain').value;
        var ajax = new XMLHttpRequest();
        var alert = document.getElementById('alert_video_service');
        ajax.open('POST', 'ajax/setting.php?action=update&type=video_service', true);
        ajax.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        ajax.send('video_port=' + video_port + '&allow_domain=' + allow_domain + '&jump_link=' + jump_link + '&play_secure=' + play_secure + '&video_domain=' + video_domain);
        ajax.onreadystatechange = function () {
            if (ajax.readyState == 4 && ajax.status == 200) {
                var result = JSON.parse(ajax.responseText);
                if (result['code'] == 201) {
                    alert.setAttribute('class', 'alert alert-success');
                    alert.innerHTML = result['data']['message'];
                }
                if (result['code'] == 101) {
                    alert.setAttribute('class', 'alert alert-danger');
                    alert.innerHTML = result['data']['message'];
                }
            }
        }
    }

    function Update_ScreenShot() {
        var sc_jpeg_start_time = document.getElementById('sc_jpeg_start_time').value;
        var sc_jpeg_number = document.getElementById('sc_jpeg_number').value;
        var sc_jpeg_res = document.getElementById('sc_jpeg_res').value;
        var sc_jpeg_int = document.getElementById('sc_jpeg_int').value;
        var sc_gif_start_time = document.getElementById('sc_gif_start_time').value;
        var sc_gif_time = document.getElementById('sc_gif_time').value;
        var sc_gif_res = document.getElementById('sc_gif_res').value;
        var sc_gif_framerate = document.getElementById('sc_gif_framerate').value;
        var alert = document.getElementById('alert_sc');
        var ajax = new XMLHttpRequest();
        ajax.open('POST', 'ajax/setting.php?action=update&type=screenshot', true);
        ajax.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        ajax.send('jpeg=' + sc_jpeg + '&jpeg_start_time=' + sc_jpeg_start_time + '&jpeg_number=' + sc_jpeg_number + '&jpeg_res=' + sc_jpeg_res + '&gif=' + sc_gif + '&gif_start_time=' + sc_gif_start_time + '&gif_time=' + sc_gif_time + '&gif_res=' + sc_gif_res + '&gif_framerate=' + sc_gif_framerate + '&jpeg_int=' + sc_jpeg_int);
        ajax.onreadystatechange = function () {
            if (ajax.readyState == 4 && ajax.status == 200) {
                var result = JSON.parse(ajax.responseText);
                if (result['code'] == 201) {
                    alert.setAttribute('class', 'alert alert-success');
                    alert.innerHTML = result['data']['message'];
                }
                if (result['code'] == 101) {
                    alert.setAttribute('class', 'alert alert-danger');
                    alert.innerHTML = result['data']['message'];
                }
            }
        }
    }

    function Update_DELogo() {
        var ajax = new XMLHttpRequest();
        var delogo_pos=document.getElementById('delogo_pos').value;
        var delogo_start_cut=document.getElementById('delogo_start_cut').value;
        var alert=document.getElementById('alert_delogo');
        ajax.open('POST', 'ajax/setting.php?action=update&type=delogo', true);
        ajax.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        ajax.send('delogo='+delogo+'&delogo_pos='+delogo_pos+'&delogo_start_cut='+delogo_start_cut);
        ajax.onreadystatechange = function () {
            if (ajax.readyState == 4 && ajax.status == 200) {
                var result = JSON.parse(ajax.responseText);
                if (result['code'] == 201) {
                    alert.setAttribute('class', 'alert alert-success');
                    alert.innerHTML = result['data']['message'];
                }
                if (result['code'] == 101) {
                    alert.setAttribute('class', 'alert alert-danger');
                    alert.innerHTML = result['data']['message'];
                }
            }
        }
    }
    
    function Random_API_Key(len) {
        len = len || 32;
        var $chars = 'ABCDEFGHJKMNPQRSTWXYZabcdefhijkmnprstwxyz2345678';
        var maxPos = $chars.length;
        var pwd = '';
        for (var i = 0; i < len; i++) {
            pwd += $chars.charAt(Math.floor(Math.random() * maxPos));
        }
        document.getElementById('api_key').value = pwd;
    }

</script>
</html>