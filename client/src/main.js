// The Vue build version to load with the `import` command
// (runtime-only or standalone) has been set in webpack.base.conf with an alias.
import Vue from 'vue'
import App from './App'
import router from './router'
import axios from 'axios'
import globalConfig from './router/global'
import {BootstrapVue, IconsPlugin} from 'bootstrap-vue'
Vue.use(BootstrapVue)
Vue.use(IconsPlugin)
// 定义全局变量
Vue.prototype.$axios = axios
Vue.prototype.$host = globalConfig.host
Vue.config.productionTip = false
/* eslint-disable no-new */
new Vue({
  el: '#app',
  router,
  components: { App },
  template: '<App/>'
})
