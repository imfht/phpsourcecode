<template>
<div>
  <Modal v-model="modalShow" :closable='false' :mask-closable=false width="600">
    <p slot="header">添加</p>
    <Form ref="formData" :model="formData" :rules="rules" label-position="left" :label-width="100">
      <FormItem label="封面：">
        <upload  :is-delete='false' v-model="formData.cover_image" :upload-config="imguploadConfig" @on-upload-change='uploadChange'></upload>
      </FormItem>
      <FormItem label="跳转链接：" prop="url">
        <Input v-model="formData.url" placeholder="请输入跳转链接"></Input>
      </FormItem>
      <FormItem label="描述" prop="description">
        <Input type="textarea" v-model="formData.description" placeholder="请输入描述"></Input>
      </FormItem>
      <FormItem label="排序：" prop="description">
        <InputNumber v-model="formData.weight"></InputNumber>
      </FormItem>
    </Form>
    <div slot="footer">
      <Button type="text" @click="cancel">取消</Button>
      <Button type="primary" @click="addExcute" :loading='saveLoading'>保存 </Button>
    </div>
  </Modal>
</div>
</template>
<script>
import {
  add
} from '@/api/carousel'

import Upload from '_c/common/upload'
export default {
  components: {
    Upload
  },
  data() {
    return {
      modalShow: true,
      saveLoading: false,
      formData: {
        description: ' ',
        weight: 10,
        cover_image: {
          attachment_id: 0,
          url:''
        },
        url: ' ',
      },
      rules: {
      },
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
  methods: {
    addExcute() {
      let t = this
      t.$refs.formData.validate((valid) => {
        if (valid) {
          t.saveLoading = true
          add(t.formData).then(res => {
            t.saveLoading = false
            t.modalShow = false
            t.$emit('on-add-modal-success')
            t.$emit('on-add-modal-hide')
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
      this.$emit('on-add-modal-hide')
    },
    uploadChange(fileList, formatFileList) {}
  }
}
</script>
