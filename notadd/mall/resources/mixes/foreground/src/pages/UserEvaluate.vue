<script>
    import Paginate from '../components/Paginate.vue';
    import product from '../assets/images/phone.png';

    export default {
        components: {
            Paginate,
        },
        data() {
            return {
                current_page: 1,
                evaluations: [
                    {
                        createdAt: '2017-02-09',
                        orderNumber: '11052646854585',
                        img: product,
                        storeName: 'xxx母婴用品店',
                        productName: 'Ｐurrfect diary 咕噜日记1-7岁儿童可爱短袜5双装可爱短袜5双装',
                        size: 'L',
                        price: 39.9,
                        star: 2,
                        evaluation: `优秀的商品蛮好的黎明眼镜正品PRADA普拉达调养眼镜，不错不错很漂亮黎
                                    明眼镜正品PRADA普拉达调养眼镜，不错不错很漂亮优秀的商品蛮好的黎明
                                    眼镜正品PRADA普拉达调养眼镜，不错不错很漂亮。`,
                        evaluationImg: [product, product],
                    },
                ],
                pages: 3,
            };
        },
        methods: {
            switchPage(page) {
                this.current_page = page;
            },
        },
    };
</script>
<template>
    <div class="user-evaluation">
        <div class="address-title">
            <h4>交易评价</h4>
        </div>
        <ul>
            <li v-for="evaluation in evaluations">
                <div>
                    <span>{{ evaluation.createAt }}</span>
                    <span>订单号： {{ evaluation.orderNumber }}</span>
                    <span>{{ evaluation.storeName }}</span>
                </div>
                <div class="clearfix">
                    <div class="pull-left clearfix">
                        <img :src="evaluation.img" alt="" class="pull-left">
                        <div class="pull-left">
                            <p>{{ evaluation.productName }}
                                  <span>尺码: {{ evaluation.size }}</span>
                            </p>
                            <p>￥ {{ evaluation.price }}</p>
                        </div>
                    </div>
                    <div class="pull-right">
                        <div class="clearfix">
                            <span class="pull-left">商品评分</span>
                            <p class="pull-left">
                                <i class="icon iconfont icon-xing"
                                   :class="{ 'star': index < evaluation.star }"
                                   v-for="(item, index) in 5" :key="index">
                                </i>
                            </p>
                        </div>
                        <div class="clearfix">
                            <span class="pull-left">评价晒单</span>
                            <div class="pull-left">
                                <p>{{ evaluation.evaluation }}</p>
                                <div>
                                    <img :src="item" alt=""
                                         v-for="(item,index) in evaluation.evaluationImg" :key="index">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </li>
        </ul>
        <div class="text-right hidden-xs" v-show="pages > 1">
            <paginate
                :pageCount="pages"
                :pageRange="3"
                :marginPages="2"
                :clickHandler="switchPage"
                prevText="上一页"
                nextText="下一页"
                containerClass="pagination no-margin"
                pageClass="page-item">
            </paginate>
        </div>
    </div>
</template>