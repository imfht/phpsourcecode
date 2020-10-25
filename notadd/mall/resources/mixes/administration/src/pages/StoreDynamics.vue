<script>
    export default {
        beforeRouteEnter(to, from, next) {
            next(() => {
            });
        },
        data() {
            const self = this;
            return {
                columns: [
                    {
                        align: 'center',
                        type: 'selection',
                        width: 60,
                    },
                    {
                        align: 'center',
                        key: 'title',
                        title: '动态标题',
                        width: 180,
                    },
                    {
                        align: 'center',
                        key: 'name',
                        title: '店铺名称',
                        width: 180,
                    },
                    {
                        align: 'center',
                        key: 'id',
                        title: '店铺ID',
                    },
                    {
                        align: 'center',
                        key: 'isshow',
                        render(h, data) {
                            if (data.row.status) {
                                return h('span', {
                                    props: {
                                        class: 'status-check',
                                    },
                                }, [
                                    h('icon', {
                                        props: {
                                            type: 'checkmark-circled',
                                        },
                                    }),
                                    '是',
                                ]);
                            }
                            return h('span', [
                                h('icon', {
                                    props: {
                                        type: 'close-circled',
                                    },
                                }),
                                '否',
                            ]);
                        },
                        title: '是否推荐品牌',
                    },
                    {
                        align: 'center',
                        key: 'time',
                        title: '发表时间',
                    },
                    {
                        align: 'center',
                        key: 'num',
                        title: '转播数量',
                    },
                    {
                        align: 'center',
                        key: 'account',
                        title: '评论数量',
                    },
                    {
                        align: 'center',
                        key: 'action',
                        render(h, data) {
                            return h('div', [
                                h('i-button', {
                                    on: {
                                        click() {
                                            self.edit();
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
                list: [
                    {
                        account: 324,
                        id: 3,
                        isshow: '是',
                        name: 'karcher凯驰旗舰店',
                        num: 42,
                        status: true,
                        time: '2016-12-12  16:11:27',
                        title: '亲，我家又上新款了。',
                    },
                    {
                        account: 324,
                        id: 3,
                        isshow: '是',
                        name: 'karcher凯驰旗舰店',
                        num: 42,
                        status: true,
                        time: '2016-12-12  16:11:27',
                        title: '亲，我家又上新款了。',
                    },
                    {
                        account: 324,
                        id: 3,
                        isshow: '是',
                        name: 'karcher凯驰旗舰店',
                        num: 42,
                        status: true,
                        time: '2016-12-12  16:11:27',
                        title: '亲，我家又上新款了。',
                    },
                    {
                        account: 324,
                        id: 3,
                        isshow: '是',
                        name: 'karcher凯驰旗舰店',
                        num: 42,
                        status: true,
                        time: '2016-12-12  16:11:27',
                        title: '亲，我家又上新款了。',
                    },
                ],
            };
        },
        methods: {
            edit() {
                const self = this;
                self.$router.push({
                    path: 'brand/edit',
                });
            },
            exportData() {
                this.$refs.brand.exportCsv({
                    filename: '品牌管理数据',
                });
            },
            newAddData() {
                const self = this;
                self.$router.push({
                    path: 'brand/add',
                });
            },
            remove(index) {
                this.list.splice(index, 1);
            },
        },
    };
</script>
<template>
    <div class="mall-wrap">
        <div class="goods-dynamics">
            <tabs value="name1">
                <tab-pane label="店铺动态" name="name1">
                    <card :bordered="false">
                        <div class="prompt-box">
                            <p>提示</p>
                            <p>如果动态信息存在不合法内容您可以将其状态设置为屏蔽或者直接删除</p>
                            <p>动态删除后其对应的评论也一并删除，请慎重</p>
                        </div>
                        <div class="brand-management">
                            <i-button class="add-data" type="ghost" @click.native="newAddData">全部评论</i-button>
                            <i-button type="text" icon="android-sync" class="refresh">刷新</i-button>
                        </div>
                        <i-table :columns="columns"
                                 :context="self"
                                 :data="list"
                                 highlight-row
                                 ref="brand">
                        </i-table>
                    </card>
                </tab-pane>
            </tabs>
        </div>
    </div>
</template>