<template>
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-3">
    <a class="navbar-brand" href="/">
      <img
        src="https://statics.gryen.com/logo.png"
        width="30"
        height="30"
        class="d-inline-block align-top"
        alt
      />
      格安
    </a>
    <ul class="navbar-nav mr-auto">
      <li class="nav-item">
        <router-link to="/pub_analytics" class="nav-link">仪表盘</router-link>
      </li>
      <li class="nav-item">
        <router-link to="/article_manager" class="nav-link">文章管理</router-link>
      </li>
    </ul>
  </nav>
</template>
