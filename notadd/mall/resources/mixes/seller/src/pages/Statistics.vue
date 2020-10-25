<script>
    export default {
        beforeRouteEnter(to, from, next) {
            next(() => {
            });
        },
        data() {
            return {
                goods: {
                    preForm: [
                        {
                            endPrice: '',
                            startPrice: '',
                        },
                        {
                            endPrice: '',
                            startPrice: '',
                        },
                    ],
                },
                goodsColumns: [
                    {
                        key: 'num',
                        title: '序号',
                    },
                    {
                        key: 'goodsName',
                        title: '商品名称',
                    },
                    {
                        key: 'amount',
                        title: '销量',
                    },
                ],
                goodsData: [
                    {
                        amount: 99,
                        goodsName: 'xxx旗舰店',
                        num: 1,
                    },
                    {
                        amount: 99,
                        goodsName: 'xxx旗舰店',
                        num: 2,
                    },
                    {
                        amount: 99,
                        goodsName: 'xxx旗舰店',
                        num: 3,
                    },
                    {
                        amount: 99,
                        goodsName: 'xxx旗舰店',
                        num: 4,
                    },
                ],
                informationList: [
                    {
                        intro: '店铺从昨天开始最近30天有效订单的总金额',
                        price: '0',
                        title: '近30天下单金额',
                    },
                    {
                        intro: '店铺从昨天开始最近30天有效订单的会员数',
                        price: '0',
                        title: '进30天下单会员数',
                    },
                    {
                        intro: '店铺从昨天开始最近30天有效订单的总订单数',
                        price: '0',
                        title: '进30天下单量',
                    },
                    {
                        intro: '店铺从昨天开始最近30天有效订单的总商品数量',
                        price: '0',
                        title: '进30天下单商品数',
                    },
                    {
                        intro: '店铺从昨天开始最近30天有效订单的平均交易金额',
                        price: '0',
                        title: '平均客单价',
                    },
                    {
                        intro: '店铺从昨天开始最近30天有效订单商品的平均成交价格',
                        price: '0',
                        title: '平均价格',
                    },
                    {
                        intro: '店铺所有商品的总收藏次数',
                        price: '0',
                        title: '商品收藏量',
                    },
                    {
                        intro: '店铺所有商品的总数(仅商品种类,不包括库存)',
                        price: '0',
                        title: '商品总数',
                    },
                    {
                        intro: '店铺总收藏次数',
                        price: '0',
                        title: '店铺收藏量',
                    },
                    {
                        intro: '',
                        price: '',
                        title: '',
                    },
                    {
                        intro: '',
                        price: '',
                        title: '',
                    },
                    {
                        intro: '',
                        price: '',
                        title: '',
                    },
                ],
                loading: false,
                orders: {
                    preForm: [
                        {
                            endPrice: '',
                            startPrice: '',
                        },
                        {
                            endPrice: '',
                            startPrice: '',
                        },
                    ],
                },
                salesTrend: {
                    legend: {
                        bottom: 'auto',
                        data: ['昨天', '今天'],
                    },
                    series: [
                        {
                            data: [120, 132, 220, 250, 90, 230, 210],
                            name: '今天',
                            stack: '下单金额',
                            type: 'line',
                        },
                        {
                            data: [220, 182, 191, 234, 290, 330, 310],
                            name: '昨天',
                            stack: '下单金额',
                            type: 'line',
                        },
                    ],
                    tooltip: {
                        trigger: 'axis',
                    },
                    xAxis: {
                        boundaryGap: false,
                        data: ['周一', '周二', '周三', '周四', '周五', '周六', '周日'],
                        type: 'category',
                    },
                    yAxis: {
                        type: 'value',
                    },
                },
                self: this,
                shopColumns: [
                    {
                        key: 'num',
                        title: '序号',
                    },
                    {
                        key: 'shopName',
                        title: '店铺名称',
                    },
                    {
                        key: 'orderAmount',
                        title: '销量',
                    },
                ],
                shopData: [
                    {
                        num: 1,
                        orderAmount: '￥99.00',
                        shopName: 'xxx旗舰店',
                    },
                    {
                        num: 2,
                        orderAmount: '￥99.00',
                        shopName: 'xxx旗舰店',
                    },
                    {
                        num: 3,
                        orderAmount: '￥99.00',
                        shopName: 'xxx旗舰店',
                    },
                    {
                        num: 4,
                        orderAmount: '￥99.00',
                        shopName: 'xxx旗舰店',
                    },
                ],
                style: 'height: 400px;',
            };
        },
        methods: {},
    };
</script>
<template>
    <div class="seller-wrap">
        <div class="statistics">
            <tabs value="name1">
                <tab-pane label="店铺概况" name="name1">
                    <card :bordered="false">
                        <div class="prompt-box">
                            <p>提示</p>
                            <p>1.符合以下任何一种条件的订单即为有效订单：1）采用在线支付方式支付并且已付款；
                                2）采用货到付款方式并且交易已完成</p>
                            <p>2.以下关于订单和订单商品进30天统计数据的依据为：从昨天开始最近30天的有效订单</p>
                        </div>
                        <div class="statistics-information">
                            <div class="information-content">
                                <row>
                                    <i-col span="6" v-for="item in informationList">
                                        <h5>{{ item.title }}</h5>
                                        <div>
                                            <span class="intro">{{ item.intro }}</span>
                                            <span class="price">{{ item.price }}</span>
                                        </div>
                                    </i-col>
                                </row>
                            </div>
                        </div>
                        <div class="statistics-information">
                            <div class="echarts">
                                <i-echarts :option="salesTrend"
                                           :style="style"
                                           @click="onClick"
                                           @ready="onReady" ></i-echarts>
                            </div>
                        </div>
                        <div class="table-information">
                            <row :gutter="16">
                                <i-col span="12">
                                    <h5>建议推广商品</h5>
                                    <i-table :columns="shopColumns" :context="self" :data="shopData"></i-table>
                                    <div class="page">
                                        <page :total="100" show-elevator></page>
                                    </div>
                                </i-col>
                                <i-col span="12">
                                    <h5>同行热卖</h5>
                                    <i-table :columns="goodsColumns" :context="self" :data="goodsData"></i-table>
                                    <div class="page">
                                        <page :total="100" show-elevator></page>
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