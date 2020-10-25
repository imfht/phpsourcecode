<template>
<div class="">
  <Upload ref="upload" :show-upload-list="false" :default-file-list="uploadConfig.default_list" :on-success="handleSuccess" :headers="uploadConfig.headers" :format="uploadConfig.format" :max-size="uploadConfig.max_size" :on-format-error="handleFormatError"
    :on-exceeded-size="handleMaxSize" :before-upload="handleBeforeUpload" :multiple="uploadConfig.multiple" :name="uploadConfig.file_name" type="drag" :action="uploadConfig.upload_url">
    <Button icon="ios-cloud-upload-outline" style="margin-right:10px;float:left" :loading="loading">{{ uploadConfig.button_text}}</Button>
    <div>
      <span class="green-color" style="line-height:32px">{{ text }}</span>
    </div>
  </Upload>
</div>
</template>
<script>
import {
  deleteAttachment
} from '@/api/common'

export default {
  props: {
    isDelete: {
      type: Boolean,
      default: false
    },
    uploadConfig: {
      type: Object,
      default: {
        headers: {
          'Authorization': window.access_token
        },
        format: ['txt', 'xsls'],
        max_size: 800, // 800KB
        upload_url: window.uploadUrl.uploadTmp,
        file_name: 'file',
        multiple: false,
        file_num: 0,
        button_text: '上传文件',
        default_list: [{
          name: '',
          attachment_id: 0,
          url: ''
        }]

      }
    }
  },
  data() {
    return {
      imgName: '',
      visible: false,
      uploadList: [],
      formatFileList: [],
      loading: false,
      text: '未上传'
    }
  },
  methods: {
    handleSuccess(res, file) {
      file.url = res.data.url
      file.name = res.data.original_name
      file.attachment_id = res.data.attachment_id

      let formatFileList = [];
      formatFileList.push({
        attachment_id: file.attachment_id,
        url: file.url
      })
      formatFileList = formatFileList[0]
      this.$emit('input', formatFileList)
      this.$emit('on-upload-change', this.uploadList, formatFileList)
      this.text = '已上传'

      this.$Notice.success({
        title: '操作成功',
        desc: '文件上传成功'
      })
      this.loading = false
    },
    fomatFile() {
      let formatFileList = []
      this.uploadList.forEach(function(value, index, array) {
        formatFileList.push({
          attachment_id: value.attachment_id,
          url: value.url
        })
      })
      this.formatFileList = formatFileList

      if (this.uploadConfig.file_num === 1) {
        formatFileList = formatFileList[0]
      }
      return formatFileList
    },
    handleFormatError(file) {
      this.$Notice.warning({
        title: '文件格式不正确',
        desc: '文件 ' + file.name + ' 格式不正确。'
      })
      this.loading = false
    },
    handleMaxSize(file) {
      this.$Notice.warning({
        title: '超出文件大小限制',
        desc: '文件 ' + file.name + ' 太大，不能超过 ' + this.uploadConfig.max_size + 'kb'
      })
      this.loading = false
    },
    handleBeforeUpload() {

      this.text = '正在上传...'
      return true
    }
  },
  mounted() {
    this.uploadList = this.$refs.upload.fileList

    let formatFileList = this.fomatFile()
    if (formatFileList != 'undefined') {
      if (formatFileList.attachment_id > 0) {
        this.text = '已上传'
      }
      this.$emit('input', formatFileList)
      this.$emit('on-upload-change', this.uploadList, formatFileList)
    }
  }
}
</script>

<style>
.ivu-upload {
  max-width: 200px !important;
}
</style>
