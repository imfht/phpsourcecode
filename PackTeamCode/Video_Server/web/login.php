<?php
include("../config/config.php");
include("include/function.php");
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
<div class="container">
    <br><br><br>
    <div align="center">
        <h1>Video Encode System</h1>
        <h6>
            <span class="badge badge-secondary">V<?php echo $version;?></span>
            <span class="badge badge-success">Open Source</span>
        </h6>

        <h3>Login</h3>
    </div>
    <br><br>
    <div id="alert"></div>
    <div class="input-group mb-3">
        <div class="input-group-prepend">
            <span class="input-group-text" id="username-span">Username</span>
        </div>
        <input id="username" type="text" class="form-control" placeholder="Username" aria-label="Username" aria-describedby="username-span">
    </div>
    <div class="input-group mb-3">
        <div class="input-group-prepend">
            <span class="input-group-text" id="password-span">Password</span>
        </div>
        <input id="password" type="password" class="form-control" placeholder="Password" aria-label="password" aria-describedby="password-span">
        <div class="input-group-append">
            <button class="btn btn-outline-success" type="button" onclick="Login_Submit()">Login</button>
        </div>
    </div>
    <br><br>
    <div class="card-subtitle" align="center">
        <?php echo $copyright;?>
    </div>
</div>
</body>
<script src="js/jquery-3.3.1.min.js"></script>
<script src="js/popper.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script>
    function Login_Submit() {
        var username=document.getElementById('username').value;
        var password=document.getElementById('password').value;
        var ajax = new XMLHttpRequest();
        var alert = document.getElementById('alert');
        ajax.open('POST','ajax/user.php?action=login',true);
        ajax.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        ajax.send('username=' + username + '&password=' + password);
        ajax.onreadystatechange=function () {
            if (ajax.readyState == 4 && ajax.status == 200) {
                var result = JSON.parse(ajax.responseText);
                if (result['code']==201){
                    alert.setAttribute('class','alert alert-success');
                    alert.innerHTML=result['data']['message'];
                    window.location = 'index.php';
                }
                if (result['code']==101){
                    alert.setAttribute('class','alert alert-danger');
                    alert.innerHTML=result['data']['message'];
                }
            }
        }
    }
</script>
</html>