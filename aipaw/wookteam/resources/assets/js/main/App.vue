<template>
    <div id="app">
        <w-header></w-header>
        <transition :name="transitionName">
            <keep-alive>
                <router-view class="child-view"></router-view>
            </keep-alive>
        </transition>
        <w-spinner></w-spinner>
    </div>
</template>

<script>
    import WSpinner from "./components/WSpinner";
    import WHeader from "./components/WHeader";
    export default {
        components: {WHeader, WSpinner},
        data () {
            return {
                transitionName: null,
            }
        },
        mounted() {
            this.checkToken();
            //
            let hash = window.location.hash;
            if (hash.indexOf("#") === 0) {
                hash = hash.substr(1);
                if (hash) {
                    this.$nextTick(() => {
                        hash = $A.removeURLParameter(hash, 'token');
                        this.goForward({path: hash});
                    });
                }
            }
            this.sessionStorage('/', 1);
            let pathname = window.location.pathname;
            if (pathname && this.sessionStorage(pathname) === 0) {
                this.sessionStorage(pathname, this.sessionStorage('::count') + 1);
            }
            //
            setInterval(() => {
                this.searchEnter();
            }, 1000);
            //
            this.handleWebSocket();
            $A.setOnUserInfoListener("app", () => { this.handleWebSocket() });
        },
        watch: {
            '$route' (To, From) {
                if (this.transitionName === null) {
                    this.transitionName = 'app-slide-no';
                    return;
                }
                if (typeof To.name === 'undefined' || typeof From.name === 'undefined') {
                    return;
                }
                this.slideType(To, From);
            }
        },
        methods: {
            checkToken() {
                let token = $A.urlParameter("token");
                if ($A.count(token) > 10) {
                    $.setToken(decodeURIComponent(token));
                    $A.getUserInfo(true);
                    let path = $A.removeURLParameter(window.location.href, 'token');
                    let uri = document.createElement('a');
                    uri.href = path;
                    if (uri.pathname) {
                        let query = $A.urlParameterAll();
                        if (typeof query['token'] !== "undefined") delete query['token'];
                        this.$nextTick(() => {
                            this.goForward({path: uri.pathname, query}, true);
                        });
                    }
                }
            },
            slideType(To, From) {
                let isBack = this.$router.isBack;
                this.$router.isBack = false;
                //
                let ToIndex = this.sessionStorage(To.path);
                let FromIndex = this.sessionStorage(From.path);
                if (ToIndex && ToIndex < FromIndex) {
                    isBack = true;      //后退
                    this.sessionStorage(true, ToIndex);
                }else{
                    isBack = false;     //前进
                    this.sessionStorage(To.path, this.sessionStorage('::count') + 1);
                }
                //
                if (To.meta.slide === false || From.meta.slide === false)
                {
                    //取消动画
                    this.transitionName = 'app-slide-no'
                }
                else if (To.meta.slide === 'up' || From.meta.slide === 'up' || To.meta.slide === 'down' || From.meta.slide === 'down')
                {
                    //上下动画
                    if (isBack) {
                        this.transitionName = 'app-slide-down'
                    } else {
                        this.transitionName = 'app-slide-up'
                    }
                }
                else
                {
                    //左右动画（默认）
                    if (isBack) {
                        this.transitionName = 'app-slide-right'
                    } else {
                        this.transitionName = 'app-slide-left'
                    }
                }
            },
            sessionStorage(path, num) {
                let conut = 0;
                let history = JSON.parse(window.sessionStorage['__history__'] || '{}');
                if (path === true) {
                    let items = {};
                    for(let i in history){
                        if (history.hasOwnProperty(i)) {
                            if (parseInt(history[i]) <= num) {
                                items[i] = history[i];
                                conut++;
                            }
                        }
                    }
                    history = items;
                    history['::count'] = Math.max(num, conut);
                    window.sessionStorage['__history__'] = JSON.stringify(history);
                    return history;
                }
                if (typeof num === 'undefined') {
                    return parseInt(history[path] || 0);
                }
                if (path === "/") num = 1;
                history[path] = num;
                for(let key in history){ if (history.hasOwnProperty(key) && key !== '::count') { conut++; } }
                history['::count'] = Math.max(num, conut);
                window.sessionStorage['__history__'] = JSON.stringify(history);
            },

            searchEnter() {
                let row = $A(".sreachBox");
                if (row.length === 0) {
                    return;
                }
                if (row.attr("data-enter-init") === "init") {
                    return;
                }
                row.attr("data-enter-init", "init");
                //
                let buttons = row.find("button[type='button']");
                let button = null;
                if (buttons.length === 0) {
                    return;
                }
                buttons.each((index, item) => {
                    if ($A(item).text().indexOf("搜索")) {
                        button = $A(item);
                    }
                });
                if (button === null) {
                    return;
                }
                row.find("input.ivu-input").keydown(function(e) {
                    if (e.keyCode == 13) {
                        if (!button.hasClass("ivu-btn-loading") ) {
                            button.click();
                        }
                    }
                });
            },

            handleWebSocket() {
                if ($A.getToken() === false) {
                    $A.WSOB.close();
                } else {
                    $A.WSOB.setOnMsgListener("app", (msgDetail) => {
                        if (msgDetail.username == this.usrName) {
                            return;
                        }
                        switch (msgDetail.messageType) {
                            case 'open':
                                window.localStorage.setItem("__::WookTeam:config", $A.jsonStringify(Object.assign(msgDetail.config, {
                                    nickname: $A.getNickName(false)
                                })));
                                break;
                            case 'close':
                                window.localStorage.setItem("__::WookTeam:config", $A.jsonStringify({}));
                                break;
                            case 'info':
                                if (msgDetail.body.type == 'update') {
                                    $A.getUserInfo(true);
                                }
                                break;
                            case 'user':
                                if (msgDetail.body.type == 'taskA') {
                                    $A.triggerTaskInfoListener(msgDetail.body.act, msgDetail.body.taskDetail, false);
                                }
                                break;
                            case 'kick':
                                $A.token("");
                                $A.storage("userInfo", {});
                                $A.triggerUserInfoListener({});
                                //
                                let id = 'inip_' + Math.round(Math.random() * 10000);
                                let ip = msgDetail.body.ip;
                                let ip2 = ip.substring(0, ip.lastIndexOf('.')) + '.*';
                                this.$Modal.warning({
                                    title: this.$L("系统提示"),
                                    content: this.$L('您的帐号在其他地方（%）登录，您被迫退出，如果这不是您本人的操作，请注意帐号安全！', '<span id="' + id + '">' + ip2 + '</span>'),
                                    onOk: () => {
                                        this.goForward({path: '/'}, true);
                                    }
                                });
                                this.$nextTick(() => {
                                    $A.getIpInfo(ip, (res) => {
                                        if (res.ret === 1) {
                                            $A("span#" + id).text(res.data.textSmall);
                                            $A("span#" + id).attr("title", ip2);
                                        }
                                    });
                                });
                                break;
                        }
                    });
                }
            }
        }
    }
</script>

<style>
    body { overflow-x: hidden; }
</style>
<!--suppress CssUnusedSymbol -->
<style scoped>
    .child-view {
        position: absolute;
        width: 100%;
        min-height: 100%;
        background-color: #f1f2f7;
        transition: all .3s cubic-bezier(.55, 0, .1, 1);
    }
    .app-slide-no-leave-to {display: none;}
    /**
     * 左右模式
     */
    .app-slide-left-leave-active{z-index:1;transform:translate(0,0)}
    .app-slide-left-leave-to{z-index:1;transform:translate(0,0)}
    .app-slide-left-enter-active{opacity:0;z-index:2;transform:translate(30%,0)}
    .app-slide-left-enter-to{opacity:1;z-index:2;transform:translate(0,0)}
    .app-slide-right-leave-active{opacity:1;z-index:2;transform:translate(0,0)}
    .app-slide-right-leave-to{opacity:0;z-index:2;transform:translate(30%,0)}
    .app-slide-right-enter-active{z-index:1;transform:translate(0,0)}
    .app-slide-right-enter{z-index:1;transform:translate(0,0)}

    /**
     * 上下模式
     */
    .app-slide-up-leave-active{z-index:1;transform:translate(0,0)}
    .app-slide-up-leave-to{z-index:1;transform:translate(0,0)}
    .app-slide-up-enter-active{opacity:0;z-index:2;transform:translate(0,20%)}
    .app-slide-up-enter-to{opacity:1;z-index:2;transform:translate(0,0)}
    .app-slide-down-leave-active{opacity:1;z-index:2;transform:translate(0,0)}
    .app-slide-down-leave-to{opacity:0;z-index:2;transform:translate(0,20%)}
    .app-slide-down-enter-active{z-index:1;transform:translate(0,0)}
    .app-slide-down-enter{z-index:1;transform:translate(0,0)}
</style>
