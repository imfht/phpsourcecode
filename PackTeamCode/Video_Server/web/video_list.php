<?php
include("../config/config.php");
include("include/function.php");
if (!Login_Status()) {
    header("Location:login.php");
    exit;
}
$redis=Redis_Link();
$db_link=DB_Link();
$waiting=mysqli_num_rows(mysqli_query($db_link,"SELECT * FROM video_list WHERE status = '0'"));
$encoding=mysqli_num_rows(mysqli_query($db_link,"SELECT * FROM video_list WHERE status = '1'"));
$success=mysqli_num_rows(mysqli_query($db_link,"SELECT * FROM video_list WHERE status = '2'"));
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
    <div class="card">
        <div class="card-header">
            Video List
        </div>
        <div class="card-body">
            Waiting:<span class="badge badge-secondary"><?php echo $waiting;?></span>&nbsp;
            Encoding:<span class="badge badge-info"><?php echo $encoding;?></span>&nbsp;
            Success:<span class="badge badge-success"><?php echo $success;?></span>&nbsp;&nbsp;&nbsp;&nbsp;
            <button class="btn btn-primary" onclick="Get_Video_List(now_page,'30')">Refresh</button>
            <button id="copy_m3u8" class="js-copy btn btn-primary" data-clipboard-text="">
                Copy M3U8 Link
            </button>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <br>
            <div id="alert"></div>
            <nav aria-label="Page navigation example">
                <ul id="page_select_top" class="pagination justify-content-center">

                </ul>
            </nav>
            <table class="table table-striped">
                <thead>
                <tr>
                    <th scope="col">ID</th>
                    <th scope="col">Filename</th>
                    <th scope="col">Add Time</th>
                    <th scope="col">Status</th>
                    <th scope="col">Action</th>
                </tr>
                </thead>
                <tbody id="video_list">
                </tbody>
            </table>
            <nav aria-label="Page navigation example">
                <ul id="page_select" class="pagination justify-content-center">

                </ul>
            </nav>
        </div>
    </div>
</div>
</body>
<script src="js/jquery-3.3.1.min.js"></script>
<script src="js/popper.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/clipboard.min.js"></script>
<script>
    var now_page=1;
    var copy_m3u8=document.getElementById('copy_m3u8');
    var clipboard = new ClipboardJS('.js-copy');
    Window.onload=Get_Video_List('1','30');
    function Get_Video_List(page,num) {
        copy_m3u8.setAttribute('data-clipboard-text','');
        page=Number(page);
        var video_list=document.getElementById('video_list');
        var ajax=new XMLHttpRequest();
        ajax.open('GET','ajax/video.php?action=video_list&page='+page+'&num='+num,true);
        ajax.send();
        ajax.onreadystatechange=function () {
            if (ajax.readyState == 4 && ajax.status == 200) {
                var result = JSON.parse(ajax.responseText);
                if (result['code']==201){
                    //Clean List
                    video_list.innerHTML="";
                    for (var i = 0;result['data'][i];i++){
                        var row_tr=document.createElement('tr');
                        //ID
                        var td_ID=document.createElement('th');
                        td_ID.innerHTML=result['data'][i]['ID'];
                        row_tr.appendChild(td_ID);
                        //Filename
                        var td_filename=document.createElement('td');
                        td_filename.innerHTML=result['data'][i]['filename'];
                        row_tr.appendChild(td_filename);
                        //Add Time
                        var td_format_time=document.createElement('td');
                        td_format_time.innerHTML=result['data'][i]['format_time'];
                        row_tr.appendChild(td_format_time);
                        //Status
                        var td_status=document.createElement('td');
                        if (result['data'][i]['status']==0){
                            td_status.innerHTML='<span class="badge badge-secondary">Waiting</span>'
                        }
                        if (result['data'][i]['status']==1){
                            td_status.innerHTML='<span class="badge badge-info">Encoding</span>'
                        }
                        if (result['data'][i]['status']==2){
                            td_status.innerHTML='<span class="badge badge-success">Success</span>'
                        }
                        if (result['data'][i]['status']==3){
                            td_status.innerHTML='<span class="badge badge-danger">Failed</span>'
                        }
                        row_tr.appendChild(td_status);
                        //Action
                        var td_action=document.createElement('td');
                        td_action.innerHTML='<a href="player/play.php?id='+result['data'][i]['ID']+'" class="btn btn-primary" target="_blank">Play</a>&nbsp;' +
                            '<a href="'+result['data'][i]['m3u8_link']+'" class="btn btn-warning" target="_blank">M3u8</a>&nbsp;' +
                            '<button class="btn btn-danger" onclick="Delete_Video('+result['data'][i]['ID']+')">Delete</button>';
                        copy_m3u8.setAttribute('data-clipboard-text',copy_m3u8.getAttribute('data-clipboard-text')+'\r'+result['data'][i]['m3u8_link']);
                        row_tr.appendChild(td_action);
                        //Add ALL
                        video_list.appendChild(row_tr);
                    }
                    //
                    //Set Pagination
                    //
                    var total_page=result['data']['total_page'];
                    var page_select = document.getElementById('page_select');
                    //Clean
                    page_select.innerHTML="";
                    //First
                    var page_first = document.createElement('li');
                    if (page==1){
                        page_first.setAttribute('class','page-item disabled');
                    }else{
                        page_first.setAttribute('class','page-item');
                    }
                    page_first.innerHTML='<a class="page-link" href="#" tabindex="-2" onclick="Get_Video_List(1,'+num+')">First</a>';
                    page_select.appendChild(page_first);
                    //Previous
                    var page_previous = document.createElement('li');
                    if (page==1){
                        page_previous.setAttribute('class','page-item disabled');
                    }else{
                        page_previous.setAttribute('class','page-item');
                    }
                    var previous_page=page-1;
                    page_previous.innerHTML='<a class="page-link" href="#" tabindex="-1" onclick="Get_Video_List('+ previous_page +','+num+')">Previous</a>';
                    page_select.appendChild(page_previous);
                    //1-Now
                    for (var m=5;m>=0;m--){
                        var page_number = document.createElement('li');
                        var number_page= page-m;
                        if (number_page<=0||number_page==page){

                        }else{
                            page_number.setAttribute('class','page-item');
                            page_number.innerHTML='<a class="page-link" href="#" onclick="Get_Video_List('+number_page+','+num+')">'+number_page+'</a>';
                            page_select.appendChild(page_number);
                        }
                    }
                    //Now
                    var page_now=document.createElement('li');
                    page_now.setAttribute('class','page-item active');
                    page_now.innerHTML='<a class="page-link" href="#">'+page+'</a>';
                    page_select.appendChild(page_now);
                    //Now-End
                    for (var n=1;page+n<=total_page&&n<=5;n++){
                        var page_number2 = document.createElement('li');
                        var number_page2= page+n;
                        page_number2.setAttribute('class','page-item');
                        page_number2.innerHTML='<a class="page-link" href="#" onclick="Get_Video_List('+number_page2+','+num+')">'+number_page2+'</a>';
                        page_select.appendChild(page_number2);
                    }
                    //Next
                    var page_next=document.createElement('li');
                    if (page==total_page){
                        page_next.setAttribute('class','page-item disabled');
                    }else{
                        page_next.setAttribute('class','page-item');
                    }
                    var next_page=page+1;
                    page_next.innerHTML='<a class="page-link" href="#" tabindex="-1" onclick="Get_Video_List('+ next_page +','+num+')">Next</a>';
                    page_select.appendChild(page_next);
                    //End
                    var page_end=document.createElement('li');
                    if (page==total_page){
                        page_end.setAttribute('class','page-item disabled');
                    }else{
                        page_end.setAttribute('class','page-item');
                    }
                    page_end.innerHTML='<a class="page-link" href="#" tabindex="-1" onclick="Get_Video_List('+ total_page +','+num+')">End</a>';
                    page_select.appendChild(page_end);
                    now_page=page;
                    document.getElementById('page_select_top').innerHTML="";
                    document.getElementById('page_select_top').innerHTML=page_select.innerHTML;
                }
                if (result['code']==101){
                }
            }
        }
    }
    function Delete_Video(id) {
        if (confirm('Do you want DELETE #'+id+' video?')){
            var alert=document.getElementById('alert');
            var ajax=new XMLHttpRequest();
            ajax.open('GET','ajax/video.php?action=delete&id='+id);
            ajax.send();
            ajax.onreadystatechange=function () {
                if (ajax.readyState == 4 && ajax.status == 200) {
                    var result = JSON.parse(ajax.responseText);
                    alert.innerHTML=result['data']['message'];
                    if (result['code']=='101'){
                        alert.setAttribute('class','alert alert-danger');
                    }
                    if (result['code']=='201'){
                        alert.setAttribute('class','alert alert-success');
                    }
                }
            }
        }
    }
</script>
</html>