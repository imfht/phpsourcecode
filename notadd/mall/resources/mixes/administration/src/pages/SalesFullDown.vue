<script>
    export default {
        beforeRouteEnter(to, from, next) {
            next(() => {
            });
        },
        data() {
            const self = this;
            return {
                activeModal: false,
                form: {
                    name: '春季家电家具买疯狂优惠',
                    rule: ['单笔订单满200元  立减现金20', '单笔订单满500元  立减现金50', '单笔订单满1000元  立减现金200'],
                    shop: '爱拍数码',
                    time: '2017-04-01至2017-04-02',
                },
                columns: [
                    {
                        align: 'center',
                        key: 'num',
                        title: '编号',
                        width: 120,
                    },
                    {
                        key: 'name',
                        title: '活动名称',
                    },
                    {
                        align: 'center',
                        key: 'shopName',
                        title: '店铺名称',
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
                        key: 'action',
                        render(h, data) {
                            return h('div', [
                                h('i-button', {
                                    on: {
                                        click() {
                                            self.look();
                                        },
                                    },
                                    props: {
                                        size: 'small',
                                        type: 'ghost',
                                    },
                                }, '查看'),
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
                        endTime: '2016-12-23',
                        shopName: '店铺名称',
                        name: '时尚但不易过时，高上大，还配有眼睛盒，发货速度',
                        num: 222,
                        startTime: '2016-12-23',
                    },
                    {
                        endTime: '2016-12-23',
                        shopName: '店铺名称',
                        name: '时尚但不易过时，高上大，还配有眼睛盒，发货速度',
                        num: 222,
                        startTime: '2016-12-23',
                    },
                    {
                        endTime: '2016-12-23',
                        shopName: '店铺名称',
                        name: '时尚但不易过时，高上大，还配有眼睛盒，发货速度',
                        num: 222,
                        startTime: '2016-12-23',
                    },
                    {
                        endTime: '2016-12-23',
                        shopName: '店铺名称',
                        name: '时尚但不易过时，高上大，还配有眼睛盒，发货速度',
                        num: 222,
                        startTime: '2016-12-23',
                    },
                ],
                searchList: [
                    {
                        label: '活动名称',
                        value: '1',
                    },
                    {
                        label: '店铺名称',
                        value: '2',
                    },
                ],
            };
        },
        methods: {
            remove(index) {
                this.list.splice(index, 1);
            },
            look() {
                this.activeModal = true;
            },
        },
    };
</script>
<template>
    <div class="mall-wrap">
        <div class="sales-fulldown">
            <tabs value="name1">
                <tab-pane label="满减活动" name="name1">
                    <card :bordered="false">
                        <div class="prompt-box">
                            <p>提示</p>
                            <p>商家发布的满减活动列表</p>
                            <p>删除操作不可恢复，请慎重操作</p>
                        </div>
                        <div class="goods-body-header">
                            <i-button type="text" icon="android-sync" class="refresh">刷新</i-button>
                            <div class="goods-body-header-right">
                                <i-input v-model="managementWord" placeholder="请输入关键词进行搜索">
                                    <i-select v-model="managementSearch" slot="prepend" style="width: 100px;">
                                        <i-option v-for="item in searchList"
                                                  :value="item.value">{{ item.label }}</i-option>
                                    </i-select>
                                    <i-button slot="append" type="primary">搜索</i-button>
                                </i-input>
                            </div>
                        </div>
                        <i-table class="goods-table"
                                 :columns="columns"
                                 :context="self"
                                 :data="list"
                                 ref="goodsList">
                        </i-table>
                        <modal
                                v-model="activeModal"
                                title="活动详情" class="refund-attribute-modal">
                            <div class="sales-fulldown-modal">
                                <i-form ref="form" :model="form" :rules="rules" :label-width="100">
                                    <row>
                                        <i-col span="18">
                                            <form-item label="活动名称">
                                                {{ form.name }}
                                            </form-item>
                                        </i-col>
                                    </row>
                                    <row>
                                        <i-col span="18">
                                            <form-item label="活动店铺">
                                                {{ form.shop }}
                                            </form-item>
                                        </i-col>
                                    </row>
                                    <row>
                                        <i-col span="18">
                                            <form-item label="活动时间">
                                                {{ form.time }}
                                            </form-item>
                                        </i-col>
                                    </row>
                                    <row>
                                        <i-col span="18">
                                            <form-item label="活动规则">
                                                <p v-for="item in form.rule">{{ item }}</p>
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
