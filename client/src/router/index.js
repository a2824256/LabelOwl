import Vue from 'vue'
import Router from 'vue-router'
import Index from '../components/Index'
import LabelPage from '../components/LabelPage'

Vue.use(Router)

export default new Router({
  routes: [
    {
      path: '/',
      name: 'Index',
      component: Index
    },
    {
      path: '/label',
      name: 'LabelPage',
      component: LabelPage
    }
  ],
  mode: 'history'
})
