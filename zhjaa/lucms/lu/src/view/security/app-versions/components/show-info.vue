<template>
<div>
  <Drawer :closable="true" v-model="show" @on-close='closed' title="用户信息：" :width="platformIsPc?30:80">
    <p class="drawer-title">基本资料：</p>
    <div class="drawer-profile">
      <Row>
        <Col span="12"> app： {{info.port}} </Col>
        <Col span="12"> 系统： {{info.system}} </Col>
        <Col span="12"> 版本号： {{info.version_sn}} </Col>
      </Row>
    </div>
    <hr class="hr-line-0">
    <p class="drawer-title">更新包下载</p>
    <div class="drawer-profile">
      <a :href='info.package.url'>点我下载</a>
    </div>
  </Drawer>
</div>
</template>
<script>
export default {
  props: {
    info: {
      type: Object,
      default: ''
    }
  },
  data() {
    return {
      show: true
    }
  },
  computed: {
    platformIsPc: function() {
      return this.globalPlatformType() == 'pc' ? true : false
    }
  },
  methods: {
    closed() {
      this.show = false
      this.$emit('show-modal-close')
    }
  }
}
</script>
