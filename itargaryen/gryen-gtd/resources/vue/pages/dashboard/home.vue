<template>
  <div class="g-dashboard">
    <nav-header></nav-header>
    <div class="container g-container">
      <router-view></router-view>
    </div>
  </div>
</template>
<style lang="scss" scoped>
a {
  cursor: pointer;
  color: var(--primary);
}

.g-dashboard {
  height: 100%;
}

.g-container {
  padding-bottom: 5rem;
  min-height: calc(100% - 30px - 2.625rem);
  position: relative;

  h5 {
    margin-bottom: 0;
  }
}
</style>
<script>
import navHeader from '../../components/dashboard/navHeader.vue';

export default {
  components: {
    navHeader
  },
  created: async function() {
    this.csrfCookie();
  },
  methods: {
    csrfCookie: async function() {
      const res = await axios.get('/sanctum/csrf-cookie');
    }
  }
};
</script>
