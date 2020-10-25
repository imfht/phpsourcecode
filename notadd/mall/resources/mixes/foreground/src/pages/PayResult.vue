<script>
    import FooterBar from '../layouts/FooterBar.vue';
    import FooterContent from '../layouts/FooterContent.vue';
    import HeaderBar from '../layouts/HeaderBar.vue';
    import LogoModal from '../layouts/LogoModal.vue';
    import NeedBrowse from '../components/NeedBrowse.vue';
    import SplinLine from '../components/SplinLine.vue';
    import image1 from '../assets/images/head.png';


    export default {
        components: {
            FooterBar,
            FooterContent,
            HeaderBar,
            LogoModal,
            NeedBrowse,
            SplinLine,
        },
        date() {
            return {
                commendList: [
                    {
                        amount: 188,
                        img: image1,
                        name: '西部母婴推荐哆啦A梦可爱儿童玩具',
                        price: 48.88,
                    },
                    {
                        amount: 188,
                        img: image1,
                        name: '西部母婴推荐哆啦A梦可爱儿童玩具',
                        price: 48.88,
                    },
                    {
                        amount: 188,
                        img: image1,
                        name: '西部母婴推荐哆啦A梦可爱儿童玩具',
                        price: 48.88,
                    },
                    {
                        amount: 188,
                        img: image1,
                        name: '西部母婴推荐哆啦A梦可爱儿童玩具',
                        price: 48.88,
                    },
                    {
                        amount: 188,
                        img: image1,
                        name: '西部母婴推荐哆啦A梦可爱儿童玩具',
                        price: 48.88,
                    },
                ],
                product: {
                    num: 1,
                    oldPrice: 128.33,
                    price: 38.99,
                    title: 'Purrfect diary 咕噜日记1-7岁儿童可爱短袜5双装儿童可爱短袜5双装儿童可爱短袜5双装',
                },
            };
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
    <div class="pay-result">
        <header-bar></header-bar>
        <logo-modal></logo-modal>
        <splin-line v-if="loading"></splin-line>
        <div v-if="!loading" class="pay-success padding-attribute">
            <div class="container">
                <div class="pay-success-content">
                    <div class="submit-title text-center"> 付款成功！我们将尽快安排发货！</div>
                    <!--<div class="submit-title text-center">付款失败！</div>-->
                    <div class="product-information">
                        <table width="100%">
                            <thead>
                            <tr>
                                <th class="name">商品信息</th>
                                <th>单价</th>
                                <th>数量</th>
                                <th>金额</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td>{{ product.title }}</td>
                                <td><s>&yen;{{ product.oldPrice }}</s>
                                    <p>&yen;{{ product.price }}</p></td>
                                <td>{{ product.num }}</td>
                                <td class="price">&yen;{{ product.price * product.num }}</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <!--<button class="order-btn submit-btn">查看订单</button>-->
                    <router-link to="/mall/payment-success" class="order-btn submit-btn">查看订单</router-link>
                </div>
                <need-browse :commendList="commendList"></need-browse>
            </div>
        </div>
        <footer-content></footer-content>
        <footer-bar></footer-bar>
    </div>
</template>