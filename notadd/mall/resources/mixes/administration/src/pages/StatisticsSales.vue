<script>
    export default {
        beforeRouteEnter(to, from, next) {
            next(() => {
            });
        },
        data() {
            return {
                applicationWord: '',
                goodsList: [
                    {
                        label: '商品1',
                        value: '1',
                    },
                    {
                        label: '商品2',
                        value: '2',
                    },
                ],
                informationList: [
                    {
                        price: '4444',
                        title: '收款金额',
                    },
                    {
                        price: '4444',
                        title: '退款金额',
                    },
                    {
                        price: '4444',
                        title: '实收金额',
                    },
                    {
                        price: '4444',
                        title: '店铺费用',
                    },
                    {
                        price: '4444',
                        title: '总收入',
                    },
                ],
                loading: false,
                orderAmount: {
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
                orderColumns: [
                    {
                        key: 'orderNum',
                        title: '订单编号',
                    },
                    {
                        key: 'buyer',
                        title: '买家名称',
                    },
                    {
                        key: 'shopName',
                        title: '店铺名称',
                    },
                    {
                        key: 'orderTime',
                        title: '下单时间',
                    },
                    {
                        key: 'account',
                        title: '下单量',
                    },
                    {
                        key: 'status',
                        title: '订单状态',
                    },
                ],
                orderData: [
                    {
                        account: '99.00',
                        buyer: 111,
                        orderNum: 111111111,
                        orderTime: '2017-2-2',
                        shopName: '店铺111',
                        status: '已完成',
                    },
                    {
                        account: '99.00',
                        buyer: 111,
                        orderNum: 111111111,
                        orderTime: '2017-2-2',
                        shopName: '店铺111',
                        status: '已完成',
                    },
                    {
                        account: '99.00',
                        buyer: 111,
                        orderNum: 111111111,
                        orderTime: '2017-2-2',
                        shopName: '店铺111',
                        status: '已完成',
                    },
                    {
                        account: '99.00',
                        buyer: 111,
                        orderNum: 111111111,
                        orderTime: '2017-2-2',
                        shopName: '店铺111',
                        status: '已完成',
                    },
                ],
                orderMoney: {
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
                orderMoneyColumns: [
                    {
                        key: 'orderNum',
                        title: '订单编号',
                    },
                    {
                        key: 'buyer',
                        title: '买家名称',
                    },
                    {
                        key: 'shopName',
                        title: '店铺名称',
                    },
                    {
                        key: 'orderTime',
                        title: '下单时间',
                    },
                    {
                        key: 'account',
                        title: '订单总额（元）',
                    },
                    {
                        key: 'status',
                        title: '订单状态',
                    },
                ],
                orderMoneyData: [
                    {
                        account: '99.00',
                        buyer: 111,
                        orderNum: 111111111,
                        orderTime: '2017-2-2',
                        shopName: '店铺111',
                        status: '已完成',
                    },
                    {
                        account: '99.00',
                        buyer: 111,
                        orderNum: 111111111,
                        orderTime: '2017-2-2',
                        shopName: '店铺111',
                        status: '已完成',
                    },
                    {
                        account: '99.00',
                        buyer: 111,
                        orderNum: 111111111,
                        orderTime: '2017-2-2',
                        shopName: '店铺111',
                        status: '已完成',
                    },
                    {
                        account: '99.00',
                        buyer: 111,
                        orderNum: 111111111,
                        orderTime: '2017-2-2',
                        shopName: '店铺111',
                        status: '已完成',
                    },
                ],
                orderMoneyOptions: {
                    shortcuts: [
                        {
                            text: '最近一周',
                            value() {
                                const end = new Date();
                                const start = new Date();
                                start.setTime(start.getTime() - (3600 * 1000 * 24 * 7));
                                return [start, end];
                            },
                        },
                        {
                            text: '最近一个月',
                            value() {
                                const end = new Date();
                                const start = new Date();
                                start.setTime(start.getTime() - (3600 * 1000 * 24 * 30));
                                return [start, end];
                            },
                        },
                        {
                            text: '最近三个月',
                            value() {
                                const end = new Date();
                                const start = new Date();
                                start.setTime(start.getTime() - (3600 * 1000 * 24 * 90));
                                return [start, end];
                            },
                        },
                    ],
                },
                orderStatistics: [
                    {
                        price: '4444',
                        title: '总销售额',
                    },
                    {
                        price: '4444',
                        title: '总订单量',
                    },
                ],
                shopsColumns: [
                    {
                        key: 'shopName',
                        title: '店铺名称',
                    },
                    {
                        key: 'orderMoney',
                        title: '订单金额（元）',
                    },
                    {
                        key: 'refundMoney',
                        title: '退单金额（元）',
                    },
                    {
                        key: 'allMoney',
                        title: '结算金额（元）',
                    },
                    {
                        key: 'shopMoney',
                        title: '店铺费用（元）',
                    },
                ],
                shopsData: [
                    {
                        allMoney: '22.00',
                        orderMoney: '99.00',
                        refundMoney: '33.00',
                        shopName: '艾派数码',
                        shopMoney: '100.00',
                    },
                    {
                        allMoney: '22.00',
                        orderMoney: '99.00',
                        refundMoney: '33.00',
                        shopName: '艾派数码',
                        shopMoney: '100.00',
                    },
                    {
                        allMoney: '22.00',
                        orderMoney: '99.00',
                        refundMoney: '33.00',
                        shopName: '艾派数码',
                        shopMoney: '100.00',
                    },
                    {
                        allMoney: '22.00',
                        orderMoney: '99.00',
                        refundMoney: '33.00',
                        shopName: '艾派数码',
                        shopMoney: '100.00',
                    },
                ],
                shopsList: [
                    {
                        label: '商品1',
                        value: '1',
                    },
                    {
                        label: '商品2',
                        value: '2',
                    },
                ],
                style: 'height: 400px;',
                timeList: [
                    {
                        label: '按照月统计',
                        value: '1',
                    },
                    {
                        label: '按照周统计',
                        value: '2',
                    },
                ],
            };
        },
        methods: {
            exportOrderData() {
                this.$refs.orderList.exportCsv({
                    filename: '下单量统计数据',
                });
            },
            exportOrderMoneyData() {
                this.$refs.orderMoneyList.exportCsv({
                    filename: '下单金额统计数据',
                });
            },
            exportShopsData() {
                this.$refs.shopsList.exportCsv({
                    filename: '收入统计数据',
                });
            },
        },
    };
</script>
<template>
    <div class="mall-wrap">
        <div class="statistics-sales">
            <tabs value="name1">
                <tab-pane label="收入统计" name="name1">
                    <card :bordered="false">
                        <div class="analysis-content">
                            <div class="order-money-content">
                                <div class="information-content">
                                    <div v-for="item in informationList">
                                        <span class="title">{{ item.title }}</span>
                                        <span class="price"> <i>{{ item.price }}</i> 元</span>
                                    </div>
                                </div>
                                <div class="select-content">
                                    <i-button type="ghost" class="export-btn export-sales-btn"
                                              @click="exportShopsData">导出数据</i-button>
                                    <ul>
                                        <li>日期筛选</li>
                                        <li>
                                            <date-picker type="date" placeholder="起始时间"></date-picker>
                                        </li>
                                        <li>
                                            <date-picker type="date" placeholder="截止时间"></date-picker>
                                        </li>
                                        <li class="store-body-header-right">
                                            <i-input v-model="applicationWord" placeholder="请输入关键词进行搜索">
                                                <i-button slot="append" type="primary">搜索</i-button>
                                            </i-input>
                                        </li>
                                    </ul>
                                </div>
                                <i-table :columns="shopsColumns" :context="self"
                                         :data="shopsData" ref="shopsList"></i-table>
                                <div class="page">
                                    <page :total="100" show-elevator></page>
                                </div>
                            </div>
                        </div>
                    </card>
                </tab-pane>
                <tab-pane label="订单统计" name="name2">
                    <card :bordered="false">
                        <div class="prompt-box">
                            <p>提示</p>
                            <p>符合以下任何一种条件的订单即为有效订单：1、采用在线支付方式支付并且已付款；
                                2、采用货到付款方式支付并且交易已完成</p>
                            <p>统计图展示了符合搜索条件的有效订单中的下单总金额和下单数量在时间段内的走势情况及与
                                前一个时间段的趋势对比</p>
                            <p>统计表显示了符合搜索条件的全部有效订单记录并可以点击“导出数据”将订单记录导出为Excel文件</p>
                        </div>
                        <div class="information-content order-information-content">
                            <div v-for="item in orderStatistics">
                                <span class="title">{{ item.title }}</span>
                                <span class="price"> <i>{{ item.price }}</i> 元</span>
                            </div>
                        </div>
                        <div class="analysis-content">
                            <tabs type="card">
                                <tab-pane label="下单量">
                                    <div class="order-money-content search-select-item">
                                        <div class="select-order-content">
                                            <div class="select-content">
                                                <ul>
                                                    <li>
                                                        商品分类
                                                        <i-select v-model="model2" style="width:124px">
                                                            <i-option v-for="item in goodsList" :value="item.value"
                                                                      :key="item">{{ item.label }}</i-option>
                                                        </i-select>
                                                    </li>
                                                    <li>
                                                        时间周期
                                                        <date-picker :options="orderMoneyOptions"
                                                                     placement="bottom-end"
                                                                     placeholder="选择日期"
                                                                     style="width: 200px"
                                                                     type="daterange"></date-picker>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="echarts">
                                        <i-echarts :option="orderAmount"
                                                   :style="style"
                                                   @click="onClick"
                                                   @ready="onReady"></i-echarts>
                                    </div>
                                    <i-button type="ghost" class="export-btn "
                                              @click="exportOrderData">导出数据</i-button>
                                    <i-table :columns="orderColumns" :context="self"
                                             :data="orderData" ref="orderList"></i-table>
                                    <div class="page">
                                        <page :total="100" show-elevator></page>
                                    </div>
                                </tab-pane>
                                <tab-pane label="下单金额">
                                    <div class="order-money-content search-select-item">
                                        <div class="select-order-content">
                                            <div class="select-content">
                                                <ul>
                                                    <li>
                                                        商品分类
                                                        <i-select v-model="model2" style="width:124px">
                                                            <i-option v-for="item in goodsList" :value="item.value"
                                                                      :key="item">{{ item.label }}</i-option>
                                                        </i-select>
                                                    </li>
                                                    <li>
                                                        时间周期
                                                        <date-picker :options="orderMoneyOptions"
                                                                     placement="bottom-end"
                                                                     placeholder="选择日期"
                                                                     style="width: 200px"
                                                                     type="daterange"></date-picker>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="echarts">
                                        <i-echarts :option="orderMoney"
                                                   :style="style"
                                                   @click="onClick"
                                                   @ready="onReady"></i-echarts>
                                    </div>
                                    <i-button type="ghost" class="export-btn "
                                              @click="exportOrderMoneyData">导出数据</i-button>
                                    <i-table :columns="orderMoneyColumns" :context="self"
                                             :data="orderMoneyData" ref="orderMoneyList"></i-table>
                                    <div class="page">
                                        <page :total="100" show-elevator></page>
                                    </div>
                                </tab-pane>
                            </tabs>
                        </div>
                    </card>
                </tab-pane>
            </tabs>
        </div>
    </div>
</template>