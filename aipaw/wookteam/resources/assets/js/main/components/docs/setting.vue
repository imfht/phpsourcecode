<template>
    <drawer-tabs-container>
        <div class="book-setting">
            <Form ref="formSystem" :model="formSystem" :label-width="80" @submit.native.prevent>
                <FormItem :label="$L('文档链接')">
                    <a class="form-link" target="_blank" :href="$A.webUrl('docs/view/b' + this.id)">{{$A.webUrl('docs/view/b' + this.id)}}</a>
                </FormItem>
                <FormItem :label="$L('管理权限')">
                    <div>
                        <div class="form-title">{{$L('修改权限')}}</div>
                        <div>
                            <div>
                                <RadioGroup v-model="formSystem.role_edit">
                                    <Radio label="private">{{$L('私有文库')}}</Radio>
                                    <Radio label="member">{{$L('成员开放')}}</Radio>
                                    <Radio label="reg">{{$L('注册会员')}}</Radio>
                                </RadioGroup>
                            </div>
                            <div v-if="formSystem.role_edit=='private'" class="form-placeholder">
                                {{$L('仅作者可以修改。')}}
                            </div>
                            <div v-else-if="formSystem.role_edit=='member'" class="form-placeholder">
                                {{$L('仅作者和文档成员可以修改。')}}
                            </div>
                            <div v-else-if="formSystem.role_edit=='reg'" class="form-placeholder">
                                {{$L('所有会员都可以修改。')}}
                            </div>
                        </div>
                    </div>
                    <div>
                        <div class="form-title">{{$L('查看权限')}}</div>
                        <div>
                            <div>
                                <RadioGroup v-if="formSystem.role_edit=='reg'" value="reg">
                                    <Radio label="edit" disabled>{{$L('修改权限')}}</Radio>
                                    <Radio label="reg" disabled>{{$L('注册会员')}}</Radio>
                                </RadioGroup>
                                <RadioGroup v-else v-model="formSystem.role_look">
                                    <Radio label="edit">{{$L('同修改权限')}}</Radio>
                                    <Radio label="reg">{{$L('注册会员')}}</Radio>
                                </RadioGroup>
                            </div>
                            <div v-if="formSystem.role_look=='reg' || formSystem.role_edit=='reg'" class="form-placeholder">
                                {{$L('所有会员都可以修改。')}}
                            </div>
                            <div v-else-if="formSystem.role_look=='edit'" class="form-placeholder">
                                {{$L('仅有修改权限的人员。')}}
                            </div>
                        </div>
                    </div>
                </FormItem>
                <FormItem :label="$L('阅读权限')">
                    <div>
                        <RadioGroup v-model="formSystem.role_view">
                            <Radio label="private">{{$L('私有文库')}}</Radio>
                            <Radio label="member">{{$L('成员开放')}}</Radio>
                            <Radio label="reg">{{$L('注册会员')}}</Radio>
                            <Radio label="all">{{$L('完全开放')}}</Radio>
                        </RadioGroup>
                    </div>
                    <div v-if="formSystem.role_view=='private'" class="form-placeholder">
                        {{$L('仅作者可以阅读分享地址。')}}
                    </div>
                    <div v-else-if="formSystem.role_view=='member'" class="form-placeholder">
                        {{$L('仅作者和文档成员可以阅读分享地址。')}}
                    </div>
                    <div v-else-if="formSystem.role_view=='reg'" class="form-placeholder">
                        {{$L('所有会员都可以阅读分享地址。')}}
                    </div>
                    <div v-else-if="formSystem.role_view=='all'" class="form-placeholder">
                        {{$L('所有人（含游客）都可以阅读分享地址。')}}
                    </div>
                </FormItem>
                <FormItem>
                    <Button :loading="loadIng > 0" type="primary" @click="handleSubmit('formSystem')">{{$L('提交')}}</Button>
                    <Button :loading="loadIng > 0" @click="handleReset('formSystem')" style="margin-left: 8px">{{$L('重置')}}</Button>
                </FormItem>
            </Form>
        </div>
    </drawer-tabs-container>
</template>

<style lang="scss" scoped>
    .book-setting {
        padding: 0 12px;
        .form-title {
            font-weight: bold;
        }
        .form-link {
            text-decoration: underline;
        }
        .form-placeholder {
            font-size: 12px;
            color: #999999;
        }
        .form-placeholder:hover {
            color: #000000;
        }
    }
</style>
<script>
    import DrawerTabsContainer from "../DrawerTabsContainer";
    export default {
        name: 'BookSetting',
        components: {DrawerTabsContainer},
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

                formSystem: {},
            }
        },
        mounted() {
            if (this.canload) {
                this.loadYet = true;
                this.getSetting();
            }
        },

        watch: {
            id() {
                if (this.loadYet) {
                    this.getSetting();
                }
            },
            canload(val) {
                if (val && !this.loadYet) {
                    this.loadYet = true;
                    this.getSetting();
                }
            }
        },

        methods: {
            getSetting(save) {
                this.loadIng++;
                $A.apiAjax({
                    url: 'docs/book/setting?type=' + (save ? 'save' : 'get'),
                    data: Object.assign(this.formSystem, {
                        id: this.id
                    }),
                    complete: () => {
                        this.loadIng--;
                    },
                    success: (res) => {
                        if (res.ret === 1) {
                            let data = res.data;
                            data.role_edit = data.role_edit || 'reg';
                            data.role_look = data.role_look || 'edit';
                            data.role_view = data.role_view || 'all';
                            this.formSystem = data;
                            this.formSystem__reset = $A.cloneData(this.formSystem);
                            if (save) {
                                this.$Message.success(this.$L('修改成功'));
                            }
                        } else {
                            if (save) {
                                this.$Modal.error({title: this.$L('温馨提示'), content: res.msg });
                            }
                        }
                    }
                });
            },
            handleSubmit(name) {
                this.$refs[name].validate((valid) => {
                    if (valid) {
                        switch (name) {
                            case "formSystem": {
                                this.getSetting(true);
                                break;
                            }
                        }
                    }
                })
            },
            handleReset(name) {
                if (typeof this[name + '__reset'] !== "undefined") {
                    this[name] = $A.cloneData(this[name + '__reset']);
                    return;
                }
                this.$refs[name].resetFields();
            },
        }
    }
</script>
