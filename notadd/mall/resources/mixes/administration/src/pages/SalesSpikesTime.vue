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
                        key: 'spikeName',
                        title: '秒杀时段名称',
                        width: 200,
                    },
                    {
                        align: 'center',
                        key: 'startTime',
                        title: '每日开始时间',
                        width: 200,
                    },
                    {
                        key: 'endTime',
                        title: '每日结束时间',
                    },
                    {
                        align: 'center',
                        key: 'action',
                        render(h, data) {
                            return h('div', [
                                h('router-link', {
                                    props: {
                                        to: '/mall/sales/spikes/time/edit',
                                    },
                                }, [
                                    h('i-button', {
                                        props: {
                                            size: 'small',
                                            type: 'ghost',
                                        },
                                    }, '编辑'),
                                ]),
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
                        endTime: '2017-2-02',
                        spikeName: '午夜场',
                        startTime: '2017-2-02',
                    },
                    {
                        endTime: '2017-2-02',
                        spikeName: '午夜场',
                        startTime: '2017-2-02',
                    },
                    {
                        endTime: '2017-2-02',
                        spikeName: '午夜场',
                        startTime: '2017-2-02',
                    },
                    {
                        endTime: '2017-2-02',
                        spikeName: '午夜场',
                        startTime: '2017-2-02',
                    },
                ],
            };
        },
        methods: {
            goBack() {
                const self = this;
                self.$router.go(-1);
            },
            remove(index) {
                this.list.splice(index, 1);
            },
        },
    };
</script>
<template>
    <div class="mall-wrap">
        <div class="sales-spikes-time">
            <div class="edit-link-title">
                <i-button type="text" @click.native="goBack">
                    <icon type="chevron-left"></icon>
                </i-button>
                <span>秒杀频道设置-秒杀时间段列表</span>
            </div>
            <div class="spikes-information">
                <card :bordered="false">
                    <div class="prompt-box">
                        <p>提示</p>
                        <p>秒杀时段列表可对时间段进行新增/编辑/删除操作</p>
                        <p>建议设置四至五个是时间段（前台显示）</p>
                    </div>
                    <div class="goods-body-header">
                        <router-link to="/mall/sales/spikes/time/create" class="first-btn">
                            <i-button type="ghost">+添加秒杀时间段</i-button>
                        </router-link>
                    </div>
                    <i-table class="goods-table"
                             :columns="columns"
                             :context="self"
                             :data="list"
                             ref="goodsList">
                    </i-table>
                </card>
            </div>
        </div>
    </div>
</template>
