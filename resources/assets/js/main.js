import cookie from 'js-cookie'
import axios from 'axios'
import Vue from 'vue'
import Vuex from 'vuex'
import VueRouter from 'vue-router'
import UserPage from './UserPage'
import EditUserPage from './EditUserPage'
import EditDevicePage from './EditDevicePage'
import DevicePage from './DevicePage'
import LoginPage from './LoginPage'

Vue.use(Vuex)
Vue.use(VueRouter)
Vue.component('UserPage', UserPage)
Vue.component('EditUserPage', EditUserPage)
Vue.component('EditDevicePage', EditDevicePage)
Vue.component('DevicePage', DevicePage)
Vue.component('LoginPage', LoginPage)

let store = new Vuex.Store({
  state: {
    user: null, users: null, curUser: null, curDevice: null, selection: null,
    fixUser(u) {
      u.groupName = u.group.name
      u.devCount = u.device.length + '/' + u.group.capacity
      u.href = '#/user/' + u.id
      u.device.forEach(e => {
        e.href = '#/user/' + u.id + '/device/' + e.id
        e.created_at = e.created_at.substr(2, e.created_at.length - 5)
      })
    }
  },
  mutations: {
    user(state, u) {
      if(u.groupId == 255)
        axios.get('./api/user').then(res => {
          state.users = res.data
          state.users.forEach(e => state.fixUser(e))
          state.user = state.users.find(v => u.id == v.id)
          store.commit('route', state.user)
        })
      else {
        state.users = [u]
        state.user = u
        state.fixUser(state.user)
        store.commit('route', u)
      }
    },
    route(state, u) {
      let r = app.$route.matched
      if(r.length) {
        r = r[0]
        if(r.name == 'users') {
          let id = app.$route.params.id
          if(u.id != id) {
            if(!id || u.groupId != 255)
              router.push('/user/' + u.id)
            else {
              state.curUser = state.users.find(u => u.id == id)
              state.selection = state.curUser
              return
            }
          }
        }
      } else
        router.push('/user/' + u.id)
      state.curUser = u
      state.selection = u
    },
    logout(state) {
      axios.delete('./api/auth').then(() => {
        state.user = null
        state.users = null
        router.push({name: 'login'})
      })
    },
  }
})

let router = new VueRouter({
  routes: [
    {
      name: 'users',
      path: '/user/:id',
      component: UserPage,
      children: [
        {
          name: 'editUser',
          path: 'edit',
          component: EditUserPage,
        }, {
          name: 'device',
          path: 'device/:devId',
          component: DevicePage,
          children: [
            {
              name: 'editDevice',
              path: 'edit',
              component: EditDevicePage,
            }
          ]
        }
      ]
    }, {
      name: 'login',
      path: '/auth/create',
      component: LoginPage
    }
  ],
})

let app = new Vue({
  el: '#app',
  store,
  router,
})
let token = cookie.get('token')
if(token)
  axios.get('./api/user/' + token).then(res => store.commit('user', res.data))
else
  router.push({name: 'login'})
