<script>
    import SplinLine from '../components/SplinLine.vue';
    import productImg from '../assets/images/img_06.png';

    export default {
        components: {
            SplinLine,
        },
        computed: {
            selectNum() {
                let num = 0;
                this.productList.forEach(item => {
                    item.products.forEach(product => {
                        if (product.selected) {
                            num += product.num;
                        }
                    });
                });
                return num;
            },
            totalPrice() {
                let tPrice = 0;
                this.productList.forEach(item => {
                    item.products.forEach(product => {
                        if (product.selected) {
                            tPrice += product.num * product.now_price;
                        }
                    });
                });
                return tPrice.toFixed(2);
            },
            totalFreight() {
                let tFreight = 0;
                this.productList.forEach(item => {
                    if (item.selected) {
                        tFreight += item.pay_transform;
                    } else {
                        let select = false;
                        if (item.products.length > 1) {
                            item.products.forEach(product => {
                                if (product.selected) {
                                    select = true;
                                }
                            });
                        }
                        if (select) {
                            tFreight += item.pay_transform;
                        }
                    }
                });
                return tFreight.toFixed(2);
            },
        },
        data() {
            return {
                isAllChecked: false,
                loading: true,
                login: true,
                productList: [
                    {
                        name: '母婴',
                        pay_transform: 10,
                        mention: false,
                        selected: false,
                        products: [
                            {
                                id: 1,
                                img: productImg,
                                selected: false,
                                name: 'Purrfect diary 咕噜日记1-7岁儿童可爱短袜5双装儿童可爱短袜5双装儿童 可爱短袜5双装',
                                num: 1,
                                now_price: 39.9,
                                old_price: 126.07,
                                size: 'M',
                            },
                        ],
                    },
                    {
                        name: 'XX母婴用品店',
                        pay_transform: 10,
                        mention: true,
                        selected: false,
                        products: [
                            {
                                id: 1,
                                img: productImg,
                                selected: false,
                                name: 'Purrfect diary 咕噜日记1-7岁儿童可爱短袜5双装儿童可爱短袜5双装儿童 可爱短袜5双装',
                                num: 1,
                                now_price: 39.9,
                                old_price: 126.07,
                                size: 'M',
                            },
                            {
                                id: 1,
                                img: productImg,
                                selected: false,
                                name: 'Purrfect diary 咕噜日记1-7岁儿童可爱短袜5双装儿童可爱短袜5双装儿童 可爱短袜5双装',
                                num: 1,
                                now_price: 39.9,
                                old_price: 126.07,
                                size: 'M',
                            },
                        ],
                    },
                    {
                        name: 'XX母婴用品',
                        offer: '买二送一',
                        mention: false,
                        selected: false,
                        pay_transform: 10,
                        products: [
                            {
                                id: 1,
                                img: productImg,
                                selected: false,
                                name: 'Purrfect diary 咕噜日记1-7岁儿童可爱短袜5双装儿童可爱短袜5双装儿童 可爱短袜5双装',
                                num: 1,
                                now_price: 39.9,
                                old_price: 126.07,
                                size: 'M',
                            },
                            {
                                id: 1,
                                img: productImg,
                                selected: false,
                                name: 'Purrfect diary 咕噜日记1-7岁儿童可爱短袜5双装儿童可爱短袜5双装儿童 可爱短袜5双装',
                                num: 1,
                                now_price: 39.9,
                                old_price: 126.07,
                                size: 'M',
                            },
                        ],
                    },
                ],
                selectPro: [],
            };
        },
        methods: {
//            全选框change事件的回调处理方法
            changeAllChecked() {
                if (this.isAllChecked) {
                    this.productList.forEach(data => {
                        data.selected = true;
                        data.products.forEach(item => {
                            item.selected = true;
                        });
                    });
                } else {
                    this.productList.forEach(data => {
                        data.selected = false;
                        data.products.forEach(item => {
                            item.selected = false;
                        });
                    });
                }
            },
//            当父标题状态变化时的处理方法
            changeTitleChecked(data) {
                this.isAllChecked = true;
                if (data.selected) {
                    data.products.forEach(item => {
                        item.selected = true;
                    });
                } else {
                    data.products.forEach(item => {
                        item.selected = false;
                    });
                }
                this.productList.forEach(item => {
                    if (item.selected === false) {
                        this.isAllChecked = false;
                    }
                });
            },
            deleteProduct(arr, num, index) {
                if (arr.products.length === 1) {
                    this.productList.splice(index, 1);
                }
                arr.products.splice(num, 1);
            },
            deleteSelected() {
                const self = this;
                Object.keys(self.productList).forEach(a => {
                    if (self.productList[a].selected) {
                        window.console.log(self.productList[a]);
                        self.productList.splice(a, 1);
                    } else {
                        Object.keys(self.productList[a].products).forEach(i => {
                            window.console.log(self.productList[a]);
                            if (self.productList[a].products[i].selected) {
                                self.productList[a].products.splice(i, 1);
                            }
                        });
                    }
                });
            },
            plus(item) {
                item.num += 1;
            },
            price(num, price) {
                return (price * num).toFixed(2);
            },
            reduce(item) {
                if (item.num > 1) {
                    item.num -= 1;
                }
            },
//            单个商品选中时处理方法
            selectProduct(item) {
                this.isAllChecked = true;
                item.selected = true;
                item.products.forEach(product => {
                    if (product.selected === false) {
                        item.selected = false;
                    }
                });
                this.productList.forEach(pro => {
                    if (pro.selected === false) {
                        this.isAllChecked = false;
                    }
                });
            },
        },
        mounted() {
            const self = this;
            self.$nextTick(() => {
                setTimeout(() => {
                    self.loading = false;
                }, 1000);
            });
        },
    };
</script>
<template>
    <div class="cart-settlement padding-attribute">
        <div class="no-product" v-if="!productList.length">
            <div class="clearfix">
                <div class="icon iconfont icon-gouwuche pull-left"></div>
                <div class="pull-left no-product-text">
                    <p>购物车里什么都没有哦~</p>
                    <router-link class="login" to="/mall/signin" v-if="login === false">登录</router-link>
                    <router-link to="/mall/search">去逛逛>></router-link>
                </div>
            </div>
        </div>
        <div v-if="!loading">
            <div class="container" v-if="productList.length">
                <p class="select-title">购物车</p>
                <div class="product-information cart-select-model">
                    <table width="100%">
                        <colgroup>
                            <col width="46px">
                            <col width="40px">
                            <col width="500px">
                            <col width="150px">
                            <col width="154px">
                            <col width="150px">
                            <col width="150px">
                        </colgroup>
                        <thead>
                        <tr>
                            <th class="select">
                                <div class="check-box select-all">
                                <span>
                                    <!--全选-->
                                    <input type="checkbox" v-model="isAllChecked" @change="changeAllChecked($event)"
                                           class="input_check" id="all-select">
                                    <label for="all-select"> </label>
                                </span>
                                </div>
                            </th>
                            <th class="select-all"><label for="all-select">全选</label></th>
                            <th class="th-information">商品信息</th>
                            <th>单价</th>
                            <th>数量</th>
                            <th>金额</th>
                            <th>操作</th>
                        </tr>
                        </thead>
                    </table>
                </div>
                <div>
                    <div class="product-information" v-for="(item,index) in productList">
                        <div class="freight clearfix">
                            <div class="name">
                                <div class="check-box select">
                                    <label>
                                        <!--店铺全选-->
                                        <input type="checkbox" class="input_check" v-model="item.selected"
                                               @change="changeTitleChecked(item)">
                                        <span></span>
                                    </label>
                                    <span class="shop">{{ item.name }}</span>
                                </div>
                            </div>
                            <span class="money">运费: {{ item.pay_transform }}</span>
                        </div>
                        <table width="100%">
                            <colgroup>
                                <col width="46px">
                                <col width="40px">
                                <col width="500px">
                                <col width="150px">
                                <col width="154px">
                                <col width="150px">
                                <col width="150px">
                            </colgroup>
                            <tbody>
                            <tr class="offer-tr" v-if="item.offer">
                                <td colspan="7" class="offer">
                                    <span>优惠</span>{{ item.offer }}
                                </td>
                            </tr>
                            <tr v-for="(product, num) in item.products">
                                <td class="td-select">
                                    <div class="check-box">
                                        <label>
                                            <!--商品选中-->
                                            <input type="checkbox"
                                                   class="input_check"
                                                   v-model='product.selected'
                                                   name='checkboxinput'
                                                   @change="selectProduct(item)">
                                            <span></span>
                                        </label>
                                    </div>
                                </td>
                                <td class="td-img">
                                    <router-link to="/mall/search/product-details">
                                        <img :src="product.img" alt="">
                                    </router-link>
                                </td>
                                <td class="td-information">
                                    <router-link to="/product-details"> {{ product.name }}</router-link>
                                    <p>尺码：{{ product.size }}</p>
                                    <p>
                                        <i class="mention" :class="{support:item.mention}">提</i>本商品
                                        <i v-if="item.mention">支持</i><i v-if="!item.mention">不支持</i>门店自提
                                    </p>
                                </td>
                                <td>
                                    <s>&yen;{{ product.old_price }}</s>
                                    <p>&yen;{{ product.now_price }}</p>
                                </td>
                                <td>
                                    <div class="num-input clearfix">
                                    <span class="num" @click="reduce(product)">
                                        -
                                    </span>
                                        <span class="input">
                                        <input type="number" readonly v-model.number="product.num">
                                    </span>
                                        <span class="num" @click="plus(product)">
                                        +
                                    </span>
                                    </div>
                                </td>
                                <td class="price">
                                    &yen;{{ price(product.num, product.now_price) }}
                                </td>
                                <td>
                                    <a @click="deleteProduct(item, num, index)">删除</a>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="product-information cart-pay">
                    <table width="100%">
                        <thead>
                        <tr>
                            <th class="select">
                                <div class="check-box select-all">
                                <span>
                                <!--全选-->
                                    <input type="checkbox" v-model="isAllChecked" @change="changeAllChecked($event)"
                                           class="input_check" id="all-select2">
                                    <label for="all-select2"> </label>
                                </span>
                                </div>
                            </th>
                            <th class="delete-product">
                                <label for="all-select2" class="select-delete-all">
                                    全选
                                </label>
                                <span @click="deleteSelected">删除选中的商品</span>
                            </th>
                            <th class="th-information"></th>
                            <th>已选商品 {{ selectNum }}</th>
                            <th class="mount">总价 (不含运费)</th>
                            <th class="num-price">&yen;{{ totalPrice }} <p>运费:{{ totalFreight }}</p></th>
                            <th>
                                <button class="order-btn" v-router-link="{ path: 'submit-order' }">
                                    去结算
                                </button>
                            </th>
                        </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
        <splin-line v-if="loading"></splin-line>
    </div>
</template>