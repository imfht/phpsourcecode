<template>
<div>
  <Drawer :closable="true" width="80" v-model="show" @on-close='closed' title="用户信息：" :width="platformIsPc?30:80">
    <p class="drawer-title">基本资料：</p>
    <div class="drawer-profile w-e-text">
      <Row>
        <Col span="12"> 跳转链接： {{info.link_url}} </Col>
      </Row>
      <Row class="expand-row">
        <p class="margin-top-10">
          <b>键值对：</b>
          <transition name="publish-time">
            <div class="publish-time-picker-con">
              <div class="margin-top-10"> 模型： &nbsp;&nbsp;
                <span class="green-color" v-if="info.model_column_value.model">{{ info.model_column_value.model }}</span>
                <span v-else> -- </span>
              </div>
              <div class="margin-top-10"> 字段： &nbsp;&nbsp;
                <span class="green-color" v-if="info.model_column_value.column">{{ info.model_column_value.column }}</span>
                <span v-else> -- </span>
              </div>
              <div class="margin-top-10"> 字段值：
                <span class="green-color" v-if="info.model_column_value.value">{{ info.model_column_value.value }}</span>
                <span v-else> -- </span>
              </div>
            </div>
          </transition>
        </p>
      </Row>
      <hr class="hr-line-0">
      <p class="drawer-title">内容：</p>
      <p v-html="info.content.html"></p>
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
      show: true,
      agreement: '',
      spinLoading: true
    }
  },
  created() {},
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
