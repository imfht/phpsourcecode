<script>
    import injection from '../helpers/injection';

    export default {
        beforeRouteEnter(to, from, next) {
            injection.loading.start();
            injection.http.all([
                injection.http.post(`${window.api}/mall/admin/configuration/search/get`),
                injection.http.post(`${window.api}/mall/admin/configuration/search/hot/list`),
            ]).then(injection.http.spread((defaultData, searchDate) => {
                next(vm => {
                    vm.form.defaultSearch = defaultData.data.data.default;
                    vm.list = searchDate.data.data;
                    injection.loading.finish();
                });
            })).catch(() => {
                injection.loading.error();
            });
        },
        data() {
            return {
                form: {
                    defaultSearch: '',
                },
                loading: false,
                rules: {
                    defaultSearch: [
                        {
                            message: '请输入默认关键词',
                            required: true,
                            trigger: 'change',
                            type: 'string',
                        },
                    ],
                },
                columns: [
                    {
                        align: 'center',
                        type: 'selection',
                        width: 100,
                    },
                    {
                        align: 'center',
                        key: 'searchTerms',
                        title: '搜索词',
                        width: 300,
                    },
                    {
                        align: 'left',
                        key: 'showTerms',
                        title: '显示词',
                    },
                    {
                        align: 'center',
                        key: 'action',
                        render(h, data) {
                            return h('div', [
                                h('i-button', {
                                    on: {
                                        click() {
                                            self.searchEdit(data.index);
                                        },
                                    },
                                    props: {
                                        class: 'first-btn',
                                        size: 'small',
                                        type: 'ghost',
                                    },
                                }, '编辑'),
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
                                }, '编辑'),
                            ]);
                        },
                        title: '操作',
                        width: 180,
                    },
                ],
                list: [],
            };
        },
        methods: {
            addData() {
                const self = this;
                self.$router.push({
                    path: 'search/add',
                });
            },
            remove(index) {
                this.list.splice(index, 1);
            },
            searchEdit() {
                const self = this;
                self.$router.push({
                    path: 'search/editor',
                });
            },
            submit() {
                const self = this;
                self.loading = true;
                self.$refs.form.validate(valid => {
                    if (valid) {
                        self.$http.post(`${window.api}/mall/admin/configuration/search/set`, self.form).then(() => {
                            self.$notice.open({
                                title: '设置默认搜索词成功！',
                            });
                        }).finally(() => {
                            self.loading = false;
                        });
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
    <div class="mall-wrap">
        <div class="configuration-search">
            <tabs value="defaultSearch">
                <tab-pane label="默认搜索" name="defaultSearch">
                    <card :bordered="false">
                        <div class="store-body">
                            <i-form :label-width="200" ref="form" :model="form" :rules="rules">
                                <row>
                                    <i-col span="12">
                                        <form-item label="默认搜索词" prop="defaultSearch">
                                            <i-input placeholder="请输入默认搜索词" v-model="form.defaultSearch"></i-input>
                                            <p class="tip">默认词设置将显示在前台搜索框下面，前台点击时直接作为关键词进行搜索，多个请用半角逗号","隔开</p>
                                        </form-item>
                                    </i-col>
                                </row>
                                <form-item>
                                    <i-button :loading="loading" type="primary" @click.native="submit">
                                        <span v-if="!loading">确认提交</span>
                                        <span v-else>正在提交…</span>
                                    </i-button>
                                </form-item>
                            </i-form>
                        </div>
                    </card>
                </tab-pane>
                <tab-pane label="热门搜索" name="hot">
                    <card :bordered="false">
                        <div class="prompt-box">
                            <p>提示</p>
                            <p>热门搜索词设置后，将显示在前台搜索框作为默认值随机出现，最多可设置10个热搜词</p>
                            <p>10个热搜词包括搜索词和显示词两部分，搜索词参与搜索，显示此不参与搜索，只显示作用</p>
                        </div>
                        <div class="store-body">
                            <div class="store-body-header">
                                <i-button class="export-btn" @click.native="addData"
                                          type="ghost" >+新增搜索词</i-button>
                            </div>
                            <i-table class="shop-table"
                                     :columns="columns"
                                     :context="self"
                                     :data="list"
                                     highlight-row
                                     ref="searchTable">
                            </i-table>
                        </div>
                    </card>
                </tab-pane>
            </tabs>
        </div>
    </div>
</template>