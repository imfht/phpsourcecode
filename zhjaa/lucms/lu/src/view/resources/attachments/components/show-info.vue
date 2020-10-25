<template>
<div>
  <Drawer :closable="true" v-model="show" @on-close='closed' title="详情" :width="platformIsPc?30:80">
    <p class="drawer-title">基本资料:</p>
    <div class="drawer-profile">
      <Row>
        <Col span="12"> 绝对路径： {{ info.storage_path }}/{{ info.storage_name}} </Col>
      </Row>
    </div>
    <hr class="hr-line-0">
    <p class="drawer-title">附件信息：</p>
    <div class="drawer-profile">
      <img :src="getAttachmentUrl" alt="tp" v-if='attachmentIsImage' />
      <div v-else>
        <span class="expand-key expand-title">访问 url:  </span>
        <a :href="getAttachmentUrl">下载附件</a>
      </div>
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
  computed: {
    platformIsPc: function() {
      return this.globalPlatformType() == 'pc' ? true : false
    },
    getAttachmentUrl() {
      return this.info.domain + '/' + this.info.link_path + '/' + this.info.storage_name
    },
    attachmentIsImage() {
      return (this.info.mime_type.indexOf('image') === -1) ? false : true
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
