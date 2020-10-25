<script>
    import img1 from '../assets/images/details/stool2.png';

    export default{
        data() {
            return {
                activeTab: 1,
                allOrder: [
                    {
                        time: '2017-02-09',
                        order_code: 11052616584585,
                        shop_name: 'xxx母婴用品店',
                        selected: false,
                        logistics: {
                            name: '顺丰速运',
                            transport_code: 11265445952,
                            logisticsList: [
                                {
                                    address: '北京海淀区xx大道',
                                    time: '2016-12-29 13:06:03',
                                },
                                {
                                    address: '北京海淀区xx大道',
                                    time: '2016-12-29 13:06:03',
                                },
                                {
                                    address: '北京海淀区xx大道',
                                    time: '2016-12-29 13:06:03',
                                },
                            ],
                        },
                        transport_price: 10.00,
                        status: [3, 4],
                        operating: [3, 4],
                        products: [
                            {
                                img: img1,
                                name: 'Ｐurrfect diary 咕噜日记1-7岁儿童 可爱短袜5双装',
                                product_size: 'M',
                                num: 1,
                                price: 39.9,
                                old_price: 123.9,
                            },
                            {
                                img: img1,
                                name: 'Ｐurrfect diary 咕噜日记1-7岁儿童 可爱短袜5双装',
                                num: 1,
                                old_price: 123.9,
                                price: 39.9,
                                product_size: 'M',
                                transport_price: 10.00,
                            },
                        ],
                    },
                    {
                        time: '2017-02-09',
                        order_code: 11052616584585,
                        shop_name: 'xxx母婴用品店',
                        selected: false,
                        operating: [1, 7],
                        transport_price: 10.00,
                        status: [2, 7],
                        logistics: {
                            name: '顺丰速运',
                            transport_code: 11265445952,
                            logisticsList: [
                                {
                                    address: '北京海淀区xx大道',
                                    time: '2016-12-29 13:06:03',
                                },
                                {
                                    address: '北京海淀区xx大道',
                                    time: '2016-12-29 13:06:03',
                                },
                                {
                                    address: '北京海淀区xx大道',
                                    time: '2016-12-29 13:06:03',
                                },
                            ],
                        },
                        products: [
                            {
                                img: img1,
                                name: 'Ｐurrfect diary 咕噜日记1-7岁儿童 可爱短袜5双装',
                                num: 1,
                                old_price: 123.9,
                                price: 39.9,
                                product_size: 'M',
                            },
                        ],
                    },
                    {
                        order_code: 11052616584585,
                        selected: false,
                        products: [
                            {
                                img: img1,
                                name: 'Ｐurrfect diary 咕噜日记1-7岁儿童 可爱短袜5双装',
                                num: 1,
                                old_price: 123.9,
                                price: 39.9,
                                size: 'M',
                            },
                        ],
                        operating: [1, 2, 6],
                        status: [5, 2],
                        transport_price: 10.00,
                        time: '2017-02-09',
                        shop_name: 'xxx母婴用品店',
                    },
                    {
                        time: '2017-02-09',
                        order_code: 11052616584585,
                        shop_name: 'xxx母婴用品店',
                        selected: false,
                        operating: [1, 9],
                        transport_price: 10.00,
                        status: [2, 7, 4],
                        logistics: {
                            name: '顺丰速运',
                            transport_code: 11265445952,
                            logisticsList: [
                                {
                                    address: '北京海淀区xx大道',
                                    time: '2016-12-29 13:06:03',
                                },
                                {
                                    address: '北京海淀区xx大道',
                                    time: '2016-12-29 13:06:03',
                                },
                                {
                                    address: '北京海淀区xx大道',
                                    time: '2016-12-29 13:06:03',
                                },
                            ],
                        },
                        products: [
                            {
                                img: img1,
                                name: 'Ｐurrfect diary 咕噜日记1-7岁儿童 可爱短袜5双装',
                                num: 1,
                                old_price: 123.9,
                                price: 39.9,
                                product_size: 'M',
                            },
                        ],
                    },
                    {
                        time: '2017-02-09',
                        order_code: 11052616584585,
                        shop_name: 'xxx母婴用品店',
                        selected: false,
                        operating: [1, 2],
                        transport_price: 10.00,
                        status: [3, 4],
                        logistics: {
                            name: '顺丰速运',
                            transport_code: 11265445952,
                            logisticsList: [
                                {
                                    address: '北京海淀区xx大道',
                                    time: '2016-12-29 13:06:03',
                                },
                                {
                                    address: '北京海淀区xx大道',
                                    time: '2016-12-29 13:06:03',
                                },
                                {
                                    address: '北京海淀区xx大道',
                                    time: '2016-12-29 13:06:03',
                                },
                            ],
                        },
                        products: [
                            {
                                img: img1,
                                name: 'Ｐurrfect diary 咕噜日记1-7岁儿童 可爱短袜5双装',
                                num: 1,
                                old_price: 123.9,
                                price: 39.9,
                                product_size: 'M',
                            },
                            {
                                img: img1,
                                name: 'Ｐurrfect diary 咕噜日记1-7岁儿童 可爱短袜5双装',
                                num: 1,
                                old_price: 123.9,
                                price: 39.9,
                                product_size: 'M',
                            },
                            {
                                img: img1,
                                name: 'Ｐurrfect diary 咕噜日记1-7岁儿童 可爱短袜5双装',
                                num: 1,
                                old_price: 123.9,
                                price: 39.9,
                                product_size: 'M',
                            },
                        ],
                    },
                ],
                tabs: [
                    {
                        name: '所有订单',
                        num: '',
                    },
                    {
                        name: '待付款',
                        num: '5',
                    },
                    {
                        name: '代发货',
                        num: '0',
                    },
                    {
                        name: '待收货',
                        num: '0',
                    },
                    {
                        name: '待评价',
                        num: '5',
                    },
                    {
                        name: '订单回收站',
                    },
                ],
                isAllChecked: false,
            };
        },
        methods: {
            changeAllChecked() {
                const self = this;
                if (self.isAllChecked) {
                    self.allOrder.forEach(data => {
                        data.selected = true;
                    });
                } else {
                    self.allOrder.forEach(data => {
                        data.selected = false;
                    });
                }
            },
            changeTitleChecked() {
                const self = this;
                self.isAllChecked = true;
                self.allOrder.forEach(item => {
                    if (item.selected === false) {
                        self.isAllChecked = false;
                    }
                });
            },
            deleteProduct(item) {
                const index = this.allOrder.indexOf(item);
                this.allOrder.splice(index, 1);
            },
            tabSwitch(index) {
                this.activeTab = index;
            },
        },
    };
</script>
<template>
    <div class="all-order">
        <div class="clearfix nav-box">
            <ul id="myTab" class="tab-nav clearfix">
                <li :class="{active:index===activeTab}" @click="tabSwitch(index)" v-for="(tab, index) in tabs">
                    <a>
                        {{ tab.name }}{{ tab.num }}
                        <span class="product-num"></span>
                    </a>
                </li>
            </ul>
        </div>
        <div id="myTabContent" class="tab-content">
            <div class="tab-pane fade in active" id="all">
                <div class="input-group">
                    <input type="text" class="form-control" placeholder="请输入关键词搜索" aria-describedby="basic-addon2">
                    <span class="input-group-addon" id="basic-addon2">搜索</span>
                </div>
                <table>
                    <colgroup>
                        <col width="120px">
                        <col width="236px">
                        <col width="90px">
                        <col width="50px">
                        <col width="90px">
                        <col width="140px">
                        <col width="140px">
                        <col width="140px">
                    </colgroup>
                    <thead>
                    <tr>
                        <th colspan="2">商品信息</th>
                        <th>单价</th>
                        <th>数量</th>
                        <th>商品操作</th>
                        <th>金额</th>
                        <th>订单状态</th>
                        <th>操作</th>
                    </tr>
                    </thead>
                    <tr class="space">
                        <td colspan="8">
                            <ul class="clearfix shop-info" >
                                <li>
                                    <label class="checkProduct">
                                        <input type="checkbox" name="product"
                                               v-model="isAllChecked"
                                               @change="changeAllChecked">
                                        <span></span>
                                    </label>
                                </li>
                                <li>全选</li>
                                <li class="alot">批量永久删除</li>
                                <li class="alot">批量还原</li>
                                <li>
                                </li>
                            </ul>
                        </td>
                    </tr>
                    <tbody v-for="order in allOrder">
                    <tr>
                        <td colspan="8">
                            <ul class="clearfix shop-info">
                                <li>
                                    <label class="checkProduct">
                                        <input type="checkbox" name="product"
                                               v-model="order.selected"
                                               @change="changeTitleChecked(order)">
                                        <span></span>
                                    </label>
                                </li>
                                <li>{{ order.time }}</li>
                                <li>{{ order.order_code }}</li>
                                <li>{{ order.shop_name }}</li>
                                <li @click="deleteProduct(order)"><a><i class="icon iconfont icon-icon_shanchu"></i></a>
                                </li>
                            </ul>
                        </td>
                    </tr>
                    <tr v-for="(product,index) in order.products">
                        <td>
                            <router-link to="/mall/search/product-details">
                                <img :src="product.img">
                            </router-link>
                        </td>
                        <td>
                            <p>
                                <router-link to="/mall/search/product-details">
                                    {{ product.name }}
                                </router-link>
                            </p>
                            <p class="size">尺码：{{ product.size }}</p>
                        </td>
                        <td>
                            <s>￥{{ product.old_price }}</s>
                            <p>￥{{ product.price }}</p>
                        </td>
                        <td>{{ product.num }}</td>
                        <td>
                            <p><a>申请售后</a></p>
                        </td>
                        <td :rowspan="order.products.length" v-show="index===0">
                            <p>￥{{ product.num * product.price }}</p>
                            <p class="freight">（含运费{{ order.transport_price }}）</p>
                        </td>
                        <td :rowspan="order.products.length" v-show="index===0">
                            <p v-if="order.status.indexOf(1) != -1">等待买家付款</p>
                            <p v-if="order.status.indexOf(7) != -1">待发货</p>
                            <p v-if="order.status.indexOf(5) != -1">交易完成</p>
                            <p v-if="order.status.indexOf(6) != -1">已取消</p>
                            <p v-if="order.status.indexOf(2) != -1"><a>订单详情</a></p>
                            <p v-if="order.status.indexOf(3) != -1"><a>再次购买</a></p>
                            <div v-if="order.status.indexOf(4) != -1" class="logistics-box">
                                <a class="a-logistics">查看物流</a>
                                <div class="logistics">
                                    <i class="sanjiao"> </i>
                                    <p class="text-left logistics-name">
                                        {{ order.logistics.name }}
                                        <span class="waybill-num">运单号：{{ order.logistics.transport_code }}</span>
                                    </p>
                                    <ul class="clearfix">
                                        <li class="clearfix" v-for="log in order.logistics.logisticsList">
                                            <i class="cricle pull-left active"> </i>
                                            <div class="waybill-info pull-left">
                                                <p>{{ log.address }} 已收入</p>
                                                <p class="text-left">{{ log.time }}</p>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </td>
                        <td :rowspan="order.products.length" v-show="index===0">
                            <p class="remind" v-if="order.operating.indexOf(1)">请在19分25秒内完成付款</p>
                            <p v-if="order.operating.indexOf(6)!=-1"><a class="operating speak">评价</a></p>
                            <p v-if="order.operating.indexOf(2)!=-1"><a href="#">再次购买</a></p>
                            <p v-if="order.operating.indexOf(4)!=-1"><a class="pay operating">立即付款</a></p>
                            <p v-if="order.operating.indexOf(7)!=-1"><a class="speak operating">申请退款</a></p>
                            <p v-if="order.operating.indexOf(3)!=-1"><a href="#">取消订单</a></p>
                            <p v-if="order.operating.indexOf(5)!=-1"><a class="receipt operating">确认收货</a></p>
                            <p v-if="order.operating.indexOf(8)!=-1"><a href="#">退款退货中</a></p>
                            <p v-if="order.operating.indexOf(9)!=-1"><a class="receive-goods operating">确认收货</a></p>
                        </td>
                    </tr>
                    <tr class="space">
                        <td colspan="8"></td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div class="tab-pane fade" id="whit-pay">
                <p>代付款</p>
            </div>
        </div>
    </div>
</template>