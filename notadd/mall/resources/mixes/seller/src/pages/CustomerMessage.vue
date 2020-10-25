<script>
    export default {
        beforeRouteEnter(to, from, next) {
            next(() => {
            });
        },
        data() {
            const self = this;
            return {
                announceColumns: [
                    {
                        key: 'title',
                        title: '标题',
                    },
                    {
                        align: 'center',
                        key: 'time',
                        title: '发布时间',
                        width: 200,
                    },
                ],
                announceData: [
                    {
                        time: '2017-6-9 15:30',
                        title: '您的结算单平台已付款，请注意查收，结算单编号：37216565965',
                    },
                ],
                form: {
                    content: '您的结算单平台已付款，请注意查收，结算单编号：37',
                    sendTime: '2017-04-01 13:10:59',
                },
                messageColumns: [
                    {
                        align: 'center',
                        title: '排序',
                        type: 'selection',
                        width: 80,
                    },
                    {
                        key: 'content',
                        title: '消息内容',
                    },
                    {
                        align: 'center',
                        key: 'sendTime',
                        title: '发送时间',
                        width: 280,
                    },
                    {
                        align: 'center',
                        key: 'action',
                        render(h, data) {
                            return h('div', [
                                h('i-button', {
                                    class: {
                                        'delete-ad': true,
                                    },
                                    on: {
                                        click() {
                                            self.look(data.index);
                                        },
                                    },
                                    props: {
                                        size: 'small',
                                        type: 'ghost',
                                    },
                                }, '查看'),
                                h('i-button', {
                                    class: {
                                        'delete-ad': true,
                                    },
                                    on: {
                                        click() {
                                            self.remove(data.index);
                                        },
                                    },
                                    props: {
                                        size: 'small',
                                        type: 'ghost',
                                    },
                                }, '删除'),
                            ]);
                        },
                        title: '操作',
                        width: 280,
                    },
                ],
                messageData: [
                    {
                        content: '您的结算单平台已付款，请注意查收，结算单编号：37216565965',
                        sendTime: '2016-12-20 13:31:54',
                    },
                    {
                        content: '您的结算单平台已付款，请注意查收，结算单编号：37216565965',
                        sendTime: '2016-12-20 13:31:54',
                    },
                    {
                        content: '您的结算单平台已付款，请注意查收，结算单编号：37216565965',
                        sendTime: '2016-12-20 13:31:54',
                    },
                    {
                        content: '您的结算单平台已付款，请注意查收，结算单编号：37216565965',
                        sendTime: '2016-12-20 13:31:54',
                    },
                ],
                messageModal: false,
                self: this,
                settingColumns: [
                    {
                        key: 'title',
                        title: '模板名称',
                    },
                    {
                        align: 'center',
                        key: 'style',
                        title: '接收方式',
                        width: 200,
                    },
                    {
                        align: 'center',
                        key: 'disabled',
                        render(h, data) {
                            return h('i-switch', {
                                props: {
                                    size: 'large',
                                    value: data.row.disabled,
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
                        title: '是否接收',
                        width: 200,
                    },
                ],
                settingData: [
                    {
                        disabled: true,
                        style: '站内提醒',
                        title: '商品被投诉提醒',
                    },
                    {
                        disabled: true,
                        style: '站内提醒',
                        title: '商品库存预警',
                    },
                    {
                        disabled: true,
                        style: '站内提醒',
                        title: '商品审核失败提醒',
                    },
                    {
                        disabled: true,
                        style: '站内提醒',
                        title: '商品违规被下架',
                    },
                    {
                        disabled: true,
                        style: '站内提醒',
                        title: '新订单提醒',
                    },
                    {
                        disabled: true,
                        style: '站内提醒',
                        title: '退款提醒',
                    },
                    {
                        disabled: true,
                        style: '站内提醒',
                        title: '退款自动处理提醒',
                    },
                    {
                        disabled: true,
                        style: '站内提醒',
                        title: '退货未收货自动处理提醒',
                    },
                    {
                        disabled: true,
                        style: '站内提醒',
                        title: '退货未收货自动处理提醒',
                    },
                ],
            };
        },
        methods: {
            look() {
                this.messageModal = true;
            },
            remove(index) {
                this.messageData.splice(index, 1);
            },
        },
    };
</script>
<template>
    <div class="seller-wrap">
        <div class="customer-message">
            <tabs value="name1">
                <tab-pane label="消息列表" name="name1">
                    <card :bordered="false">
                        <div class="prompt-box">
                            <p>提示</p>
                            <p>1.管理员可以看见全部消息</p>
                            <p>2.只有管理员可以删除消息，删除后其他账户的该条消息也将被删除</p>
                        </div>
                        <i-button class="first-btn" type="ghost">标位已读</i-button>
                        <i-button type="ghost">批量删除</i-button>
                        <i-table :columns="messageColumns"
                                 :context="self"
                                 :data="messageData"
                                 ref="messageList">
                        </i-table>
                        <modal
                                v-model="messageModal"
                                title="系统消息" class="upload-picture-modal customer-message-modal">
                            <div>
                                <i-form ref="form" :model="from" :rules="ruleValidate" :label-width="100">
                                    <row>
                                        <i-col span="16">
                                            <form-item label="发送时间">
                                                {{ form.sendTime }}
                                            </form-item>
                                        </i-col>
                                    </row>
                                    <row>
                                        <i-col span="16">
                                            <form-item label="消息内容">
                                                {{ form.content }}
                                            </form-item>
                                        </i-col>
                                    </row>
                                </i-form>
                            </div>
                        </modal>
                    </card>
                </tab-pane>
                <tab-pane label="系统公告" name="name2">
                    <card :bordered="false">
                        <i-table :columns="announceColumns"
                                 :context="self"
                                 :data="announceData"
                                 ref="announceList">
                        </i-table>
                    </card>
                </tab-pane>
                <tab-pane label="消息接收设置" name="name3">
                    <card :bordered="false">
                        <div class="prompt-box">
                            <p>提示</p>
                            <p>1.短信、邮件接收方式需要正确设置接收号码才能正常接收</p>
                            <p>2.子账号接收消息权限请到账号组中设置</p>
                        </div>
                        <i-table :columns="settingColumns"
                                 :context="self"
                                 :data="settingData"
                                 ref="settingList">
                        </i-table>
                    </card>
                </tab-pane>
            </tabs>
        </div>
    </div>
</template>