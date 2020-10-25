<template>
    <div class="menu">
        <ul class="menu-bar">
            <li v-for="menu in menus">
                <i class="icon iconfont icon-nvzhuang" :class="menu.icon"></i>
                <span>{{ menu.name }}</span>
                <i class="icon iconfont icon-gengduo triangle"></i>
                <div class="menu-content">
                    <div class="clearfix menu-box pull-left">
                        <ul class="recommend clearfix">
                            <li v-for="goods in menu.recommend">
                                <router-link to="/mall/search" target="_blank">{{ goods.name }}</router-link>
                            </li>
                        </ul>
                        <div class="content-list" v-for="item in menu.secondMenu">
                            <h5>{{ item.name }}</h5>
                            <ul class="row">
                                <li v-for="product in item.products">
                                    <router-link to="/mall/search" target="_blank">{{ product.product }}</router-link>
                                    <span>|</span></li>
                            </ul>
                        </div>
                    </div>
                    <div class="advertising pull-left">
                        <ul class="clearfix">
                            <li class="pull-left" v-for="adv in menu.advertising.smallAdv">
                                <router-link to="/mall/search" target="_blank">
                                    <img :src="adv.img" alt="">
                                </router-link>
                            </li>
                        </ul>
                        <div class="bigAdv" v-for="bigAdv in menu.advertising.bigAdvs">
                            <router-link to="/mall/search" target="_blank">
                                <img :src="bigAdv.img" alt="">
                            </router-link>
                        </div>
                    </div>
                </div>
            </li>
        </ul>
    </div>
</template>
<script>
    import huwei from '../assets/images/huawei.png';
    import adv from '../assets/images/banner_01.png';

    export default {
        data() {
            return {
                menus: [
                    {
                        advertising: {
                            smallAdv: [
                                {
                                    img: huwei,
                                },
                                {
                                    img: huwei,
                                },
                                {
                                    img: huwei,
                                },
                                {
                                    img: huwei,
                                },
                                {
                                    img: huwei,
                                },
                                {
                                    img: huwei,
                                },
                                {
                                    img: huwei,
                                },
                                {
                                    img: huwei,
                                },
                            ],
                            bigAdvs: [
                                {
                                    img: adv,
                                },
                                {
                                    img: adv,
                                },
                            ],
                        },
                        name: '女装外套',
                        icon: 'icon-nvzhuang',
                        recommend: [
                            {
                                id: 1,
                                name: '护发精油',
                            },
                            {
                                id: 1,
                                name: '护发精油',
                            },
                            {
                                id: 1,
                                name: '护发精油',
                            },
                            {
                                id: 1,
                                name: '护发精油',
                            },
                            {
                                id: 1,
                                name: '护发精油',
                            },
                            {
                                id: 1,
                                name: '护发精油',
                            },
                            {
                                id: 1,
                                name: '护发精油',
                            },
                            {
                                id: 1,
                                name: '护发精油',
                            },
                        ],
                        secondMenu: [
                            {
                                name: '洗护日用1',
                                products: [
                                    {
                                        id: 123,
                                        product: '洗发水',
                                    },
                                    {
                                        id: 123,
                                        product: '护发素',
                                    },
                                    {
                                        id: 123,
                                        product: '发膜',
                                    },
                                    {
                                        id: 123,
                                        product: '染发剂',
                                    },
                                    {
                                        id: 123,
                                        product: '护法精油',
                                    },
                                    {
                                        id: 123,
                                        product: '梳子',
                                    },
                                    {
                                        id: 123,
                                        product: '洗发水',
                                    },
                                    {
                                        id: 123,
                                        product: '护发素',
                                    },
                                    {
                                        id: 123,
                                        product: '发膜',
                                    },
                                    {
                                        id: 123,
                                        product: '染发剂',
                                    },
                                    {
                                        id: 123,
                                        product: '护法精油',
                                    },
                                    {
                                        id: 123,
                                        product: '梳子',
                                    },
                                    {
                                        id: 123,
                                        product: '洗发水',
                                    },
                                    {
                                        id: 123,
                                        product: '护发素',
                                    },
                                    {
                                        id: 123,
                                        product: '发膜',
                                    },
                                    {
                                        id: 123,
                                        product: '染发剂',
                                    },
                                    {
                                        id: 123,
                                        product: '护法精油',
                                    },
                                    {
                                        id: 123,
                                        product: '梳子',
                                    },
                                    {
                                        id: 123,
                                        product: '沐浴露',
                                    },
                                ],
                            },
                            {
                                name: '洗护日用',
                                products: [
                                    {
                                        id: 123,
                                        product: '洗发水',
                                    },
                                    {
                                        id: 123,
                                        product: '护发素',
                                    },
                                    {
                                        id: 123,
                                        product: '发膜',
                                    },
                                    {
                                        id: 123,
                                        product: '染发剂',
                                    },
                                    {
                                        id: 123,
                                        product: '护法精油',
                                    },
                                    {
                                        id: 123,
                                        product: '梳子',
                                    },
                                    {
                                        id: 123,
                                        product: '沐浴露',
                                    },
                                ],
                            },
                            {
                                name: '洗护日用',
                                products: [
                                    {
                                        id: 123,
                                        product: '洗发水',
                                    },
                                    {
                                        id: 123,
                                        product: '护发素',
                                    },
                                    {
                                        id: 123,
                                        product: '发膜',
                                    },
                                    {
                                        id: 123,
                                        product: '染发剂',
                                    },
                                    {
                                        id: 123,
                                        product: '护法精油',
                                    },
                                    {
                                        id: 123,
                                        product: '梳子',
                                    },
                                    {
                                        id: 123,
                                        product: '沐浴露',
                                    },
                                ],
                            },
                            {
                                name: '洗护日用',
                                products: [
                                    {
                                        id: 123,
                                        product: '洗发水',
                                    },
                                    {
                                        id: 123,
                                        product: '护发素',
                                    },
                                    {
                                        id: 123,
                                        product: '发膜',
                                    },
                                    {
                                        id: 123,
                                        product: '染发剂',
                                    },
                                    {
                                        id: 123,
                                        product: '护法精油',
                                    },
                                    {
                                        id: 123,
                                        product: '梳子',
                                    },
                                    {
                                        id: 123,
                                        product: '沐浴露',
                                    },
                                ],
                            },
                        ],
                    },
                    {
                        advertising: {
                            smallAdv: [
                                {
                                    img: adv,
                                },
                                {
                                    img: adv,
                                },
                                {
                                    img: adv,
                                },
                                {
                                    img: adv,
                                },
                                {
                                    img: adv,
                                },
                                {
                                    img: adv,
                                },
                                {
                                    img: adv,
                                },
                                {
                                    img: adv,
                                },
                            ],
                            bigAdvs: [
                                {
                                    img: adv,
                                },
                                {
                                    img: adv,
                                },
                            ],
                        },
                        name: '女装外套',
                        icon: 'icon-nvzhuang',
                        recommend: [
                            {
                                id: 1,
                                name: '护发精油',
                            },
                            {
                                id: 1,
                                name: '护发精油',
                            },
                            {
                                id: 1,
                                name: '护发精油',
                            },
                            {
                                id: 1,
                                name: '护发精油',
                            },
                            {
                                id: 1,
                                name: '护发精油',
                            },
                            {
                                id: 1,
                                name: '护发精油',
                            },
                            {
                                id: 1,
                                name: '护发精油',
                            },
                            {
                                id: 1,
                                name: '护发精油',
                            },
                        ],
                        secondMenu: [
                            {
                                name: '洗护日用1',
                                products: [
                                    {
                                        id: 123,
                                        product: '洗发水',
                                    },
                                    {
                                        id: 123,
                                        product: '护发素',
                                    },
                                    {
                                        id: 123,
                                        product: '发膜',
                                    },
                                    {
                                        id: 123,
                                        product: '染发剂',
                                    },
                                    {
                                        id: 123,
                                        product: '护法精油',
                                    },
                                    {
                                        id: 123,
                                        product: '梳子',
                                    },
                                    {
                                        id: 123,
                                        product: '洗发水',
                                    },
                                    {
                                        id: 123,
                                        product: '护发素',
                                    },
                                    {
                                        id: 123,
                                        product: '发膜',
                                    },
                                    {
                                        id: 123,
                                        product: '染发剂',
                                    },
                                    {
                                        id: 123,
                                        product: '护法精油',
                                    },
                                    {
                                        id: 123,
                                        product: '梳子',
                                    },
                                    {
                                        id: 123,
                                        product: '洗发水',
                                    },
                                    {
                                        id: 123,
                                        product: '护发素',
                                    },
                                    {
                                        id: 123,
                                        product: '发膜',
                                    },
                                    {
                                        id: 123,
                                        product: '染发剂',
                                    },
                                    {
                                        id: 123,
                                        product: '护法精油',
                                    },
                                    {
                                        id: 123,
                                        product: '梳子',
                                    },
                                    {
                                        id: 123,
                                        product: '沐浴露',
                                    },
                                ],
                            },
                            {
                                name: '洗护日用',
                                products: [
                                    {
                                        id: 123,
                                        product: '洗发水',
                                    },
                                    {
                                        id: 123,
                                        product: '护发素',
                                    },
                                    {
                                        id: 123,
                                        product: '发膜',
                                    },
                                    {
                                        id: 123,
                                        product: '染发剂',
                                    },
                                    {
                                        id: 123,
                                        product: '护法精油',
                                    },
                                    {
                                        id: 123,
                                        product: '梳子',
                                    },
                                    {
                                        id: 123,
                                        product: '沐浴露',
                                    },
                                ],
                            },
                            {
                                name: '洗护日用',
                                products: [
                                    {
                                        id: 123,
                                        product: '洗发水',
                                    },
                                    {
                                        id: 123,
                                        product: '护发素',
                                    },
                                    {
                                        id: 123,
                                        product: '发膜',
                                    },
                                    {
                                        id: 123,
                                        product: '染发剂',
                                    },
                                    {
                                        id: 123,
                                        product: '护法精油',
                                    },
                                    {
                                        id: 123,
                                        product: '梳子',
                                    },
                                    {
                                        id: 123,
                                        product: '沐浴露',
                                    },
                                ],
                            },
                            {
                                name: '洗护日用',
                                products: [
                                    {
                                        id: 123,
                                        product: '洗发水',
                                    },
                                    {
                                        id: 123,
                                        product: '护发素',
                                    },
                                    {
                                        id: 123,
                                        product: '发膜',
                                    },
                                    {
                                        id: 123,
                                        product: '染发剂',
                                    },
                                    {
                                        id: 123,
                                        product: '护法精油',
                                    },
                                    {
                                        id: 123,
                                        product: '梳子',
                                    },
                                    {
                                        id: 123,
                                        product: '沐浴露',
                                    },
                                ],
                            },
                        ],
                    },
                    {
                        advertising: {
                            smallAdv: [
                                {
                                    img: adv,
                                },
                                {
                                    img: adv,
                                },
                                {
                                    img: adv,
                                },
                                {
                                    img: adv,
                                },
                                {
                                    img: adv,
                                },
                                {
                                    img: adv,
                                },
                                {
                                    img: adv,
                                },
                                {
                                    img: adv,
                                },
                            ],
                            bigAdvs: [
                                {
                                    img: adv,
                                },
                                {
                                    img: adv,
                                },
                            ],
                        },
                        name: '女装外套',
                        icon: 'icon-nvzhuang',
                        recommend: [
                            {
                                id: 1,
                                name: '护发精油',
                            },
                            {
                                id: 1,
                                name: '护发精油',
                            },
                            {
                                id: 1,
                                name: '护发精油',
                            },
                            {
                                id: 1,
                                name: '护发精油',
                            },
                            {
                                id: 1,
                                name: '护发精油',
                            },
                            {
                                id: 1,
                                name: '护发精油',
                            },
                            {
                                id: 1,
                                name: '护发精油',
                            },
                            {
                                id: 1,
                                name: '护发精油',
                            },
                        ],
                        secondMenu: [
                            {
                                name: '洗护日用1',
                                products: [
                                    {
                                        id: 123,
                                        product: '洗发水',
                                    },
                                    {
                                        id: 123,
                                        product: '护发素',
                                    },
                                    {
                                        id: 123,
                                        product: '发膜',
                                    },
                                    {
                                        id: 123,
                                        product: '染发剂',
                                    },
                                    {
                                        id: 123,
                                        product: '护法精油',
                                    },
                                    {
                                        id: 123,
                                        product: '梳子',
                                    },
                                    {
                                        id: 123,
                                        product: '洗发水',
                                    },
                                    {
                                        id: 123,
                                        product: '护发素',
                                    },
                                    {
                                        id: 123,
                                        product: '发膜',
                                    },
                                    {
                                        id: 123,
                                        product: '染发剂',
                                    },
                                    {
                                        id: 123,
                                        product: '护法精油',
                                    },
                                    {
                                        id: 123,
                                        product: '梳子',
                                    },
                                    {
                                        id: 123,
                                        product: '洗发水',
                                    },
                                    {
                                        id: 123,
                                        product: '护发素',
                                    },
                                    {
                                        id: 123,
                                        product: '发膜',
                                    },
                                    {
                                        id: 123,
                                        product: '染发剂',
                                    },
                                    {
                                        id: 123,
                                        product: '护法精油',
                                    },
                                    {
                                        id: 123,
                                        product: '梳子',
                                    },
                                    {
                                        id: 123,
                                        product: '沐浴露',
                                    },
                                ],
                            },
                            {
                                name: '洗护日用',
                                products: [
                                    {
                                        id: 123,
                                        product: '洗发水',
                                    },
                                    {
                                        id: 123,
                                        product: '护发素',
                                    },
                                    {
                                        id: 123,
                                        product: '发膜',
                                    },
                                    {
                                        id: 123,
                                        product: '染发剂',
                                    },
                                    {
                                        id: 123,
                                        product: '护法精油',
                                    },
                                    {
                                        id: 123,
                                        product: '梳子',
                                    },
                                    {
                                        id: 123,
                                        product: '沐浴露',
                                    },
                                ],
                            },
                            {
                                name: '洗护日用',
                                products: [
                                    {
                                        id: 123,
                                        product: '洗发水',
                                    },
                                    {
                                        id: 123,
                                        product: '护发素',
                                    },
                                    {
                                        id: 123,
                                        product: '发膜',
                                    },
                                    {
                                        id: 123,
                                        product: '染发剂',
                                    },
                                    {
                                        id: 123,
                                        product: '护法精油',
                                    },
                                    {
                                        id: 123,
                                        product: '梳子',
                                    },
                                    {
                                        id: 123,
                                        product: '沐浴露',
                                    },
                                ],
                            },
                            {
                                name: '洗护日用',
                                products: [
                                    {
                                        id: 123,
                                        product: '洗发水',
                                    },
                                    {
                                        id: 123,
                                        product: '护发素',
                                    },
                                    {
                                        id: 123,
                                        product: '发膜',
                                    },
                                    {
                                        id: 123,
                                        product: '染发剂',
                                    },
                                    {
                                        id: 123,
                                        product: '护法精油',
                                    },
                                    {
                                        id: 123,
                                        product: '梳子',
                                    },
                                    {
                                        id: 123,
                                        product: '沐浴露',
                                    },
                                ],
                            },
                        ],
                    },
                ],
            };
        },
    };
</script>
