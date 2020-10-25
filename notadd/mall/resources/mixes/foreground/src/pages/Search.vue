<script>
    import product from '../assets/images/thephone.png';
    import Paginate from '../components/Paginate.vue';
    import NeedBrowse from '../components/NeedBrowse.vue';
    import image1 from '../assets/images/head.png';

    export default {
        components: {
            NeedBrowse,
            Paginate,
        },
        data() {
            return {
                brand: '',
                classification: '',
                currect_page: 1,
                filterItems: [],
                filters: [
                    {
                        expand: false,
                        name: '品牌',
                        list: ['佳尔乐', 'HUGGIES/好奇', 'Pampers/帮宝适', 'HUGGIES/好奇',
                            'HUGGIES/好奇', 'Pampers/帮宝适', 'HUGGIES/好奇', 'Pampers/帮宝适', 'HUGGIES/好奇', 'Pampers/帮宝适'],
                    },
                    {
                        expand: false,
                        name: '详细分类',
                        list: ['纸尿裤', '拉拉裤', '尿片', '个人洗护'],
                    },
                    {
                        expand: false,
                        name: '尺寸规格',
                        list: ['M', 'L', 'XL', 'XXL'],
                    },
                    {
                        expand: false,
                        name: '产地',
                        list: ['中国', '日本', '美国', '英国'],
                    },
                ],
                contractPostage: false,
                isDiscount: true,
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
                productList: [
                    {
                        id: '',
                        img: product,
                        intro: 'Apple iPhone 6 32G 金色 移动联通电信4G手机',
                        hot: true,
                        old_price: 46.88,
                        price: 3299.00,
                        pre_price: 1.21,
                        saleNum: 3324,
                    },
                    {
                        id: '',
                        img: product,
                        hot: false,
                        intro: 'Apple iPhone 6 32G 金色 移动联通电信4G手机',
                        old_price: 46.88,
                        price: 3299.00,
                        pre_price: 1.21,
                        saleNum: 3324,
                    },
                    {
                        id: '',
                        img: product,
                        hot: false,
                        intro: 'Apple iPhone 6 32G 金色 移动联通电信4G手机',
                        old_price: 46.88,
                        price: 3299.00,
                        pre_price: 1.21,
                        saleNum: 3324,
                    },
                    {
                        id: '',
                        img: product,
                        hot: false,
                        intro: 'Apple iPhone 6 32G 金色 移动联通电信4G手机',
                        old_price: 46.88,
                        price: 3299.00,
                        pre_price: 1.21,
                        saleNum: 3324,
                    },
                    {
                        id: '',
                        img: product,
                        hot: false,
                        intro: 'Apple iPhone 6 32G 金色 移动联通电信4G手机',
                        old_price: 46.88,
                        price: 3299.00,
                        pre_price: 1.21,
                        saleNum: 3324,
                    },
                    {
                        id: '',
                        img: product,
                        hot: false,
                        intro: 'Apple iPhone 6 32G 金色 移动联通电信4G手机',
                        old_price: 46.88,
                        price: 3299.00,
                        pre_price: 1.21,
                        saleNum: 3324,
                    },
                    {
                        id: '',
                        img: product,
                        hot: false,
                        intro: 'Apple iPhone 6 32G 金色 移动联通电信4G手机',
                        old_price: 46.88,
                        price: 3299.00,
                        pre_price: 1.21,
                        saleNum: 3324,
                    },
                    {
                        id: '',
                        img: product,
                        hot: false,
                        intro: 'Apple iPhone 6 32G 金色 移动联通电信4G手机',
                        old_price: 46.88,
                        price: 3299.00,
                        pre_price: 1.21,
                        saleNum: 3324,
                    },
                    {
                        id: '',
                        img: product,
                        hot: false,
                        intro: 'Apple iPhone 6 32G 金色 移动联通电信4G手机',
                        old_price: 46.88,
                        price: 3299.00,
                        pre_price: 1.21,
                        saleNum: 3324,
                    },
                ],
                minPrice: 0,
                madeAddress: '',
                maxPrice: '',
                priceSort: true,
                size: '',
                sortBy: 1,
                total_page: 10,
            };
        },
        methods: {
            expand(index) {
                this.filters[index].expand = !this.filters[index].expand;
            },
            changeFilterItems(item, subItem, e) {
                window.console.log(e.target.checked);
                if (e.target.checked) {
                    this.filterItems.push(
                        {
                            name: item.name,
                            sub: subItem,
                        },
                    );
                }
//                window.console.log(this.filterItems);
            },
            nextPage() {
                if (this.currect_page < this.total_page) {
                    this.currect_page += 1;
                }
            },
            prevPage() {
                if (this.currect_page > 1) {
                    this.currect_page -= 1;
                }
            },
            sortPrice() {
                this.priceSort = !this.priceSort;
            },
            switchPage(page) {
                this.current_page = page;
            },
        },
        watch: {
            filterItems() {
                let searchStr = '';
                this.filterItems.forEach(item => {
                    searchStr = `${searchStr}&${item.name}=${item.sub}`;
                });
                this.$router.push(`${location.pathname}?${searchStr.substr(1)}`);
            },
        },
        beforeRouteUpdate() {
            window.console.log(location.search);
        },
    };
</script>
<template>
    <div class="search container padding-attribute">
        <p class="search-result">全部结果
            <i class="icon iconfont "> ></i>
            "尿不湿"&nbsp;&nbsp;&nbsp;&nbsp;
            <span v-for="item in filterItems">{{ item.name }}: <i>{{ item.sub }}</i><i class="icon iconfont icon-close"></i></span>
        </p>
        <div class="filter-box">
            <div class="row" v-for="(filterItem, filterIndex) in filters" :key="filterIndex">
                <div class="filter-item pull-left">{{ filterItem.name }}:</div>
                <div class="filter-content clearfix pull-left">
                    <ul class="pull-left" :class="{ expand: filterItem.expand }">
                        <li v-for="(item, index) in filterItem.list" :key="index">
                            <label>
                                <input type="checkbox" @change="changeFilterItems(filterItem, item, $event)" :value="item">
                                <a>{{ item }}</a>
                            </label>
                        </li>
                    </ul>
                    <span class="pull-right expand-btn" @click="expand(filterIndex)">
                        <b v-if="!filterItem.expand">展开</b>
                        <b v-if="filterItem.expand">收起</b>
                    </span>
                </div>
            </div>
        </div>
        <div class="container">
            <div class="arrangement-method clearfix">
                <ul class="clearfix pull-left">
                    <li class="active text-center pull-left">
                        <label>
                            <input type="radio" name="sortBy" value="1" v-model="sortBy">
                            <span>人气<i class="icon iconfont icon-paixu"></i></span>
                        </label>
                    </li>
                    <li class="active text-center pull-left">
                        <label>
                            <input type="radio" name="sortBy" value="2" v-model="sortBy">
                            <span>销量<i class="icon iconfont icon-paixu"></i></span>
                        </label>
                    </li>
                    <li class="active text-center pull-left">
                        <label>
                            <input type="radio" name="sortBy" value="3" v-model="sortBy">
                            <span @click.stop="sortPrice">价格<i class="icon iconfont icon-paixu" :class="{rotate: priceSort}"></i></span>
                        </label>
                    </li>
                    <li class="text-center pull-left">
                        <label>
                            <input type="radio" name="sortBy" value="4" v-model="sortBy">
                            <span>新品<i class="icon iconfont icon-paixu"></i></span>
                        </label>
                    </li>
                </ul>
                <div class="price">
                    <input type="number" min="0" placeholder="￥" v-model="minPrice">
                    -
                    <input type="number" :min="minPrice" placeholder="￥" v-model="maxPrice">
                </div>
                <div class="check-box select">
                    <label class="ivu-checkbox-wrapper ivu-checkbox-group-item">
                        <span class="ivu-checkbox">
                            <input
                                type="checkbox"
                                class="ivu-checkbox-input"
                                v-model="contractPostage"
                                value="remember">
                            <span class="ivu-checkbox-inner"></span>
                        </span>
                        <span>包邮</span>
                    </label>
                </div>
                <div class="check-box select">
                    <label class="ivu-checkbox-wrapper ivu-checkbox-group-item">
                        <span class="ivu-checkbox">
                            <input
                                type="checkbox"
                                class="ivu-checkbox-input"
                                v-model="isDiscount"
                                value="remember">
                            <span class="ivu-checkbox-inner"></span>
                        </span>
                        <span>折扣</span>
                    </label>
                </div>
                <span class="page pull-right">
                    {{ currect_page }}/{{ total_page }}
                    <i @click="prevPage" class="icon iconfont icon-gengduo page-pre" :class="{active: currect_page === 1}"> </i>
                    <i @click="nextPage" class="icon iconfont icon-gengduo page-pre" :class="{active: currect_page === total_page}"> </i>
                </span>
            </div>
        </div>
        <div class="search-list container">
            <div class="product" v-for="product in productList">
                <router-link to="/mall/search/product-details">
                    <img :src="product.img"/>
                    <p>
                        <em>
                            <b>￥</b>
                            {{ product.price }}
                        </em>
                        <s>
                            <b>￥</b>
                            {{ product.old_price }}
                        </s>
                        <span>{{ product.pre_price }}元/片</span>
                    </p>
                    <p class="product-intro">{{ product.intro }}</p>
                    <p class="sales">本月销量<span> {{ product.saleNum }} </span>件</p>
                </router-link>
            </div>
        </div>
        <div class="text-center" v-show="total_page > 1">
            <paginate
                :pageCount="total_page"
                :pageRange="3"
                :marginPages="2"
                :clickHandler="switchPage"
                prevText="上一页"
                nextText="下一页"
                containerClass="pagination no-margin"
                pageClass="page-item">
            </paginate>
        </div>
        <need-browse :commendList="commendList"></need-browse>
    </div>
</template>