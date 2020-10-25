var lockReconnect = false;  //避免ws重复连接
var ws = null;          // 判断当前浏览器是否支持WebSocket
var wsUrl = "ws://www.onebase.com:2346";

createWebSocket(wsUrl);   //连接ws

function createWebSocket(url) {
    try{
        if('WebSocket' in window){
            ws = new WebSocket(url);
        }
        initEventHandle();
    }catch(e){
        reconnect(url);
        console.log(e);
    }     
}

function initEventHandle() {
    ws.onclose = function () {
        reconnect(wsUrl);
        console.log("ws连接关闭："+new Date().toLocaleString());
    };
    ws.onerror = function () {
        reconnect(wsUrl);
        console.log("ws连接错误");
    };
    ws.onopen = function () {
        heartCheck.reset().start();      //心跳检测重置
        console.log("ws连接成功："+new Date().toLocaleString());
    };
    ws.onmessage = function (e) {    //如果获取到消息，心跳检测重置
        heartCheck.reset().start();      //拿到任何消息都说明当前连接是正常的
        console.log("ws收到消息：" +e.data);
        
        var json_data = JSON.parse(e.data); 
        
        if (json_data.msg !== 'ping') {
            
            obj = document.getElementById("send_text");

            if (obj){

                $('.new_message_num').html('');

                var json_data = JSON.parse(e.data); 

                var chat_item = "<div class='item'><img src='"+json_data.head_img+"' class='online'><p class='message'> <a class='name'> <small class='text-muted pull-right'><i class='fa fa-clock-o'></i> "+json_data.time+"</small> "+json_data.nickname+" </a> "+chat_contents_replace(json_data.msg)+" </p> </div>";

                $('#chat-box').append(chat_item);

                $('#chat-box').scrollTop($('#chat-box')[0].scrollHeight);

            } else {

                var new_message_num = $('.new_message_num').html();

                if (new_message_num == '') {

                    new_message_num = 1;
                } else {
                    new_message_num++;
                }

                $('.new_message_num').html(new_message_num);
            }
        }
    };
}
// 监听窗口关闭事件，当窗口关闭时，主动去关闭websocket连接，防止连接还没断开就关闭窗口，server端会抛异常。
window.onbeforeunload = function() {
    ws.close();
}  

function reconnect(url) {
    if(lockReconnect) return;
    lockReconnect = true;
    setTimeout(function () {     //没连接上会一直重连，设置延迟避免请求过多
        createWebSocket(url);
        lockReconnect = false;
    }, 2000);
}

//心跳检测
var heartCheck = {
    timeout: 60000,        //1分钟发一次心跳
    timeoutObj: null,
    serverTimeoutObj: null,
    reset: function(){
        clearTimeout(this.timeoutObj);
        clearTimeout(this.serverTimeoutObj);
        return this;
    },
    start: function(){
        var self = this;
        this.timeoutObj = setTimeout(function(){
            //这里发送一个心跳，后端收到后，返回一个心跳消息，
            //onmessage拿到返回的心跳就说明连接正常
            var ping_ws_send_json = '{"member_id":"1","msg":"ping"}';
            ws.send(ping_ws_send_json);
            console.log("ping!")
            self.serverTimeoutObj = setTimeout(function(){//如果超过一定时间还没重置，说明后端主动断开了
                ws.close();     //如果onclose会执行reconnect，我们执行ws.close()就行了.如果直接执行reconnect 会触发onclose导致重连两次
            }, self.timeout)
        }, this.timeout)
    }
}

function chat_contents_replace(str)
{

    str = str.replace(/\</g,'&lt;');

    str = str.replace(/\>/g,'&gt;');

    str = str.replace(/\n/g,'<br/>');

    str = str.replace(/\[em_([0-9]*)\]/g,'<img src="/static/module/common/qqface/arclist/$1.gif" border="0" />');

    str = str.replace(/([[\s\S]*?])/i, '<img onclick="chatImgsOnClick(this)" src="$1"/>');

    str = str.replace('[', '');

    str = str.replace(']', '');

    return str;

}