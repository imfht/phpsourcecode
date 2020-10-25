
var pages = {};
var app = new Vue({
    el: '#app',
    data: {
        req_url:'http://127.0.0.30',    // 请求的域名
        bg_imgarr:[     // 背景图片
            '../public/img/bg/a.jpg',
            '../public/img/bg/b.jpg',
            '../public/img/bg/c.jpg',
            '../public/img/bg/d.jpg',
            '../public/img/bg/e.jpg',
            '../public/img/bg/f.jpg',
            '../public/img/bg/g.jpg',
            '../public/img/bg/h.jpg',
            '../public/img/bg/i.jpg',
            '../public/img/bg/j.jpg',
            '../public/img/bg/k.jpg',
        ],
        bg_is_show: false,   // 遮罩背景
        aside_class_obj:{   // 手机端侧边栏 css
            show_sider: false,
            hidden_sider: false
        },
        bg_img_class: {
            bg_img_all_1: false,
            bg_img_all_0: false
        },
        is_mobile: false,   // 是否是手机端访问
        userData: '',   // 管理用户数据
        urlData: [],  // 侧边栏菜单数据
        list_btn_obj:{
            icon: 'navicon-round',
            type: 'primary',
            text: '菜单'
        },
        login_exit_btn: false,  // 退出按钮

    },
    created: function () {     // 实例创建完成后被立即调用
        this.is_login();
    },
    methods: {
        open_box(id,title,url) {    // 打开弹框
            this.is_login();
            var _this = this,
                widths = '80vw',
                height = '80vh',
                weizhi = 'ct',
                bgcolor = 'rgba(255,255,255,0)';
            if( !_this.is_mobile ){
                widths = '70vw';
            }else{
                bgcolor = 'rgba(56, 41, 9,0.8)';
            }
            if( id == 'edit'){
                widths = '40vw';
                height = '40vh';
                bgcolor = 'rgba(56, 41, 9,0.8)';
            }
            var config = {
                width: widths,
                height: height,
                storeStatus: false,   // 存储窗口位置、大小信息
                position: weizhi,   // 窗口位置
                // skin: 'river',  //窗口皮肤
                bgColor: bgcolor,      // 窗口背景颜色
                // border: false,  // 无边框
                controlStyle:'background-color: rgba(56, 41, 9, 0.8); color:#fff;',  // 标题栏外观
                style: layx.multiLine(function(){/*
                    .layx-inlay-menus .layx-icon:hover {
                        background-color: rgba(0,0,0,0.2);
                    }
                */}),
                statusBar: '<div style="line-height:25px;padding:0 10px;" >当前登录管理员：' + (this.userData?this.userData.username+' ('+ this.userData.name +')':'没有登录') +'</div>', // 状态栏
                event: {    // 监听窗口事件
                    // 加载事件
                    onload: {
                        // 加载之前，return false 不执行
                        before: function (layxWindow, winform) {
                            if( _this.is_mobile ){
                                layx.max(id);
                            }
                        },
                        // 加载之后
                        after: function (layxWindow, winform) {
                        }
                    },
                    // 关闭事件
                    ondestroy: {
                        // 关闭之前，return false 不执行，inside 区分用户点击内置关闭按钮还是自动调用，用户关闭之前传递的参数，escKey表示是否是按下esc触发
                        before: function (layxWindow, winform,params,inside,escKey) {
                        },
                        // 关闭之后
                        after: function () {
                        }
                    },
                    // 窗口存在事件
                    onexist: function (layxWindow, winform) {
                        if( id == 'edit' ){
                            layx.setTitle(id, title);
                            layx.setUrl(id,url);
                        }else{
                            layx.flicker(id);
                        }
                    },
                }
            }
            layx.iframe(id, title, url, config);
        },
        open_fd_box(id) {   // 打开浮动框
            var btnTarget= document.getElementById(id);
            var title = '', content = '', heights = '250',width = '250', types = 'html', urls = '', bg_color = '', border = '#fff';
            if( id == 'zanshang' ){
                content = '<div><img src="../public/img/wx_zs.jpg" style="width: 100%;"><img src="../public/img/zfb_zs.jpg" style="width: 100%;"> </div>';
            }else if( id == 'wx_qq' ){
                heights = '300';
                content = '<div><img src="../public/img/jia_wx.png" style="width: 100%;"><img src="../public/img/jia_qq.png" style="width: 100%;"> </div>';
            }else if( id == 'caidan' ){
                if( this.is_login() ){
                    // if( !this.is_mobile ){
                        // heights = 'auto';
                    // }else{
                        heights = '400';
                    // }
                    bg_color = 'rgba(0,0,0,0)';
                    border = 'rgba(0,0,0,0.2)';
                    content = document.getElementById('caidan_list');
                }else{
                    bg_color = 'rgba(0,0,0,0)';
                    border = 'rgba(0,0,0,0.1)';
                    content = '';
                    types = 'url';
                    urls = 'login.html';
                }
            }
            var config = {
                id: 'fd_box_id',
                type: types,
                url: urls,
                title: title,
                control: false,     // 是否显示控制标题栏
                content: content,
                floatTarget: btnTarget,  // 被吸附的DOM对象
                width: width,
                height: heights,
                bgColor: bg_color,      // 窗口背景颜色
                border: border,       // 窗口边框
                shadable: true,  // 窗口阻隔、遮罩
                shadeDestroy: true,  // 是否点击阻隔关闭窗口
                cloneElementContent: false,   // 设置文本窗口DOM对象拷贝模式
                skin: 'river',  //窗口皮肤
                alwaysOnTop: true,   // 是否总是置顶
                floatDirection: 'right',   // 设置方向
                escKey: false,    // 是否启用esc按键关闭窗口
                resizable: false,    //是否允许拖曳调整大小
            }
            layx.open(config);
        },
        is_login(){ // 判断是否登录
            if( window.innerWidth >= 767 ){ // 是否是手机端访问
                this.is_mobile = false;
            }else{
                this.is_mobile = !false;
            }
            var userData = x.get_session('userData');
            var urlData = x.get_session('urlData');
            if( userData && urlData ){
                this.list_btn_obj.icon = 'navicon-round';
                this.list_btn_obj.type = 'primary';
                this.list_btn_obj.text = '菜单';
                this.userData = userData;
                this.urlData = urlData;
                this.login_exit_btn = !false;   // 退出按钮
                return true;
            }else{
                this.list_btn_obj.icon = 'log-in';
                this.list_btn_obj.type = 'warning';
                this.list_btn_obj.text = '登录';
                this.userData = '';
                this.urlData = [];
                this.login_exit_btn = false;    // 退出按钮
                return false;
            }
        },
        req_code(code,info){     // 请求状态操作
            switch (code) {
                case '0':
                    this.$Message.error(info);
                break;
                default:
                    sessionStorage.removeItem('userData');
                    sessionStorage.removeItem('urlData');
                    this.is_login();
                    this.$Message.error(info);
                break;
            }
        },
        exit_login(){   // 退出登录
            var _this = this;
            iview.Modal.confirm({
                content: '你确定要退出登录吗？',
                onOk: function(){
                    x.del_session('userData');
                    x.del_session('urlData');
                    _this.is_login();
                    iview.Message.success('成功退出');
                }
            });
        },
        click_fun(data){     // 侧边菜单 点击菜单触发方法
            layx.destroy('fd_box_id');   // 关闭浮动框
            this.open_box(data.name,data.title,data.name + '.html');
        },
        ajax(url,type,data, fun = '',msg = true ){
            iview.LoadingBar.start();   //  顶部进度条开始
            var _this = this;
            x.ajax({
                url: url, // 请求url
                type: type,  // 请求的类型
                dataType: "json",  //数据类型
                headers:{   // Header头里面传递数据
                    usertoken:_this.userData.userToken
                },
                data: data,     //发送到服务器的数据
                success: function (res) {   //成功后执行
                    console.log(res);
                    if( res.code == 1 ){
                        iview.LoadingBar.finish();   //  顶部进度条关闭
                        if(msg){ iview.Message.success(res.info); }
                        if( fun != '' && typeof fun == 'function'){
                            fun(res.data);
                        }
                    }else{
                        iview.LoadingBar.error();   //  顶部进度条关闭
                        _this.req_code(res.code,res.info);
                    }
                }
            });
        },
        set_bg_img(img){    // 点击更换背景
            x('body').css({'backgroundImage':'url(' + img +')'});
            x.set_lsession('bg_img',img);
        },
        bg_btn_fun(){   // 打开背景列表
            if( this.bg_img_class.bg_img_all_1 ){
                this.bg_img_class.bg_img_all_0 = !false;
                this.bg_img_class.bg_img_all_1 = false;
            }else{
                this.bg_img_class.bg_img_all_1 = !false;
                this.bg_img_class.bg_img_all_0 = false;
            }
        },
        dh_is_show() {  //手机端 显示/隐藏 侧边栏
            if( this.aside_class_obj.show_sider ){
                this.aside_class_obj.hidden_sider = !false;
                this.aside_class_obj.show_sider = false;
                this.bg_is_show = false;
            }else{
                this.aside_class_obj.show_sider = !false;
                this.aside_class_obj.hidden_sider = false;
                this.bg_is_show = !false;
            }
        },

    }
})
if( x.get_lsession('bg_img') ){
    x('body').css({'backgroundImage':'url(' + x.get_lsession('bg_img') +')'});
}
