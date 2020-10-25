<script>
    export default {
        beforeRouteEnter(to, from, next) {
            next(() => {
            });
        },
        data() {
            return {
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
                        title: '下单商品数',
                    },
                    {
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
                industryGoods: {
                    series: [
                        {
                            data: [120, 132, 220, 250, 90, 230, 210],
                            name: '下单商品数',
                            stack: '下单商品数',
                            type: 'line',
                        },
                    ],
                    tooltip: {
                        trigger: 'axis',
                    },
                    xAxis: {
                        boundaryGap: false,
                        data: ['1', '2', '3', '4', '5', '6', '7'],
                        type: 'category',
                    },
                    yAxis: {
                        type: 'value',
                    },
                },
                industryGoodsArea: {
                    series: [
                        {
                            data: [120, 132, 220, 250, 90, 230, 210],
                            name: '客单价',
                            stack: '客单价',
                            type: 'line',
                        },
                    ],
                    tooltip: {
                        trigger: 'axis',
                    },
                    xAxis: {
                        boundaryGap: false,
                        data: ['1-100', '100-200', '200-300', '300-400', '400-500', '500-600', '600-700'],
                        type: 'category',
                    },
                    yAxis: {
                        type: 'value',
                    },
                },
                isPriceArea: false,
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
                orderNumber: {
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
                self: this,
                style: 'height: 400px;',
            };
        },
        methods: {
            settingPrice() {
                const self = this;
                self.$router.push(
                    {
                        path: 'industry/set',
                    },
                );
            },
        },
    };
</script>
<template>
    <div class="seller-wrap">
        <div class="statistics-industry">
            <tabs value="name1">
                <tab-pane label="同行热卖" name="name1">
                    <card :bordered="false">
                        <div class="prompt-box">
                            <p>提示</p>
                            <p>1.查看各个行业的热销商品，了解行业热卖便于调整商品结构</p>
                        </div>
                        <div class="analysis-content">
                            <div class="order-money-content">
                                <div class="select-content select-content-left" style="top: -10px">
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
                                <i-echarts :option="industryGoods"
                                           :style="style"
                                           @click="onClick"
                                           @ready="onReady" ></i-echarts>
                            </div>
                            <i-table :columns="goodsColumns" :context="self" :data="goodsData"></i-table>
                            <div class="page">
                                <page :total="100" show-elevator></page>
                            </div>
                        </div>
                    </card>
                </tab-pane>
                <tab-pane label="行业价格分布" name="name2">
                    <card :bordered="false">
                        <div class="prompt-box">
                            <p>提示</p>
                            <p>1.点击“设置价格区间”进入设置价格区间页面，客单价分布图将根据您设置的价格区间进行分布统计</p>
                            <p>2.查看各个行业的客单价分布情况，了解各个行业订单成交情况，便于调整商品价格等信息</p>
                        </div>
                        <div class="analysis-content">
                            <div class="order-money-content">
                                <div class="select-content" style="top: -10px">
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
                                <i-echarts :option="industryGoodsArea"
                                           :style="style"
                                           @click="onClick"
                                           @ready="onReady" ></i-echarts>
                            </div>
                        </div>
                    </card>
                </tab-pane>
            </tabs>
        </div>
    </div>
</template>