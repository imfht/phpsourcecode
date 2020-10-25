<template>
<div>
  <Modal v-model="modalShow" :closable='false' :mask-closable=false width="600">
    <p slot="header">添加</p>
    <Form ref="formData" :model="formData" :rules="rules" label-position="left" :label-width="100">
      <FormItem label="类型" prop="type">
        <Select v-model="formData.type" filterable placeholder="请选择类型">
          <Option v-for="(item,key) in types" :value="key" :key="key">{{ item }}</Option>
        </Select>
      </FormItem>
      <FormItem label="ip" prop="ip">
        <Input v-model="formData.ip" placeholder="请输入ip"></Input>
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
} from '@/api/ip-filter'

export default {
  props: {
    types: {
      type: Object,
      default: []
    }
  },
  data() {
    return {
      modalShow: true,
      saveLoading: false,
      formData: {
        type: '',
        ip: ''
      },
      rules: {
        ip: [{
          required: true,
          message: '请填写ip',
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
