var app = new Vue({
    el: '#app',
    data: {
        style_obj:{
            height:''
        },     // 右边框架样式
        is_mobile: false,   // 是否是手机端访问
        aside_class_obj:{   // 手机端侧边栏 css
            x_aside_hidden: false,
            x_aside_show: false
        },
        req_url:'http://127.0.0.30',    // 请求的域名
        bg_is_show:false,   // 遮罩背景
        userData: '',   //用户数据
        urlList: [],  // 侧边栏菜单数据
        open_tab_id: '',     // 展开的菜单
        tab_active: 1,   // 当前选择菜单
        tabShowId: 1,    // 默认显示
        tabData:[],     // 页面右边 tab数据
    },
    created: function () {     // 实例创建完成后被立即调用
        this.is_login();
        this.fun_is_mobile();
        this.style_obj.height = window.innerHeight - 161 + "px";
        x(".bg-blur").css({height: window.innerHeight +"px"});      //设置背景高度
    },
    methods: {
        fun_is_mobile(){     // 判断是否是手机端访问
            if( innerWidth >= 767 ){
                this.is_mobile = !false;
            }
        },
        req_code(code,info){     // 请求状态操作
            switch (code) {
                case '0':
                    this.$message.error(info);
                break;
                default:
                    sessionStorage.removeItem('userData');
                    sessionStorage.removeItem('urlList');
                    this.is_login();
                    this.$message.error(info);
                break;
            }
        },
        user_btn(e){   // 退出登录
            var _this = this;
            if( e == 'exit'){
                this.$confirm('你确定要退出登录吗, 是否继续?', '提示', {
                    cancelButtonText: '点错了',
                    confirmButtonText: '确认退出',
                    type: 'warning',
                    callback:function(e,d){
                        if( e == 'confirm' ){
                            sessionStorage.removeItem('userData');
                            sessionStorage.removeItem('urlList');
                            _this.is_login();
                            _this.$message.success('成功退出')
                        }
                    }
                  });
            }else if( e == 'user_info'){
                this.$message.success('个人设置')
            }


        },
        is_login(){ // 判断是否登录
            var userData = JSON.parse( sessionStorage.getItem('userData') );
            var urlList = JSON.parse( sessionStorage.getItem('urlList') );
            if( userData && urlList ){
                var tabs = {id:1,title:'后台首页',content:'<iframe src="Index/index.html" scrolling="yes" frameborder="0" style="display: block; height: 100%; width: 100%;"></iframe>'};
                this.tabData = [tabs];
                this.userData = userData;
                this.urlList = urlList;
                this.tabShowId = 1;
            }else{
                this.tabData = '';
                this.userData = '';
                this.urlList = [];
                this.tabShowId = 1;
                var tabs = {id:1,title:'管理登录',content:'<iframe src="login.html" scrolling="yes" frameborder="0" style="display: block; height: 100%; width: 100%;"></iframe>'};
                this.tabData = [tabs];
            }
        },
        dh_is_show() {  //手机端 显示/隐藏 侧边栏
            if( this.aside_class_obj.x_aside_show ){
                this.aside_class_obj.x_aside_hidden = !false;
                this.aside_class_obj.x_aside_show = false;
                this.bg_is_show = false;
            }else{
                this.aside_class_obj.x_aside_show = !false;
                this.aside_class_obj.x_aside_hidden = false;
                this.bg_is_show = !false;
            }
        },
        addTab(data){   //增加 tab
            var _this = this;
            var tabDatas = this.tabData;
            var is_are = true;
            tabDatas.forEach(function(tab, index){
                if( tab.id == data.id ){
                    is_are = false;
                }
            })
            if( !is_are ){
                _this.tabShowId = data.id;
            }else{
                var tab_data = {
                    id: data.id,
                    title: data.title,
                    content: '<iframe scrolling="yes" style="height:' + (window.innerHeight - 161) + 'px; display: block; width: 100%;" frameborder="0" src="' + data.name +'.html"></iframe>'
                }
                _this.tabData.push(tab_data);
                _this.tabShowId = data.id;
            }
        },
        delTab(targetName){   // 删除 tab
            console.log(targetName)
            var tabs = this.tabData;
            var activeName = this.tabShowId;
            if (activeName === targetName) {
                tabs.forEach(function(tab, index){
                    if (tab.id === targetName) {
                        var nextTab = tabs[index + 1] || tabs[index - 1];
                        if (nextTab) {
                            activeName = nextTab.id;
                        }
                    }
                });
            }
            this.tabShowId = activeName;
            this.tabData = tabs.filter(function(tab){
                return tab.id !== targetName
            } );
        },
        open_tab(e){
            this.open_tab_id = [e];
        },
        tab_click(e){
            if( e.name == 1 ){
                this.tab_active = 1;
            }else{
                this.tab_active = this.open_tab_id + '-' + e.name;
            }
        }

    }
})
x.ready(function(){
    //设置主内容高度
    // x(".iframe-content").css({height: window.innerHeight - 161 + "px"});
    //设置背景高度
    // x(".bg-blur").css({height: window.innerHeight +"px"});
    console.log(window)

})
