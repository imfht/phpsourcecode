<template>
    <div class="chat-index">
        <!--左边选项-->
        <ul class="chat-menu">
            <li class="self">
                <UserImg :info="usrInfo" class="avatar"/>
            </li>
            <li :class="{active:chatTap=='dialog'}" @click="[chatTap='dialog',chatTam='dialog']">
                <Icon type="md-text" />
                <em v-if="unreadTotal > 0" class="chat-num">{{unreadTotal > 99 ? '99+' : unreadTotal}}</em>
            </li>
            <li :class="{active:chatTap=='team'}" @click="[chatTap='team',chatTam='team']">
                <Icon type="md-person" />
            </li>
        </ul>

        <!--对话列表-->
        <ul class="chat-user" :class="{'chat-flex':chatTap=='dialog','chat-tam':chatTam=='dialog'}">
            <li class="sreach">
                <Input :placeholder="$L('搜索')" prefix="ios-search" v-model="dialogSearch"/>
            </li>
            <li ref="dialogLists" class="lists">
                <ul>
                    <li v-for="(dialog, index) in dialogListsS"
                        :key="index"
                        :class="{active:dialog.username==dialogTarget.username}"
                        :data-id="dialog.id"
                        @click="openDialog(dialog)">
                        <UserImg :info="dialog" class="avatar"/>
                        <div class="user-msg-box">
                            <div class="user-msg-title">
                                <span><UserView :username="dialog.username" placement="right" @on-result="userViewResult(dialog, $event)"/></span>
                                <em>{{formatCDate(dialog.lastdate)}}</em>
                            </div>
                            <div class="user-msg-text">{{dialog.lasttext}}</div>
                        </div>
                        <em v-if="dialog.unread > 0" class="user-msg-num">{{dialog.unread}}</em>
                    </li>
                    <li v-if="dialogNoDataText==$L('数据加载中.....')" class="chat-none"><w-loading/></li>
                    <li v-else-if="dialogLists.length == 0" class="chat-none">{{dialogNoDataText}}</li>
                </ul>
            </li>
        </ul>

        <!--联系人列表-->
        <ul class="chat-team" :class="{'chat-flex':chatTap=='team','chat-tam':chatTam=='team'}">
            <li class="sreach">
                <Input :placeholder="$L('搜索')" prefix="ios-search" v-model="teamSearch"/>
            </li>
            <li class="lists">
                <ul>
                    <li v-for="(lists, key) in teamLists" :key="key" v-if="teamListsS(lists).length > 0">
                        <div class="team-label">{{key}}</div>
                        <ul>
                            <li v-for="(item, index) in teamListsS(lists)" :key="index" @click="openDialog(item, true)">
                                <UserImg :info="item" class="avatar"/>
                                <div class="team-username">{{item.nickname||item.username}}</div>
                            </li>
                        </ul>
                    </li>
                    <li v-if="teamNoDataText==$L('数据加载中.....')" class="chat-none"><w-loading/></li>
                    <li v-else-if="Object.keys(teamLists).length == 0" class="chat-none">{{teamNoDataText}}</li>
                    <li v-if="teamHasMorePages" class="chat-more" @click="getTeamLists(true)">{{$L('加载更多...')}}</li>
                </ul>
            </li>
        </ul>

        <!--对话窗口-->
        <div class="chat-message"
             :style="{display:(chatTap=='dialog'&&dialogTarget.username)?'block':'none'}"
             @drop.prevent="messagePasteDrag($event, 'drag')"
             @dragover.prevent="messageDragOver(true)"
             @dragleave.prevent="messageDragOver(false)">
            <div class="manage-title">
                <UserView :username="dialogTarget.username"/>
                <Dropdown class="manage-title-right" placement="bottom-end" trigger="click" @on-click="dialogDropdown" transfer>
                    <Icon type="ios-more"/>
                    <DropdownMenu slot="list">
                        <DropdownItem name="delete">{{$L('删除对话')}}</DropdownItem>
                        <DropdownItem name="clear">{{$L('清除聊天记录')}}</DropdownItem>
                    </DropdownMenu>
                </Dropdown>
            </div>
            <ScrollerY ref="manageLists" class="manage-lists" @on-scroll="messageListsScroll">
                <div ref="manageBody" class="manage-body">
                    <div v-if="messageHasMorePages" class="manage-more" @click="getDialogMessage(true)">{{$L('加载更多...')}}</div>
                    <div v-if="messageNoDataText==$L('数据加载中.....')" class="manage-more"><w-loading/></div>
                    <div v-else-if="messageNoDataText" class="manage-more">{{messageNoDataText}}</div>
                    <chat-message v-for="(info, index) in messageLists" :key="index" :info="info"></chat-message>
                </div>
                <div class="manage-lists-message-new" v-if="messageNew > 0" @click="messageBottomGo(true)">{{$L('有%条新消息', messageNew)}}</div>
            </ScrollerY>
            <div class="manage-send" @click="clickDialog(dialogTarget.username)">
                <textarea ref="textarea" class="manage-input" maxlength="20000" v-model="messageText" :placeholder="$L('请输入要发送的消息')" @keydown="messageSend($event)" @paste="messagePasteDrag"></textarea>
            </div>
            <div class="manage-quick">
                <emoji-picker @emoji="messageInsertText" :search="messageEmojiSearch">
                    <div slot="emoji-invoker" slot-scope="{ events: { click: clickEvent } }" @click.stop="(event)=>{clickEvent(event)}">
                        <Tooltip :content="$L('表情')" placement="top">
                            <Icon class="quick-item" type="ios-happy-outline"  />
                        </Tooltip>
                    </div>
                    <div slot="emoji-picker" slot-scope="{ emojis, insert, display }">
                        <div class="emoji-box">
                            <Input class="emoji-input" :placeholder="$L('搜索')" v-model="messageEmojiSearch" prefix="ios-search"/>
                            <div>
                                <div v-for="(emojiGroup, category) in emojis" :key="category">
                                    <h5>{{ category }}</h5>
                                    <div class="emojis">
                                        <span v-for="(emoji, emojiName) in emojiGroup" :key="emojiName" @click="insert(emoji)" :title="emojiName">{{ emoji }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </emoji-picker>
                <Tooltip :content="$L('文件/图片')" placement="top">
                    <Icon class="quick-item" type="ios-photos-outline" @click="$refs.messageUpload.handleClick()"/>
                    <ChatLoad
                        ref="messageUpload"
                        class="message-upload"
                        :target="dialogTarget.username"
                        @on-progress="messageFile('progress', $event)"
                        @on-success="messageFile('success', $event)"
                        @on-error="messageFile('error', $event)"/>
                </Tooltip>
                <template v-if="systemConfig.callav=='open'">
                    <Tooltip :content="$L('语音聊天')" placement="top">
                        <Icon class="quick-item voicecam" type="ios-call-outline" @click="videoConnect(null, false)"/>
                    </Tooltip>
                    <Tooltip :content="$L('视频聊天')" placement="top">
                        <Icon class="quick-item videocam" type="ios-videocam-outline" @click="videoConnect(null, true)"/>
                    </Tooltip>
                </template>
            </div>
            <div v-if="dialogDragOver" class="manage-drag-over">
                <div class="manage-drag-text">{{$L('拖动到这里发送给 %', dialogTarget.nickname || dialogTarget.username)}}</div>
            </div>
        </div>

        <!--语音、视频通话-->
        <div class="chat-video" :style="{display:(videoUserName)?'block':'none',backgroundImage:'url(' + videoUserImg +')'}">
            <div v-if="videoChat" class="video-opacity">{{$L('正在视频通话...')}}</div>
            <div v-else class="video-opacity">{{$L('正在语音通话...')}}</div>
            <video ref="remoteVideo" class="video-active" autoplay></video>
            <video ref="localVideo" class="video-mini" autoplay muted="true"></video>
            <div class="video-close"><Icon type="ios-close-circle-outline" @click="videoClose(videoUserName)"/></div>
        </div>

        <!--提示音-->
        <audio class="chat-audio" ref="messageAudio" preload="none">
            <source :src="messageAudio + 'message.mp3'" type="audio/mpeg">
            <source :src="messageAudio + 'message.wav'" type="audio/wav">
        </audio>
        <audio class="chat-audio" ref="callAudio" preload="none">
            <source :src="messageAudio + 'call.mp3'" type="audio/mpeg">
            <source :src="messageAudio + 'call.wav'" type="audio/wav">
        </audio>

    </div>
</template>

<style lang="scss">
    .chat-notice-box {
        display: flex;
        align-items: flex-start;
        cursor: pointer;
        .chat-notice-userimg {
            width: 42px;
            height: 42px;
            font-size: 20px;
            line-height: 42px;
            border-radius: 4px;
        }
        .ivu-notice-with-desc {
            flex: 1;
            padding: 0 12px;
        }
        .chat-notice-btn-box {
            margin-top: 8px;
            margin-bottom: -4px;
            .ivu-btn {
                margin-right: 12px;
                font-size: 12px;
                min-width: 42px;
            }
        }
        .ivu-notice-desc {
            font-size: 13px;
            word-break: break-all;
            line-height: 1.3;
            text-overflow: ellipsis;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            overflow: hidden;
            -webkit-box-orient: vertical;
        }
        .chat-user {
            .user-msg-title {
                .user-view-inline {
                    .user-view-name {
                        max-width: 100%;
                        white-space: nowrap;
                        overflow: hidden;
                        text-overflow: ellipsis;
                    }
                }
            }
        }
        .chat-message {
            .manage-title {
                .user-view-inline {
                    max-width: 70%;
                    .user-view-name {
                        max-width: 100%;
                        white-space: nowrap;
                        overflow: hidden;
                        text-overflow: ellipsis;
                    }
                }
            }
        }
    }
</style>
<style lang="scss" scoped>
    .chat-index {
        display: flex;
        flex-direction: row;
        align-items: flex-start;
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        .chat-none {
            height: auto;
            color: #666666;
            padding: 22px 8px;
            text-align: center;
            justify-content: center;
            margin: 0 !important;
            &:before {
                display: none;
            }
        }
        .chat-more {
            color: #666666;
            padding: 18px 0;
            text-align: center;
            cursor: pointer;
            margin: 0 !important;
            &:hover {
                color: #444444;
            }
        }
        .chat-menu {
            background-color: rgba(28, 29, 31, 0.92);
            width: 68px;
            height: 100%;
            padding-top: 20px;
            li {
                position: relative;
                padding: 12px 0;
                text-align: center;
                font-size: 28px;
                color: #919193;
                background-color: transparent;
                cursor: pointer;
                &.self {
                    cursor: default;
                    .avatar {
                        width: 36px;
                        height: 36px;
                        font-size: 20px;
                        border-radius: 3px;
                    }
                }
                &.active {
                    color: #ffffff;
                    background-color: rgba(255, 255, 255, 0.06);
                }
                &:hover {
                    > i {
                        transform: scale(1.1);
                    }
                }
                > i {
                    transition: all 0.2s;
                }
                .chat-num {
                    position: absolute;
                    top: 50%;
                    left: 50%;
                    height: auto;
                    line-height: normal;
                    color: #ffffff;
                    background-color: #ff0000;
                    text-align: center;
                    border-radius: 10px;
                    padding: 1px 5px;
                    font-size: 12px;
                    transform: scale(0.9) translate(5px, -20px);
                }
            }
        }
        .chat-user {
            display: none;
            flex-direction: column;
            width: 248px;
            height: 100%;
            background-color: #ffffff;
            border-right: 1px solid #ededed;
            > li {
                position: relative;
                &.sreach {
                    display: flex;
                    flex-direction: row;
                    align-items: center;
                    height: 62px;
                    margin: 0;
                    padding: 0 12px;
                    position: relative;
                    cursor: pointer;

                    &:before {
                        content: "";
                        position: absolute;
                        left: 0;
                        right: 0;
                        bottom: 0;
                        height: 1px;
                        background-color: rgba(0, 0, 0, 0.06);
                    }
                }
                &.lists {
                    flex: 1;
                    overflow: auto;
                    transform: translateZ(0);
                    > ul {
                        > li {
                            display: flex;
                            flex-direction: row;
                            align-items: center;
                            height: 70px;
                            padding: 0 12px;
                            position: relative;
                            cursor: pointer;
                            &:before {
                                content: "";
                                position: absolute;
                                left: 0;
                                right: 0;
                                bottom: 0;
                                height: 1px;
                                background-color: rgba(0, 0, 0, 0.06);
                            }
                            &.active {
                                &:before {
                                    top: 0;
                                    height: 100%;
                                }
                            }
                            .avatar {
                                width: 42px;
                                height: 42px;
                                font-size: 20px;
                                border-radius: 4px;
                            }
                            .user-msg-box {
                                flex: 1;
                                display: flex;
                                flex-direction: column;
                                padding-left: 12px;
                                .user-msg-title {
                                    display: flex;
                                    flex-direction: row;
                                    align-items: center;
                                    justify-content: space-between;
                                    line-height: 24px;
                                    span {
                                        flex: 1;
                                        max-width: 130px;
                                        color: #333333;
                                        font-size: 14px;
                                        white-space: nowrap;
                                        overflow: hidden;
                                        text-overflow: ellipsis;
                                    }
                                    em {
                                        color: #999999;
                                        font-size: 12px;
                                    }
                                }
                                .user-msg-text {
                                    max-width: 170px;
                                    color: #999999;
                                    font-size: 12px;
                                    line-height: 24px;
                                    white-space: nowrap;
                                    overflow: hidden;
                                    text-overflow: ellipsis;
                                }
                            }
                        }
                    }
                }
                .user-msg-num {
                    position: absolute;
                    top: 6px;
                    left: 44px;
                    height: auto;
                    line-height: normal;
                    color: #ffffff;
                    background-color: #ff0000;
                    text-align: center;
                    border-radius: 10px;
                    padding: 1px 5px;
                    font-size: 12px;
                    transform: scale(0.9);
                    border: 1px solid #ffffff;
                }
            }
        }
        .chat-team {
            display: none;
            flex-direction: column;
            width: 248px;
            height: 100%;
            background-color: #ffffff;
            border-right: 1px solid #ededed;
            > li {
                position: relative;
                &.sreach {
                    display: flex;
                    flex-direction: row;
                    align-items: center;
                    height: 62px;
                    margin: 0;
                    padding: 0 12px;
                    position: relative;
                    cursor: pointer;

                    &:before {
                        content: "";
                        position: absolute;
                        left: 0;
                        right: 0;
                        bottom: 0;
                        height: 1px;
                        background-color: rgba(0, 0, 0, 0.06);
                    }
                }
                &.lists {
                    flex: 1;
                    overflow: auto;
                    transform: translateZ(0);
                    > ul {
                        > li {
                            margin-left: 24px;
                            position: relative;
                            .team-label {
                                padding-left: 4px;
                                margin-top: 6px;
                                margin-bottom: 6px;
                                height: 34px;
                                line-height: 34px;
                                border-bottom: 1px solid #efefef;
                            }
                            > ul {
                                > li {
                                    display: flex;
                                    flex-direction: row;
                                    align-items: center;
                                    height: 52px;
                                    cursor: pointer;
                                    .avatar {
                                        width: 30px;
                                        height: 30px;
                                        font-size: 16px;
                                        border-radius: 3px;
                                    }
                                    .team-username {
                                        padding: 0 12px;
                                        font-size: 14px;
                                        white-space: nowrap;
                                        overflow: hidden;
                                        text-overflow: ellipsis;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        .chat-message {
            flex: 1;
            height: 100%;
            background-color: #F3F3F3;
            position: relative;
            .manage-title {
                position: absolute;
                top: 0;
                left: 0;
                z-index: 3;
                width: 100%;
                height: 62px;
                padding: 0 20px;
                line-height: 62px;
                font-size: 16px;
                font-weight: 500;
                text-align: left;
                background: #ffffff;
                border-bottom: 1px solid #ededed;
                .manage-title-right {
                    position: absolute;
                    top: 0;
                    right: 0;
                    z-index: 9;
                    width: 62px;
                    height: 62px;
                    line-height: 62px;
                    text-align: center;
                    font-size: 22px;
                    color: #242424;
                }
            }
            .manage-lists {
                position: absolute;
                left: 0;
                top: 62px;
                z-index: 1;
                bottom: 120px;
                width: 100%;
                overflow: auto;
                padding: 8px 0;
                background-color: #E8EBF2;
                .manage-more {
                    color: #666666;
                    padding: 8px 0;
                    text-align: center;
                    cursor: pointer;
                    &:hover {
                        color: #444444;
                    }
                }
                .manage-lists-message-new {
                    position: fixed;
                    bottom: 130px;
                    right: 20px;
                    color: #ffffff;
                    background-color: rgba(0, 0, 0, 0.6);
                    padding: 6px 12px;
                    border-radius: 16px;
                    font-size: 12px;
                    cursor: pointer;
                }
            }
            .manage-send {
                position: absolute;
                left: 0;
                bottom: 0;
                z-index: 2;
                display: flex;
                width: 100%;
                height: 120px;
                background-color: #ffffff;
                border-top: 1px solid #e4e4e4;
                .manage-input,.manage-input:focus {
                    flex: 1;
                    -webkit-appearance: none;
                    font-size: 14px;
                    box-sizing: border-box;
                    padding: 0;
                    margin: 38px 10px 6px;
                    border: 0;
                    line-height: 20px;
                    box-shadow: none;
                    resize:none;
                    outline: 0;
                }
                .manage-join,
                .manage-spin {
                    position: absolute;
                    top: 0;
                    left: 0;
                    right: 0;
                    bottom: 0;
                    background: #ffffff;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                }
            }
            .manage-quick {
                position: absolute;
                z-index: 2;
                left: 0;
                right: 0;
                bottom: 79px;
                padding: 8px 0;
                display: flex;
                align-items: center;
                height: 40px;
                .quick-item {
                    color: #444444;
                    font-size: 24px;
                    margin: 0 7px;
                    &.voicecam {
                        font-size: 26px;
                        height: 24px;
                        line-height: 24px;
                    }
                    &.videocam {
                        color: #666666;
                        font-size: 30px;
                        height: 24px;
                        line-height: 24px;
                    }
                }
                .emoji-box {
                    position: absolute;
                    left: 0;
                    bottom: 40px;
                    max-height: 320px;
                    width: 100%;
                    overflow: auto;
                    background-color: #ffffff;
                    padding: 12px;
                    border-bottom: 1px solid #efefef;
                    .emoji-input {
                        margin: 6px 0;
                    }
                    h5 {
                        padding: 0;
                        margin: 8px 0 0 0;
                        color: #b1b1b1;
                        text-transform: uppercase;
                        font-size: 14px;
                        cursor: default;
                        font-weight: normal;
                    }
                    .emojis {
                        display: flex;
                        flex-wrap: wrap;
                        justify-content: space-between;
                        &:after {
                            content: "";
                            flex: auto;
                        }
                        span {
                            padding: 2px 4px;
                            cursor: pointer;
                            font-size: 22px;
                            &:hover {
                                background: #ececec;
                                cursor: pointer;
                            }
                        }
                    }
                }
                .message-upload {
                    display: none;
                    width: 0;
                    height: 0;
                    overflow: hidden;
                }
            }
            .manage-drag-over {
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                z-index: 3;
                background-color: rgba(255, 255, 255, 0.78);
                display: flex;
                align-items: center;
                justify-content: center;
                &:before {
                    content: "";
                    position: absolute;
                    top: 16px;
                    left: 16px;
                    right: 16px;
                    bottom: 16px;
                    border: 2px dashed #7b7b7b;
                    border-radius: 12px;
                }
                .manage-drag-text {
                    padding: 12px;
                    font-size: 18px;
                    color: #666666;
                }
            }
            @media screen and (max-width: 768px) {
                .manage-lists {
                    bottom: 96px;
                    .manage-lists-message-new {
                        bottom: 106px;
                    }
                }
                .manage-send {
                    height: 96px;
                }
                .manage-quick {
                    bottom: 54px;
                    .quick-item {
                        font-size: 24px;
                        margin-right: 8px;
                    }
                }
            }
        }
        .chat-video {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: #000000;
            background-size: cover;
            background-position: center center;
            background-repeat: no-repeat;
            z-index: 9;
            &:before {
                content: "";
                position: absolute;
                left: -10%;
                right: -10%;
                top: -10%;
                bottom: -10%;
                background: inherit;
                filter: blur(25px);
                z-index: 1;
            }
            &:after {
                content: "";
                position: absolute;
                left: -10%;
                right: -10%;
                top: -10%;
                bottom: -10%;
                background: rgba(0, 0, 0, 0.82);
                z-index: 2;
            }
            .video-opacity {
                position: absolute;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
                font-size: 26px;
                color: #aaaaaa;
                padding: 24px;
                z-index: 3;
                animation:opacity 2s infinite alternate ;
                @keyframes opacity {
                    0% {
                        opacity: 0.1;
                    }
                    100% {
                        opacity: 1;
                    }
                }
            }
            .video-mini,
            .video-active {
                position: absolute;
                max-width: 640px;
                max-height: 100%;
                object-fit: cover;
                transition: opacity 1s;
            }
            .video-active {
                top: 0;
                left: 50%;
                width: 100%;
                height: 100%;
                transform: rotateY(180deg) translateX(50%);
                z-index: 4;
            }
            .video-mini {
                top: 0;
                right: 0;
                width: 260px;
                height: 180px;
                transform: scale(-1, 1);
                z-index: 5;
            }
            .video-close {
                position: absolute;
                max-width: 720px;
                bottom: 18px;
                left: 50%;
                width: 100%;
                transform: translateX(-50%);
                display: flex;
                align-items: center;
                justify-content: center;
                z-index: 6;
                > i {
                    font-weight: 600;
                    font-size: 46px;
                    color: #ffffff;
                    cursor: pointer;
                    &:hover {
                        color: #ff0000;
                    }
                }
            }
        }
        .chat-audio {
            width: 0;
            height: 0;
            display: none;
        }
        .chat-flex {
            display: flex;
        }
        @media (max-width: 768px) {
            .chat-menu {
                width: 50px;
                li {
                    padding: 6px 0;
                    margin: 6px 0;
                    font-size: 24px;
                    &.self {
                        .avatar {
                            width: 32px;
                            height: 32px;
                            font-size: 18px;
                        }
                    }
                }
            }
            .chat-user,
            .chat-team {
                display: none;
                position: absolute;
                left: 50px;
                right: 0;
                z-index: 2;
                width: auto;
                > li {
                    &.sreach {
                        height: 54px;
                    }
                }
                &.chat-tam {
                    display: flex;
                }
            }
            .chat-message {
                .manage-title {
                    height: 54px;
                    line-height: 54px;
                    .manage-title-right {
                        width: 54px;
                        height: 54px;
                        line-height: 54px;
                    }
                }
                .manage-lists {
                    top: 54px;
                }
            }
        }
    }
</style>

<script>
    import EmojiPicker from 'vue-emoji-picker'
    import DrawerTabsContainer from "../DrawerTabsContainer";
    import ScrollerY from "../../../_components/ScrollerY";
    import ChatMessage from "./Message";
    import ImgUpload from "../ImgUpload";
    import ChatLoad from "./Upload";

    export default {
        name: 'ChatIndex',
        components: {ChatLoad, ImgUpload, ChatMessage, EmojiPicker, ScrollerY, DrawerTabsContainer},
        props: {
            value: {
                default: 0
            },
            openWindow: {
                type: Boolean,
                default: false
            },
        },
        data () {
            return {
                loadIng: 0,

                openAlready: false,

                chatTap: 'dialog',
                chatTam: 'dialog',

                messageAudio: window.location.origin + '/audio/',

                dialogSearch: '',
                dialogTarget: {},
                dialogLists: [],
                dialogNoDataText: '',
                dialogDragOver: false,

                teamSearch: '',
                teamReady: false,
                teamLists: {},
                teamNoDataText: '',
                teamCurrentPage: 1,
                teamHasMorePages: false,

                autoBottom: true,
                messageNew: 0,
                messageText: '',
                messageLists: [],
                messageNoDataText: '',
                messageEmojiSearch: '',
                messageCurrentPage: 1,
                messageHasMorePages: false,

                unreadTotal: 0,

                videoUserName: '',      //视频对话用户名
                videoUserImg: '',       //视频对话用户头像
                videoStartTime: 0,      //视频开始时间
                videoInitiator: false,  //是否发起人
                videoChat: false,       //是否视频通话（否则音频通话）
                videoRtc: null,         //视频Rtc
                videoLocalStream: null, //视频流

                systemConfig: $A.jsonParse($A.storage("systemSetting"), {
                    callav: '',
                }),
            }
        },

        created() {
            this.dialogNoDataText = this.$L("数据加载中.....");
            this.teamNoDataText = this.$L("数据加载中.....");
            this.messageNoDataText = this.$L("数据加载中.....");
        },

        mounted() {
            this.formatCall();
            this.getSetting();
            //
            window.onChatOpenUserName = (username) => {
                this.$emit("on-open-notice", username);
                this.clickDialog(username, true);
            }
            //
            if (this.openWindow) {
                $A.WSOB.connection();
                if (!this.openAlready) {
                    this.openAlready = true;
                    this.getDialogLists();
                }
            }
            //
            $A.WSOB.setOnMsgListener("chat/index", (msgDetail) => {
                if (msgDetail.username == this.usrName) {
                    return;
                }
                switch (msgDetail.messageType) {
                    case 'open':
                        if (this.openWindow) {
                            this.getDialogLists();
                            this.getDialogMessage();
                        } else {
                            this.openAlready = false;
                            this.dialogTarget = {};
                        }
                        break;
                    case 'info':
                        if (msgDetail.body.type == 'video') {
                            this.videoMessage(msgDetail)
                        }
                        break;
                    case 'user':
                        let body = msgDetail.body;
                        if (['taskA'].indexOf(body.type) !== -1) {
                            return;
                        }
                        let lasttext = $A.WSOB.getMsgDesc(body);
                        this.unreadTotal += 1;
                        this.addDialog({
                            username: body.username,
                            userimg: body.userimg,
                            lasttext: lasttext,
                            lastdate: body.indate,
                            unread: body.unread
                        });
                        if (msgDetail.username == this.dialogTarget.username) {
                            this.addMessageData(body, true);
                        }
                        if (!this.openWindow) {
                            this.$Notice.close('chat-notice');
                            this.$Notice.open({
                                name: 'chat-notice',
                                duration: 0,
                                render: h => {
                                    return h('div', {
                                        class: 'chat-notice-box',
                                        on: {
                                            click: () => {
                                                this.$Notice.close('chat-notice');
                                                this.$emit("on-open-notice", body.username);
                                                this.clickDialog(body.username);
                                            }
                                        }
                                    }, [
                                        h('UserImg', {class: 'chat-notice-userimg', props: {info: body}}),
                                        h('div', {class: 'ivu-notice-with-desc'}, [
                                            h('div', {class: 'ivu-notice-title'}, [
                                                h('UserView', {props: {username: body.username}})
                                            ]),
                                            h('div', {class: 'ivu-notice-desc'}, lasttext)
                                        ])
                                    ])
                                }
                            });
                        }
                        try {
                            this.$refs.messageAudio.play();
                        } catch (e) {

                        }
                        break;
                }
            });
            $A.WSOB.setOnSpecialListener("chat/index", (simpleMsg) => {
                this.addDialog({
                    username: simpleMsg.target,
                    lasttext: $A.WSOB.getMsgDesc(simpleMsg.body),
                    lastdate: simpleMsg.body.indate
                });
                if (simpleMsg.target == this.dialogTarget.username) {
                    this.addMessageData(simpleMsg.body, true);
                }
            });
        },

        watch: {
            usrName() {
                this.formatCall();
            },

            chatTap(val) {
                if (val === 'team' && this.teamReady == false) {
                    this.teamReady = true;
                    this.getTeamLists();
                } else if (val === 'dialog') {
                    this.autoBottom = true;
                    this.$nextTick(() => {
                        this.messageBottomGo();
                    });
                }
            },

            openWindow(val) {
                if (val) {
                    $A.WSOB.connection();
                    if (!this.openAlready) {
                        this.openAlready = true;
                        this.getDialogLists();
                    }
                }
                //
                let tmpRand = $A.randomString(8);
                this.__openWindowRand = tmpRand;
                setTimeout(() => {
                    if (this.__openWindowRand !== tmpRand) {
                        return;
                    }
                    $A.WSOB.sendTo('unread', (res) => {
                        if (res.status === 1) {
                            this.unreadTotal = $A.runNum(res.message);
                        } else {
                            this.unreadTotal = 0;
                        }
                    });
                }, 500);
            },

            unreadTotal(val) {
                if (val < 0) {
                    this.unreadTotal = 0;
                    return;
                }
                this.$emit('input', val);
            },

            dialogTarget: {
                handler: function () {
                    let username = this.dialogTarget.username;
                    if (username === this.__dialogTargetUsername) {
                        return;
                    }
                    this.__dialogTargetUsername = username;
                    this.getDialogMessage();
                },
                deep: true
            }
        },

        computed: {
            dialogListsS() {
                return this.dialogLists.filter(item => {
                    return (item.username + "").indexOf(this.dialogSearch) > -1 || (item.lasttext + "").indexOf(this.dialogSearch) > -1 || (item.nickname + "").indexOf(this.dialogSearch) > -1
                });
            },
            teamListsS() {
                return function (lists) {
                    return lists.filter(item => {
                        return (item.username + "").indexOf(this.teamSearch) > -1 || (item.nickname + "").indexOf(this.teamSearch) > -1
                    });
                }
            }
        },

        methods: {
            formatCall() {
                if ($A.getToken() === false) {
                    return;
                }
                $A.WSOB.sendTo('unread', (res) => {
                    if (res.status === 1) {
                        this.unreadTotal = $A.runNum(res.message);
                    } else {
                        this.unreadTotal = 0;
                    }
                });
                this.getDialogLists();
                this.messageBottomAuto();
            },

            getSetting() {
                $A.apiAjax({
                    url: 'system/setting',
                    error: () => {
                        $A.storage("systemSetting", {});
                    },
                    success: (res) => {
                        if (res.ret === 1) {
                            this.systemConfig = res.data;
                            this.systemConfig.callav = this.systemConfig.callav || 'open';
                            $A.storage("systemSetting", this.systemConfig);
                        } else {
                            $A.storage("systemSetting", {});
                        }
                    }
                });
            },

            formatCDate(v) {
                let string = '';
                if ($A.runNum(v) > 0) {
                    if ($A.formatDate('Ymd') === $A.formatDate('Ymd', v)) {
                        string = $A.formatDate('H:i', v)
                    } else if ($A.formatDate('Y') === $A.formatDate('Y', v)) {
                        string = $A.formatDate('m-d', v)
                    } else {
                        string = $A.formatDate('Y-m-d', v)
                    }
                }
                return string || '';
            },

            getDialogLists() {
                if (!this.openAlready) {
                    return;
                }
                this.loadIng++;
                this.dialogNoDataText = this.$L("数据加载中.....");
                $A.apiAjax({
                    url: 'chat/dialog/lists',
                    complete: () => {
                        this.loadIng--;
                    },
                    error: () => {
                        this.dialogNoDataText = this.$L("数据加载失败！");
                    },
                    success: (res) => {
                        if (res.ret === 1) {
                            this.dialogLists = res.data;
                            this.dialogNoDataText = this.$L("没有相关的数据");
                            this.scrollToActive();
                        } else {
                            this.dialogLists = [];
                            this.dialogNoDataText = res.msg
                        }
                    }
                });
            },

            getDialogMessage(isNextPage = false) {
                let username = this.dialogTarget.username;
                if (!username) {
                    return;
                }
                //
                if (isNextPage === true) {
                    if (!this.messageHasMorePages) {
                        return;
                    }
                    this.messageCurrentPage+= 1;
                } else {
                    this.messageCurrentPage = 1;
                    this.autoBottom = true;
                    this.messageNew = 0;
                    this.messageLists = [];
                }
                this.messageHasMorePages = false;
                //
                this.loadIng++;
                this.messageNoDataText = this.$L("数据加载中.....");
                $A.apiAjax({
                    url: 'chat/message/lists',
                    data: {
                        username: username,
                        page: this.messageCurrentPage,
                        pagesize: 30
                    },
                    complete: () => {
                        this.loadIng--;
                    },
                    error: () => {
                        this.messageNoDataText = this.$L("数据加载失败！");
                    },
                    success: (res) => {
                        if (username != this.dialogTarget.username) {
                            return;
                        }
                        if (res.ret === 1) {
                            let tempId = "notice_" + $A.randomString(6);
                            let tempLists = res.data.lists;
                            if (isNextPage) {
                                this.addMessageData({
                                    id: tempId,
                                    type: 'notice',
                                    notice: this.$L('历史消息'),
                                }, false, isNextPage);
                            } else {
                                tempLists = tempLists.reverse();
                            }
                            tempLists.forEach((item) => {
                                this.addMessageData(Object.assign(item.message, {
                                    id: item.id,
                                    username: item.username,
                                    userimg: item.userimg,
                                    indate: item.indate,
                                }), false, isNextPage);
                            });
                            if (isNextPage) {
                                this.$nextTick(() => {
                                    let tempObj = $A('div[data-id="' + tempId + '"]');
                                    if (tempObj.length > 0) {
                                        this.$refs.manageLists.scrollTo(tempObj.offset().top - tempObj.height() - 24, false);
                                    }
                                });
                            }
                            this.messageNoDataText = '';
                            this.messageHasMorePages = res.data.hasMorePages;
                        } else {
                            this.messageNoDataText = res.msg
                            this.messageHasMorePages = false;
                        }
                    }
                });
            },

            getTeamLists(isNextPage = false) {
                if (isNextPage === true) {
                    if (!this.teamHasMorePages) {
                        return;
                    }
                    this.teamCurrentPage+= 1;
                } else {
                    this.teamCurrentPage = 1;
                }
                this.teamHasMorePages = false;
                //
                this.loadIng++;
                this.teamNoDataText = this.$L("数据加载中.....");
                $A.apiAjax({
                    url: 'users/team/lists',
                    data: {
                        sorts: {
                            key: 'az',
                            order: 'asc'
                        },
                        page: this.teamCurrentPage,
                        pagesize: 100,
                    },
                    complete: () => {
                        this.loadIng--;
                    },
                    error: () => {
                        this.teamNoDataText = this.$L("数据加载失败！");
                    },
                    success: (res) => {
                        if (res.ret === 1) {
                            res.data.lists.forEach((item) => {
                                if (typeof this.teamLists[item.az] === "undefined") {
                                    this.$set(this.teamLists, item.az, []);
                                }
                                this.teamLists[item.az].push(item);
                            });
                            this.teamNoDataText = this.$L("没有相关的数据");
                            this.teamHasMorePages = res.data.hasMorePages;
                            //
                            if (this.teamHasMorePages && res.data.currentPage < 5) {
                                this.getTeamLists(true);
                            }
                        } else {
                            this.teamLists = {};
                            this.teamNoDataText = res.msg
                            this.teamHasMorePages = false;
                        }
                    }
                });
            },

            addDialog(data) {
                if (!data.username) {
                    return;
                }
                let lists = this.dialogLists.filter((item) => {return item.username == data.username});
                let unread = 0;
                if (lists.length > 0) {
                    if (typeof data.userimg === "undefined") {
                        data.userimg = lists[0].userimg;
                    }
                    unread = $A.runNum(lists[0].unread);
                    this.dialogLists = this.dialogLists.filter((item) => {return item.username != data.username});
                }
                if (typeof data.unread === "undefined") {
                    data.unread = unread;
                }
                this.dialogLists.unshift(data);
            },

            openDialog(user, autoAddDialog = false) {
                if (autoAddDialog === true) {
                    let lists = this.dialogLists.filter((item) => {return item.username == user.username});
                    if (lists.length === 0) {
                        this.addDialog(user);
                    }
                }
                this.chatTap = 'dialog';
                this.chatTam = '';
                this.dialogTarget = user;
                if (typeof user.unread === "number" && user.unread > 0) {
                    this.unreadTotal -= user.unread;
                    this.$set(user, 'unread', 0);
                    $A.WSOB.sendTo('read', user.username);
                }
                if (autoAddDialog === true) {
                    this.scrollToActive();
                }
            },

            scrollToActive() {
                //自动滚到焦点
                this.$nextTick(() => {
                    let dialogObj = $A(this.$refs.dialogLists);
                    let activeObj = dialogObj.find("li.active");
                    if (activeObj.length > 0) {
                        let offsetTop = activeObj.offset().top;
                        if (offsetTop < 0) {
                            dialogObj.stop().scrollTop(activeObj[0].offsetTop - 50)
                        } else if (offsetTop > dialogObj.height()) {
                            dialogObj.stop().scrollTop(activeObj[0].offsetTop + 50 + activeObj.height() - dialogObj.height())
                        }
                    }
                });
            },

            clickDialog(username, autoPush = false) {
                let lists = this.dialogLists.filter((item) => {return item.username == username});
                if (lists.length > 0) {
                    this.openDialog(lists[0]);
                    if (autoPush === true) {
                        this.scrollToActive();
                    }
                } else if (autoPush === true) {
                    $A.apiAjax({
                        url: 'users/team/lists',
                        data: {
                            username: username,
                        },
                        success: (res) => {
                            if (res.ret === 1 && $A.isPlainObject(res.data)) {
                                this.$nextTick(() => {
                                    typeof this.dialogTarget.username === "undefined" && this.openDialog(res.data, true)
                                });
                            }
                        }
                    });
                }
            },

            dialogDropdown(type) {
                switch (type) {
                    case 'clear':
                    case 'delete':
                        this.$Modal.confirm({
                            title: this.$L('确认操作'),
                            content: type === 'delete' ? this.$L('你确定要删除此对话吗？') : this.$L('你确定要清除聊天记录吗？'),
                            loading: true,
                            onOk: () => {
                                let username = this.dialogTarget.username;
                                $A.apiAjax({
                                    url: 'chat/dialog/clear',
                                    data: {
                                        username: username,
                                        delete: type === 'delete' ? 1 : 0
                                    },
                                    error: () => {
                                        this.$Modal.remove();
                                        alert(this.$L('网络繁忙，请稍后再试！'));
                                    },
                                    success: (res) => {
                                        this.$Modal.remove();
                                        if (res.ret === 1) {
                                            if (type === 'delete') {
                                                this.dialogLists = this.dialogLists.filter((item) => {return item.username != username});
                                                this.dialogTarget = {};
                                            } else {
                                                this.$set(this.dialogTarget, 'lasttext', '');
                                                this.getDialogMessage();
                                            }
                                        }
                                        setTimeout(() => {
                                            if (res.ret === 1) {
                                                this.$Message.success(res.msg);
                                            } else {
                                                this.$Modal.error({title: this.$L('温馨提示'), content: res.msg});
                                            }
                                        }, 350);
                                    }
                                });
                            }
                        });
                        break;
                }
            },

            messageListsScroll(res) {
                if (res.directionreal === 'up') {
                    if (res.scrollE < 10) {
                        this.autoBottom = true;
                    }
                } else if (res.directionreal === 'down') {
                    this.autoBottom = false;
                }
            },

            messageBottomAuto() {
                let randString = $A.randomString(8);
                window.__messageBottomAuto = randString;
                setTimeout(() => {
                    if (randString === window.__messageBottomAuto) {
                        window.__messageBottomAuto = null;
                        if (this.autoBottom) {
                            this.messageBottomGo();
                        }
                        this.messageBottomAuto();
                    }
                }, 1000);
            },

            messageBottomGo(animation = false) {
                this.$nextTick(() => {
                    this.messageNew = 0;
                    if (typeof this.$refs.manageLists !== "undefined") {
                        this.$refs.manageLists.scrollTo(this.$refs.manageBody.clientHeight, animation);
                        this.autoBottom = true;
                    }
                });
            },

            messageInsertText(emoji) {
                this.messageText+= emoji;
            },

            messageInsertFile(item) {
                if (typeof item === 'object' && typeof item.url === "string") {
                    let data = {
                        type: ['jpg', 'jpeg', 'png', 'gif'].indexOf(item.ext) !== -1 ? 'image' : 'file',
                        filename: item.name,
                        filesize: item.size,
                        filethumb: item.thumb,
                        username: this.usrInfo.username,
                        userimg: this.usrInfo.userimg,
                        indate: Math.round(new Date().getTime() / 1000),
                        url: item.url,
                        width: $A.getObject(item, 'response.data.width'),
                        height: $A.getObject(item, 'response.data.height'),
                        replaceId: item.tempId,
                    };
                    $A.WSOB.sendTo('user', this.dialogTarget.username, data, (res) => {
                        this.$set(data, res.status === 1 ? 'id' : 'error', res.message)
                    });
                    //
                    this.addDialog(Object.assign(this.dialogTarget, {
                        lasttext: this.$L(data.type == 'image' ? '[图片]' : '[文件]'),
                        lastdate: data.indate
                    }));
                    this.openDialog(this.dialogTarget);
                    this.addMessageData(data, true);
                }
            },

            addMessageData(data, animation = false, isUnshift = false) {
                data.self = data.username === this.usrInfo.username;
                let sikp = false;
                if (data.id || data.replaceId) {
                    this.messageLists.some((item, index) => {
                        if (item.id == data.id || item.id == data.replaceId) {
                            data.nickname = data.nickname || item.nickname;
                            this.messageLists.splice(index, 1, data);
                            return sikp = true;
                        }
                    });
                    if (sikp) {
                        return;
                    }
                }
                if (isUnshift) {
                    this.messageLists.unshift(data);
                } else {
                    this.messageLists.push(data);
                    if (this.autoBottom) {
                        this.messageBottomGo(animation);
                    } else {
                        this.messageNew++;
                    }
                }
            },

            messageSend(e) {
                if (e.keyCode == 13) {
                    if (e.shiftKey) {
                        return;
                    }
                    e.preventDefault();
                    this.messageSubmit();
                }
            },

            messageDragOver(show) {
                let random = (this.__dialogDragOver = $A.randomString(8));
                if (!show) {
                    setTimeout(() => {
                        if (random === this.__dialogDragOver) {
                            this.dialogDragOver = show;
                        }
                    }, 150);
                } else {
                    this.dialogDragOver = show;
                }
            },

            messagePasteDrag(e, type) {
                this.dialogDragOver = false;
                const files = type === 'drag' ? e.dataTransfer.files : e.clipboardData.files;
                const postFiles = Array.prototype.slice.call(files);
                if (postFiles.length > 0) {
                    e.preventDefault();
                    postFiles.forEach((file) => {
                        this.$refs.messageUpload.upload(file);
                    });
                }
            },

            messageFile(type, file) {
                switch (type) {
                    case 'progress':
                        this.addMessageData({
                            id: file.tempId,
                            type: 'image',
                            username: this.usrInfo.username,
                            userimg: this.usrInfo.userimg,
                            indate: Math.round(new Date().getTime() / 1000),
                            url: 'loading',
                        }, true);
                        break;
                    case 'error':
                        this.messageLists.some((item, index) => {
                            if (item.id == file.tempId) {
                                this.messageLists.splice(index, 1);
                                return true;
                            }
                        });
                        break;
                    case 'success':
                        this.messageInsertFile(file);
                        break;
                }
            },

            messageSubmit() {
                let dialogUser = this.dialogLists.filter((item) => { return item.username == this.dialogTarget.username });
                if (dialogUser.length > 0) {
                    let user = dialogUser[0];
                    if (typeof user.unread === "number" && user.unread > 0) {
                        this.unreadTotal -= user.unread;
                        this.$set(user, 'unread', 0);
                        $A.WSOB.sendTo('read', user.username);
                    }
                }
                //
                this.autoBottom = true;
                let text = this.messageText.trim();
                if ($A.count(text) > 0) {
                    let data = {
                        type: 'text',
                        username: this.usrInfo.username,
                        userimg: this.usrInfo.userimg,
                        indate: Math.round(new Date().getTime() / 1000),
                        text: text
                    };
                    $A.WSOB.sendTo('user', this.dialogTarget.username, data, (res) => {
                        this.$set(data, res.status === 1 ? 'id' : 'error', res.message)
                    });
                    //
                    this.addDialog(Object.assign(this.dialogTarget, {
                        lasttext: text,
                        lastdate: data.indate
                    }));
                    this.openDialog(this.dialogTarget);
                    this.addMessageData(data, true);
                }
                this.$nextTick(() => {
                    this.messageText = "";
                });
            },

            userViewResult(user, data) {
                this.$set(user, 'nickname', data.nickname);
                this.$set(user, 'userimg', data.userimg);
            },

            videoConnect(username, videoChat) {
                if (!$A.leftExists(window.location.protocol, "https")) {
                    this.$Modal.warning({title: this.$L('温馨提示'), content: this.$L('浏览器阻止音视频访问不是https的网站，所以尝试安装SSL！')});
                    return;
                }
                this.videoChat = videoChat;
                navigator.getUserMedia = navigator.getUserMedia || navigator.webkitGetUserMedia || navigator.mozGetUserMedia;
                try {
                    navigator.mediaDevices.getUserMedia({
                        audio: true,
                        video: this.videoChat
                    }).then((stream) => {
                        this.videoLocalStream = stream;
                        this.$refs.localVideo.srcObject = stream;
                        this.$refs.localVideo.removeEventListener('loadedmetadata', this.videoListener);
                        //
                        if (username === null) {
                            // 发起者
                            this.videoUserName = this.dialogTarget.username;
                            this.videoUserImg = this.dialogTarget.userimg;
                            this.videoStartTime = 0;
                            this.videoInitiator = true;
                            this.$refs.localVideo.addEventListener('loadedmetadata', this.videoListener);
                        } else {
                            // 接受者
                            this.videoIcecandidate(this.videoLocalStream, username);
                            this.videoRtc.createOffer({
                                offerToReceiveAudio: true,
                                offerToReceiveVideo: this.videoChat
                            }).then((desc) => {
                                this.videoRtc.setLocalDescription(desc).then(() => {
                                    $A.WSOB.sendTo('info', username, {
                                        'type': 'video',
                                        'subtype': 'offer',
                                        'data': this.videoRtc.localDescription
                                    });
                                }).catch((e) => {
                                    this.$Modal.warning({title: this.$L('温馨提示'), content: e});
                                    this.videoClose(username, this.$L('对方：') + e);
                                });
                            }).catch((e) => {
                                this.$Modal.warning({title: this.$L('温馨提示'), content: e});
                                this.videoClose(username, this.$L('对方：') + e);
                            });
                        }
                    }).catch((e) => {
                        this.$Modal.warning({title: this.$L('温馨提示'), content: e});
                        username && this.videoClose(username, this.$L('对方：') + e);
                    });
                } catch (e) {
                    this.$Modal.warning({title: this.$L('温馨提示'), content: this.$L('当前浏览器不支持音视频通话！')});
                    username && this.videoClose(username, this.$L('对方：') + this.$L('浏览器不支持音视频通话！'));
                }
            },

            videoListener() {
                $A.WSOB.sendTo('info', this.videoUserName, {
                    'type': 'video',
                    'subtype': 'call',
                    'username': this.usrInfo.username,
                    'userimg': this.usrInfo.userimg,
                    'video': this.videoChat,
                }, (res) => {
                    if (res.status !== 1) {
                        this.videoClose(this.videoUserName, this.$L('呼叫失败！'));
                    }
                });
            },

            videoIcecandidate(localStream, username) {
                this.videoRtc = new RTCPeerConnection({"iceServers":[{"urls":["turn:business.swoole.com:3478?transport=udp","turn:business.swoole.com:3478?transport=tcp"],"username":"ceshi","credential":"ceshi"}]});
                this.videoRtc.onicecandidate = (event) => {
                    if (event.candidate) {
                        $A.WSOB.sendTo('info', username, {
                            'type': 'video',
                            'subtype': 'candidate',
                            'data': event.candidate
                        });
                    }
                };
                try {
                    this.videoRtc.addStream(localStream);
                } catch (e) {
                    let tracks = localStream.getTracks();
                    for (let i = 0; i < tracks.length; i++) {
                        this.videoRtc.addTrack(tracks[i], localStream);
                    }
                }
                this.videoRtc.onaddstream = (e) => {
                    this.$refs.remoteVideo.srcObject = e.stream;
                };
            },

            videoClose(username, reason) {
                if (username && reason !== false) {
                    $A.WSOB.sendTo('info', username, {
                        'type': 'video',
                        'subtype': 'close',
                        'reason': reason,
                    });
                }
                if (this.videoInitiator) {
                    this.videoInitiator = false;
                    if (this.videoStartTime > 0) {
                        let second = Math.round(new Date().getTime() / 1000) - this.videoStartTime;
                        if (second >= 2) {
                            $A.WSOB.sendTo('user', this.videoUserName, {
                                type: this.videoChat ? 'video' : 'voice',
                                username: this.usrInfo.username,
                                userimg: this.usrInfo.userimg,
                                indate: Math.round(new Date().getTime() / 1000),
                                text: this.videoChat ? '视频通话' : '语音通话',
                                other: {
                                    second: second
                                }
                            }, 'special');
                        }
                    }
                }
                if (username == this.videoUserName) {
                    this.videoUserName = '';
                    this.videoUserImg = '';
                    this.videoStartTime = 0;
                    if (this.videoLocalStream !== null) {
                        this.videoLocalStream.getTracks().forEach((track) => {
                            track.stop();
                        });
                        this.videoLocalStream = null;
                    }
                    this.$refs.localVideo.srcObject = null;
                    this.$refs.remoteVideo.srcObject = null;
                }
            },

            videoMessage(msgDetail) {
                let body = msgDetail.body;
                let username = msgDetail.username;
                if (['offer', 'candidate', 'answer'].indexOf(body.subtype) !== -1) {
                    if (!this.videoLocalStream) {
                        this.videoClose(username);
                        return;
                    }
                }
                //
                switch (body.subtype) {
                    case 'call':
                        let callIng = true;
                        let callAudio = this.$refs.callAudio;
                        if (callAudio.getAttribute("data-listener") !== 'yes') {
                            callAudio.setAttribute("data-listener", "yes");
                            callAudio.addEventListener('ended', () => {
                                if (callIng) {
                                    $A.WSOB.sendTo('info', username, {
                                        'type': 'video',
                                        'subtype': 'judge',
                                    });
                                    callAudio.play();
                                }
                            }, false);
                        }
                        callAudio.currentTime = 0;
                        callAudio.play();
                        //
                        this.$Notice.close('chat-call');
                        this.$Notice.open({
                            name: 'chat-call',
                            duration: 0,
                            onClose: () => {
                                callIng = false;
                                callAudio.pause();
                                this.videoClose(username, this.$L('对方：拒绝接听'));
                            },
                            render: h => {
                                return h('div', {
                                    class: 'chat-notice-box',
                                }, [
                                    h('UserImg', {class: 'chat-notice-userimg', props: {info: body}}),
                                    h('div', {class: 'ivu-notice-with-desc'}, [
                                        h('div', {class: 'ivu-notice-title'}, [
                                            h('UserView', {props: {username: username}})
                                        ]),
                                        h('div', {class: 'ivu-notice-desc'}, this.$L(body.video ? "邀请视频通话..." : "邀请语音通话...")),
                                        h('div', {class: 'chat-notice-btn-box'}, [
                                            h('Button', {
                                                props: {type: 'success', size: 'small'},
                                                on: {
                                                    click: () => {
                                                        callIng = false;
                                                        callAudio.pause();
                                                        this.$Notice.close('chat-call');
                                                        this.$emit("on-open-notice", username);
                                                        this.clickDialog(username);
                                                        this.videoConnect(username, body.video);
                                                        this.videoUserName = username;
                                                        this.videoUserImg = body.userimg;
                                                        this.videoStartTime = 0;
                                                        this.videoInitiator = false;
                                                    }
                                                }
                                            }, this.$L("接受")),
                                            h('Button', {
                                                props: {type: 'error', size: 'small'},
                                                on: {
                                                    click: () => {
                                                        callIng = false;
                                                        callAudio.pause();
                                                        this.$Notice.close('chat-call');
                                                        this.videoClose(username, this.$L('对方：拒绝接听'));
                                                    }
                                                }
                                            }, this.$L("拒绝")),
                                        ])
                                    ])
                                ])
                            }
                        });
                        break;

                    case 'judge':
                        if (username != this.videoUserName) {
                            $A.WSOB.sendTo('info', username, {
                                'type': 'video',
                                'subtype': 'close',
                            });
                        }
                        break;

                    case 'close':
                        this.$refs.callAudio.pause();
                        this.$Notice.close('chat-call');
                        this.videoClose(username, false);
                        body.reason && this.$Message.warning(body.reason);
                        break;

                    case 'offer':
                        this.videoIcecandidate(this.videoLocalStream, username);
                        this.videoRtc.setRemoteDescription(new RTCSessionDescription(body.data)).then(() => {
                            if (this.videoStartTime === 0) {
                                this.videoRtc.createAnswer().then((desc) => {
                                    this.videoRtc.setLocalDescription(desc).then(() => {
                                        $A.WSOB.sendTo('info', username, {
                                            'type': 'video',
                                            'subtype': 'answer',
                                            'data': this.videoRtc.localDescription
                                        });
                                    }).catch((e) => {
                                        this.$Modal.warning({title: this.$L('温馨提示'), content: e});
                                    });
                                }).catch((e) => {
                                    this.$Modal.warning({title: this.$L('温馨提示'), content: e});
                                });
                                this.videoStartTime = Math.round(new Date().getTime() / 1000);
                            }
                        }).catch((e) => {
                            this.$Modal.warning({title: this.$L('温馨提示'), content: e});
                        });
                        break;

                    case 'answer':
                        if (this.videoRtc) {
                            this.videoRtc.setRemoteDescription(new RTCSessionDescription(body.data)).then(() => {
                            }).catch((e) => {
                                this.$Modal.warning({title: this.$L('温馨提示'), content: e});
                            });
                        }
                        break;

                    case 'candidate':
                        if (this.videoRtc) {
                            this.videoRtc.addIceCandidate(new RTCIceCandidate(body.data)).then(() => {
                            }).catch((e) => {
                                this.$Modal.warning({title: this.$L('温馨提示'), content: e});
                            });
                        }
                        break;
                }
            }
        }
    }
</script>
