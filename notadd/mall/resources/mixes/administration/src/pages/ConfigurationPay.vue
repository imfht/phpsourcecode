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
                        type: 'selection',
                        width: 150,
                    },
                    {
                        key: 'style',
                        title: '支付方式',
                        width: 300,
                    },
                    {
                        key: 'status',
                        render(h, data) {
                            return h('i-switch', {
                                props: {
                                    size: 'large',
                                    value: data.row.status,
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
                        title: '当前状态',
                    },
                ],
                list: [
                    {
                        style: '货到付款',
                    },
                    {
                        style: '支付宝',
                    },
                    {
                        style: '财付通',
                    },
                    {
                        style: '银联',
                    },
                ],
                plugin: true,
                switch1: true,
            };
        },
        methods: {
            toEdit() {
                const self = this;
                self.$router.push(
                    {
                        path: 'pay',
                    },
                );
            },
        },
    };
</script>
<template>
    <div class="mall-wrap">
        <div class="configuration-pay">
            <tabs value="name1">
                <tab-pane label="支付方式" name="name1">
                    <card :bordered="false" v-if="plugin">
                        <div class="prompt-box">
                            <p>提示</p>
                            <p>此处列出了系统支持的支付方式，点击“编辑”按钮可以编辑支付参数及开关状态</p>
                        </div>
                        <div class="store-body">
                            <i-table  :columns="columns" :data="list" ref="payStyle"></i-table>
                        </div>
                    </card>
                    <card :bordered="false" v-if="!plugin">
                        <div class="pay-plugin">
                            <p>系统检测到您未安装插件，请安装后尝试</p>
                            <i-button type="ghost">立即安装</i-button>
                        </div>
                    </card>
                </tab-pane>
            </tabs>
        </div>
    </div>
</template>