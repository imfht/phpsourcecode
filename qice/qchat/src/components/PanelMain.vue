<template>
<div id="panel-main">
    <div class="chat-title">
        <Icon type="ios-arrow-back" :size="26" v-if="isPhone" @click="back()" style="position: absolute; top:12px;" />
        <div class="users-list" v-if="!user.group" style="text-align: center;">
            {{ user.name }}
        </div>
        <div class="users-list" v-if="user.group" style="text-align: center;">
            <a href="javascript:void(0)" size="26" @click="showMembers = showMembers ? false : true;">
                {{ user.name }} {{ user.members.length }}人
                <Icon type="ios-arrow-down" v-if="user.group" /></Icon>
            </a>
            <div class="members" v-if="showMembers">
                <div class="user" v-for="item in user.members">
                    <Avatar icon="ios-person" />
                    <p>{{item.name}}</p>
                </div>
            </div>
        </div>

        <Dropdown class="chat-act" trigger="click" placement="bottom-end" v-if="Object.keys(user).length !== 0" @on-click="chatAct">
            <a href="javascript:void(0)">
                <Icon type="md-more" size="26" /></Icon>
            </a>
            <DropdownMenu slot="list">
                <!--DropdownItem>关闭通知</DropdownItem-->
                <DropdownItem name="delConversation">{{user.group ? '退出群聊' : '删除会话'}}</DropdownItem>
                <DropdownItem divided name="memberSearch">{{user.group ? '拉人入群' : '发起群聊'}}</DropdownItem>
            </DropdownMenu>
        </Dropdown>
    </div>

    <div class="chat-body">
        <div class="chat-main">
        <ul>
            <li v-for="item in user.content" :class="{ 'chat-mine' : item.from === userInfo.name }">
                <div class="chat-user">
                    <Avatar :src="item.img" icon="ios-person" />
                    <p><span class="msg_from">{{ item.from }}</span><span class="msg_time">{{ dateFormat(item.time) }}</span></p>
                </div>

                <div class="chat-text" v-if="item.type == 'txt'" v-html="item.msg.replace(/\n/g, '<br />')">
                </div>
                
                <div class="chat-text" v-if="item.type != 'txt'">
                    <a :href="item.msg" target="_blank" v-if="item.type === 'img' ">
                        <img class="timg" :src="item.msg" />
                    </a>

                    <a :href="item.msg" style="color: white;" v-if="item.type === 'file' ">
                        <Icon type="md-copy" size="40" color="#515a6e" />
                        <span>{{ item.filename }}</span>
                    </a> 
                </div>
            </li>
        </ul>
        </div>
    </div>
    
    <div class="chat-bottom" v-if="Object.keys(user).length !== 0">
        <Row>
            <Col span="2">
            <Upload
                :show-upload-list="false"
                :on-success="uploadSuccess"
                :before-upload="beforeUpload"
                v-bind:action="uploadUrl">
                <Icon type="md-copy" size="26" />
            </Upload>
            </Col>
            <Col span="2">
                <div 
                    @mouseleave="face_show = false"
                    v-if="face_show" class="face-box">
                    <ul>
                        <li v-for="i in 20" v-html="'&#'+ (128512+i)+';'" @click="face"></li>
                    </ul>
                </div>
                <Icon 
                    @mouseover.native="face_show = true"
                    type="md-happy" size="26" />
            </Col>
        </Row>

        <div>
            <textarea class="ivu-input" rows="3" v-model="msg" @keyup.enter.exact="sendMsg()" @keyup.ctrl.enter="msgBr()"></textarea>
        </div>

        <Button type="primary" @click="sendMsg()" style="float: right; margin-top: 5px;">
            发送 <Icon type="ios-paper-plane" :size="14" />
        </Button>
    </div>
</div>        
</template>

<script>
import util from '../utils/util.js';
export default {
    name: 'PanelMain',

    props: ['user', 'userInfo'],

    watch: {
        'user.content'() {
            this.$nextTick(() => {
                setTimeout(() => {
                    let chatMain = this.$el.querySelector('.chat-main');
                    chatMain.scrollTop = chatMain.scrollHeight;
                }, 15);
            })
        }
    },

    methods: {
        dateFormat: util.dateFormat,

        sendMsg(){
            if ( this.msg.trim() == '') return;
            
            this.$emit('sendMsg', this.msg, {type: 'txt'});
            this.msg = '';
        },

        beforeUpload: function(file){
            if (this.uploadUrl == '') {
                this.$Message.error('demo 暂不支持上传文件及图片');
                return false;
            }
            return true;
        },

        uploadSuccess: function(response, file, fileList){
            let fileUrl = response[uploadConfig.urlKey];
            let fileName= file.name;

            var index = fileName.lastIndexOf(".");
            var ext   = fileName.substr(index+1);
            var isPic = false;
            if ( ['png', 'jpg', 'jpeg', 'bmp', 'gif', 'webp', 'psd', 'svg', 'tiff'].indexOf(ext.toLowerCase()) !== -1 ) {
                isPic = true;
            }

            this.$emit('sendMsg', fileUrl, {type: isPic ? 'img' : 'file', name: fileName});
        },

        chatAct: function(name){
            switch ( name ) {
                case 'memberSearch':
                    this.$emit('actionMain', 'memberSearch');
                break;

                case 'delConversation':
                    this.$emit('actionMain', name);
                break;
            }
        },

        face: function(e){
            this.face_show = false;

            let f = e.currentTarget.innerHTML;
            this.msg = this.msg + f;
        },

        msgBr() {
            this.msg += "\n";
        },

        back(){
            this.$emit('actionMain', 'back', -1);
        }
    },

    data: function(){
        return {
            isPhone: isPhone,
            uploadUrl: uploadConfig.url,
            showMembers: false,

            msg: '',

            face_show: false
        }
    }
}
</script>

<style>
#panel-main{
    height: 100%;
}

.chat-title, .chat-bottom{
    top: 0;
    position: absolute;
    width: 100%;
    background-color: #f8f8f9;
    padding: 5px 10px;
    height: 52px;
    border-bottom: 1px #dcdee2 solid;
    z-index: 2;
}

.chat-title .users-list{
    line-height: 40px;
    font-size: 16px;
}
.chat-title .users-list a{ font-size: 16px;}
.chat-title .user{
    width: 42px;
    display: inline-block;
    text-align: center;
    margin: 10px;
}

.members { 
    position: absolute;
    width: 100%;
    left: 0;
    background: #fff;
}

.chat-act{
    position:absolute;
    right: 10px;
    top: 5px;
    line-height: 42px;
    float: right;
}

.chat-body{ height: 100%; background-color: #f8f8f9;}
.chat-body>.chat-main {
    padding: 67px 10px 170px 10px;
    height: 100%;
    overflow-y: auto;
    overflow-x: hidden;
}
.chat-body ul>li {
    position: relative;
    font-size: 0;
    margin-bottom: 25px;
    padding-left: 60px;
    min-height: 68px;
}
.chat-body ul>li.chat-mine{
    text-align: right;
    padding-left: 0;
    padding-right: 60px;
}
.chat-body ul>li span.msg_time{ margin: 0 10px;}
.chat-body ul>li.chat-mine span.msg_from { float: right; }

.chat-body ul>li .chat-user {
    position: absolute;
    left: 5px;
    top: 0;
}
.chat-body ul>li.chat-mine .chat-user{
    right: 5px;
    left: auto;
}
.chat-body ul>li p{
    position: absolute;
    left: 60px;
    top: -2px;
    width: 500px;
    line-height: 24px;
    font-size: 12px;
    white-space: nowrap;
    color: #999;
    text-align: left;
    font-style: normal;
}
.chat-body ul>li.chat-mine p{
    left: auto;
    right: 60px;
    text-align: right;
}
.chat-body .chat-text {
    position: relative;
    line-height: 22px;
    top: 25px;
    padding: 8px 15px;
    background-color: #e2e2e2;
    border-radius: 3px;
    color: #333;
    word-break: break-all;
    max-width: 462px;
    font-size: 15px;
    display: inline-block;
}
.chat-body li.chat-mine .chat-text{
    margin-left: 0;
    text-align: left;
    background-color: #5FB878;
    color: #fff;
}
.chat-body .chat-text img {
    max-width: 100%;
    vertical-align: middle;
}
.chat-body .chat-text .timg {
    max-height: 300px;
}

.chat-body .chat-text:after {
    content: '';
    position: absolute;
    left: -10px;
    top: 13px;
    width: 0;
    height: 0;
    border-style: solid dashed dashed;
    border-color: #e2e2e2 transparent transparent;
    overflow: hidden;
    border-width: 10px;
}
.chat-body li.chat-mine .chat-text:after {
    left: auto; 
    right: -10px;
    border-top-color: #5FB878;
}

.chat-bottom {
    height: 170px;
    border-top: 1px #dcdee2 solid;
    border-bottom: 0;
    left: 0;
    bottom: 0;
    top: auto;
    padding-top: 0;
}

.chat-bottom .ivu-col{ line-height: 42px; text-align: center; cursor: pointer;}
.chat-bottom textarea{  font-size: 16px;}

.face-box{
    border: 1px solid #dcdee2;
    width: 202px;
    height: 162px;
    position: absolute;
    bottom: 42px;
    background: #fff;
}
.face-box li{
    list-style: none;
    width: 40px;
    height: 40px;
    font-size: 22px;
    display: inline-block;
    float: left;
    cursor: pointer;
}
</style>
