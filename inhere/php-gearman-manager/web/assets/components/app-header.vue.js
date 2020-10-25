components.appHeader = {
  props: ['projInfo'],
  template: `
<header>
  <b-navbar toggleable fixed="top" type="inverse" style="background-color: #5893ff;box-shadow: 0 1px 3px #4277d8;">
    <div class="container">
      <b-nav-toggle target="nav_collapse"></b-nav-toggle>

      <b-link class="navbar-brand" to="/">
        <span>Php-gwm</span>
      </b-link>

      <b-collapse is-nav id="nav_collapse">

        <b-nav is-nav-bar>
          <b-nav-item to="/home">Home</b-nav-item>
          <b-nav-item to="/server-info">Server Info</b-nav-item>
          <b-nav-item to="/log-info">Log Analysis</b-nav-item>
        </b-nav>

        <b-nav is-nav-bar class="ml-auto" v-show="projInfo">
          <b-nav-item :href="projInfo.github" target="_blank"><i class="icon-github-circled"></i>Github</b-nav-item>
          <b-nav-item :href="projInfo.github" target="_blank">Git@OSC</b-nav-item>
        </b-nav>
      </b-collapse>
    </div>
  </b-navbar>
</header>
`
}
