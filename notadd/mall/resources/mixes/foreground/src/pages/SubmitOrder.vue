<script>
    import Message from 'iview/src/components/message';
    import Modal from '../components/Modal.vue';
    import order from '../assets/images/details/order.png';

    export default {
        components: {
            Modal,
            Message,
        },
        computed: {
            total_price() {
                let totalPrice = 0;
                this.submitOrder.productList.forEach(item => {
                    totalPrice += item.price * item.num;
                });
                return totalPrice.toFixed(2);
            },
            selectedOfferNum() {
                let num = 0;
                this.coupons.forEach(item => {
                    if (item.selected) {
                        num += 1;
                    }
                });
                return num;
            },
            selectedOfferPrice() {
                let money = 0;
                this.coupons.forEach(item => {
                    if (item.selected) {
                        money += item.money;
                    }
                });
                return money.toFixed(2);
            },
            productNum() {
                let num = 0;
                this.submitOrder.productList.forEach(item => {
                    num += item.num;
                });
                return num;
            },
        },
        data() {
            const reg = /^1[3|4|5|7|8][0-9]\d{8}$/;
            const validatorPhone = (rule, value, callback) => {
                if (value === '') {
                    callback(new Error('手机号不能为空'));
                } else if (!reg.test(value)) {
                    callback(new Error('请输入正确手机号'));
                } else {
                    callback();
                }
            };
            return {
                activeTab: 1,
                address: {
                    name: '',
                    phone: '',
                    area: [],
                    detail: '',
                    isdefault: false,
                },
                addressRule: {
                    name: [
                        {
                            required: true,
                            message: '请填写收货人姓名',
                            trigger: 'blur',
                        },
                    ],
                    phone: [
                        {
                            required: true,
                            trigger: 'blur',
                            type: 'number',
                            validator: validatorPhone,
                        },
                    ],
                    area: [
                        {
                            message: '请选择所在地区',
                            required: true,
                            type: 'array',
                            trigger: 'change',
                        },
                    ],
                    detail: [
                        {
                            required: true,
                            message: '请填写详细地址',
                            trigger: 'blur',
                        },
                        {
                            type: 'string',
                            max: 20,
                            message: '详细地址最多50个字',
                            trigger: 'blur',
                        },
                    ],
                    isdefault: false,
                },
                addStatus: 1,
                addressSelect: [
                    {
                        address: '北京市  北京市  朝阳区 解放路  某贸大厦1604',
                        isdefault: true,
                        name: '王茂',
                        phone: 12345676543,
                    },
                    {
                        address: '北京市  北京市  朝阳区 解放路  某贸大厦1604',
                        isdefault: false,
                        name: '王茂',
                        phone: 12345676543,
                    },
                ],
                cityList: [
                    {
                        value: 'beijing',
                        label: '北京市',
                    },
                    {
                        value: 'shanghai',
                        label: '上海市',
                    },
                    {
                        value: 'shenzhen',
                        label: '深圳市',
                    },
                    {
                        value: 'hangzhou',
                        label: '杭州市',
                    },
                    {
                        value: 'nanjing',
                        label: '南京市',
                    },
                    {
                        value: 'chongqing',
                        label: '重庆市',
                    },
                ],
                coupons: [
                    {
                        canuse: true,
                        endTime: '2017.12.1',
                        money: 50.00,
                        other: '无',
                        selected: false,
                        startTime: '5016.12.12',
                        type: '户外运动',
                        use: '满399元可用',
                    },
                    {
                        canuse: true,
                        endTime: '2017.12.1',
                        money: 50.00,
                        other: '无',
                        selected: false,
                        startTime: '5016.12.12',
                        type: '户外运动',
                        use: '满399元可用',
                    },
                    {
                        canuse: true,
                        endTime: '2017.12.1',
                        money: 50.00,
                        other: '无',
                        selected: false,
                        startTime: '5016.12.12',
                        type: '户外运动',
                        use: '满399元可用',
                    },
                    {
                        canuse: false,
                        endTime: '2017.12.1',
                        money: 50.00,
                        other: '无',
                        selected: false,
                        startTime: '5016.12.12',
                        type: '户外运动',
                        use: '满399元可用',
                    },
                    {
                        canuse: false,
                        endTime: '2017.12.1',
                        money: 50.00,
                        other: '无',
                        selected: false,
                        startTime: '5016.12.12',
                        type: '户外运动',
                        use: '满399元可用',
                    },
                ],
                model1: '',
                data: [
                    {
                        children: [
                            {
                                label: '故宫',
                                value: 'gugong',
                            },
                            {
                                label: '天坛',
                                value: 'tiantan',
                            },
                            {
                                label: '王府井',
                                value: 'wangfujing',
                            },
                        ],
                        label: '北京',
                        value: 'beijing',
                    },
                    {
                        children: [
                            {
                                value: 'nanjing',
                                label: '南京',
                                children: [
                                    {
                                        value: 'fuzimiao',
                                        label: '夫子庙',
                                    },
                                ],
                            },
                            {
                                value: 'suzhou',
                                label: '苏州',
                                children: [
                                    {
                                        value: 'zhuozhengyuan',
                                        label: '拙政园',
                                    },
                                    {
                                        value: 'shizilin',
                                        label: '狮子林',
                                    },
                                ],
                            },
                        ],
                        label: '江苏',
                        value: 'jiangsu',
                    },
                ],
                invoice: '',
                invoices: [
                    {
                        type: '个人',
                    },
                    {
                        type: '公司',
                    },
                ],
                methods: [
                    {
                        name: '在线支付',
                    },
                    {
                        name: '货到付款',
                    },
                ],
                modalTitle: '',
                newSelfTake: {
                    address: [],
                    city: '',
                    name: '',
                    phone: '',
                },
                selfTake: [
                    {
                        address: '北京市  北京市  朝阳区 解放路  某贸大厦1604',
                        name: '王茂',
                        phone: 12345676543,
                    },
                ],
                selfIntegral: {
                    deductibleAmount: 6.00,
                    integral: 3014,
                    useIntegral: '',
                },
                submitOrder: {
                    integral_num: 1660,
                    integral_price: 16.6,
                    freight: 20,
                    productList: [
                        {
                            color: '白色',
                            img: order,
                            name: 'Purrfect diary 咕噜日记1-7岁儿童可爱短袜5双装儿童可爱短袜5双装儿童可爱短袜5双装',
                            num: 2,
                            price: 39.9,
                            size: 'L',
                            shop: 'XXX母婴用品店',
                        },
                        {
                            color: '白色',
                            img: order,
                            name: 'Purrfect diary 咕噜日记1-7岁儿童可爱短袜5双装儿童可爱短袜5双装儿童可爱短袜5双装',
                            num: 2,
                            price: 126.07,
                            size: 'M',
                            shop: 'XXX母婴用品店',
                        },
                    ],
                },
            };
        },
        methods: {
            addAddress() {
                this.addStatus = 2;
            },
            addSelfTake() {
                this.$refs.modal.open();
                this.modalTitle = '添加自提门店';
            },
            cancelAdd() {
                this.addStatus = 1;
            },
            editDefault(item) {
                this.addressSelect.forEach(address => {
                    address.isdefault = false;
                });
                item.isdefault = true;
            },
            modifyInvoice() {
                this.$refs.invoice.open();
            },
            closeInvoice() {
                this.$refs.invoice.close();
            },
            notNeedInvoice() {
                this.invoice = '不需要发票';
                this.closeInvoice();
            },
            selfTakeAdd() {
                this.selfTake.push(this.newSelfTake);
                this.$refs.modal.close();
            },
            saveAddress() {
                this.$refs.addressForm.validate(valid => {
                    if (valid) {
                        Message.success('提交成功!');
                        this.addressSelect.push(this.address);
                        this.addStatus = 1;
                    } else {
                        Message.error('表单验证失败!');
                    }
                });
            },
            delateAddress(item) {
                const index = this.addressSelect.indexOf(item);
                this.addressSelect.splice(index, 1);
            },
            switchUseOffer(index) {
                this.activeTab = index;
            },
        },
    };
</script>
<template>
    <div class="submit-order padding-attribute">
        <div class="container">
            <div class="select-address">
                <div>
                    <p class="select-title">确认订单</p>
                    <p>请仔细核对填写收货，发票等信息，以确保物流快递及时准确投递</p>
                </div>
                <div class="address-selected">
                    <h5>收货人信息</h5>
                    <div class="address-list" v-if="addStatus === 1" v-for="(item, index) in addressSelect"
                         :key="index">
                        <label class="form-control-radio">
                            <input type="radio" name="address" :checked="index == 0">
                            <div class="address clearfix">
                                <p>
                                    <span>{{ item.name }}</span>
                                    <span>{{ item.phone }}</span>
                                    <span class="address-detail">{{ item.address }}</span>
                                    <i v-if="item.isdefault">默认地址</i>
                                    <span class="pull-right" v-if="item.isdefault === false">
                                        <span @click="editDefault(item)">设为默认地址</span>
                                        <span @click="addAddress">编辑</span>
                                        <span @click="delateAddress(item)">删除</span>
                                    </span>
                                </p>
                            </div>
                        </label>
                    </div>
                    <a class="select-btn"
                       @click="addAddress"
                       v-if="addStatus === 1">新增收货地址</a>
                    <div class="add-address" v-if="addStatus === 2">
                        <i-form class="signup-form" ref="addressForm" :model="address" :rules="addressRule">
                            <form-item class="signup-form-group clearfix" label="收货人姓名" prop="name">
                                <i-input v-model="address.name"
                                         class="signup-form-control"
                                         placeholder="请输入收货人姓名">
                                </i-input>
                            </form-item>
                            <form-item class="signup-form-group clearfix" label="手机号码" prop="phone">
                                <i-input v-model="address.phone"
                                         class="signup-form-control"
                                         placeholder="手机号码为必填项">
                                </i-input>
                            </form-item>
                            <form-item class="signup-form-group clearfix" label="所在地区" prop="area">
                                <cascader class="destination pull-left"
                                          :data="data"
                                          width="180px"
                                          v-model="address.area">
                                </cascader>
                            </form-item>
                            <form-item class="signup-form-group clearfix" label="详细地址" prop="detail">
                                <i-input v-model="address.detail"
                                         class="signup-form-control"
                                         type="textarea"
                                         :autosize="{minRows: 3,maxRows: 5}"
                                         placeholder="无需重复填写省市区，小于50个字">
                                </i-input>
                            </form-item>
                        </i-form>
                        <label class="clearfix select-default">
                            <div class="pull-left">
                                <input type="checkbox" v-model="address.isdefault">
                                <span></span>
                            </div>
                            <span class="pull-left">设为默认地址</span>
                        </label>
                        <div class="btn-div">
                            <a class="order-btn submit-btn pull-left" @click="saveAddress">保存地址</a>
                            <a @click="cancelAdd">取消</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="address-selected self-take-select">
                <h5>使用自提门店</h5>
                <div>
                    <label class="form-control-radio" v-for="(take, index) in selfTake" :key="index">
                        <input type="radio" name="takeAddress">
                        <div class="address clearfix">
                            <p>
                                <span>{{ take.name }}</span>
                                <span>{{ take.phone }}</span>
                                <span class="address-detail">{{ take.address }}</span>
                                <a class="self-take pull-right" @click="addSelfTake">修改自提信息</a>
                            </p>
                        </div>
                    </label>
                </div>
                <a class="select-btn add-self-take"
                   @click="addSelfTake"
                   v-if="selfTake.length === 0">+添加自提点</a>
                <modal ref="modal">
                    <div slot="title">
                        <h4 class="modal-title" v-text="modalTitle"></h4>
                    </div>
                    <div slot="body">
                        <form class="signup-form">
                            <div class="signup-form-group clearfix">
                                <label class="form-title">收货人姓名</label>
                                <input type="text"
                                       class="signup-form-control"
                                       name="username"
                                       placeholder="请输入收货人姓名"
                                       v-model="newSelfTake.name">
                            </div>
                            <div class="signup-form-group clearfix">
                                <label class="form-title">手机号码</label>
                                <input type="number"
                                       class="signup-form-control"
                                       name="telphone"
                                       placeholder="手机号码为必填项"
                                       v-model.number="newSelfTake.phone">
                            </div>
                            <div class="signup-form-group clearfix">
                                <label class="form-title">门店</label>
                                <cascader class="destination pull-left"
                                          :data="data"
                                          v-model="newSelfTake.address">
                                </cascader>
                                <i-select v-model="newSelfTake.city" style="width:200px">
                                    <i-option v-for="(item, index) in cityList"
                                            :value="item.value"
                                            :key="index">
                                        {{ item.label }}
                                    </i-option>
                                </i-select>
                            </div>
                        </form>
                    </div>
                    <button type="button"
                            class="order-btn"
                            @click="selfTakeAdd"
                            slot="save_address">保存门店
                    </button>
                </modal>
            </div>
            <div class="pay-method">
                <h5 class="select-title">支付方式</h5>
                <div class="methods">
                    <label class="form-control-radio" v-for="(method, index) in methods" :key="index">
                        <input type="radio" name="method">
                        <span>{{ method.name }}</span>
                    </label>
                </div>
            </div>
            <div class="pay-method invoice-info">
                <h5 class="select-title">发票信息</h5>
                <p>{{ invoice }} <a @click="modifyInvoice">修改</a></p>
                <modal ref="invoice">
                    <div slot="title">
                        <h4 class="modal-title">发票信息</h4>
                    </div>
                    <div slot="body">
                        <form class="signup-form">
                            <div class="signup-form-group clearfix">
                                <label class="form-title">发票类型</label>
                                <label class="form-control-radio">
                                    <input type="radio" name="invoice" value="普通发票" v-model="invoice">
                                    <span>普通发票</span>
                                </label>
                                <label class="form-control-radio">
                                    <input type="radio" name="invoice" value="不需要发票" v-model="invoice">
                                    <span>不需要发票</span>
                                </label>
                            </div>
                            <div class="signup-form-group clearfix">
                                <label class="form-title">发票抬头</label>
                                <i-select v-model="invoice.title" class="invoice-select" style="width:200px">
                                    <i-option v-for="(item, index) in cityList"
                                            :value="item.value"
                                            :key="index">
                                        {{ item.label }}
                                    </i-option>
                                </i-select>
                            </div>
                            <div class="signup-form-group clearfix">
                                <label class="form-title">发票内容</label>
                                <input class="form-control invoice-content" type="text" v-model="invoice.info">
                            </div>
                        </form>
                    </div>
                    <button type="button"
                            @click="closeInvoice"
                            class="order-btn"
                            slot="save_address">保存发票信息
                    </button>
                    <button type="button"
                            @click="notNeedInvoice"
                            class="order-btn notNeed"
                            slot="save_address">不需要发票
                    </button>
                </modal>
            </div>
            <div class="ensure-information">
                <p class="select-title">商品清单</p>
                <ul class="product-head clearfix">
                    <li class="pull-left">商品信息</li>
                    <li class="pull-left text-center">单价(元)</li>
                    <li class="pull-left text-center">数量</li>
                    <li class="pull-left text-center">金额</li>
                </ul>
                <ul class="product-list">
                    <li v-for="(order, index) in submitOrder.productList" :key="index">
                        <h5>店铺{{ order.shop }}</h5>
                        <ul class="order-detail clearfix">
                            <li class="pull-left clearfix">
                                <img class="pull-left" :src="order.img" alt="">
                                <div class="pull-left">
                                    <p>{{ order.name }}</p>
                                    <p>颜色: {{ order.color }} 尺码: {{ order.size }}</p>
                                </div>
                            </li>
                            <li class="pull-left text-center">￥{{ order.price }}</li>
                            <li class="pull-left text-center">{{ order.num }}</li>
                            <li class="pull-left text-center price">￥{{ order.price * order.num }}</li>
                        </ul>
                        <div>
                            买家留言：
                            <input class="form-control"
                                   type="text"
                                   placeholder="限50字（对本次交易的说明，建议填写已经和商家达成一致的说明）">
                        </div>
                    </li>
                </ul>
                <p class="select-title">使用优惠/积分</p>
                <ul class="select-offer clearfix">
                    <li class="pull-left" :class="{active:activeTab===1}" @click="switchUseOffer(1)">
                        优惠券
                    </li>
                    <li class="pull-left" :class="{active:activeTab===2}" @click="switchUseOffer(2)">
                        积分
                    </li>
                </ul>
                <div class="tab-content" v-if="activeTab === 1">
                    <ul class="tab-pane fade in active clearfix">
                        <li class="pull-left pane pull-left"
                            :class="{canuse:coupon.canuse}"
                            v-for="(coupon, index) in coupons"
                            :key="index">
                            <label>
                                <input type="checkbox"
                                       :disabled="coupon.canuse === false"
                                       v-model="coupon.selected"
                                       name="offer">
                                <div>
                                    <div class="coupons">
                                        <span class="symbol">￥</span>
                                        {{ coupon.money }}&nbsp;
                                        <span>{{ coupon.use }}</span>
                                        <a>取消使用</a>
                                    </div>
                                    <div class="coupons-info text-center">
                                        <ul class="text-left">
                                            <li>品类限制：{{ coupon.type }}</li>
                                            <li>使用时间：{{ coupon.startTime }}-{{ coupon.endTime }}</li>
                                        </ul>
                                        <i class="iconfont icon icon-used-copy"></i>
                                    </div>
                                </div>
                            </label>
                        </li>
                    </ul>
                    <div class="totalCoupon">
                        使用优惠券{{ selectedOfferNum }}张
                        <span>优惠金额 <b>￥{{ selectedOfferPrice }}</b></span>
                    </div>
                </div>
                <div class="tab-content integral" v-if="activeTab === 2">
                    <span>账户共&nbsp;<b>{{ selfIntegral.integral }}</b>&nbsp;积分</span>
                    本次使用 &nbsp;
                    <input class="form-control" type="number" v-model.number="selfIntegral.useIntegral">
                    &nbsp;抵扣 <i>￥{{ selfIntegral.deductibleAmount }}</i>
                    <div class="totalCoupon totalIntegral">
                        使用{{ selfIntegral.useIntegral }}积分
                        <span>抵扣金额 <b>￥{{ selfIntegral.deductibleAmount }}</b></span>
                    </div>
                </div>
                <div class="order-submit submit-btn">
                    <div class="order-submit-content clearfix">
                        <span class="order-price price">&yen;{{ total_price}}</span>
                        <span class="name">{{ productNum }}件商品&nbsp;(不含运费)：</span>
                    </div>
                    <div class="order-submit-content clearfix">
                        <span class="order-price">-&yen;{{ submitOrder.freight }}</span>
                        <span class="name">运费：</span>
                    </div>
                    <div class="order-submit-content clearfix">
                        <span class="order-price">-&yen;{{ selectedOfferPrice }}</span>
                        <span class="name">商品优惠：</span>
                    </div>
                    <router-link to="/mall/order-success" class="order-btn submit-btn">提交订单</router-link>
                </div>
            </div>
        </div>
    </div>
</template>