<template>
<div>
  <Modal v-model="modalShow" :closable='false' :mask-closable=false width="600">
    <p slot="header">添加</p>
    <Form ref="formData" :model="formData" :rules="rules" label-position="left" :label-width="100">
      <FormItem label="分类名称" prop="name">
        <Input v-model="formData.name" placeholder="请输入"></Input>
      </FormItem>
      <FormItem label="封面：">
        <upload v-model="formData.cover_image" :upload-config="imguploadConfig" @on-upload-change='uploadChange'></upload>
      </FormItem>
      <FormItem label="描述" prop="description">
        <Input type="textarea" :rows="3" v-model="formData.description" placeholder="请输入"></Input>
      </FormItem>
      <FormItem label="排序：">
        <Input v-model="formData.weight" placeholder="请输入序号" />
      </FormItem>
    </Form>
    <div slot="footer">
      <Button type="text" @click="cancel">取消</Button>
      <Button type="primary" @click="addEditExcute" :loading='saveLoading'>保存 </Button>
    </div>
  </Modal>
</div>
</template>
<script>
import {
  addEdit
} from '@/api/category'

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
        name: '',
        description: '',
        cover_image: {
          attachment_id: 0,
          url: ''
        },
        weight: 10
      },
      rules: {
        name: [{
          required: true,
          message: '请填写分类名称',
          trigger: 'blur'
        }],
      },
      imguploadConfig: {
        headers: {
          'Authorization': window.access_token
        },
        format: ['jpg', 'jpeg', 'png', 'gif'],
        max_size: 800, // 800KB
        upload_url: window.uploadUrl.uploadAdvertisement,
        file_name: 'file',
        multiple: false,
        file_num: 1,
        default_list: []
      },
    }
  },
  methods: {
    addEditExcute() {
      let t = this
      t.$refs.formData.validate((valid) => {
        if (valid) {
          t.saveLoading = true
          addEdit(t.formData).then(res => {
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
