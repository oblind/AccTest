import cookie from 'js-cookie'
import axios from 'axios'
import Vue from 'vue'
import Vuex from 'vuex'
import VueRouter from 'vue-router'
import DropMenu from './components/DropMenu'
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
    user: null, users: null, groups: null, curUser: null, selection: null,
    fixUser(u) {
      u.groupName = u.group.name
      u.href = '#/user/' + u.id
      u.device.forEach(e => {
        e.href = '#/user/' + u.id + '/device/' + e.id
        e.created_at = e.created_at.substr(2, e.created_at.length - 5)
      })
    }
  },
  mutations: {
    user(state, u) {
      if(u.groupId == 255) {
        axios.get('api/user').then(res => {
          state.users = res.data
          state.users.forEach(e => state.fixUser(e))
          state.user = state.users.find(v => u.id == v.id)
        })
        axios.get('api/group').then(res => state.groups = res.data)
      } else {
        state.users = [u]
        state.user = u
        state.fixUser(state.user)
      }
      if(!app.$route.name)
        app.$router.push('/user/' + u.id)
    },
    editUser(state, u) {
      state.user.name = u.name
      state.user.groupId = u.groupId
      u = state.user
      for(let i = 0; i < state.users.length; i++)
        if(state.users[i].id == u.id) {
          Vue.set(state.users, i, u)
          break
        }
    },
    logout(state) {
      axios.delete('api/auth').then(() => {
        state.user = null
        state.curUser = null
        state.users = null
        state.groups = null
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
  components: {DropMenu},
  computed: {
    admin() {
      return this.$store.state.user
    },
    menu() {
      return [
        {
          caption: this.$store.state.user && this.$store.state.user.name
        }, {
          caption: '退出',
          onclick(e) {
            e.$store.commit('logout')
          }
        }
      ]
    }
  },
})
let token = cookie.get('token')
if(token)
  axios.get('api/user/' + token).then(res => store.commit('user', res.data))
else
  router.push({name: 'login'})
