<template>
    <div id="app" style="background-color: #fff;">
        <Row class="wrapper">
            <Col :md="7" :sm="9" :xs="24" v-show="lrDisplay==0 || lrDisplay==-1">
                <panel-left 
                        v-bind:user-info="userInfo" 
                        v-bind:users-list="usersList" 
                        v-bind:all-users-list="allUsersList"
                        v-bind:userIndex="userIndex"
                        @actionLeft = "actionLeft"
                        @allUserSelected = "allUserSelected"
                        @userSelected="userSelected">
                </panel-left>
            </Col>

            <transition name="slide-fade">
            <Col :md="17" :sm="15" :xs="24" v-show="lrDisplay==1 || lrDisplay==-1">
                <panel-main 
                    v-bind:user="user" 
                    v-bind:userInfo="userInfo"
                    @actionMain="actionMain"
                    @sendMsg="sendMsg">
                </panel-main>
            </Col>
            </transition>
            <!--Col span="5"><panel-right></panel-right></Col-->
        </Row>

        <Modal v-model="Object.keys(userInfo).length === 0" title="请登录" :closable="false" :mask-closable="false">
            <Form ref="formLogin" :model="formLogin" :rules="formLogin.rule" inline>
                <FormItem prop="user">
                    <Input type="text" v-model="formLogin.user" placeholder="Username">
                        <Icon type="ios-person-outline" slot="prepend"></Icon>
                    </Input>
                </FormItem>
                <FormItem prop="password">
                    <Input type="password" v-model="formLogin.password" placeholder="Password">
                        <Icon type="ios-lock-outline" slot="prepend"></Icon>
                    </Input>
                </FormItem>
            </Form>
            
            <div slot="footer">
                <Button size="large" @click="formLoginSubmit('formLogin')">登录</Button>
            </div>
        </Modal>

        <Modal v-model="formPasswd.show" title="密码修改" :closable="false">
            <Form ref="formPasswd" :model="formPasswd" :rules="formPasswd.rule" inline>
                <FormItem prop="passwd">
                    <Input type="password" v-model="formPasswd.passwd" placeholder="旧密码">
                        <Icon type="ios-lock-outline" slot="prepend"></Icon>
                    </Input>
                </FormItem>
                <FormItem prop="passwd2">
                    <Input type="password" v-model="formPasswd.passwd2" placeholder="新密码">
                        <Icon type="ios-lock-outline" slot="prepend"></Icon>
                    </Input>
                </FormItem>
            </Form>
            
            <div slot="footer">
                <Button size="large" @click="formLoginSubmit('formPasswd')">确定</Button>
            </div>
        </Modal>

        <Modal v-model="memberManage.show" title="人员管理">
            <Form ref="formUpdateUser" :model="formLogin" :rules="formLogin.rule" inline>
                <FormItem prop="user">
                    <Input type="text" size="default" v-model="formLogin.user" placeholder="Username" style="width: 200px;">
                        <Icon type="ios-person-outline" slot="prepend"></Icon>
                    </Input>
                </FormItem>
                <FormItem prop="password">
                    <Input type="password" size="default" v-model="formLogin.password" placeholder="Password" style="width: 190px;">
                        <Icon type="ios-lock-outline" slot="prepend"></Icon>
                    </Input>
                </FormItem>

                <FormItem>
                    <Button type="primary" size="default" @click="formLoginSubmit('formUpdateUser')">添加</Button>
                </FormItem>
            </Form>
            <Table size="small" :columns="memberManage.columns" :data="memberManage.data"></Table>
        </Modal>

        <Modal v-model="memberSearch.show" title="人员列表" @on-ok="memberSearchOk">
            <template>
                <Input v-if="!user.group" v-model="memberSearch.group_name" placeholder="群名称 创建后不可修改" style="margin-bottom: 8px;" />
            </template>
            <Table size="small" :columns="memberSearch.columns" :data="memberSearch.data" @on-selection-change="memberSearchSelected"></Table>
        </Modal>

        <Modal v-model="ws_status" :footer-hide="true" :closable="false" :mask-closable="false" width="320">
            <Spin></Spin><span style="color: #ff9900; float: left; margin: -26px 0 0 60px; font-size: 15px;">服务器连接中...</span>
        </Modal>
    </div>
</template>

<script>
import PanelLeft from './components/PanelLeft';
import PanelMain from './components/PanelMain';
import PanelRight from './components/PanelRight';
import wsEvents from './wsEvents';
require('./wsEvents.js');

export default {
    name: 'App',
    components: {
        PanelLeft,
        PanelMain,
        PanelRight,
    },

    methods: {
        userSelected: function(index){
            if ( index < 0 || index > this.usersList.length ) {
                this.user = {};
                return;
            }
            this.user = this.usersList[index];

            // 消息
            this.ws.send( JSON.stringify({'type': 'msg_list', 'group': this.user.group, 'from': this.user.name, 'to': this.userInfo.name}) );

            if ( this.user.unread > 0 ) {
                this.ws.send( JSON.stringify({'type': 'mark_read', 'from': this.user.name, 'to': this.userInfo.name}) );
            }

            this.user.unread = 0;

            // 选中会话中的用户
            this.userIndex = index;
            if ( isPhone && index > -1 ) {
                // 手机版，需要显示聊天框
                this.lrDisplay = 1;
            }
        },

        allUserSelected: function(user) {
            // 在未读消息那里双向加好友了
            // this.ws.send(JSON.stringify({'type': 'add_friend', 'from': this.userInfo.name, 'to': user.name}));
         
            // 从人员列表中选择聊天对象
            this.usersList.unshift({
                'name': user.name,
                'group': false,
                'unread': 0,
                'last_time': 9999999999,
                'content': []
            });
            this.user = this.usersList[0];

            this.userSelected(0);
        },

        formLoginSubmit(name) {
            this.$refs[name].validate((valid) => {
                if (valid) {
                    switch(name) {
                        case 'formLogin':
                            var data = {
                                'type': 'login',
                                'name': this.formLogin.user,
                                'passwd': this.formLogin.password
                            };
                            this.ws.send(JSON.stringify(data));
                        break;

                        case 'formUpdateUser':
                            var data = {
                                'type': 'update_user',
                                'name': this.formLogin.user,
                                'passwd': this.formLogin.password
                            };
                            this.ws.send(JSON.stringify(data));
                        break;

                        case 'formPasswd':
                            var data = {
                                'type': 'passwd',
                                'passwd': this.formPasswd.passwd,
                                'passwd2': this.formPasswd.passwd2,
                                'name': this.userInfo.name
                            };
                            this.ws.send(JSON.stringify(data));
                        break;
                    }
                }
            })
        },

        wsUpdateUser($data){

        },

        wsMsg( evt ){
            let msg = eval('('+ evt.data +')');
            switch ( msg.type ) {
                case 'login':
                    if ( !msg.status ) {
                        return this.$Message.error(msg.msg);
                    }

                    this.$Message.success(msg.msg);

                    this.formLogin.user = '';
                    this.formLogin.password = '';

                    let data = msg.data;
                    delete data.passwd;

                    this.userInfo = data;
                    sessionStorage.setItem('user', JSON.stringify(data));

                    this.afterLogin();
                break;

                case 'friend_list':
                    for ( let i in msg.data ) {
                        msg.data[i].content = [];
                    }
                    this.usersList = msg.data;
                break;

                case 'msg_list':
                    this.user.content = msg.data;
                break;

                case 'txt':
                case 'img':
                case 'file':
                    if ( msg.from != this.userInfo.name ) {
                        // 收信人
                        if( msg.to.indexOf('@') === 0 ) {
                            if ( msg.to.substr(1) != this.user.name ) {
                                for ( let i in this.usersList ) {
                                    if ( this.usersList[i].group && this.usersList[i].name == msg.to.substr(1) ) {
                                        this.usersList[i].unread ++;

                                        let _top = this.usersList[i];
                                        this.usersList.splice(i, 1);
                                        this.usersList.unshift(_top);

                                        if ( this.userIndex >= 0 && this.userIndex < i ) {
                                            this.userIndex += 1;
                                        }
                                        break;
                                    }
                                }
                            } else {
                                this.user.content.push( msg );
                            }
                        } else {
                            // 用户消息
                            if ( msg.new_chat == 1 ) {
                                this.usersList.unshift({
                                    'name': msg.from,
                                    'img': '',
                                    'group': false,
                                    'unread': 1,
                                    'last_time': msg.time,
                                    'content': []
                                });
                                if ( typeof(this.user.name) != 'undefined' ) {
                                    this.userIndex += 1;
                                }
                            }else if ( msg.from != this.user.name ) {
                                for ( let i in this.usersList ) {
                                    if ( this.usersList[i].name == msg.from ) {
                                        this.usersList[i].unread ++;
                                        this.usersList[i].last_time = msg.time;

                                        let _top = this.usersList[i];
                                        this.usersList.splice(i, 1);
                                        this.usersList.unshift(_top);

                                        if ( this.userIndex >= 0 && this.userIndex < i ) {
                                            this.userIndex += 1;
                                        }
                                        break;
                                    }
                                }
                            } else {
                                this.user.last_time = msg.time;
                                this.user.content.push( msg );
                            }
                        }
                    } else if ( this.user.name == msg.to || this.user.name == msg.to.substr(1) ) {
                        this.user.last_time = msg.time;
                        this.user.content.push( msg );
                    }
                break;

                default:
                    if ( typeof( wsEvents[msg.type] ) == 'function' ) {
                        wsEvents[msg.type]( this, msg );
                    }
                break;
            }
        },

        sendMsg (msg, ext) {
            let data = {
                'type': 'txt',
                'from': this.userInfo.name,
                'to': this.user.name,
                'msg': msg,
                'name': this.userInfo.name,
                //'img': '',
            };

            // setInterval(()=>{
            //     this.ws.send(JSON.stringify(data));
            // }, 1200);

            switch ( ext.type ) {
                case 'txt':

                break;

                case 'img':
                    data.type = 'img';
                break;

                case 'file':
                    data.type = 'file';
                    data.filename = ext.name;
                break;
            }

            
            if ( this.user.group ) data.to = '@'+ data.to;
            this.ws.send(JSON.stringify(data));
        },

        actionLeft(act, data){
            switch ( act ) {
                case 'memberManage':
                    this.ws.send(JSON.stringify({'type': 'user_list'}));
                    this.memberManage.show = true;
                break;

                case 'passwd':
                    this.formPasswd.show = true;
                break;
            }
        },

        afterLogin(){
            this.ws.send(JSON.stringify({'type': 'friend_list'}));

            setTimeout(() => {
                this.ws.send(JSON.stringify({'type': 'user_list', 'online': 1}));
            }, 500);
        },

        connectWs(){
            let vthis = this;
            if ( this.wsStatus == -1 ) {
                this.ws = new WebSocket(wsUrl);
                this.ws.onopen = function() {
                    vthis.wsStatus = 10;

                    if ( Object.keys(vthis.userInfo).length > 0 ) {
                        let data = {
                            'type': 'login',
                            'name': vthis.userInfo.name,
                        };
                        vthis.ws.send(JSON.stringify(data));

                        setTimeout(() => {
                            vthis.afterLogin();
                        }, 500);
                    }
                };
                this.ws.onmessage = this.wsMsg;
                this.ws.onclose = function() { 
                    console.debug('onclose...');
                    vthis.wsStatus = -1;
                };
            }
            
            setTimeout(this.connectWs, 3000);
        },

        actionMain(event, data) {
            switch ( event ) {
                case 'memberSearch':
                    this.ws.send(JSON.stringify({'type': 'user_list'}));
                    this.memberSearch.show = true;
                break;

                case 'delConversation':
                    if ( this.user.group ) {
                        this.ws.send(JSON.stringify({
                            'type': 'leave_group', 
                            'from': this.userInfo.name,
                            'group_name': this.user.name
                        }));
                    } else {
                        this.ws.send(JSON.stringify({
                            'type': 'del_friend', 
                            'from': this.userInfo.name,
                            'name': this.user.name
                        }));
                    }

                    this.usersList.splice(this.userIndex, 1);
                    this.userIndex = -1;
                break;

                case 'back':
                    this.lrDisplay = 0;
                    this.userIndex = -1;
                break;
            }
        },


        memberSearchSelected(selection) {
            this.memberSearch.selected = [];
            for ( var i in selection ) {
                this.memberSearch.selected.push(selection[i].name);
            }
        },

        memberSearchOk() {
            if ( this.memberSearch.selected.length == 0 ) return ;

            let group_name = '';
            let create = true;
            if ( this.user.group ) {
                group_name = this.user.name;
                create = false;
            } else {
                if ( this.memberSearch.group_name == '' ) {
                    this.$Message.error('请填写群名称!');
                    return; 
                }

                this.memberSearch.selected.push(this.user.name);
                this.memberSearch.selected.push(this.userInfo.name);
                
                group_name = this.memberSearch.group_name; //this.memberSearch.selected.slice(0,2).join(',') + new Date().getTime();

                this.memberSearch.group_name = '';
            }

            let data = {
                'type': 'join_group', 
                'create': create,
                'from': this.userInfo.name,
                'group_name': group_name, 
                'users': this.memberSearch.selected
            };
            this.ws.send(JSON.stringify(data));
        },
    },

    created () {
        let vthis = this;

        // 初始化界面，为手机时列表、聊天界面分开显示
        this.lrDisplay = isPhone ? 0 : -1;

        var user = sessionStorage.getItem('user');
        if ( user != null ) {
            this.userInfo = eval('('+ user +')');
        }

        // wsStatus : -1 断开 0 - 9 连接中 10 连接上
        this.connectWs();
        
        /*
        if ( Object.keys(this.userInfo).length === 0 ) {
            this.$router.push("/login");
            return;
        }
        */

        setInterval(()=>{
            let m1 = '【新消息】';
            let m2 = '【　　　】';

            if ( this.notification.title == '' ) {
                this.notification.title = document.querySelector('title').innerHTML;
            }

            let unread = this.usersList.filter(u => u.unread > 0);

            if ( unread.length == 0 ) {
                document.querySelector('title').innerHTML = this.notification.title;
                return;
            }

            if ( this.notification.trigger ) {
                document.querySelector('title').innerHTML = m1 + ' - '+ this.notification.title;
                this.notification.trigger = false;
            } else {
                document.querySelector('title').innerHTML = m2 + ' - '+ this.notification.title;
                this.notification.trigger = true;
            }
        }, 500);
    },

    watch: {
    },

    computed: {
        ws_status: function () {
            return this.wsStatus != 10 ? true : false;
        }
    },

    data() {
        return {
            ws: '',
            wsStatus: -1,

            lrDisplay: -1, // 为手机时，列表、聊天界面分开显示；为：0、隐藏聊天界面，1、隐藏列表界面，-1、不处理

            formLogin: {
                user: '',
                password: '',

                rule: {
                    user : [
                        { required: true, message: '请填写用户名', trigger: 'blur' }
                    ],

                    password: [
                        { required: true, message: '请填写密码', trigger: 'blur' },
                        { type: 'string', min: 6, message: '密码最少6位', trigger: 'blur' }
                    ]
                }
            },

            formPasswd: {
                show: false,
                passwd: '',
                passwd2: '',

                rule: {
                    passwd : [
                        { required: true, message: '请填写旧密码', trigger: 'blur' },
                    ],

                    passwd2: [
                        { required: true, message: '请填写新密码', trigger: 'blur' },
                        { type: 'string', min: 6, message: '密码最少6位', trigger: 'blur' }
                    ]
                }
            },

            userInfo: {

            },

            allUsersList:[],
            usersList: [
                /*
                {
                    id: 1,
                    group: true,
                    members: [
                        {
                            name: '前端群',
                            img: '//tva4.sinaimg.cn/crop.0.1.1125.1125.180/475bb144jw8f9nwebnuhkj20v90vbwh9.jpg',
                        }
                    ],
                    name: '小明',
                    img: '//tva4.sinaimg.cn/crop.0.1.1125.1125.180/475bb144jw8f9nwebnuhkj20v90vbwh9.jpg',
                    unread: 0,
                    content: [
                        {
                            type: 'txt', // img file
                            uid: 1,
                            to: '123',
                            msg: 'ssssss',
                            name: '小明',
                            img: '//tva4.sinaimg.cn/crop.0.1.1125.1125.180/475bb144jw8f9nwebnuhkj20v90vbwh9.jpg',
                            time: 1234567890
                        }
                    ]
                },

                {
                    id: 2,
                    group: false,
                    num: 0,
                    name: '小李',
                    img: '//tva1.sinaimg.cn/crop.0.0.118.118.180/5db11ff4gw1e77d3nqrv8j203b03cweg.jpg',
                    unread: 3,
                    content: [
                        {
                            type: 'txt', // img file
                            uid: 1,
                            to: '2',
                            msg: 'ssssss',
                            name: '小明',
                            img: '//tva4.sinaimg.cn/crop.0.1.1125.1125.180/475bb144jw8f9nwebnuhkj20v90vbwh9.jpg',
                            time: 1234567890
                        },

                        {
                            type: 'img', // img file
                            uid: 2,
                            to: '1',
                            msg: 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQSdvjrryBoc_rdovfcwNgVuGRG8qqWA-BJ2Ti_TKc3wLzR_2mt',
                            name: '小李',
                            img: '//tva1.sinaimg.cn/crop.0.0.118.118.180/5db11ff4gw1e77d3nqrv8j203b03cweg.jpg',
                            time: 1234567890
                        },

                        {
                            type: 'file', // img file
                            uid: 2,
                            to: '1',
                            filename: '好文件',
                            msg: 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQSdvjrryBoc_rdovfcwNgVuGRG8qqWA-BJ2Ti_TKc3wLzR_2mt',
                            name: '小李',
                            img: '//tva1.sinaimg.cn/crop.0.0.118.118.180/5db11ff4gw1e77d3nqrv8j203b03cweg.jpg',
                            time: 1234567890
                        },
                    ]
                }
                */
            ],

            user: {},
            userIndex: -1,

            memberManage: {
                show: false,
                columns: [
                    {'title': '用户名', 'key': 'name'}, 
                    {'title': '操作', 'key': 'act',
                    render: (h, params) => {
                            return h('div', [
                                /*
                                h('Button', {
                                    props: {
                                        type: 'error',
                                        size: 'small'
                                    },
                                    on: {
                                        click: () => {
                                            this.ws.send( JSON.stringify({'type': 'update_user', 'name':params.row.name, 'del': 1}) );
                                        }
                                    }
                                }, '删除'),
                                */

                                h('Button', {
                                    style: {
                                        marginLeft: '5px'
                                    },
                                    props: {
                                        type: 'error',
                                        size: 'small'
                                    },
                                    on: {
                                        click: () => {
                                            
                                            this.formLogin.user = params.row.name;
                                        }
                                    }
                                }, '密码修改'),
                            ]);
                        }
                    }
                ],
                data: [
                    {'name': 'loading...'}
                ]
            },

            memberSearch: {
                show: false,
                columns: [
                    {'type': 'selection', width: 80},
                    {'title': '用户名', 'key': 'name'}
                ],

                data: [{'name': 'loading...'}],

                group_name: '',
                selected: []
            },

            notification: {
                trigger: true,
                title: '',
            }
        }
    }
}
</script>

<style>
.wrapper, .wrapper>.ivu-col{ height: 100%;}
.wrapper { max-width: 1000px; margin: auto; border: #dcdee2 1px solid;}

/* 动画 */
.fade-enter-active, .fade-leave-active {
  transition: opacity .8s;
}
.fade-enter, .fade-leave-to .fade-leave-active {
  opacity: 0;
}

.slide-fade-enter-active {
  transition: all .3s ease;
}
.slide-fade-leave-active {
  transition: all .8s cubic-bezier(1.0, 0.5, 0.8, 1.0);
}
.slide-fade-enter, .slide-fade-leave-to
/* .slide-fade-leave-active for below version 2.1.8 */ {
  transform: translateX(100px);
  opacity: 0;
}
</style>