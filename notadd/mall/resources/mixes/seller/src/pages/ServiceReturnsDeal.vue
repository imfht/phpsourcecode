<script>
    import image from '../assets/images/img_logo.png';

    export default {
        beforeRouteEnter(to, from, next) {
            next(() => {
            });
        },
        data() {
            return {
                dealSteps: [
                    {
                        content: '2016-12-21 13:11:20',
                        title: '买家申请退货',
                    },
                    {
                        content: '2016-12-21 13:11:20',
                        title: '商家处理退货申请',
                    },
                    {
                        title: '买家退货给商家',
                    },
                    {
                        title: '确认收货，平台审核完成退款',
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
                loading: false,
                whether: '同意',
            };
        },
        methods: {
            goBack() {
                const self = this;
                self.$router.go(-1);
            },
        },
        computed: {
            dealTotal() {
                return (this.form.price * this.form.amount)
                        + this.form.freight;
            },
        },
    };
</script>
<template>
    <div class="seller-wrap">
        <div class="service-returns-deal">
            <div class="edit-link-title">
                <i-button type="text" @click.native="goBack">
                    <icon type="chevron-left"></icon>
                </i-button>
                <span>退货记录-处理</span>
            </div>
            <card :bordered="false">
                <div class="deal-details">
                    <i-form ref="form" :model="form" :rules="rule" :label-width="160">
                        <row>
                            <i-col span="15">
                                <h5>退货服务</h5>
                                <row>
                                    <i-col>
                                        <div class="check-step">
                                            <steps :current="1">
                                                <step :content="step.content" :title="step.title"
                                                      v-for="step in dealSteps"></step>
                                            </steps>
                                        </div>
                                    </i-col>
                                </row>
                                <h5>买家退货申请</h5>
                                <row>
                                    <i-col>
                                        <form-item label="退货编号">
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
                                        <form-item label="退货原因">
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
                                        <form-item label="退货说明">
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
                                            <radio-group v-model="whether">
                                                <radio label="同意"></radio>
                                                <radio label="弃货"></radio>
                                                <radio label="拒绝"></radio>
                                            </radio-group>
                                            <p class="tip">如果选择弃货，买家将不用退回原商品，提交后直接由管理员确认退款</p>
                                        </form-item>
                                    </i-col>
                                </row>
                                <row>
                                    <i-col span="22">
                                        <form-item label="备注信息">
                                            <i-input type="textarea" :autosize="{minRows: 3,maxRows: 5}"></i-input>
                                            <p class="tip">如是同意退货，请及时关注买家的发货情况，并机型收货（发货5天后可以
                                                选择未收到，超过7天不处理按弃货处理）</p>
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
                                            {{ dealTotal }}
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
                <row>
                    <i-col span="14">
                        <i-button type="primary" :loading="loading"
                                  class="deal-submit">确认提交</i-button>
                    </i-col>
                </row>
            </card>
        </div>
    </div>
</template>