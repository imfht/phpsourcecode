<template>
<div>
  <Modal v-model="modalShow" :closable='false' :mask-closable=false width="600">
    <p slot="header">添加</p>
    <Form ref="formData" :model="formData" :rules="rules" label-position="left" :label-width="100">
      <FormItem label="广告位名称" prop="name">
        <Input v-model="formData.name" placeholder="请输入" />
      </FormItem>
      <FormItem label="广告位类型" prop="type">
        <Select v-model="formData.type" filterable>
          <Option v-for="(item,key) in tableStatus.type" :value="key" :key="key">{{ item }}</Option>
        </Select>
      </FormItem>
      <FormItem label="广告位描述" prop="description">
        <Input type="textarea" :rows="3" v-model="formData.description" placeholder="请输入" />
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
} from '@/api/advertisement-position'

export default {
  props: {
    tableStatus: {
      default: {}
    }
  },
  data() {
    return {
      modalShow: true,
      saveLoading: false,
      formData: {
        name: '',
        type: '',
        description: ''
      },
      rules: {
        name: [{
          required: true,
          message: '请填写广告位名称',
          trigger: 'blur'
        }],
        type: [{
          required: true,
          message: '请选择类型',
          trigger: 'blur'
        }],
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
    }
  }
}
</script>
