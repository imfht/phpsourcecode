<script>
    export default {
        beforeRouteEnter(to, from, next) {
            next(() => {
            });
        },
        data() {
            return {
                goodsList: [
                    {
                        children: [
                            {
                                children: [
                                    {
                                        label: '婴儿推车',
                                        value: '婴儿推车',
                                    },
                                    {
                                        label: '自行车',
                                        value: '自行车',
                                    },
                                    {
                                        label: '婴儿推车',
                                        value: '婴儿推车',
                                    },
                                    {
                                        label: '电动车',
                                        value: '电动车',
                                    },
                                    {
                                        label: '安全座椅',
                                        value: '安全座椅',
                                    },
                                ],
                                label: '童车童床',
                                value: '童车童床',
                            },
                            {
                                label: '营养辅食',
                                value: '营养辅食',
                            },
                            {
                                label: '尿裤湿巾',
                                value: '尿裤湿巾',
                            },
                        ],
                        label: '个护化妆',
                        value: '个护化妆',
                    },
                    {
                        children: [
                            {
                                children: [
                                    {
                                        label: '婴儿推车1',
                                        value: '婴儿推车1',
                                    },
                                    {
                                        label: '自行车2',
                                        value: '自行车2',
                                    },
                                    {
                                        label: '婴儿推车3',
                                        value: '婴儿推车3',
                                    },
                                    {
                                        label: '电动车',
                                        value: '电动车',
                                    },
                                    {
                                        label: '安全座椅4',
                                        value: '安全座椅4',
                                    },
                                ],
                                label: '服饰寝居',
                                value: '服饰寝居',
                            },
                            {
                                children: [
                                    {
                                        label: '婴儿推车1',
                                        value: '婴儿推车1',
                                    },
                                    {
                                        label: '自行车2',
                                        value: '自行车2',
                                    },
                                ],
                                label: '营养辅食',
                                value: '营养辅食',
                            },
                            {
                                children: [
                                    {
                                        label: '车1',
                                        value: '车1',
                                    },
                                    {
                                        label: '自行车2',
                                        value: '自行车2',
                                    },
                                ],
                                label: '尿裤湿巾',
                                value: '尿裤湿巾',
                            },
                        ],
                        label: '家用电器',
                        value: '家用电器',
                    },
                ],
                hotGoods: {
                    series: [
                        {
                            data: [120, 132, 220, 250, 90, 230, 210],
                            name: '下单金额',
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
                hotGoodsMoney: {
                    series: [
                        {
                            data: [120, 132, 220, 250, 90, 230, 210],
                            name: '下单金额',
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
                loading: false,
                orderColumns: [
                    {
                        key: 'num',
                        title: '序号',
                    },
                    {
                        key: 'goodName',
                        title: '商品名称',
                    },
                    {
                        key: 'amount',
                        title: '下单量',
                    },
                ],
                orderData: [
                    {
                        amount: 22,
                        goodName: 4,
                        num: 333,
                    },
                    {
                        amount: 22,
                        goodName: 4,
                        num: 333,
                    },
                    {
                        amount: 22,
                        goodName: 4,
                        num: 333,
                    },
                    {
                        amount: 22,
                        goodName: 4,
                        num: 333,
                    },
                ],
                orderMoneyColumns: [
                    {
                        key: 'num',
                        title: '序号',
                    },
                    {
                        key: 'goodName',
                        title: '商品名称',
                    },
                    {
                        key: 'amount',
                        title: '下单金额(元)',
                    },
                ],
                orderMoneyData: [
                    {
                        amount: 22,
                        goodName: 4,
                        num: 333,
                    },
                    {
                        amount: 22,
                        goodName: 4,
                        num: 333,
                    },
                    {
                        amount: 22,
                        goodName: 4,
                        num: 333,
                    },
                    {
                        amount: 22,
                        goodName: 4,
                        num: 333,
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
                priceAmount: {
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
                salesColumns: [
                    {
                        key: 'goodName',
                        title: '商品名称',
                    },
                    {
                        key: 'price',
                        title: '价格',
                    },
                    {
                        key: 'amountNum',
                        title: '近30天下单商品数',
                    },
                    {
                        key: 'money',
                        title: '近30天下单金额',
                    },
                    {
                        align: 'center',
                        key: 'action',
                        render(h) {
                            return h('i-button', {
                                props: {
                                    size: 'small',
                                    type: 'ghost',
                                },
                            }, '查看');
                        },
                        title: '操作',
                        width: 120,
                    },
                ],
                salesData: [
                    {
                        amountNum: 222,
                        goodName: '洗护套装买二送一',
                        price: '10.00',
                        money: 444,
                    },
                    {
                        amountNum: 222,
                        goodName: '洗护套装买二送一',
                        price: '10.00',
                        money: 444,
                    },
                    {
                        amountNum: 222,
                        goodName: '洗护套装买二送一',
                        price: '10.00',
                        money: 444,
                    },
                    {
                        amountNum: 222,
                        goodName: '洗护套装买二送一',
                        price: '10.00',
                        money: 444,
                    },
                ],
                self: this,
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
                style: 'height: 400px',
            };
        },
        methods: {
            exportSalesData() {
                this.$refs.salesList.exportCsv({
                    filename: '商品销售明细数据',
                });
            },
            settingPrice() {
                const self = this;
                self.$router.push(
                    {
                        path: 'goods/set',
                    },
                );
            },
        },
    };
</script>
<template>
    <div class="seller-wrap">
        <div class="statistics-goods">
            <tabs value="name1">
                <tab-pane label="商品详情" name="name1">
                    <card :bordered="false">
                        <div class="prompt-box">
                            <p>提示</p>
                            <p>1.符合以下任何一种条件的订单即为有效订单：1）采用在线支付方式支付并且已付款；
                                2）采用货到付款方式并且交易已完成</p>
                            <p>2.以下列表为从昨天开始最近30天有效订单中的所有商品数据</p>
                            <p>3.近30天下单商品数：从昨天开始最近30天有效订单的某商品总销量</p>
                            <p>4.近30天下单金额：从昨天开始最近30天有效订单的某商品总销售额</p>
                            <p>5.点击每列旁边的箭头对列表进行排序，默认按照“近30天成交件数”降序排列</p>
                            <p>6.点击每条记录后的“走势图”，查看最近30天下单金额、下单商品数、下单量走势</p>
                        </div>
                        <div class="analysis-content">
                            <div class="order-money-content">
                                <div class="select-content">
                                    <i-button type="ghost" class="export-btn export-sales-btn"
                                              @click="exportSalesData">导出数据</i-button>
                                    <ul>
                                        <li>
                                            商品分类
                                            <cascader :data="goodsList" trigger="hover"></Cascader>
                                        </li>
                                        <li class="store-body-header-right">
                                            <i-input v-model="applicationWord" placeholder="请输入关键词进行搜索">
                                                <i-button slot="append" type="primary">搜索</i-button>
                                            </i-input>
                                        </li>
                                    </ul>
                                </div>
                                <i-table :columns="salesColumns" :context="self"
                                         :data="salesData" ref="salesList"></i-table>
                                <div class="page">
                                    <page :total="100" show-elevator></page>
                                </div>
                            </div>
                        </div>
                    </card>
                </tab-pane>
                <tab-pane label="价格销量" name="name2">
                    <card :bordered="false">
                        <div class="prompt-box">
                            <p>提示</p>
                            <p>符合以下任何一种条件的订单即为有效订单：1、采用在线支付方式支付并且已付款；
                                2、采用货到付款方式支付并且交易已完成</p>
                            <p>点击"设置价格区间"进入设置价格区间页面，下方统计图将根据您设置的价格区间进行统计</p>
                            <p>统计图展示了符合搜索条件的有效订单中的商品单价，在所设置的价格区间的分布情况</p>
                        </div>
                        <div class="analysis-content">
                            <div class="order-money-content search-select-item">
                                <div class="select-content">
                                    <i-button type="ghost" class="export-btn"
                                              @click.native="settingPrice">设置价格区间</i-button>
                                    <ul>
                                        <li>
                                            商品分类
                                            <cascader :data="goodsList" trigger="hover"></Cascader>
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
                            <div class="echarts">
                                <i-echarts :option="priceAmount"
                                           :style="style"
                                           @click="onClick"
                                           @ready="onReady" ></i-echarts>
                            </div>
                        </div>
                    </card>
                </tab-pane>
                <tab-pane label="热卖商品" name="name3">
                    <card :bordered="false">
                        <div class="prompt-box">
                            <p>提示</p>
                            <p>1.符合以下任何一种条件的订单即为有效订单：1）采用在线支付方式支付并且已付款；
                                2）采用货到付款方式并且交易已完成</p>
                            <p>2.图表展示了符合搜索条件的有效订单中的下单总金额和下单商品总数排名前30位的商品</p>
                        </div>
                        <div class="analysis-content">
                            <tabs type="card">
                                <tab-pane label="下单金额">
                                    <div class="order-money-content">
                                        <div class="select-content hot-sales-goods">
                                            <ul>
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
                                    <div class="echarts">
                                        <i-echarts :option="hotGoodsMoney"
                                                   :style="style"
                                                   @click="onClick"
                                                   @ready="onReady" ></i-echarts>
                                    </div>
                                    <div class="order-module-content">
                                        <i-table :columns="orderMoneyColumns" :context="self"
                                                 :data="orderMoneyData" ref="orderMoneyList"></i-table>
                                        <div class="page">
                                            <page :total="100" show-elevator></page>
                                        </div>
                                    </div>
                                </tab-pane>
                                <tab-pane label="下单商品数">
                                    <div class="order-money-content">
                                        <div class="select-content hot-sales-goods">
                                            <ul>
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
                                    <div class="echarts">
                                        <i-echarts :option="hotGoods"
                                                   :style="style"
                                                   @click="onClick"
                                                   @ready="onReady" ></i-echarts>
                                    </div>
                                    <div class="order-module-content">
                                        <i-table :columns="orderColumns" :context="self"
                                                 :data="orderData" ref="orderList"></i-table>
                                        <div class="page">
                                            <page :total="100" show-elevator></page>
                                        </div>
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