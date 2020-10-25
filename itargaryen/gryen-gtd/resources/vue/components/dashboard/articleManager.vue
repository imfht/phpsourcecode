<template>
  <div>
    <div class="d-flex justify-content-between mb-3">
      <div class="btn-group" role="group">
        <button
          type="button"
          class="btn btn-primary"
          @click="filter('status', 0)"
          :disabled="listQueryParams.status === 0 && listQueryParams.onlyTrashed !== 'yes'"
        >待发布</button>
        <button
          type="button"
          class="btn btn-success"
          @click="filter('status', 1)"
          :disabled="listQueryParams.status === 1 && listQueryParams.onlyTrashed !== 'yes'"
        >已发布</button>
        <button
          type="button"
          class="btn btn-secondary"
          @click="filter('onlyTrashed', 'yes')"
          :disabled="listQueryParams.onlyTrashed === 'yes'"
        >已删除</button>
      </div>
      <div class="btn-group" role="group">
        <button
          type="button"
          class="btn btn-outline-primary"
          :class="{'active': listQueryParams.sorter.indexOf('published_at') > -1}"
          @click="order('published_at')"
        >发布时间</button>
        <button
          type="button"
          class="btn btn-outline-primary"
          :class="{'active': listQueryParams.sorter.indexOf('created_at') > -1}"
          @click="order('created_at')"
        >新建时间</button>
        <button
          type="button"
          class="btn btn-outline-primary"
          :class="{'active': listQueryParams.sorter.indexOf('updated_at') > -1}"
          @click="order('updated_at')"
        >更新时间</button>
        <button
          type="button"
          class="btn btn-outline-primary"
          :class="{'active': listQueryParams.sorter.indexOf('views') > -1}"
          @click="order('views')"
        >浏览量</button>
      </div>
    </div>
    <ul class="list-group">
      <li class="list-group-item disabled">
        <div class="row">
          <div class="col-sm-1">ID</div>
          <div class="col-sm-3 text-truncate">文章</div>
          <div class="col-sm-2 text-truncate">
            <span>发布时间</span>
          </div>
          <div class="col-sm-2 text-truncate">
            <span>新建时间</span>
          </div>
          <div class="col-sm-2 text-truncate">
            <span>更新时间</span>
          </div>
          <div class="col-sm-1 text-truncate">
            <span>更新次数</span>
          </div>
          <div class="col-sm-1 text-right text-truncate">
            <span>浏览量</span>
          </div>
        </div>
      </li>
      <li
        v-for="article in articleRes.data"
        :key="article.id"
        class="list-group-item list-group-item-action"
      >
        <div class="row">
          <div class="col-sm-1">{{article.id}}</div>
          <h5 class="col-sm-3 text-truncate" :title="article.title">
            <a :href="article.href" target="_blank">{{article.title}}</a>
          </h5>
          <div class="col-sm-2 text-truncate" :title="article.publishedAt">
            <span>{{article.publishedAt}}</span>
          </div>
          <div class="col-sm-2 text-truncate" :title="article.createdAt">
            <span>{{article.createdAt}}</span>
          </div>
          <div class="col-sm-2 text-truncate" :title="article.updatedAt">
            <span>{{article.updatedAt}}</span>
          </div>
          <div class="col-sm-1 text-center" :title="article.updatedAt">
            <span>{{article.modified_times}}</span>
          </div>
          <div class="col-sm-1 text-right text-truncate">
            <span class="badge badge-primary badge-pill">{{article.views}}</span>
          </div>
        </div>
      </li>
    </ul>
    <nav aria-label="Page navigation" class="g-pagination">
      <ul class="pagination">
        <li class="page-item" :class="{'disabled': articleRes.current_page === 1}">
          <span v-if="articleRes.current_page === 1" class="page-link">第一页</span>
          <a v-else class="page-link" @click="firstPage">第一页</a>
        </li>
        <li class="page-item" :class="{'disabled': !articleRes.prev_page_url}">
          <a v-if="articleRes.prev_page_url" class="page-link" @click="prevPage">上一页</a>
          <span v-else class="page-link">上一页</span>
        </li>
        <li class="page-item" :class="{'disabled': !articleRes.next_page_url}">
          <a v-if="articleRes.next_page_url" class="page-link" @click="nextPage">下一页</a>
          <span v-else class="page-link">下一页</span>
        </li>
        <li
          class="page-item"
          :class="{'disabled': articleRes.current_page === articleRes.last_page}"
        >
          <span v-if="articleRes.current_page === articleRes.last_page" class="page-link">最后一页</span>
          <a v-else class="page-link" @click="lastPage">最后一页</a>
        </li>
      </ul>
    </nav>
  </div>
</template>
<style lang="scss" scoped>
.g-pagination {
  position: absolute;
  bottom: 1rem;
  left: 15px;
}
</style>
<script>
export default {
  data: function() {
    return {
      listQueryParams: {
        pageSize: 15,
        page: 1,
        sorter: 'published_at_desc',
        status: 0,
        onlyTrashed: 'no'
      },
      articleRes: {}
    };
  },
  created: async function() {
    this.renderArticleList();
  },
  methods: {
    renderArticleList: async function() {
      const res = await axios.get('/api/articles/list', {
        params: {
          ...this.listQueryParams
        }
      });

      this.articleRes = res.data;
      this.listQueryParams.page = this.articleRes.current_page;
    },
    filter: function(type, value) {
      this.listQueryParams[type] = value;

      if (type === 'status') {
        this.listQueryParams.onlyTrashed = 'no';
      }
      this.renderArticleList();
    },
    order: function(type) {
      const sorterArr = this.listQueryParams.sorter.split('_');
      const oldValue = sorterArr[sorterArr.length - 1];
      const oldType = this.listQueryParams.sorter.replace(`_${oldValue}`, '');

      if (oldType === type) {
        if (oldValue === 'desc') {
          this.listQueryParams.sorter = `${type}_asc`;
        }

        if (oldValue === 'asc') {
          this.listQueryParams.sorter = `${type}_desc`;
        }
      } else {
        this.listQueryParams.sorter = `${type}_desc`;
      }

      this.renderArticleList();
    },
    firstPage: function() {
      if (this.listQueryParams.page < 1) {
        return;
      }

      this.listQueryParams.page = 1;

      this.renderArticleList();
    },
    prevPage: function() {
      if (this.listQueryParams.page < 1) {
        return;
      }

      this.listQueryParams.page = this.listQueryParams.page - 1;

      this.renderArticleList();
    },
    nextPage: function() {
      if (this.listQueryParams.page >= this.articleRes.last_page) {
        return;
      }

      this.listQueryParams.page = this.listQueryParams.page + 1;

      this.renderArticleList();
    },
    lastPage: function() {
      if (this.listQueryParams.page < 1) {
        return;
      }

      this.listQueryParams.page = this.articleRes.last_page;

      this.renderArticleList();
    }
  }
};
</script>
