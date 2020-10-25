<template>
<div>
  <Modal v-model="modalShow" :closable='false' :mask-closable=false width="600">
    <p slot="header">添加</p>
    <Form ref="formData" :model="formData" :rules="rules" label-position="left" :label-width="100">
      <FormItem label="角色名称" prop="name">
        <Input v-model="formData.name" placeholder="请输入"></Input>
      </FormItem>
      <FormItem label="看守器" prop="guard_name">
        <Input v-model="formData.guard_name" placeholder="请输入"></Input>
      </FormItem>
      <FormItem label="角色描述" prop="description">
        <Input type="textarea" :rows="3" v-model="formData.description" placeholder="请输入"></Input>
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
} from '@/api/roles'

export default {
  data() {
    return {
      modalShow: true,
      saveLoading: false,
      formData: {
        name: '',
        guard_name: '',
        description: ''
      },
      rules: {
        name: [{
          required: true,
          message: '请填写角色限名称',
          trigger: 'blur'
        }],
        guard_name: [{
          required: true,
          message: '请填写看守器',
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
