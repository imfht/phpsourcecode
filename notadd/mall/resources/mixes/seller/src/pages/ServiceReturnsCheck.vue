<script>
    import image from '../assets/images/img_logo.png';

    export default {
        beforeRouteEnter(to, from, next) {
            next(() => {
            });
        },
        data() {
            return {
                form: {
                    amount: 1,
                    freight: 10.00,
                    goodsName: 'MIUI /小米小米手机4小米4代MI4智能4G手机包邮黑色D-LTE（4G）/ TD-SCD',
                    logistics: [
                        { information: '2016-12-21 11:30:31  发往西安中转' },
                        { information: '2016-12-21 11:30:31  发往西安中转' },
                        { information: '2016-12-21 11:30:31  发往西安中转' },
                        { information: '2016-12-21 11:30:31  发往西安中转' },
                    ],
                    logisticsCompany: '顺丰快递',
                    pic: image,
                    picture: image,
                    price: 1999.00,
                    returnMoney: '99.00',
                    returnName: 'maijia',
                    returnNumber: '263567946465245485',
                    returnResponse: '尺码不标准',
                    returnsNote: '退款',
                    returnsRefund: '退款',
                    returnsPlatform: '已完成',
                    returnsIdea: '同意',
                    returnsGoods: '确认物品完整',
                    returnsLogistics: '圆通快递 323264991323',
                    returnState: '发货太慢',
                    serial: 1254525945416,
                    trackingNumber: '326596623565694313',
                },
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
            };
        },
        methods: {
            goBack() {
                const self = this;
                self.$router.go(-1);
            },
        },
        computed: {
            checkOrder() {
                return (this.form.price * this.form.amount)
                        + this.form.freight;
            },
        },
    };
</script>
<template>
    <div class="seller-wrap">
        <div class="service-returns-check">
            <div class="edit-link-title">
                <i-button type="text" @click.native="goBack">
                    <icon type="chevron-left"></icon>
                </i-button>
                <span>退货记录-查看</span>
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
                                        <form-item label="退货编号">
                                            {{ form.returnNumber }}
                                        </form-item>
                                    </i-col>
                                </row>
                                <row>
                                    <i-col>
                                        <form-item label="申请人(买家)">
                                            {{ form.returnName }}
                                        </form-item>
                                    </i-col>
                                </row>
                                <row>
                                    <i-col>
                                        <form-item label="退货原因">
                                            {{ form.returnResponse }}
                                        </form-item>
                                    </i-col>
                                </row>
                                <row>
                                    <i-col>
                                        <form-item label="退款金额">
                                            {{ form.returnMoney }}
                                        </form-item>
                                    </i-col>
                                </row>
                                <row>
                                    <i-col>
                                        <form-item label="退货说明">
                                            {{ form.returnState }}
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
                                            {{ form.returnsIdea }}
                                        </form-item>
                                    </i-col>
                                </row>
                                <row>
                                    <i-col>
                                        <form-item label="备注信息">
                                            {{ form.returnsNote }}
                                        </form-item>
                                    </i-col>
                                </row>
                                <row>
                                    <i-col>
                                        <form-item label="物流单号">
                                            {{ form.returnsLogistics }}
                                        </form-item>
                                    </i-col>
                                </row>
                                <row>
                                    <i-col>
                                        <form-item label="收货备注">
                                            {{ form.returnsGoods }}
                                        </form-item>
                                    </i-col>
                                </row>
                                <h5>商城平台审核</h5>
                                <row>
                                    <i-col>
                                        <form-item label="平台确认">
                                            {{ form.returnsPlatform }}
                                        </form-item>
                                    </i-col>
                                </row>
                                <row>
                                    <i-col>
                                        <form-item label="退款方式">
                                            {{ form.returnsRefund }}
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
                                            {{ checkOrder }}
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
                                <h5>物流信息</h5>
                                <row>
                                    <i-col>
                                        <form-item label="物流公司">
                                            {{ form.logisticsCompany }}
                                        </form-item>
                                    </i-col>
                                </row>
                                <row>
                                    <i-col>
                                        <form-item label="运单号码">
                                            {{ form.trackingNumber }}
                                        </form-item>
                                    </i-col>
                                </row>
                                <row>
                                    <i-col>
                                        <form-item label="运单号码">
                                            <p v-for="item in form.logistics" class="logistics-check"
                                            >{{item.information}}</p>
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