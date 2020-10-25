<template>
<div>
  <Modal v-model="modalShow" :closable='false' :mask-closable=false width="600">
    <p slot="header">修改</p>
    <Form ref="formData" :model="formData" :rules="rules" label-position="left" :label-width="100">
      <FormItem label="封面：">
        <upload v-if='formdataFinished' :is-delete='false' v-model="formData.cover_image" :upload-config="imguploadConfig" @on-upload-change='uploadChange'></upload>
      </FormItem>
      <FormItem label="跳转链接：" prop="url">
        <Input v-model="formData.url" placeholder="请输入"></Input>
      </FormItem>
      <FormItem label="描述：" prop="description">
        <Input type="textarea" v-model="formData.description" placeholder="请输入"></Input>
      </FormItem>
      <FormItem label="排序：" prop="description">
        <InputNumber v-model="formData.weight"></InputNumber>
      </FormItem>
    </Form>
    <div slot="footer">
      <Button type="text" @click="cancel">取消</Button>
      <Button type="primary" @click="editExcute" :loading='saveLoading'>保存 </Button>
    </div>
    <div class="demo-spin-container" v-if='spinLoading === true'>
      <Spin fix>
        <Icon type="load-c" size=18 class="spin-icon-load"></Icon>
        <div>加载中...</div>
      </Spin>
    </div>
  </Modal>
</div>
</template>
<script>
import {
  edit,
  getInfoById
} from '@/api/carousel'

import Upload from '_c/common/upload'
export default {
  components: {
    Upload
  },
  props: {
    modalId: {
      type: Number,
      default: 0
    }
  },
  data() {
    return {
      modalShow: true,
      saveLoading: false,
      spinLoading: true,
      formdataFinished: false,
      formData: {
        id: 0,
        description: ' ',
        url: ' ',
        weight: 10,
        cover_image: {
          attachment_id: 0,
          url: ''
        }
      },
      rules: {},
      imguploadConfig: {
        headers: {
          'Authorization': window.access_token
        },
        format: ['jpg', 'jpeg', 'png', 'gif'],
        max_size: 800, // 800KB
        upload_url: window.uploadUrl.uploadCarousel,
        file_name: 'file',
        multiple: false,
        file_num: 3,
        default_list: []
      },
    }
  },
  mounted() {
    if (this.modalId > 0) {
      this.getInfoByIdExcute()
    }
  },
  methods: {
    getInfoByIdExcute() {
      let t = this;
      getInfoById(t.modalId).then(res => {
        let res_data = res.data
        t.formData = {
          id: res_data.id,
          url: res_data.url,
          weight: res_data.weight,
          description: res_data.description,
          cover_image: {
            attachment_id: res_data.cover_image.attachment_id,
            url: res_data.cover_image.url
          },
        }
        t.imguploadConfig.default_list = [t.formData.cover_image]
        t.formdataFinished = true
        t.spinLoading = false
      })
    },
    editExcute() {
      let t = this
      t.$refs.formData.validate((valid) => {
        if (valid) {
          t.saveLoading = true
          edit(t.formData, t.formData.id).then(res => {
            t.saveLoading = false
            t.modalShow = false
            t.$emit('on-edit-modal-success')
            t.$emit('on-edit-modal-hide')
            t.$Notice.success({
              title: res.message
            })
          }, function(error) {
            t.saveLoading = false;
          })
        }
      })
    },
    cancel() {
      this.modalShow = false
      this.$emit('on-edit-modal-hide')
    },
    uploadChange(fileList, formatFileList) {}
  }
}
</script>
