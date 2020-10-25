<template>
    <div class="w-main docs-edit">

        <v-title>{{$L('文档编辑')}}-{{$L('轻量级的团队在线协作')}}</v-title>

        <div class="edit-box">
            <div class="edit-header">
                <div class="header-menu active" @click="handleClick('back')"><Icon type="md-arrow-back" /></div>
                <Tooltip class="header-menu" :content="$L('知识库目录')">
                    <div class="menu-container" @click="handleClick('menu')"><Icon type="md-menu" /></div>
                </Tooltip>
                <Tooltip class="header-menu" :content="$L('分享文档')">
                    <div class="menu-container" @click="handleClick('share')"><Icon type="md-share" /></div>
                </Tooltip>
                <Tooltip class="header-menu" :content="$L('浏览文档')">
                    <a class="menu-container" target="_blank" :href="handleClick('view')"><Icon type="md-eye" /></a>
                </Tooltip>
                <Tooltip class="header-menu" :content="$L('历史版本')">
                    <div class="menu-container" @click="handleClick('history')"><Icon type="md-time" /></div>
                </Tooltip>
                <Poptip class="header-menu synch">
                    <div class="menu-container">
                        <Icon type="md-contacts" :title="$L('正在协作会员')"/><em v-if="synchUsers.length > 0">{{synchUsers.length}}</em>
                    </div>
                    <ul class="synch-lists" slot="content">
                        <li class="title">{{$L('正在协作会员')}}:</li>
                        <li v-for="(item, key) in synchUsersS" :key="key" @click="handleSynch(item.username)">
                            <UserView class="synch-username" placement="right" :username="item.username" showimg/>
                            <span v-if="item.username==usrInfo.username" class="synch-self">{{$L('自己')}}</span>
                        </li>
                    </ul>
                </Poptip>
                <Tooltip class="header-menu" :class="{lock:isLock}" max-width="500">
                    <div slot="content" style="white-space:nowrap">
                        <span v-if="isLock&&docDetail.lockname!=usrInfo.username">【<UserView :username="docDetail.lockname"/>】{{$L('已锁定')}}</span>
                        <span v-else>{{$L('锁定后其他会员将无法修改保存文档。')}}</span>
                    </div>
                    <div class="menu-container" @click="handleClick(isLock?'unlock':'lock')"><Icon :type="isLock?'md-lock':'md-unlock'" /></div>
                </Tooltip>
                <div class="header-title">{{docDetail.title}}</div>
                <div v-if="docDetail.type=='document'" class="header-hint">
                    <ButtonGroup size="small" shape="circle">
                        <Button :type="`${docContent.type!='md'?'primary':'default'}`" @click="$set(docContent, 'type', 'text')">{{$L('文本编辑器')}}</Button>
                        <Button :type="`${docContent.type=='md'?'primary':'default'}`" @click="$set(docContent, 'type', 'md')">{{$L('MD编辑器')}}</Button>
                    </ButtonGroup>
                </div>
                <div v-if="docDetail.type=='mind'" class="header-hint">
                    {{$L('选中节点，按enter键添加同级节点，tab键添加子节点')}}
                </div>
                <Dropdown v-if="docDetail.type=='mind' || docDetail.type=='flow' || docDetail.type=='sheet'"
                          trigger="click"
                          class="header-hint"
                          @on-click="exportMenu">
                    <a href="javascript:void(0)">
                        {{$L('导出')}}
                        <Icon type="ios-arrow-down"></Icon>
                    </a>
                    <DropdownMenu v-if="docDetail.type=='sheet'" slot="list">
                        <DropdownItem name="xlsx">{{$L('导出XLSX')}}</DropdownItem>
                        <DropdownItem name="xlml">{{$L('导出XLS')}}</DropdownItem>
                        <DropdownItem name="csv">{{$L('导出CSV')}}</DropdownItem>
                        <DropdownItem name="txt">{{$L('导出TXT')}}</DropdownItem>
                    </DropdownMenu>
                    <DropdownMenu v-else slot="list">
                        <DropdownItem name="png">{{$L('导出PNG图片')}}</DropdownItem>
                        <DropdownItem name="pdf">{{$L('导出PDF文件')}}</DropdownItem>
                    </DropdownMenu>
                </Dropdown>
                <Button :disabled="equalContent" :loading="loadIng > 0" class="header-button" size="small" type="primary" @click="handleClick('save')">{{$L('保存')}}</Button>
            </div>
            <div class="docs-body">
                <template v-if="docDetail.type=='document'">
                    <MDEditor v-if="docContent.type=='md'" class="body-text" v-model="docContent.content" height="100%"></MDEditor>
                    <TEditor v-else class="body-text" v-model="docContent.content" height="100%" @editorSave="handleClick('saveBefore')"></TEditor>
                </template>
                <minder v-else-if="docDetail.type=='mind'" ref="myMind" class="body-mind" v-model="docContent" @saveData="handleClick('saveBefore')"></minder>
                <sheet v-else-if="docDetail.type=='sheet'" ref="mySheet" class="body-sheet" v-model="docContent.content"></sheet>
                <flow v-else-if="docDetail.type=='flow'" ref="myFlow" class="body-flow" v-model="docContent" @saveData="handleClick('saveBefore')"></flow>
            </div>
        </div>

        <WDrawer v-model="docDrawerShow" maxWidth="450">
            <Tabs v-if="docDrawerShow" v-model="docDrawerTab">
                <TabPane :label="$L('知识库目录')" name="menu">
                    <nested-draggable :lists="sectionLists" :readonly="true" :activeid="sid" @change="handleSection"></nested-draggable>
                    <div v-if="sectionLists.length == 0" style="color:#888;padding:32px;text-align:center">{{sectionNoDataText}}</div>
                </TabPane>
                <TabPane :label="$L('文档历史版本')" name="history">
                    <Table class="tableFill" :columns="historyColumns" :data="historyLists" :no-data-text="historyNoDataText" size="small" stripe></Table>
                </TabPane>
            </Tabs>
        </WDrawer>

    </div>
</template>


<style lang="scss">
    .docs-edit {
        .edit-box {
            .synch-username {
                .user-view-img {
                    width: 24px;
                    height: 24px;
                    font-size: 14px;
                    line-height: 24px;
                    border-radius: 12px;
                }
            }
        }
        .body-text {
            .mdeditor-box {
                position: relative;
                width: 100%;
                .markdown {
                    position: absolute;
                    top: 0;
                    left: 0;
                    bottom: 0;
                    right: 0;
                    overflow: auto;
                    transform: translateZ(0);
                    &.border {
                        border: 0 !important;
                    }
                }
            }
            .teditor-loadedstyle {
                .tox-tinymce {
                    border: 0;
                    border-radius: 0;
                }
                .tox-mbtn {
                    height: 28px;
                }
                .tox-menubar,
                .tox-toolbar-overlord {
                    padding: 0 12%;
                    background: #f9f9f9;
                }
                .tox-toolbar__overflow,
                .tox-toolbar__primary {
                    background: none !important;
                    border-top: 1px solid #eaeaea !important;
                }
                .tox-toolbar-overlord {
                    border-bottom: 1px solid #E9E9E9 !important;
                }
                .tox-toolbar__group:not(:last-of-type) {
                    border-right: 1px solid #eaeaea !important;
                }
                .tox-sidebar-wrap {
                    margin: 22px 12%;
                    border: 1px solid #e8e8e8;
                    border-radius: 2px;
                    box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.08);
                    .tox-edit-area {
                        border-top: 0;
                    }
                }
                .tox-statusbar {
                    border-top: 1px solid #E9E9E9;
                    .tox-statusbar__resize-handle {
                        display: none;
                    }
                }
            }
        }
        .body-sheet {
            box-sizing: content-box;
            * {
                box-sizing: content-box;
            }
        }
    }
</style>
<style lang="scss" scoped>
    .docs-edit {
        .edit-box {
            display: flex;
            flex-direction: column;
            position: absolute;
            width: 100%;
            height: 100%;
            overflow-x: auto;
            .edit-header {
                display: flex;
                flex-direction: row;
                align-items: center;
                width: 100%;
                height: 38px;
                min-width: 1024px;
                background-color: #ffffff;
                box-shadow: 0 1px 5px 0 rgba(0, 0, 0, 0.1);
                position: relative;
                z-index: 99;
                .header-menu {
                    width: 48px;
                    height: 100%;
                    text-align: center;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    margin-right: 3px;
                    cursor: pointer;
                    position: relative;
                    .menu-container {
                        display: inline-block;
                        width: 48px;
                        height: 38px;
                        line-height: 38px;
                        color: #777777;
                        transition: color .2s ease;
                    }
                    .ivu-icon {
                        font-size: 16px;
                    }
                    &.synch {
                        .menu-container {
                            em {
                                padding-left: 2px;
                            }
                        }
                    }
                    &.lock {
                        .menu-container {
                            color: #059DFD;
                        }
                    }
                    &:hover,
                    &.active {
                        color: #fff;
                        background: #059DFD;
                        .menu-container {
                            color: #fff;
                        }
                    }
                    .synch-lists {
                        max-height: 500px;
                        overflow: auto;
                        li {
                            display: flex;
                            align-items: center;
                            padding: 6px 0;
                            border-bottom: 1px dashed #eeeeee;
                            &.title {
                                font-size: 14px;
                                font-weight: 600;
                                color: #333333;
                            }
                            .synch-userimg {
                                width: 24px;
                                height: 24px;
                                font-size: 14px;
                                line-height: 24px;
                                border-radius: 12px;
                            }
                            .synch-self {
                                padding: 1px 3px;
                                margin-left: 5px;
                                height: 18px;
                                line-height: 16px;
                                background-color: #FF5722;
                                color: #ffffff;
                                font-size: 12px;
                                border-radius: 3px;
                                transform: scale(0.95);
                            }
                            .synch-username {
                                padding-left: 8px;
                                font-size: 14px;
                                color: #555555;
                            }
                        }
                    }
                }
                .header-title {
                    flex: 1;
                    color: #333333;
                    border-left: 1px solid #ddd;
                    margin-left: 5px;
                    padding-left: 24px;
                    padding-right: 24px;
                    font-size: 16px;
                    overflow: hidden;
                    text-overflow:ellipsis;
                    white-space: nowrap;
                }
                .header-hint {
                    padding-right: 22px;
                    font-size: 12px;
                    color: #666;
                    white-space: nowrap;
                    .ivu-btn {
                        font-size: 12px;
                        padding: 0 10px;
                    }
                    .ivu-dropdown-item {
                        font-size: 12px !important;
                    }
                }
                .header-button {
                    font-size: 12px;
                    margin-right: 12px;
                }
            }
            .docs-body {
                flex: 1;
                width: 100%;
                min-width: 1024px;
                position: relative;
                .body-text {
                    display: flex;
                    width: 100%;
                    height: 100%;
                    .teditor-loadedstyle {
                        height: 100%;
                    }
                }
            }
        }
    }
</style>
<script>
    import Vue from 'vue'
    import minder from '../../components/docs/minder'
    Vue.use(minder)

    const MDEditor = resolve => require(['../../components/MDEditor/index'], resolve);
    const TEditor = resolve => require(['../../components/TEditor'], resolve);
    const Sheet = resolve => require(['../../components/docs/sheet/index'], resolve);
    const Flow = resolve => require(['../../components/docs/flow/index'], resolve);
    const NestedDraggable = resolve => require(['../../components/docs/NestedDraggable'], resolve);
    const WDrawer = resolve => require(['../../components/iview/WDrawer'], resolve);

    export default {
        components: {WDrawer, Flow, Sheet, MDEditor, TEditor, NestedDraggable},
        data () {
            return {
                loadIng: 0,

                sid: 0,
                hid: 0,

                docDetail: { },
                docContent: { },
                bakContent: null,

                docDrawerShow: false,
                docDrawerTab: '',

                sectionLists: [],
                sectionNoDataText: "",

                historyColumns: [],
                historyLists: [],
                historyNoDataText: "",

                routeName: '',
                synergyNum: 0,
                synchUsers: [],

                timeValue: Math.round(new Date().getTime() / 1000),
            }
        },
        created() {
            this.historyColumns = [{
                "title": this.$L("存档日期"),
                "minWidth": 160,
                "maxWidth": 200,
                render: (h, params) => {
                    return h('span', $A.formatDate("Y-m-d H:i:s", params.row.indate));
                }
            }, {
                "title": this.$L("操作员"),
                "key": 'username',
                "minWidth": 80,
                "maxWidth": 130,
                render: (h, params) => {
                    return h('UserView', {
                        props: {
                            username: params.row.username
                        }
                    });
                }
            }, {
                "title": " ",
                "key": 'action',
                "width": 80,
                "align": 'center',
                render: (h, params) => {
                    if (this.hid == params.row.id || (this.hid == 0 && params.index == 0)) {
                        return h('Icon', {
                            props: { type: 'md-checkmark' },
                            style: { marginRight: '6px', fontSize: '16px', color: '#FF5722' },
                        });
                    }
                    return h('Button', {
                        props: {
                            type: 'text',
                            size: 'small'
                        },
                        style: {
                            fontSize: '12px'
                        },
                        on: {
                            click: () => {
                                let data = {sid: this.getSid() + "-" + params.row.id, other: this.$route.params.other}
                                if (params.index == 0) {
                                    data.sid = this.getSid();
                                }
                                this.handleSection('openBefore', data);
                            }
                        }
                    }, this.$L('还原'));
                }
            }];
        },
        mounted() {
            this.routeName = this.$route.name;
            //
            setInterval(() => {
                if (this.routeName === this.$route.name) {
                    this.timeValue = Math.round(new Date().getTime() / 1000);
                }
            });
            //
            $(window).bind('beforeunload', () => {
                if (!this.equalContent && this.routeName === this.$route.name) {
                    return '是否放弃修改的内容返回？';
                }
            });
            //
            $A.WSOB.setOnMsgListener("chat/index", ['docs'], (msgDetail) => {
                if (this.routeName !== this.$route.name) {
                    return;
                }
                let body = msgDetail.body;
                if (body.sid != this.sid) {
                    return;
                }
                switch (body.type) {
                    case 'users':
                        this.synchUsers = body.lists;
                        this.synchUsers.splice(this.synchUsers.length);
                        break;

                    case 'update':
                        if (this.isLock) {
                            this.getDetail();
                        } else {
                            this.$Modal.confirm({
                                title: this.$L("更新提示"),
                                content: this.$L('团队成员（%）更新了内容，<br/>更新时间：%。<br/><br/>点击【确定】加载最新内容。', body.nickname, $A.formatDate("Y-m-d H:i:s", body.time)),
                                onOk: () => {
                                    this.getDetail();
                                }
                            });
                        }
                        break;

                    case 'lock':
                    case 'unlock':
                        if (this.docDetail.lockname == body.lockname) {
                            return;
                        }
                        this.$set(this.docDetail, 'lockname', body.lockname);
                        this.$set(this.docDetail, 'lockdate', body.lockdate);
                        this.$Notice.close('docs-lock')
                        this.$Notice[body.type=='lock'?'warning':'info']({
                            name: 'docs-lock',
                            duration: 0,
                            render: h => {
                                return h('div', {
                                    style: {
                                        lineHeight: '18px'
                                    }
                                }, [
                                    h('span', {
                                        style: {
                                            fontWeight: 500
                                        }
                                    }, body.nickname + ':'),
                                    h('span', {
                                        style: {
                                            paddingLeft: '6px'
                                        }
                                    }, this.$L(body.type == 'lock' ? '锁定文档' : '解锁文档'))
                                ])
                            }
                        });
                        break;
                }
            });
        },
        activated() {
            this.docDrawerTab = '';
            this.sectionNoDataText = '';
            this.historyNoDataText = '';
            //
            this.refreshSid();
            this.synergy(true);
            document.addEventListener("keydown", this.keySave);
        },
        deactivated() {
            if (this.isLock && this.docDetail.lockname == this.usrInfo.username) {
                this.docDetail.lockname = '';
                this.handleClick('unlock');
            }
            this.$Notice.close('docs-lock');
            //
            if (!this.equalContent) {
                this.handleClick('save');
            }
            //
            this.synergy(false);
            document.removeEventListener("keydown", this.keySave);
            this.docDrawerShow = false;
            if ($A.getToken() === false) {
                this.sid = 0;
            }
        },
        watch: {
            sid(val) {
                if (!val) {
                    this.goBackDirect();
                    return;
                }
                this.hid = $A.runNum($A.strExists(val, '-') ? $A.getMiddle(val, "-", null) : 0);
                this.refreshDetail();
            },

            '$route' (To) {
                if (To.name == 'docs-edit') {
                    this.sid = To.params.sid;
                }
            },

            docDrawerTab(act) {
                switch (act) {
                    case "menu":
                        if (!this.sectionNoDataText) {
                            this.sectionNoDataText = this.$L("数据加载中.....");
                            let bookid = this.docDetail.bookid;
                            $A.apiAjax({
                                url: 'docs/section/lists',
                                data: {
                                    act: 'edit',
                                    bookid: bookid
                                },
                                error: () => {
                                    if (bookid != this.docDetail.bookid) {
                                        return;
                                    }
                                    this.sectionNoDataText = this.$L("数据加载失败！");
                                },
                                success: (res) => {
                                    if (bookid != this.docDetail.bookid) {
                                        return;
                                    }
                                    if (res.ret === 1) {
                                        this.sectionLists = res.data.tree;
                                        this.sectionNoDataText = this.$L("没有相关的数据");
                                    }else{
                                        this.sectionLists = [];
                                        this.sectionNoDataText = res.msg;
                                    }
                                }
                            });
                        }
                        break;

                    case "history":
                        if (!this.historyNoDataText) {
                            this.historyNoDataText = this.$L("数据加载中.....");
                            let sid = this.getSid();
                            $A.apiAjax({
                                url: 'docs/section/history',
                                data: {
                                    id: sid,
                                    pagesize: 50
                                },
                                error: () => {
                                    if (sid != this.getSid()) {
                                        return;
                                    }
                                    this.historyNoDataText = this.$L("数据加载失败！");
                                },
                                success: (res) => {
                                    if (sid != this.getSid()) {
                                        return;
                                    }
                                    if (res.ret === 1) {
                                        this.historyLists = res.data;
                                        this.historyNoDataText = this.$L("没有相关的数据");
                                    }else{
                                        this.historyLists = [];
                                        this.historyNoDataText = res.msg;
                                    }
                                }
                            });
                        }
                        break;
                }
            }
        },
        computed: {
            equalContent() {
                return this.bakContent == $A.jsonStringify(this.docContent);
            },
            synchUsersS() {
                return this.synchUsers.filter(item => {
                    return item.indate + 20 > this.timeValue;
                });
            },
            isLock() {
                return !!(this.docDetail.lockname && this.docDetail.lockdate > this.timeValue - 60);
            }
        },
        methods: {
            keySave(e) {
                if ((e.ctrlKey || e.metaKey) && e.keyCode === 83) {
                    this.handleClick('saveBefore');
                    e.preventDefault();
                }
            },

            goBackDirect() {
                this.bakContent = $A.jsonStringify(this.docContent);
                this.goBack({name:'docs'});
            },

            synergy(enter) {
                if (enter === false) {
                    $A.WSOB.sendTo('docs', {
                        type: 'quit',
                        sid: this.sid,
                        username: this.usrInfo.username,
                    });
                } else {
                    if (this.routeName !== this.$route.name) {
                        let tmpNum = this.synergyNum;
                        setTimeout(() => {
                            if (tmpNum === this.synergyNum) {
                                this.synergyNum++;
                                this.synergy();
                            }
                        }, 10000);
                    } else {
                        $A.WSOB.sendTo('docs', null, {
                            type: enter === true ? 'enter' : 'refresh',
                            sid: this.sid,
                            nickname: this.usrInfo.nickname,
                            username: this.usrInfo.username,
                            userimg: this.usrInfo.userimg,
                            indate: Math.round(new Date().getTime() / 1000),
                        }, (res) => {
                            this.synchUsers = res.status === 1 ? res.message : [];
                            let tmpNum = this.synergyNum;
                            setTimeout(() => {
                                if (tmpNum === this.synergyNum) {
                                    this.synergyNum++;
                                    this.synergy();
                                }
                            }, 10000);
                        });
                    }
                }
            },

            refreshSid() {
                this.sid = this.$route.params.sid;
                if (typeof this.$route.params.other === "object") {
                    this.$set(this.docDetail, 'title', $A.getObject(this.$route.params.other, 'title'))
                }
            },

            getSid() {
                return $A.runNum($A.getMiddle(this.sid, null, '-'));
            },

            refreshDetail() {
                this.docDetail = { };
                this.docContent = { };
                this.bakContent = null;
                this.getDetail();
            },

            getDetail() {
                this.loadIng++;
                $A.apiAjax({
                    url: 'docs/section/content',
                    data: {
                        act: 'edit',
                        id: this.sid,
                    },
                    complete: () => {
                        this.loadIng--;
                    },
                    error: () => {
                        this.goBackDirect();
                        alert(this.$L('网络繁忙，请稍后再试！'));
                    },
                    success: (res) => {
                        if (res.ret === 1) {
                            this.docDetail = res.data;
                            this.docContent = $A.jsonParse(res.data.content);
                            this.bakContent = this.hid == 0 ? $A.jsonStringify(this.docContent) : '';
                            this.continueLock(1000);
                        } else {
                            this.$Modal.error({title: this.$L('温馨提示'), content: res.msg});
                        }
                    }
                });
            },

            handleSection(act, detail) {
                switch (act) {
                    case 'open':
                        this.handleSection('openBefore', {sid: detail.id, other: detail || {}})
                        break;

                    case 'openBefore':
                        if (!this.equalContent) {
                            this.$Modal.confirm({
                                title: this.$L('温馨提示'),
                                content: this.$L('是否放弃保存修改的内容？'),
                                cancelText: this.$L('取消'),
                                okText: this.$L('放弃保存'),
                                onOk: () => {
                                    this.handleSection('openConfirm', detail)
                                }
                            });
                        } else {
                            this.handleSection('openConfirm', detail)
                        }
                        break;

                    case 'openConfirm':
                        this.goForward({name: 'docs-edit', params: detail}, true);
                        this.refreshSid();
                        this.docDrawerShow = false;
                        break;
                }
            },

            handleSynch(username) {
                if (username == this.usrInfo.username) {
                    return;
                }
                if (typeof window.onChatOpenUserName === "function") {
                    window.onChatOpenUserName(username);
                }
            },

            handleClick(act) {
                switch (act) {
                    case "back":
                        if (this.equalContent) {
                            this.goBackDirect();
                            return;
                        }
                        this.$Modal.confirm({
                            title: this.$L('温馨提示'),
                            content: this.$L('是否放弃修改的内容返回？'),
                            cancelText: this.$L('放弃保存'),
                            onCancel: () => {
                                this.goBackDirect();
                            },
                            okText: this.$L('保存并返回'),
                            onOk: () => {
                                this.handleClick('save');
                                this.goBackDirect();
                            }
                        });
                        break;

                    case "saveBefore":
                        if (!this.equalContent && this.loadIng == 0) {
                            this.handleClick('save');
                        } else {
                            this.$Message.warning(this.$L('没有任何修改！'));
                        }
                        return;

                    case "save":
                        this.bakContent = $A.jsonStringify(this.docContent);
                        $A.apiAjax({
                            url: 'docs/section/save',
                            method: 'post',
                            data: Object.assign(this.docDetail, {
                                id: this.getSid(),
                                content: this.bakContent
                            }),
                            error: () => {
                                this.bakContent = '';
                                alert(this.$L('网络繁忙，保存失败！'));
                            },
                            success: (res) => {
                                if (res.ret === 1) {
                                    if (this.getSid() == res.data.sid && this.docDetail.type == 'document') {
                                        this.docContent = Object.assign({}, this.docContent, res.data.content);
                                    }
                                    this.$Message.success(res.msg);
                                    this.historyNoDataText = '';
                                    if (this.hid != 0) {
                                        this.hid = 0;
                                        this.goForward({name: 'docs-edit', params: {sid: this.getSid()}}, true);
                                    }
                                } else {
                                    this.bakContent = '';
                                    this.$Modal.error({title: this.$L('温馨提示'), content: res.msg});
                                }
                            }
                        });
                        break;

                    case "menu":
                    case "history":
                        this.docDrawerTab = act;
                        this.docDrawerShow = true
                        break;

                    case "share":
                        this.$Modal.confirm({
                            render: (h) => {
                                return h('div', [
                                    h('div', {
                                        style: {
                                            fontSize: '16px',
                                            fontWeight: '500',
                                            marginBottom: '20px',
                                        }
                                    }, this.$L('文档链接')),
                                    h('Input', {
                                        props: {
                                            value: this.handleClick('view'),
                                            readonly: true,
                                        },
                                    })
                                ])
                            },
                        });
                        break;

                    case "lock":
                    case "unlock":
                        $A.apiAjax({
                            url: 'docs/section/lock?id=' + this.getSid(),
                            data: {
                                act: act,
                            },
                            error: () => {
                                alert(this.$L('网络繁忙，请稍后再试！'));
                            },
                            success: (res) => {
                                if (res.ret === 1) {
                                    if (this.docDetail.lockname != res.data.lockname) {
                                        this.$Message.success(res.msg);
                                    }
                                    this.$set(this.docDetail, 'lockname', res.data.lockname);
                                    this.$set(this.docDetail, 'lockdate', res.data.lockdate);
                                    this.continueLock(20000);
                                } else {
                                    this.$Modal.error({title: this.$L('温馨提示'), content: res.msg});
                                }
                            }
                        });
                        break;

                    case "view":
                        return $A.webUrl('docs/view/' + this.docDetail.id);

                }
            },

            continueLock(time) {
                if (!this.isLock) {
                    return;
                }
                if (this.docDetail.lockname != this.usrInfo.username) {
                    return;
                }
                this.__continueLock = $A.randomString(6);
                let tempString = this.__continueLock;
                setTimeout(() => {
                    if (tempString != this.__continueLock) {
                        return;
                    }
                    if (!this.isLock) {
                        return;
                    }
                    if (this.docDetail.lockname != this.usrInfo.username) {
                        return;
                    }
                    this.handleClick('lock');
                }, time);
            },

            exportMenu(act) {
                switch (this.docDetail.type) {
                    case 'mind':
                        this.$refs.myMind.exportHandle(act == 'pdf' ? 1 : 0, this.docDetail.title);
                        break;

                    case 'flow':
                        this.$refs.myFlow[act == 'pdf' ? 'exportPDF' : 'exportPNG'](this.docDetail.title, 3);
                        break;

                    case 'sheet':
                        this.$refs.mySheet.exportExcel(this.docDetail.title, act);
                        break;
                }
            }
        },
    }
</script>
