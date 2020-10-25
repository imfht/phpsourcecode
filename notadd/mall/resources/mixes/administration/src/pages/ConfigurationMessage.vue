<script>
    export default {
        beforeRouteEnter(to, from, next) {
            next(() => {
            });
        },
        data() {
            const self = this;
            return {
                merchantColumns: [
                    {
                        align: 'center',
                        type: 'selection',
                        width: 100,
                    },
                    {
                        key: 'describe',
                        title: '模板描述',
                    },
                    {
                        align: 'center',
                        key: 'stationLetter',
                        render(h, data) {
                            return h('i-switch', {
                                props: {
                                    size: 'large',
                                    value: data.row.stationLetter,
                                },
                                scopedSlots: {
                                    close() {
                                        return h('span', '关闭');
                                    },
                                    open() {
                                        return h('span', '开启');
                                    },
                                },
                            });
                        },
                        title: '站内信',
                    },
                    {
                        align: 'center',
                        key: 'smsStatus',
                        render(h, data) {
                            return h('i-switch', {
                                props: {
                                    size: 'large',
                                    value: data.row.smsStatus,
                                },
                                scopedSlots: {
                                    close() {
                                        return h('span', '关闭');
                                    },
                                    open() {
                                        return h('span', '开启');
                                    },
                                },
                            });
                        },
                        title: '手机短信',
                    },
                    {
                        align: 'center',
                        key: 'mailStatus',
                        render(h, data) {
                            return h('i-switch', {
                                props: {
                                    size: 'large',
                                    value: data.row.mailStatus,
                                },
                                scopedSlots: {
                                    close() {
                                        return h('span', '关闭');
                                    },
                                    open() {
                                        return h('span', '开启');
                                    },
                                },
                            });
                        },
                        title: '邮件',
                    },
                    {
                        align: 'center',
                        key: 'action',
                        render(h, data) {
                            return h('i-button', {
                                on: {
                                    click() {
                                        self.toEdit(data.index);
                                    },
                                },
                                props: {
                                    class: 'editor-btn',
                                    size: 'small',
                                    type: 'ghost',
                                },
                            }, '编辑');
                        },
                        title: '操作',
                        width: '140',
                    },
                ],
                merchantData: [
                    {
                        describe: '商品库存预警',
                        mailStatus: true,
                        smsStatus: true,
                        stationLetter: false,
                    },
                    {
                        describe: '商品被投诉提醒',
                        mailStatus: false,
                        smsStatus: true,
                        stationLetter: true,
                    },
                    {
                        describe: '商品审核失败提醒',
                        mailStatus: true,
                        smsStatus: true,
                        stationLetter: false,
                    },
                    {
                        describe: '商品违规被下架',
                        mailStatus: false,
                        smsStatus: true,
                        stationLetter: false,
                    },
                    {
                        describe: '新订单提醒',
                        mailStatus: true,
                        smsStatus: false,
                        stationLetter: false,
                    },
                    {
                        describe: '退款提醒',
                        mailStatus: true,
                        smsStatus: false,
                        stationLetter: false,
                    },
                ],
                userColumns: [
                    {
                        align: 'center',
                        type: 'selection',
                        width: 100,
                    },
                    {
                        key: 'describe',
                        title: '模板描述',
                    },
                    {
                        align: 'center',
                        key: 'stationLetter',
                        render(h, data) {
                            return h('i-switch', {
                                props: {
                                    size: 'large',
                                    value: data.row.stationLetter,
                                },
                                scopedSlots: {
                                    close() {
                                        return h('span', '关闭');
                                    },
                                    open() {
                                        return h('span', '开启');
                                    },
                                },
                            });
                        },
                        title: '站内信',
                    },
                    {
                        align: 'center',
                        key: 'smsStatus',
                        render(h, data) {
                            return h('i-switch', {
                                props: {
                                    size: 'large',
                                    value: data.row.smsStatus,
                                },
                                scopedSlots: {
                                    close() {
                                        return h('span', '关闭');
                                    },
                                    open() {
                                        return h('span', '开启');
                                    },
                                },
                            });
                        },
                        title: '手机短信',
                    },
                    {
                        align: 'center',
                        key: 'mailStatus',
                        render(h, data) {
                            return h('i-switch', {
                                props: {
                                    size: 'large',
                                    value: data.row.mailStatus,
                                },
                                scopedSlots: {
                                    close() {
                                        return h('span', '关闭');
                                    },
                                    open() {
                                        return h('span', '开启');
                                    },
                                },
                            });
                        },
                        title: '邮件',
                    },
                    {
                        align: 'center',
                        key: 'action',
                        render(h, data) {
                            return h('i-button', {
                                on: {
                                    click() {
                                        self.toEdit(data.index);
                                    },
                                },
                                props: {
                                    class: 'editor-btn',
                                    size: 'small',
                                    type: 'ghost',
                                },
                            }, '编辑');
                        },
                        title: '操作',
                        width: 140,
                    },
                ],
                userData: [
                    {
                        describe: '到货通知提醒',
                        mailStatus: true,
                        smsStatus: true,
                        stationLetter: false,
                    },
                    {
                        describe: '商品咨询回复提醒',
                        mailStatus: true,
                        smsStatus: true,
                        stationLetter: false,
                    },
                    {
                        describe: '平台客服回复提醒',
                        mailStatus: true,
                        smsStatus: true,
                        stationLetter: true,
                    },
                    {
                        describe: '预定订单尾款支付提醒',
                        mailStatus: true,
                        smsStatus: true,
                        stationLetter: true,
                    },
                    {
                        describe: '付款成功提醒',
                        mailStatus: false,
                        smsStatus: true,
                        stationLetter: true,
                    },
                    {
                        describe: '余额变动提醒',
                        mailStatus: false,
                        smsStatus: true,
                        stationLetter: false,
                    },
                    {
                        describe: '充值卡余额变动提醒',
                        mailStatus: true,
                        smsStatus: false,
                        stationLetter: false,
                    },
                ],
            };
        },
        methods: {
            toEdit() {
                const self = this;
                self.$router.push({
                    path: 'message/editor',
                });
            },
        },
    };
</script>
<template>
    <div class="mall-wrap">
        <div class="configuration-message">
            <tabs value="name1">
                <tab-pane label="商家消息模板" name="name1">
                   <card :bordered="false">
                       <div class="prompt-box">
                           <p>提示</p>
                           <p>平台可给商家提供站内信、短消息、邮件三种通知方式。平台可以选择开启一种或多种通知方式供商家选择。</p>
                           <p>开启强制接收后，商家不能取消该方式通知的接收。</p>
                           <p> 短消息、邮件需要商家设置正确号码后才能正常接收。</p>
                           <p> 编辑完成后请清理“商家消息模板”缓存。</p>
                       </div>
                       <div class="store-body">
                           <i-table class="shop-table"
                                    :context="self"
                                    :columns="merchantColumns"
                                    :data="merchantData"
                                    highlight-row></i-table>
                       </div>
                   </card>
                </tab-pane>
                <tab-pane label="用户消息模板" name="name2">
                    <card :bordered="false">
                        <div class="prompt-box">
                            <p>提示</p>
                            <p>平台可以选择开启一种或多种消息通知方式</p>
                            <p>短消息、邮件需要用户绑定手机、邮箱后才能正常接收</p>
                            <p> 编辑完成后请清理“用户消息模板”缓存</p>
                        </div>
                        <div class="store-body">
                            <i-table class="shop-table" :columns="userColumns" :data="userData"></i-table>
                        </div>
                    </card>
                </tab-pane>
            </tabs>
        </div>
    </div>
</template>