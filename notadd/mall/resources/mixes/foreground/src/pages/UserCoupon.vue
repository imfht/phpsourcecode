<script>
    export default {
        data() {
            return {
                activeNav: 0,
                coupons: [
                    {
                        endTime: '2017.12.1',
                        money: 50.00,
                        other: '无',
                        startTime: '5016.12.12',
                        type: '户外运动',
                        use: '满399元可用',
                    },
                    {
                        endTime: '2017.12.1',
                        money: 50.00,
                        other: '无',
                        startTime: '5016.12.12',
                        type: '户外运动',
                        use: '满399元可用',
                    },
                ],
                navs: ['可使用', '已使用', '已失效'],
                used: true,
            };
        },
        methods: {
            tabSwitch(index) {
                this.activeNav = index;
            },
        },
    };
</script>
<template>
    <div class="coupon">
        <h4>我的优惠券</h4>
        <div class="exchange clearfix">
            <span class="pull-left">输入兑换码:</span>
            <input class="form-control exchange-code pull-left" type="text">
            <button class="exchange-btn order-btn pull-left">兑换</button>
        </div>
        <div class="clearfix nav-box">
            <ul id="myTab" class="tab-nav clearfix">
                <li :class="{active:index===activeNav}" v-for="(nav,index) in navs" @click="tabSwitch(index)">
                    <a>
                        {{ nav }}{{ index }}
                        <span class="product-num"></span>
                    </a>
                    <i></i>
                </li>
            </ul>
        </div>
        <div id="myTabContent" class="tab-content">
            <ul class="tab-pane fade in active clearfix" :class="{used:activeNav !== 0}" >
                <li class="pull-left pane pull-left" v-for="coupon in coupons">
                    <div class="coupons">
                        <p class="text-center"><span class="symbol">￥</span>{{ coupon.money }}</p>
                        <p class="text-center">{{ coupon.use }}</p>
                    </div>
                    <div class="coupons-info text-center">
                        <ul class="text-left">
                            <li>品类限制：{{ coupon.type }}</li>
                            <li>使用时间：{{ coupon.startTime }}-{{ coupon.endTime }}</li>
                            <li>其他说明：{{ coupon.other }}</li>
                        </ul>
                        <router-link to="/mall/search/product-details" class="use">立即使用</router-link>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</template>