<template>
    <div class="card" v-if="articles.length">
        <div class="card-header">可能还想看</div>
        <div class="row card-body">
            <a  v-for="article in articles" :key="article.id" :href="article.href" class="col-md-4">
                <figure class="figure">
                    <img v-lazy="article.cover" class="figure-img img-fluid rounded" :alt="article.title">
                    <figcaption class="figure-caption">
                        <h6 class="t-line-ellipsis-1">{{ article.title }}</h6>
                    </figcaption>
                </figure>
            </a>
        </div>
    </div>
</template>

<script>
import Vue from 'vue';
import VueLazyload from 'vue-lazyload';

Vue.use(VueLazyload);

export default {
    data: function() {
        return {
            articles: []
        };
    },
    created: async function() {
        let articleId = this.getArticleId();
        let response = await axios.get(`/api/articles/list/${articleId}`);

        if (response && response.status === 200) {
            this.articles = response.data;
        }
    },
    methods: {
        getArticleId: function() {
            let articleId = -1;

            try {
                articleId = /\/articles\/show\/(\d+).html/.exec(location.href)[1];
            } catch (error) {
                console.error('not get articleId');
            }

            return articleId;
        }
    }
};

</script>
<style lang="scss">
.card {
    margin-bottom: 20px;

    .figure {
        width: 100%;

        img {
            width: 100%;
        }
    }
}
</style>
