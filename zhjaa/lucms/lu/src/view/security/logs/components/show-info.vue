<template>
<div>
  <Drawer :closable="true" v-model="show" @on-close='closed' title="用户信息：" :width="platformIsPc?30:80">
    <p class="drawer-title">日志内容：</p>
    <transition name="publish-time">
      <div class="publish-time-picker-con">
        <div class="margin-top-10" v-for="(item,key) in info.content"> {{ key }}: &nbsp;&nbsp;
          <span class="green-color">{{ item }}</span>
        </div>
      </div>
    </transition>
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
