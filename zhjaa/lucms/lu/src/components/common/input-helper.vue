<template>
<p>
  <Icon :type="type"></Icon>
  <span :class="styleClass">{{ text }}</span>
</p>

</template>

<script>
export default {
  props: {
    styleClass: {
      type: String,
      default: 'color-light-gray'
    },
    type: {
      type: String,
      default: 'md-information'
    },
    text: {
      type: String,
      default: '温馨提示...'
    }
  }
}
</script>
