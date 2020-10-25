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
                        fixed: 'left',
                        type: 'selection',
                        width: 60,
                    },
                    {
                        key: 'name',
                        title: '名称',
                        width: 200,
                    },
                    {
                        key: 'type',
                        title: '类型',
                        width: 100,
                    },
                    {
                        key: 'showStyle',
                        title: '展示方式',
                        width: 200,
                    },
                    {
                        key: 'wordNum',
                        title: '宽度/字数',
                        width: 150,
                    },
                    {
                        key: 'heightNum',
                        title: '高度',
                        width: 150,
                    },
                    {
                        key: 'adverNum',
                        title: '广告数',
                        width: 150,
                    },
                    {
                        key: 'showNum',
                        title: '正在展示',
                        width: 150,
                    },
                    {
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
                                    '开启',
                                ]);
                            }
                            return h('span', [
                                h('icon', {
                                    props: {
                                        type: 'close-circled',
                                    },
                                }),
                                '关闭',
                            ]);
                        },
                        title: '是否启用',
                        width: 200,
                    },
                    {
                        align: 'center',
                        fixed: 'right',
                        key: 'action',
                        render(h, data) {
                            return h('div', [
                                h('dropdown', {
                                    scopedSlots: {
                                        list() {
                                            return h('dropdown-menu', [
                                                h('dropdown-item', '设置设置'),
                                            ]);
                                        },
                                    },
                                }, [
                                    h('i-button', {
                                        props: {
                                            type: 'ghost',
                                        },
                                    }, [
                                        '设置',
                                        h('icon', {
                                            props: {
                                                type: 'arrow-down-b',
                                            },
                                        }),
                                    ]),
                                ]),
                                h('i-button', {
                                    on: {
                                        click() {
                                            self.removeAd(data.index);
                                        },
                                    },
                                    props: {
                                        type: 'ghost',
                                    },
                                    style: {
                                        marginLeft: '10px',
                                    },
                                }, '删除'),
                            ]);
                        },
                        title: '操作',
                        width: 200,
                    },
                ],
                list: [
                    {
                        adverNum: 0,
                        heightNum: 206,
                        isshow: '是',
                        name: '商品列表左侧广告位',
                        showNum: 4,
                        showStyle: '多广告展示',
                        status: true,
                        type: '图片',
                        wordNum: 206,
                    },
                    {
                        adverNum: 0,
                        heightNum: 206,
                        isshow: '是',
                        name: '商品列表左侧广告位',
                        showNum: 4,
                        showStyle: '多广告展示',
                        status: true,
                        type: '图片',
                        wordNum: 206,
                    },
                    {
                        adverNum: 0,
                        heightNum: 206,
                        isshow: '是',
                        name: '商品列表左侧广告位',
                        showNum: 4,
                        showStyle: '多广告展示',
                        status: true,
                        type: '图片',
                        wordNum: 206,
                    },
                ],
            };
        },
        methods: {
            newAddData() {
                const self = this;
                self.$router.push({
                    path: 'advertisement/add',
                });
            },
            removeAd(index) {
                this.list.splice(index, 1);
            },
        },
    };
</script>
<template>
    <div class="mall-wrap">
        <div class="configuration-advertisement">
            <tabs value="name1">
                <tab-pane label="广告管理" name="name1">
                    <card :bordered="false">
                        <div class="prompt-box">
                            <p>提示</p>
                            <p>将广告位调用代码放入前台页面，将显示该广告位的广告</p>
                        </div>
                        <div class="advertisement-action">
                            <i-button class="add-data" type="ghost" @click.native="newAddData">+新增数据</i-button>
                            <i-button class="delete-data" type="ghost">批量删除</i-button>
                            <i-button type="text" icon="android-sync" class="refresh">刷新</i-button>
                        </div>
                        <i-table highlight-row :columns="columns" :context="self"
                                 :data="list"></i-table>
                    </card>
                </tab-pane>
            </tabs>
        </div>
    </div>
</template>