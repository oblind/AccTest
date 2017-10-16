import Vue from 'vue'
import VueRouter from 'vue-router'
import Vuex from 'vuex'
import TestPage from './TestPage'

Vue.use(Vuex)
Vue.use(VueRouter)
Vue.component('TestPage', TestPage)

let store = new Vuex.Store({
  state: {

  },
  mutations: {
    user() {
      router.addRoutes([{
        name: 'user',
        path: '/user/:id',
        component: TestPage
      }])
      if(!app.$route.name)
        router.push('/user/1')
    }
  }
})

let router = new VueRouter({
  routes: [{
    name: 'login',
    path: '/login',
    component: {
      template: `<div>login
  <TestPage></TestPage>
</div>`
    },
  }]
})

let app = new Vue({
  el: '#app',
  store,
  router,
})

store.commit('user')