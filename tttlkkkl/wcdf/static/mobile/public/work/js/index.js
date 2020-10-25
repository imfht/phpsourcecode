var nowDOM = document.getElementById('now');
var hours = new Date().getHours();
var isLogin = 0;//标记是否登录
function getTime() {
    var min = new Date().getMinutes();
    var sec = new Date().getSeconds();
    var now = hours + ':' + min + ':' + sec;
    return now;
}
nowDOM.innerHTML = getTime();
setInterval(function () {
    nowDOM.innerHTML = getTime();
}, 1000);


var year = new Date().getFullYear();
var month = new Date().getMonth() + 1;
var day = new Date().getDate();

var today = year + '-' + month + '-' + day;
document.getElementById('today').innerHTML = today;

var gps = new Location();
var address = null;
var punchBtn = document.getElementById('punchBtn');

punchBtn.addEventListener('click', function () {
    gps.bLocation(function (rs) {
        address = this.address;
        var location=this;
        var data = {
            lat: location.lat,
            lng: location.lng,
            address: location.address
        };
        console.log(location);
        console.log(11111);
        console.log(data);
        http.post('/work/api/works', data, function (ret) {
            if (ret.code === 0) {
                var html='';
                var container=$('#container');

                html+='<li><span class="r-time">';
                html+=ret.data.time+'</span><span class="r-address">';
                html+=ret.data.address+'</span></li>';
                container.append(html);
                layer.open({
                    content: '打卡成功!'
                    , skin: 'msg'
                    , time: 2 //2秒后自动关闭
                });
            } else {
                layer.open({
                    content: ret.msg || '打卡失败!'
                    , btn: '好'
                });
            }
        });
    })
});
$(function () {
    //获取用户信息
    http.get('/system/api/oAuth', '', function (data) {
        if (data.code === 0) {
            if (data.data.result === 1) {
                isLogin = 1;
                var user = data.data.user;
                var avatar = $('#avatar');
                var userName = $('#user_name');
                if (user.avatar !== null && user.avatar !== undefined && user.avatar !== '') {
                    avatar.attr('src', user.avatar);
                }
                if (user.name !== null && user.name !== undefined && user.name !== '') {
                    userName.text(user.name);
                }
            } else {
                layer.open({
                    content: '您未登入系统，是否前往登录?'
                    , btn: ['去登录', '朕知道了']
                    , yes: function (index) {
                        layer.close(index);
                        location.href = '/mobile/';
                    }
                });
            }
        } else {
            layer.open({
                content: data.msg || '鉴权失败!'
                , skin: 'msg'
                , time: 2 //2秒后自动关闭
            });
        }
    });
    //获取考勤列表
    var map={
        page:1
    };
    http.get('/work/api/works',map,function(ret){
        if(ret.code===0){
            var container=$('#container');
            var html='';
            var data=ret.data;
            for (i in data){
                html+='<li><span class="r-time">';
                html+=data[i].create_time+'</span><span class="r-address">';
                html+=data[i].address+'</span></li>';
            }
            container.html(html);
        }else {
            layer.open({
                content: ret.msg || '打卡信息获取失败!'
                , skin: 'msg'
                , time: 2 //2秒后自动关闭
            });
        }
    })
});
