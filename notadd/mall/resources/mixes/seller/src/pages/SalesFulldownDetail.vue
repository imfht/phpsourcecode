<script>
    export default {
        beforeRouteEnter(to, from, next) {
            next(() => {
            });
        },
        data() {
            return {
                columns: [
                    {
                        align: 'center',
                        key: 'activeName',
                        title: '活动名称',
                    },
                    {
                        align: 'center',
                        key: 'activeContent',
                        render(h, data) {
                            return h('div', {
                                style: {
                                    padding: '10px 0',
                                },
                            }, [
                                h('p', data.row.content1),
                                h('p', data.row.content2),
                                h('p', data.row.content3),
                            ]);
                        },
                        title: '活动内容',
                    },
                    {
                        align: 'center',
                        key: 'startTime',
                        title: '开始时间',
                    },
                    {
                        align: 'center',
                        key: 'endTime',
                        title: '结束时间',
                    },
                    {
                        align: 'center',
                        key: 'status',
                        title: '状态',
                        width: 180,
                    },
                ],
                list: [
                    {
                        activeName: '春季服装大秒杀活动',
                        content1: '单笔订单满100元，立减现金5元',
                        content2: '单笔订单满100元，立减现金5元',
                        content3: '单笔订单满100元，立减现金5元',
                        endTime: '2018-02-30',
                        startTime: '2018-04-23',
                        status: '已结束',
                    },
                ],
                loading: false,
            };
        },
        methods: {
            goBack() {
                const self = this;
                self.$router.go(-1);
            },
        },
    };
</script>
<template>
    <div class="seller-wrap">
        <div class="sales-fulldown-detail">
            <div class="edit-link-title">
                <i-button type="text" @click.native="goBack">
                    <icon type="chevron-left"></icon>
                </i-button>
                <span>活动列表-活动内容</span>
            </div>
            <card :bordered="false">
                <i-table class="goods-table"
                         :columns="columns"
                         :context="self"
                         :data="list">
                </i-table>
            </card>
        </div>
    </div>
</template>
