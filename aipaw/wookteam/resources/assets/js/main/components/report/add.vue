<template>
    <drawer-tabs-container>
        <div class="report-add">
            <div class="add-header">
                <div class="add-title">
                    <Input v-model="dataDetail.title" :placeholder="$L('汇报标题')"></Input>
                </div>
                <ButtonGroup>
                    <Button :disabled="id > 0" :type="`${type=='日报'?'primary':'default'}`" @click="type='日报'">{{$L('日报')}}</Button>
                    <Button :disabled="id > 0" :type="`${type=='周报'?'primary':'default'}`" @click="type='周报'">{{$L('周报')}}</Button>
                </ButtonGroup>
            </div>
            <t-editor class="add-edit" v-model="dataDetail.content" height="100%"></t-editor>
            <div class="add-input">
                <UserInput v-model="dataDetail.ccuser" :nousername="usrName" :placeholder="$L('输入关键词搜索')" multiple><span slot="prepend">{{$L('抄送人')}}</span></UserInput>
                <div class="add-prev-btn" @click="getPrevCc">{{$L('使用我上次抄送的人')}}</div>
            </div>
            <div class="add-footer">
                <Button :loading="loadIng > 0" type="primary" @click="handleSubmit" style="margin-right:6px">{{$L('保存')}}</Button>
                <Button v-if="dataDetail.status=='已发送'" :loading="loadIng > 0" type="success" icon="md-checkmark-circle-outline" ghost @click="handleSubmit(true)">{{$L('已发送')}}</Button>
                <Button v-else :loading="loadIng > 0" @click="handleSubmit(true)">{{$L('保存并发送')}}</Button>
            </div>
        </div>
    </drawer-tabs-container>
</template>

<style lang="scss">
    .report-add {
        .add-edit {
            .teditor-loadedstyle {
                height: 100%;
            }
        }
    }
</style>
<style lang="scss" scoped>
    .report-add {
        display: flex;
        flex-direction: column;
        height: 100%;
        padding: 0 24px;
        .add-header {
            display: flex;
            align-items: center;
            margin-top: 22px;
            margin-bottom: 14px;
            .add-title {
                flex: 1;
                padding-right: 36px;
            }
        }
        .add-edit {
            width: 100%;
            flex: 1;
        }
        .add-input,
        .add-footer {
            margin-top: 14px;
        }
        .add-prev-btn {
            cursor: pointer;
            opacity: 0.9;
            margin-top: 8px;
            text-decoration: underline;
            &:hover {
                opacity: 1;
                color: #2d8cf0;
            }
        }
    }
</style>
<script>
    import DrawerTabsContainer from "../DrawerTabsContainer";
    import TEditor from "../TEditor";

    /**
     * 新建汇报
     */
    export default {
        name: 'ReportAdd',
        components: {TEditor, DrawerTabsContainer},
        props: {
            id: {
                default: 0
            },
            canload: {
                type: Boolean,
                default: true
            },
        },
        data () {
            return {
                loadYet: false,

                loadIng: 0,

                dataDetail: {
                    title: '',
                    content: '',
                    ccuser: '',
                    status: '',
                },

                type: '日报'
            }
        },

        created() {
            this.dataDetail.content = this.$L('数据加载中.....')
        },

        mounted() {
            if (this.canload) {
                this.loadYet = true;
                this.getData();
            }
        },

        watch: {
            canload(val) {
                if (val && !this.loadYet) {
                    this.loadYet = true;
                    this.getData();
                }
            },
            type() {
                if (this.loadYet) {
                    this.getData();
                }
            },
            id() {
                if (this.loadYet) {
                    this.dataDetail = {};
                    this.getData();
                }
            },
        },

        methods: {
            getData() {
                this.loadIng++;
                $A.apiAjax({
                    url: 'report/template',
                    method: 'post',
                    data: {
                        id: this.id,
                        type: this.type,
                    },
                    complete: () => {
                        this.loadIng--;
                    },
                    success: (res) => {
                        if (res.ret === 1) {
                            this.dataDetail = res.data;
                        }
                    }
                });
            },

            getPrevCc() {
                this.loadIng++;
                $A.apiAjax({
                    url: 'report/prevcc',
                    complete: () => {
                        this.loadIng--;
                    },
                    success: (res) => {
                        if (res.ret === 1) {
                            this.dataDetail.ccuser = res.data.lists.join(',');
                        } else {
                            this.$Modal.error({title: this.$L('温馨提示'), content: res.msg});
                        }
                    }
                });
            },

            handleSubmit(send = false) {
                this.loadIng++;
                $A.apiAjax({
                    url: 'report/template',
                    method: 'post',
                    data: Object.assign(this.dataDetail, {
                        act: 'submit',
                        id: this.id,
                        type: this.type,
                        send: (send === true ? 1 : 0),
                    }),
                    complete: () => {
                        this.loadIng--;
                    },
                    error: () => {
                        alert(this.$L('网络繁忙，请稍后再试！'));
                    },
                    success: (res) => {
                        if (res.ret === 1) {
                            this.dataDetail = res.data;
                            this.$Message.success(res.msg);
                            this.$emit("on-success", res.data);
                            //
                            if (this.dataDetail.status === '已发送') {
                                let msgData = {
                                    type: 'report',
                                    username: this.usrInfo.username,
                                    userimg: this.usrInfo.userimg,
                                    indate: Math.round(new Date().getTime() / 1000),
                                    text: this.dataDetail.ccuserAgain ? this.$L('修改了工作报告') : this.$L('发送了工作报告'),
                                    other: {
                                        id: this.dataDetail.id,
                                        type: this.dataDetail.type,
                                        title: this.dataDetail.title,
                                    }
                                };
                                this.dataDetail.ccuserArray.forEach((username) => {
                                    if (username != msgData.username) {
                                        $A.WSOB.sendTo('user', username, msgData, 'special');
                                    }
                                });
                            }
                        } else {
                            this.$Modal.error({title: this.$L('温馨提示'), content: res.msg});
                        }
                    }
                });
            }
        }
    }
</script>
