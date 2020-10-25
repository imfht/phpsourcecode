<template>
  <div class="card mb-3">
    <div class="card-body">
      <p>
        每
        <span class="display-4 font-weight-bold text-primary">{{analytics.aveDist}}</span> 天发布一篇文章，平均间隔
        <span class="display-4 font-weight-bold text-primary">{{analytics.aveRage}}</span> 天。
      </p>
      <p>
        最后一篇文章发布日期是：
        <span class="display-4 font-weight-bold text-primary">{{analytics.maxDateCal}}</span>，已经过去了
        <span class="display-4 font-weight-bold text-primary">{{analytics.disNow}}</span> 天。
      </p>
    </div>
  </div>
</template>
<script>
export default {
  data: function() {
    return {
      analytics: ''
    };
  },
  created: async function() {
    this.renderAnalytics();
  },
  methods: {
    renderAnalytics: async function() {
      const res = await axios.get('/api/analytics');

      this.analytics = res.data;
    }
  }
};
</script>
