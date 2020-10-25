<script>
    import image from '../assets/images/img_logo.png';

    export default {
        beforeRouteEnter(to, from, next) {
            next(() => {
            });
        },
        data() {
            return {
                checkSteps: [
                    {
                        content: '2016-12-21 13:11:20',
                        title: '买家申请退款',
                    },
                    {
                        content: '2016-12-21 13:11:20',
                        title: '商家处理申请',
                    },
                    {
                        title: '平台审核',
                    },
                    {
                        title: '退货完成',
                    },
                ],
                form: {
                    amount: 1,
                    freight: 10.00,
                    goodsName: 'MIUI /小米小米手机4小米4代MI4智能4G手机包邮黑色D-LTE（4G）/ TD-SCD',
                    information: '退款',
                    money: 99.00,
                    number: 263567946465245485,
                    orderNum: 1254525945416,
                    person: 'maijia',
                    price: 1999.00,
                    picture: image,
                    response: '未按时发货',
                    state: '发货太慢',
                    whether: '同意',
                },
            };
        },
        methods: {
            goBack() {
                const self = this;
                self.$router.go(-1);
            },
        },
        computed: {
            refundTotal() {
                return (this.form.price * this.form.amount)
                        + this.form.freight;
            },
        },
    };
</script>
<template>
    <div class="seller-wrap">
        <div class="service-refund-check">
            <div class="edit-link-title">
                <i-button type="text" @click.native="goBack">
                    <icon type="chevron-left"></icon>
                </i-button>
                <span>退款记录-查看</span>
            </div>
            <card :bordered="false">
                <div class="refund-details">
                    <i-form ref="form" :model="form" :rules="rule" :label-width="160">
                        <row>
                            <i-col span="15">
                                <h5>退款服务</h5>
                                <row>
                                    <i-col>
                                        <div class="check-step">
                                            <steps :current="1">
                                                <step :content="step.content" :title="step.title"
                                                      v-for="step in checkSteps"></step>
                                            </steps>
                                        </div>
                                    </i-col>
                                </row>
                                <h5>买家退款申请</h5>
                                <row>
                                    <i-col>
                                        <form-item label="退款编号">
                                            {{ form.number }}
                                        </form-item>
                                    </i-col>
                                </row>
                                <row>
                                    <i-col>
                                        <form-item label="申请人(买家)">
                                            {{ form.person }}
                                        </form-item>
                                    </i-col>
                                </row>
                                <row>
                                    <i-col>
                                        <form-item label="退款原因">
                                            {{ form.response }}
                                        </form-item>
                                    </i-col>
                                </row>
                                <row>
                                    <i-col>
                                        <form-item label="退款金额">
                                            {{ form.money }}
                                        </form-item>
                                    </i-col>
                                </row>
                                <row>
                                    <i-col>
                                        <form-item label="退款说明">
                                            {{ form.state }}
                                        </form-item>
                                    </i-col>
                                </row>
                                <row>
                                    <i-col>
                                        <form-item label="凭证上传" class="form-border">
                                            <img :src="form.picture" alt="">
                                        </form-item>
                                    </i-col>
                                </row>
                                <h5>商家处理意见</h5>
                                <row>
                                    <i-col>
                                        <form-item label="是否同意">
                                            {{ form.whether }}
                                        </form-item>
                                    </i-col>
                                </row>
                                <row>
                                    <i-col>
                                        <form-item label="备注信息">
                                            {{ form.information }}
                                        </form-item>
                                    </i-col>
                                </row>
                            </i-col>
                            <i-col span="9">
                                <h5>商品交易信息</h5>
                                <div class="goods-intro-content">
                                    <row>
                                        <i-col span="5">
                                            <img :src="form.picture" alt="">
                                        </i-col>
                                        <i-col span="19">
                                            <p>{{ form.goodsName }}</p>
                                            <p><i>&yen;{{ form.price }}</i>*{{ form.amount }}(数量)</p>
                                        </i-col>
                                    </row>
                                </div>
                                <row>
                                    <i-col>
                                        <form-item label="运费">
                                            {{ form.freight }}
                                        </form-item>
                                    </i-col>
                                </row>
                                <row>
                                    <i-col>
                                        <form-item label="订单总额">
                                            {{ refundTotal }}
                                        </form-item>
                                    </i-col>
                                </row>
                                <row>
                                    <i-col>
                                        <form-item label="订单编号">
                                            {{ form.orderNum }}
                                        </form-item>
                                    </i-col>
                                </row>
                            </i-col>
                        </row>
                    </i-form>
                </div>
            </card>
        </div>
    </div>
</template>