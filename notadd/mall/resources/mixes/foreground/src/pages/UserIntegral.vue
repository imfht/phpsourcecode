<script>
    import image1 from '../assets/images/head.png';

    export default {
        data() {
            return {
                commendList: [
                    {
                        img: image1,
                        name: '西部母婴推荐哆啦A梦可爱儿童玩具',
                        price: 48.88,
                    },
                    {
                        img: image1,
                        name: '西部母婴推荐哆啦A梦可爱儿童玩具',
                        price: 48.88,
                    },
                    {
                        img: image1,
                        name: '西部母婴推荐哆啦A梦可爱儿童玩具',
                        price: 48.88,
                    },
                    {
                        img: image1,
                        name: '西部母婴推荐哆啦A梦可爱儿童玩具',
                        price: 48.88,
                    },
                ],
                integrals: [
                    {
                        time: '2016-12-24 13:17:40',
                        recording: '+60',
                        detail: '购物赠送积分',
                    },
                    {
                        time: '2016-12-24 13:17:40',
                        recording: '+60',
                        detail: '购物赠送积分',
                    },
                    {
                        time: '2016-12-24 13:17:40',
                        recording: '+60',
                        detail: '购物赠送积分',
                    },
                    {
                        time: '2016-12-24 13:17:40',
                        recording: '+60',
                        detail: '购物赠送积分',
                    },
                ],
                totalIntegrals: '114',
            };
        },
    };
</script>
<template>
    <div class="my-integral">
        <h4>我的积分</h4>
        <div class="integral-count">积分 <span>{{ totalIntegrals }}</span></div>
        <h4>推荐兑换</h4>
        <div class="recommend-model">
            <div class="browse-content">
                <dl class="col col-line" v-for="item in commendList">
                    <router-link to="/mall/search/product-details">
                        <dt><img :src="item.img"></dt>
                        <dd>
                            <p class="name">{{ item.name }}</p>
                            <p class="new-price">￥{{ item.price }}</p>
                        </dd>
                    </router-link>
                </dl>
            </div>
        </div>
        <h4>积分明细</h4>
        <div class="integral-details">
            <table width="100%">
                <colgroup>
                    <col width="240px">
                    <col width="264px">
                    <col width="503px">
                </colgroup>
                <thead>
                <tr>
                    <th>日期</th>
                    <th>收入/支出</th>
                    <th>详细说明</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="integral in integrals">
                    <td>{{ integral.time }}</td>
                    <td>{{ integral.recording }}</td>
                    <td>{{ integral.detail }}</td>
                </tr>
                </tbody>
            </table>
            <div class="page text-right">分页</div>
        </div>
        <div class="integral-use">
            <p>1.积分的有效期</p>
            <p>积分的有效期最长2年，最短1年，即从获得积分开始至次年年底，逾期自动作废</p>
            <p>2.积分的兑换比例</p>
            <p>积分和人民币兑换比例是100:1，即100个积分相当于人民币1元。</p>
            <p>3.积分如何获取</p>
            <p>用户在积分进行购物、商品评价、晒单、交易满意度、移动端下单、指定区域在线支付等活动都可以获得积分。具体获取积分规则详见帮助中心。</p>
        </div>
    </div>
</template>