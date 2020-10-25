<script>
    import img from '../assets/images/img_02-3.png';

    export default{
        data() {
            return {
                type: true,
                goods: [
                    {
                        refundnum: '11052646854585',
                        time: '2017-5-30-13:21:54',
                        seller: 'xxx旗舰店',
                        imgsrc: img,
                        name: 'Ｐurrfect diary 咕噜日记1-7岁儿童可爱短袜5双装岁儿童可爱短袜5双装',
                        num: '5546459835659646',
                        price: '3300.00',
                        status: 1,
                    },
                    {
                        refundnum: '11052646854585',
                        time: '2017-5-30-13:21:54',
                        seller: 'xxx旗舰店',
                        imgsrc: img,
                        name: 'Ｐurrfect diary 咕噜日记1-7岁儿童可爱短袜5双装岁儿童可爱短袜5双装',
                        num: '5546459835659646',
                        price: '3300.00',
                        status: 1,
                    },
                    {
                        refundnum: '11052646854585',
                        time: '2017-5-30-13:21:54',
                        seller: 'xxx旗舰店',
                        imgsrc: img,
                        name: 'Ｐurrfect diary 咕噜日记1-7岁儿童可爱短袜5双装岁儿童可爱短袜5双装',
                        num: '5546459835659646',
                        price: '3300.00',
                        status: 1,
                    },
                    {
                        refundnum: '11052646854585',
                        time: '2017-5-30-13:21:54',
                        seller: 'xxx旗舰店',
                        imgsrc: img,
                        name: 'Ｐurrfect diary 咕噜日记1-7岁儿童可爱短袜5双装岁儿童可爱短袜5双装',
                        num: '5546459835659646',
                        price: '3300.00',
                        status: 1,
                    },
                ],
            };
        },
        methods: {
            qiehuan() {
                this.type = !this.type;
            },
        },
    };
</script>
<template>
    <div class="selases-return">
        <div class="saases-title">
            <span @click="qiehuan" :class="{xuanzhong: !type}">退款申请</span>
            <span @click="qiehuan" :class="{xuanzhong: type}">退货申请</span>
        </div>
        <div class="selaes-serch">
            <input class="form-control" type="text" placeholder="">
            <span>搜索</span>
        </div>
        <div class="clearfix"></div>
        <div>
            <div class="goods-list-title">
                <div>商品</div>
                <div>退款金额（元）</div>
                <div>退货数量</div>
                <div>审核状态</div>
                <div>平台确认</div>
                <div>操作</div>
            </div>
            <div class="goods-list" v-for="item in goods">
                <p>
                    <span>退款编号：{{ item.refundnum }}</span>
                    <span>申请时间：{{ item.time }}</span>
                    <span>{{ item.seller }}</span>
                </p>
                <div class="goods-list-title">
                    <div>
                        <div class="goods-img">
                            <img :src="item.imgsrc" alt="">
                        </div>
                        <div class="goods-msg">
                            <div class="goods-msg-main">{{ item.name }}</div>
                            <div class="diangdanbianhao">订单编号：{{ item.num }}</div>
                        </div>
                    </div>
                    <div>{{ item.price }}</div>
                    <div>1</div>
                    <div>待审核</div>
                    <div>无</div>
                    <div><router-link class="caozuo" to="/mall/refund">
                        <span>查看</span>
                    </router-link></div>

                </div>
            </div>
        </div>
    </div>
</template>