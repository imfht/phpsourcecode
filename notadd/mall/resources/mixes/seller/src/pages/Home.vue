<script>
    export default {
        beforeRouteEnter(to, from, next) {
            next(() => {
            });
        },
        data() {
            return {
                sellColumns: [
                    {
                        align: 'center',
                        title: '序号',
                        type: 'index',
                        width: 120,
                    },
                    {
                        key: 'goodsName',
                        title: '商品名称',
                    },
                    {
                        align: 'center',
                        key: 'sales',
                        title: '销量',
                        width: 120,
                    },
                ],
                sellData: [
                    {
                        goodsName: 'rey四合一套装 周末大促销',
                        sales: 3256,
                    },
                    {
                        goodsName: 'rey四合一套装 周末大促销',
                        sales: 3256,
                    },
                    {
                        goodsName: 'rey四合一套装 周末大促销',
                        sales: 3256,
                    },
                    {
                        goodsName: 'rey四合一套装 周末大促销',
                        sales: 3256,
                    },
                    {
                        goodsName: 'rey四合一套装 周末大促销',
                        sales: 3256,
                    },
                    {
                        goodsName: 'rey四合一套装 周末大促销',
                        sales: 3256,
                    },
                    {
                        goodsName: 'rey四合一套装 周末大促销',
                        sales: 3256,
                    },
                    {
                        goodsName: 'rey四合一套装 周末大促销',
                        sales: 3256,
                    },
                    {
                        goodsName: 'rey四合一套装 周末大促销',
                        sales: 3256,
                    },
                    {
                        goodsName: 'rey四合一套装 周末大促销',
                        sales: 3256,
                    },
                ],
                shop: {
                    level: '白金店铺',
                    manage: '管理员',
                    name: '西部母婴用品店',
                    valid: '2020年5月10日',
                },
                tipDetail: {
                    goodTip: {
                        imgAll: 500,
                        imgNum: 399,
                        num: 102,
                        replyNum: 27,
                        reviewNum: 0,
                        sellNum: 138,
                        violationNum: 7,
                    },
                    operation: {
                        spike: true,
                        sell: true,
                        fullDown: false,
                    },
                    order: {
                        totalAmount: 543578,
                        totalNum: 543,
                        totalPrice: '99.00',
                    },
                    transaction: {
                        ensure: 22,
                        pay: 138,
                        refund: 23,
                        returnGoods: 20,
                        ship: 120,
                    },
                },
                user: {
                    name: 'nwaijhf',
                },
            };
        },
        methods: {},
    };
</script>
<template>
    <div class="seller-wrap">
        <div class="home-wrap">
            <tabs value="home">
                <tab-pane label="首页" name="home">
                    <card :bordered="false">
                        <row class="top-shop-intro">
                            <i-col class="img" span="2">
                                <img src="../assets/images/img_logo.png" alt="">
                            </i-col>
                            <i-col span="22">
                                <h4>{{ shop.name }}
                                    <span>(用户名:{{ user.name }})</span>
                                </h4>
                                <ul>
                                    <li>管理权限:{{ shop.manage }}</li>
                                    <li>店铺等级:{{ shop.level }}</li>
                                    <li>有效期:{{ shop.valid }}</li>
                                </ul>
                            </i-col>
                        </row>
                        <div class="bottom-shop-tip">
                            <row>
                                <i-col span="12" class="border-line">
                                    <ul class="shop-goods-module">
                                        <li>
                                            <div>
                                                <h5>店铺及商品提示</h5>
                                                <span class="tip distance"> 店铺信息及待处理事项</span>
                                            </div>
                                            <div class="trading-tips">
                                                <span>商品发布情况：{{ tipDetail.goodTip.num }}/不限</span>
                                                <span>图片空间使用：
                                                    {{ tipDetail.goodTip.imgNum }}/{{ tipDetail.goodTip.imgAll }}</span>
                                            </div>
                                            <ul class="border-module clearfix">
                                                <li>
                                                    <router-link to="/">
                                                        出售中 <span>{{ tipDetail.goodTip.sellNum }}</span>
                                                    </router-link>
                                                </li>
                                                <li>
                                                    <router-link to="/">
                                                        违规下架 <span>{{ tipDetail.goodTip.violationNum }}</span>
                                                    </router-link>
                                                </li>
                                                <li>
                                                    <router-link to="/">
                                                        仓库中已审核 <span>{{ tipDetail.goodTip.reviewNum }}</span>
                                                    </router-link>
                                                </li>
                                                <li>
                                                    <router-link to="/">
                                                        待回复咨询 <span>{{ tipDetail.goodTip.replyNum }}</span>
                                                    </router-link>
                                                </li>
                                            </ul>
                                        </li>
                                        <li>
                                            <div>
                                                <h5>交易提示</h5>
                                                <span class="tip distance"> 需要处理的交易订单</span>
                                            </div>
                                            <ul class="border-module clearfix">
                                                <li>
                                                    <router-link to="/">
                                                        待付款 <span>{{ tipDetail.transaction.pay }}</span>
                                                    </router-link>
                                                </li>
                                                <li>
                                                    <router-link to="/">
                                                        待发货 <span>{{ tipDetail.transaction.ship }}</span>
                                                    </router-link>
                                                </li>
                                                <li>
                                                    <router-link to="/">
                                                        待确认账单 <span>{{ tipDetail.transaction.ensure }}</span>
                                                    </router-link>
                                                </li>
                                                <li>
                                                    <router-link to="/">
                                                        退款 <span>{{ tipDetail.transaction.refund }}</span>
                                                    </router-link>
                                                </li>
                                                <li>
                                                    <router-link to="/">
                                                        退货 <span>{{ tipDetail.transaction.returnGoods }}</span>
                                                    </router-link>
                                                </li>
                                            </ul>
                                        </li>
                                        <li>
                                            <div>
                                                <h5>店铺运营推广</h5>
                                                <span class="tip distance"> 合理参加促销活动可以有效提升商品销量</span>
                                            </div>
                                            <ul class="clearfix operation-module">
                                                <li>
                                                    <div class="clearfix">
                                                        <div class="img">
                                                            <img src="../assets/images/img_spike.png"
                                                            v-if="tipDetail.operation.spike">
                                                            <img src="../assets/images/img_spike01.png"
                                                                 v-if="!tipDetail.operation.spike">
                                                        </div>
                                                        <div class="img-text">
                                                            <p>限时秒杀</p>
                                                            <p v-if="tipDetail.operation.spike">
                                                                <icon type="checkmark-circled"></icon>已开通</p>
                                                            <p v-if="!tipDetail.operation.spike">
                                                                <icon type="close-circled"></icon>未开通</p>
                                                        </div>
                                                    </div>
                                                </li>
                                                <li>
                                                    <div class="clearfix">
                                                        <div class="img">
                                                            <img src="../assets/images/img_sell.png"
                                                                 v-if="tipDetail.operation.sell">
                                                            <img src="../assets/images/img_sell01.png"
                                                                 v-if="!tipDetail.operation.sell">
                                                        </div>
                                                        <div class="img-text">
                                                            <p>商品预售</p>
                                                            <p v-if="tipDetail.operation.sell">
                                                                <icon type="checkmark-circled"></icon>已开通</p>
                                                            <p v-if="!tipDetail.operation.sell">
                                                                <icon type="close-circled"></icon>未开通</p>
                                                        </div>
                                                    </div>
                                                </li>
                                                <li>
                                                    <div class="clearfix">
                                                        <div class="img">
                                                            <img src="../assets/images/img_fulldown.png"
                                                                 v-if="tipDetail.operation.fullDown">
                                                            <img src="../assets/images/img_fulldown01.png"
                                                                 v-if="!tipDetail.operation.fullDown">
                                                        </div>
                                                        <div class="img-text">
                                                            <p>商品满减</p>
                                                            <p v-if="tipDetail.operation.fullDown">
                                                                <icon type="checkmark-circled"></icon>已开通</p>
                                                            <p v-if="!tipDetail.operation.fullDown">
                                                                <icon type="close-circled"></icon>未开通</p>
                                                        </div>
                                                    </div>
                                                </li>
                                            </ul>
                                        </li>
                                    </ul>
                                </i-col>
                                <i-col span="12" class="sell-statistics border-line">
                                    <div class="statistics-information">
                                        <div>
                                            <h5>销售情况统计</h5>
                                            <span class="tip distance"> 昨日店铺销售概况</span>
                                        </div>
                                        <ul class="clearfix">
                                            <li>
                                                <p>下单金额</p>
                                                <div class="clearfix">
                                                    <span class="tip">有效订单的总金额(元)</span>
                                                    <span>{{ tipDetail.order.totalAmount }}</span>
                                                </div>
                                            </li>
                                            <li>
                                                <p>下单量</p>
                                                <div class="clearfix">
                                                    <span class="tip">有效订单的总数量</span>
                                                    <span>{{ tipDetail.order.totalNum }}</span>
                                                </div>
                                            </li>
                                            <li>
                                                <p>平均客单价</p>
                                                <div class="clearfix">
                                                    <span class="tip">有效订单的平均每单的金额(元)</span>
                                                    <span>{{ tipDetail.order.totalPrice }}</span>
                                                </div>
                                            </li>
                                        </ul>
                                        <div class="sort-table-module">
                                            <h5>单品销售排名TOP10</h5>
                                            <i-table :context="self"
                                                     :columns="sellColumns"
                                                     :data="sellData"
                                                     ref="sells"></i-table>
                                        </div>
                                    </div>
                                </i-col>
                            </row>
                        </div>
                    </card>
                </tab-pane>
            </tabs>
        </div>
    </div>
</template>