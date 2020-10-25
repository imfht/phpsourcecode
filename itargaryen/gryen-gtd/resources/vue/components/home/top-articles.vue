<template>
    <div class="t-index-top-article">
        <div class="list-group flex-column align-items-start">
            <a v-for="article in articles" :key="article.id" :href="article.href"
               class="list-group-item list-group-item-action" :title="article.title">
                <div class="d-flex w-100 justify-content-between">
                    <h6>{{ article.title }}</h6>
                </div>
                <small class="text-muted float-right">{{ article.publishedAt }}</small>
            </a>
        </div>
        <div class="t-index-showmore">
            <a href="/articles">查看更多</a>
        </div>
    </div>
</template>
<style lang="scss" scoped>
    .t-index-top-article {
        margin-top: 1rem;

        .list-group-item-action {
            border-top-width: 1px;
            border-color: #fff;

            &:hover,
            &:focus {
                background-color: #fff;
                border-color: #8e354a;
            }
        }

        .t-index-showmore {
            margin: 1rem auto;
            text-align: center;
        }
    }
</style>
<script>
    export default {
        data: function () {
            return {
                articles: []
            };
        },
        created: async function () {
            let response = await axios.get(`/api/articles/list/top`);

            if (response && response.status === 200) {
                this.articles = response.data;
            }
        }
    }
</script>
