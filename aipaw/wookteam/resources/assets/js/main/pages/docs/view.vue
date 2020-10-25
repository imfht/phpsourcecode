<template>
    <div class="w-main docs-view">

        <v-title>{{$L('文档浏览')}}-{{$L('轻量级的团队在线协作')}}</v-title>

        <div class="view-box">
            <div class="view-head">
                <div class="header-title">
                    <span v-if="bookDetail.title">{{bookDetail.title}}</span>
                    <em v-if="bookDetail.title && docDetail.title">-</em>
                    {{docDetail.title}}
                </div>
                <Button class="header-button" size="small" type="primary" ghost @click="toggleFullscreen">{{$L(isFullscreen ? '退出全屏' : '全屏')}}</Button>
            </div>
            <div class="view-main" :class="{'view-book':isBook}">
                <div class="view-menu">
                    <div class="view-menu-list">
                        <nested-draggable :lists="sectionLists" :readonly="true" :activeid="sid" @change="handleSection"></nested-draggable>
                    </div>
                </div>
                <div class="view-body">
                    <div class="view-body-content">
                        <template v-if="docDetail.type=='document'">
                            <MarkdownPreview v-if="docContent.type=='md'" :initialValue="docContent.content"></MarkdownPreview>
                            <ReportContent v-else :content="docContent.content"></ReportContent>
                        </template>
                        <minder v-else-if="docDetail.type=='mind'" ref="myMind" class="body-mind" v-model="docContent" :readOnly="true"></minder>
                        <sheet v-else-if="docDetail.type=='sheet'" ref="mySheet" class="body-sheet" v-model="docContent.content" :readOnly="true"></sheet>
                        <flow v-else-if="docDetail.type=='flow'" ref="myFlow" class="body-flow" v-model="docContent" :readOnly="true"></flow>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>


<style lang="scss">
    .view-body {
        .view-body-content {
            .markdown-preview,
            .report-content {
                margin: 0 !important;
                padding: 0 !important;
            }
            .minder-editor-container {
                transform: translateZ(0);
            }
            .body-sheet {
                box-sizing: content-box;
                * {
                    box-sizing: content-box;
                }
            }
        }
    }
</style>
<style lang="scss" scoped>
    .docs-view {
        background-color: #ffffff;
        .view-box {
            display: flex;
            flex-direction: column;
            position: absolute;
            width: 100%;
            height: 100%;
            .view-head {
                display: flex;
                flex-direction: row;
                align-items: center;
                width: 100%;
                height: 38px;
                box-shadow: 0 1px 5px 0 rgba(0, 0, 0, 0.1);
                position: relative;
                z-index: 99;
                .header-title {
                    flex: 1;
                    color: #333333;
                    padding-left: 12px;
                    padding-right: 12px;
                    font-size: 16px;
                    font-weight: 500;
                    white-space: nowrap;
                    em {
                        padding: 0 3px;
                        font-weight: normal;
                    }
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
            .view-main {
                flex: 1;
                width: 100%;
                display: flex;
                flex-direction: row;
                align-items: flex-start;
                justify-content: flex-start;
                &.view-book {
                    .view-menu {
                        border-right: 0;
                        width: 100%;
                        .view-menu-list {
                            padding: 18px 8%;
                        }
                    }
                    .view-body {
                        display: none;
                    }
                }
                .view-menu {
                    position: relative;
                    height: 100%;
                    width: 280px;
                    border-right: 1px solid #E6ECF1;
                    .view-menu-list {
                        position: absolute;
                        left: 0;
                        top: 0;
                        right: 0;
                        bottom: 0;
                        padding: 18px 12px;
                        overflow: auto;
                    }
                }
                .view-body {
                    flex: 1;
                    height: 100%;
                    position: relative;
                    .view-body-content {
                        position: absolute;
                        left: 0;
                        top: 0;
                        right: 0;
                        bottom: 0;
                        padding: 18px;
                        overflow: auto;
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


    const Sheet = resolve => require(['../../components/docs/sheet/index'], resolve);
    const Flow = resolve => require(['../../components/docs/flow/index'], resolve);
    const NestedDraggable = resolve => require(['../../components/docs/NestedDraggable'], resolve);
    const MarkdownPreview = resolve => require(['../../components/MDEditor/components/preview/index'], resolve);
    const ReportContent = resolve => require(['../../components/report/content'], resolve);

    export default {
        components: {Sheet, Flow, ReportContent, MarkdownPreview, NestedDraggable},
        data () {
            return {
                loadIng: 0,

                sid: 0,

                docDetail: { },
                docContent: { },

                bookDetail: {},

                sectionLists: [],
                sectionNoDataText: "",

                routeName: '',

                isBook: false,
                isFullscreen: false,
            }
        },
        mounted() {
            this.routeName = this.$route.name;
            //
            document.addEventListener("fullscreenchange", () => {
                this.isFullscreen = !!document.fullscreenElement;
            });
        },
        activated() {
            this.refreshSid();
        },
        deactivated() {
            if ($A.getToken() === false) {
                this.sid = 0;
            }
        },
        watch: {
            sid(val) {
                if (!val) {
                    return;
                }
                val += "";
                if (val.substring(0, 1) == 'b') {
                    this.isBook = true;
                    this.docDetail.bookid = val.substring(1);
                    this.getSectionMenu();
                } else {
                    this.isBook = false;
                    this.refreshDetail();
                }
            },
            '$route' (To) {
                if (To.name == 'docs-view') {
                    this.sid = To.params.sid;
                }
            },
        },
        methods: {
            refreshSid() {
                this.sid = this.$route.params.sid;
                if (typeof this.$route.params.other === "object") {
                    this.$set(this.docDetail, 'title', $A.getObject(this.$route.params.other, 'title'))
                }
            },

            refreshDetail() {
                this.docDetail = { };
                this.docContent = { };
                this.getDetail();
            },

            getDetail() {
                this.loadIng++;
                $A.apiAjax({
                    url: 'docs/section/content',
                    data: {
                        act: 'view',
                        id: this.sid,
                    },
                    complete: () => {
                        this.loadIng--;
                    },
                    error: () => {
                        alert(this.$L('网络繁忙，请稍后再试！'));
                    },
                    success: (res) => {
                        if (res.ret === 1) {
                            this.docDetail = res.data;
                            this.docContent = $A.jsonParse(res.data.content);
                            this.getSectionMenu();
                        } else {
                            this.$Modal.error({
                                title: this.$L('温馨提示'),
                                content: res.msg,
                                onOk: () => {
                                    if (res.data == '-1001') {
                                        this.goForward({path: '/', query:{from:encodeURIComponent(window.location.href)}}, true);
                                    }
                                }
                            });
                        }
                    }
                });
            },

            getSectionMenu() {
                this.sectionNoDataText = this.$L("数据加载中.....");
                let bookid = this.docDetail.bookid;
                $A.apiAjax({
                    url: 'docs/section/lists',
                    data: {
                        act: 'view',
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
                            this.bookDetail = res.data.book;
                            this.sectionLists = res.data.tree;
                            this.sectionNoDataText = this.$L("没有相关的数据");
                        } else {
                            this.sectionLists = [];
                            this.sectionNoDataText = res.msg;
                            this.$Modal.error({
                                title: this.$L('温馨提示'),
                                content: res.msg,
                                onOk: () => {
                                    if (res.data == '-1001') {
                                        this.goForward({path: '/', query:{from:encodeURIComponent(window.location.href)}}, true);
                                    }
                                }
                            });
                        }
                    }
                });
            },

            handleSection(act, detail) {
                if (act === 'open') {
                    this.goForward({name: 'docs-view', params: {sid: detail.id, other: detail || {}}});
                    this.refreshSid();
                }
            },

            toggleFullscreen() {
                if (this.isFullscreen) {
                    this.exitFullscreen();
                } else {
                    this.launchFullscreen(this.$el);
                }
            },

            launchFullscreen(element) {
                if (element.requestFullscreen) {
                    element.requestFullscreen();
                } else if (element.mozRequestFullScreen) {
                    element.mozRequestFullScreen();
                } else if (element.msRequestFullscreen) {
                    element.msRequestFullscreen();
                } else if (element.webkitRequestFullscreen) {
                    element.webkitRequestFullScreen();
                }
            },

            exitFullscreen() {
                if (document.exitFullscreen) {
                    document.exitFullscreen();
                } else if (document.msExitFullscreen) {
                    document.msExitFullscreen();
                } else if (document.mozCancelFullScreen) {
                    document.mozCancelFullScreen();
                } else if (document.webkitExitFullscreen) {
                    document.webkitExitFullscreen();
                }
            }
        },
    }
</script>
