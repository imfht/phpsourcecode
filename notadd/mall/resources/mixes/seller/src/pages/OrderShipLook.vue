<script>
    import image from '../assets/images/img_logo.png';

    export default {
        beforeRouteEnter(to, from, next) {
            next(() => {
            });
        },
        data() {
            return {
                order: {
                    address: '陕西省西安市雁塔区科技四路中段陕西国土资源大厦公寓楼2304',
                    amount: 2,
                    company: '顺丰速运',
                    freight: '10.00',
                    goodsName: 'MIUI /小米小米手机4小米4代MI4智能4G手机包邮黑色D-LTE（4G）/ TD-SCD',
                    message: '请尽快发货',
                    number: 22222222222,
                    orderNum: 3333333333333,
                    phone: 122222222,
                    picture: image,
                    price: '20.00',
                    priceAll: '167.00',
                    process: [
                        {
                            status: '2016-12-21 11:30:31 发往西安中转',
                        },
                        {
                            status: '2016-12-21 11:30:31 发往西安中转',
                        },
                        {
                            status: '2016-12-21 11:30:31 西安市高新二路揽件',
                        },
                    ],
                    user: '旺旺',
                },
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
        <div class="order-ship-look">
            <div class="edit-link-title">
                <i-button type="text" @click.native="goBack">
                    <icon type="chevron-left"></icon>
                </i-button>
                <span>发货-物流详情</span>
            </div>
            <card :bordered="false">
                <div class="logistics-detail">
                    <h5>物流详情</h5>
                    <i-form ref="order" :model="order" :rules="ruleValidate" :label-width="160">
                        <row>
                            <i-col span="15">
                                <row>
                                    <i-col>
                                        <form-item label="收货信息">
                                            {{ order.user }} {{ order.phone }} {{ order.address }}
                                        </form-item>
                                    </i-col>
                                </row>
                                <row>
                                    <i-col>
                                        <form-item label="买家留言">
                                            {{ order.message }}
                                        </form-item>
                                    </i-col>
                                </row>
                                <row>
                                    <i-col>
                                        <form-item label="发货信息">
                                            {{ order.user }} {{ order.phone }} {{ order.address }}
                                        </form-item>
                                    </i-col>
                                </row>
                                <h5>物流动态</h5>
                                <row>
                                    <i-col>
                                        <form-item label="物流公司">
                                            {{ order.company }}
                                        </form-item>
                                    </i-col>
                                </row>
                                <row>
                                    <i-col>
                                        <form-item label="物流单号">
                                            {{ order.number }}
                                        </form-item>
                                    </i-col>
                                </row>
                                <row>
                                    <i-col>
                                        <form-item label="物流动态">
                                            <p v-for="item in order.process">{{ item.status }}</p>
                                        </form-item>
                                    </i-col>
                                </row>
                            </i-col>
                            <i-col span="9">
                                <div class="goods-intro-content">
                                    <row>
                                        <i-col span="5">
                                            <img :src="order.picture" alt="">
                                        </i-col>
                                        <i-col span="19">
                                            <p>{{ order.goodsName }}</p>
                                            <p><i>&yen;{{ order.price }}</i>*{{ order.amount }}(数量)</p>
                                        </i-col>
                                    </row>
                                </div>
                                <row>
                                    <i-col>
                                        <form-item label="运费">
                                            {{ order.freight }}
                                        </form-item>
                                    </i-col>
                                </row>
                                <row>
                                    <i-col>
                                        <form-item label="订单总额">
                                            {{ order.priceAll }}
                                        </form-item>
                                    </i-col>
                                </row>
                                <row>
                                    <i-col>
                                        <form-item label="订单编号">
                                            {{ order.orderNum }}
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