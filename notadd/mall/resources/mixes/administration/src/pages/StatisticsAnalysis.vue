<script>
    export default {
        beforeRouteEnter(to, from, next) {
            next(() => {
            });
        },
        data() {
            return {
                filter: {
                    goodsType: [],
                    goodsTypeList: [
                        {
                            children: [
                                {
                                    label: '面部护理',
                                    value: '1',
                                },
                                {
                                    label: '口腔护理',
                                    value: '2',
                                },
                                {
                                    label: '身体护理',
                                    value: '3',
                                },
                            ],
                            label: '个护化妆',
                            value: '1',
                        },
                        {
                            children: [
                                {
                                    label: '生活电器',
                                    value: '1',
                                },
                                {
                                    label: '厨房电器',
                                    value: '2',
                                },
                                {
                                    label: '大家电',
                                    value: '3',
                                },
                            ],
                            label: '家用电器',
                            value: '2',
                        },
                    ],
                    industryGoodsType: [],
                    industryList: [
                        {
                            children: [
                                {
                                    label: '面部护理',
                                    value: '1',
                                },
                                {
                                    label: '口腔护理',
                                    value: '2',
                                },
                                {
                                    label: '身体护理',
                                    value: '3',
                                },
                            ],
                            label: '个护化妆',
                            value: '1',
                        },
                        {
                            children: [
                                {
                                    children: [
                                        {
                                            label: '冰箱',
                                            value: '1',
                                        },
                                        {
                                            label: '空调',
                                            value: '2',
                                        },
                                    ],
                                    label: '生活电器',
                                    value: '1',
                                },
                                {
                                    children: [
                                        {
                                            label: '热水器',
                                            value: '1',
                                        },
                                    ],
                                    label: '厨房电器',
                                    value: '2',
                                },
                                {
                                    label: '大家电',
                                    value: '3',
                                },
                            ],
                            label: '家用电器',
                            value: '2',
                        },
                    ],
                    priceGoodsType: [],
                    priceList: [
                        {
                            children: [
                                {
                                    label: '面部护理',
                                    value: '1',
                                },
                                {
                                    label: '口腔护理',
                                    value: '2',
                                },
                                {
                                    label: '身体护理',
                                    value: '3',
                                },
                            ],
                            label: '个护化妆',
                            value: '1',
                        },
                        {
                            children: [
                                {
                                    children: [
                                        {
                                            label: '冰箱',
                                            value: '1',
                                        },
                                        {
                                            label: '空调',
                                            value: '2',
                                        },
                                    ],
                                    label: '生活电器',
                                    value: '1',
                                },
                                {
                                    children: [
                                        {
                                            label: '热水器',
                                            value: '1',
                                        },
                                    ],
                                    label: '厨房电器',
                                    value: '2',
                                },
                                {
                                    label: '大家电',
                                    value: '3',
                                },
                            ],
                            label: '家用电器',
                            value: '2',
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
                industryGoods: {
                    color: ['#3398DB'],
                    series: [
                        {
                            barWidth: '60%',
                            data: [10, 52, 200, 334, 390, 330, 220],
                            name: '直接访问',
                            type: 'bar',
                        },
                    ],
                    tooltip: {
                        axisPointer: {
                            type: 'line',
                        },
                        trigger: 'axis',
                    },
                    xAxis: [
                        {
                            axisTick: {
                                alignWithLabel: true,
                            },
                            data: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                            type: 'category',
                        },
                    ],
                    yAxis: [
                        {
                            type: 'value',
                        },
                    ],
                },
                industryShop: {
                    color: ['#3398DB'],
                    series: [
                        {
                            barWidth: '60%',
                            data: [10, 52, 200, 334, 390, 330, 220],
                            name: '直接访问',
                            type: 'bar',
                        },
                    ],
                    tooltip: {
                        axisPointer: {
                            type: 'line',
                        },
                        trigger: 'axis',
                    },
                    xAxis: [
                        {
                            axisTick: {
                                alignWithLabel: true,
                            },
                            data: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                            type: 'category',
                        },
                    ],
                    yAxis: [
                        {
                            type: 'value',
                        },
                    ],
                },
                isPriceArea: false,
                orderAccount: {
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
                profileOrderGoods: {
                    color: ['#3398DB'],
                    series: [
                        {
                            barWidth: '60%',
                            data: [10, 52, 200, 334, 390, 330, 220],
                            name: '直接访问',
                            type: 'bar',
                        },
                    ],
                    tooltip: {
                        axisPointer: {
                            type: 'line',
                        },
                        trigger: 'axis',
                    },
                    xAxis: [
                        {
                            axisTick: {
                                alignWithLabel: true,
                            },
                            data: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                            type: 'category',
                        },
                    ],
                    yAxis: [
                        {
                            type: 'value',
                        },
                    ],
                },
                profileOrderMoney: {
                    color: ['#3398DB'],
                    series: [
                        {
                            barWidth: '60%',
                            data: [10, 52, 200, 334, 390, 330, 220],
                            name: '直接访问',
                            type: 'bar',
                        },
                    ],
                    tooltip: {
                        axisPointer: {
                            type: 'line',
                        },
                        trigger: 'axis',
                    },
                    xAxis: [
                        {
                            axisTick: {
                                alignWithLabel: true,
                            },
                            data: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                            type: 'category',
                        },
                    ],
                    yAxis: [
                        {
                            type: 'value',
                        },
                    ],
                },
                profileOrderNum: {
                    color: ['#3398DB'],
                    series: [
                        {
                            barWidth: '60%',
                            data: [10, 52, 200, 334, 390, 330, 220],
                            name: '直接访问',
                            type: 'bar',
                        },
                    ],
                    tooltip: {
                        axisPointer: {
                            type: 'line',
                        },
                        trigger: 'axis',
                    },
                    xAxis: [
                        {
                            axisTick: {
                                alignWithLabel: true,
                            },
                            data: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                            type: 'category',
                        },
                    ],
                    yAxis: [
                        {
                            type: 'value',
                        },
                    ],
                },
                shopsColumns: [
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
                        title: '操作',
                        width: 120,
                        render(h) {
                            return h('i-button', {
                                props: {
                                    size: 'small',
                                    type: 'ghost',
                                },
                            }, '查看');
                        },
                    },
                ],
                shopsData: [
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
                style: 'height: 400px;',
            };
        },
        methods: {},
    };
</script>
<template>
    <div class="mall-wrap">
        <div class="statistics-analysis">
            <tabs value="name1">
                <tab-pane label="概况总览" name="name1">
                    <card :bordered="false">
                        <div class="prompt-box">
                            <p>提示</p>
                            <p>可以选择不同的商品分类和时间查询数据</p>
                            <p>统计某行业子分类在不同时间段的下单金额、下单商品数、下单量，为分析行业销量提供依据</p>
                        </div>
                        <div class="analysis-content">
                            <tabs type="card">
                                <tab-pane label="下单金额">
                                    <div class="order-money-content">
                                        <div class="select-content">
                                            <ul>
                                                <li>
                                                    商品分类
                                                    <cascader :data="filter.goodsTypeList" style="width: 148px"
                                                              trigger="hover" v-model="filter.goodsType"></cascader>
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
                                        <i-echarts :option="profileOrderMoney"
                                                   :style="style"
                                                   @click="onClick"
                                                   @ready="onReady"></i-echarts>
                                    </div>
                                </tab-pane>
                                <tab-pane label="下单商品数">
                                    <div class="order-money-content">
                                        <div class="select-content">
                                            <ul>
                                                <li>
                                                    商品分类
                                                    <cascader :data="filter.goodsTypeList" style="width: 148px"
                                                              trigger="hover" v-model="filter.goodsType"></cascader>
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
                                        <i-echarts :option="profileOrderGoods"
                                                   :style="style"
                                                   @click="onClick"
                                                   @ready="onReady" ></i-echarts>
                                    </div>
                                </tab-pane>
                                <tab-pane label="下单量">
                                    <div class="order-money-content">
                                        <div class="select-content">
                                            <ul>
                                                <li>
                                                    商品分类
                                                    <cascader :data="filter.goodsTypeList" style="width: 148px"
                                                              trigger="hover" v-model="filter.goodsType"></cascader>
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
                                        <i-echarts :option="profileOrderNum"
                                                   :style="style"
                                                   @click="onClick"
                                                   @ready="onReady" ></i-echarts>
                                    </div>
                                </tab-pane>
                            </tabs>
                        </div>
                    </card>
                </tab-pane>
                <tab-pane label="行业排行" name="name2">
                    <card :bordered="false">
                        <div class="prompt-box">
                            <p>提示</p>
                            <p>可以选择不同的商品分类和时间查询数据</p>
                            <p>统计某行业在不同时间段下单量前50名商品和前30名店铺</p>
                        </div>
                        <div class="analysis-content">
                            <h5 v-if="!isPriceArea">行业商品TOP50</h5>
                            <div class="order-money-content">
                                <div class="select-content" style="top: -10px">
                                    <ul>
                                        <li>
                                            商品分类
                                            <cascader :data="filter.industryList" style="width: 190px"
                                                      trigger="hover" v-model="filter.industryGoodsType"></cascader>
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
                            <i-table :columns="goodsColumns" :data="goodsData"></i-table>
                            <div class="page">
                                <page :total="100" show-elevator></page>
                            </div>
                            <h5 v-if="!isPriceArea">行业店铺TOP50</h5>
                            <div class="order-money-content">
                                <div class="select-content" style="top: -10px">
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
                            <div class="echarts">
                                <i-echarts :option="industryShop"
                                           :style="style"
                                           @click="onClick"
                                           @ready="onReady"></i-echarts>
                            </div>
                            <i-table :columns="shopsColumns" :data="shopsData"></i-table>
                            <div class="page">
                                <page :total="100" show-elevator></page>
                            </div>
                        </div>
                    </card>
                </tab-pane>
                <tab-pane label="价格分析" name="name3">
                    <card :bordered="false">
                        <div class="prompt-box">
                            <p>提示</p>
                            <p>符合以下任何一种条件的订单即为有效订单：1、采用在线支付方式支付并且已付款；
                                2、采用货到付款方式支付并且交易已完成</p>
                            <p>点击“设置价格区间”进入设置价格区间页面，下方统计图将根据您设置的价格区间进行统计</p>
                            <p>统计图展示符合搜索条件的有效订单中商品的单价所在价格区间中的总销售额和总下单商品数量走势</p>
                        </div>
                        <div class="analysis-content">
                            <i-button type="ghost">设置价格区间</i-button>
                            <h5>行业价格分布图</h5>
                            <div class="order-money-content">
                                <h5 v-if="!isPriceArea">下单金额</h5>
                                <div class="select-content" style="top: -10px">
                                    <ul>
                                        <li>
                                            商品分类
                                            <cascader :data="filter.priceList" style="width: 190px"
                                                      trigger="hover" v-model="filter.priceGoodsType"></cascader>
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
                                <div v-if="isPriceArea" class="set-price-area">
                                    <p>看行业价格分布情况前，请先设置价格区间</p>
                                </div>
                            </div>
                            <div class="echarts">
                                <i-echarts :option="orderAccount"
                                           :style="style"
                                           @click="onClick"
                                           @ready="onReady"></i-echarts>
                            </div>
                            <div class="order-money-content">
                                <h5 v-if="!isPriceArea">下单商品数</h5>
                            </div>
                            <div class="echarts">
                                <i-echarts :option="orderNumber"
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