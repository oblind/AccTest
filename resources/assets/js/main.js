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
import DataPage from './DataPage'
import GroupsPage from './GroupsPage'
import EditGroupPage from './EditGroupPage'
import OptionPage from './OptionPage'
import LoginPage from './LoginPage'
import './date'

Vue.use(Vuex)
Vue.use(VueRouter)
Vue.component('UserPage', UserPage)
Vue.component('EditUserPage', EditUserPage)
Vue.component('EditDevicePage', EditDevicePage)
Vue.component('DevicePage', DevicePage)
Vue.component('DataPage', DataPage)
Vue.component('GroupsPage', GroupsPage)
Vue.component('EditGroupPage', EditGroupPage)
Vue.component('OptionPage', OptionPage)
Vue.component('LoginPage', LoginPage)

axios.defaults.headers.common = {
  'X-Requested-With': 'XMLHttpRequest'
}

let store = new Vuex.Store({
  state: {
    option: null,
    user: null, users: null, groups: null, curUser: null,
    curDevice: null, selection: null, message: '', error: false,
    routes: false, labelDetail: [],
    fixUser(u) {
      u.groupName = u.group.name
      u.href = '#/user/' + u.id
      this.fixPosition(u.info)
      u.device.forEach(dev => {
        dev.href = '#/user/' + u.id + '/device/' + dev.id
        dev.created_at = dev.created_at.substr(2, dev.created_at.length - 5)
        dev.data.forEach(data => this.fixData(u, dev, data))
      })
    },
    fixData(u, dev, d) {
      let l = d.fileSize
      if(l > 0x40000000)
        d.size = Math.ceil(l / 0x40000000) + 'G'
      if(l > 0x100000)
        d.size = Math.ceil(l / 0x100000) + 'M'
      else
        d.size = Math.ceil(l / 0x400) + 'K'
      d.href = '#/user/' + u.id + '/device/' + dev.id + '/data/' + d.id
      d.download = 'api/user/' + u.id + '/device/' + dev.id + '/data/' + d.id + '/download'
      d.created_at = d.created_at.substr(2, d.created_at.length - 5)
    },
    fixPosition(p) {
      p.train.forEach(t => {
        let p = Math.floor(Math.log10(t.endTrain)) - Math.floor(Math.log10(t.startTrain)), s = t.startTrain
        for(let i = 0; i < p; i++)
          s = '0' + s
        t.train = t.prefix + s + ' - ' + t.prefix + String(t.endTrain)
      })
      p.pos = p.position.join(', ')
    }
  },
  mutations: {
    users(state, {users, id}) {
      axios.get('api/group').then(res => {
        state.users = users
        state.user = users.find(u => u.id == id)
        state.groups = res.data
        state.groups.forEach(g => g.href = '#/group/' + g.id)
        users.forEach(u => {
          u.group = state.groups.find(g => g.id == u.groupId)
          state.fixUser(u)
        })
        if(!state.routes) {
          state.routes = true
          router.addRoutes([{
            name: 'user',
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
                    name: 'data',
                    path: 'data/:dataId',
                    component: DataPage,
                  }, {
                    name: 'editDevice',
                    path: 'edit',
                    component: EditDevicePage,
                  }
                ]
              }
            ]
          }, {
            name: 'groups',
            path: '/group',
            component: GroupsPage,
            children: [
              {
                name: 'group',
                path: ':id',
                component: EditGroupPage
              }
            ]
          }, {
            name: 'option',
            path: '/option',
            component: OptionPage,            
          }])
        }
        if(!app.$route.name || app.$route.name == 'login')
          app.$router.replace('/user/' + id)
      })
    },
    option(state, v) {
      state.option = JSON.parse(JSON.stringify(v))
    },
    editUser(state, v) {
      let u = state.curUser
      u.name = v.name
      u.groupId = v.groupId
      u.group = state.groups.find(g => g.id == u.groupId)
      u.groupName = u.group.name
      for(let i = 0; i < state.users.length; i++)
        if(state.users[i].id == u.id) {
          Vue.set(state.users, i, u)
          break
        }
    },
    editDevice(state, v) {
      for(let i = 0; i < state.users.length; i++)
        if(state.users[i].id == v.id) {
          let u = state.users[i]
          for(let j = 0; j < u.device.length; j++)
            if(u.device[j].id == v.devId) {
              u.device[j].name = v.name
              Vue.set(u.device, j, u.device[j])
              break
            }
          break;
        }
    },
    delUser(state, u) {
      let i = state.users.indexOf(u)
      Vue.delete(state.users, i)
      if(i == state.users.length)
        i--
      router.replace('/user/' + state.users[i].id)
    },
    passDevice(state, d) {
      d.state = 1
      Vue.set(state.curUser.device, state.curUser.device.indexOf(d), d)
    },
    deleteDevice(state, d) {
      let i = state.curUser.device.indexOf(d)
      Vue.delete(state.curUser.device, i)
      if(state.curUser.device.length == 0)
        router.replace('/user/' + state.curUser.id)
      else {
        if(i == state.curUser.device.length)
          i--
        router.replace('/user/' + state.curUser.id + '/device/' + state.curUser.device[i].id)
      }
    },
    logout(state) {
      axios.delete('api/auth').then(() => {
        state.option = null
        state.user = null
        state.curUser = null
        state.users = null
        state.groups = null
        router.push({name: 'login'})
      })
    },
    message(state, msg) {
      state.message = msg
      state.error = false
      setTimeout(() => state.message = '', 5000)
    },
    error(state, err) {
      state.message = err
      state.error = true
      setTimeout(() => state.message = '', 5000)
    }
  }
})
window.state = store.state

let router = new VueRouter({
  routes: [
    {
      name: 'login',
      path: '/auth/create',
      component: LoginPage
    }
  ],
})
router.beforeEach((to, from, next) => {
  if(store.state.message)
    store.state.message = ''
  next()
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
    option() {
      if(this.$store.state.user && !this.$store.state.option)
        axios.get('api/option').then(res => {
          this.$store.state.option = res.data
        }).catch(res => this.$store.commit('error', res.response.data))
      return this.$store.state.option
    },
    menu() {
      let r = []
      r.push({
        caption: this.$store.state.user.name,
        href: this.$store.state.user.href,
        icon: this.$store.state.user.icon
      })
      if(this.$store.state.user.groupId == 255) {
        r.push({
          caption: '用户组',
          href: '#/group',
        })  
        r.push({
          caption: '设置',
          href: '#/option',
        })
      }
      r.push({
        caption: '退出',
        onclick() {
          this.$store.commit('logout')
        }
      })
      return r
    }
  },
})
let token = cookie.get('token')
if(token)
  axios.get('api/user').then(res => {
    store.commit('users', {users: res.data, id: token})
  })
else
  router.push({name: 'login'})
