$(function(){
    //start();
    //setTimeout(socketuse,6000);    
    $('#chat_send').click(function(){
        var chart = $('#chart').val();        
        if(chart){
            $('#mychart').append('<p>'+chart+'</p>')
            //var socket = io();
            socket.emit('news',chart);           
            socket.on('event', function(data){
                $('#socket').append('<p>'+JSON.stringify(data)+'</p>');
                //$('#mychart').append('<p>'+data.+'</p>')
                console.log(data);
                alert(JSON.stringify(data));
            });
            // 信息接收
            socket.on('news', function(msg){
                $('#mychart').append('<p style="color:red;">'+msg+'</p>');
            });
            $('#chart').val('');  
        }
    });
    socketuse();
});
//var socket = io('127.0.0.1:6011');
var socket = io();
function start(){
    var ws = new WebSocket('ws://127.0.0.1:6010');
    ws.onopen = function(){
        console.log("ws 开启成功")
        ws.send("502-8555")
    };
    ws.onmessage = function(e){
        console.log("接受到的数据："+e.data);
    };
}
function socketuse(){
    console.log("开始执行- socket.io");
    //socket = io();
    socket.on('connect',function(){
        console.log("服务器已经成功连接");
        $('#socket').append('<p>服务器已经成功连接</p>');
    });
    socket.on('event', function(data){
        $('#socket').append('<p>'+JSON.stringify(data)+'</p>');
        console.log(data);
    });
    socket.on('disconnect', function(){
        console.log("服务器- 断开连接");
    });
    $('#socket').append('<p>页面开始了 socket.io 服务</p>');
}