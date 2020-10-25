components.appFooter = {
  props: ['projInfo'],
  template: `
<footer class="bd-footer text-muted">
  <div class="container">
    <ul class="bd-footer-links" v-show="projInfo">
        <router-link to="/home">Home</router-link>
        <li><a :href="projInfo.github" target="_blank">GitHub</a></li>
        <li><a :href="projInfo.gitosc" target="_blank">Git@osc</a></li>
    </ul>
    <p>welcome to here. current version <code>v{{projInfo.version}}</code></p>
  </div>
</footer>
`
}
