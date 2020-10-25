<script>
    import mapData from '../maps/china';

    export default {
        beforeRouteEnter(to, from, next) {
            next(() => {
            });
        },
        data() {
            return {
                analysisMoneyColumns: [
                    {
                        key: 'num',
                        title: '订单编号',
                    },
                    {
                        key: 'buyer',
                        title: '买家',
                    },
                    {
                        key: 'orderTime',
                        title: '下单时间',
                    },
                    {
                        key: 'orderMoney',
                        title: '订单总额',
                    },
                    {
                        key: 'status',
                        title: '订单状态',
                    },
                ],
                analysisMoneyData: [
                    {
                        buyer: 'djsed',
                        num: 22,
                        orderMoney: '99.00',
                        orderTime: '2016-12-21 16:11:25',
                        status: '交易成功',
                    },
                    {
                        buyer: 'djsed',
                        num: 22,
                        orderMoney: '99.00',
                        orderTime: '2016-12-21 16:11:25',
                        status: '交易成功',
                    },
                    {
                        buyer: 'djsed',
                        num: 22,
                        orderMoney: '99.00',
                        orderTime: '2016-12-21 16:11:25',
                        status: '交易成功',
                    },
                    {
                        buyer: 'djsed',
                        num: 22,
                        orderMoney: '99.00',
                        orderTime: '2016-12-21 16:11:25',
                        status: '交易成功',
                    },
                ],
                analysisNumberColumns: [
                    {
                        key: 'num',
                        title: '订单编号',
                    },
                    {
                        key: 'buyer',
                        title: '买家',
                    },
                    {
                        key: 'orderTime',
                        title: '下单时间',
                    },
                    {
                        key: 'orderMoney',
                        title: '订单总额',
                    },
                    {
                        key: 'status',
                        title: '订单状态',
                    },
                ],
                analysisNumberData: [
                    {
                        buyer: 'djsed',
                        num: 22,
                        orderMoney: '99.00',
                        orderTime: '2016-12-21 16:11:25',
                        status: '交易成功',
                    },
                    {
                        buyer: 'djsed',
                        num: 22,
                        orderMoney: '99.00',
                        orderTime: '2016-12-21 16:11:25',
                        status: '交易成功',
                    },
                    {
                        buyer: 'djsed',
                        num: 22,
                        orderMoney: '99.00',
                        orderTime: '2016-12-21 16:11:25',
                        status: '交易成功',
                    },
                    {
                        buyer: 'djsed',
                        num: 22,
                        orderMoney: '99.00',
                        orderTime: '2016-12-21 16:11:25',
                        status: '交易成功',
                    },
                ],
                isPriceArea: false,
                loading: false,
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
                orderCountProvince: {
                    tooltip: {},
                    visualMap: {
                        calculable: true,
                        inRange: {
                            color: ['#e0ffff', '#006edd'],
                        },
                        left: 'left',
                        max: 1500,
                        min: 0,
                        seriesIndex: [1],
                        text: ['High', 'Low'],
                        top: 'bottom',
                    },
                    geo: {
                        itemStyle: {
                            emphasis: {
                                areaColor: null,
                                borderWidth: 0,
                                shadowBlur: 20,
                                shadowColor: 'rgba(0, 0, 0, 0.5)',
                                shadowOffsetX: 0,
                                shadowOffsetY: 0,
                            },
                            normal: {
                                borderColor: 'rgba(0, 0, 0, 0.2)',
                            },
                        },
                        label: {
                            normal: {
                                show: true,
                                textStyle: {
                                    color: 'rgba(0,0,0,0.4)',
                                },
                            },
                        },
                        layoutCenter: ['50%', '50%'],
                        layoutSize: 520,
                        map: 'china',
                        roam: true,
                    },
                    series: [
                        {
                            coordinateSystem: 'geo',
                            data: this.convertData(),
                            itemStyle: {
                                normal: {
                                    color: '#F06C00',
                                },
                            },
                            label: {
                                emphasis: {
                                    show: true,
                                },
                                normal: {
                                    formatter: '{b}',
                                    position: 'right',
                                    show: false,
                                },
                            },
                            symbol: 'path://M1705.06,1318.313v-89.254l-319.9-221.799l0.073-208.063c0.521-84.662-26.' +
                            '629-121.796-63.961-121.491c-37.332-0.305-64.482,36.829-63.961,121.491l0.073,208.063l-319.9,' +
                            '221.799v89.254l330.343-157.288l12.238,241.308l-134.449,92.931l0.531,' +
                            '42.034l175.125-42.917l175.125,42.917l0.531-42.034l-134.449-92.931l12.238-241.308L1705.06,' +
                            '1318.313z',
                            symbolRotate: 35,
                            symbolSize: 80,
                            type: 'scatter',
                        },
                        {
                            data: [
                                {
                                    name: '北京',
                                    value: this.randomValue(),
                                },
                                {
                                    name: '天津',
                                    value: this.randomValue(),
                                },
                                {
                                    name: '上海',
                                    value: this.randomValue(),
                                },
                                {
                                    name: '重庆',
                                    value: this.randomValue(),
                                },
                                {
                                    name: '河北',
                                    value: this.randomValue(),
                                },
                                {
                                    name: '河南',
                                    value: this.randomValue(),
                                },
                                {
                                    name: '云南',
                                    value: this.randomValue(),
                                },
                                {
                                    name: '辽宁',
                                    value: this.randomValue(),
                                },
                                {
                                    name: '黑龙江',
                                    value: this.randomValue(),
                                },
                                {
                                    name: '湖南',
                                    value: this.randomValue(),
                                },
                                {
                                    name: '安徽',
                                    value: this.randomValue(),
                                },
                                {
                                    name: '山东',
                                    value: this.randomValue(),
                                },
                                {
                                    name: '新疆',
                                    value: this.randomValue(),
                                },
                                {
                                    name: '江苏',
                                    value: this.randomValue(),
                                },
                                {
                                    name: '浙江',
                                    value: this.randomValue(),
                                },
                                {
                                    name: '江西',
                                    value: this.randomValue(),
                                },
                                {
                                    name: '湖北',
                                    value: this.randomValue(),
                                },
                                {
                                    name: '广西',
                                    value: this.randomValue(),
                                },
                                {
                                    name: '甘肃',
                                    value: this.randomValue(),
                                },
                                {
                                    name: '山西',
                                    value: this.randomValue(),
                                },
                                {
                                    name: '内蒙古',
                                    value: this.randomValue(),
                                },
                                {
                                    name: '陕西',
                                    value: this.randomValue(),
                                },
                                {
                                    name: '吉林',
                                    value: this.randomValue(),
                                },
                                {
                                    name: '福建',
                                    value: this.randomValue(),
                                },
                                {
                                    name: '贵州',
                                    value: this.randomValue(),
                                },
                                {
                                    name: '广东',
                                    value: this.randomValue(),
                                },
                                {
                                    name: '青海',
                                    value: this.randomValue(),
                                },
                                {
                                    name: '西藏',
                                    value: this.randomValue(),
                                },
                                {
                                    name: '四川',
                                    value: this.randomValue(),
                                },
                                {
                                    name: '宁夏',
                                    value: this.randomValue(),
                                },
                                {
                                    name: '海南',
                                    value: this.randomValue(),
                                },
                                {
                                    name: '台湾',
                                    value: this.randomValue(),
                                },
                                {
                                    name: '香港',
                                    value: this.randomValue(),
                                },
                                {
                                    name: '澳门',
                                    value: this.randomValue(),
                                },
                            ],
                            geoIndex: 0,
                            name: '下单量',
                            type: 'map',
                        },
                    ],
                },
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
                orderMoneyProvince: {
                    tooltip: {},
                    visualMap: {
                        calculable: true,
                        inRange: {
                            color: ['#e0ffff', '#006edd'],
                        },
                        left: 'left',
                        max: 1500,
                        min: 0,
                        seriesIndex: [1],
                        text: ['High', 'Low'],
                        top: 'bottom',
                    },
                    geo: {
                        itemStyle: {
                            emphasis: {
                                areaColor: null,
                                borderWidth: 0,
                                shadowBlur: 20,
                                shadowColor: 'rgba(0, 0, 0, 0.5)',
                                shadowOffsetX: 0,
                                shadowOffsetY: 0,
                            },
                            normal: {
                                borderColor: 'rgba(0, 0, 0, 0.2)',
                            },
                        },
                        label: {
                            normal: {
                                show: true,
                                textStyle: {
                                    color: 'rgba(0,0,0,0.4)',
                                },
                            },
                        },
                        layoutCenter: ['50%', '50%'],
                        layoutSize: 520,
                        map: 'china',
                        roam: true,
                    },
                    series: [
                        {
                            coordinateSystem: 'geo',
                            data: this.convertData(),
                            itemStyle: {
                                normal: {
                                    color: '#F06C00',
                                },
                            },
                            label: {
                                emphasis: {
                                    show: true,
                                },
                                normal: {
                                    formatter: '{b}',
                                    position: 'right',
                                    show: false,
                                },
                            },
                            symbol: 'path://M1705.06,1318.313v-89.254l-319.9-221.799l0.073-208.063c0.521-84.662-26.' +
                            '629-121.796-63.961-121.491c-37.332-0.305-64.482,36.829-63.961,121.491l0.073,208.063l-319.9,' +
                            '221.799v89.254l330.343-157.288l12.238,241.308l-134.449,92.931l0.531,' +
                            '42.034l175.125-42.917l175.125,42.917l0.531-42.034l-134.449-92.931l12.238-241.308L1705.06,' +
                            '1318.313z',
                            symbolRotate: 35,
                            symbolSize: 80,
                            type: 'scatter',
                        },
                        {
                            data: [
                                {
                                    name: '北京',
                                    value: this.randomValue(),
                                },
                                {
                                    name: '天津',
                                    value: this.randomValue(),
                                },
                                {
                                    name: '上海',
                                    value: this.randomValue(),
                                },
                                {
                                    name: '重庆',
                                    value: this.randomValue(),
                                },
                                {
                                    name: '河北',
                                    value: this.randomValue(),
                                },
                                {
                                    name: '河南',
                                    value: this.randomValue(),
                                },
                                {
                                    name: '云南',
                                    value: this.randomValue(),
                                },
                                {
                                    name: '辽宁',
                                    value: this.randomValue(),
                                },
                                {
                                    name: '黑龙江',
                                    value: this.randomValue(),
                                },
                                {
                                    name: '湖南',
                                    value: this.randomValue(),
                                },
                                {
                                    name: '安徽',
                                    value: this.randomValue(),
                                },
                                {
                                    name: '山东',
                                    value: this.randomValue(),
                                },
                                {
                                    name: '新疆',
                                    value: this.randomValue(),
                                },
                                {
                                    name: '江苏',
                                    value: this.randomValue(),
                                },
                                {
                                    name: '浙江',
                                    value: this.randomValue(),
                                },
                                {
                                    name: '江西',
                                    value: this.randomValue(),
                                },
                                {
                                    name: '湖北',
                                    value: this.randomValue(),
                                },
                                {
                                    name: '广西',
                                    value: this.randomValue(),
                                },
                                {
                                    name: '甘肃',
                                    value: this.randomValue(),
                                },
                                {
                                    name: '山西',
                                    value: this.randomValue(),
                                },
                                {
                                    name: '内蒙古',
                                    value: this.randomValue(),
                                },
                                {
                                    name: '陕西',
                                    value: this.randomValue(),
                                },
                                {
                                    name: '吉林',
                                    value: this.randomValue(),
                                },
                                {
                                    name: '福建',
                                    value: this.randomValue(),
                                },
                                {
                                    name: '贵州',
                                    value: this.randomValue(),
                                },
                                {
                                    name: '广东',
                                    value: this.randomValue(),
                                },
                                {
                                    name: '青海',
                                    value: this.randomValue(),
                                },
                                {
                                    name: '西藏',
                                    value: this.randomValue(),
                                },
                                {
                                    name: '四川',
                                    value: this.randomValue(),
                                },
                                {
                                    name: '宁夏',
                                    value: this.randomValue(),
                                },
                                {
                                    name: '海南',
                                    value: this.randomValue(),
                                },
                                {
                                    name: '台湾',
                                    value: this.randomValue(),
                                },
                                {
                                    name: '香港',
                                    value: this.randomValue(),
                                },
                                {
                                    name: '澳门',
                                    value: this.randomValue(),
                                },
                            ],
                            geoIndex: 0,
                            name: '下单金额',
                            type: 'map',
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
                orderNumberProvince: {
                    tooltip: {},
                    visualMap: {
                        calculable: true,
                        inRange: {
                            color: ['#e0ffff', '#006edd'],
                        },
                        left: 'left',
                        max: 1500,
                        min: 0,
                        seriesIndex: [1],
                        text: ['High', 'Low'],
                        top: 'bottom',
                    },
                    geo: {
                        itemStyle: {
                            emphasis: {
                                areaColor: null,
                                borderWidth: 0,
                                shadowBlur: 20,
                                shadowColor: 'rgba(0, 0, 0, 0.5)',
                                shadowOffsetX: 0,
                                shadowOffsetY: 0,
                            },
                            normal: {
                                borderColor: 'rgba(0, 0, 0, 0.2)',
                            },
                        },
                        label: {
                            normal: {
                                show: true,
                                textStyle: {
                                    color: 'rgba(0,0,0,0.4)',
                                },
                            },
                        },
                        layoutCenter: ['50%', '50%'],
                        layoutSize: 520,
                        map: 'china',
                        roam: true,
                    },
                    series: [
                        {
                            coordinateSystem: 'geo',
                            data: this.convertData(),
                            itemStyle: {
                                normal: {
                                    color: '#F06C00',
                                },
                            },
                            label: {
                                emphasis: {
                                    show: true,
                                },
                                normal: {
                                    formatter: '{b}',
                                    position: 'right',
                                    show: false,
                                },
                            },
                            symbol: 'path://M1705.06,1318.313v-89.254l-319.9-221.799l0.073-208.063c0.521-84.662-26.' +
                            '629-121.796-63.961-121.491c-37.332-0.305-64.482,36.829-63.961,121.491l0.073,208.063l-319.9,' +
                            '221.799v89.254l330.343-157.288l12.238,241.308l-134.449,92.931l0.531,' +
                            '42.034l175.125-42.917l175.125,42.917l0.531-42.034l-134.449-92.931l12.238-241.308L1705.06,' +
                            '1318.313z',
                            symbolRotate: 35,
                            symbolSize: 80,
                            type: 'scatter',
                        },
                        {
                            data: [
                                {
                                    name: '北京',
                                    value: this.randomValue(),
                                },
                                {
                                    name: '天津',
                                    value: this.randomValue(),
                                },
                                {
                                    name: '上海',
                                    value: this.randomValue(),
                                },
                                {
                                    name: '重庆',
                                    value: this.randomValue(),
                                },
                                {
                                    name: '河北',
                                    value: this.randomValue(),
                                },
                                {
                                    name: '河南',
                                    value: this.randomValue(),
                                },
                                {
                                    name: '云南',
                                    value: this.randomValue(),
                                },
                                {
                                    name: '辽宁',
                                    value: this.randomValue(),
                                },
                                {
                                    name: '黑龙江',
                                    value: this.randomValue(),
                                },
                                {
                                    name: '湖南',
                                    value: this.randomValue(),
                                },
                                {
                                    name: '安徽',
                                    value: this.randomValue(),
                                },
                                {
                                    name: '山东',
                                    value: this.randomValue(),
                                },
                                {
                                    name: '新疆',
                                    value: this.randomValue(),
                                },
                                {
                                    name: '江苏',
                                    value: this.randomValue(),
                                },
                                {
                                    name: '浙江',
                                    value: this.randomValue(),
                                },
                                {
                                    name: '江西',
                                    value: this.randomValue(),
                                },
                                {
                                    name: '湖北',
                                    value: this.randomValue(),
                                },
                                {
                                    name: '广西',
                                    value: this.randomValue(),
                                },
                                {
                                    name: '甘肃',
                                    value: this.randomValue(),
                                },
                                {
                                    name: '山西',
                                    value: this.randomValue(),
                                },
                                {
                                    name: '内蒙古',
                                    value: this.randomValue(),
                                },
                                {
                                    name: '陕西',
                                    value: this.randomValue(),
                                },
                                {
                                    name: '吉林',
                                    value: this.randomValue(),
                                },
                                {
                                    name: '福建',
                                    value: this.randomValue(),
                                },
                                {
                                    name: '贵州',
                                    value: this.randomValue(),
                                },
                                {
                                    name: '广东',
                                    value: this.randomValue(),
                                },
                                {
                                    name: '青海',
                                    value: this.randomValue(),
                                },
                                {
                                    name: '西藏',
                                    value: this.randomValue(),
                                },
                                {
                                    name: '四川',
                                    value: this.randomValue(),
                                },
                                {
                                    name: '宁夏',
                                    value: this.randomValue(),
                                },
                                {
                                    name: '海南',
                                    value: this.randomValue(),
                                },
                                {
                                    name: '台湾',
                                    value: this.randomValue(),
                                },
                                {
                                    name: '香港',
                                    value: this.randomValue(),
                                },
                                {
                                    name: '澳门',
                                    value: this.randomValue(),
                                },
                            ],
                            geoIndex: 0,
                            name: '下单商品数',
                            type: 'map',
                        },
                    ],
                },
                provinceCountColumns: [
                    {
                        key: 'province',
                        title: '省份',
                    },
                    {
                        key: 'memberNum',
                        title: '下单会员数',
                    },
                    {
                        key: 'money',
                        title: '下单金额（元）',
                    },
                    {
                        key: 'amount',
                        title: '下单量',
                    },
                ],
                provinceCountData: [
                    {
                        amount: 20,
                        memberNum: 4,
                        money: 222,
                        province: '陕西',
                    },
                    {
                        amount: 20,
                        memberNum: 4,
                        money: 222,
                        province: '陕西',
                    },
                    {
                        amount: 20,
                        memberNum: 4,
                        money: 222,
                        province: '陕西',
                    },
                    {
                        amount: 20,
                        memberNum: 4,
                        money: 222,
                        province: '陕西',
                    },
                ],
                provinceMoneyColumns: [
                    {
                        key: 'province',
                        title: '省份',
                    },
                    {
                        key: 'memberNum',
                        title: '下单会员数',
                    },
                    {
                        key: 'money',
                        title: '下单金额（元）',
                    },
                    {
                        key: 'amount',
                        title: '下单量',
                    },
                ],
                provinceMoneyData: [
                    {
                        amount: 20,
                        memberNum: 4,
                        money: 222,
                        province: '陕西',
                    },
                    {
                        amount: 20,
                        memberNum: 4,
                        money: 222,
                        province: '陕西',
                    },
                    {
                        amount: 20,
                        memberNum: 4,
                        money: 222,
                        province: '陕西',
                    },
                    {
                        amount: 20,
                        memberNum: 4,
                        money: 222,
                        province: '陕西',
                    },
                ],
                provinceNumberColumns: [
                    {
                        key: 'province',
                        title: '省份',
                    },
                    {
                        key: 'memberNum',
                        title: '下单会员数',
                    },
                    {
                        key: 'money',
                        title: '下单金额（元）',
                    },
                    {
                        key: 'amount',
                        title: '下单商品数',
                    },
                ],
                provinceNumberData: [
                    {
                        amount: 20,
                        memberNum: 4,
                        money: 222,
                        province: '陕西',
                    },
                    {
                        amount: 20,
                        memberNum: 4,
                        money: 222,
                        province: '陕西',
                    },
                    {
                        amount: 20,
                        memberNum: 4,
                        money: 222,
                        province: '陕西',
                    },
                    {
                        amount: 20,
                        memberNum: 4,
                        money: 222,
                        province: '陕西',
                    },
                ],
                self: this,
                style: 'height: 400px;',
            };
        },
        methods: {
            convertData() {
                const data = [];
                const res = [];
                const geoCoordMap = {};
                for (let i = 0; i < data.length; i += 1) {
                    const geoCoord = geoCoordMap[data[i].name];
                    if (geoCoord) {
                        res.push({
                            name: data[i].name,
                            value: geoCoord.concat(data[i].value),
                        });
                    }
                }
                return res;
            },
            exportMemberMoneyData() {
                this.$refs.analysisMoneyList.exportCsv({
                    filename: '会员分析下单金额数据',
                });
            },
            exportMemberNumberData() {
                this.$refs.analysisNumberList.exportCsv({
                    filename: '会员分析下单商品数数据',
                });
            },
            exportProvinceCountData() {
                this.$refs.provinceCountList.exportCsv({
                    filename: '地区分析下单金额数据',
                });
            },
            exportProvinceMoneyData() {
                this.$refs.provinceMoneyList.exportCsv({
                    filename: '地区分析下单商品数数据',
                });
            },
            exportProvinceNumberData() {
                this.$refs.provinceNumberList.exportCsv({
                    filename: '地区分析下单量数据',
                });
            },
            onMapReady(a, echarts) {
                echarts.registerMap('china', mapData);
            },
            randomValue() {
                return Math.round(Math.random() * 1000);
            },
            settingPriceArea() {
                const self = this;
                self.$router.push(
                    {
                        path: 'operation/set',
                    },
                );
            },
        },
    };
</script>
<template>
    <div class="seller-wrap">
        <div class="statistics-operation">
            <tabs value="name1">
                <tab-pane label="销售统计" name="name1">
                    <card :bordered="false">
                        <div class="prompt-box">
                            <p>提示</p>
                            <p>1.符合以下任何一种条件的订单即为有效订单：1）采用在线支付方式支付并且已付款;
                                2）采用货到付款方式并且交易已完成</p>
                            <p>2.统计图展示了符合搜索条件的有效订单中的下单总金额和下单数量在时间段内的走势情况
                                及与前一个时间段的趋势对比</p>
                            <p>3.统计表显示了符合搜索条件的全部有效订单记录并可以点击“导出Excel的”将订单记录导出为Excel的文件</p>
                        </div>
                        <div class="analysis-content">
                            <tabs type="card">
                                <tab-pane label="下单金额">
                                    <div class="order-money-content">
                                        <!--<h5>买家排行榜TOP15</h5>-->
                                        <div class="select-content">
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
                                        <i-echarts :option="orderMoney"
                                                   :style="style"
                                                   @click="onClick"
                                                   @ready="onReady" ></i-echarts>
                                    </div>

                                    <i-button type="ghost" class="export-btn"
                                              @click="exportMemberMoneyData">导出数据</i-button>
                                    <i-table :columns="analysisMoneyColumns" :context="self"
                                             :data="analysisMoneyData" ref="analysisMoneyList"></i-table>
                                    <div class="page">
                                        <page :total="100" show-elevator></page>
                                    </div>
                                </tab-pane>
                                <tab-pane label="下单商品数">
                                    <div class="order-money-content">
                                        <!--<h5>买家排行榜TOP15</h5>-->
                                        <div class="select-content">
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
                                        <i-echarts :option="orderMoney" :style="style" @ready="onReady" @click="onClick"></i-echarts>
                                    </div>

                                    <i-button type="ghost" class="export-btn"
                                              @click="exportMemberNumberData">导出数据</i-button>
                                    <i-table :columns="analysisNumberColumns" :context="self"
                                             :data="analysisNumberData" ref="analysisNumberList"></i-table>
                                    <div class="page">
                                        <page :total="100" show-elevator></page>
                                    </div>
                                </tab-pane>
                            </tabs>
                        </div>
                    </card>
                </tab-pane>
                <tab-pane label="区域分布" name="name2">
                    <card :bordered="false">
                        <div class="prompt-box">
                            <p>提示</p>
                            <p>1.符合以下任何一种条件的订单即为有效订单：1）采用在线支付方式支付并且已付款;
                                2）采用货到付款方式并且交易已完成</p>
                            <p>2.统计图展示了符合搜索条件的有效订单的下单会员数，下单总金额和下单数量在各省级地区的分布情况</p>
                            <p>3.统计地图将根据各个区域的有效订单统计数据等级依次显示不同的颜色</p>
                            <p>4.地区排行将根据各个区域的有效订单统计数据进行排名显示</p>
                        </div>
                        <div class="analysis-content">
                            <tabs type="card">
                                <tab-pane label="下单会员数">
                                    <div class="order-money-content">
                                        <div class="select-content">
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
                                        <i-echarts :option="orderNumberProvince"
                                                   :style="style"
                                                   ref="echarts"
                                                   @click="onClick"
                                                   @ready="onMapReady"></i-echarts>
                                    </div>
                                    <i-button type="ghost" class="export-btn"
                                              @click="exportProvinceNumberData">导出数据</i-button>
                                    <i-table :columns="provinceNumberColumns" :context="self"
                                             :data="provinceNumberData" ref="provinceNumberList"></i-table>
                                    <div class="page">
                                        <page :total="100" show-elevator></page>
                                    </div>
                                </tab-pane>
                                <tab-pane label="下单金额">
                                    <div class="order-money-content">
                                        <div class="select-content">
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
                                        <i-echarts :option="orderMoneyProvince"
                                                   :style="style"
                                                   ref="echarts"
                                                   @click="onClick"
                                                   @ready="onMapReady"></i-echarts>
                                    </div>
                                    <i-button type="ghost" class="export-btn"
                                              @click="exportProvinceMoneyData">导出数据</i-button>
                                    <i-table :columns="provinceMoneyColumns" :context="self"
                                             :data="provinceMoneyData" ref="provinceMoneyList"></i-table>
                                    <div class="page">
                                        <page :total="100" show-elevator></page>
                                    </div>
                                </tab-pane>
                                <tab-pane label="下单量">
                                    <div class="order-money-content">
                                        <div class="select-content">
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
                                        <i-echarts :option="orderCountProvince"
                                                   :style="style"
                                                   ref="echarts"
                                                   @click="onClick"
                                                   @ready="onMapReady"></i-echarts>
                                    </div>
                                    <i-button type="ghost" class="export-btn"
                                              @click="exportProvinceCountData">导出数据</i-button>
                                    <i-table :columns="provinceCountColumns" :context="self"
                                             :data="provinceCountData" ref="provinceCountList"></i-table>
                                    <div class="page">
                                        <page :total="100" show-elevator></page>
                                    </div>
                                </tab-pane>
                            </tabs>
                        </div>
                    </card>
                </tab-pane>
                <tab-pane label="购买分析" name="name3">
                    <card :bordered="false">
                        <div class="prompt-box">
                            <p>提示</p>
                            <p>1.符合以下任何一种条件的订单即为有效订单：1）采用在线支付方式支付并且已付款;
                                2）采用货到付款方式并且交易已完成</p>
                            <p>2.统计图展示了符合搜索条件的有效订单的下单会员数，下单总金额和下单数量在各省级地区的分布情况</p>
                            <p>3.统计地图将根据各个区域的有效订单统计数据等级依次显示不同的颜色</p>
                            <p>4.地区排行将根据各个区域的有效订单统计数据进行排名显示</p>
                        </div>
                        <div class="analysis-content">
                            <h5>买家排行榜TOP15</h5>
                            <div class="order-money-content">
                                <i-button type="ghost" @click.native="settingPriceArea">设置价格区间</i-button>
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
                            <h5>购买时间段分布</h5>
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