<script>
    import image1 from '../assets/images/img_logo.png';

    export default {
        beforeRouteEnter(to, from, next) {
            next(() => {
            });
        },
        data() {
            return {
                cancelOrder: {
                    buyer: '第三方',
                    data: '2017-06-03',
                    num: 458789990006645,
                    time: '',
                },
                loading: false,
                managementSearch: '',
                modal: false,
                orderReceipt: [
                    {
                        address: '陕西省  西安市  雁塔区  高新四路  36号国土资源大厦2304',
                        buyer: 'maijiaming',
                        freight: '10.00',
                        goods: [
                            {
                                amount: 2,
                                image: image1,
                                name: 'MIUI/小米 小米手机4 小米4 MI4智能4G手机包邮 黑色 D-LT（4G）/TD-SCD',
                                price: '100.00',
                            },
                        ],
                        phone: 123455677,
                        user: 'wang',
                    },
                    {
                        address: '陕西省  西安市  雁塔区  高新四路  36号国土资源大厦2304',
                        buyer: 'maijiaming',
                        freight: '10.00',
                        goods: [
                            {
                                amount: 2,
                                image: image1,
                                name: 'MIUI/小米 小米手机4 小米4 MI4智能4G手机包邮 黑色 D-LT（4G）/TD-SCD',
                                price: '100.00',
                            },
                            {
                                amount: 2,
                                image: image1,
                                name: 'MIUI/小米 小米手机4 小米4 MI4智能4G手机包邮 黑色 D-LT（4G）/TD-SCD',
                                price: '100.00',
                            },
                        ],
                        phone: 123455677,
                        user: 'wang',
                    },
                    {
                        address: '陕西省  西安市  雁塔区  高新四路  36号国土资源大厦2304',
                        buyer: 'maijiaming',
                        freight: '10.00',
                        goods: [
                            {
                                amount: 2,
                                image: image1,
                                name: 'MIUI/小米 小米手机4 小米4 MI4智能4G手机包邮 黑色 D-LT（4G）/TD-SCD',
                                price: '100.00',
                            },
                        ],
                        image: image1,
                        isGifts: false,
                        phone: 123455677,
                        user: 'wang',
                    },
                    {
                        address: '陕西省  西安市  雁塔区  高新四路  36号国土资源大厦2304',
                        buyer: 'maijiaming',
                        freight: '10.00',
                        goods: [
                            {
                                amount: 2,
                                image: image1,
                                name: 'MIUI/小米 小米手机4 小米4 MI4智能4G手机包邮 黑色 D-LT（4G）/TD-SCD',
                                price: '100.00',
                            },
                            {
                                amount: 2,
                                image: image1,
                                name: 'MIUI/小米 小米手机4 小米4 MI4智能4G手机包邮 黑色 D-LT（4G）/TD-SCD',
                                price: '100.00',
                            },
                        ],
                        phone: 123455677,
                        user: 'wang',
                    },
                ],
                orderShip: [
                    {
                        address: '陕西省  西安市  雁塔区  高新四路  36号国土资源大厦2304',
                        buyer: 'maijiaming',
                        freight: '10.00',
                        goods: [
                            {
                                amount: 2,
                                image: image1,
                                name: 'MIUI/小米 小米手机4 小米4 MI4智能4G手机包邮 黑色 D-LT（4G）/TD-SCD',
                                price: '100.00',
                            },
                        ],
                        phone: 123455677,
                        user: 'wang',
                    },
                    {
                        address: '陕西省  西安市  雁塔区  高新四路  36号国土资源大厦2304',
                        buyer: 'maijiaming',
                        freight: '10.00',
                        goods: [
                            {
                                amount: 2,
                                image: image1,
                                name: 'MIUI/小米 小米手机4 小米4 MI4智能4G手机包邮 黑色 D-LT（4G）/TD-SCD',
                                price: '100.00',
                            },
                            {
                                amount: 2,
                                image: image1,
                                name: 'MIUI/小米 小米手机4 小米4 MI4智能4G手机包邮 黑色 D-LT（4G）/TD-SCD',
                                price: '100.00',
                            },
                        ],
                        phone: 123455677,
                        user: 'wang',
                    },
                    {
                        address: '陕西省  西安市  雁塔区  高新四路  36号国土资源大厦2304',
                        buyer: 'maijiaming',
                        freight: '10.00',
                        goods: [
                            {
                                amount: 2,
                                image: image1,
                                name: 'MIUI/小米 小米手机4 小米4 MI4智能4G手机包邮 黑色 D-LT（4G）/TD-SCD',
                                price: '100.00',
                            },
                        ],
                        image: image1,
                        isGifts: false,
                        phone: 123455677,
                        user: 'wang',
                    },
                    {
                        address: '陕西省  西安市  雁塔区  高新四路  36号国土资源大厦2304',
                        buyer: 'maijiaming',
                        freight: '10.00',
                        goods: [
                            {
                                amount: 2,
                                image: image1,
                                name: 'MIUI/小米 小米手机4 小米4 MI4智能4G手机包邮 黑色 D-LT（4G）/TD-SCD',
                                price: '100.00',
                            },
                            {
                                amount: 2,
                                image: image1,
                                name: 'MIUI/小米 小米手机4 小米4 MI4智能4G手机包邮 黑色 D-LT（4G）/TD-SCD',
                                price: '100.00',
                            },
                        ],
                        phone: 123455677,
                        user: 'wang',
                    },
                ],
                shiping: [
                    {
                        address: '陕西省  西安市  雁塔区  高新四路  36号国土资源大厦2304',
                        buyer: 'maijiaming',
                        freight: '10.00',
                        goods: [
                            {
                                amount: 2,
                                image: image1,
                                name: 'MIUI/小米 小米手机4 小米4 MI4智能4G手机包邮 黑色 D-LT（4G）/TD-SCD',
                                price: '100.00',
                            },
                        ],
                        phone: 123455677,
                        user: 'wang',
                    },
                    {
                        address: '陕西省  西安市  雁塔区  高新四路  36号国土资源大厦2304',
                        buyer: 'maijiaming',
                        freight: '10.00',
                        goods: [
                            {
                                amount: 2,
                                image: image1,
                                name: 'MIUI/小米 小米手机4 小米4 MI4智能4G手机包邮 黑色 D-LT（4G）/TD-SCD',
                                price: '100.00',
                            },
                            {
                                amount: 2,
                                image: image1,
                                name: 'MIUI/小米 小米手机4 小米4 MI4智能4G手机包邮 黑色 D-LT（4G）/TD-SCD',
                                price: '100.00',
                            },
                        ],
                        phone: 123455677,
                        user: 'wang',
                    },
                    {
                        address: '陕西省  西安市  雁塔区  高新四路  36号国土资源大厦2304',
                        buyer: 'maijiaming',
                        freight: '10.00',
                        goods: [
                            {
                                amount: 2,
                                image: image1,
                                name: 'MIUI/小米 小米手机4 小米4 MI4智能4G手机包邮 黑色 D-LT（4G）/TD-SCD',
                                price: '100.00',
                            },
                        ],
                        image: image1,
                        isGifts: false,
                        phone: 123455677,
                        user: 'wang',
                    },
                    {
                        address: '陕西省  西安市  雁塔区  高新四路  36号国土资源大厦2304',
                        buyer: 'maijiaming',
                        freight: '10.00',
                        goods: [
                            {
                                amount: 2,
                                image: image1,
                                name: 'MIUI/小米 小米手机4 小米4 MI4智能4G手机包邮 黑色 D-LT（4G）/TD-SCD',
                                price: '100.00',
                            },
                            {
                                amount: 2,
                                image: image1,
                                name: 'MIUI/小米 小米手机4 小米4 MI4智能4G手机包邮 黑色 D-LT（4G）/TD-SCD',
                                price: '100.00',
                            },
                        ],
                        phone: 123455677,
                        user: 'wang',
                    },
                ],
                searchList: [
                    {
                        label: '店铺名称',
                        value: '店铺名称',
                    },
                    {
                        label: '商品名称',
                        value: '商品名称',
                    },
                    {
                        label: '商品分类',
                        value: '商品分类',
                    },
                ],
                self: this,
                timeList: [
                    {
                        label: '1',
                        value: '1',
                    },
                    {
                        label: '2',
                        value: '2',
                    },
                    {
                        label: '3',
                        value: '3',
                    },
                ],
            };
        },
        methods: {
            delayedReceipt() {
                this.modal = true;
            },
            lookLogistics() {
                const self = this;
                self.$router.push({
                    path: 'ship/look',
                });
            },
            settingShip() {
                const self = this;
                self.$router.push({
                    path: 'ship/set',
                });
            },
            submitCancelOrder() {
                const self = this;
                self.loading = true;
                self.$refs.cancelOrder.validate(valid => {
                    if (valid) {
                        window.console.log(valid);
                    } else {
                        self.loading = false;
                        self.$notice.error({
                            title: '请正确填写设置信息！',
                        });
                    }
                });
            },
        },
    };
</script>
<template>
    <div class="seller-wrap">
        <div class="order-ship">
            <tabs value="name1">
                <tab-pane label="待发货" name="name1">
                    <card :bordered="false">
                        <div class="prompt-box">
                            <p>提示</p>
                            <p>1.可以对待发货的订单进行发货操作，发货时可以设置收货人和发货人信息，填写一些备忘信息，
                                选择相应的物流服务，打印发货单</p>
                            <p>2.已经设置为发货中的订单，您还可以继续编辑上次的发货信息</p>
                            <p>3.如果因物流等原因造成买家不能及时收货，您可以使用点击延迟收货按钮来延迟系统的自动收货时间</p>
                            <p>4.订单全部退款中的订单不能进行发货，拼团订单之后只有成团后才能发货</p>
                        </div>
                        <div class="analysis-content clearfix">
                            <div class="order-money-content">
                                <div class="select-content">
                                    <ul class="clearfix">
                                        <li>
                                            下单时间
                                            <date-picker type="date" placeholder="选择日期"
                                                         style="width: 124px"></date-picker>
                                            -
                                            <date-picker type="date" placeholder="选择日期"
                                                         style="width: 124px"></date-picker>
                                        </li>
                                        <li class="store-body-header-right">
                                            <i-input v-model="applicationWord" placeholder="请输入关键词进行搜索">
                                                <span slot="prepend">订单编号</span>
                                                <i-button slot="append" type="primary">搜索</i-button>
                                            </i-input>
                                        </li>
                                    </ul>
                                </div>
                                <!-- <div class="page">
                                     <page :total="100" show-elevator></page>
                                 </div>-->
                            </div>
                        </div>
                        <table class="order-table">
                            <tbody v-for="(item, index) in orderShip">
                            <tr class="space">
                                <td colspan="2">
                                    <span>订单号:{{ item.num }}</span>
                                    <span>下单时间:{{ item.createTime }}</span>
                                    <button class="print-btn">打印发货单</button>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div>
                                        <div class="goods-name-row" v-for="item in item.goods">
                                            <div class="img-text">
                                                <div class="img">
                                                    <img :src="item.image" alt="">
                                                </div>
                                                <div class="goods-name">
                                                    <p>{{ item.name }}</p>
                                                    <p>&yen;{{ item.price }} X {{ item.amount }}件</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="goods-gifts" v-if="item.isGifts">
                                            赠品<img :src="item.image" alt="">
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <i-form ref="order" :model="order" :rules="ruleValidate" :label-width="80">
                                        <row>
                                            <i-col>
                                                <form-item label="买家">{{ item.buyer}}</form-item>
                                            </i-col>
                                        </row>
                                        <row>
                                            <i-col>
                                                <form-item label="运费">{{ item.freight}}</form-item>
                                            </i-col>
                                        </row>
                                        <row>
                                            <i-col>
                                                <form-item label="收货人">{{ item.user}} {{ item.phone}}
                                                    <p>{{ item.address}}</p>
                                                </form-item>
                                            </i-col>
                                        </row>
                                        <row>
                                            <i-col>
                                                <i-button type="primary" @click.native="settingShip(item)">设置发货</i-button>
                                            </i-col>
                                        </row>
                                    </i-form>
                                </td>
                            </tr>
                            <tr class="space-bg">
                                <td colspan="2"></td>
                            </tr>
                            </tbody>
                        </table>
                    </card>
                </tab-pane>
                <tab-pane label="发货中" name="name2">
                    <card :bordered="false">
                        <div class="prompt-box">
                            <p>提示</p>
                            <p>1.可以对待发货的订单进行发货操作，发货时可以设置收货人和发货人信息，填写一些备忘信息，
                                选择相应的物流服务，打印发货单</p>
                            <p>2.已经设置为发货中的订单，您还可以继续编辑上次的发货信息</p>
                            <p>3.如果因物流等原因造成买家不能及时收货，您可以使用点击延迟收货按钮来延迟系统的自动收货时间</p>
                            <p>4.订单全部退款中的订单不能进行发货，拼团订单之后只有成团后才能发货</p>
                        </div>
                        <div class="analysis-content clearfix">
                            <div class="order-money-content">
                                <div class="select-content">
                                    <ul class="clearfix">
                                        <li>
                                            下单时间
                                            <date-picker type="date" placeholder="选择日期"
                                                         style="width: 124px"></date-picker>
                                            -
                                            <date-picker type="date" placeholder="选择日期"
                                                         style="width: 124px"></date-picker>
                                        </li>
                                        <li class="store-body-header-right">
                                            <i-input v-model="applicationWord" placeholder="请输入关键词进行搜索">
                                                <span slot="prepend">订单编号</span>
                                                <i-button slot="append" type="primary">搜索</i-button>
                                            </i-input>
                                        </li>
                                    </ul>
                                </div>
                                <!-- <div class="page">
                                     <page :total="100" show-elevator></page>
                                 </div>-->
                            </div>
                        </div>
                        <table class="order-table">
                            <tbody v-for="(item, index) in shiping">
                            <tr class="space">
                                <td colspan="2" class="clearfix">
                                    <span>订单号:{{ item.num }}</span>
                                    <span>下单时间:{{ item.createTime }}</span>
                                    <div class="look-shipping">
                                        <button class="print-btn" @click="lookLogistics">查看物流</button>
                                        <button class="print-btn">打印发货单</button>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div>
                                        <div class="goods-name-row" v-for="item in item.goods">
                                            <div class="img-text">
                                                <div class="img">
                                                    <img :src="item.image" alt="">
                                                </div>
                                                <div class="goods-name">
                                                    <p>{{ item.name }}</p>
                                                    <p>&yen;{{ item.price }} X {{ item.amount }}件</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <i-form ref="order" :model="order" :rules="ruleValidate" :label-width="80">
                                        <row>
                                            <i-col>
                                                <form-item label="买家">{{ item.buyer}}</form-item>
                                            </i-col>
                                        </row>
                                        <row>
                                            <i-col>
                                                <form-item label="运费">{{ item.freight}}</form-item>
                                            </i-col>
                                        </row>
                                        <row>
                                            <i-col>
                                                <form-item label="收货人">{{ item.user}} {{ item.phone}}
                                                    <p>{{ item.address}}</p>
                                                </form-item>
                                            </i-col>
                                        </row>
                                        <row>
                                            <i-col>
                                                <i-button type="primary" @click.native="delayedReceipt">延迟收货</i-button>
                                            </i-col>
                                        </row>
                                    </i-form>
                                </td>
                            </tr>
                            <tr class="space-bg">
                                <td colspan="2"></td>
                            </tr>
                            </tbody>
                        </table>
                        <modal
                                v-model="modal"
                                title="延迟收货" class="upload-picture-modal delayed-receipt-modal">
                            <div>
                                <i-form ref="cancelOrder" :model="cancelOrder" :rules="cancelValidate" :label-width="100">
                                    <row>
                                        <i-col span="12">
                                            <form-item label="订单编号">
                                                {{ cancelOrder.num }}
                                            </form-item>
                                        </i-col>
                                    </row>
                                    <row>
                                        <i-col span="12">
                                            <form-item label="买家">
                                                {{ cancelOrder.buyer }}
                                            </form-item>
                                        </i-col>
                                    </row>
                                    <row>
                                        <i-col span="12">
                                            <form-item label="最晚收货时间">
                                                {{ cancelOrder.data }}
                                            </form-item>
                                        </i-col>
                                    </row>
                                    <form-item label="延迟">
                                        <row>
                                            <i-col span="10">
                                                <i-select v-model="cancelOrder.time" style="width: 235px;">
                                                    <i-option v-for="item in timeList"
                                                              :value="item.value">{{ item.label }}</i-option>
                                                </i-select>
                                            </i-col>
                                            <i-col span="2">天</i-col>
                                        </row>
                                    </form-item>
                                    <row>
                                        <i-col span="20">
                                            <form-item>
                                                <i-button :loading="loading" type="primary" @click.native="submitCancelOrder">
                                                    <span v-if="!loading">确认提交</span>
                                                    <span v-else>正在提交…</span>
                                                </i-button>
                                            </form-item>
                                        </i-col>
                                    </row>
                                </i-form>
                            </div>
                        </modal>
                    </card>
                </tab-pane>
                <tab-pane label="已收货" name="name3">
                    <card :bordered="false">
                        <div class="prompt-box">
                            <p>提示</p>
                            <p>1.可以对待发货的订单进行发货操作，发货时可以设置收货人和发货人信息，填写一些备忘信息，
                                选择相应的物流服务，打印发货单</p>
                            <p>2.已经设置为发货中的订单，您还可以继续编辑上次的发货信息</p>
                            <p>3.如果因物流等原因造成买家不能及时收货，您可以使用点击延迟收货按钮来延迟系统的自动收货时间</p>
                            <p>4.订单全部退款中的订单不能进行发货，拼团订单之后只有成团后才能发货</p>
                        </div>
                        <div class="analysis-content clearfix">
                            <div class="order-money-content">
                                <div class="select-content">
                                    <ul class="clearfix">
                                        <li>
                                            下单时间
                                            <date-picker type="date" placeholder="选择日期"
                                                         style="width: 124px"></date-picker>
                                            -
                                            <date-picker type="date" placeholder="选择日期"
                                                         style="width: 124px"></date-picker>
                                        </li>
                                        <li class="store-body-header-right">
                                            <i-input v-model="applicationWord" placeholder="请输入关键词进行搜索">
                                                <span slot="prepend">订单编号</span>
                                                <i-button slot="append" type="primary">搜索</i-button>
                                            </i-input>
                                        </li>
                                    </ul>
                                </div>
                                <!-- <div class="page">
                                     <page :total="100" show-elevator></page>
                                 </div>-->
                            </div>
                        </div>
                        <table class="order-table">
                            <tbody v-for="(item, index) in orderReceipt">
                            <tr class="space">
                                <td colspan="2">
                                    <span>订单号:{{ item.num }}</span>
                                    <span>下单时间:{{ item.createTime }}</span>
                                    <button class="print-btn">打印发货单</button>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div>
                                        <div class="goods-name-row" v-for="item in item.goods">
                                            <div class="img-text">
                                                <div class="img">
                                                    <img :src="item.image" alt="">
                                                </div>
                                                <div class="goods-name">
                                                    <p>{{ item.name }}</p>
                                                    <p>&yen;{{ item.price }} X {{ item.amount }}件</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <i-form ref="order" :model="order" :rules="ruleValidate" :label-width="80">
                                        <row>
                                            <i-col>
                                                <form-item label="买家">{{ item.buyer}}</form-item>
                                            </i-col>
                                        </row>
                                        <row>
                                            <i-col>
                                                <form-item label="运费">{{ item.freight}}</form-item>
                                            </i-col>
                                        </row>
                                        <row>
                                            <i-col>
                                                <form-item label="收货人">{{ item.user}} {{ item.phone}}
                                                    <p>{{ item.address}}</p>
                                                </form-item>
                                            </i-col>
                                        </row>
                                        <row>
                                            <i-col></i-col>
                                        </row>
                                    </i-form>
                                </td>
                            </tr>
                            <tr class="space-bg">
                                <td colspan="2"></td>
                            </tr>
                            </tbody>
                        </table>
                    </card>
                </tab-pane>
            </tabs>
        </div>
    </div>
</template>