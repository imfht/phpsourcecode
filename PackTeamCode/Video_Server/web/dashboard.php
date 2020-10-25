<?php
include("../config/config.php");
include("include/function.php");
if (!Login_Status()) {
    header("Location:login.php");
    exit;
}
$worker_thread = Get_Config('worker_thread');
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
    <div class="row">
        <div class="col-lg-6 col-md-6">
            <div class="card">
                <div class="card-header">
                    Server Status
                </div>
                <div class="card-body">
                    CPU:
                    <div class="progress">
                        <div id="cpu-progress" class="progress-bar progress-bar-striped progress-bar-animated"
                             role="progressbar"
                             aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0">Loading
                        </div>
                    </div>
                    Memory:
                    <div class="progress">
                        <div id="mem-progress" class="progress-bar progress-bar-striped progress-bar-animated"
                             role="progressbar"
                             aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0">Loading
                        </div>
                    </div>
                    Disk:(Free:<span id="disk-free">Loading</span>/Total:<span id="disk-total">Loading</span>)
                    <div class="progress">
                        <div id="disk-progress" class="progress-bar progress-bar-striped progress-bar-animated"
                             role="progressbar"
                             aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0">Loading
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6 col-md-6">
            <div class="card">
                <div class="card-header">
                    Worker Status
                </div>
                <div class="card-body">
                    <table class="table">
                        <thead>
                        <tr>
                            <th width="5%">Worker</th>
                            <th width="25%">Status</th>
                            <th>Progress</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        for ($i = 1; $i <= $worker_thread; $i++) {
                            ?>
                            <tr>
                                <td><?php echo $i; ?></td>
                                <td id="worker_status_<?php echo $i; ?>">
                                    <span class="badge badge-secondary">Loading</span>
                                </td>
                                <td>
                                    <div class="progress">
                                        <div id="worker_progress_<?php echo $i; ?>"
                                             class="progress-bar progress-bar-striped progress-bar-animated"
                                             role="progressbar"
                                             aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0">
                                            Loading
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <?php
                        }
                        ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-6 col-md-6">
            <div class="card">
                <div class="card-header">
                    Encode Control
                </div>
                <div class="card-body">
                    <button id="start_encode" type="button" class="btn btn-primary btn-lg" onclick="Start_Encode()">
                        Start Encode
                    </button>
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
    function Update_System_Info() {
        var cpu_progress = document.getElementById('cpu-progress');
        var mem_progress = document.getElementById('mem-progress');
        var disk_progress = document.getElementById('disk-progress');
        var ajax = new XMLHttpRequest();
        ajax.open('GET', 'ajax/load.php', true);
        ajax.send();
        ajax.onreadystatechange = function () {
            if (ajax.readyState == 4 && ajax.status == 200) {
                var result = JSON.parse(ajax.responseText);
                cpu_progress.innerHTML = result['data']['cpu'] + '%';
                cpu_progress.style.width = result['data']['cpu'] + '%';
                cpu_progress.setAttribute('aria-valuenow', result['data']['cpu']);
                mem_progress.innerHTML = result['data']['mem'] + '%';
                mem_progress.style.width = result['data']['mem'] + '%';
                mem_progress.setAttribute('aria-valuenow', result['data']['mem']);
                disk_progress.innerHTML = result['data']['disk_per'] + '%';
                disk_progress.style.width = result['data']['disk_per'] + '%';
                disk_progress.setAttribute('aria-valuenow', result['data']['disk_per']);
                document.getElementById('disk-free').innerHTML = result['data']['disk_free'] + 'GB';
                document.getElementById('disk-total').innerHTML = result['data']['disk_total'] + 'GB';
            }
        }
    }

    function Update_Worker_Info() {
        var ajax = new XMLHttpRequest();
        ajax.open('GET', 'ajax/monitor.php', true);
        ajax.send();
        ajax.onreadystatechange = function () {
            if (ajax.readyState == 4 && ajax.status == 200) {
                var result = JSON.parse(ajax.responseText);
                for (var i=1;i<=result['total'];i++){
                    var worker_status=document.getElementById('worker_status_'+i);
                    var worker_progress=document.getElementById('worker_progress_'+i);
                    if (result['worker'][i]['status']==101){
                        worker_status.innerHTML='<span class="badge badge-success">Free</span>';
                    }
                    if (result['worker'][i]['status']==0){
                        worker_status.innerHTML='<span class="badge badge-warning">Initialization</span>';
                    }
                    if (result['worker'][i]['status']==102){
                        worker_status.innerHTML='<span class="badge badge-danger">Load Cache Failed</span>';
                    }
                    if (result['worker'][i]['status']==1){
                        worker_status.innerHTML='<span class="badge badge-primary">Encoding</span>';
                        worker_progress.innerHTML=result['worker'][i]['progress']+'%';
                        worker_progress.style.width=result['worker'][i]['progress']+'%';
                        worker_progress.setAttribute('aria-valuenow', result['worker'][i]['progress']);
                    }else{
                        worker_progress.innerHTML="Empty";
                        worker_progress.style.width="0";
                        worker_progress.setAttribute('aria-valuenow', "0");
                    }
                    if (result['worker'][i]['status']==2){
                        worker_status.innerHTML='<span class="badge badge-primary">ScreenShot</span>';
                    }
                    if (result['worker'][i]['status']==3){
                        worker_status.innerHTML='<span class="badge badge-primary">Segmenting</span>';
                    }
                    if (result['worker'][i]['status']==103){
                        worker_status.innerHTML='<span class="badge badge-danger">Unknown Status</span>';
                    }
                }
            }
        }
    }
    function Start_Encode() {
        var ajax = new XMLHttpRequest();
        ajax.open('GET', 'ajax/encode.php?action=start', true);
        ajax.send();
        ajax.onreadystatechange = function () {
            if (ajax.readyState == 4 && ajax.status == 200) {
                var result = JSON.parse(ajax.responseText);
                if (result['code'] == 201) {
                    document.getElementById('start_encode').disabled = true;
                    document.getElementById('start_encode').innerHTML = 'Starting...';
                }
            }
        }
    }
    setInterval(Update_Worker_Info, 2000);
    setInterval(Update_System_Info, 5000);
    window.onload = Update_System_Info;
</script>
</html>