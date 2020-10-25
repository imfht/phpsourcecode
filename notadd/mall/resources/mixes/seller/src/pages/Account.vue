<script>
    export default {
        beforeRouteEnter(to, from, next) {
            next(() => {
            });
        },
        data() {
            const self = this;
            return {
                accountColumns: [
                    {
                        align: 'center',
                        title: '排序',
                        type: 'selection',
                        width: 80,
                    },
                    {
                        align: 'center',
                        key: 'account',
                        title: '管理员账号',
                    },
                    {
                        align: 'center',
                        key: 'name',
                        title: '真实姓名',
                    },
                    {
                        align: 'center',
                        key: 'phone',
                        title: '联系电话',
                    },
                    {
                        align: 'center',
                        key: 'time',
                        title: '最后登录时间',
                    },
                    {
                        align: 'center',
                        key: 'action',
                        render(h, data) {
                            return h('div', [
                                h('i-button', {
                                    on: {
                                        click() {
                                            self.set(data.index);
                                        },
                                    },
                                    props: {
                                        size: 'small',
                                        type: 'ghost',
                                    },
                                }, '设置'),
                                h('i-button', {
                                    on: {
                                        click() {
                                            self.remove(data.index);
                                        },
                                    },
                                    props: {
                                        size: 'small',
                                        type: 'ghost',
                                    },
                                    style: {
                                        marginLeft: '10px',
                                    },
                                }, '删除'),
                            ]);
                        },
                        title: '操作',
                        width: 180,
                    },
                ],
                accountData: [
                    {
                        account: 'guanli',
                        name: '王某某',
                        phone: 2334444444,
                        time: '2016-12-20 13:31:54',
                    },
                    {
                        account: 'guanli',
                        name: '王某某',
                        phone: 2334444444,
                        time: '2016-12-20 13:31:54',
                    },
                    {
                        account: 'guanli',
                        name: '王某某',
                        phone: 2334444444,
                        time: '2016-12-20 13:31:54',
                    },
                ],
                addModal: false,
                form: {
                    account: '',
                    name: '',
                    phone: '',
                },
                loading: false,
                self: this,
                setForm: {
                    account: 'dgwjw',
                    password: '',
                },
                setModal: false,
            };
        },
        methods: {
            addManager() {
                this.addModal = true;
            },
            remove(index) {
                this.accountData.splice(index, 1);
            },
            set() {
                this.setModal = true;
            },
            submit() {
                const self = this;
                self.loading = true;
                self.$refs.form.validate(valid => {
                    if (valid) {
                        window.console.log(valid);
                    } else {
                        self.loading = false;
                        self.$notice.error({
                            title: '请正确填写设置信息！',
                        });
                    }
                });
            },
            submitSet() {
                const self = this;
                self.loading = true;
                self.$refs.setForm.validate(valid => {
                    if (valid) {
                        window.console.log(valid);
                    } else {
                        self.loading = false;
                        self.$notice.error({
                            title: '请正确填写设置信息！',
                        });
                    }
                });
            },
        },
    };
</script>
<template>
    <div class="seller-wrap">
        <div class="account-wrap">
            <tabs value="name1">
                <tab-pane label="账号管理" name="name1">
                    <card :bordered="false">
                        <div class="prompt-box">
                            <p>提示</p>
                            <p>1.只有商家后台总管理员可以增删管理员账号</p>
                            <p>2.管理员账号可操作商家后台的所有功能</p>
                        </div>
                        <i-button class="first-btn" type="ghost"
                        @click.native="addManager">+新增管理员</i-button>
                        <i-button type="ghost">批量删除</i-button>
                        <i-table :columns="accountColumns"
                                 :context="self"
                                 :data="accountData"
                                 ref="accountList">
                        </i-table>
                        <modal
                                v-model="addModal"
                                title="新增管理员" class="upload-picture-modal">
                            <div>
                                <i-form ref="form" :model="from" :rules="ruleValidate" :label-width="100">
                                    <row>
                                        <i-col span="16">
                                            <form-item label="登录账号">
                                                <i-input v-model="form.account"></i-input>
                                            </form-item>
                                        </i-col>
                                    </row>
                                    <row>
                                        <i-col span="16">
                                            <form-item label="真实姓名">
                                                <i-input v-model="form.name"></i-input>
                                            </form-item>
                                        </i-col>
                                    </row>
                                    <row>
                                        <i-col span="16">
                                            <form-item label="联系电话">
                                                <i-input v-model="form.phone"></i-input>
                                            </form-item>
                                        </i-col>
                                    </row>
                                    <row>
                                        <i-col span="16">
                                            <form-item>
                                                <i-button :loading="loading" type="primary" @click.native="submit">
                                                    <span v-if="!loading">确认提交</span>
                                                    <span v-else>正在提交…</span>
                                                </i-button>
                                            </form-item>
                                        </i-col>
                                    </row>
                                </i-form>
                            </div>
                        </modal>
                        <modal
                                v-model="setModal"
                                title="接受设置" class="upload-picture-modal">
                            <div>
                                <i-form ref="setForm" :model="setForm" :rules="setValidate" :label-width="100">
                                    <row>
                                        <i-col span="16">
                                            <form-item label="登录账号">
                                                {{ setForm.account }}
                                            </form-item>
                                        </i-col>
                                    </row>
                                    <row>
                                        <i-col span="16">
                                            <form-item label="密码">
                                                <i-input v-model="setForm.password"></i-input>
                                            </form-item>
                                        </i-col>
                                    </row>
                                    <row>
                                        <i-col span="16">
                                            <form-item>
                                                <i-button :loading="loading" type="primary" @click.native="submitSet">
                                                    <span v-if="!loading">确认提交</span>
                                                    <span v-else>正在提交…</span>
                                                </i-button>
                                            </form-item>
                                        </i-col>
                                    </row>
                                </i-form>
                            </div>
                        </modal>
                    </card>
                </tab-pane>
            </tabs>
        </div>
    </div>
</template>