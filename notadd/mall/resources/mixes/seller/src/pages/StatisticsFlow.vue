<script>
    export default {
        beforeRouteEnter(to, from, next) {
            next(() => {
            });
        },
        data() {
            return {
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
                self: this,
                style: 'height: 400px;',
            };
        },
    };
</script>
<template>
    <div class="seller-wrap">
        <div class="statistics-flow">
            <tabs value="name1">
                <tab-pane label="店铺总流量" name="name1">
                    <card :bordered="false">
                        <div class="prompt-box">
                            <p>提示</p>
                            <p>统计图展示了店铺在搜索时间段内的访问量走势情况</p>
                        </div>
                        <div class="analysis-content">
                            <div class="order-money-content">
                                <div class="select-content" style="top: -10px">
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
                                <i-echarts :option="orderAccount"
                                           :style="style"
                                           @click="onClick"
                                           @ready="onReady" ></i-echarts>
                            </div>
                        </div>
                    </card>
                </tab-pane>
                <tab-pane label="商品流量排名" name="name2">
                    <card :bordered="false">
                        <div class="prompt-box">
                            <p>提示</p>
                            <p>统计图展示了在搜索时间段内访问次数多的店铺商品前30名 </p>
                        </div>
                        <div class="analysis-content">
                            <div class="order-money-content">
                                <div class="select-content" style="top: -10px">
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
                                <i-echarts :option="industryGoods"
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